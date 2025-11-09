<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule; // <-- ۱. این را اضافه کنید

class VideoController extends Controller
{
    public function index()
    {
        $videos = Video::latest()->get();
        return view('admin.videos.index', compact('videos'));
    }

    public function create()
    {
        // Pass a new video object to the view to reuse the form
        $video = new Video(['type' => 'upload']);
        return view('admin.videos.create', compact('video'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:upload,embed',
            'video_file' => 'nullable|file|mimetypes:video/mp4,mov,ogg,qt,webm|max:51200|required_if:type,upload',
            'embed_code' => 'nullable|string|regex:/<iframe.*<\/iframe>/i|required_if:type,embed',
        ], [
            'video_file.required_if' => 'در صورت انتخاب نوع "آپلود"، فایل ویدیو الزامی است.',
            'embed_code.required_if' => 'در صورت انتخاب نوع "الصاق"، کد embed الزامی است.',
            'embed_code.regex' => 'کد الصاقی (embed) معتبر نیست. باید شامل تگ <iframe> باشد.',
        ]);

        $data = [
            'name' => $validated['name'],
            'type' => $validated['type'],
        ];

        if ($validated['type'] === 'upload' && $request->hasFile('video_file')) {
            $path = $request->file('video_file')->store('videos', 'public');
            $data['path'] = $path;
            $data['embed_code'] = null; // Ensure embed is null
        } elseif ($validated['type'] === 'embed' && $request->filled('embed_code')) {
            $data['embed_code'] = $request->embed_code;
            $data['path'] = null; // Ensure path is null
        }

        Video::create($data);

        return redirect()->route('admin.videos.index')->with('success', 'ویدیو با موفقیت به کتابخانه اضافه شد.');
    }

    public function edit(Video $video)
    {
        return view('admin.videos.edit', compact('video'));
    }

    /**
     * --- این متد کامل شده است ---
     * Update the specified resource in storage.
     */
    public function update(Request $request, Video $video)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:upload,embed',
            // File is only required if type is 'upload' AND no file already exists
            'video_file' => [
                'nullable',
                'file',
                'mimetypes:video/mp4,mov,ogg,qt,webm',
                'max:51200',
                Rule::requiredIf(fn () => $request->type === 'upload' && !$video->path),
            ],
            // Embed code is only required if type is 'embed'
            'embed_code' => [
                'nullable',
                'string',
                'regex:/<iframe.*<\/iframe>/i',
                Rule::requiredIf(fn () => $request->type === 'embed'),
            ],
        ], [
            'video_file.required_if' => 'در صورت انتخاب نوع "آپلود"، فایل ویدیو الزامی است.',
            'embed_code.required_if' => 'در صورت انتخاب نوع "الصاق"، کد embed الزامی است.',
            'embed_code.regex' => 'کد الصاقی (embed) معتبر نیست. باید شامل تگ <iframe> باشد.',
        ]);

        $data = [
            'name' => $validated['name'],
            'type' => $validated['type'],
        ];

        if ($validated['type'] === 'upload') {
            $data['embed_code'] = null; // Remove embed code
            if ($request->hasFile('video_file')) {
                // Delete old file, if it exists
                if ($video->path) {
                    Storage::disk('public')->delete($video->path);
                }
                // Store new file
                $path = $request->file('video_file')->store('videos', 'public');
                $data['path'] = $path;
            }
            // If no new file is uploaded, we just keep the old $video->path
            
        } elseif ($validated['type'] === 'embed') {
            // Delete old file, if it exists
            if ($video->path) {
                Storage::disk('public')->delete($video->path);
            }
            $data['path'] = null; // Remove path
            $data['embed_code'] = $request->embed_code;
        }

        $video->update($data);

        return redirect()->route('admin.videos.index')->with('success', 'ویدیو با موفقیت به‌روزرسانی شد.');
    }
    // --- پایان متد update ---

    public function destroy(Video $video)
    {
        // Delete file only if it's an 'upload' type
        if ($video->type === 'upload' && $video->path) {
            Storage::disk('public')->delete($video->path);
        }
        $video->delete();
        
        return redirect()->route('admin.videos.index')->with('success', 'ویدیو با موفقیت حذف شد.');
    }
}