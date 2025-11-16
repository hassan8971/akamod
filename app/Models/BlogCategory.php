<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlogCategory extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'slug', 'image_path'];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}