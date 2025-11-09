<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'path',
        'embed_code',
    ];

    /**
     * Get all products that are assigned this video.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_video', 'video_id', 'product_id');
    }
}