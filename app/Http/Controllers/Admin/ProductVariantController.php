<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use App\Models\Size;
use App\Models\Color;
use App\Models\BuySource;

class ProductVariantController extends Controller
{
    /**
     * Store a newly created resource in storage.
     * We pass in the Product to link it.
     */
    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'color' => 'required|string|max:100',
            'size' => 'required|string|max:100',
            'discount_price' => 'nullable|integer|min:0',
            'price' => 'required|numeric|min:0',
            'buy_price' => 'nullable|integer|min:0',
            'stock' => 'required|integer|min:0',
            'buy_source_id' => 'nullable|integer|exists:buy_sources,id',
        ], [
            'size.required' => 'فیلد سایز الزامی است.',
            'color.required' => 'فیلد رنگ الزامی است.',
            'discount_price.lt' => 'قیمت تخفیf باید کمتر از قیمت اصلی باشد.'
        ]);

        // Convert price from dollars (e.g., 10.50) to cents (1050)
        $validated['price'] = (int) $validated['price'];

        // Create the variant and link it to the product
        $product->variants()->create($validated);

        return redirect()->route('admin.products.edit', $product)
            ->with('success', 'Variant added successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductVariant $variant)
    {
        // We need to load the product this variant belongs to
        // This is for the "Back" link
        $product = $variant->product; 
        $sizes = Size::orderBy('name')->get();
        $colors = Color::orderBy('name')->get();
        $buySources = BuySource::orderBy('name')->get();
        
        
        return view('admin.variants.edit', compact('variant', 'product', 'sizes', 'colors', 'buySources'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductVariant $variant)
    {
        $validated = $request->validate([
            'color' => 'required|string|max:100',
            'size' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|integer|min:0',
            'buy_price' => 'nullable|integer|min:0',
            'stock' => 'required|integer|min:0',
            'buy_source_id' => 'nullable|integer|exists:buy_sources,id',
        ], [
            'size.required' => 'فیلد سایز الزامی است.',
            'color.required' => 'فیلد رنگ الزامی است.',
            'discount_price.lt' => 'قیمت تخفیf باید کمتر از قیمت اصلی باشد.'
        ]);

        // Convert price from dollars (e.g., 10.50) to cents (1050)
        $validated['price'] = (int) ($validated['price']);

        $variant->update($validated);

        // Redirect back to the main product edit page
        return redirect()->route('admin.products.edit', $variant->product)
            ->with('success', 'Variant updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductVariant $variant)
    {
        // Get the product *before* we delete the variant
        $product = $variant->product;
        $variant->delete();

        return redirect()->route('admin.products.edit', $product)
            ->with('success', 'Variant deleted successfully.');
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
