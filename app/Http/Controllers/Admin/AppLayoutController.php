<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppPage;
use App\Models\AppSection;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AppLayoutController extends Controller
{
    public function index(Request $request)
    {
        $pages = AppPage::where('platform', 'web')->get();
        return view('admin.app_layouts.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.app_layouts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:app_pages,slug',
            'platform' => 'required|in:web,android,ios',
        ]);

        $page = AppPage::create([
            'title' => $request->title,
            'slug' => $request->slug,
            'platform' => $request->platform,
            'is_active' => true,
        ]);

        return redirect()->route('admin.layouts.builder', $page->id)
            ->with('success', 'صفحه جدید ساخته شد. حالا می‌توانید آن را طراحی کنید.');
    }

    public function edit(AppPage $page)
    {
        $page->load(['sections' => function($q) {
            $q->orderBy('sort_order');
        }]);

        $products = Product::select('id', 'name', 'created_at')
            ->with('images', 'variants')
            ->latest()
            ->get();

        return view('admin.app_layouts.builder', compact('page', 'products'));
    }

    public function saveAll(Request $request, AppPage $page)
    {
        $data = $request->validate([
            'sections' => 'array',
        ]);

        DB::transaction(function () use ($page, $data) {
            // 1. ذخیره سکشن‌ها
            $orderCounter = 0;
            if (isset($data['sections'])) {
                // نگه داشتن ID هایی که در ریکوئست هستند برای حذف بقیه
                $keptIds = [];

                foreach ($data['sections'] as $secData) {
                    $sectionData = [
                        'type' => $secData['type'],
                        'title' => $secData['title'] ?? null,
                        'source_type' => $secData['source_type'] ?? 'auto',
                        'config' => $secData['config'] ?? [], 
                        'sort_order' => $orderCounter++,
                        'app_page_id' => $page->id,
                        'is_visible' => true
                    ];

                    if (isset($secData['is_new']) && $secData['is_new']) {
                        // ایجاد آیتم جدید
                        $newSection = AppSection::create($sectionData);
                        $keptIds[] = $newSection->id;
                    } else {
                        // آپدیت آیتم موجود
                        // FIX: استفاده از find و سپس update برای فعال شدن Casting
                        $section = AppSection::find($secData['id']);
                        if ($section) {
                            $section->update($sectionData);
                            $keptIds[] = $section->id;
                        }
                    }
                }

                // حذف سکشن‌هایی که در لیست جدید نیستند
                $page->sections()->whereNotIn('id', $keptIds)->delete();
            }

        
        });

        return response()->json(['success' => true]);
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('layout_images', 'public');
            return response()->json([
                'success' => true,
                'url' => asset('storage/' . $path),
                'path' => $path
            ]);
        }

        return response()->json(['success' => false], 400);
    }

    public function uploadVideo(Request $request)
    {
        $request->validate([
            'video' => 'required|video|max:50000',
        ]);

        if ($request->hasFile('video')) {
            $path = $request->file('video')->store('layout_videos', 'public');
            return response()->json([
                'success' => true,
                'url' => asset('storage/' . $path),
                'path' => $path
            ]);
        }

        return response()->json(['success' => false], 400);
    }

    public function destroySection(AppSection $section)
    {
        $section->delete();
        return back()->with('success', 'بخش حذف شد.');
    }
}