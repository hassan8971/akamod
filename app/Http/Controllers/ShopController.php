<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ShopController extends Controller
{
    /**
     * Display a list of all visible products.
     */
    public function index()
    {
        $products = Product::where('is_visible', true)
                            ->with('images') // Eager load images
                            ->paginate(12); // Paginate for performance

        $categories = Category::where('is_visible', true)->get();

        return view('shop.index', compact('products', 'categories'));
    }

    public function categoriesIndex()
    {
        $categories = Category::where('is_visible', true)
                                ->whereNull('parent_id') // Get only top-level
                                ->with('children') // Eager load children
                                ->get();
        
        // This method requires a new view file: resources/views/shop/categories.blade.php
        return view('shop.categories', compact('categories'));
    }

    /**
     * Display a single product page.
     */
    public function show(string $slug)
    {
        $product = Product::where('slug', $slug)
                            ->where('is_visible', true)
                            ->with([
                                'category', 'variants', 'images', 'videos', 
                                // --- بارگیری روابط تودرتو (تا ۳ سطح) ---
                                'approvedReviews.user', 
                                'approvedReviews.replies.user',
                                'approvedReviews.replies.replies.user'
                            ])
                            ->firstOrFail();

        $user = Auth::user();
        $hasPurchased = false;

        if ($user) {
            $hasPurchased = $user->hasPurchased($product->id);
            $canReview = $hasPurchased;
        }
        
        // This is a bit advanced, but useful for the variant selector
        // We get all unique sizes and colors
        $options = [
            'sizes' => $product->variants->pluck('size')->unique()->filter(),
            'colors' => $product->variants->pluck('color')->unique()->filter(),
        ];

        // Pass all variants to the view as a JSON object for Alpine.js
        $variantsJson = $product->variants->keyBy(function($variant) {
            return $variant->size . '-' . $variant->color;
        })->toJson();

        return view('shop.show', compact('product', 'options', 'variantsJson', 'hasPurchased'));
    }

    /**
     * Display products for a specific category.
     */
    public function category(string $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        
        $products = $category->products()
                            ->where('is_visible', true)
                            ->with('images')
                            ->paginate(12);
        
        $categories = Category::where('is_visible', true)->get();

        return view('shop.index', compact('products', 'categories', 'category'));
    }

    public function search(Request $request)
    {
        // 1. Get the search query from the URL (e.g., /search?q=my-query)
        $query = $request->input('q');

        // 2. Perform the search
        $products = Product::where('is_visible', true)
            ->where(function ($db) use ($query) {
                // Search in product name
                $db->where('name', 'LIKE', "%{$query}%")
                // Also search in product description
                   ->orWhere('description', 'LIKE', "%{$query}%");
            })
            ->with('images', 'variants') // Load relationships
            ->latest() // Show newest first
            ->paginate(12) // Paginate the results
            ->withQueryString(); // Keep '?q=...' in pagination links

        // 3. Return the results view
        return view('shop.search-results', compact('products', 'query'));
    }
}