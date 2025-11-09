<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Video;
use App\Models\Size;
use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with(['category', 'admin'])->latest()->paginate(20);
        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $sizes = Size::orderBy('name')->get();
        $colors = Color::orderBy('name')->get();
        $allProducts = Product::select('id', 'name')->get(); 
        $allVideos = Video::all();
        
        $latestProduct = Product::orderBy('id', 'desc')->first();
        $nextId = $latestProduct ? $latestProduct->id + 1 : 1;
        $newProductId = str_pad($nextId, 8, '0', STR_PAD_LEFT);
        while (Product::where('product_id', $newProductId)->exists()) {
            $nextId++;
            $newProductId = str_pad($nextId, 8, '0', STR_PAD_LEFT);
        }

        $product = new Product([
            'product_id' => $newProductId,
            'is_visible' => true,
            'is_for_men' => false,
            'is_for_women' => false,
        ]);
        
        // Load an empty relationship for the create form
        // (این کار باعث می‌شود $product->videos->pluck('id') در ویو خطا ندهد)
        $product->load('videos', 'relatedProducts'); 

        return view('admin.products.create', compact(
            'categories', 
            'product', 
            'sizes', 
            'colors',
            'allProducts',
            'allVideos'
        ));
    }

    /**
     * Store a newly created resource in storage.
     * --- اصلاح شده ---
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Product Details
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'product_id' => 'required|string|max:255|unique:products,product_id',
            'boxing_type' => 'nullable|string|max:100',
            'is_visible' => 'boolean',
            'is_for_men' => 'boolean',
            'is_for_women' => 'boolean',

            // Variants Validation (بر اساس فرم شما)
            'variants' => 'nullable|array',
            'variants.*.size' => 'nullable|string|max:255',
            'variants.*.color' => 'nullable|string|max:255',
            'variants.*.price' => 'required_with:variants|integer|min:0',
            'variants.*.discount_price' => 'nullable|integer|min:0|lt:variants.*.price',
            'variants.*.buy_price' => 'nullable|integer|min:0',
            'variants.*.stock' => 'required_with:variants|integer|min:0',
            'variants.*.buy_source' => 'nullable|string|max:255',

            // Media Validation
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            // (اعتبارسنجی فایل‌های ویدیویی حذف شد، چون دیگر مستقیماً آپلود نمی‌شوند)
            // 'videos' => ... 
            
            'video_embeds' => 'nullable|array', 
            'video_embeds.*' => 'nullable|string|regex:/<iframe.*<\/iframe>/i', 

            'related_product_ids' => 'nullable|array',
            'related_product_ids.*' => 'exists:products,id',

            // --- FIX: Add validation for selected video IDs ---
            'video_ids' => 'nullable|array',
            'video_ids.*' => 'exists:videos,id',
        ], [
            'variants.*.discount_price.lt' => 'قیمت با تخفیf باید کمتر از قیمت اصلی باشد.',
            'video_embeds.*.regex' => 'کد الصاقی (embed) معتبر نیست. باید شامل تگ <iframe> باشد.'
        ]);
        
        // --- آماده‌سازی داده‌ها ---
        $validated['slug'] = empty($request->slug) ? Str::slug($request->name) . '-' . uniqid() : Str::slug($request->slug);
        $validated['admin_id'] = Auth::guard('admin')->id();
        $validated['is_visible'] = $request->boolean('is_visible');
        $validated['is_for_men'] = $request->boolean('is_for_men');
        $validated['is_for_women'] = $request->boolean('is_for_women');
        // --- پایان آماده‌سازی ---

        DB::beginTransaction();
        try {
            // 1. ایجاد محصول اصلی
            $product = Product::create($validated);

            // 2. ایجاد متغیرها
            if ($request->has('variants')) {
                foreach ($request->variants as $variantData) {
                    $product->variants()->create($variantData);
                }
            }

            // 3. ذخیره تصاویر
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $path = $file->store('products', 'public');
                    $product->images()->create(['path' => $path, 'alt_text' => $product->name]);
                }
            }
            
            // 4. ذخیره ویدیوهای الصاقی (Embed)
            // (این بخش را نگه داشتیم، اما می‌توانید آن را حذف کنید اگر دیگر لازم نیست)
            if ($request->has('video_embeds')) {
                foreach ($request->video_embeds as $embedCode) {
                    if (!empty($embedCode)) {
                        // This uses the old logic, you might want to remove this
                        // and force users to use the Video Library
                        $product->videos()->create([
                            'embed_code' => $embedCode,
                            'alt_text' => $product->name . ' (embed)',
                            'type' => 'embed',
                        ]);
                    }
                }
            }
        
            // --- FIX: 5. ذخیره ویدیوهای مرتبط (از کتابخانه) ---
            if ($request->has('video_ids')) {
                $product->videos()->sync($request->video_ids);
            }

            // 6. ذخیره محصولات مرتبط
            if ($request->has('related_product_ids')) {
                $product->relatedProducts()->sync($request->related_product_ids);
            }

            DB::commit();

            return redirect()->route('admin.products.edit', $product)->with('success', 'محصول با موفقیت ایجاد شد.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'خطا در ایجاد محصول: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        $sizes = Size::orderBy('name')->get();
        $colors = Color::orderBy('name')->get();
        
        $product->load('variants', 'images', 'videos', 'relatedProducts'); 
        
        $allProducts = Product::where('id', '!=', $product->id)
                                ->select('id', 'name')
                                ->get();

        $allVideos = Video::all();
        
        return view('admin.products.edit', compact('product', 'categories', 'sizes', 'colors', 'allProducts', 'allVideos'));
    }

    /**
     * Update the specified resource in storage.
     * --- اصلاح شده ---
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'product_id' => ['required', 'string', 'max:255', Rule::unique('products')->ignore($product->id)],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('products')->ignore($product->id)],
            'boxing_type' => 'nullable|string|max:100',
            'is_visible' => 'boolean',
            'is_for_men' => 'boolean',
            'is_for_women' => 'boolean',
            'related_product_ids' => 'nullable|array',
            'related_product_ids.*' => 'exists:products,id',
            
            // --- FIX: Add validation for selected video IDs ---
            'video_ids' => 'nullable|array',
            'video_ids.*' => 'exists:videos,id'
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']) . '-' . $product->id;
        }

        $validated['is_visible'] = $request->boolean('is_visible');
        $validated['is_for_men'] = $request->boolean('is_for_men');
        $validated['is_for_women'] = $request->boolean('is_for_women');

        $product->update($validated);

        if ($request->has('related_product_ids')) {
            $product->relatedProducts()->sync($request->related_product_ids);
        } else {
            $product->relatedProducts()->sync([]);
        }

        // --- FIX: Add sync logic for videos ---
        if ($request->has('video_ids')) {
            $product->videos()->sync($request->video_ids);
        } else {
            $product->videos()->sync([]);
        }
        // --- End Fix ---

        return redirect()->route('admin.products.edit', $product)
            ->with('success', 'محصول با موفقیت به‌روزرسانی شد.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->load('images', 'videos');

        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->path);
        }
        // We no longer delete videos, just detach them
        // (unless they are 'upload' type and you want to delete the file)
        
        $product->delete(); // This should detach pivot table records

        return redirect()->route('admin.products.index')
            ->with('success', 'محصول (و تمام فایل‌های مرتبط) با موفقیت حذف شد.');
    }

    /**
     * Helper function to generate shoe size list.
     */
    private function getSizeList(): array
    {
        $sizes = [];
        for ($i = 36.5; $i <= 47; $i += 0.5) {
            $sizes[] = (string)$i;
        }
        return $sizes;
    }
}