@extends('admin.layouts.app')
@section('title', 'افزودن آیتم منو')

@section('content')
<div dir="rtl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">افزودن آیتم منوی جدید</h1>
        <a href="{{ route('admin.menu-items.index') }}" class="px-4 py-2 text-gray-600 rounded-lg hover:bg-gray-100">
            &larr; بازگشت
        </a>
    </div>

    <form action="{{ route('admin.menu-items.store') }}" method="POST">
        @csrf
        @include('admin.menu-items._form')
    </form>
</div>
@endsection