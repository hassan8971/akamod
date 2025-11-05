<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DiscountController extends Controller
{
    /**
     * Display a listing of the resource.
     * (لیست تمام کدهای تخفیf را نشان می‌دهد)
     */
    public function index()
    {
        $discounts = Discount::latest()->get();
        return view('admin.discounts.index', compact('discounts'));
    }

    /**
     * Show the form for creating a new resource.
     * (فرم ایجاد کد تخفیف جدید را نشان می‌دهد)
     */
    public function create()
    {
        $discount = new Discount([
            'is_active' => true,
            'type' => 'fixed',
            'min_purchase' => 0,
        ]);
        return view('admin.discounts.create', compact('discount'));
    }

    /**
     * Store a newly created resource in storage.
     * (کد تخفیف جدید را در دیتابیس ذخیره می‌کند)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:discounts,code',
            'type' => 'required|string|in:percent,fixed',
            'value' => 'required|integer|min:0',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'usage_limit' => 'nullable|integer|min:0',
            'min_purchase' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['min_purchase'] = $validated['min_purchase'] ?? 0;

        Discount::create($validated);

        return redirect()->route('admin.discounts.index')
            ->with('success', 'کد تخفیف با موفقیت ایجاد شد.');
    }

    /**
     * Show the form for editing the specified resource.
     * (فرم ویرایش کد تخفیف را نشان می‌دهد)
     */
    public function edit(Discount $discount)
    {
        return view('admin.discounts.edit', compact('discount'));
    }

    /**
     * Update the specified resource in storage.
     * (تغییرات کد تخفیف را ذخیره می‌کند)
     */
    public function update(Request $request, Discount $discount)
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('discounts')->ignore($discount->id),
            ],
            'type' => 'required|string|in:percent,fixed',
            'value' => 'required|integer|min:0',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'usage_limit' => 'nullable|integer|min:0',
            'min_purchase' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['min_purchase'] = $validated['min_purchase'] ?? 0;

        $discount->update($validated);

        return redirect()->route('admin.discounts.index')
            ->with('success', 'کد تخفیف با موفقیت به‌روزرسانی شد.');
    }

    /**
     * Remove the specified resource from storage.
     * (کد تخفیف را حذف می‌کند)
     */
    public function destroy(Discount $discount)
    {
        // TODO: You might want to prevent deletion if the discount
        // has been used in an order, but for now we just delete it.
        try {
            $discount->delete();
            return redirect()->route('admin.discounts.index')
                ->with('success', 'کد تخفیف با موفقیت حذف شد.');
        } catch (\Exception $e) {
            return redirect()->route('admin.discounts.index')
                ->with('error', 'امکان حذف این کد وجود ندارد.');
        }
    }
}