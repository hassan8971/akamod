<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SizeController extends Controller
{
    public function index()
    {
        $sizes = Size::orderBy('name')->get();
        return view('admin.sizes.index', compact('sizes'));
    }

    public function create()
    {
        $size = new Size();
        return view('admin.sizes.create', compact('size'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:sizes,name',
        ]);

        Size::create($validated);
        return redirect()->route('admin.sizes.index')->with('success', 'سایز جدید با موفقیت ایجاد شد.');
    }

    public function edit(Size $size)
    {
        return view('admin.sizes.edit', compact('size'));
    }

    public function update(Request $request, Size $size)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('sizes')->ignore($size->id),
            ],
        ]);

        $size->update($validated);
        return redirect()->route('admin.sizes.index')->with('success', 'سایز با موفقیت به‌روزرسانی شد.');
    }

    public function destroy(Size $size)
    {
        // TODO: Check if size is used in product_variants before deleting
        try {
            $size->delete();
            return redirect()->route('admin.sizes.index')->with('success', 'سایز با موفقیت حذف شد.');
        } catch (\Exception $e) {
            return redirect()->route('admin.sizes.index')->with('error', 'امکان حذف این سایز وجود ندارد (ممکن است در حال استفاده باشد).');
        }
    }
}