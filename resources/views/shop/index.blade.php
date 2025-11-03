@extends('layouts.app')

@section('title', isset($category) ? $category->name . ' Products' : 'Shop All Products')

@section('content')
    <div class="flex">
        <!-- Sidebar: Categories -->
        <aside class="w-1/4 p-4">
            <h2 class="text-xl font-bold mb-4">Categories</h2>
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('shop.index') }}" 
                       class="block px-3 py-2 rounded-lg {{ !isset($category) ? 'bg-blue-600 text-white' : 'hover:bg-gray-200' }}">
                       All Products
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

        <!-- Main Content: Product Grid -->
        <div class="w-3/4 p-4">
            <h1 class="text-3xl font-bold mb-6">
                {{ isset($category) ? $category->name : 'All Products' }}
            </h1>

            @if ($products->isEmpty())
                <p class="text-gray-600">No products found in this category.</p>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($products as $product)
                        @include('components.product-card', ['product' => $product])
                    @endforeach
                </div>

                <!-- Pagination Links -->
                <div class="mt-8">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
