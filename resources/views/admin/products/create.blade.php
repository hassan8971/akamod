@extends('admin.layouts.app')

@section('title', 'Create Product')

@section('content')
    <h1 class="text-3xl font-bold mb-6">Create New Product</h1>

    <div class="bg-white shadow-md rounded-lg p-6">
        <form action="{{ route('admin.products.store') }}" method="POST">
            @csrf
            
            <!-- Shared Form Partial -->
            @include('admin.products._form')

            <div class="flex justify-end mt-6">
                <a href="{{ route('admin.products.index') }}" class="px-4 py-2 text-gray-600 rounded-lg hover:bg-gray-100 mr-2">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Create Product
                </button>
            </div>
        </form>
    </div>
@endsection
