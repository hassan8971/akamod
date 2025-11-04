@extends('layouts.app')

@section('title', 'تمام دسته‌بندی‌ها')

@section('content')
<div class="bg-white" dir="rtl">
    <div class="max-w-2xl mx-auto py-16 px-4 sm:py-24 sm:px-6 lg:max-w-7xl lg:px-8">
        <h2 class="text-3xl font-extrabold tracking-tight text-gray-900 mb-10 text-center">
            خرید بر اساس دسته‌بندی
        </h2>

        <div class="grid grid-cols-1 gap-y-10 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-x-6">
            @forelse ($categories as $category)
                <div class="group relative">
                    <div class="w-full h-56 bg-gray-200 rounded-lg overflow-hidden group-hover:opacity-75">
                        <img src="{{ $category->image_path ? Storage::url($category->image_path) : 'https://placehold.co/400x300/e2e8f0/cbd5e0?text=' . urlencode($category->name) }}" 
                             alt="{{ $category->name }}" 
                             class="w-full h-full object-center object-cover">
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900">
                        <a href="{{ route('shop.category', $category->slug) }}">
                            <span class="absolute inset-0"></span>
                            {{ $category->name }}
                        </a>
                    </h3>
                    @if($category->children->count() > 0)
                        <ul class="mt-1 text-sm text-gray-500">
                            @foreach ($category->children->take(3) as $child)
                                <li>
                                    <a href="{{ route('shop.category', $child->slug) }}" class="hover:text-gray-700">
                                        {{ $child->name }}
                                    </a>
                                </li>
                            @endforeach
                            @if($category->children->count() > 3)
                                <li class="text-xs">... و بیشتر</li>
                            @endif
                        </ul>
                    @endif
                </div>
            @empty
                <p class="text-gray-500 col-span-full text-center">
                    در حال حاضر هیچ دسته‌بندی برای نمایش وجود ندارد.
                </p>
            @endforelse
        </div>
    </div>
</div>
@endsection
