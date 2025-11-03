<div class="bg-white shadow-lg rounded-lg overflow-hidden transition-transform duration-300 hover:scale-105">
    <a href="{{ route('shop.show', $product->slug) }}" class="block">
        <!-- Image -->
        <img src="{{ $product->images->first() ? Storage::url($product->images->first()->path) : 'https://placehold.co/400x400/e2e8f0/cccccc?text=No+Image' }}"
             alt="{{ $product->name }}"
             class="w-full h-56 object-cover">
        
        <div class="p-4">
            <!-- Category -->
            <span class="text-xs text-gray-500">{{ $product->category->name }}</span>
            <!-- Name -->
            <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $product->name }}</h3>
            <!-- Price -->
            <p class="text-lg font-bold text-blue-600 mt-2">
                <!-- This is simplified. A real site would show a price range. -->
                Toman {{ number_format($product->variants->min('price')) }}
            </p>
        </div>
    </a>
</div>
