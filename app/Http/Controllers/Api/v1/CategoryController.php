<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Resources\Api\v1\CategoryResource;
use App\Http\Resources\Api\v1\ProductResource;
use App\Http\Resources\Api\v1\ProductListResource;

class CategoryController extends Controller
{
    /**
     * Display a listing of all categories.
     */
    public function index()
    {
        // We get all visible categories, but nested (with children)
        // This is perfect for building menus in the frontend
        $categories = Category::where('is_visible', true)
                                ->whereNull('parent_id') // Get only top-level
                                ->with('children') // Eager load children
                                ->orderBy('name', 'asc')
                                ->get();

        return CategoryResource::collection($categories);
    }

    /**
     * Display the specified category and its products.
     * (نمایش محصولات یک دسته‌بندی خاص)
     */
    public function show(Request $request, string $slug)
    {
        // --- اصلاح خطا ---
        // تابع end() نمی‌تواند نتیجه مستقیم تابع explode() را بگیرد.
        // ابتدا آن را به یک متغیر تبدیل می‌کنیم:
        $slugParts = explode('/', $slug);
        $targetSlug = end($slugParts); 
        // ----------------

        // ۱. پیدا کردن دسته‌بندی
        $category = Category::where('slug', $targetSlug) 
                            ->where('is_visible', true)
                            ->with(['children', 'descendants']) 
                            ->firstOrFail();

        // ۲. استخراج تمام IDهای زیرمجموعه
        $allCategoryIds = collect([$category->id]);

        $this->collectDescendantIds($category, $allCategoryIds);

        // ۳. دریافت محصولات
        $products = Product::whereIn('category_id', $allCategoryIds)
                             ->where('is_visible', true)
                             ->with(['variants', 'images', 'hoverImage', 'variants.colorData'])
                             ->latest()
                             ->paginate(12)
                             ->withQueryString();

        // ۴. بازگشت پاسخ
        return response()->json([
            'category' => new CategoryResource($category),
            'products' => ProductListResource::collection($products),
            'pagination' => [
                'total' => $products->total(),
                'count' => $products->count(),
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
                'total_pages' => $products->lastPage(),
                'links' => [
                    'next' => $products->nextPageUrl(),
                    'prev' => $products->previousPageUrl(),
                ],
            ]
        ]);
    }

    private function collectDescendantIds($category, &$ids)
    {
        foreach ($category->descendants as $child) {
            $ids->push($child->id);
            // اگر این فرزند خودش فرزندی داشت، تابع را دوباره صدا بزن
            if ($child->descendants->isNotEmpty()) {
                $this->collectDescendantIds($child, $ids);
            }
        }
    }
}