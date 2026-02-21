<?php

namespace App\Http\Controllers\Api\Bridge;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class BridgeDiscountController extends Controller
{
    // لیست کدها
    public function index()
    {
        return response()->json(Discount::latest()->get());
    }

    // دریافت تکی
    public function show($id)
    {
        return response()->json(Discount::findOrFail($id));
    }

    // ایجاد کد (تکی یا گروهی)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'generation_mode' => 'required|in:manual,batch',
            'code' => 'required_if:generation_mode,manual|nullable|string|max:255|unique:discounts,code',
            'quantity' => 'required_if:generation_mode,batch|nullable|integer|min:1|max:100',
            'type' => 'required|string|in:percent,fixed',
            'value' => 'required|integer|min:0',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'usage_limit' => 'nullable|integer|min:0',
            'min_purchase' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

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

        if ($validated['generation_mode'] === 'manual') {
            $commonData['code'] = $validated['code'];
            Discount::create($commonData);
            $message = 'کد تخفیف با موفقیت ایجاد شد.';
        } else {
            $count = $validated['quantity'];
            DB::beginTransaction();
            try {
                for ($i = 0; $i < $count; $i++) {
                    $commonData['code'] = $this->generateUniqueCode();
                    Discount::create($commonData);
                }
                DB::commit();
                $message = "تعداد {$count} کد تخفیف رندوم ایجاد شد.";
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['message' => $e->getMessage()], 500);
            }
        }

        return response()->json(['message' => $message], 201);
    }

    // ویرایش
    public function update(Request $request, $id)
    {
        $discount = Discount::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:255', Rule::unique('discounts')->ignore($discount->id)],
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

        return response()->json(['message' => 'Discount updated']);
    }

    // حذف
    public function destroy($id)
    {
        $discount = Discount::findOrFail($id);
        $discount->delete();
        return response()->json(['message' => 'Discount deleted']);
    }

    private function generateUniqueCode(): string
    {
        do {
            $code = Str::upper(Str::random(8));
        } while (Discount::where('code', $code)->exists());
        return $code;
    }
}