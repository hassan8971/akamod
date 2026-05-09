<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">عنوان روش ارسال <span class="text-red-500">*</span></label>
        <input type="text" name="title" id="title" value="{{ old('title', $shippingMethod->title) }}" required placeholder="مثال: پست پیشتاز" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
        @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="method_key" class="block text-sm font-medium text-gray-700 mb-1">کلید انگلیسی (Key) <span class="text-red-500">*</span></label>
        <input type="text" name="method_key" id="method_key" value="{{ old('method_key', $shippingMethod->method_key) }}" dir="ltr" required placeholder="مثال: pishtaz" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
        <p class="text-gray-400 text-xs mt-1">باید به صورت انگلیسی و یکتا باشد. بدون فاصله.</p>
        @error('method_key') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="cost" class="block text-sm font-medium text-gray-700 mb-1">هزینه ارسال (تومان) <span class="text-red-500">*</span></label>
        <input type="number" min="0" name="cost" id="cost" value="{{ old('cost', $shippingMethod->cost ?? 0) }}" dir="ltr" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
        <p class="text-gray-400 text-xs mt-1">عدد 0 به معنی ارسال رایگان (یا پس کرایه) است.</p>
        @error('cost') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="flex items-center pt-8">
        <label class="inline-flex items-center cursor-pointer">
            <input type="checkbox" name="is_active" class="form-checkbox h-5 w-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500" value="1" {{ old('is_active', $shippingMethod->is_active ?? true) ? 'checked' : '' }}>
            <span class="mr-2 text-sm text-gray-700 font-medium">این روش ارسال در سایت فعال باشد</span>
        </label>
    </div>

    <div class="md:col-span-2">
        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">توضیحات (اختیاری)</label>
        <textarea name="description" id="description" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" placeholder="توضیحات کوتاهی که به کاربر در صفحه پرداخت نمایش داده می‌شود...">{{ old('description', $shippingMethod->description) }}</textarea>
        @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
</div>