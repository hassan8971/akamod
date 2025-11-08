@extends('admin.layouts.app')

@section('title', 'مدیریت محصولات')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-right">محصولات</h1>
        <a href="{{ route('admin.products.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            + ایجاد محصول
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 text-right" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        نام
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        دسته بندی
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        وضعیت
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        شناسه محصول
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        جنسیت
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        ایجاد کننده
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100">عملیات</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm text-right">
                            <p class="text-gray-900 whitespace-no-wrap">{{ $product->name }}</p>
                        </td>
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm text-right">
                            <p class="text-gray-900 whitespace-no-wrap">{{ $product->category->name ?? 'N/A' }}</p>
                        </td>
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm text-right">
                            @if($product->is_visible)
                                <span class="relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight">
                                    <span aria-hidden class="absolute inset-0 bg-green-200 opacity-50 rounded-full"></span>
                                    <span class="relative">قابل مشاهده</span>
                                </span>
                            @else
                                <span class="relative inline-block px-3 py-1 font-semibold text-gray-700 leading-tight">
                                    <span aria-hidden class="absolute inset-0 bg-gray-200 opacity-50 rounded-full"></span>
                                    <span class="relative">مخفی</span>
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm text-right">
                            <p class="text-gray-900 whitespace-no-wrap">{{ $product->product_id }}</p>
                        </td>
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">
                                @if($product->is_for_men && $product->is_for_women)
                                    <span class="font-semibold text-gray-800">هردو</span>
                                @elseif($product->is_for_men)
                                    <span class="font-semibold text-blue-600">آقایان</span>
                                @elseif($product->is_for_women)
                                    <span class="font-semibold text-pink-600">بانوان</span>
                                @else
                                    <span class="text-gray-400">---</span>
                                @endif
                        </td>
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm text-right">
                            <p class="text-gray-900 whitespace-no-wrap">
                                @if($product->admin)
                                {{ $product->admin->name }}
                                @else
                                نامشخص
                                @endif
                            </p>
                        </td>
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm text-left">
                            <a href="{{ route('admin.products.edit', $product) }}" class="text-blue-600 hover:text-blue-900">ویرایش</a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline-block mr-4" onsubmit="return confirm('آیا از حذف این محصول اطمینان دارید؟ این عمل دائمی است.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">حذف</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">
                            هیچ محصولی یافت نشد. <a href="{{ route('admin.products.create') }}" class="text-blue-600 hover:underline">یکی ایجاد کنید!</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $products->links() }}
    </div>
@endsection