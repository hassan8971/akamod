<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str; // We'll use this to auto-generate slugs
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all products, newest first.
        // 'with('category')' is "eager loading" - it's fast!
        $products = Product::with(['category', 'admin'])->latest()->paginate(20);

        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // We need all categories to display in a dropdown
        $categories = Category::all();

        // 1. Get the highest 'id' (auto-increment) from the products table.
        $latestProduct = Product::orderBy('id', 'desc')->first();
        
        // 2. Determine the next ID
        $nextId = $latestProduct ? $latestProduct->id + 1 : 1;
        
        // 3. Format it as an 8-digit string (e.g., 00000001)
        $newProductId = str_pad($nextId, 8, '0', STR_PAD_LEFT);

        // 4. (Safety Check) In case this ID was manually used, find the next available one
        while (Product::where('product_id', $newProductId)->exists()) {
            $nextId++;
            $newProductId = str_pad($nextId, 8, '0', STR_PAD_LEFT);
        }

        $product = new Product([
            'product_id' => $newProductId,
            'is_visible' => true // Set default visibility
        ]);


        return view('admin.products.create', compact('categories', 'product'));
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
            'product_id' => 'required|string|max:255|unique:products,product_id',
            'boxing_type' => 'nullable|string|max:100',
            'is_visible' => 'boolean',
            'is_for_men' => 'boolean',
            'is_for_women' => 'boolean',
        ]);
        
        // Add the slug
        $validated['slug'] = Str::slug($request->name) . '-' . uniqid();

        $validated['admin_id'] = Auth::guard('admin')->id();
        
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
        $sizes = $this->getSizeList();

        // 1. Load the relationships we need for the edit page
        $product->load('variants', 'images', 'videos', 'relatedProducts'); 
        
        // 2. Get all OTHER products for the search dropdown (only ID and name)
        $allProducts = Product::where('id', '!=', $product->id)
                                ->select('id', 'name')
                                ->get();
        
        return view('admin.products.edit', compact('product', 'categories', 'sizes', 'allProducts'));
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
            'is_for_men' => 'boolean',
            'is_for_women' => 'boolean',
            'related_product_ids' => 'nullable|array',
            'related_product_ids.*' => 'exists:products,id' // Must be valid product IDs
        ]);

        // If the name changed, update the slug
        if ($request->name !== $product->name) {
            $validated['slug'] = Str::slug($request->name) . '-' . $product->id;
        }

        // Handle the checkbox
        $validated['is_visible'] = $request->boolean('is_visible');

        $product->update($validated);

        // Sync the related products
        // sync() automatically adds new ones, and removes old ones
        if ($request->has('related_product_ids')) {
            $product->relatedProducts()->sync($request->related_product_ids);
        } else {
            // If no IDs are sent, remove all relationships
            $product->relatedProducts()->sync([]);
        }

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

    private function getSizeList(): array
    {
        $sizes = [];
        for ($i = 36.5; $i <= 47; $i += 0.5) {
            // Convert to string to handle .0 and .5 correctly
            $sizes[] = (string)$i;
        }
        return $sizes;
    }
}
