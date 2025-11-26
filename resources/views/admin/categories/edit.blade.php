@extends('admin.layouts.app')

@section('title', 'ویرایش دسته: ' . $category->name)

@section('content')
    <div class="flex justify-between items-center mb-4" dir="rtl">
        <h1 class="text-2xl font-semibold">ویرایش دسته: {{ $category->name }}</h1>
        <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 text-gray-600 rounded-lg hover:bg-gray-100">
            &rarr; بازگشت به لیست
        </a>
    </div>

    <form id="update-form" action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6" dir="rtl">
            
            <div class="md:col-span-2 bg-white shadow-md rounded-lg p-6 space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">نام دسته‌بندی</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label for="slug" class="block text-sm font-medium text-gray-700">اسلاگ (Slug)</label>
                    <input type="text" name="slug" id="slug" value="{{ old('slug', $category->slug) }}" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" dir="ltr">
                    @error('slug') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">توضیحات</label>
                    <textarea name="description" id="description" rows="5" 
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old('description', $category->description) }}</textarea>
                    @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="md:col-span-1 space-y-6">
                <div class="bg-white shadow-md rounded-lg p-6">
                    <label for="parent_id" class="block text-sm font-medium text-gray-700">دسته‌بندی والد</label>
                    <select name="parent_id" id="parent_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- بدون والد (سطح بالا) --</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" 
                                {{ old('parent_id', $category->parent_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('parent_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="bg-white shadow-md rounded-lg p-6">
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-2">تصویر دسته‌بندی (جدید)</label>
                    <input type="file" name="image" id="image" class="mt-1 block w-full text-sm text-gray-500
                        file:mr-4 file:py-2 file:px-4 file:ml-4
                        file:rounded-full file:border-0
                        file:text-sm file:font-semibold
                        file:bg-blue-50 file:text-blue-700
                        hover:file:bg-blue-100">
                    @error('image') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                    @if($category->image_path)
                        <div class="mt-4">
                            <p class="text-sm text-gray-500 mb-2">تصویر فعلی:</p>
                            <img src="{{ Storage::url($category->image_path) }}" alt="{{ $category->name }}" class="w-32 h-32 object-cover rounded-md">
                        </div>
                    @endif
                </div>
                
                <div class="bg-white shadow-md rounded-lg p-6 flex items-center">
                    <input type="checkbox" name="is_visible" id="is_visible" value="1" 
                           {{ old('is_visible', $category->is_visible) ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 border-gray-300 rounded ml-2">
                    <label for="is_visible" class="text-sm font-medium text-gray-700">قابل مشاهده برای عموم</label>
                </div>
            </div>
        </div>

    </form>

    <div class="mt-6 flex justify-between" dir="rtl">
        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('آیا از حذف این دسته مطمئن هستید؟ فرزندان این دسته نیز حذف خواهند شد.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                حذف دسته
            </button>
        </form>
        
        <button type="submit" form="update-form" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            ذخیره تغییرات
        </button>
    </div>

@endsection