@extends('admin.layouts.app')

@section('title', 'ایجاد محصول جدید')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-right">ایجاد محصول جدید</h1>

    <div class="bg-white shadow-md rounded-lg p-6">
        <form action="{{ route('admin.products.store') }}" method="POST">
            @csrf
            
            @include('admin.products._form')

            <div class="flex justify-start mt-6">
                <a href="{{ route('admin.products.index') }}" class="px-4 py-2 text-gray-600 rounded-lg hover:bg-gray-100 ml-2">
                    لغو
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    ایجاد محصول
                </button>
            </div>
        </form>
    </div>
@endsection