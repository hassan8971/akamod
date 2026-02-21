<?php

namespace App\Http\Controllers\Api\Bridge;

use App\Http\Controllers\Controller;
use App\Models\BuySource;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BridgeBuySourceController extends Controller
{
    public function index()
    {
        return response()->json(BuySource::latest()->get());
    }

    public function show($id)
    {
        return response()->json(BuySource::findOrFail($id));
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

        $source = BuySource::create($validated);

        return response()->json(['message' => 'Buy Source created', 'source' => $source], 201);
    }

    public function update(Request $request, $id)
    {
        $buySource = BuySource::findOrFail($id);

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

        return response()->json(['message' => 'Buy Source updated']);
    }

    public function destroy($id)
    {
        $buySource = BuySource::findOrFail($id);
        
        try {
            $buySource->delete();
            return response()->json(['message' => 'Buy Source deleted']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Cannot delete source. It might be in use.'], 409);
        }
    }
}