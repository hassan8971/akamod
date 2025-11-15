@extends('admin.layouts.app')
@section('title', 'افزودن مقاله جدید')

@section('content')
<div dir="rtl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">افزودن مقاله جدید</h1>
        <a href="{{ route('admin.posts.index') }}" class="px-4 py-2 text-gray-600 rounded-lg hover:bg-gray-100">
            &larr; بازگشت
        </a>
    </div>

    @if ($errors->any())
        <div class="bg-red-100 border-r-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
            <strong class="font-bold">خطا!</strong>
            <ul class="mt-3 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data">
        @include('admin.posts._form')
    </form>
</div>
@endsection