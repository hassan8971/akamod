<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\ProductController;
use App\Http\Controllers\Api\v1\CategoryController;
use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\ProductReviewController;
use App\Http\Controllers\Api\v1\user\UserPanelController;
use App\Http\Controllers\Api\v1\CheckoutController;
use App\Http\Controllers\Api\v1\MenuController;
use App\Http\Controllers\Api\v1\BlogController;
use App\Http\Controllers\Api\v1\CartController;
use App\Http\Controllers\Api\Bridge\BridgeProductController;
use App\Http\Middleware\CheckAdminBridge;
use App\Http\Controllers\Api\Bridge\BridgeDashboardController;
use App\Http\Controllers\Api\Bridge\BridgeCategoryController;
use App\Http\Controllers\Api\Bridge\BridgeOrderController;
use App\Http\Controllers\Api\Bridge\BridgeUserController;
use App\Http\Controllers\Api\Bridge\BridgeDiscountController;
use App\Http\Controllers\Api\Bridge\BridgeVideoController;
use App\Http\Controllers\Api\Bridge\BridgeSizeController;
use App\Http\Controllers\Api\Bridge\BridgeColorController;
use App\Http\Controllers\Api\Bridge\BridgeBuySourceController;
use App\Http\Controllers\Api\Bridge\BridgeMenuItemController;

/*
|--------------------------------------------------------------------------
| API Routes (Version 1)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {


Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{slug}', [ProductController::class, 'show']);
Route::get('/search', [ProductController::class, 'search']);

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{slug}', [CategoryController::class, 'show'])->where('slug', '.*');

Route::get('/menus', [MenuController::class, 'index']);

Route::get('blog/posts', [BlogController::class, 'index']);
Route::get('blog/posts/{slug}', [BlogController::class, 'show']);
Route::get('blog/categories', [BlogController::class, 'categories']);

Route::post('/cart/check-discount', [CartController::class, 'checkDiscount']);


// این مسیر پیش‌فرض لاراول برای احراز هویت با Sanctum است
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

    Route::post('login/send-otp', [AuthController::class, 'sendOtp']);
    Route::post('login/verify-otp', [AuthController::class, 'verifyOtp']);


Route::middleware('auth:sanctum')->group(function () {
    
    Route::get('/user', [UserPanelController::class, 'getUser']);
    Route::post('logout', [AuthController::class, 'logout']);

    Route::get('/user/orders', [UserPanelController::class, 'getOrders']);
    Route::get('/user/orders/{orderId}', [UserPanelController::class, 'getOrderDetails']);

    Route::post('/user/profile', [UserPanelController::class, 'updateProfile']);

    Route::get('/user/addresses', [UserPanelController::class, 'getAddresses']);
    Route::post('/user/addresses', [UserPanelController::class, 'storeAddress']);
    Route::put('/user/addresses/{addressId}', [UserPanelController::class, 'updateAddress']);
    Route::delete('/user/addresses/{addressId}', [UserPanelController::class, 'destroyAddress']);

    Route::post('products/{product}/reviews', [ProductReviewController::class, 'store']);

    Route::post('checkout', [CheckoutController::class, 'store']);

});

});

Route::middleware([CheckAdminBridge::class])->prefix('bridge')->group(function () {
    Route::get('/products', [BridgeProductController::class, 'index']);
    Route::delete('/products/{id}', [BridgeProductController::class, 'destroy']);
    Route::get('/products/create-resources', [BridgeProductController::class, 'createResources']); // روت جدید
    Route::post('/products', [BridgeProductController::class, 'store']);

    Route::get('/products/{id}', [BridgeProductController::class, 'show']);
    Route::put('/products/{id}', [BridgeProductController::class, 'update']);
    
    // واریانت‌ها
    Route::post('/products/{id}/variants', [BridgeProductController::class, 'storeVariant']);
    Route::delete('/variants/{id}', [BridgeProductController::class, 'destroyVariant']);
    
    // تصاویر
    Route::post('/products/{id}/images', [BridgeProductController::class, 'storeImage']);
    Route::delete('/images/{id}', [BridgeProductController::class, 'destroyImage']);
    Route::post('/images/reorder', [BridgeProductController::class, 'reorderImages']);

    Route::get('/dashboard', [BridgeDashboardController::class, 'index']);

    Route::get('/categories', [BridgeCategoryController::class, 'index']);
    Route::post('/categories', [BridgeCategoryController::class, 'store']);
    Route::get('/categories/tree', [BridgeCategoryController::class, 'tree']); // برای لیست اصلی
    Route::delete('/categories/{id}', [BridgeCategoryController::class, 'destroy']);

    Route::get('/categories/{id}', [BridgeCategoryController::class, 'show']); // دریافت تکی
    Route::post('/categories/{id}', [BridgeCategoryController::class, 'update']);

    Route::get('/orders', [BridgeOrderController::class, 'index']);
    Route::get('/orders/{id}', [BridgeOrderController::class, 'show']);
    Route::put('/orders/{id}/status', [BridgeOrderController::class, 'updateStatus']);

    Route::get('/users', [BridgeUserController::class, 'index']);
    Route::get('/users/{id}', [BridgeUserController::class, 'show']);

    Route::get('/discounts', [BridgeDiscountController::class, 'index']);
    Route::get('/discounts/{id}', [BridgeDiscountController::class, 'show']);
    Route::post('/discounts', [BridgeDiscountController::class, 'store']);
    Route::put('/discounts/{id}', [BridgeDiscountController::class, 'update']);
    Route::delete('/discounts/{id}', [BridgeDiscountController::class, 'destroy']);

    Route::apiResource('videos', BridgeVideoController::class);

    Route::apiResource('sizes', BridgeSizeController::class);

    Route::apiResource('colors', BridgeColorController::class);

    Route::apiResource('buy-sources', BridgeBuySourceController::class);

    Route::apiResource('menu-items', BridgeMenuItemController::class);
});