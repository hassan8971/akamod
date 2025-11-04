@extends('layouts.app')

@section('title', $product->name)

@section('content')
    <div x-data="{
            variants: {{ $variantsJson }},
            selected: { size: '{{ $options['sizes']->first() }}', color: '{{ $options['colors']->first() }}' },
            get currentVariantKey() { return this.selected.size + '-' + this.selected.color },
            get currentVariant() { return this.variants[this.currentVariantKey] || null },
            get currentPrice() {
                if (this.currentVariant && this.currentVariant.stock > 0) {
                    return parseInt(this.currentVariant.price).toLocaleString('fa-IR');
                }
                return '---';
            },
            get inStock() {
                return this.currentVariant && this.currentVariant.stock > 0;
            }
        }"
        class="bg-white shadow-lg rounded-lg overflow-hidden md:flex md:flex-row-reverse">

        <div class="md:w-1/2">
            <img src="{{ $product->images->first() ? Storage::url($product->images->first()->path) : 'https://placehold.co/600x600/e2e8f0/cccccc?text=بدون+تصویر' }}"
                 alt="{{ $product->name }}"
                 class="w-full h-full object-cover">
        </div>

        <div class="md:w-1/2 p-8 text-right">
            <h1 class="text-4xl font-bold mb-2">{{ $product->name }}</h1>
            <p class="text-gray-600 mb-4">{{ $product->category->name }}</p>

            <div class="mb-6">
                <span class="text-3xl font-bold text-blue-600"
                      x-text="currentVariant ? currentPrice + ' تومان' : 'گزینه‌ها را انتخاب کنید'">
                </span>
                <span x-show="!inStock && currentVariant" class="text-red-500 mr-2">ناموجود</span>
            </div>

            @if ($options['sizes']->isNotEmpty())
            <div class="mb-4">
                <label for="size" class="block text-sm font-medium text-gray-700 mb-2">اندازه</label>
                <select x-model="selected.size" id="size" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-right">
                    @foreach ($options['sizes'] as $size)
                        <option value="{{ $size }}">{{ $size }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            @if ($options['colors']->isNotEmpty())
            <div class="mb-6">
                <label for="color" class="block text-sm font-medium text-gray-700 mb-2">رنگ</label>
                <select x-model="selected.color" id="color" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-right">
                    @foreach ($options['colors'] as $color)
                        <option value="{{ $color }}">{{ $color }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <form action="{{ route('cart.store') }}" method="POST">
                @csrf
                <input type="hidden" name="variant_id" x-bind:value="currentVariant ? currentVariant.id : ''">
                
                <div class="mb-6">
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">تعداد</label>
                    <input type="number" id="quantity" name="quantity" value="1" min="1"
                           x-bind:max="currentVariant ? currentVariant.stock : 1"
                           x-bind:disabled="!inStock"
                           class="w-24 px-3 py-2 border border-gray-300 rounded-md shadow-sm disabled:bg-gray-100 text-right">
                </div>
                
                <button type="submit"
                        x-bind:disabled="!inStock"
                        class="w-full px-6 py-3 text-lg font-semibold text-white bg-blue-600 rounded-lg shadow
                               hover:bg-blue-700
                               disabled:bg-gray-300 disabled:cursor-not-allowed">
                    <span x-show="inStock">افزودن به سبد خرید</span>
                    <span x-show="!inStock">ناموجود</span>
                </button>
            </form>

            <div class="mt-8">
                <h3 class="text-xl font-semibold mb-2">توضیحات</h3>
                <p class="text-gray-700 leading-relaxed">
                    {!! nl2br(e($product->description)) !!}
                </p>
            </div>

        </div>
    </div>
@endsection