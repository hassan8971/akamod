<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Discount;
use App\Models\ProductVariant;

class CartController extends Controller
{
    /**
     * Check discount validity and calculate amounts.
     * (بررسی کد تخفیف و محاسبه مبالغ برای فرانت‌اند)
     */
    public function checkDiscount(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string',
            'items' => 'required|array', // فرانت‌اند باید لیست آیتم‌ها را بفرستد
            'items.*.variant_id' => 'required|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        // ۱. محاسبه مبلغ کل سبد خرید (Subtotal) در سرور
        // (ما به قیمت‌های ارسالی فرانت اعتماد نمی‌کنیم، خودمان از دیتابیس می‌خوانیم)
        $subtotal = 0;
        foreach ($validated['items'] as $item) {
            $variant = ProductVariant::find($item['variant_id']);
            // اگر قیمت تخفیف‌دار دارد از آن استفاده کن، وگرنه قیمت اصلی
            $price = $variant->discount_price ?? $variant->price;
            $subtotal += $price * $item['quantity'];
        }

        // ۲. پیدا کردن کد تخفیف
        $discount = Discount::where('code', $validated['code'])->first();

        // ۳. اعتبارسنجی کد
        if (!$discount) {
            return response()->json(['valid' => false, 'message' => 'کد تخفیف معتبر نیست.'], 404);
        }
        if (!$discount->is_active) {
            return response()->json(['valid' => false, 'message' => 'این کد تخفیف غیرفعال است.'], 422);
        }
        if ($discount->expires_at && $discount->expires_at->isPast()) {
            return response()->json(['valid' => false, 'message' => 'مهلت استفاده از این کد تمام شده است.'], 422);
        }
        if ($discount->starts_at && $discount->starts_at->isFuture()) {
            return response()->json(['valid' => false, 'message' => 'زمان استفاده از این کد هنوز نرسیده است.'], 422);
        }
        if ($discount->usage_limit && $discount->times_used >= $discount->usage_limit) {
            return response()->json(['valid' => false, 'message' => 'سقف استفاده از این کد تکمیل شده است.'], 422);
        }
        if ($discount->min_purchase > $subtotal) {
            return response()->json([
                'valid' => false, 
                'message' => 'حداقل خرید برای این کد ' . number_format($discount->min_purchase) . ' تومان است.'
            ], 422);
        }

        // ۴. محاسبه مبلغ تخفیف
        $discountAmount = 0;
        if ($discount->type == 'percent') {
            $discountAmount = ($subtotal * $discount->value) / 100;
        } else {
            $discountAmount = $discount->value;
        }

        // جلوگیری از منفی شدن مبلغ کل
        if ($discountAmount > $subtotal) {
            $discountAmount = $subtotal;
        }

        // ۵. ارسال پاسخ به فرانت‌اند
        return response()->json([
            'valid' => true,
            'message' => 'کد تخفیف اعمال شد.',
            'discount_code' => $discount->code,
            'discount_amount' => $discountAmount,
            'new_total' => $subtotal - $discountAmount,
            // اطلاعات برای نمایش زیبا در فرانت
            'discount_display' => number_format($discountAmount) . ' تومان',
        ]);
    }
}