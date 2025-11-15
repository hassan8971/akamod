<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

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

        return $categories;
    }

    /**
     * Display the specified category and its products.
     * (نمایش محصولات یک دسته‌بندی خاص)
     */
    public function show(Request $request, string $slug)
    {
        // This logic is similar to your ShopController
        $slugParts = explode('/', $slug);
        $categorySlug = end($slugParts);
        
        $category = Category::where('slug', $categorySlug)
                            ->where('is_visible', true)
                            ->firstOrFail();

        // Get the products for this category
        $products = $category->products()
                             ->where('is_visible', true)
                             ->with('variants', 'images', 'videos') // Load relationships
                             ->latest()
                             ->paginate(12)
                             ->withQueryString(); // Keep pagination queries

        // Return both the category info and the paginated products
        return response()->json([
            'category' => $category,
            'products' => $products,
        ]);
    }
}