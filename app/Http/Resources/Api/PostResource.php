<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Api\UserResource;
use App\Http\Resources\Api\BlogCategoryResource;

class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'featured_image_url' => $this->featured_image_path ? Storage::url($this->featured_image_path) : null,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'status' => $this->status,
            'published_at' => $this->published_at ? jdate($this->published_at)->format('Y-m-d H:i') : null,
            // --- 2. Use the new resource for the category ---
            'category' => new BlogCategoryResource($this->whenLoaded('category')),
            'author' => new UserResource($this->whenLoaded('admin')), // (UserResource shows admin name)
        ];
    }
}