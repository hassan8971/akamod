{{-- This extends the user panel layout --}}
@extends('user.layouts.app')

@section('title', 'مدیریت پروفایل')

@section('panel-content')

    <div class="bg-white shadow-md rounded-lg p-6 mb-8">
        <h1 class="text-2xl font-semibold mb-6">اطلاعات پروفایل</h1>
        
        <form action="{{ route('user.profile.update') }}" method="POST">
            @csrf
            <div class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 text-right">نام و نام خانوادگی</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-right" required>
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 text-right">ایمیل (اختیاری)</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-left" dir="ltr" placeholder="example@mail.com">
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="mobile" class="block text-sm font-medium text-gray-700 text-right">شماره موبایل (غیرقابل تغییر)</label>
                    <input type="tel" name="mobile" id="mobile" value="{{ $user->mobile }}" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-100 text-gray-500 text-left" dir="ltr" disabled>
                    <p class="mt-1 text-xs text-gray-500">برای تغییر شماره موبایل، لطفا با پشتیبانی تماس بگیرید.</p>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        ذخیره تغییرات
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-6">تغییر رمز عبور</h2>

        <form action="{{ route('user.password.update') }}" method="POST">
            @csrf
            <div class="space-y-6">
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 text-right">رمز عبور فعلی</label>
                    <input type="password" name="current_password" id="current_password" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-left" dir="ltr" required>
                    @error('current_password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 text-right">رمز عبور جدید</label>
                    <input type="password" name="password" id="password" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-left" dir="ltr" required>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</sppan>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 text-right">تکرار رمز عبور جدید</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-left" dir="ltr" required>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        تغییر رمز عبور
                    </button>
                </div>
            </div>
        </form>
    </div>

@endsection