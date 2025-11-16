@extends('admin.layouts.app')
@section('title', 'دسته‌بندی‌های وبلاگ')

@section('content')
<div dir="rtl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">دسته‌بندی‌های وبلاگ</h1>
        <a href="{{ route('admin.blog-categories.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            + افزودن دسته‌بندی
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-r-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border-r-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-5 py-3 border-b-2 ... uppercase">تصویر</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 text-right text-xs font-semibold uppercase">نام</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 text-right text-xs font-semibold uppercase">اسلاگ (Slug)</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $category)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-5 border-b ...">
                        @if($category->image_path)
                            <img src="{{ Storage::url($category->image_path) }}" alt="{{ $category->name }}" class="w-16 h-10 object-cover rounded-md">
                        @else
                            <span class="w-16 h-10 bg-gray-200 rounded-md flex items-center justify-center text-xs text-gray-500">بدون تصویر</span>
                        @endif
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ $category->name }}</td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm" dir="ltr">{{ $category->slug }}</td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-left">
                        <a href="{{ route('admin.blog-categories.edit', $category) }}" class="text-blue-600 hover:text-blue-900 ml-4">ویرایش</a>
                        <form action="{{ route('admin.blog-categories.destroy', $category) }}" method="POST" class="inline-block" onsubmit="return confirm('آیا از حذف این دسته‌بندی مطمئن هستید؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">حذف</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="py-10 text-center text-gray-500">
                        هیچ دسته‌بندی برای وبلاگ تعریف نشده است.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection