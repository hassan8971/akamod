<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // <-- Import Storage

class ProductImageController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048' // Validate each file
        ]);

        foreach ($request->file('images') as $file) {
            // Store the file in 'public/products'
            $path = $file->store('products', 'public');

            // Create the database record
            $product->images()->create([
                'path' => $path,
                'alt_text' => $product->name, // Use product name as a default alt text
            ]);
        }

        return redirect()->route('admin.products.edit', $product)
            ->with('success', 'Images uploaded successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductImage $image)
    {
        // Get the product *before* deleting the image
        $product = $image->product;

        // 1. Delete the file from storage
        Storage::disk('public')->delete($image->path);

        // 2. Delete the record from the database
        $image->delete();

        return redirect()->route('admin.products.edit', $product)
            ->with('success', 'Image deleted successfully.');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'images' => 'required|array',
        ]);

        foreach ($request->images as $index => $id) {
            // آپدیت کردن ستون order بر اساس ایندکس آرایه دریافتی
            \App\Models\ProductImage::where('id', $id)->update(['order' => $index]);
        }

        return response()->json(['status' => 'success']);
    }
}

