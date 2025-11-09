@extends('admin.layouts.app')
@section('title', 'ویرایش رنگ')

@section('content')
<div dir="rtl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">ویرایش رنگ: {{ $color->name }}</h1>
        <a href="{{ route('admin.colors.index') }}" class="px-4 py-2 text-gray-600 rounded-lg hover:bg-gray-100">
            &larr; بازگشت
        </a>
    </div>

    <form action="{{ route('admin.colors.update', $color) }}" method="POST">
        @csrf
        @method('PUT')
        @include('admin.colors._form')
    </form>
</div>
@endsection