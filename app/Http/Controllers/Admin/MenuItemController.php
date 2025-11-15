<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuItemController extends Controller
{
    // Helper to get items for dropdowns
    private function getMenuItemsList()
    {
        return MenuItem::orderBy('name')->get();
    }

    public function index()
    {
        // Get all items, ordered by group, then parent, then order
        $menuItems = MenuItem::with('parent')
                            ->orderBy('menu_group')
                            ->orderBy('parent_id')
                            ->orderBy('order')
                            ->get();
                            
        return view('admin.menu-items.index', compact('menuItems'));
    }

    public function create()
    {
        $menuItem = new MenuItem(['order' => 0]);
        $menuItems = $this->getMenuItemsList(); // For parent dropdown
        return view('admin.menu-items.create', compact('menuItem', 'menuItems'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'link_url' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:menu_items,id',
            'menu_group' => 'required|string|max:100',
            'order' => 'required|integer|min:0',
        ]);

        MenuItem::create($validated);
        return redirect()->route('admin.menu-items.index')->with('success', 'آیتم منو با موفقیت ایجاد شد.');
    }

    public function edit(MenuItem $menuItem)
    {
        // Get all items *except* this one (an item can't be its own parent)
        $menuItems = MenuItem::where('id', '!=', $menuItem->id)->orderBy('name')->get();
        return view('admin.menu-items.edit', compact('menuItem', 'menuItems'));
    }

    public function update(Request $request, MenuItem $menuItem)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'link_url' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:menu_items,id',
            'menu_group' => 'required|string|max:100',
            'order' => 'required|integer|min:0',
        ]);
        
        // Prevent setting itself as parent
        if (isset($validated['parent_id']) && $validated['parent_id'] == $menuItem->id) {
            return redirect()->back()->with('error', 'یک آیتم نمی‌تواند والد خودش باشد.');
        }

        $menuItem->update($validated);
        return redirect()->route('admin.menu-items.index')->with('success', 'آیتم منو با موفقیت به‌روزرسانی شد.');
    }

    public function destroy(MenuItem $menuItem)
    {
        // Deleting the parent will cascade and delete all children
        $menuItem->delete();
        return redirect()->route('admin.menu-items.index')->with('success', 'آیتم منو (و زیرمجموعه‌های آن) با موفقیت حذف شد.');
    }
}