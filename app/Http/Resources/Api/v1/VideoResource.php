<?php

namespace App\Http\Resources\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage; // <-- 1. Import Storage

class VideoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        // 2. Check the type of the video
        if ($this->type === 'embed') {
            return [
                'id' => $this->id,
                'name' => $this->name,
                'type' => 'embed',
                'content' => $this->embed_code, // <-- 3. Return the raw embed code
            ];
        }

        // If it's an 'upload' type
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => 'upload',
            'content' => Storage::url($this->path), // <-- 4. Return the full URL
        ];
    }
}