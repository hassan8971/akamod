<?php

namespace App\Http\Resources\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
// Import other resources we will use
use App\Http\Resources\Api\v1\CategoryResource;
use App\Http\Resources\Api\v1\ProductVariantResource;
use App\Http\Resources\Api\v1\ProductImageResource;
use App\Http\Resources\Api\v1\VideoResource;
use App\Http\Resources\Api\v1\UserResource;
use App\Http\Resources\Api\v1\ProductListResource;
use App\Http\Resources\Api\v1\ProductReviewResource;
use Illuminate\Support\Facades\Storage;


class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'care' => $this->care_and_maintenance,
            'product_id' => $this->product_id,
            'is_for_men' => $this->is_for_men,
            'is_for_women' => $this->is_for_women,
            
            // --- Use Resources for relationships ---
            
            // We use 'whenLoaded' to prevent errors if the relation wasn't eager-loaded
            
            'admin' => new UserResource($this->whenLoaded('admin')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            
            'images' => ProductImageResource::collection($this->whenLoaded('images')),
            'hover_image' => $this->hoverImage ? Storage::url($this->hoverImage->path) : null,
            'videos' => VideoResource::collection($this->whenLoaded('videos')),
            'variants' => ProductVariantResource::collection($this->whenLoaded('variants')),
            
            'related_products' => ProductListResource::collection($this->whenLoaded('relatedProducts')),
            
            // Include average rating
            'average_rating' => $this->averageRating(),
            'reviews_count' => $this->approvedReviewsCount(),

            'reviews' => ProductReviewResource::collection($this->whenLoaded('approvedReviews')),
            
            'created_at' => $this->created_at,
        ];
    }
}