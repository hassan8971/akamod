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
        // دریافت همه دسته‌بندی‌ها
        $allCategories = \App\Models\Category::orderBy('name')->get();

        // جدا کردن دسته‌های اصلی (بدون والد)
        $rootCategories = $allCategories->whereNull('parent_id')->values();

        // گروه‌بندی زیردسته‌ها بر اساس آیدی والدشان
        $groupedChildren = $allCategories->whereNotNull('parent_id')->groupBy('parent_id');
                            
        return view('admin.categories.index', compact('rootCategories', 'groupedChildren'));
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
            'name' => 'required|string|max:255|unique:categories',
            'slug' => 'nullable|string|max:255|unique:categories',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'is_visible' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'accordion_title' => 'nullable|string|max:255',
            'accordion_description' => 'nullable|string',
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

        $this->syncCategoryToWordPress($category);

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
            'accordion_title' => 'nullable|string|max:255',
            'accordion_description' => 'nullable|string',
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

        $this->syncCategoryToWordPress($category);

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

    private function syncCategoryToWordPress($category)
    {
        $category->load('parent');
        
        $imageUrl = null;
        if ($category->image_path) {
            // استفاده از آدرس داینامیک لاراول
            $remoteStorage = rtrim(config('app.url'), '/') . '/storage/';
            $imageUrl = $remoteStorage . ltrim($category->image_path, '/');
        }

        $data = [
            'id'          => $category->id,
            'name'        => $category->name,
            'slug'        => $category->slug,
            'parent_slug' => $category->parent ? $category->parent->slug : null,
            'description' => $category->description,
            'image_url'   => $imageUrl,
            
            // 💡 اضافه کردن فیلدهای آکاردئون به پکیج ارسالی
            'accordion_title'       => $category->accordion_title,
            'accordion_description' => $category->accordion_description,
        ];

        try {
            $wpUrl = env('WP_AKAMODE_URL', 'https://akaleather.com') . '/wp-json/akamode/v1/sync-category';
            $secret = env('WP_AKAMODE_SECRET', 'slafLKlskggslf@34rfkljw');

            $response = \Illuminate\Support\Facades\Http::timeout(10)->withHeaders([
                'X-Akamode-Secret' => $secret
            ])->post($wpUrl, $data);

            if ($response->failed()) {
                \Illuminate\Support\Facades\Log::error('WP Category Sync Failed: ' . $response->body());
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('WP Category Sync Error: ' . $e->getMessage());
        }
    }
}

