@extends('layouts.app')

@section('title', 'پرداخت')

@section('content')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div class="container mx-auto px-4 py-12" dir="rtl">
    <h1 class="text-3xl font-bold text-center mb-8">پرداخت</h1>

    @if(session('error'))
        <div class="bg-red-100 border-r-4 border-red-500 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            {{ session('error') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="bg-red-100 border-r-4 border-red-500 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <strong class="font-bold">خطا!</strong>
            <span class="block sm:inline">مشکلاتی در ورودی شما وجود دارد.</span>
            <ul class="mt-3 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="flex flex-col lg:flex-row-reverse gap-12" x-data="{
            subtotal: {{ $subtotal }},
            selectedShipping: 'pishaz',
            shippingOptions: {
                pishaz: 35000,
                tipax: 60000
            },

            packagingOptions: {{ $packagingOptions->pluck('price', 'id') }},
            selectedPackaging: {{ $packagingOptions->first()?->id ?? 0 }}, // انتخاب اولین گزینه به عنوان پیش‌فرض
            
            get shippingCost() {
                return this.shippingOptions[this.selectedShipping] || 0;
            },
            get packagingCost() {
                return this.packagingOptions[this.selectedPackaging] || 0;
            },
            get total() {
                return this.subtotal + this.shippingCost + this.packagingCost;
            },
            formatToman(amount) {
                return new Intl.NumberFormat('fa-IR').format(amount) + ' تومان';
            }
        }">

        <div class="w-full lg:w-2/3">
            <form id="checkout-form" action="{{ route('checkout.store') }}" method="POST">
                @csrf
                <input type="hidden" name="shipping_method" x-model="selectedShipping">

                <div class="bg-white shadow-md rounded-lg p-6">
                    <h2 class="text-2xl font-semibold mb-6 text-right">اطلاعات ارسال</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="full_name" class="block text-sm font-medium text-gray-700 text-right">نام کامل</label>
                            <input type="text" id="full_name" name="full_name" value="{{ old('full_name', Auth::user()->name ?? '') }}"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-right" required>
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 text-right">شماره تلفن</label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', Auth::user()->mobile_number ?? '') }}"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-right" required>
                        </div>
                    </div>

                    <div class="mt-6">
                        <label for="address" class="block text-sm font-medium text-gray-700 text-right">آدرس</label>
                        <input type="text" id="address" name="address" value="{{ old('address') }}"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-right" required>
                    </div>
                    
                    <div class="mt-6">
                        <label for="address_line_2" class="block text-sm font-medium text-gray-700 text-right">پلاک / واحد (اختیاری)</label>
                        <input type="text" id="address_line_2" name="address_line_2" value="{{ old('address_line_2') }}"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-right">
                    </div>


                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 text-right">شهر</label>
                            <input type="text" id="city" name="city" value="{{ old('city') }}"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-right" required>
                        </div>
                        <div>
                            <label for="state" class="block text-sm font-medium text-gray-700 text-right">استان</label>
                            <input type="text" id="state" name="state" value="{{ old('state') }}"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-right" required>
                        </div>
                        <div>
                            <label for="zip_code" class="block text-sm font-medium text-gray-700 text-right">کد پستی</label>
                            <input type="text" id="zip_code" name="zip_code" value="{{ old('zip_code') }}"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-right" required>
                        </div>
                    </div>
                    
                 
                    
                    @auth
                    <div class="mt-6 text-right">
                        <label class="flex items-center justify-end">
                            <input type="checkbox" name="save_address" value="1" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 ml-2">
                            <span class="text-sm text-gray-600">ذخیره این آدرس در حساب کاربری من</span>
                        </label>
                    </div>
                    @endauth

                    <h2 class="text-2xl font-semibold mt-8 mb-6 text-right">روش ارسال</h2>
                    <div class="space-y-4">
                        <label class="flex items-center p-4 border rounded-lg cursor-pointer" 
                               :class="{ 'bg-blue-50 border-blue-500 ring-2 ring-blue-500': selectedShipping === 'pishaz' }">
                            <input type="radio" name="shipping_method_option" value="pishaz" x-model="selectedShipping" class="text-blue-600 ml-3">
                            <span class="flex-grow flex justify-between items-center text-sm font-medium text-gray-700">
                                <span>پست پیشتاز</span>
                                <span class="font-bold text-gray-900" x-text="formatToman(shippingOptions.pishaz)"></span>
                            </span>
                        </label>
                        
                        <label class="flex items-center p-4 border rounded-lg cursor-pointer"
                               :class="{ 'bg-blue-50 border-blue-500 ring-2 ring-blue-500': selectedShipping === 'tipax' }">
                            <input type="radio" name="shipping_method_option" value="tipax" x-model="selectedShipping" class="text-blue-600 ml-3">
                            <span class="flex-grow flex justify-between items-center text-sm font-medium text-gray-700">
                                <span>تیپاکس</span>
                                <span class="font-bold text-gray-900" x-text="formatToman(shippingOptions.tipax)"></span>
                            </span>
                        </label>
                    </div>

                    <h2 class="text-2xl font-semibold mt-8 mb-6 text-right">نوع بسته‌بندی</h2>
                    <div class="space-y-4">
                        @forelse ($packagingOptions as $option)
                        <label class="flex items-center p-4 border rounded-lg cursor-pointer" 
                               :class="{ 'bg-blue-50 border-blue-500 ring-2 ring-blue-500': selectedPackaging == {{ $option->id }} }">
                            <input type="radio" name="packaging_id" value="{{ $option->id }}" x-model.number="selectedPackaging" class="text-blue-600 ml-3">
                            <span class="flex-grow flex justify-between items-center text-sm font-medium text-gray-700">
                                <span>{{ $option->name }}</span>
                                <span class="font-bold text-gray-900">{{ number_format($option->price) }} تومان</span>
                            </span>
                        </label>
                        @empty
                        <p class="text-gray-500 text-right">گزینه بسته‌بندی فعالی وجود ندارد.</p>
                        @endforelse
                    </div>

                </div>
            </form>
        </div>

        <div class="w-full lg:w-1/3">
            <div class="bg-white shadow-md rounded-lg p-6 sticky top-8">
                <h2 class="text-2xl font-semibold mb-6 text-right">خلاصه سفارش</h2>
                <div class="space-y-4">
                    @foreach ($cartItems->sortBy('name') as $item)
                        <div class="flex justify-between items-center">
                            <p class="text-gray-700">{{ number_format($item->getPriceSum()) }} <span class="text-xs">تومان</span></p>
                            <div class="flex-grow text-right">
                                <p class="font-medium">{{ $item->name }}</p>
                                <p class="text-sm text-gray-500">تعداد: {{ $item->quantity }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="border-t my-6"></div>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <p class="font-medium" x-text="formatToman(subtotal)"></p>
                        <p class="text-gray-600">جمع سبد خرید</p>
                    </div>
                    <div class="flex justify-between">
                        <p class="font-medium" x-text="formatToman(shippingCost)"></p>
                        <p class="text-gray-600">هزینه ارسال</p>
                    </div>
                    <div class="flex justify-between text-lg font-bold">
                        <p x-text="formatToman(total)"></p>
                        <p>مبلغ کل</p>
                    </div>
                </div>
                <button type="submit" form="checkout-form"
                        class="w-full mt-8 bg-blue-600 text-white text-lg font-semibold py-3 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                    ثبت سفارش
                </button>
            </div>
        </div>
    </div>
</div>
@endsection