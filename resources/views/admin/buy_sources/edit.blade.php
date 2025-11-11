@extends('admin.layouts.app')
@section('title', 'ویرایش منبع خرید')

@section('content')
<div dir="rtl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">ویرایش: {{ $source->name }}</h1>
        <a href="{{ route('admin.buy-sources.index') }}" class="px-4 py-2 text-gray-600 rounded-lg hover:bg-gray-100">
            &larr; بازگشت
        </a>
    </div>

    <form action="{{ route('admin.buy-sources.update', $source) }}" method="POST">
        @csrf
        @method('PUT')
        @include('admin.buy_sources._form')
    </form>
</div>
@endsection