<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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
        ]);
        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        BlogCategory::create($validated);
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
        ]);
        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $blogCategory->update($validated);
        return redirect()->route('admin.blog-categories.index')->with('success', 'دسته‌بندی وبلاگ به‌روزرسانی شد.');
    }

    public function destroy(BlogCategory $blogCategory)
    {
        // TODO: Handle posts in this category (set to null?)
        $blogCategory->delete();
        return redirect()->route('admin.blog-categories.index')->with('success', 'دسته‌بندی وبلاگ حذف شد.');
    }
}