@csrf
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="full_name" class="block text-sm font-medium text-gray-700 text-right">نام کامل</label>
            <input type="text" name="full_name" id="full_name" value="{{ old('full_name', $address->full_name) }}"
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required>
            @error('full_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="phone" class="block text-sm font-medium text-gray-700 text-right">شماره تلفن</label>
            <input type="text" name="phone" id="phone" value="{{ old('phone', $address->phone) }}"
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required>
            @error('phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
    </div>
    
    <div>
        <label for="address_line_1" class="block text-sm font-medium text-gray-700 text-right">آدرس (خط ۱)</label>
        <input type="text" name="address_line_1" id="address_line_1" value="{{ old('address_line_1', $address->address_line_1) }}"
               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required>
        @error('address_line_1') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>
    
    <div>
        <label for="address_line_2" class="block text-sm font-medium text-gray-700 text-right">پلاک / واحد (اختیاری)</label>
        <input type="text" name="address_line_2" id="address_line_2" value="{{ old('address_line_2', $address->address_line_2) }}"
               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <label for="city" class="block text-sm font-medium text-gray-700 text-right">شهر</label>
            <input type="text" name="city" id="city" value="{{ old('city', $address->city) }}"
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required>
            @error('city') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="state" class="block text-sm font-medium text-gray-700 text-right">استان</label>
            <input type="text" name="state" id="state" value="{{ old('state', $address->state) }}"
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required>
            @error('state') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="zip_code" class="block text-sm font-medium text-gray-700 text-right">کد پستی</label>
            <input type="text" name="zip_code" id="zip_code" value="{{ old('zip_code', $address->zip_code) }}"
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required>
            @error('zip_code') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
    </div>
    
    <div>
        <label for="country" class="block text-sm font-medium text-gray-700 text-right">کشور</label>
        <input type="text" name="country" id="country" value="{{ old('country', $address->country ?? 'ایران') }}"
               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required>
        @error('country') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>
</div>