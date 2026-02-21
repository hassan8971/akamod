<?php

namespace App\Http\Controllers\Api\Bridge;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class BridgeCategoryController extends Controller
{
    // دریافت لیست دسته‌بندی‌ها برای انتخاب والد
    public function index()
    {
        return response()->json(Category::select('id', 'name', 'slug')->get());
    }

    // ذخیره دسته‌بندی جدید
    public function store(Request $request)
    {
        // اعتبارسنجی
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            // unique را دستی چک می‌کنیم تا با ساختار API سازگار باشد
            'slug' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $data = $request->only(['name', 'description', 'parent_id']);
            
            // مدیریت اسلاگ
            $slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->name);
            // اطمینان از یکتا بودن اسلاگ (ساده شده)
            if (Category::where('slug', $slug)->exists()) {
                $slug .= '-' . time();
            }
            $data['slug'] = $slug;

            $data['is_visible'] = $request->boolean('is_visible');

            // آپلود تصویر
            if ($request->hasFile('image')) {
                $data['image_path'] = $request->file('image')->store('categories', 'public');
            }

            $category = Category::create($data);

            $this->syncCategoryToWordPress($category);

            return response()->json(['message' => 'Category created', 'id' => $category->id], 201);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    // دریافت لیست درختی برای صفحه ایندکس
    public function tree()
    {
        $categories = Category::whereNull('parent_id')
                            ->with('children') // لود کردن فرزندان
                            ->latest()
                            ->get();
        return response()->json($categories);
    }

    // حذف دسته‌بندی
    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);
            
            // حذف تصویر اگر وجود دارد
            if ($category->image_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($category->image_path);
            }

            $category->delete(); // فرزندان هم بسته به تنظیمات دیتابیس حذف یا null می‌شوند

            return response()->json(['message' => 'Category deleted']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    // دریافت اطلاعات یک دسته‌بندی خاص
    public function show($id)
    {
        return response()->json(Category::findOrFail($id));
    }

    // ویرایش دسته‌بندی
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            // نادیده گرفتن آیدی فعلی برای چک کردن یکتایی
            'slug' => 'nullable|string|max:255', 
            'parent_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $data = $request->only(['name', 'description', 'parent_id']);
            
            // مدیریت اسلاگ
            $slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->name);
            // چک کردن تکراری نبودن (به جز خودش)
            if (Category::where('slug', $slug)->where('id', '!=', $id)->exists()) {
                $slug .= '-' . time();
            }
            $data['slug'] = $slug;

            $data['is_visible'] = $request->boolean('is_visible');

            // آپلود تصویر جدید و حذف قدیمی
            if ($request->hasFile('image')) {
                if ($category->image_path) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($category->image_path);
                }
                $data['image_path'] = $request->file('image')->store('categories', 'public');
            }

            $category->update($data);
            $this->syncCategoryToWordPress($category);

            return response()->json(['message' => 'Category updated']);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    private function syncCategoryToWordPress($category)
    {
        // لود کردن دسته والد برای استخراج اسلاگ آن
        $category->load('parent');
        
        $imageUrl = null;
        if ($category->image_path) {
            // نکته: پورت 8001 لوکال را مثل محصولات اعمال کنید
            $imageUrl = 'https://api.akamode.com/storage/' . ltrim($category->image_path, '/');
        }

        $data = [
            'id'          => $category->id,
            'name'        => $category->name,
            'slug'        => $category->slug,
            'parent_slug' => $category->parent ? $category->parent->slug : null,
            'description' => $category->description,
            'image_url'   => $imageUrl,
        ];

        try {
            Http::withHeaders([
                'X-Akamode-Secret' => env('WP_AKAMODE_SECRET', 'slafLKlskggslf@34rfkljw')
            ])->post('https://akamode.com/wp-json/akamode/v1/sync-category', $data);
        } catch (\Exception $e) {
            \Log::error('WP Category Sync Error: ' . $e->getMessage());
        }
    }
}