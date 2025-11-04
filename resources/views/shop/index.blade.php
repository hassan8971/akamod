@extends('layouts.app')

@section('title', isset($category) ? 'محصولات ' . $category->name : 'فروشگاه - همه محصولات')

@section('content')
    <div class="flex flex-row-reverse">
        <aside class="w-1/4 p-4">
            <h2 class="text-xl font-bold mb-4 text-right">دسته‌بندی‌ها</h2>
            <ul class="space-y-2 text-right">
                <li>
                    <a href="{{ route('shop.index') }}" 
                       class="block px-3 py-2 rounded-lg {{ !isset($category) ? 'bg-blue-600 text-white' : 'hover:bg-gray-200' }}">
                       همه محصولات
                    </a>
                </li>
                @foreach ($categories as $cat)
                    <li>
                        <a href="{{ route('shop.category', $cat->slug) }}" 
                           class="block px-3 py-2 rounded-lg {{ (isset($category) && $category->id == $cat->id) ? 'bg-blue-600 text-white' : 'hover:bg-gray-200' }}">
                           {{ $cat->name }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </aside>

        <div class="w-3/4 p-4">
            <h1 class="text-3xl font-bold mb-6 text-right">
                {{ isset($category) ? $category->name : 'همه محصولات' }}
            </h1>

            @if ($products->isEmpty())
                <p class="text-gray-600 text-right">هیچ محصولی در این دسته‌بندی یافت نشد.</p>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($products as $product)
                        @include('components.product-card', ['product' => $product])
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection