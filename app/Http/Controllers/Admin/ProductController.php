<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Video;
use App\Models\Size;
use App\Models\Color;
use App\Models\PackagingOption;
use App\Models\BuySource;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Http; // 💡 برای ارسال درخواست به وردپرس
use Illuminate\Support\Facades\Log;  // 💡 برای لاگ کردن خطاهای احتمالی ارتباط با وردپرس

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::orderBy('name')->get();
        $selectedCategory = null;

        $query = Product::with(['category', 'admin']);

        if ($request->filled('category_id')) {
            $categoryId = $request->input('category_id');
            $query->where('category_id', $categoryId);
            $selectedCategory = $categories->find($categoryId);
        }

        $productCount = $query->count();

        $products = $query->latest()
                          ->paginate(20)
                          ->withQueryString(); 

        return view('admin.products.index', compact(
            'products',
            'categories',       
            'productCount',     
            'selectedCategory'  
        ));
    }

    public function create()
    {
        $categories = Category::all();
        $sizes = Size::orderBy('name')->get();
        $colors = Color::orderBy('name')->get();
        $buySources = BuySource::orderBy('name')->get();
        $allProducts = Product::select('id', 'name')->get(); 
        $allVideos = Video::all();
        $allPackagingOptions = PackagingOption::where('is_active', true)->get();
        
        $product = new Product([
            'is_visible' => true,
            'is_for_men' => false,
            'is_for_women' => false,
            'is_for_kids' => false,
        ]);
        
        $product->load('videos', 'relatedProducts'); 

        return view('admin.products.create', compact(
            'categories', 
            'product', 
            'sizes', 
            'colors',
            'buySources',
            'allProducts',
            'allVideos',
            'allPackagingOptions'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'care_and_maintenance' => 'nullable|string',
            'invoice_number' => 'nullable|string|max:255|unique:products,invoice_number',
            'is_visible' => 'boolean',
            'is_for_men' => 'boolean',
            'is_for_women' => 'boolean',
            'is_for_kids' => 'boolean',
            
            'apple_title' => 'nullable|string|max:255',
            'apple_description' => 'nullable|string',
            
            'variants' => 'nullable|array',
            'variants.*.size' => 'required|string|max:255',
            'variants.*.color' => 'required|string|max:255',
            'variants.*.price' => 'required_with:variants|integer|min:0',
            'variants.*.discount_price' => 'nullable|integer|min:0|lt:variants.*.price',
            'variants.*.buy_price' => 'nullable|integer|min:0',
            'variants.*.stock' => 'required_with:variants|integer|min:0',
            'variants.*.buy_source_id' => 'nullable|integer|exists:buy_sources,id',
            'variants.*.sku' => 'nullable|string|max:255',
            'variants.*.qr_code' => 'nullable|string|max:255',

            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            
            'video_embeds' => 'nullable|array', 
            'video_embeds.*' => 'nullable|string|regex:/<iframe.*<\/iframe>/i', 

            'related_product_ids' => 'nullable|array',
            'related_product_ids.*' => 'exists:products,id',

            'video_ids' => 'nullable|array',
            'video_ids.*' => 'exists:videos,id',

            'packaging_option_ids' => 'nullable|array',
            'packaging_option_ids.*' => 'exists:packaging_options,id',
        ], [
            'variants.*.size.required' => 'فیلد سایز برای همه‌ی متغیرها الزامی است.',
            'variants.*.color.required' => 'فیلد رنگ برای همه‌ی متغیرها الزامی است.',
            'variants.*.discount_price.lt' => 'قیمت با تخفیف باید کمتر از قیمت اصلی باشد.',
            'video_embeds.*.regex' => 'کد الصاقی (embed) معتبر نیست. باید شامل تگ <iframe> باشد.'
        ]);
        
        $validated['slug'] = empty($request->slug) ? Str::slug($request->name) . '-' . uniqid() : Str::slug($request->slug);
        $validated['admin_id'] = Auth::guard('admin')->id() ?? 1; // مقدار پیش‌فرض در صورت نبود احراز هویت
        $validated['is_visible'] = $request->boolean('is_visible');
        $validated['is_for_men'] = $request->boolean('is_for_men');
        $validated['is_for_women'] = $request->boolean('is_for_women');
        $validated['is_for_kids'] = $request->boolean('is_for_kids');

        DB::beginTransaction();
        try {
            $product = Product::create($validated);

            if ($request->has('variants')) {
                foreach ($request->variants as $variantData) {
                    $product->variants()->create($variantData);
                }
            }

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $path = $file->store('products', 'public');
                    $product->images()->create(['path' => $path, 'alt_text' => $product->name]);
                }
            }
            
            if ($request->has('video_embeds')) {
                foreach ($request->video_embeds as $embedCode) {
                    if (!empty($embedCode)) {
                        $product->videos()->create([
                            'embed_code' => $embedCode,
                            'alt_text' => $product->name . ' (embed)',
                            'type' => 'embed',
                        ]);
                    }
                }
            }
        
            if ($request->has('video_ids')) {
                $product->videos()->sync($request->video_ids);
            }

            if ($request->has('related_product_ids')) {
                $product->relatedProducts()->sync($request->related_product_ids);
            }

            if ($request->has('packaging_option_ids')) {
                $product->packagingOptions()->sync($request->packaging_option_ids);
            }

            // 💡 همگام‌سازی با وردپرس بعد از ساخته شدن محصول
            $this->syncProductToWordPress($product);

            DB::commit();
            return redirect()->route('admin.products.edit', $product)->with('success', 'محصول با موفقیت ایجاد و به وردپرس ارسال شد.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating product: ' . $e->getMessage());
            return redirect()->back()->with('error', 'خطا در ایجاد محصول: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $sizes = Size::orderBy('name')->get();
        $colors = Color::orderBy('name')->get();
        $buySources = BuySource::orderBy('name')->get();
        
        $product->load('variants', 'images', 'videos', 'relatedProducts'); 
        
        $allProducts = Product::where('id', '!=', $product->id)
                                ->select('id', 'name')
                                ->get();

        $allVideos = Video::all();
        $avg_sale_price = $product->variants->where('discount_price', '>', 0)->avg('discount_price');
        $avg_buy_price = $product->variants->where('buy_price', '>', 0)->avg('buy_price');
        $allPackagingOptions = PackagingOption::where('is_active', true)->get();
        
        return view('admin.products.edit', compact('product', 'categories', 'sizes', 'colors', 'buySources', 'allProducts', 'allVideos', 'allPackagingOptions', 'avg_sale_price', 'avg_buy_price'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'hover_image_id' => 'nullable|exists:product_images,id',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'care_and_maintenance' => 'nullable|string',
            'invoice_number' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('products', 'invoice_number')->ignore($product->id)
            ],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('products')->ignore($product->id)],
            'is_visible' => 'boolean',
            'is_for_men' => 'boolean',
            'is_for_women' => 'boolean',
            'is_for_kids' => 'boolean',
            
            'apple_title' => 'nullable|string|max:255',
            'apple_description' => 'nullable|string',

            'variants' => 'nullable|array',
            'variants.*.sku' => 'nullable|string|max:255',
            'variants.*.qr_code' => 'nullable|string|max:255',
            
            'related_product_ids' => 'nullable|array',
            'related_product_ids.*' => 'exists:products,id',

            'video_ids' => 'nullable|array',
            'video_ids.*' => 'exists:videos,id',
            
            'packaging_option_ids' => 'nullable|array',
            'packaging_option_ids.*' => 'exists:packaging_options,id'
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']) . '-' . $product->id;
        }

        $validated['is_visible'] = $request->boolean('is_visible');
        $validated['is_for_men'] = $request->boolean('is_for_men');
        $validated['is_for_women'] = $request->boolean('is_for_women');
        $validated['is_for_kids'] = $request->boolean('is_for_kids');

        $product->update($validated);

        if ($request->has('related_product_ids')) {
            $product->relatedProducts()->sync($request->related_product_ids);
        } else {
            $product->relatedProducts()->sync([]);
        }

        if ($request->has('video_ids')) {
            $product->videos()->sync($request->video_ids);
        } else {
            $product->videos()->sync([]);
        }

        if ($request->has('packaging_option_ids')) {
            $product->packagingOptions()->sync($request->packaging_option_ids);
        } else {
            $product->packagingOptions()->sync([]);
        }
        
        // 💡 همگام‌سازی با وردپرس بعد از آپدیت
        $this->syncProductToWordPress($product);

        return redirect()->route('admin.products.edit', $product)
            ->with('success', 'محصول با موفقیت به‌روزرسانی و با سایت همگام‌سازی شد.');
    }

    public function destroy(Product $product)
    {
        // 💡 1. Store the ID before deleting the product
        $productId = $product->id; 

        $product->load('images', 'videos');

        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->path);
        }
        
        // 2. Delete from Laravel
        $product->delete(); 

        // 💡 3. Now use the stored ID to delete from WordPress
        $this->deleteProductFromWordPress($productId);

        return redirect()->route('admin.products.index')
            ->with('success', 'محصول (و تمام فایل‌های مرتبط) با موفقیت حذف شد.');
    }

    private function getSizeList(): array
    {
        $sizes = [];
        for ($i = 36.5; $i <= 47; $i += 0.5) {
            $sizes[] = (string)$i;
        }
        return $sizes;
    }

    /**
     * 💡 متد اختصاصی برای ارسال اطلاعات محصول به سایت وردپرسی آکامد
     */
    private function syncProductToWordPress($product)
    {
        // 1. لود کردن تمام وابستگی‌های محصول برای ارسال کامل
        $product->loadMissing(['category', 'variants', 'images', 'relatedProducts', 'videos', 'packagingOptions']);

        // 2. آماده‌سازی لیست تصاویر با لینک کامل دانلود
        $imageUrls = [];
        
        // تشخیص اتوماتیک آدرس سرور (مثلاً: https://panel.akamode.com)
        $remoteStorage = rtrim(config('app.url'), '/') . '/storage/'; 
      
      $sortedImages = $product->images->sortBy('order');

        foreach ($sortedImages as $img) {
            $imageUrls[] = [
                'id' => $img->id,
                'path' => $img->path,
                'url' => $remoteStorage . ltrim($img->path, '/'),
                'color' => $img->color // <-- اضافه کردن رنگ برای وردپرس
            ];
        }

        // 3. پیدا کردن اسلاگ دسته‌بندی برای ست کردن در وردپرس
        $categorySlug = $product->category ? $product->category->slug : null;

        $allColors = Color::all(); 

        $formattedVariants = $product->variants->map(function ($variant) use ($allColors) {
            $vArray = $variant->toArray();
            
            // پیدا کردن رنگ متناظر از دیتابیس بر اساس نام ذخیره شده در واریانت
            $colorObj = $allColors->where('name', $variant->color)->first();
            
            if ($colorObj) {
                $vArray['color'] = [
                    'name'         => $colorObj->name,
                    'persian_name' => $colorObj->persian_name, // <-- Fixed to pull persian_name
                ];
            } else {
                $vArray['color'] = [
                    'name'         => $variant->color,
                    'persian_name' => $variant->color,
                ];
            }
            
            return $vArray;
        })->toArray();

        // 4. بسته‌بندی 100% تمام فیلدهای موجود برای وردپرس
        $data = [
            'id'                   => $product->id,
            'name'                 => $product->name,
            'slug'                 => $product->slug,
            'description'          => $product->description,
            'care_and_maintenance' => $product->care_and_maintenance,
            'category_id'          => $product->category_id,
            'category_slug'        => $categorySlug, 
            'invoice_number'       => $product->invoice_number,
            'is_visible'           => $product->is_visible,
            'is_for_men'           => $product->is_for_men,
            'is_for_women'         => $product->is_for_women,
            'is_for_kids'          => $product->is_for_kids,
            'apple_title'          => $product->apple_title,
            'apple_description'    => $product->apple_description,
            
            // آرایه‌های پیچیده
            'variants'                 => $formattedVariants,
            'images'                   => $imageUrls,
            'related_product_ids'      => $product->relatedProducts->pluck('id')->toArray(),
            'video_ids'                => $product->videos->pluck('id')->toArray(),
            'packaging_options'        => $product->packagingOptions->map(function($opt) {
                return [
                    'id'    => $opt->id,
                    'name'  => $opt->name,
                    'price' => $opt->price
                ];
            })->toArray(),
        ];

        try {
            // آدرس سایت وردپرسی (حتماً در فایل env. لاراول خود، WP_AKAMODE_SECRET را تعریف کنید)
            $wpUrl = env('WP_AKAMODE_URL', 'https://akamode.com') . '/wp-json/akamode/v1/sync-product';
            $secret = env('WP_AKAMODE_SECRET', 'slafLKlskggslf@34rfkljw');

            $response = Http::timeout(10)->withHeaders([
                'X-Akamode-Secret' => $secret
            ])->post($wpUrl, $data); 

            if ($response->failed()) {
                Log::error('WP Product Sync Failed. Status: ' . $response->status() . ' | Response: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('WP Product Sync Connection Error: ' . $e->getMessage());
        }
    }

    /**
     * 💡 ارسال درخواست حذف محصول به وردپرس
     */
    private function deleteProductFromWordPress($productId)
    {
        try {
            $wpUrl = env('WP_AKAMODE_URL', 'https://akamode.com') . '/wp-json/akamode/v1/delete-product';
            $secret = env('WP_AKAMODE_SECRET', 'slafLKlskggslf@34rfkljw');

            // Send a POST request with the ID to be deleted
            $response = Http::timeout(10)->withHeaders([
                'X-Akamode-Secret' => $secret
            ])->post($wpUrl, [
                'id' => $productId
            ]); 

            if ($response->failed()) {
                Log::error('WP Product Delete Failed. Status: ' . $response->status() . ' | Response: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('WP Product Delete Connection Error: ' . $e->getMessage());
        }
    }

    // متد جدید برای آپدیت رنگ تصویر
    public function updateImageColor(Request $request, $id)
    {
        $image = \App\Models\ProductImage::findOrFail($id);
        $image->color = $request->color;
        $image->save();

        return response()->json(['status' => 'success']);
    }
}