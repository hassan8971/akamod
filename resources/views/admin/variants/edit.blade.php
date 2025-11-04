@extends('admin.layouts.app')

@section('title', 'ویرایش مدل')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-right">ویرایش مدل</h1>
        <a href="{{ route('admin.products.edit', $product) }}" class="px-4 py-2 text-gray-600 rounded-lg hover:bg-gray-100">
            بازگشت به محصول &rarr;
        </a>
    </div>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 text-right" role="alert">
            <strong class="font-bold">خطا!</strong>
            <span class="block sm:inline">ورودی شما دارای چند مشکل است.</span>
            <ul class="mt-3 list-disc list-inside text-sm text-right">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg p-6">
        <form action="{{ route('admin.variants.update', $variant) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 text-right">نام مدل (مثلاً: «کوچک، قرمز»)</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $variant->name) }}" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-right" required>
                </div>
                <div>
                    <label for="size" class="block text-sm font-medium text-gray-700 text-right">اندازه</label>
                    <input type="text" name="size" id="size" value="{{ old('size', $variant->size) }}" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-right">
                </div>
                <div>
                    <label for="color" class="block text-sm font-medium text-gray-700 text-right">رنگ</label>
                    <input type="text" name="color" id="color" value="{{ old('color', $variant->color) }}" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-right">
                </div>
                <div>
                    <!-- We divide by 100 to show dollars again -->
                    <label for="price" class="block text-sm font-medium text-gray-700 text-right">قیمت (به تومان)</label>
                    <input type="number" name="price" id="price" value="{{ old('price', $variant->price / 100) }}" 
                           step="0.01" min="0" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-right" required>
                </div>
                <div>
                    <label for="stock" class="block text-sm font-medium text-gray-700 text-right">تعداد موجودی</label>
                    <input type="number" name="stock" id="stock" value="{{ old('stock', $variant->stock) }}" 
                           min="0" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-right" required>
                </div>
            </div>

            <div class="flex justify-start mt-6">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    ذخیره تغییرات
                </button>
            </div>
        </form>
    </div>
@endsection