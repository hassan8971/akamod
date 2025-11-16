@csrf
<div class="bg-white shadow-md rounded-lg p-6 space-y-6">
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">نام دسته‌بندی</label>
        <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="مثال: اخبار" required>
        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="slug" class="block text-sm font-medium text-gray-700">اسلاگ (اختیاری)</label>
        <input type="text" name="slug" id="slug" value="{{ old('slug', $category->slug) }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="در صورت خالی بودن، خودکار ساخته می‌شود" dir="ltr">
        @error('slug') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="image" class="block text-sm font-medium text-gray-700">تصویر (اختیاری)</label>
        <input type="file" name="image" id="image" class="mt-1 block w-full text-sm text-gray-500
            file:mr-4 file:py-2 file:px-4 file:ml-4
            file:rounded-full file:border-0
            file:text-sm file:font-semibold
            file:bg-blue-50 file:text-blue-700
            hover:file:bg-blue-100">
        @error('image') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        
        @if($category->image_path)
        <div class="mt-4">
            <p class="text-xs text-gray-500 mb-1">تصویر فعلی:</p>
            <img src="{{ Storage::url($category->image_path) }}" alt="{{ $category->name }}" class="w-32 h-32 object-cover rounded-md">
        </div>
        @endif
    </div>
    <div class="flex justify-end">
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">ذخیره</button>
    </div>
</div>