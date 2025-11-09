<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PackagingOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PackagingOptionController extends Controller
{
    /**
     * Display a listing of the resource.
     * (لیست تمام گزینه‌ها را نشان می‌دهد)
     */
    public function index()
    {
        $packagingOptions = PackagingOption::orderBy('price')->get();
        return view('admin.packaging-options.index', compact('packagingOptions'));
    }

    /**
     * Show the form for creating a new resource.
     * (فرم ایجاد گزینه‌ی جدید را نشان می‌دهد)
     */
    public function create()
    {
        // Pass an empty object to reuse the form
        $option = new PackagingOption(['is_active' => true]); // Set default value
        return view('admin.packaging-options.create', compact('option'));
    }

    /**
     * Store a newly created resource in storage.
     * (گزینه‌ی جدید را در دیتابیس ذخیره می‌کند)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'is_active' => 'nullable|boolean', // Handles 'on' or missing
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = [
            'name' => $validated['name'],
            'price' => $validated['price'],
            'is_active' => $request->boolean('is_active'),
        ];

        // --- مدیریت آپلود تصویر ---
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('packaging', 'public');
            $data['image_path'] = $path;
        }
        // --- پایان بخش جدید ---

        PackagingOption::create($data);

        return redirect()->route('admin.packaging-options.index')
            ->with('success', 'گزینه بسته‌بندی جدید با موفقیت ایجاد شد.');
    }

    /**
     * Show the form for editing the specified resource.
     * (فرم ویرایش یک گزینه‌ی موجود را نشان می‌دهد)
     */
    public function edit(PackagingOption $packagingOption)
    {
        // Pass the existing option to the edit view
        return view('admin.packaging-options.edit', ['option' => $packagingOption]);
    }

    /**
     * Update the specified resource in storage.
     * (تغییرات گزینه‌ی ویرایش‌شده را در دیتابیس ذخیره می‌کند)
     */
    public function update(Request $request, PackagingOption $packagingOption)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'is_active' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = [
            'name' => $validated['name'],
            'price' => $validated['price'],
            'is_active' => $request->boolean('is_active'),
        ];

        if ($request->hasFile('image')) {
            // ۱. حذف تصویر قدیمی (اگر وجود داشت)
            if ($packagingOption->image_path) {
                Storage::disk('public')->delete($packagingOption->image_path);
            }
            // ۲. ذخیره تصویر جدید
            $path = $request->file('image')->store('packaging', 'public');
            $data['image_path'] = $path;
        }

        $packagingOption->update($data);

        return redirect()->route('admin.packaging-options.index')
            ->with('success', 'گزینه بسته‌بندی با موفقیت به‌روزرسانی شد.');
    }

    /**
     * Remove the specified resource from storage.
     * (گزینه‌ی انتخاب‌شده را حذف می‌کند)
     */
    public function destroy(PackagingOption $packagingOption)
    {
        try {
            if ($packagingOption->image_path) {
                Storage::disk('public')->delete($packagingOption->image_path);
            }
            // Delete the option
            $packagingOption->delete();
            return redirect()->route('admin.packaging-options.index')
                ->with('success', 'گزینه بسته‌بندی با موفقیت حذف شد.');
        } catch (\Exception $e) {
            // Handle potential database constraint errors (if it's used in an old order)
            return redirect()->route('admin.packaging-options.index')
                ->with('error', 'امکان حذف این گزینه وجود ندارد، ممکن است در سفارشات قبلی استفاده شده باشد.');
        }
    }
}