@extends('admin.layouts.app')
@section('title', 'ویرایش گزینه بسته‌بندی')

@section('content')
<div dir="rtl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">ویرایش: {{ $option->name }}</h1>
        <a href="{{ route('admin.packaging-options.index') }}" class="px-4 py-2 text-gray-600 rounded-lg hover:bg-gray-100">
            &larr; بازگشت
        </a>
    </div>

    <form action="{{ route('admin.packaging-options.update', $option) }}" method="POST">
        @csrf
        @method('PUT') <div class="bg-white shadow-md rounded-lg p-6 space-y-6">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">نام</label>
                <input type="text" name="name" id="name" value="{{ old('name', $option->name) }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md" required>
                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="price" class="block text-sm font-medium text-gray-700">هزینه (به تومان)</label>
                <input type="number" name="price" id="price" value="{{ old('price', $option->price) }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md" required>
                @error('price') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" class="h-4 w-4 text-blue-600 border-gray-300 rounded ml-2" @checked(old('is_active', $option->is_active))>
                <label for="is_active" class="text-sm font-medium text-gray-700">این گزینه فعال باشد</label>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">ذخیره تغییرات</button>
            </div>
        </div>
    </form>
</div>
@endsection