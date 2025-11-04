@extends('layouts.app')

@section('title', 'تسویه حساب')

@section('content')
<div class="container mx-auto px-4 py-12">
    <h1 class="text-3xl font-bold text-center mb-8">تسویه حساب</h1>

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6 text-right" role="alert">
            {{ session('error') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6 text-right" role="alert">
            <strong class="font-bold">خطا!</strong>
            <span class="block sm:inline">ورودی شما دارای چند مشکل است.</span>
            <ul class="mt-3 list-disc list-inside text-sm text-right">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="flex flex-col lg:flex-row-reverse gap-12">

        <div class="w-full lg:w-2/3">
            <form id="checkout-form" action="{{ route('checkout.store') }}" method="POST">
                @csrf
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
                            <input type="text" id="phone" name="phone" value="{{ old('phone', Auth::user()->mobile ?? '') }}"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-right" placeholder="اختیاری">
                        </div>
                    </div>

                    <div class="mt-6">
                        <label for="address_line_1" class="block text-sm font-medium text-gray-700 text-right">آدرس (خط ۱)</label>
                        <input type="text" id="address_line_1" name="address_line_1" value="{{ old('address_line_1') }}"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-right" required>
                    </div>

                    <div class="mt-6">
                        <label for="address_line_2" class="block text-sm font-medium text-gray-700 text-right">آدرس (خط ۲ - اختیاری)</label>
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

                    <div class="mt-6">
                        <label for="country" class="block text-sm font-medium text-gray-700 text-right">کشور</label>
                        <input type="text" id="country" name="country" value="{{ old('country') }}"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-right" required>
                    </div>
                    
                    @auth
                    <div class="mt-6 text-right">
                        <label class="flex items-center justify-end">
                            <input type="checkbox" name="save_address" value="1" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="mr-2 text-sm text-gray-600">ذخیره این آدرس در حساب کاربری من</span>
                        </label>
                    </div>
                    @endauth

                    <h2 class="text-2xl font-semibold mt-8 mb-6 text-right">روش پرداخت</h2>
                    <div class="space-y-4">
                        <label class="flex items-center p-4 border rounded-lg cursor-pointer justify-end">
                            <span class="mr-3 text-sm font-medium text-gray-700">پرداخت در محل (COD)</span>
                            <input type="radio" name="payment_method" value="cod" class="text-blue-600" checked>
                        </label>
                        <label class="flex items-center p-4 border rounded-lg cursor-pointer bg-gray-50 opacity-50 justify-end">
                            <span class="mr-3 text-sm font-medium text-gray-400">کارت اعتباری (Stripe - به زودی)</span>
                            <input type="radio" name="payment_method" value="stripe" class="text-blue-600" disabled>
                        </label>
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
                            <p class="text-gray-700 text-left">{{ number_format($item->getPriceSum()) }} تومان</p>
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
                        <p class="font-medium text-left">{{ number_format(Cart::getSubTotal()) }} تومان</p>
                        <p class="text-gray-600 text-right">جمع کل</p>
                    </div>
                    <div class="flex justify-between">
                        <p class="font-medium text-left">رایگان</p>
                        <p class="text-gray-600 text-right">هزینه ارسال</p>
                    </div>
                    <div class="flex justify-between text-lg font-bold">
                        <p class="text-left">{{ number_format($total) }} تومان</p>
                        <p class="text-right">مجموع نهایی</p>
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