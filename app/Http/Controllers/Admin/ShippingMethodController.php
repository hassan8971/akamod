<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingMethod;
use Illuminate\Http\Request;

class ShippingMethodController extends Controller
{
    /**
     * نمایش لیست روش‌های ارسال
     */
    public function index()
    {
        $shippingMethods = ShippingMethod::latest()->get();
        return view('admin.shipping-methods.index', compact('shippingMethods'));
    }

    /**
     * نمایش فرم ایجاد روش ارسال جدید
     */
    public function create()
    {
        return view('admin.shipping-methods.create');
    }

    /**
     * ذخیره روش ارسال در دیتابیس
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'method_key'  => 'required|string|max:255|unique:shipping_methods,method_key',
            'title'       => 'required|string|max:255',
            'cost'        => 'required|integer|min:0',
            'description' => 'nullable|string|max:1000',
        ]);

        $validated['is_active'] = $request->has('is_active');

        ShippingMethod::create($validated);

        return redirect()->route('admin.shipping-methods.index')
                         ->with('success', 'روش ارسال جدید با موفقیت اضافه شد.');
    }

    /**
     * نمایش فرم ویرایش
     */
    public function edit(ShippingMethod $shippingMethod)
    {
        return view('admin.shipping-methods.edit', compact('shippingMethod'));
    }

    /**
     * بروزرسانی اطلاعات در دیتابیس
     */
    public function update(Request $request, ShippingMethod $shippingMethod)
    {
        $validated = $request->validate([
            'method_key'  => 'required|string|max:255|unique:shipping_methods,method_key,' . $shippingMethod->id,
            'title'       => 'required|string|max:255',
            'cost'        => 'required|integer|min:0',
            'description' => 'nullable|string|max:1000',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $shippingMethod->update($validated);

        // 💡 پاک کردن کش سیستم تا تغییرات سریعا به وردپرس ارسال شود
        cache()->forget('akamode_shipping_methods_cache');

        return redirect()->route('admin.shipping-methods.index')
                         ->with('success', 'روش ارسال با موفقیت بروزرسانی شد.');
    }

    /**
     * حذف روش ارسال
     */
    public function destroy(ShippingMethod $shippingMethod)
    {
        $shippingMethod->delete();

        cache()->forget('akamode_shipping_methods_cache');

        return redirect()->route('admin.shipping-methods.index')
                         ->with('success', 'روش ارسال حذف شد.');
    }
}