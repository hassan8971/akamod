@extends('admin.layouts.app')
@section('title', 'افزودن کد تخفیف جدید')

@section('content')
<script defer src="http://localhost/alpine/dist/cdn.min.js"></script>

<div dir="rtl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">افزودن کد تخفیف جدید</h1>
        <a href="{{ route('admin.discounts.index') }}" class="px-4 py-2 text-gray-600 rounded-lg hover:bg-gray-100">
            &larr; بازگشت
        </a>
    </div>

    <form action="{{ route('admin.discounts.store') }}" method="POST" 
          x-data="{ generation_mode: '{{ old('generation_mode', 'manual') }}' }">
        @csrf

        <div class="bg-white shadow-md rounded-lg p-6 space-y-6">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">حالت ایجاد کد</label>
                <div class="flex items-center space-x-4 space-x-reverse">
                    <label class="flex items-center">
                        <input type="radio" name="generation_mode" value="manual" x-model="generation_mode" class="ml-2">
                        <span>ایجاد کد تکی (دستی)</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="generation_mode" value="batch" x-model="generation_mode" class="ml-2">
                        <span>ایجاد کدهای گروهی (رندوم)</span>
                    </label>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">نام تخفیف (برای نمایش در پنل)</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $discount->name) }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="مثال: تخفیf بهاره ۱۴۰۴" required>
                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                
                <div x-show="generation_mode === 'manual'" x-transition>
                    <label for="code" class="block text-sm font-medium text-gray-700">کد تخفیف</label>
                    <input type="text" name="code" id="code" value="{{ old('code', $discount->code) }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="BAHAR20" dir="ltr">
                    @error('code') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div x-show="generation_mode === 'batch'" x-transition style="display: none;">
                    <label for="quantity" class="block text-sm font-medium text-gray-700">تعداد کد جهت ساخت</label>
                    <input type="number" name="quantity" id="quantity" value="{{ old('quantity', 10) }}" min="1" max="100" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="مثلا: 50" dir="ltr">
                    @error('quantity') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700">نوع تخفیف</label>
                    <select name="type" id="type" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="fixed" @selected(old('type', $discount->type) == 'fixed')>مبلغ ثابت (تومان)</option>
                        <option value="percent" @selected(old('type', $discount->type) == 'percent')>درصدی (%)</option>
                    </select>
                    @error('type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="value" class="block text-sm font-medium text-gray-700">مقدار</label>
                    <input type="number" name="value" id="value" value="{{ old('value', $discount->value) }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="مثلا: 50000 یا 20" required dir="ltr">
                    @error('value') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <hr>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="starts_at" class="block text-sm font-medium text-gray-700">تاریخ شروع (اختیاری)</label>
                    <input type="datetime-local" name="starts_at" id="starts_at" value="{{ old('starts_at', $discount->starts_at ? $discount->starts_at->format('Y-m-d\TH:i') : '') }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md" dir="ltr">
                    @error('starts_at') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="expires_at" class="block text-sm font-medium text-gray-700">تاریخ انقضا (اختیاری)</label>
                    <input type="datetime-local" name="expires_at" id="expires_at" value="{{ old('expires_at', $discount->expires_at ? $discount->expires_at->format('Y-m-d\TH:i') : '') }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md" dir="ltr">
                    @error('expires_at') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="usage_limit" class="block text-sm font-medium text-gray-700">سقف استفاده (اختیاری)</label>
                    <input type="number" name="usage_limit" id="usage_limit" value="{{ old('usage_limit', $discount->usage_limit) }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="مثلا: 100 (0 یا خالی = نامحدود)" dir="ltr">
                    @error('usage_limit') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="min_purchase" class="block text-sm font-medium text-gray-700">حداقل خرید (اختیاری - تومان)</label>
                    <input type="number" name="min_purchase" id="min_purchase" value="{{ old('min_purchase', $discount->min_purchase) }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="مثلا: 200000 (0 = بدون محدودیت)" dir="ltr">
                    @error('min_purchase') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <hr>

            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" class="h-4 w-4 text-blue-600 border-gray-300 rounded ml-2" @checked(old('is_active', $discount->is_active))>
                <label for="is_active" class="text-sm font-medium text-gray-700">این کد فعال باشد</label>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">ذخیره</button>
            </div>
        </div>
    </form>
</div>
@endsection