<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuItemController extends Controller
{
    // متد کمکی برای دریافت آیتم‌ها جهت نمایش در منوی کشویی (Dropdown)
    private function getMenuItemsList()
    {
        return MenuItem::orderBy('name')->get();
    }

    public function index()
    {
        // دریافت همه آیتم‌ها از دیتابیس
        $allMenuItems = MenuItem::with('parent')
                            ->orderBy('menu_group')
                            ->orderBy('parent_id')
                            ->orderBy('order')
                            ->get();

        // جدا کردن منوهای اصلی (بدون والد)
        $rootItems = $allMenuItems->whereNull('parent_id')->values();

        // گروه‌بندی زیرمنوها بر اساس آیدی والدشان (برای نمایش سریع درختی)
        $groupedChildren = $allMenuItems->whereNotNull('parent_id')->groupBy('parent_id');
                            
        return view('admin.menu-items.index', compact('rootItems', 'groupedChildren'));
    }

    public function create()
    {
        $menuItem = new MenuItem(['order' => 0]);
        $menuItems = $this->getMenuItemsList(); 
        $categories = Category::orderBy('name')->get(); 

        return view('admin.menu-items.create', compact('menuItem', 'menuItems', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'link_url' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:menu_items,id',
            'menu_group' => 'required|string|max:100',
            'order' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->except('image');

        // آپلود تصویر دقیقاً در مسیر menu-items
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('menu-items', 'public');
        }

        MenuItem::create($data);

        return redirect()->route('admin.menu-items.index')->with('success', 'آیتم منو با موفقیت ایجاد شد.');
    }

    public function edit(MenuItem $menuItem)
    {
        // دریافت تمام آیتم‌ها به جز خودش (برای جلوگیری از خطای بصری)
        $menuItems = MenuItem::where('id', '!=', $menuItem->id)->orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('admin.menu-items.edit', compact('menuItem', 'menuItems', 'categories'));
    }

    public function update(Request $request, MenuItem $menuItem)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'link_url' => 'required|string|max:255',
            'parent_id' => [
                'nullable',
                'exists:menu_items,id',
                // جلوگیری از تنظیم کردن خود آیتم به عنوان والد (دقیقاً مثل سیستم اتوماسیون)
                function ($attribute, $value, $fail) use ($menuItem) {
                    if ($value == $menuItem->id) {
                        $fail('یک آیتم نمی‌تواند والد خودش باشد.');
                    }
                },
            ],
            'menu_group' => 'required|string|max:100',
            'order' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->except('image');

        // آپلود تصویر جدید و حذف تصویر قبلی از سرور
        if ($request->hasFile('image')) {
            if ($menuItem->image_path) {
                Storage::disk('public')->delete($menuItem->image_path);
            }
            $data['image_path'] = $request->file('image')->store('menu-items', 'public');
        }

        $menuItem->update($data);

        return redirect()->route('admin.menu-items.index')->with('success', 'آیتم منو با موفقیت به‌روزرسانی شد.');
    }

    public function destroy(MenuItem $menuItem)
    {
        // حذف عکس فیزیکی از روی سرور قبل از پاک کردن رکورد
        if ($menuItem->image_path) {
            Storage::disk('public')->delete($menuItem->image_path);
        }
        
        $menuItem->delete();
        
        return redirect()->route('admin.menu-items.index')->with('success', 'آیتم منو (و زیرمجموعه‌های آن) با موفقیت حذف شد.');
    }
}