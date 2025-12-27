<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Admin\Products;
use App\Models\Admin\ProductVariant;
use App\Models\Users\CartItems;
use App\Models\Users\Carts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CartsController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // FIXED: Get the OLDEST active cart (cart_id = 1 with items)
            $cart = Carts::where('user_id', $user->id)
                        ->where('status', 'active')
                        ->orderBy('created_at', 'asc') // Get oldest cart first
                        ->first();

            if (!$cart) {
                // Create new cart
                $cart = Carts::create([
                    'user_id' => $user->id,
                    'session_id' => session()->getId(),
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // Get cart items with proper stock calculation
            $cartItems = CartItems::select([
                    'cart_items.id',
                    'cart_items.product_id',
                    'cart_items.variant_id',
                    'cart_items.size_id',
                    'cart_items.quantity',
                    'cart_items.price',
                    'cart_items.created_at',
                    'products.name',
                    'products.main_image_name',
                    'products.stock as product_stock', // Product-level stock
                    'product_variants.color_name',
                    'sizes.size_title',
                    // Get size-specific stock from pivot table
                    DB::raw('IF(cart_items.size_id IS NOT NULL,
                        (SELECT pvs.stock FROM product_variant_sizes pvs
                         WHERE pvs.variant_id = cart_items.variant_id
                         AND pvs.size_id = cart_items.size_id),
                        NULL) as size_stock')
                ])
                ->leftJoin('products', 'products.id', '=', 'cart_items.product_id')
                ->leftJoin('product_variants', 'product_variants.id', '=', 'cart_items.variant_id')
                ->leftJoin('sizes', 'sizes.id', '=', 'cart_items.size_id')
                ->where('cart_items.cart_id', $cart->id)
                ->orderBy('cart_items.created_at', 'DESC')
                ->get();

            // Format items and calculate stock availability
            $formattedItems = [];
            foreach ($cartItems as $item) {
                // Determine stock based on variant/size selection
                $stock = $item->product_stock; // Default to product stock

                // If size is selected, use size-specific stock from pivot table
                if ($item->size_id && $item->size_stock !== null) {
                    $stock = $item->size_stock;
                }

                // Calculate if item is in stock
                $has_stock = $stock > 0;

                $formattedItems[] = [
                    'id' => $item->product_id,
                    'cart_item_id' => $item->id,
                    'name' => $item->name,
                    'price' => (float) $item->price,
                    'image' => $item->main_image_name ?
                        asset('storage/' . $item->main_image_name) :
                        asset('images/default-product.jpg'),
                    'quantity' => $item->quantity,
                    'variant_id' => $item->variant_id,
                    'variant_name' => $item->color_name,
                    'size_id' => $item->size_id,
                    'size_title' => $item->size_title,
                    'stock' => $stock,
                    'has_stock' => $has_stock,
                    'created_at' => $item->created_at,
                ];
            }

            // Calculate totals
            $totalItems = array_sum(array_column($formattedItems, 'quantity'));
            $subtotal = array_sum(array_map(function($item) {
                return $item['price'] * $item['quantity'];
            }, $formattedItems));

            return response()->json([
                'status' => true,
                'data' => [
                    'cart_id' => $cart->id,
                    'items' => $formattedItems,
                    'total_items' => $totalItems,
                    'subtotal' => $subtotal
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Cart index error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Failed to load cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add item to cart
     */
    public function add(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1',
                'variant_id' => 'nullable|exists:product_variants,id',
                'size_id' => 'nullable|exists:sizes,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Debug log
            \Log::info('Add to cart - User ID: ' . $user->id);
            \Log::info('Request: ' . json_encode($request->all()));

            // Get product
            $product = Products::select([
                'id', 'name', 'price', 'stock', 'main_image_name'
            ])->find($request->product_id);

            if (!$product) {
                return response()->json([
                    'status' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            // FIXED: Get the OLDEST active cart (same logic as index)
            $cart = Carts::where('user_id', $user->id)
                        ->where('status', 'active')
                        ->orderBy('created_at', 'asc')
                        ->first();

            \Log::info('Found cart: ' . ($cart ? 'ID: ' . $cart->id : 'None'));

            if (!$cart) {
                $cart = Carts::create([
                    'user_id' => $user->id,
                    'session_id' => session()->getId(),
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                \Log::info('Created new cart ID: ' . $cart->id);
            }

            // Calculate price and check stock based on variant/size
            $price = $product->price;
            $stock = $product->stock;

            // Check variant if provided
            if ($request->variant_id) {
                $variant = ProductVariant::select(['id', 'product_id', 'color_name'])
                    ->find($request->variant_id);

                if ($variant) {
                    // Check if variant belongs to product
                    if ($variant->product_id != $product->id) {
                        return response()->json([
                            'status' => false,
                            'message' => 'Variant does not belong to this product'
                        ], 400);
                    }

                    // Check size if provided
                    if ($request->size_id) {
                        // Get price and stock from product_variant_sizes pivot table
                        $sizeVariant = DB::table('product_variant_sizes')
                            ->select(['price', 'stock'])
                            ->where('variant_id', $variant->id)
                            ->where('size_id', $request->size_id)
                            ->first();

                        if (!$sizeVariant) {
                            return response()->json([
                                'status' => false,
                                'message' => 'Size not available for this variant'
                            ], 400);
                        }

                        $price = $sizeVariant->price;
                        $stock = $sizeVariant->stock;
                    } else {
                        // If variant but no size, check if variant has a default price
                        return response()->json([
                            'status' => false,
                            'message' => 'Size selection is required for this variant'
                        ], 400);
                    }
                }
            }

            // Check stock availability
            if ($request->quantity > $stock) {
                return response()->json([
                    'status' => false,
                    'message' => 'Requested quantity exceeds available stock. Only ' . $stock . ' items available.'
                ], 400);
            }

            // Check if item already exists in cart
            $existingItem = CartItems::where('cart_id', $cart->id)
                ->where('product_id', $request->product_id)
                ->where('variant_id', $request->variant_id ?: null)
                ->where('size_id', $request->size_id ?: null)
                ->first();

            if ($existingItem) {
                // Check total quantity after update
                $newQuantity = $existingItem->quantity + $request->quantity;
                if ($newQuantity > $stock) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Cannot add more items. Maximum available: ' . $stock . '. Already in cart: ' . $existingItem->quantity
                    ], 400);
                }

                // Update existing item
                $existingItem->quantity = $newQuantity;
                $existingItem->price = $price;
                $existingItem->updated_at = now();

                if ($existingItem->save()) {
                    $message = 'Cart item updated';
                    $itemId = $existingItem->id;
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Failed to update cart item'
                    ], 500);
                }
            } else {
                // Create new item
                $cartItemData = [
                    'cart_id' => $cart->id,
                    'product_id' => $request->product_id,
                    'variant_id' => $request->variant_id,
                    'size_id' => $request->size_id,
                    'quantity' => $request->quantity,
                    'price' => $price,
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                $cartItem = CartItems::create($cartItemData);

                if ($cartItem) {
                    $message = 'Item added to cart';
                    $itemId = $cartItem->id;
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Failed to add item to cart'
                    ], 500);
                }
            }

            // Get updated cart totals
            $cartTotals = CartItems::select([
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(price * quantity) as total_price')
            ])
            ->where('cart_id', $cart->id)
            ->first();

            $totalItems = $cartTotals->total_quantity ?? 0;
            $subtotal = $cartTotals->total_price ?? 0;

            \Log::info('Add to cart success - Cart ID: ' . $cart->id . ', Total items: ' . $totalItems);

            return response()->json([
                'status' => true,
                'message' => $message,
                'data' => [
                    'cart_item_id' => $itemId,
                    'total_items' => $totalItems,
                    'subtotal' => $subtotal
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Cart add error: ' . $e->getMessage());
            \Log::error('Trace: ' . $e->getTraceAsString());

            return response()->json([
                'status' => false,
                'message' => 'Failed to add item to cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update cart item quantity
     */
    public function update($id, Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'quantity' => 'required|integer|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Get cart
            $cart = Carts::where('user_id', $user->id)
                        ->where('status', 'active')
                        ->orderBy('created_at', 'asc')
                        ->first();

            if (!$cart) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cart not found'
                ], 404);
            }

            // Check if item belongs to user's cart
            $cartItem = CartItems::where('id', $id)
                                ->where('cart_id', $cart->id)
                                ->first();

            if (!$cartItem) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cart item not found'
                ], 404);
            }

            if ($request->quantity === 0) {
                // Remove item
                if (CartItems::where('id', $id)->delete()) {
                    $message = 'Item removed from cart';
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Failed to remove item from cart'
                    ], 500);
                }
            } else {
                // Check stock availability
                $stock = $this->getAvailableStock($cartItem);

                if ($request->quantity > $stock) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Requested quantity exceeds available stock. Only ' . $stock . ' items available.'
                    ], 400);
                }

                // Update quantity
                if (CartItems::where('id', $id)->update([
                    'quantity' => $request->quantity,
                    'updated_at' => now()
                ])) {
                    $message = 'Cart item updated';
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Failed to update cart item'
                    ], 500);
                }
            }

            // Get updated cart totals
            $cartItems = CartItems::select([
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(price * quantity) as total_price')
            ])
            ->where('cart_id', $cart->id)
            ->first();

            $totalItems = $cartItems->total_quantity ?? 0;
            $subtotal = $cartItems->total_price ?? 0;

            return response()->json([
                'status' => true,
                'message' => $message,
                'data' => [
                    'total_items' => $totalItems,
                    'subtotal' => $subtotal
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Cart update error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Failed to update cart item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove item from cart
     */
    public function remove($id)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Get cart
            $cart = Carts::where('user_id', $user->id)
                        ->where('status', 'active')
                        ->orderBy('created_at', 'asc')
                        ->first();

            if (!$cart) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cart not found'
                ], 404);
            }

            // Check if item belongs to user's cart
            $cartItem = CartItems::where('id', $id)
                                ->where('cart_id', $cart->id)
                                ->first();

            if (!$cartItem) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cart item not found'
                ], 404);
            }

            if (CartItems::where('id', $id)->delete()) {
                // Get updated cart totals
                $cartItems = CartItems::select([
                    DB::raw('SUM(quantity) as total_quantity'),
                    DB::raw('SUM(price * quantity) as total_price')
                ])
                ->where('cart_id', $cart->id)
                ->first();

                $totalItems = $cartItems->total_quantity ?? 0;
                $subtotal = $cartItems->total_price ?? 0;

                return response()->json([
                    'status' => true,
                    'message' => 'Item removed from cart',
                    'data' => [
                        'total_items' => $totalItems,
                        'subtotal' => $subtotal
                    ]
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to remove item from cart'
                ], 500);
            }

        } catch (\Exception $e) {
            \Log::error('Cart remove error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Failed to remove item from cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear entire cart
     */
    public function clear(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Get cart
            $cart = Carts::where('user_id', $user->id)
                        ->where('status', 'active')
                        ->orderBy('created_at', 'asc')
                        ->first();

            if (!$cart) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cart not found'
                ], 404);
            }

            // Get all cart item IDs
            $cartItems = CartItems::select(['id'])
                ->where('cart_id', $cart->id)
                ->get();

            $itemIds = $cartItems->pluck('id')->toArray();

            if (!empty($itemIds)) {
                if (CartItems::whereIn('id', $itemIds)->delete()) {
                    return response()->json([
                        'status' => true,
                        'message' => 'Cart cleared successfully',
                        'data' => [
                            'total_items' => 0,
                            'subtotal' => 0
                        ]
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Failed to clear cart'
                    ], 500);
                }
            } else {
                return response()->json([
                    'status' => true,
                    'message' => 'Cart is already empty',
                    'data' => [
                        'total_items' => 0,
                        'subtotal' => 0
                    ]
                ]);
            }

        } catch (\Exception $e) {
            \Log::error('Cart clear error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Failed to clear cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Merge guest cart with user cart after login
     */
    public function merge(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'guest_items' => 'required|array',
                'guest_items.*.product_id' => 'required|exists:products,id',
                'guest_items.*.quantity' => 'required|integer|min:1',
                'guest_items.*.variant_id' => 'nullable',
                'guest_items.*.size_id' => 'nullable',
                'guest_items.*.price' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Get or create user cart
            $cart = Carts::where('user_id', $user->id)
                        ->where('status', 'active')
                        ->orderBy('created_at', 'asc')
                        ->first();

            if (!$cart) {
                $cart = Carts::create([
                    'user_id' => $user->id,
                    'session_id' => session()->getId(),
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            $mergedCount = 0;
            $errors = [];

            foreach ($request->guest_items as $index => $guestItem) {
                try {
                    // Check if item already exists
                    $existingItem = CartItems::where('cart_id', $cart->id)
                        ->where('product_id', $guestItem['product_id'])
                        ->where('variant_id', $guestItem['variant_id'] ?? null)
                        ->where('size_id', $guestItem['size_id'] ?? null)
                        ->first();

                    if ($existingItem) {
                        // Update existing item
                        $newQuantity = $existingItem->quantity + $guestItem['quantity'];

                        if (CartItems::where('id', $existingItem->id)->update([
                            'quantity' => $newQuantity,
                            'price' => $guestItem['price'],
                            'updated_at' => now()
                        ])) {
                            $mergedCount++;
                        }
                    } else {
                        // Create new item
                        $cartItemData = [
                            'cart_id' => $cart->id,
                            'product_id' => $guestItem['product_id'],
                            'variant_id' => $guestItem['variant_id'] ?? null,
                            'size_id' => $guestItem['size_id'] ?? null,
                            'quantity' => $guestItem['quantity'],
                            'price' => $guestItem['price'],
                            'created_at' => now(),
                            'updated_at' => now()
                        ];

                        if (CartItems::create($cartItemData)) {
                            $mergedCount++;
                        }
                    }
                } catch (\Exception $e) {
                    $errors[] = [
                        'index' => $index,
                        'product_id' => $guestItem['product_id'],
                        'error' => $e->getMessage()
                    ];
                }
            }

            // Get updated cart totals
            $cartItems = CartItems::select([
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(price * quantity) as total_price')
            ])
            ->where('cart_id', $cart->id)
            ->first();

            $totalItems = $cartItems->total_quantity ?? 0;
            $subtotal = $cartItems->total_price ?? 0;

            $response = [
                'status' => true,
                'message' => "Cart merged successfully. {$mergedCount} items added.",
                'data' => [
                    'total_items' => $totalItems,
                    'subtotal' => $subtotal
                ]
            ];

            if (!empty($errors)) {
                $response['warnings'] = $errors;
            }

            return response()->json($response);

        } catch (\Exception $e) {
            \Log::error('Cart merge error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Failed to merge cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper method to get available stock for a cart item
     */
    private function getAvailableStock($cartItem)
    {
        // Get product stock
        $product = Products::select(['stock'])->find($cartItem->product_id);
        $stock = $product->stock;

        // Check size stock if size is selected
        if ($cartItem->size_id && $cartItem->variant_id) {
            $sizeVariant = DB::table('product_variant_sizes')
                ->select(['stock'])
                ->where('variant_id', $cartItem->variant_id)
                ->where('size_id', $cartItem->size_id)
                ->first();

            if ($sizeVariant) {
                $stock = $sizeVariant->stock;
            }
        }

        return $stock;
    }
}
