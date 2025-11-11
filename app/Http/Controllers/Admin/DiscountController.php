<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

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
        // اعتبار سنجی داینامیک بر اساس حالت انتخابی
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'generation_mode' => 'required|in:manual,batch',
            
            // اگر حالت دستی بود، کد الزامی و یونیک است
            'code' => 'required_if:generation_mode,manual|nullable|string|max:255|unique:discounts,code',
            
            // اگر حالت گروهی بود، تعداد الزامی و عددی بین 1 تا 100 است
            'quantity' => 'required_if:generation_mode,batch|nullable|integer|min:1|max:100', // سقف 100 تایی برای جلوگیری از فشار

            // فیلدهای مشترک
            'type' => 'required|string|in:percent,fixed',
            'value' => 'required|integer|min:0',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'usage_limit' => 'nullable|integer|min:0',
            'min_purchase' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ], [
            'code.required_if' => 'فیلد کد تخفیف در حالت دستی الزامی است.',
            'quantity.required_if' => 'فیلد تعداد در حالت گروهی الزامی است.',
            'quantity.max' => 'حداکثر تعداد مجاز برای ساخت گروهی 100 عدد است.',
        ]);

        // آماده‌سازی داده‌های مشترک
        $commonData = [
            'name' => $validated['name'],
            'type' => $validated['type'],
            'value' => $validated['value'],
            'starts_at' => $validated['starts_at'],
            'expires_at' => $validated['expires_at'],
            'usage_limit' => $validated['usage_limit'],
            'min_purchase' => $validated['min_purchase'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ];
        
        $message = '';

        // --- شروع منطق اصلی ---
        if ($validated['generation_mode'] === 'manual') {
            // حالت دستی: ایجاد یک کد
            $commonData['code'] = $validated['code'];
            Discount::create($commonData);
            $message = 'کد تخفیف با موفقیت ایجاد شد.';

        } else {
            // حالت گروهی: ایجاد چندین کد رندوم
            $count = $validated['quantity'];
            
            DB::beginTransaction();
            try {
                for ($i = 0; $i < $count; $i++) {
                    $commonData['code'] = $this->generateUniqueCode(); // ساخت کد رندوم
                    Discount::create($commonData);
                }
                DB::commit();
                $message = "تعداد {$count} کد تخفیf رندوم با موفقیت ایجاد شد.";
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->back()->with('error', 'خطا در ایجاد کدهای گروهی: ' . $e->getMessage())->withInput();
            }
        }

        return redirect()->route('admin.discounts.index')
            ->with('success', $message);
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
            'name' => 'required|string|max:255',
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

    private function generateUniqueCode(): string
    {
        do {
            // ترکیب 8 کاراکتر از حروف بزرگ (بدون O و I) و اعداد (بدون 0 و 1) برای خوانایی
            $code = Str::upper(Str::random(8)); 
        } while (Discount::where('code', $code)->exists()); // چک کردن تکراری نبودن

        return $code;
    }
}