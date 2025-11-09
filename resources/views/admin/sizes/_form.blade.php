<div class="bg-white shadow-md rounded-lg p-6 space-y-6">
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">نام سایز</label>
        <input type="text" name="name" id="name" value="{{ old('name', $size->name) }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="مثال: 38.5 یا Medium" required>
        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>
    <div class="flex justify-end">
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">ذخیره</button>
    </div>
</div>