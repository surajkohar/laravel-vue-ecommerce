<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Admin\OrderItem;
use App\Models\Admin\Orders;
use App\Models\Admin\Products;
use App\Models\Users\Carts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CheckoutController extends Controller
{
    /**
     * Get payment configuration
     */
    public function getPaymentConfig(Request $request)
    {
        $stripeKey = config('services.stripe.key');
        $stripeEnabled = !empty($stripeKey) && config('services.stripe.enabled', false);

        return response()->json([
            'stripe_enabled' => $stripeEnabled,
            'stripe_publishable_key' => $stripeKey,
            'google_pay_merchant_id' => config('services.google_pay.merchant_id'),
            'apple_pay_merchant_id' => config('services.apple_pay.merchant_id'),
            'currency' => config('services.stripe.currency', 'inr'),
            'test_mode' => config('services.stripe.test_mode', true),
        ]);
    }

    /**
     * Create Stripe payment intent
     */
    public function createPaymentIntent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'currency' => 'sometimes|string|size:3',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Check if Stripe is configured
            $stripeSecret = config('services.stripe.secret');
            if (empty($stripeSecret)) {
                // Return a test payment intent for development
                return $this->createTestPaymentIntent($request);
            }

            // Real Stripe integration
            \Stripe\Stripe::setApiKey($stripeSecret);

            $intent = \Stripe\PaymentIntent::create([
                'amount' => round($request->amount * 100), // Convert to paisa/cents
                'currency' => $request->currency ?? 'inr',
                'payment_method_types' => ['card', 'google_pay'],
                'metadata' => [
                    'user_id' => Auth::id(),
                    'test_mode' => $request->test_mode ?? false,
                ],
            ]);

            return response()->json([
                'status' => true,
                'client_secret' => $intent->client_secret,
                'payment_intent_id' => $intent->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Payment Intent Error: ' . $e->getMessage());

            // Fallback to test mode
            return $this->createTestPaymentIntent($request);
        }
    }

    /**
     * Create test payment intent for development
     */
    private function createTestPaymentIntent(Request $request)
    {
        return response()->json([
            'status' => true,
            'client_secret' => 'pi_test_' . time() . '_secret_' . bin2hex(random_bytes(10)),
            'payment_intent_id' => 'pi_test_' . time(),
            'test_mode' => true,
            'note' => 'Test payment intent created (Stripe not configured)'
        ]);
    }

    /**
     * Process direct checkout (buy now)
     */
    public function directCheckout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'checkout_type' => 'required|in:direct,cart',
            'email' => 'required|email',
            'phone' => 'required|string',
            'shipping_address' => 'required|array',
            'shipping_address.firstName' => 'required|string',
            'shipping_address.lastName' => 'required|string',
            'shipping_address.address1' => 'required|string',
            'shipping_address.city' => 'required|string',
            'shipping_address.postcode' => 'required|string',
            'shipping_address.country' => 'required|string',
            'billing_address' => 'required|array',
            'billing_address.firstName' => 'required|string',
            'billing_address.lastName' => 'required|string',
            'shipping_method' => 'required|string',
            'shipping_cost' => 'required|numeric|min:0',
            'payment_method' => 'required|string|in:cod,card,paypal,google-pay',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'product_id' => 'required_if:checkout_type,direct|integer',
            'variant_id' => 'required_if:checkout_type,direct|integer',
            'size_id' => 'required_if:checkout_type,direct|integer',
            'quantity' => 'required_if:checkout_type,direct|integer|min:1',
            'payment_status' => 'required|in:pending,paid,failed,refunded',
            'stripe_payment_intent_id' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Generate order number
            $orderNumber = $this->generateOrderNumber();

            // Determine payment status based on payment method
            $paymentStatus = $request->payment_status;
            if ($request->payment_method === 'cod') {
                $paymentStatus = 'pending'; // COD always starts as pending
            } elseif ($request->test_mode && $request->payment_status === 'paid') {
                $paymentStatus = 'paid'; // Test mode can be marked as paid
            }

            // Create order
            $order = Orders::create([
                'order_number' => $orderNumber,
                'user_id' => Auth::id(),
                'status' => 'pending',
                'subtotal' => $request->subtotal,
                'shipping_cost' => $request->shipping_cost,
                'tax_amount' => $request->tax_amount,
                'total_amount' => $request->total_amount,
                'payment_method' => $request->payment_method,
                'payment_status' => $paymentStatus,
                'stripe_payment_intent_id' => $request->stripe_payment_intent_id,
                'shipping_address' => json_encode($request->shipping_address),
                'billing_address' => json_encode($request->billing_address),
                'customer_email' => $request->email,
                'customer_phone' => $request->phone,
                'notes' => $request->notes ?? '',
            ]);

            // Process order items based on checkout type
            if ($request->checkout_type === 'direct') {
                $this->processDirectOrderItems($order, $request);
            } else {
                $this->processCartOrderItems($order, $request);
            }

            // Update product stock if payment is paid or COD
            if ($paymentStatus === 'paid' || $request->payment_method === 'cod') {
                $this->updateProductStock($order);
            }

            // If payment is already marked as paid, update order status
            if ($paymentStatus === 'paid') {
                $order->update(['status' => 'processing']);

                // Clear cart if checkout type is cart
                if ($request->checkout_type === 'cart' && Auth::check()) {
                    Carts::where('user_id', Auth::id())->delete();
                }
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Order created successfully',
                'data' => [
                    'order_number' => $order->order_number,
                    'order_id' => $order->id,
                    'total_amount' => $order->total_amount,
                    'status' => $order->status,
                    'payment_status' => $order->payment_status,
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout Error: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Failed to process checkout',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process cart checkout
     */
    public function cartCheckout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'phone' => 'required|string',
            'shipping_address' => 'required|array',
            'billing_address' => 'required|array',
            'shipping_method' => 'required|string',
            'payment_method' => 'required|string|in:cod,card,paypal,google-pay',
            'payment_status' => 'required|in:pending,paid,failed,refunded',
            'stripe_payment_intent_id' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Get cart items
        $cartItems = Carts::with(['product', 'variant', 'size'])
            ->where('user_id', Auth::id())
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Your cart is empty'
            ], 400);
        }

        // Calculate totals
        $subtotal = $cartItems->sum(function ($item) {
            return $item->quantity * $item->price;
        });

        $shippingCost = $request->shipping_cost ?? 0;
        $taxAmount = $request->tax_amount ?? 0;
        $totalAmount = $subtotal + $shippingCost + $taxAmount;

        // Merge cart data into request
        $request->merge([
            'checkout_type' => 'cart',
            'subtotal' => $subtotal,
            'shipping_cost' => $shippingCost,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
        ]);

        // Process checkout
        return $this->directCheckout($request);
    }

    /**
     * Generate order number
     */
    private function generateOrderNumber()
    {
        $prefix = 'ORD-';
        $date = date('Ymd');
        $random = strtoupper(substr(uniqid(), -6));

        return $prefix . $date . '-' . $random;
    }

    /**
     * Process direct order items
     */
    private function processDirectOrderItems(Orders $order, Request $request)
    {
        $product = Products::with(['variants' => function ($query) use ($request) {
            $query->with(['sizes', 'images']);
        }])->find($request->product_id);

        if (!$product) {
            throw new \Exception('Product not found');
        }

        $variant = $product->variants->firstWhere('id', $request->variant_id);
        $size = $variant->sizes->firstWhere('id', $request->size_id);

        // Get variant image
        $imageUrl = null;
        if ($variant->images->isNotEmpty()) {
            $imageUrl = asset('storage/' . $variant->images->first()->image_name);
        }

        // Create order item
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'variant_id' => $variant->id,
            'size_id' => $size->id,
            'product_name' => $product->name,
            'variant_name' => $variant->color_name,
            'size_title' => $size->title ?? $size->name,
            'quantity' => $request->quantity,
            'unit_price' => $product->price,
            'total_price' => $product->price * $request->quantity,
            'image_url' => $imageUrl,
        ]);
    }

    /**
     * Process cart order items
     */
    private function processCartOrderItems(Orders $order, Request $request)
    {
        $cartItems = Carts::with(['product', 'variant', 'size'])
            ->where('user_id', Auth::id())
            ->get();

        foreach ($cartItems as $cartItem) {
            $product = $cartItem->product;
            $variant = $cartItem->variant;
            $size = $cartItem->size;

            // Get variant image
            $imageUrl = null;
            if ($variant && $variant->images->isNotEmpty()) {
                $imageUrl = asset('storage/' . $variant->images->first()->image_name);
            }

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'variant_id' => $variant->id ?? null,
                'size_id' => $size->id ?? null,
                'product_name' => $product->name,
                'variant_name' => $variant->color_name ?? null,
                'size_title' => $size->title ?? $size->name ?? null,
                'quantity' => $cartItem->quantity,
                'unit_price' => $cartItem->price,
                'total_price' => $cartItem->price * $cartItem->quantity,
                'image_url' => $imageUrl,
            ]);
        }
    }

    /**
     * Update product stock
     */
    private function updateProductStock(Orders $order)
    {
        $orderItems = OrderItem::where('order_id', $order->id)->get();

        foreach ($orderItems as $item) {
            if ($item->variant_id && $item->size_id) {
                // Update variant size stock
                DB::table('product_variant_sizes')
                    ->where('variant_id', $item->variant_id)
                    ->where('size_id', $item->size_id)
                    ->decrement('stock', $item->quantity);
            }

            // Update product total stock
            $product = Products::find($item->product_id);
            if ($product) {
                $product->decrement('stock', $item->quantity);
            }
        }
    }

    /**
     * Get user orders
     */
    public function getUserOrders(Request $request)
    {
        $orders = Orders::with(['items'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'status' => true,
            'data' => $orders
        ]);
    }

    /**
     * Get order details
     */
    public function getOrder($orderNumber)
    {
        $order = Orders::with(['items'])
            ->where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->first();

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $order
        ]);
    }

    /**
     * Complete order without payment (for testing)
     */
    public function completeOrderWithoutPayment(Request $request, $orderId)
    {
        if (!app()->environment('local', 'testing')) {
            return response()->json([
                'status' => false,
                'message' => 'This endpoint is only available in development mode'
            ], 403);
        }

        $order = Orders::where('id', $orderId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found'
            ], 404);
        }

        $order->update([
            'payment_status' => 'paid',
            'status' => 'processing',
            'stripe_payment_intent_id' => 'test_' . time(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Order marked as paid',
            'data' => $order
        ]);
    }

    /**
     * Stripe webhook handler (optional for now)
     */
    public function stripeWebhook(Request $request)
    {
        return response()->json([
            'status' => true,
            'message' => 'Webhook received (not implemented in test mode)'
        ]);
    }
}
