@extends('layouts.app')

@section('title', $product->name)

@section('content')
    <!-- 
        This is a complex component. It uses Alpine.js to handle variant selection.
        - x-data: Initializes the component's state
        - variants: A JSON object of all available variants
        - selected: Holds the currently chosen size and color
        - currentVariant: The single variant object that matches the selection
    -->
    <div x-data="{
            variants: {{ $variantsJson }},
            selected: { size: '{{ $options['sizes']->first() }}', color: '{{ $options['colors']->first() }}' },
            get currentVariantKey() { return this.selected.size + '-' + this.selected.color },
            get currentVariant() { return this.variants[this.currentVariantKey] || null },
            get currentPrice() {
                if (this.currentVariant && this.currentVariant.stock > 0) {
                    return parseInt(this.currentVariant.price).toLocaleString('en-US');
                }
                return '---';
            },
            get inStock() {
                return this.currentVariant && this.currentVariant.stock > 0;
            }
        }"
        class="bg-white shadow-lg rounded-lg overflow-hidden md:flex">

        <!-- Product Images -->
        <div class="md:w-1/2">
            <!-- This just shows the first image. A real site would have a gallery. -->
            <img src="{{ $product->images->first() ? Storage::url($product->images->first()->path) : 'https://placehold.co/600x600/e2e8f0/cccccc?text=No+Image' }}"
                 alt="{{ $product->name }}"
                 class="w-full h-full object-cover">
        </div>

        <!-- Product Details & Options -->
        <div class="md:w-1/2 p-8">
            <h1 class="text-4xl font-bold mb-2">{{ $product->name }}</h1>
            <p class="text-gray-600 mb-4">{{ $product->category->name }}</p>

            <!-- Price -->
            <div class="mb-6">
                <span class="text-3xl font-bold text-blue-600"
                      x-text="currentVariant ? '$' + currentPrice : 'Select options'">
                </span>
                <span x-show="!inStock && currentVariant" class="text-red-500 ml-2">Out of Stock</span>
            </div>

            <!-- Variant Selection: Size -->
            @if ($options['sizes']->isNotEmpty())
            <div class="mb-4">
                <label for="size" class="block text-sm font-medium text-gray-700 mb-2">Size</label>
                <select x-model="selected.size" id="size" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                    @foreach ($options['sizes'] as $size)
                        <option value="{{ $size }}">{{ $size }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <!-- Variant Selection: Color -->
            @if ($options['colors']->isNotEmpty())
            <div class="mb-6">
                <label for="color" class="block text-sm font-medium text-gray-700 mb-2">Color</label>
                <select x-model="selected.color" id="color" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                    @foreach ($options['colors'] as $color)
                        <option value="{{ $color }}">{{ $color }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <!-- Add to Cart Button -->
            <!-- We will build the cart logic in the next step -->
            <form action="{{ route('cart.store') }}" method="POST">
                @csrf
                <!-- This hidden input will hold the ID of the selected variant -->
                <input type="hidden" name="variant_id" x-bind:value="currentVariant ? currentVariant.id : ''">
                
                <!-- This is the new quantity input -->
                <div class="mb-6">
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                    <input type="number" id="quantity" name="quantity" value="1" min="1"
                           x-bind:max="currentVariant ? currentVariant.stock : 1"
                           x-bind:disabled="!inStock"
                           class="w-24 px-3 py-2 border border-gray-300 rounded-md shadow-sm disabled:bg-gray-100">
                </div>
                
                <button type="submit"
                        x-bind:disabled="!inStock"
                        class="w-full px-6 py-3 text-lg font-semibold text-white bg-blue-600 rounded-lg shadow
                               hover:bg-blue-700
                               disabled:bg-gray-300 disabled:cursor-not-allowed">
                    <span x-show="inStock">Add to Cart</span>
                    <span x-show="!inStock">Out of Stock</span>
                </button>
            </form>

            <!-- Product Description -->
            <div class="mt-8">
                <h3 class="text-xl font-semibold mb-2">Description</h3>
                <p class="text-gray-700 leading-relaxed">
                    {!! nl2br(e($product->description)) !!}
                </p>
            </div>

        </div>
    </div>
@endsection
