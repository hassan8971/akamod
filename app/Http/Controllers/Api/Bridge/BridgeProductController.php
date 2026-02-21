<?php

namespace App\Http\Controllers\Api\Bridge;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BridgeProductController extends Controller
{
    public function index(Request $request)
    {
        // 1. دریافت محصولات با فیلتر احتمالی
        $query = Product::with(['category', 'admin']); // ادمین رو هم اضافه کردیم

        // اگر فیلتر دسته بندی خواسته شده بود
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->latest()->paginate(12);

        // 2. دریافت لیست کل دسته‌بندی‌ها برای منوی فیلتر
        $categories = \App\Models\Category::select('id', 'name')->get();

        // 3. ارسال هر دو داده
        return response()->json([
            'products' => $products,
            'categories' => $categories
        ]);
    }

    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            
            // حذف تصویر (اختیاری - اگر نیاز است فایل هم پاک شود)
            // if ($product->primaryImage) {
            //     \Storage::delete($product->primaryImage->path);
            // }

            $product->delete();

            return response()->json(['message' => 'Product deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error deleting product'], 500);
        }
    }

    public function store(Request $request)
    {
        // 1. اعتبارسنجی فقط برای فیلدهای اجباری اصلی
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name' => 'required|string',
            'category_id' => 'required',
            // بقیه موارد مثل variants و images اینجا اجباری نیستند
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            // 2. ساخت محصول اصلی (این همیشه انجام می‌شود)
            $product = \App\Models\Product::create([
                'name' => $request->name,
                'slug' => $request->slug ?? \Illuminate\Support\Str::slug($request->name) . '-' . time(),
                'category_id' => $request->category_id,
                'description' => $request->description,
                'care_and_maintenance' => $request->care_and_maintenance,
                'invoice_number' => $request->invoice_number,
                'is_visible' => $request->boolean('is_visible', true), // پیش‌فرض true اگر ارسال نشد
                'is_for_men' => $request->boolean('is_for_men', false),
                'is_for_women' => $request->boolean('is_for_women', false),
                'is_for_kids' => $request->boolean('is_for_kids', false),
                'apple_title' => $request->apple_title,
                'apple_description' => $request->apple_description,
                'admin_id' => 1, // آی‌دی ادمین پیش‌فرض
            ]);

            // 3. ثبت متغیرها (Variants) - فقط اگر معتبر باشند
            $variants = is_string($request->variants) ? json_decode($request->variants, true) : $request->variants;
            
            if (!empty($variants) && is_array($variants)) {
                foreach ($variants as $variant) {
                    // شرط حیاتی: اگر سایز یا رنگ خالی بود، این ردیف را نادیده بگیر و رد شو
                    // این خط باعث می‌شود خطای SQL برطرف شود
                    if (empty($variant['size']) || empty($variant['color'])) {
                        continue; 
                    }

                    // پر کردن مقادیر پیش‌فرض برای جلوگیری از خطای دیتابیس
                    $product->variants()->create([
                        'size' => $variant['size'],
                        'color' => $variant['color'],
                        'price' => $variant['price'] ?? 0,
                        'discount_price' => $variant['discount_price'] ?? null,
                        'buy_price' => $variant['buy_price'] ?? null,
                        'stock' => $variant['stock'] ?? 0,
                        'buy_source_id' => $variant['buy_source_id'] ?? null,
                        'sku' => $variant['sku'] ?? null,
                    ]);
                }
            }

            // 4. آپلود تصاویر (Images) - کاملاً اختیاری
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $path = $file->store('products', 'public');
                    $product->images()->create([
                        'path' => $path,
                        'alt_text' => $product->name
                    ]);
                }
            }

            // 5. ویدیوها (Videos) - کاملاً اختیاری
            if (!empty($request->video_ids)) {
                $videoIds = is_string($request->video_ids) ? explode(',', $request->video_ids) : $request->video_ids;
                // فیلتر کردن مقادیر خالی احتمالی
                $videoIds = array_filter($videoIds);
                if (!empty($videoIds)) {
                    $product->videos()->sync($videoIds);
                }
            }
            
            // 6. محصولات مرتبط (Related Products) - کاملاً اختیاری
            if (!empty($request->related_product_ids)) {
                $ids = is_string($request->related_product_ids) ? explode(',', $request->related_product_ids) : $request->related_product_ids;
                $ids = array_filter($ids);
                if (!empty($ids)) {
                    $product->relatedProducts()->sync($ids);
                }
            }

            // 7. بسته‌بندی (Packaging) - کاملاً اختیاری
            if (!empty($request->packaging_option_ids)) {
                $ids = is_string($request->packaging_option_ids) ? explode(',', $request->packaging_option_ids) : $request->packaging_option_ids;
                $ids = array_filter($ids);
                if (!empty($ids)) {
                    $product->packagingOptions()->sync($ids);
                }
            }

            $this->syncProductToWordPress($product);

            \Illuminate\Support\Facades\DB::commit();
            return response()->json(['message' => 'Created', 'id' => $product->id], 201);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            // لاگ کردن خطا برای دیباگ بهتر در آینده
            \Illuminate\Support\Facades\Log::error('Remote Create Error: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function createResources()
    {
        return response()->json([
            'categories' => \App\Models\Category::all(),
            'sizes' => \App\Models\Size::orderBy('name')->get(),
            'colors' => \App\Models\Color::orderBy('name')->get(),
            'buy_sources' => \App\Models\BuySource::orderBy('name')->get(),
            'products' => \App\Models\Product::select('id', 'name')->get(),
            'videos' => \App\Models\Video::all(),
            'packaging_options' => \App\Models\PackagingOption::where('is_active', true)->get(),
        ]);
    }
    // 1. متد دریافت اطلاعات محصول برای ویرایش (شامل تمام لیست‌ها)
    public function show($id)
    {
        $product = \App\Models\Product::with(['variants.buySource', 'images', 'videos', 'relatedProducts', 'hoverImage', 'packagingOptions'])->findOrFail($id);

        return response()->json([
            'product' => $product,
            // ارسال لیست‌های مورد نیاز برای دراپ‌داون‌ها
            'categories' => \App\Models\Category::all(),
            'sizes' => \App\Models\Size::orderBy('name')->get(),
            'colors' => \App\Models\Color::orderBy('name')->get(),
            'buy_sources' => \App\Models\BuySource::orderBy('name')->get(),
            'products_list' => \App\Models\Product::where('id', '!=', $id)->select('id', 'name')->get(), // برای محصولات مرتبط
            'videos_list' => \App\Models\Video::all(),
            'packaging_options' => \App\Models\PackagingOption::where('is_active', true)->get(),
        ]);
    }

    // 2. آپدیت کلی محصول
    public function update(Request $request, $id)
    {
        $product = \App\Models\Product::findOrFail($id);

        $request->merge([
            'is_for_men' => $request->boolean('is_for_men'),
            'is_for_women' => $request->boolean('is_for_women'),
            'is_for_kids' => $request->boolean('is_for_kids'), // <--- فیلد جدید
            'is_visible' => $request->boolean('is_visible'),
        ]);
        
        // آپدیت فیلدهای اصلی
        $product->update($request->except(['variants', 'images', 'video_ids', 'related_product_ids', 'packaging_option_ids']));

        // سینک کردن روابط (اگر ارسال شده باشند)
        if ($request->has('video_ids')) {
             $ids = is_string($request->video_ids) ? explode(',', $request->video_ids) : $request->video_ids;
             $product->videos()->sync(array_filter($ids));
        }
        if ($request->has('related_product_ids')) {
             $ids = is_string($request->related_product_ids) ? explode(',', $request->related_product_ids) : $request->related_product_ids;
             $product->relatedProducts()->sync(array_filter($ids));
        }
        if ($request->has('packaging_option_ids')) {
             $ids = is_string($request->packaging_option_ids) ? explode(',', $request->packaging_option_ids) : $request->packaging_option_ids;
             $product->packagingOptions()->sync(array_filter($ids));
        }

        $this->syncProductToWordPress($product);

        return response()->json(['message' => 'Updated successfully']);
    }

    // 3. متدهای اختصاصی واریانت
    public function storeVariant(Request $request, $id)
    {
        $product = \App\Models\Product::findOrFail($id);
        $product->variants()->create($request->all());
        return response()->json(['message' => 'Variant added']);
    }

    public function destroyVariant($variantId)
    {
        \App\Models\ProductVariant::destroy($variantId);
        return response()->json(['message' => 'Variant deleted']);
    }

    // 4. متدهای اختصاصی تصویر
    public function storeImage(Request $request, $id)
    {
        $product = \App\Models\Product::findOrFail($id);
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('products', 'public');
                $product->images()->create(['path' => $path, 'alt_text' => $product->name]);
            }
        }
        return response()->json(['message' => 'Images uploaded']);
    }

    public function destroyImage($imageId)
    {
        $image = \App\Models\ProductImage::findOrFail($imageId);
        \Illuminate\Support\Facades\Storage::disk('public')->delete($image->path);
        $image->delete();
        return response()->json(['message' => 'Image deleted']);
    }
    
    public function reorderImages(Request $request)
    {
        foreach ($request->images as $index => $id) {
            \App\Models\ProductImage::where('id', $id)->update(['order' => $index]);
        }
        return response()->json(['status' => 'success']);
    }

    private function syncProductToWordPress($product)
    {
        // 1. لود کردن تمام وابستگی‌های محصول برای ارسال کامل
        $product->loadMissing(['category', 'variants', 'images', 'relatedProducts', 'videos', 'packagingOptions']);

        // 2. آماده‌سازی لیست تصاویر با لینک کامل دانلود
        $imageUrls = [];
        $remoteStorage = 'https://api.akamode.com/storage/'; // استفاده از آدرس دقیق لاراول شما

        foreach ($product->images as $img) {
            $imageUrls[] = [
                'id' => $img->id,
                'path' => $img->path,
                // ساخت لینک دقیق برای جلوگیری از خطای asset()
                'url' => $remoteStorage . ltrim($img->path, '/') 
            ];
        }

        // 3. پیدا کردن اسلاگ دسته‌بندی برای ست کردن در وردپرس
        $categorySlug = $product->category ? $product->category->slug : null;

        $allColors = \App\Models\Color::all(); 

        $formattedVariants = $product->variants->map(function ($variant) use ($allColors) {
            $vArray = $variant->toArray();
            
            // پیدا کردن رنگ متناظر از دیتابیس بر اساس نام ذخیره شده در واریانت
            $colorObj = $allColors->where('name', $variant->color)->first();
            
            if ($colorObj) {
                // ساختاری که وردپرس انتظار دارد
                $vArray['color'] = [
                    'name'         => $colorObj->name,
                    // اگر فیلد نام فارسی در دیتابیس شما اسم دیگری دارد (مثل fa_name)، اینجا اصلاحش کنید
                    'persian_name' => $colorObj->persian_name ?? $colorObj->name, 
                ];
            } else {
                // اگر رنگ پیدا نشد، همان نام ساده را بفرست
                $vArray['color'] = [
                    'name'         => $variant->color,
                    'persian_name' => $variant->color,
                ];
            }
            
            return $vArray;
        })->toArray();

        // 4. بسته‌بندی 100% تمام فیلدهای موجود در فرم اتوماسیون
        $data = [
            'id'                   => $product->id,
            'name'                 => $product->name,
            'slug'                 => $product->slug,
            'description'          => $product->description,
            'care_and_maintenance' => $product->care_and_maintenance,
            'category_id'          => $product->category_id,
            'category_slug'        => $categorySlug, // بسیار مهم برای وردپرس
            'invoice_number'       => $product->invoice_number,
            'is_visible'           => $product->is_visible,
            'is_for_men'           => $product->is_for_men,
            'is_for_women'         => $product->is_for_women,
            'is_for_kids'          => $product->is_for_kids,
            'apple_title'          => $product->apple_title,
            'apple_description'    => $product->apple_description,
            
            // آرایه‌های پیچیده
            'variants'             => $formattedVariants,
            'images'                   => $imageUrls,
            'related_product_ids'      => $product->relatedProducts->pluck('id')->toArray(),
            'video_ids'                => $product->videos->pluck('id')->toArray(),
            'packaging_option_ids'     => $product->packagingOptions->pluck('id')->toArray(),
        ];

        try {
            $response = Http::withHeaders([
                'X-Akamode-Secret' => env('WP_AKAMODE_SECRET', 'slafLKlskggslf@34rfkljw')
            ])->post('https://akamode.com/wp-json/akamode/v1/sync-product', $data); // آدرس سایت وردپرسی‌ات

            if ($response->failed()) {
                \Log::error('WP Product Sync Failed: ' . $response->body());
            }
        } catch (\Exception $e) {
            \Log::error('WP Product Sync Connection Error: ' . $e->getMessage());
        }
    }
}