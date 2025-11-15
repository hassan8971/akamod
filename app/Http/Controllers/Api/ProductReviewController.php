<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
        // Get logged in user using sent token
        $user = $request->user(); 

        $validated = $request->validate([
            'rating' => 'nullable|integer|min:1|max:5|required_without:parent_id',
            'comment' => 'required|string|min:3|max:2000',
            'parent_id' => 'nullable|integer|exists:product_reviews,id', 
        ], [
            'rating.required_without' => 'لطفاً برای ثبت نظر اصلی، امتیاز خود را انتخاب کنید.',
            'comment.required' => 'نوشتن متن نظر الزامی است.',
            'parent_id.exists' => 'نظری که به آن پاسخ می‌دهید یافت نشد.',
        ]);

        // Make sure the user has purchased the product already
        if (!$user->hasPurchased($product->id)) {
            return response()->json([
                'success' => false,
                'message' => 'شما فقط در صورتی می‌توانید نظر دهید که این محصول را خریداری کرده باشید.'
            ], 403); // 403 Forbidden
        }

        // Save the comment with reply
        $review = $product->reviews()->create([
            'user_id' => $user->id,
            'rating' => $validated['rating'] ?? null,
            'comment' => $validated['comment'],
            'parent_id' => $validated['parent_id'] ?? null,
            'is_approved' => true,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'نظر شما با موفقیت ثبت شد.',
            'review' => $review->load('user')
        ], 201); // 201 Created
    }
}