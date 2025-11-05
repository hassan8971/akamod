@extends('layouts.app')

@section('title', 'تایید کد')

@section('content')
<div class="container mx-auto max-w-md p-6" dir="rtl">
    <div class="bg-white shadow-md rounded-lg p-8">
        <h1 class="text-3xl font-bold text-center text-gray-900 mb-6">
            کد تایید را وارد کنید
        </h1>

        @if(session('error'))
            <div class="bg-red-100 border-r-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                {{ session('error') }}
            </div>
        @endif
        
        @if(session('success'))
            <div class="bg-green-100 border-r-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                {{ session('success') }}
            </div>
        @endif
        
        <p class="text-center text-gray-600 mb-6">
            کد تایید ۴ رقمی ارسال شده به شماره
            <span class="font-medium" dir="ltr">{{ $mobile ?? session('mobile') }}</span>
            را وارد کنید.
            <br>
            (کد تست: 1234)
        </p>

        <form method="POST" action="{{ route('otp.verify') }}" class="space-y-6">
            @csrf

            <div>
                <label for="code" class="block text-sm font-medium text-gray-700 text-right">کد تایید ۴ رقمی</label>
                <input id="code" type="tel" name="code" 
                       required autofocus
                       class="w-full px-3 py-2 mt-1 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-center tracking-[1em]"
                       maxlength="4" dir="ltr">
                @error('code')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end mt-4">
                <button type="submit"
                        class="w-full px-4 py-2 text-base font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    ورود و تایید
                </button>
            </div>

            <div class="text-center">
                <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:text-blue-500">
                    اصلاح شماره موبایل
                </a>
            </div>
        </form>
    </div>
</div>
@endsection