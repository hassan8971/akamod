<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductReviewController extends Controller
{
    /**
     * Store a new review for a product.
     */
    public function store(Request $request, Product $product)
    {
        $user = Auth::user();

        // --- 1. اعتبارسنجی ورودی ---
        $validated = $request->validate([
            // امتیاز فقط برای نظرات سطح بالا (بدون والد) الزامی است
            'rating' => 'nullable|integer|min:1|max:5|required_without:parent_id',
            'comment' => 'required|string|min:3|max:2000',
            // بررسی می‌کند که آیا نظری که به آن پاسخ داده می‌شود، وجود دارد
            'parent_id' => 'nullable|integer|exists:product_reviews,id', 
        ], [
            'rating.required_without' => 'لطفاً برای ثبت نظر اصلی، امتیاز خود را انتخاب کنید.',
            'comment.required' => 'نوشتن متن نظر الزامی است.',
            'parent_id.exists' => 'نظری که به آن پاسخ می‌دهید یافت نشد.',
        ]);

        // --- 2. بررسی مجوزها (Authorization) ---
        if (!$user->hasPurchased($product->id)) {
            return redirect()->back()->with('error', 'شما فقط در صورتی می‌توانید نظر دهید که این محصول را خریداری کرده باشید.');
        }

        // --- 3. ذخیره نظر ---
        $product->reviews()->create([
            'user_id' => $user->id,
            'rating' => $validated['rating'] ?? null, // پاسخ‌ها امتیاز ندارند
            'comment' => $validated['comment'],
            'parent_id' => $validated['parent_id'] ?? null, // ذخیره ID والد (اگر پاسخ باشد)
            'is_approved' => true, // (فعلاً نظرات را خودکار تایید می‌کنیم)
        ]);

        return redirect()->back()->with('success', 'نظر شما با موفقیت ثبت شد و پس از بررسی نمایش داده خواهد شد.');
    }
}