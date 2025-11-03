<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

    /**
     * Display a single product page.
     */
    public function show(string $slug)
    {
        $product = Product::where('slug', $slug)
                            ->where('is_visible', true)
                            ->with(['images', 'variants']) // Ecalb
                            ->firstOrFail();
        
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

        return view('shop.show', compact('product', 'options', 'variantsJson'));
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
}
