<?php

namespace App\Http\Controllers\Api\Bridge;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class BridgeVideoController extends Controller
{
    public function index()
    {
        return response()->json(Video::latest()->get());
    }

    public function show($id)
    {
        return response()->json(Video::findOrFail($id));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:upload,embed',
            'video_file' => 'required_if:type,upload|nullable|file|mimetypes:video/mp4,mov,ogg,qt,webm|max:51200', // 50MB
            'embed_code' => 'required_if:type,embed|nullable|string',
        ]);

        $data = [
            'name' => $validated['name'],
            'type' => $validated['type'],
        ];

        if ($validated['type'] === 'upload' && $request->hasFile('video_file')) {
            // ذخیره در استوریج عمومی آکامد
            $path = $request->file('video_file')->store('videos', 'public');
            $data['path'] = $path;
            $data['embed_code'] = null;
        } elseif ($validated['type'] === 'embed') {
            $data['embed_code'] = $request->embed_code;
            $data['path'] = null;
        }

        $video = Video::create($data);

        return response()->json(['message' => 'Video created successfully', 'video' => $video], 201);
    }

    public function update(Request $request, $id)
    {
        $video = Video::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:upload,embed',
            'video_file' => 'nullable|file|mimetypes:video/mp4,mov,ogg,qt,webm|max:51200',
            'embed_code' => 'nullable|string',
        ]);

        $data = [
            'name' => $validated['name'],
            'type' => $validated['type'],
        ];

        if ($validated['type'] === 'upload') {
            $data['embed_code'] = null;
            
            if ($request->hasFile('video_file')) {
                // حذف فایل قبلی
                if ($video->path) {
                    Storage::disk('public')->delete($video->path);
                }
                $path = $request->file('video_file')->store('videos', 'public');
                $data['path'] = $path;
            }
        } elseif ($validated['type'] === 'embed') {
            if ($video->path) {
                Storage::disk('public')->delete($video->path);
            }
            $data['path'] = null;
            $data['embed_code'] = $request->embed_code;
        }

        $video->update($data);

        return response()->json(['message' => 'Video updated successfully']);
    }

    public function destroy($id)
    {
        $video = Video::findOrFail($id);
        
        if ($video->path) {
            Storage::disk('public')->delete($video->path);
        }
        
        $video->delete();

        return response()->json(['message' => 'Video deleted successfully']);
    }
}