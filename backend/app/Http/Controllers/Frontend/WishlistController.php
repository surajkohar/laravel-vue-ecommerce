<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Users\WishlistItems;
use App\Models\Users\Wishlists;
use App\Models\Admin\Products;
use App\Models\Admin\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class WishlistController extends Controller
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

            // Get wishlist for user
            $wishlist = Wishlists::getRow([
                'user_id = ?' => [$user->id]
            ]);

            if (!$wishlist) {
                return response()->json([
                    'status' => true,
                    'data' => [
                        'wishlist_id' => null,
                        'items' => [],
                        'total_items' => 0
                    ]
                ]);
            }

            // Get wishlist items
            $wishlistItems = WishlistItems::select([
                    'wishlist_items.id',
                    'wishlist_items.product_id',
                    'wishlist_items.variant_id',
                    'wishlist_items.size_id',
                    'wishlist_items.created_at',
                    'products.name',
                    'products.main_image_name',
                    'products.price',
                    'products.stock as product_stock',
                    'product_variants.color_name',
                    'sizes.size_title',
                    DB::raw('(SELECT COUNT(*) FROM product_variants pv WHERE pv.product_id = products.id) as variant_count')
                ])
                ->leftJoin('products', 'products.id', '=', 'wishlist_items.product_id')
                ->leftJoin('product_variants', 'product_variants.id', '=', 'wishlist_items.variant_id')
                ->leftJoin('sizes', 'sizes.id', '=', 'wishlist_items.size_id')
                ->where('wishlist_items.wishlist_id', $wishlist->id)
                ->orderBy('wishlist_items.created_at', 'DESC')
                ->get();

            // Format items
            $formattedItems = [];
            foreach ($wishlistItems as $item) {
                // Determine stock
                $stock = $item->product_stock;
                $has_stock = $stock > 0;

                $formattedItems[] = [
                    'id' => $item->product_id,
                    'wishlist_item_id' => $item->id,
                    'name' => $item->name,
                    'price' => (float) $item->price,
                    'image' => $item->main_image_name ?
                        asset('storage/' . $item->main_image_name) :
                        asset('images/default-product.jpg'),
                    'variant_id' => $item->variant_id,
                    'variant_name' => $item->color_name,
                    'size_id' => $item->size_id,
                    'size_title' => $item->size_title,
                    'stock' => $stock,
                    'has_stock' => $has_stock,
                    'variant_count' => $item->variant_count,
                    'added_at' => $item->created_at,
                ];
            }

            return response()->json([
                'status' => true,
                'data' => [
                    'wishlist_id' => $wishlist->id,
                    'items' => $formattedItems,
                    'total_items' => count($formattedItems)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to load wishlist',
                'error' => $e->getMessage()
            ], 500);
        }
    }

   public function add(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
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

        // DEBUG: Log the user
        \Log::info('Wishlist add - User ID: ' . $user->id);

        // Get or create wishlist - FIXED: Use simple where
        $wishlist = DB::table('wishlists')->where('user_id', $user->id)->first();

        \Log::info('Found wishlist: ' . ($wishlist ? 'ID: ' . $wishlist->id : 'None'));

        if (!$wishlist) {
            \Log::info('Creating new wishlist');
            $wishlistId = DB::table('wishlists')->insertGetId([
                'user_id' => $user->id,
                'session_id' => session()->getId(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $wishlist = (object)['id' => $wishlistId];
            \Log::info('Created wishlist ID: ' . $wishlistId);
        }

        \Log::info('Using wishlist ID: ' . $wishlist->id);

        // Check if item already exists
        $existingItem = DB::table('wishlist_items')
            ->where('wishlist_id', $wishlist->id)
            ->where('product_id', $request->product_id)
            ->where('variant_id', $request->variant_id)
            ->where('size_id', $request->size_id)
            ->first();

        if ($existingItem) {
            // Remove if exists
            DB::table('wishlist_items')->where('id', $existingItem->id)->delete();
            $message = 'Item removed from wishlist';
            $action = 'removed';
            $inWishlist = false;
        } else {
            // Add new item
            $itemId = DB::table('wishlist_items')->insertGetId([
                'wishlist_id' => $wishlist->id,
                'product_id' => $request->product_id,
                'variant_id' => $request->variant_id,
                'size_id' => $request->size_id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $message = 'Item added to wishlist';
            $action = 'added';
            $inWishlist = true;
        }

        // Get updated count
        $totalItems = DB::table('wishlist_items')
            ->where('wishlist_id', $wishlist->id)
            ->count();

        \Log::info('Success! Action: ' . $action . ', Total items: ' . $totalItems);

        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => [
                'action' => $action,
                'total_items' => $totalItems,
                'in_wishlist' => $inWishlist
            ]
        ]);

    } catch (\Exception $e) {
        \Log::error('Wishlist add error: ' . $e->getMessage());
        \Log::error('Trace: ' . $e->getTraceAsString());

        return response()->json([
            'status' => false,
            'message' => 'Failed to update wishlist',
            'error' => $e->getMessage()
        ], 500);
    }
}

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

            // Get wishlist
            $wishlist = Wishlists::getRow([
                'user_id = ?' => [$user->id]
            ]);

            if (!$wishlist) {
                return response()->json([
                    'status' => false,
                    'message' => 'Wishlist not found'
                ], 404);
            }

            // Check if item belongs to user's wishlist
            $wishlistItem = WishlistItems::getRow([
                'id = ? AND wishlist_id = ?' => [$id, $wishlist->id]
            ]);

            if (!$wishlistItem) {
                return response()->json([
                    'status' => false,
                    'message' => 'Wishlist item not found'
                ], 404);
            }

            if (WishlistItems::remove($id)) {
                // Get updated wishlist count
                $totalItems = WishlistItems::select([DB::raw('COUNT(*) as total')])
                    ->where('wishlist_id', $wishlist->id)
                    ->first()->total ?? 0;

                return response()->json([
                    'status' => true,
                    'message' => 'Item removed from wishlist',
                    'data' => [
                        'total_items' => $totalItems
                    ]
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to remove item from wishlist'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to remove item from wishlist',
                'error' => $e->getMessage()
            ], 500);
        }
    }

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

            // Get wishlist
            $wishlist = Wishlists::getRow([
                'user_id = ?' => [$user->id]
            ]);

            if (!$wishlist) {
                return response()->json([
                    'status' => false,
                    'message' => 'Wishlist not found'
                ], 404);
            }

            // Get all wishlist item IDs
            $wishlistItems = WishlistItems::select(['id'])
                ->where('wishlist_id', $wishlist->id)
                ->get();

            $itemIds = $wishlistItems->pluck('id')->toArray();

            if (!empty($itemIds)) {
                if (WishlistItems::removeAll($itemIds)) {
                    return response()->json([
                        'status' => true,
                        'message' => 'Wishlist cleared successfully',
                        'data' => [
                            'total_items' => 0
                        ]
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Failed to clear wishlist'
                    ], 500);
                }
            } else {
                return response()->json([
                    'status' => true,
                    'message' => 'Wishlist is already empty',
                    'data' => [
                        'total_items' => 0
                    ]
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to clear wishlist',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function check(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|exists:products,id',
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

            // Get wishlist
            $wishlist = Wishlists::getRow([
                'user_id = ?' => [$user->id]
            ]);

            $inWishlist = false;
            $wishlistItemId = null;

            if ($wishlist) {
                $wishlistItem = WishlistItems::getRow([
                    'wishlist_id = ? AND product_id = ? AND variant_id = ? AND size_id = ?' => [
                        $wishlist->id,
                        $request->product_id,
                        $request->variant_id ?: null,
                        $request->size_id ?: null
                    ]
                ]);

                if ($wishlistItem) {
                    $inWishlist = true;
                    $wishlistItemId = $wishlistItem->id;
                }
            }

            return response()->json([
                'status' => true,
                'data' => [
                    'in_wishlist' => $inWishlist,
                    'wishlist_item_id' => $wishlistItemId
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to check wishlist status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function moveToCart($id, Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Get wishlist
            $wishlist = Wishlists::getRow([
                'user_id = ?' => [$user->id]
            ]);

            if (!$wishlist) {
                return response()->json([
                    'status' => false,
                    'message' => 'Wishlist not found'
                ], 404);
            }

            // Get wishlist item
            $wishlistItem = WishlistItems::select(['*'])
                ->where('id', $id)
                ->where('wishlist_id', $wishlist->id)
                ->first();

            if (!$wishlistItem) {
                return response()->json([
                    'status' => false,
                    'message' => 'Wishlist item not found'
                ], 404);
            }

            // Remove from wishlist
            if (WishlistItems::remove($id)) {
                // Return success - frontend will handle adding to cart
                return response()->json([
                    'status' => true,
                    'message' => 'Item removed from wishlist. Add to cart separately.',
                    'data' => [
                        'product_id' => $wishlistItem->product_id,
                        'variant_id' => $wishlistItem->variant_id,
                        'size_id' => $wishlistItem->size_id
                    ]
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to move item'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to move item to cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
