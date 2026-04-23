<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;

class HomepageController extends Controller
{
    public function edit()
    {
        $setting = DB::table('settings')->where('key', 'homepage_data')->first();
        $data = $setting ? json_decode($setting->value, true) : [];

        if (empty($data['main_slider'])) $data['main_slider'] = [[]];
        if (empty($data['category_grid'])) $data['category_grid'] = [[]];
        if (empty($data['info_accordions'])) $data['info_accordions'] = [[]];

        // 💡 دریافت لیست دسته‌بندی‌ها و محصولات برای نمایش در فرم
        $categories = \App\Models\Category::select('id', 'name', 'slug')->get();
        $products = \App\Models\Product::select('id', 'name', 'slug')->latest()->get();

        return view('admin.homepage.edit', compact('data', 'categories', 'products'));
    }

    public function update(Request $request)
    {
        $oldSetting = DB::table('settings')->where('key', 'homepage_data')->first();
        $oldData = $oldSetting ? json_decode($oldSetting->value, true) : [];

        // کل دیتای ورودی را می‌گیریم (بدون فایل‌ها)
        $data = $request->except(['_token', '_method']);
        $remoteStorage = rtrim(config('app.url'), '/') . '/storage/';

        // 1. پردازش آرایه پویا: اسلایدر اصلی
        $sliderData = [];
        if ($request->has('main_slider')) {
            foreach ($request->input('main_slider') as $index => $slide) {
                if ($request->hasFile("main_slider.{$index}.image")) {
                    $path = $request->file("main_slider.{$index}.image")->store('homepage', 'public');
                    $slide['image'] = $remoteStorage . ltrim($path, '/');
                } else {
                    $slide['image'] = $oldData['main_slider'][$index]['image'] ?? '';
                }
                if ($request->hasFile("main_slider.{$index}.badge_img")) {
                    $path = $request->file("main_slider.{$index}.badge_img")->store('homepage', 'public');
                    $slide['badge_img'] = $remoteStorage . ltrim($path, '/');
                } else {
                    $slide['badge_img'] = $oldData['main_slider'][$index]['badge_img'] ?? '';
                }
                $sliderData[] = $slide;
            }
        }
        $data['main_slider'] = $sliderData;

        // 2. پردازش آرایه پویا: گرید عکس‌های دسته‌بندی
        $catGridData = [];
        if ($request->has('category_grid')) {
            foreach ($request->input('category_grid') as $index => $item) {
                if ($request->hasFile("category_grid.{$index}.image")) {
                    $path = $request->file("category_grid.{$index}.image")->store('homepage', 'public');
                    $item['image'] = $remoteStorage . ltrim($path, '/');
                } else {
                    $item['image'] = $oldData['category_grid'][$index]['image'] ?? '';
                }
                $catGridData[] = $item;
            }
        }
        $data['category_grid'] = $catGridData;

        // 3. آپلود تصاویر تکی
        $singleImageFields = [
            'middle_images.image_1', 'middle_images.image_2',
            'authentic_section.big_card.image', 'authentic_section.small_card_1.image', 'authentic_section.small_card_2.image',
            'carousel_2.background_image',
            'carousel_3.side_image',
            'info_section.image_1', 'info_section.image_2'
        ];

        foreach ($singleImageFields as $fieldPath) {
            if ($request->hasFile($fieldPath)) {
                $path = $request->file($fieldPath)->store('homepage', 'public');
                Arr::set($data, $fieldPath, $remoteStorage . ltrim($path, '/'));
            } else {
                Arr::set($data, $fieldPath, Arr::get($oldData, $fieldPath, ''));
            }
        }

        // 4. پردازش کاروسل‌ها و آیتم‌های سفارشی (Custom Items)
        $carousels = ['carousel_1', 'carousel_2', 'carousel_3'];
        foreach ($carousels as $carousel) {
            if (isset($data[$carousel]['query_type']) && $data[$carousel]['query_type'] === 'custom') {
                $customItems = [];
                if ($request->has("{$carousel}.custom_items")) {
                    foreach ($request->input("{$carousel}.custom_items") as $index => $item) {
                        // آپلود عکس مربوط به آیتم سفارشی
                        if ($request->hasFile("{$carousel}.custom_items.{$index}.image")) {
                            $path = $request->file("{$carousel}.custom_items.{$index}.image")->store('homepage', 'public');
                            $item['image'] = $remoteStorage . ltrim($path, '/');
                        } else {
                            // در صورت عدم تغییر، عکس قبلی را نگه دار
                            $item['image'] = Arr::get($oldData, "{$carousel}.custom_items.{$index}.image", '');
                        }
                        $customItems[] = $item;
                    }
                }
                Arr::set($data, "{$carousel}.custom_items", $customItems);
            } else {
                // اگر نوع کاروسل سفارشی نیست، آرایه آیتم‌های سفارشی را خالی کن تا حجم دیتابیس اشغال نشود
                Arr::set($data, "{$carousel}.custom_items", []);
            }
        }

        // 5. پردازش آرایه پویا: آکاردئون‌ها
        $data['info_accordions'] = $request->input('info_accordions', []);

        // ذخیره در دیتابیس
        DB::table('settings')->updateOrInsert(
            ['key' => 'homepage_data'],
            ['value' => json_encode($data), 'updated_at' => now()]
        );

        // همگام‌سازی با وردپرس
        $this->syncToWordPress($data);

        return redirect()->back()->with('success', 'تمام تنظیمات صفحه اصلی با موفقیت ذخیره و در سایت اعمال شد.');
    }

    private function syncToWordPress($data)
    {
        try {
            $wpUrl = env('WP_AKAMODE_URL', 'http://akamode.com') . '/wp-json/akamode/v1/sync-homepage';
            $secret = env('WP_AKAMODE_SECRET', 'slafLKlskggslf@34rfkljw');

            $response = Http::timeout(15)->withHeaders([
                'X-Akamode-Secret' => $secret
            ])->post($wpUrl, $data);

            if ($response->failed()) {
                Log::error('WP Homepage Sync Failed: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('WP Homepage Sync Error: ' . $e->getMessage());
        }
    }
}