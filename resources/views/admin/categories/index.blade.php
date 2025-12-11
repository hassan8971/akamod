@extends('admin.layouts.app')
@section('title', 'دسته‌بندی‌های محصولات')

@section('content')
<div dir="rtl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">دسته‌بندی‌های محصولات</h1>
        <a href="{{ route('admin.categories.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            + افزودن دسته‌بندی
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-r-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 text-right text-xs font-semibold text-gray-600 uppercase">تصویر</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 text-right text-xs font-semibold text-gray-600 uppercase">نام دسته‌بندی</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 text-right text-xs font-semibold text-gray-600 uppercase">اسلاگ</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $category)
                    @include('admin.categories.partials.category-row', ['category' => $category, 'level' => 0])
                @empty
                    <tr>
                        <td colspan="4" class="py-10 text-center text-gray-500">
                            هیچ دسته‌بندی یافت نشد.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection