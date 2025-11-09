<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\LoginController as AdminLoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PackagingOptionController;
use App\Http\Controllers\Admin\DiscountController;
use App\Http\Controllers\Admin\SizeController;
use App\Http\Controllers\Admin\ColorController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\Admin\ProductVariantController;
use App\Http\Controllers\Admin\ProductImageController;
use App\Http\Controllers\Admin\VideoController;
use App\Http\Controllers\Auth\LoginController as UserLoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\OtpLoginController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\UserPanelController;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('shop/products/{slug}', [ShopController::class, 'show'])->name('shop.show');
Route::get('shop/categories/{slug}', [ShopController::class, 'category'])->name('shop.category');

Route::get('categories', [ShopController::class, 'categoriesIndex'])->name('shop.categories.index');

// Cart Routes
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('add', [CartController::class, 'store'])->name('store');
    Route::patch('{cartId}', [CartController::class, 'update'])->name('update');
    Route::delete('{cartId}', [CartController::class, 'destroy'])->name('destroy');
    Route::delete('clear', [CartController::class, 'clear'])->name('clear');
});

// We use middleware('auth') to make sure only logged-in users can check out
Route::prefix('checkout')->name('checkout.')->middleware('auth')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/', [CheckoutController::class, 'store'])->name('store');
    Route::get('/success/{order}', [CheckoutController::class, 'success'])->name('success');

    Route::post('discount/apply', [CheckoutController::class, 'applyDiscount'])->name('discount.apply');
    Route::post('discount/remove', [CheckoutController::class, 'removeDiscount'])->name('discount.remove');
});

// Admin Login
Route::post('admin/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');

// Admin Dashboard (Protected)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware(['auth:admin'])->group(function () {
        Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('products', ProductController::class)->except(['show']);


        Route::post('products/{product}/variants', [ProductVariantController::class, 'store'])
            ->name('products.variants.store');

        // GET /admin/variants/{variant}/edit
        // (Matches the 'edit' link on the product page)
        Route::get('variants/{variant}/edit', [ProductVariantController::class, 'edit'])
            ->name('variants.edit');
        
        // PUT /admin/variants/{variant}
        // (Matches the form action in the edit.blade.php file)
        Route::put('variants/{variant}', [ProductVariantController::class, 'update'])
            ->name('variants.update');

        // DELETE /admin/variants/{variant}
        // (Matches the 'delete' form on the product page)
        Route::delete('variants/{variant}', [ProductVariantController::class, 'destroy'])
            ->name('variants.destroy');
        
            // POST /admin/products/{product}/images
        // (This uploads images *for* a specific product)
        Route::post('products/{product}/images', [ProductImageController::class, 'store'])
            ->name('products.images.store');
        
        // DELETE /admin/images/{image}
        // (Deletes a specific image by its ID)
        Route::delete('images/{image}', [ProductImageController::class, 'destroy'])
            ->name('images.destroy');

        Route::resource('videos', VideoController::class)->except(['show']);
        Route::resource('sizes', SizeController::class)->except(['show']);
        Route::resource('colors', ColorController::class)->except(['show']);

        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::put('orders/{order}', [OrderController::class, 'update'])->name('orders.update');

        Route::resource('packaging-options', PackagingOptionController::class)->except(['show']);
        Route::resource('discounts', DiscountController::class)->except(['show']);
    });
});

// // Login
// Route::get('login', [UserLoginController::class, 'showLoginForm'])->name('login');
// Route::post('login', [UserLoginController::class, 'login']);
Route::post('logout', [UserLoginController::class, 'logout'])->name('logout');

// // Registration
// Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
// Route::post('register', [RegisterController::class, 'register']);

// --- User Auth Routes (OTP Passwordless) ---
Route::middleware('guest')->group(function () {
    // Step 1: Show mobile number form
    Route::get('login', [OtpLoginController::class, 'showLoginForm'])->name('login');
    // Step 1: Handle mobile number submission
    Route::post('login', [OtpLoginController::class, 'sendOtp'])->name('otp.send');
    
    // Step 2: Show verification code form
    Route::get('login/verify', [OtpLoginController::class, 'showVerifyForm'])->name('otp.verify.form');
    // Step 2: Handle code submission
    Route::post('login/verify', [OtpLoginController::class, 'verifyOtp'])->name('otp.verify');
});

// Protected "Home" Route
// Users will be redirected here after login.
Route::middleware(['auth'])->group(function () {
    // Route::get('/home', function () {
    //     return view('home');
    // })->name('home');
    
    // Add other protected user routes here (e.g., profile, orders)
});

// --- پنل کاربری (نیازمند ورود کاربر) ---
Route::middleware(['auth'])->prefix('my-account')->name('user.')->group(function () {
    
    // /my-account (تاریخچه سفارشات)
    Route::get('/', [UserPanelController::class, 'index'])->name('index');
    
    // /my-account/orders/{orderId}
    // (این مسیری است که فایل داخل Canvas شما استفاده می‌کند)
    Route::get('orders/{orderId}', [UserPanelController::class, 'showOrder'])->name('order.show');
    
    // /my-account/profile
    Route::get('profile', [UserPanelController::class, 'profile'])->name('profile');
    Route::post('profile', [UserPanelController::class, 'updateProfile'])->name('profile.update');
    Route::post('password', [UserPanelController::class, 'updatePassword'])->name('password.update');
    
    // /my-account/addresses (لیست آدرس‌ها)
    Route::get('addresses', [UserPanelController::class, 'addressesIndex'])->name('addresses.index');
    
    // /my-account/addresses/create (فرم ایجاد آدرس)
    Route::get('addresses/create', [UserPanelController::class, 'addressesCreate'])->name('addresses.create');
    
    // (ذخیره آدرس جدید)
    Route::post('addresses', [UserPanelController::class, 'addressesStore'])->name('addresses.store');
    
    // /my-account/addresses/{address}/edit (فرم ویرایش آدرس)
    Route::get('addresses/{address}/edit', [UserPanelController::class, 'addressesEdit'])->name('addresses.edit');
    
    // (به‌روزرسانی آدرس)
    Route::put('addresses/{address}', [UserPanelController::class, 'addressesUpdate'])->name('addresses.update');
    
    // (حذف آدرس)
    Route::delete('addresses/{address}', [UserPanelController::class, 'addressesDestroy'])->name('addresses.destroy');
});