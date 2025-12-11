<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Resources\Api\v1\ProductResource;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::where('is_visible', true)
                            ->with('variants', 'images', 'videos', 'relatedProducts')
                            ->latest() // newest at top
                            ->paginate(15);

        // Return as JSON
        return ProductResource::collection($products);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $slug)
    {
        $product = Product::where('slug', $slug)
                            ->where('is_visible', true)
                            ->with([
                                'category', 
                                'variants.buySource',
                                'images', 
                                'videos', 
                                'relatedProducts.images',   
                                'relatedProducts.variants',
                                'approvedReviews' => function ($query) {
                                    $query->whereNull('parent_id') // فقط نظرات اصلی
                                        ->orderBy('created_at', 'desc') // جدیدترین اول
                                        ->with(['user', 'replies.user']); // لود کردن کاربر و پاسخ‌ها
                                }
                            ])
                            ->firstOrFail(); // 404 if not found
        return new ProductResource($product);
    }

    public function search(Request $request)
    {
        // validate 'q' query
        $validated = $request->validate([
            'q' => 'required|string|min:3',
        ], [
            'q.required' => 'لطفاً یک عبارت برای جستجو وارد کنید.',
            'q.min' => 'عبارت جستجو باید حداقل ۳ کاراکتر باشد.',
        ]);

        $query = $validated['q'];

        // Perform the search
        $products = Product::where('is_visible', true)
            ->where(function ($db) use ($query) {
                $db->where('name', 'LIKE', "%{$query}%")
                   ->orWhere('description', 'LIKE', "%{$query}%");
            })
            ->with('images', 'variants') // get the relationships
            ->latest()
            ->paginate(15)
            ->withQueryString(); // keep the 'q' in pages

        return ProductResource::collection($products);
    }

}