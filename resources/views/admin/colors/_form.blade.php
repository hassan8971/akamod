<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div class="bg-white shadow-md rounded-lg p-6 space-y-6" x-data="{ hex: '{{ old('hex_code', $color->hex_code) }}' }">
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">نام رنگ</label>
        <input type="text" name="name" id="name" value="{{ old('name', $color->name) }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="مثال: قرمز تیره" required>
        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div>
        <label for="persian_name" class="block text-sm font-medium text-gray-700">نام رنگ (فارسی)</label>
        <input type="text" name="persian_name" id="persian_name" value="{{ old('persian_name', $color->persian_name) }}" 
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md" 
                placeholder="مثال: قرمز تیره">
        @error('persian_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>
    
    <div>
        <label for="hex_code" class="block text-sm font-medium text-gray-700">کد رنگ (Hex)</label>
        <div class="mt-1 flex items-center space-x-3 space-x-reverse">
            <input type="color" x-model="hex" class="w-10 h-10 rounded-md border-gray-300 p-0 m-0">
            <input type="text" name="hex_code" id="hex_code" x-model="hex" class="block w-full max-w-xs px-3 py-2 border border-gray-300 rounded-md" placeholder="#FF0000" required dir="ltr">
        </div>
        @error('hex_code') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div class="flex justify-end">
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">ذخیره</button>
    </div>
</div>