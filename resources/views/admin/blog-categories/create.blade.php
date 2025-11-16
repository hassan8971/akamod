@extends('admin.layouts.app')
@section('title', 'افزودن دسته‌بندی وبلاگ')

@section('content')
<div dir="rtl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">افزودن دسته‌بندی جدید</h1>
        <a href="{{ route('admin.blog-categories.index') }}" class="px-4 py-2 text-gray-600 rounded-lg hover:bg-gray-100">
            &larr; بازگشت
        </a>
    </div>

    <form action="{{ route('admin.blog-categories.store') }}" method="POST" enctype="multipart/form-data">
        @include('admin.blog-categories._form')
    </form>
</div>
@endsection