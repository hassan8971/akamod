<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class BlogCategoryController extends Controller
{
    public function index()
    {
        $categories = BlogCategory::latest()->get();
        return view('admin.blog-categories.index', compact('categories'));
    }

    public function create()
    {
        $category = new BlogCategory();
        return view('admin.blog-categories.create', compact('category'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:blog_categories',
            'slug' => 'nullable|string|max:255|unique:blog_categories',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // <-- 2. Add image validation
        ]);
        
        $data = $validated;
        $data['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        
        // --- 3. Add image upload logic ---
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('blog-categories', 'public');
            $data['image_path'] = $path;
        }
        // --- End new logic ---

        BlogCategory::create($data);
        return redirect()->route('admin.blog-categories.index')->with('success', 'دسته‌بندی وبلاگ ایجاد شد.');
    }

    public function edit(BlogCategory $blogCategory)
    {
        return view('admin.blog-categories.edit', ['category' => $blogCategory]);
    }

    public function update(Request $request, BlogCategory $blogCategory)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('blog_categories')->ignore($blogCategory->id)],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('blog_categories')->ignore($blogCategory->id)],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // <-- 4. Add image validation
        ]);
        
        $data = $validated;
        $data['slug'] = $validated['slug'] ?? Str::slug($validated['name']);

        // --- 5. Add image update logic ---
        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($blogCategory->image_path) {
                Storage::disk('public')->delete($blogCategory->image_path);
            }
            // Store new image
            $path = $request->file('image')->store('blog-categories', 'public');
            $data['image_path'] = $path;
        }
        // --- End new logic ---

        $blogCategory->update($data);
        return redirect()->route('admin.blog-categories.index')->with('success', 'دسته‌بندی وبلاگ به‌روزرسانی شد.');
    }

    public function destroy(BlogCategory $blogCategory)
    {
        if ($blogCategory->image_path) {
            Storage::disk('public')->delete($blogCategory->image_path);
        }
        
        $blogCategory->delete();
        return redirect()->route('admin.blog-categories.index')->with('success', 'دسته‌بندی وبلاگ حذف شد.');
    }
}