<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('admin', 'category')->latest()->paginate(20);
        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        $post = new Post(['status' => 'published', 'published_at' => now()]);
        $categories = BlogCategory::all();
        return view('admin.posts.create', compact('post', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $this->validatePost($request);

        $validated['admin_id'] = Auth::guard('admin')->id();
        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['title']);
        $validated['status'] = $request->status ?? 'draft';
        $validated['published_at'] = ($validated['status'] === 'published') ? now() : null;

        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('posts', 'public');
            $validated['featured_image_path'] = $path;
        }

        $post = Post::create($validated);
        return redirect()->route('admin.posts.edit', $post)->with('success', 'مقاله با موفقیت ایجاد شد.');
    }

    public function show(Post $post)
    {
        // Use show route for preview
        return view('blog.show', compact('post')); // (ما باید این ویو عمومی را بسازیم)
    }

    public function edit(Post $post)
    {
        $categories = BlogCategory::all();
        return view('admin.posts.edit', compact('post', 'categories'));
    }

    public function update(Request $request, Post $post)
    {
        $validated = $this->validatePost($request, $post);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['title']);
        $validated['status'] = $request->status ?? 'draft';
        
        if ($validated['status'] === 'published' && !$post->published_at) {
            $validated['published_at'] = now(); // Set publish date on first publish
        }

        if ($request->hasFile('featured_image')) {
            if ($post->featured_image_path) {
                Storage::disk('public')->delete($post->featured_image_path);
            }
            $path = $request->file('featured_image')->store('posts', 'public');
            $validated['featured_image_path'] = $path;
        }

        $post->update($validated);
        return redirect()->route('admin.posts.edit', $post)->with('success', 'مقاله با موفقیت به‌روزرسانی شد.');
    }

    public function destroy(Post $post)
    {
        if ($post->featured_image_path) {
            Storage::disk('public')->delete($post->featured_image_path);
        }
        $post->delete();
        return redirect()->route('admin.posts.index')->with('success', 'مقاله با موفقیت حذف شد.');
    }

    // Helper validation function
    private function validatePost(Request $request, Post $post = null): array
    {
        $slugRule = $post
            ? Rule::unique('posts')->ignore($post->id)
            : 'unique:posts';

        return $request->validate([
            'title' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', $slugRule],
            'blog_category_id' => 'nullable|exists:blog_categories,id',
            'content' => 'required|string|min:10',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:1000',
            'status' => 'required|in:published,draft',
        ]);
    }
}