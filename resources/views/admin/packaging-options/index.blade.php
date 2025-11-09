@extends('admin.layouts.app')
@section('title', 'مدیریت انواع بسته‌بندی')

@section('content')
<div dir="rtl">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">مدیریت انواع بسته‌بندی</h1>
        <a href="{{ route('admin.packaging-options.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            + افزودن گزینه جدید
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 ... uppercase">تصویر</th> <th class="px-5 py-3 border-b-2 ... uppercase">نام</th>
                    <th class="px-5 py-3 border-b-2 ... uppercase">هزینه (تومان)</th>
                    <th class="px-5 py-3 border-b-2 ... uppercase">وضعیت</th>
                    <th class="px-5 py-3 border-b-2 ...">عملیات</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($packagingOptions as $option)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        @if($option->image_path)
                            <img src="{{ Storage::url($option->image_path) }}" alt="{{ $option->name }}" class="w-16 h-16 object-cover rounded-md">
                        @else
                            <span class="w-16 h-16 bg-gray-200 rounded-md flex items-center justify-center text-xs text-gray-500">بدون تصویر</span>
                        @endif
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ $option->name }}</td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ number_format($option->price) }} تومان</td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        @if($option->is_active)
                            <span class="... text-green-700 bg-green-100">فعال</span>
                        @else
                            <span class="... text-gray-700 bg-gray-100">غیرفعال</span>
                        @endif
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-left">
                        <a href="{{ route('admin.packaging-options.edit', $option) }}" class="text-blue-600 hover:text-blue-900">ویرایش</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-10 text-center text-gray-500"> هیچ گزینه‌ی بسته‌بندی تعریف نشده است.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection