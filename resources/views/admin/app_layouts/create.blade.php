@extends('admin.layouts.app')

@section('title', 'ساخت صفحه جدید')

@section('content')
<div dir="rtl" class="max-w-2xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold dark:text-white">ساخت صفحه جدید</h1>
        <a href="{{ route('admin.layouts.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 text-sm">بازگشت</a>
    </div>

    <div class="bg-white dark:bg-dark-paper shadow-lg rounded-xl p-6 border border-gray-100 dark:border-gray-700">
        <form action="{{ route('admin.layouts.store') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">عنوان صفحه</label>
                <input type="text" name="title" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-dark-bg text-gray-800 dark:text-gray-200 p-2.5" placeholder="مثلا: جشنواره تابستانی" required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">اسلاگ (شناسه یکتا در URL)</label>
                <input type="text" name="slug" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-dark-bg text-gray-800 dark:text-gray-200 p-2.5 dir-ltr text-left" placeholder="summer-festival" required>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">پلتفرم</label>
                <select name="platform" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-dark-bg text-gray-800 dark:text-gray-200 p-2.5">
                    <option value="web">وب‌سایت</option>
                    <option value="android">اپلیکیشن اندروید</option>
                    <option value="ios">اپلیکیشن iOS</option>
                </select>
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-lg transition-colors">
                ساخت صفحه و شروع طراحی
            </button>
        </form>
    </div>
</div>
@endsection