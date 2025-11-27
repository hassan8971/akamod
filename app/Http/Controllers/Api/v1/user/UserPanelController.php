<?php

namespace App\Http\Controllers\Api\v1\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\Address;

class UserPanelController extends Controller
{
    /**
     * Get the authenticated user's details.
     * (دریافت اطلاعات کاربر لاگین‌کرده)
     */
    public function getUser(Request $request)
    {
        // $request->user() به طور خودکار کاربر مرتبط با توکن را برمی‌گرداند
        return $request->user();
    }

    /**
     * Get the authenticated user's order history.
     * (دریافت تاریخچه سفارشات کاربر)
     */
    public function getOrders(Request $request)
    {
        $user = $request->user();

        $orders = $user->orders()
                       ->with([
                           // ما به این روابط نیاز داریم تا تصویر و اسلاگ را نمایش دهیم
                           'items',
                           'items.productVariant:id,product_id', // اطلاعات متغیر
                           'items.productVariant.product:id,slug', // اطلاعات محصول (شامل اسلاگ)
                           'items.productVariant.product.images' // تصاویر محصول
                       ])
                       ->orderBy('created_at', 'desc')
                       ->paginate(10); // صفحه‌بندی نتایج

        return $orders;
    }

    /**
     * Get the details of a single order, ensuring it belongs to the user.
     * (دریافت جزئیات یک سفارش خاص، با بررسی مالکیت)
     */
    public function getOrderDetails(Request $request, $orderId)
    {
        $user = $request->user();

        // findOrFail خطا برمی‌گرداند اگر سفارش یافت نشود یا متعلق به این کاربر نباشد
        $order = $user->orders()
                     ->with([
                         // ما 'items.productVariant.product.images' را اضافه می‌کنیم
                         'items.productVariant.product.images', 
                         'address', 
                         'packagingOption'
                     ])
                     ->findOrFail($orderId);

        return $order;
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
        ]);
        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'پروفایل با موفقیت به‌روزرسانی شد.',
            'user' => $user->fresh(), // اطلاعات جدید کاربر را برمی‌گردانیم
        ]);
    }

    public function getAddresses(Request $request)
    {
        return $request->user()->addresses;
    }

    public function storeAddress(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip_code' => 'required|string|max:20',
            'phone' => 'required|string|max:20',
        ]);

        $address = $user->addresses()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'آدرس جدید با موفقیت اضافه شد.',
            'address' => $address
        ], 201); // 201 Created
    }

    public function updateAddress(Request $request, $addressId)
    {
        $user = $request->user();
        
        // ۱. پیدا کردن آدرس و اطمینان از اینکه متعلق به این کاربر است
        $address = $user->addresses()->findOrFail($addressId);

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip_code' => 'required|string|max:20',
            'phone' => 'required|string|max:20',
        ]);

        $address->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'آدرس با موفقیت به‌روزرسانی شد.',
            'address' => $address
        ]);
    }

    public function destroyAddress(Request $request, $addressId)
    {
        $user = $request->user();

        // ۱. پیدا کردن آدرس و اطمینان از اینکه متعلق به این کاربر است
        $address = $user->addresses()->findOrFail($addressId);

        // ۲. حذف آدرس
        $address->delete();

        return response()->json([
            'success' => true,
            'message' => 'آدرس با موفقیت حذف شد.'
        ]);
    }
}