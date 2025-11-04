{{-- Extends the new admin layout --}}
@extends('admin.layouts.app')

{{-- Sets the title for the page --}}
@section('title', 'داشبورد')

{{-- Main content section --}}
@section('content')
    <div class="bg-white shadow-md rounded-lg p-6 text-right">
        <h1 class="text-3xl font-bold text-gray-800 mb-4">به داشبورد خود خوش آمدید!</h1>
        <p class="text-gray-600">از اینجا می‌توانید محصولات، دسته‌بندی‌ها، سفارشات و موارد دیگر فروشگاه خود را مدیریت کنید.</p>

        <div class="mt-8 border-t pt-6">
            <h3 class="text-xl font-semibold text-gray-900 text-right">مراحل بعدی</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-4">
                
                <a href="{{ route('admin.categories.index') }}" class="block p-6 bg-blue-50 rounded-lg shadow-sm hover:shadow-md transition-shadow text-right">
                    <h4 class="text-lg font-semibold text-blue-800">مدیریت دسته‌بندی‌ها</h4>
                    <p class="text-sm text-blue-600 mt-1">افزودن، ویرایش یا حذف دسته‌بندی‌های محصولات.</p>
                </a>
                
                <a href="{{ route('admin.products.index') }}" class="block p-6 bg-green-50 rounded-lg shadow-sm hover:shadow-md transition-shadow text-right">
                    <h4 class="text-lg font-semibold text-green-800">مدیریت محصولات</h4>
                    <p class="text-sm text-green-600 mt-1">ایجاد محصولات جدید و مدیریت جزئیات آن‌ها.</p>
                </a>
                
                <div class="block p-6 bg-gray-50 rounded-lg shadow-sm text-right">
                    <h4 class="text-lg font-semibold text-gray-800">مشاهده سفارشات</h4>
                    <p class="text-sm text-gray-600 mt-1">(به زودی) مشاهده و تکمیل سفارشات مشتریان.</p>
                </div>

            </div>
        </div>
    </div>
@endsection