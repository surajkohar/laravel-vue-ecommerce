<?php

use App\Http\Controllers\Frontend\CheckoutController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Frontend\BrandController;
use App\Http\Controllers\Frontend\CartsController;
use App\Http\Controllers\Frontend\CategoryController;
use App\Http\Controllers\Frontend\ProductsController as FrontendProductsController;
use App\Http\Controllers\Frontend\SettingsController;
use App\Http\Controllers\Frontend\WishlistController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UsersController;
use App\Models\Admin\Products;

Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/roles/create', [RoleController::class, 'create']);
    Route::get('/roles', [RoleController::class, 'index']);
    Route::get('/roles/{id}', [RoleController::class, 'edit']);
    Route::put('/roles/{id}', [RoleController::class, 'update']);
    Route::delete('/roles/{id}', [RoleController::class, 'deleteRole']);
    Route::post('/permissions/create', [PermissionController::class, 'create']);
    Route::get('/permissions', [PermissionController::class, 'index']);
    Route::get('/permissions/{id}', [PermissionController::class, 'edit']);
    Route::put('/permissions/{id}', [PermissionController::class, 'update']);
    Route::delete('/permissions/{id}', [PermissionController::class, 'deletePermission']);
    Route::get('/rolesAndPermissions', [RoleController::class, 'fetchRolePermission']);

    Route::post('/users/{id}/assign-role', [RoleController::class, 'assignRole']);
    Route::post('/users/{id}/remove-role', [RoleController::class, 'removeRole']);
    Route::get('/users/{id}/roles', [RoleController::class, 'listRoles']);
    Route::post('/roles/{id}/assign-permission', [PermissionController::class, 'assignPermission']);
    Route::post('/roles/{id}/remove-permission', [PermissionController::class, 'removePermission']);
    Route::get('/roles/{id}/permissions', [PermissionController::class, 'listPermissions']);

    Route::post('/user/add', [UsersController::class, 'create']);
    Route::get('/users', [UsersController::class, 'index']);
    Route::get('/user/{id}/edit', [UsersController::class, 'edit']);
    Route::put('/user/{id}/update', [UsersController::class, 'update']);
    Route::post('/pro', [UsersController::class, 'updateProfile']);

    require_once __DIR__ . '/product.php';
    require_once __DIR__ . '/productCategory.php';
    require_once __DIR__ . '/productSubCategory.php';
    require_once __DIR__ . '/sizes.php';
    require_once __DIR__ . '/brands.php';
    require_once __DIR__ . '/emails.php';
});

// Route::get('/homepage/fetch-products', [ProductsController::class, 'fetchProducts']);

// ==================== FRONTEND ROUTES (Customer) ====================
Route::prefix('frontend')->group(function () {
    Route::get('/settings', [SettingsController::class, 'getFrontendSettings']);

    // Public routes (no authentication required)
    Route::get('/products', [FrontendProductsController::class, 'index']);
    Route::get('/products/featured', [FrontendProductsController::class, 'featured']);
    Route::get('/products/{slug}', [FrontendProductsController::class, 'show']);
    Route::get('/products/id/{id}', [FrontendProductsController::class, 'showById']);
    Route::get('/products/search', [FrontendProductsController::class, 'search']);
    Route::get('/products/related/{id}', [FrontendProductsController::class, 'related']);

    // Categories
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{slug}/products', [CategoryController::class, 'products']);

    // Brands
    Route::get('/brands', [BrandController::class, 'index']);
    Route::get('/brands/{slug}/products', [BrandController::class, 'products']);

    // Stripe webhook (no auth required)
    Route::post('/stripe/webhook', [CheckoutController::class, 'stripeWebhook']);

    //authenticated routes
    Route::middleware('auth:sanctum')->group(function () {

        // Wishlist routes
        Route::get('/wishlist', [WishlistController::class, 'index']);
        Route::post('/wishlist/add', [WishlistController::class, 'add']);
        Route::get('/wishlist/check', [WishlistController::class, 'check']);
        Route::delete('/wishlist/{id}', [WishlistController::class, 'remove']);
        Route::delete('/wishlist/clear', [WishlistController::class, 'clear']);
        Route::post('/wishlist/move-to-cart/{id}', [WishlistController::class, 'moveToCart']);

        // Cart routes
        Route::get('/cart', [CartsController::class, 'index']);
        Route::post('/cart/add', [CartsController::class, 'add']);
        Route::put('/cart/update/{id}', [CartsController::class, 'update']);
        Route::delete('/cart/remove/{id}', [CartsController::class, 'remove']);
        Route::post('/cart/clear', [CartsController::class, 'clear']);
        Route::post('/cart/merge', [CartsController::class, 'merge']);

        // ========== CHECKOUT ROUTES ==========
        Route::get('/checkout/config', [CheckoutController::class, 'getPaymentConfig']);
        Route::post('/checkout/create-payment-intent', [CheckoutController::class, 'createPaymentIntent']);
        Route::post('/checkout/cart', [CheckoutController::class, 'cartCheckout']);
        Route::post('/checkout/direct', [CheckoutController::class, 'directCheckout']);
        Route::get('/orders', [CheckoutController::class, 'getUserOrders']);
        Route::get('/orders/{order_number}', [CheckoutController::class, 'getOrder']);
        Route::post('/orders/{orderId}/complete', [CheckoutController::class, 'completeOrderWithoutPayment']);
    });
});
