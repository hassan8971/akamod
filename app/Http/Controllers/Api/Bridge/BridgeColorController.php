<?php

namespace App\Http\Controllers\Api\Bridge;

use App\Http\Controllers\Controller;
use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BridgeColorController extends Controller
{
    public function index()
    {
        return response()->json(Color::orderBy('name')->get());
    }

    public function show($id)
    {
        return response()->json(Color::findOrFail($id));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:colors,name',
            'persian_name' => 'nullable|string|max:100',
            'hex_code' => 'required|string|size:7|starts_with:#|unique:colors,hex_code',
        ]);

        $color = Color::create($validated);

        return response()->json(['message' => 'Color created', 'color' => $color], 201);
    }

    public function update(Request $request, $id)
    {
        $color = Color::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('colors')->ignore($color->id)],
            'persian_name' => 'nullable|string|max:100',
            'hex_code' => ['required', 'string', 'size:7', 'starts_with:#', Rule::unique('colors')->ignore($color->id)],
        ]);

        $color->update($validated);

        return response()->json(['message' => 'Color updated']);
    }

    public function destroy($id)
    {
        $color = Color::findOrFail($id);
        
        try {
            $color->delete();
            return response()->json(['message' => 'Color deleted']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Cannot delete color.'], 409);
        }
    }
}