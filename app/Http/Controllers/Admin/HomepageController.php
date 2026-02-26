<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HomepageController extends Controller
{
    public function edit()
    {
        // دریافت تنظیمات ذخیره شده از دیتابیس
        $setting = DB::table('settings')->where('key', 'homepage_data')->first();
        $data = $setting ? json_decode($setting->value, true) : [];

        return view('admin.homepage.edit', compact('data'));
    }

    public function update(Request $request)
    {
        $oldSetting = DB::table('settings')->where('key', 'homepage_data')->first();
        $oldData = $oldSetting ? json_decode($oldSetting->value, true) : [];

        // دریافت تمام دیتای ارسال شده از فرم
        $data = $request->except(['_token', '_method']);

        $remoteStorage = rtrim(config('app.url'), '/') . '/storage/';

        // ۱. مدیریت آپلود عکس‌های اسلایدر اصلی
        if (isset($data['main_slider'])) {
            foreach ($data['main_slider'] as $index => $slide) {
                // عکس بک‌گراند اسلاید
                if ($request->hasFile("main_slider.{$index}.image")) {
                    $path = $request->file("main_slider.{$index}.image")->store('homepage', 'public');
                    $data['main_slider'][$index]['image'] = $remoteStorage . ltrim($path, '/');
                } else {
                    $data['main_slider'][$index]['image'] = $oldData['main_slider'][$index]['image'] ?? '';
                }
                
                // عکس بج (کوچک) اسلاید
                if ($request->hasFile("main_slider.{$index}.badge_img")) {
                    $path = $request->file("main_slider.{$index}.badge_img")->store('homepage', 'public');
                    $data['main_slider'][$index]['badge_img'] = $remoteStorage . ltrim($path, '/');
                } else {
                    $data['main_slider'][$index]['badge_img'] = $oldData['main_slider'][$index]['badge_img'] ?? '';
                }
            }
        }

        // ۲. مدیریت آپلود عکس‌های میانی (دو بنر کنار هم)
        if ($request->hasFile('middle_images.image_1')) {
            $path = $request->file('middle_images.image_1')->store('homepage', 'public');
            $data['middle_images']['image_1'] = $remoteStorage . ltrim($path, '/');
        } else {
            $data['middle_images']['image_1'] = $oldData['middle_images']['image_1'] ?? '';
        }

        if ($request->hasFile('middle_images.image_2')) {
            $path = $request->file('middle_images.image_2')->store('homepage', 'public');
            $data['middle_images']['image_2'] = $remoteStorage . ltrim($path, '/');
        } else {
            $data['middle_images']['image_2'] = $oldData['middle_images']['image_2'] ?? '';
        }

        // ذخیره در دیتابیس لاراول
        DB::table('settings')->updateOrInsert(
            ['key' => 'homepage_data'],
            ['value' => json_encode($data), 'updated_at' => now()]
        );

        // ارسال داینامیک اطلاعات به وردپرس
        $this->syncToWordPress($data);

        return redirect()->back()->with('success', 'تنظیمات صفحه اصلی با موفقیت ذخیره و در سایت اعمال شد.');
    }

    private function syncToWordPress($data)
    {
        try {
            $wpUrl = env('WP_AKAMODE_URL', 'https://akamode.com') . '/wp-json/akamode/v1/sync-homepage';
            $secret = env('WP_AKAMODE_SECRET', 'slafLKlskggslf@34rfkljw');

            $response = Http::timeout(10)->withHeaders([
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