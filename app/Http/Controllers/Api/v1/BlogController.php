<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Resources\Api\v1\PostResource;
use App\Http\Resources\Api\v1\BlogCategoryResource;

class BlogController extends Controller
{
    /**
     * Get all published posts, paginated.
     */
    public function index()
    {
        $posts = Post::where('status', 'published')
                     ->where('published_at', '<=', now())
                     ->with('admin:id,name', 'category:id,name,slug') // Load relationships
                     ->latest('published_at')
                     ->paginate(10);
                     
        return PostResource::collection($posts);
    }

    /**
     * Get all blog categories.
     */
    public function categories()
    {
        $categories = BlogCategory::orderBy('name')->get();
        return BlogCategoryResource::collection($categories);
    }

    /**
     * Get a single published post by its slug.
     */
    public function show($slug)
    {
        $post = Post::where('slug', $slug)
                    ->where('status', 'published')
                    ->where('published_at', '<=', now())
                    ->with('admin:id,name', 'category:id,name,slug')
                    ->firstOrFail();
                    
        return new PostResource($post);
    }
}