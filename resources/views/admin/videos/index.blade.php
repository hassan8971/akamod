@extends('admin.layouts.app')
@section('title', 'کتابخانه ویدیوها')

@section('content')
<div dir="rtl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">کتابخانه ویدیوها</h1>
        <a href="{{ route('admin.videos.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            + افزودن ویدیوی جدید
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-r-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold uppercase">ویدیو</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold uppercase">نام (برای ادمین)</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold uppercase">نوع</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($videos as $video)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        @if($video->type == 'embed')
                            <div class="w-32 h-20 bg-gray-800 text-white flex items-center justify-center rounded">Embed</div>
                        @else
                            <video class="w-32 h-20 rounded bg-gray-800" controls>
                                <source src="{{ Storage::url($video->path) }}" type="video/mp4">
                            </video>
                        @endif
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ $video->name }}</td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        {{ $video->type == 'upload' ? 'آپلودی' : 'الصاقی' }}
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-left">
                        <a href="{{ route('admin.videos.edit', $video) }}" class="text-blue-600 hover:text-blue-900 ml-4">ویرایش</a>
                        <form action="{{ route('admin.videos.destroy', $video) }}" method="POST" class="inline-block" onsubmit="return confirm('آیا از حذف این ویدیو مطمئن هستید؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">حذف</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-10 text-center text-gray-500">
                        هیچ ویدیویی در کتابخانه یافت نشد.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection