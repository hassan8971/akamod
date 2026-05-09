@extends('admin.layouts.app')

@section('title', 'افزودن روش ارسال')

@section('content')
<div dir="rtl" class="max-w-4xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-800">افزودن روش ارسال جدید</h1>
        <a href="{{ route('admin.shipping-methods.index') }}" class="text-gray-600 hover:text-gray-900 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            بازگشت
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.shipping-methods.store') }}" method="POST">
            @csrf
            @include('admin.shipping-methods._form', ['shippingMethod' => new \App\Models\ShippingMethod()])

            <div class="mt-6 flex items-center justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg">
                    ذخیره روش ارسال
                </button>
            </div>
        </form>
    </div>
</div>
@endsection