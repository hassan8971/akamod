@extends('layouts.app')

@section('title', 'نتایج جستجو برای "' . e($query) . '"')

@section('content')
<div class="container mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12" dir="rtl">

    <div class="pb-6 border-b border-gray-200 mb-8">
        <h1 class="text-3xl font-bold text-right">
            نتایج جستجو برای: 
            <span class="text-blue-600">"{{ $query }}"</span>
        </h1>
        <p class="mt-2 text-sm text-gray-600 text-right">
            {{ $products->total() }} محصول یافت شد.
        </p>
    </div>

    @if ($products->isEmpty())
        <div class="text-center py-16">
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">محصولی مطابق با جستجوی شما یافت نشد.</h2>
            <p class="text-gray-500">لطفاً با عبارات دیگری جستجو کنید.</p>
            <a href="{{ route('shop.index') }}" class="mt-6 inline-block px-6 py-3 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700">
                بازگشت به فروشگاه
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach ($products as $product)
                @include('components.product-card', ['product' => $product])
            @endforeach
        </div>

        <div class="mt-12">
            {{-- links() به طور خودکار پارامتر 'q' را به خاطر می‌سپارد چون از withQueryString() استفاده کردیم --}}
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection