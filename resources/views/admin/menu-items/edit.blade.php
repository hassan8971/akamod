@extends('admin.layouts.app')
@section('title', 'ویرایش آیتم منو')

@section('content')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<div dir="rtl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">ویرایش: {{ $menuItem->name }}</h1>
        <a href="{{ route('admin.menu-items.index') }}" class="px-4 py-2 text-gray-600 rounded-lg hover:bg-gray-100">
            &larr; بازگشت
        </a>
    </div>

    <form action="{{ route('admin.menu-items.update', $menuItem) }}" method="POST">
        @csrf
        @method('PUT')
        @include('admin.menu-items._form')
    </form>
</div>
@endsection