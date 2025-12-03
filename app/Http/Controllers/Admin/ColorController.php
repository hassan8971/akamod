<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ColorController extends Controller
{
    public function index()
    {
        $colors = Color::orderBy('name')->get();
        return view('admin.colors.index', compact('colors'));
    }

    public function create()
    {
        $color = new Color(['hex_code' => '#ffffff']); // Set default color
        return view('admin.colors.create', compact('color'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:colors,name',
            'persian_name' => 'nullable|string|max:100',
            'hex_code' => 'required|string|size:7|starts_with:#|unique:colors,hex_code',
        ], [
            'hex_code.size' => 'کد رنگ باید 7 کاراکتر باشد (مثلا #FF0000)',
            'hex_code.starts_with' => 'کد رنگ باید با # شروع شود.',
        ]);

        Color::create($validated);
        return redirect()->route('admin.colors.index')->with('success', 'رنگ جدید با موفقیت ایجاد شد.');
    }

    public function edit(Color $color)
    {
        return view('admin.colors.edit', compact('color'));
    }

    public function update(Request $request, Color $color)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('colors')->ignore($color->id),
            ],
            'persian_name' => 'nullable|string|max:100',
            'hex_code' => [
                'required',
                'string',
                'size:7',
                'starts_with:#',
                Rule::unique('colors')->ignore($color->id),
            ],
        ], [
            'hex_code.size' => 'کد رنگ باید 7 کاراکتر باشد (مثلا #FF0000)',
            'hex_code.starts_with' => 'کد رنگ باید با # شروع شود.',
        ]);

        $color->update($validated);
        return redirect()->route('admin.colors.index')->with('success', 'رنگ با موفقیت به‌روزرسانی شد.');
    }

    public function destroy(Color $color)
    {
        // TODO: Check if color is used in product_variants before deleting
        try {
            $color->delete();
            return redirect()->route('admin.colors.index')->with('success', 'رنگ با موفقیت حذف شد.');
        } catch (\Exception $e) {
            return redirect()->route('admin.colors.index')->with('error', 'امکان حذف این رنگ وجود ندارد (ممکن است در حال استفاده باشد).');
        }
    }
}