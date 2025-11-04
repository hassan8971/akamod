@extends('layouts.app')

@section('title', 'تایید سفارش')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="bg-white shadow-lg rounded-lg max-w-2xl mx-auto p-8 text-center">
        <svg class="w-20 h-20 text-green-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <h1 class="text-3xl font-bold text-gray-800 mb-2">از شما سپاسگزاریم!</h1>
        <p class="text-xl text-gray-600">سفارش شما با موفقیت ثبت شد.</p>
        <p class="text-gray-500 mt-4">شناسه سفارش شما: <span class="font-medium text-gray-700">#{{ $order->order_code }}</span></p>
        <p class="text-gray-500">یک ایمیل تاییدیه برای شما ارسال شد (در صورت ارائه ایمیل).</p>
        
        <div class="mt-8">
            <a href="{{ route('shop.index') }}" class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700">
                ادامه خرید
            </a>
            <!-- Later, add a link to their order history -->
        </div>
    </div>
</div>
@endsection