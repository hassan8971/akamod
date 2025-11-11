<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BuySource;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BuySourceController extends Controller
{
    public function index()
    {
        $sources = BuySource::latest()->get();
        return view('admin.buy_sources.index', compact('sources'));
    }

    public function create()
    {
        $source = new BuySource();
        return view('admin.buy_sources.create', compact('source'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:buy_sources,name',
            'phone' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        BuySource::create($validated);
        return redirect()->route('admin.buy-sources.index')->with('success', 'منبع خرید جدید با موفقیت ایجاد شد.');
    }

    public function edit(BuySource $buySource)
    {
        return view('admin.buy_sources.edit', ['source' => $buySource]);
    }

    public function update(Request $request, BuySource $buySource)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('buy_sources')->ignore($buySource->id),
            ],
            'phone' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $buySource->update($validated);
        return redirect()->route('admin.buy-sources.index')->with('success', 'منبع خرید با موفقیت به‌روزرسانی شد.');
    }

    public function destroy(BuySource $buySource)
    {
        // TODO: Check if source is used in product_variants
        try {
            $buySource->delete();
            return redirect()->route('admin.buy-sources.index')->with('success', 'منبع خرید با موفقیت حذف شد.');
        } catch (\Exception $e) {
            return redirect()->route('admin.buy-sources.index')->with('error', 'امکان حذف این منبع وجود ندارد (ممکن است در حال استفاده باشد).');
        }
    }
}