@extends('user.layouts.app')
@section('title', 'افزودن آدرس جدید')

@section('panel-content')
<div class="bg-white shadow-md rounded-lg p-6">
    <h1 class="text-2xl font-semibold mb-6">افزودن آدرس جدید</h1>
    
    <form action="{{ route('user.addresses.store') }}" method="POST">
        @include('user.addresses._form')
        
        <div class="flex justify-end mt-6">
            <a href="{{ route('user.addresses.index') }}" class="px-4 py-2 text-gray-600 rounded-lg hover:bg-gray-100">انصراف</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 mr-2">
                ذخیره آدرس
            </button>
        </div>
    </form>
</div>
@endsection