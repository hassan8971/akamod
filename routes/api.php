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