<div class="bg-white shadow-md rounded-lg p-6 space-y-6">
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">نام منبع (اجباری)</label>
            <input type="text" name="name" id="name" value="{{ old('name', $source->name) }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="مثال: تأمین‌کننده X" required>
            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="phone" class="block text-sm font-medium text-gray-700">شماره تلفن (اختیاری)</label>
            <input type="text" name="phone" id="phone" value="{{ old('phone', $source->phone) }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
            @error('phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="city" class="block text-sm font-medium text-gray-700">شهر (اختیاری)</label>
            <input type="text" name="city" id="city" value="{{ old('city', $source->city) }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
            @error('city') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
    </div>

    <div>
        <label for="address" class="block text-sm font-medium text-gray-700">آدرس (اختیاری)</label>
        <textarea name="address" id="address" rows="3" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">{{ old('address', $source->address) }}</textarea>
        @error('address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div>
        <label for="description" class="block text-sm font-medium text-gray-700">توضیحات (اختیاری)</label>
        <textarea name="description" id="description" rows="3" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">{{ old('description', $source->description) }}</textarea>
        @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div class="flex justify-end">
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">ذخیره</button>
    </div>
</div>