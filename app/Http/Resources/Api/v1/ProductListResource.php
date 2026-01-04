<?php

namespace App\Http\Resources\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProductListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // 1. Get the first image safely
        $firstImage = $this->images->sortBy('order')->first();
        $imageUrl = $firstImage ? Storage::url($firstImage->path) : null;

        $hoverImageUrl = $this->hoverImage ? Storage::url($this->hoverImage->path) : null;

        // 2. Get price information (e.g., min price or first variant price)
        $firstVariant = $this->variants->first();
        $price = $firstVariant ? $firstVariant->price : 0;
        $discountPrice = $firstVariant ? $firstVariant->discount_price : null;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            // Manually include the image URL
            'image' => $imageUrl, 
            'hover_image' => $hoverImageUrl,
            // Manually include price info
            'price' => $price,
            'discount_price' => $discountPrice,
            
            // You can still include other fields if needed
            'is_for_men' => $this->is_for_men,
            'is_for_women' => $this->is_for_women,
        ];
    }
}