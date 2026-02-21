<?php

namespace App\Http\Controllers\Api\Bridge;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BridgeMenuItemController extends Controller
{
    public function index()
    {
        // دریافت همه آیتم‌ها همراه با نام والد برای نمایش در لیست
        // ترتیب: گروه > والد > ترتیب نمایش
        return response()->json(
            MenuItem::with('parent')
                ->orderBy('menu_group')
                ->orderBy('parent_id')
                ->orderBy('order')
                ->get()
        );
    }

    public function show($id)
    {
        return response()->json(MenuItem::findOrFail($id));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'link_url' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:menu_items,id',
            'menu_group' => 'required|string|max:100',
            'order' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->except('image');

        // آپلود تصویر
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('menu-items', 'public');
        }

        $menuItem = MenuItem::create($data);

        return response()->json(['message' => 'Menu Item created', 'menu_item' => $menuItem], 201);
    }

    public function update(Request $request, $id)
    {
        $menuItem = MenuItem::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'link_url' => 'required|string|max:255',
            'parent_id' => [
                'nullable',
                'exists:menu_items,id',
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

        // آپلود تصویر جدید و حذف قبلی
        if ($request->hasFile('image')) {
            if ($menuItem->image_path) {
                \Storage::disk('public')->delete($menuItem->image_path);
            }
            $data['image_path'] = $request->file('image')->store('menu-items', 'public');
        }

        $menuItem->update($data);

        return response()->json(['message' => 'Menu Item updated']);
    }

    public function destroy($id)
    {
        $menuItem = MenuItem::findOrFail($id);
        $menuItem->delete(); 
        if ($menuItem->image_path) {
            \Storage::disk('public')->delete($menuItem->image_path);
        }
        return response()->json(['message' => 'Menu Item deleted']);
    }
}