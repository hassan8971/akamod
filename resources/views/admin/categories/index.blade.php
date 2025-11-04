@extends('admin.layouts.app')

@section('title', 'مدیریت دسته‌بندی‌ها')

@section('content')
    <div class="flex justify-between items-center mb-6" dir="rtl">
        <h1 class="text-2xl font-semibold">مدیریت دسته‌بندی‌ها</h1>
        <a href="{{ route('admin.categories.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            + افزودن دسته جدید
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert" dir="rtl">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden" dir="rtl">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr>
                        <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold uppercase">نام</th>
                        <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold uppercase">اسلاگ</th>
                        <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold uppercase">قابل مشاهده</th>
                        <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-100"></th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @forelse ($categories as $category)
                        <!-- Top-level Category -->
                        <tr class="hover:bg-gray-50 border-b border-gray-200 bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    @if($category->image_path)
                                        <img src="{{ Storage::url($category->image_path) }}" alt="{{ $category->name }}" class="w-10 h-10 object-cover rounded-md ml-4">
                                    @else
                                        <span class="w-10 h-10 rounded-md bg-gray-200 flex items-center justify-center text-gray-400 ml-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l-1-1m6-3l-2 2" /></svg>
                                        </span>
                                    @endif
                                    <span class="font-semibold">{{ $category->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4" dir="ltr">{{ $category->slug }}</td>
                            <td class="px-6 py-4">
                                @if ($category->is_visible)
                                    <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">بله</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">خیر</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-left">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="text-blue-600 hover:text-blue-900">ویرایش</a>
                            </td>
                        </tr>
                        
                        <!-- Child Categories -->
                        @if ($category->children->isNotEmpty())
                            @foreach ($category->children as $child)
                                <tr class="hover:bg-gray-50 border-b border-gray-200">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center mr-8"> <!-- Indent child -->
                                            <span class="text-gray-400 mr-2">&Larr;</span>
                                            @if($child->image_path)
                                                <img src="{{ Storage::url($child->image_path) }}" alt="{{ $child->name }}" class="w-8 h-8 object-cover rounded-md ml-3">
                                            @else
                                                <span class="w-8 h-8 rounded-md bg-gray-200 ml-3"></span>
                                            @endif
                                            <span>{{ $child->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4" dir="ltr">{{ $child->slug }}</td>
                                    <td class="px-6 py-4">
                                        @if ($child->is_visible)
                                            <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">بله</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">خیر</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-left">
                                        <a href="{{ route('admin.categories.edit', $child) }}" class="text-blue-600 hover:text-blue-900">ویرایش</a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                هیچ دسته‌بندی یافت نشد.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- This is where the links() call was. I have removed it. -->
    </div>
@endsection

