<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVideo; // Make sure this model exists
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductVideoController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'videos' => 'required|array',
            'videos.*' => 'required|file|mimes:mp4,mov,ogg,qt,webm|max:51200' // Validate each file (e.g., max 50MB)
        ]);

        foreach ($request->file('videos') as $file) {
            // Store the file in 'public/videos'
            $path = $file->store('products/videos', 'public');

            // Create the database record
            $product->videos()->create([
                'path' => $path,
                'alt_text' => $product->name . " video", // Default alt text
            ]);
        }

        return redirect()->route('admin.products.edit', $product)
            ->with('success', 'ویدیوها با موفقیت آپلود شدند.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductVideo $video) // Use route-model binding
    {
        // Get the product *before* deleting the video
        $product = $video->product;

        // 1. Delete the file from storage
        Storage::disk('public')->delete($video->path);

        // 2. Delete the record from the database
        $video->delete();

        return redirect()->route('admin.products.edit', $product)
            ->with('success', 'ویدیو با موفقیت حذف شد.');
    }
}

