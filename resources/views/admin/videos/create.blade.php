@extends('admin.layouts.app')
@section('title', 'افزودن ویدیوی جدید')

@section('content')
<div dir="rtl" x-data="{ type: 'upload' }">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">افزودن ویدیوی جدید به کتابخانه</h1>
        <a href="{{ route('admin.videos.index') }}" class="px-4 py-2 text-gray-600 rounded-lg hover:bg-gray-100">
            &larr; بازگشت
        </a>
    </div>

    @if ($errors->any())
        <div class="bg-red-100 border-r-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
            <strong class="font-bold">خطا!</strong>
            <ul class="mt-3 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.videos.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="bg-white shadow-md rounded-lg p-6 space-y-6">
            
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">نام ویدیو (برای شناسایی در پنل ادمین)</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="مثال: ویدیو معرفی محصول X" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">نوع ویدیو</label>
                <select name="type" x-model="type" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                    <option value="upload">آپلود فایل</option>
                    <option value="embed">کد الصاقی (Embed)</option>
                </select>
            </div>

            <div x-show="type === 'upload'" x-transition>
                <label for="video_file" class="block text-sm font-medium text-gray-700">آپلود فایل (mp4, mov, ...)</label>
                <input type="file" name="video_file" id="video_file" accept="video/*"
                       class="mt-1 block w-full text-sm ... file:bg-blue-50 file:text-blue-700 ...">
            </div>

            <div x-show="type === 'embed'" x-transition>
                <label for="embed_code" class="block text-sm font-medium text-gray-700">کد الصاقی (Embed)</label>
                <textarea name="embed_code" id="embed_code" rows="3" 
                          class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md" 
                          placeholder="<iframe ...></iframe>">{{ old('embed_code') }}</textarea>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">ذخیره در کتابخانه</button>
            </div>
        </div>
    </form>
</div>
@endsection