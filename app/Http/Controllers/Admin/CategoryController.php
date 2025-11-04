<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage; // Import Storage

class CategoryController extends Controller
{
    /**
     * Display a list of all categories.
     */
    public function index()
    {
        // Get top-level categories with their children
        $categories = Category::whereNull('parent_id')
                            ->with('children') // Eager load children
                            ->latest()
                            ->get();
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     * --- THIS IS ONE OF THE FIXES ---
     */
    public function create()
    {
        // Fetch all categories to be used in the 'Parent' dropdown
        $categories = Category::all();
        return view('admin.categories.create', compact('categories'));
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'is_visible' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('categories', 'public');
            $validated['image_path'] = $path;
        }

        // Handle slug
        $validated['slug'] = $validated['slug'] 
                            ? Str::slug($validated['slug'], '-') 
                            : Str::slug($validated['name'], '-');
        
        // Handle checkbox
        $validated['is_visible'] = $request->has('is_visible');

        Category::create($validated);

        return redirect()->route('admin.categories.index')
                         ->with('success', 'دسته با موفقیت ایجاد شد.');
    }

    /**
     * Show the form for editing the specified category.
     * --- THIS IS THE SECOND FIX ---
     */
    public function edit(Category $category)
    {
        // Fetch all categories *except* the current one (a category can't be its own parent)
        $categories = Category::where('id', '!=', $category->id)->get();
        return view('admin.categories.edit', compact('category', 'categories'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('categories')->ignore($category->id),
            ],
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'is_visible' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($category->image_path) {
                Storage::disk('public')->delete($category->image_path);
            }
            $path = $request->file('image')->store('categories', 'public');
            $validated['image_path'] = $path;
        }

        // Handle slug
        $validated['slug'] = $validated['slug'] 
                            ? Str::slug($validated['slug'], '-') 
                            : Str::slug($validated['name'], '-');
        
        // Handle checkbox
        $validated['is_visible'] = $request->has('is_visible');

        $category->update($validated);

        return redirect()->route('admin.categories.index')
                         ->with('success', 'دسته با موفقیت به‌روزرسانی شد.');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category)
    {
        // TODO: Add logic to handle what happens to child categories
        // For now, we'll just delete the category

        // Delete the image from storage
        if ($category->image_path) {
            Storage::disk('public')->delete($category->image_path);
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
                         ->with('success', 'دسته با موفقیت حذف شد.');
    }
}

