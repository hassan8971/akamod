<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str; // We'll use this to auto-generate slugs

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all products, newest first.
        // 'with('category')' is "eager loading" - it's fast!
        $products = Product::with('category')->latest()->paginate(20);

        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // We need all categories to display in a dropdown
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'product_id' => 'nullable|string|max:100|unique:products',
            'boxing_type' => 'nullable|string|max:100',
            'is_visible' => 'boolean',
        ]);
        
        // Add the slug
        $validated['slug'] = Str::slug($request->name) . '-' . uniqid();
        
        // Handle the checkbox
        $validated['is_visible'] = $request->boolean('is_visible');

        Product::create($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        // We need categories for the dropdown
        $categories = Category::all();
        
        // We also load the product's variants and images here
        // We will use these in the *next* step, but we load them now.
        $product->load('variants', 'images');
        
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            // Make sure to ignore the current product's ID when checking for unique
            'product_id' => 'nullable|string|max:100|unique:products,product_id,' . $product->id,
            'boxing_type' => 'nullable|string|max:100',
            'is_visible' => 'boolean',
        ]);

        // If the name changed, update the slug
        if ($request->name !== $product->name) {
            $validated['slug'] = Str::slug($request->name) . '-' . $product->id;
        }

        // Handle the checkbox
        $validated['is_visible'] = $request->boolean('is_visible');

        $product->update($validated);

        return redirect()->route('admin.products.edit', $product)
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // This will also delete all associated variants and images
        // because of the `onDelete('cascade')` in our migrations.
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }
}
