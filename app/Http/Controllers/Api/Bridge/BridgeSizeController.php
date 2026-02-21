<?php

namespace App\Http\Controllers\Api\Bridge;

use App\Http\Controllers\Controller;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BridgeSizeController extends Controller
{
    public function index()
    {
        // مرتب‌سازی بر اساس نام تا لیست مرتب باشد
        return response()->json(Size::orderBy('name')->get());
    }

    public function show($id)
    {
        return response()->json(Size::findOrFail($id));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:sizes,name',
        ]);

        $size = Size::create($validated);

        return response()->json(['message' => 'Size created', 'size' => $size], 201);
    }

    public function update(Request $request, $id)
    {
        $size = Size::findOrFail($id);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('sizes')->ignore($size->id),
            ],
        ]);

        $size->update($validated);

        return response()->json(['message' => 'Size updated']);
    }

    public function destroy($id)
    {
        $size = Size::findOrFail($id);
        
        try {
            $size->delete();
            return response()->json(['message' => 'Size deleted']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Cannot delete size. It might be in use.'], 409);
        }
    }
}