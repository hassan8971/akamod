<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\LoginController as AdminLoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Auth\LoginController as UserLoginController;
use App\Http\Controllers\Auth\RegisterController;

Route::get('/', function () {
    return view('home');
})->name('home');


// Admin Login
Route::get('admin/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('admin/login', [AdminLoginController::class, 'login']);
Route::post('admin/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');

// Admin Dashboard (Protected)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware(['auth:admin'])->group(function () {
        Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
        Route::resource('categories', CategoryController::class)->except(['show']);
    });
});

// Login
Route::get('login', [UserLoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [UserLoginController::class, 'login']);
Route::post('logout', [UserLoginController::class, 'logout'])->name('logout');

// Registration
Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [RegisterController::class, 'register']);

// Protected "Home" Route
// Users will be redirected here after login.
Route::middleware(['auth'])->group(function () {
    // Route::get('/home', function () {
    //     return view('home');
    // })->name('home');
    
    // Add other protected user routes here (e.g., profile, orders)
});