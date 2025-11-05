@extends('admin.layouts.app')
@section('title', 'افزودن گزینه بسته‌بندی')

@section('content')
<div dir="rtl">
    <h1 class="text-3xl font-bold mb-6">افزودن گزینه بسته‌بندی جدید</h1>

    <form action="{{ route('admin.packaging-options.store') }}" method="POST">
        @csrf
        <div class="bg-white shadow-md rounded-lg p-6 space-y-6">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">نام</label>
                <input type="text" name="name" id="name" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="مثال: بسته‌بندی هدیه" required>
            </div>
            <div>
                <label for="price" class="block text-sm font-medium text-gray-700">هزینه (به تومان)</label>
                <input type="number" name="price" id="price" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md" value="0" required>
            </div>
            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" class="h-4 w-4 text-blue-600 border-gray-300 rounded ml-2" checked>
                <label for="is_active" class="text-sm font-medium text-gray-700">این گزینه فعال باشد</Vlabel>
            </div>
            <div class="flex justify-end">
                <a href="{{ route('admin.packaging-options.index') }}" class="px-4 py-2 text-gray-600 rounded-lg hover:bg-gray-100">انصراف</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 mr-2">ذخیره</button>
            </div>
        </div>
    </form>
</div>
@endsection