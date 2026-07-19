@extends('layouts.app')

@section('title', 'ورود مدیریت')

@section('content')
<div class="container mx-auto max-w-md p-6" dir="rtl">
    <div class="bg-white shadow-md rounded-lg p-8">
        <h1 class="text-2xl font-bold text-center text-gray-900 mb-6">ورود به پنل مدیریت</h1>

        @if(session('error'))
            <div class="bg-red-100 border-r-4 border-red-500 text-red-700 p-4 mb-4" role="alert">{{ session('error') }}</div>
        @endif
        @if(session('success'))
            <div class="bg-green-100 border-r-4 border-green-500 text-green-700 p-4 mb-4" role="alert">{{ session('success') }}</div>
        @endif

        <!-- فرم ورود با کلمه عبور -->
        <form method="POST" action="{{ route('admin.login.password') }}" class="mb-8 border-b pb-8">
            @csrf
            <h2 class="text-sm font-semibold text-gray-700 mb-4">ورود با کلمه عبور</h2>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 text-right">شماره موبایل</label>
                <input type="tel" name="mobile" required class="w-full px-3 py-2 border rounded-md" dir="ltr">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 text-right">کلمه عبور</label>
                <input type="password" name="password" required class="w-full px-3 py-2 border rounded-md" dir="ltr">
            </div>
            
            <button type="submit" class="w-full px-4 py-2 text-white bg-gray-800 rounded-md">ورود با رمز عبور</button>
        </form>

        <!-- فرم ورود با پیامک -->
        <form method="POST" action="{{ route('admin.otp.send') }}">
            @csrf
            <h2 class="text-sm font-semibold text-gray-700 mb-4">ورود با پیامک (یکبار مصرف)</h2>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 text-right">شماره موبایل</label>
                <input type="tel" name="mobile" required class="w-full px-3 py-2 border rounded-md" dir="ltr">
            </div>
            
            <button type="submit" class="w-full px-4 py-2 text-white bg-blue-600 rounded-md">دریافت کد پیامکی</button>
        </form>
    </div>
</div>
@endsection