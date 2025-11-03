@extends('layouts.app')

@section('title', 'Your Shopping Cart')

@section('content')
    <div class="bg-white shadow-lg rounded-lg overflow-hidden p-8">
        <h1 class="text-3xl font-bold mb-6">Your Shopping Cart</h1>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                {{ session('error') }}
            </div>
        @endif

        @if ($cartItems->isEmpty())
            <p class="text-gray-600 text-lg">Your cart is empty.</p>
            <a href="{{ route('shop.index') }}" class="mt-4 inline-block px-6 py-3 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700">
                Start Shopping
            </a>
        @else
            <!-- Cart Items Table -->
            <div class="overflow-x-auto mb-6">
                <table class="min-w-full">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold uppercase">Product</th>
                            <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold uppercase">Price</th>
                            <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold uppercase">Quantity</th>
                            <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold uppercase">Total</th>
                            <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-100"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cartItems->sortBy('name') as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 border-b border-gray-200">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 w-20 h-20">
                                            <img class="w-full h-full object-cover rounded" 
                                                 src="{{ $item->attributes->image ? Storage::url($item->attributes->image) : 'https://placehold.co/100x100/e2e8f0/cccccc?text=No+Image' }}" 
                                                 alt="{{ $item->name }}">
                                        </div>
                                        <div class="ml-4">
                                            <a href="{{ route('shop.show', $item->attributes->slug) }}" class="font-semibold text-gray-800 hover:text-blue-600">{{ $item->name }}</a>
                                            <div class="text-sm text-gray-600">{{ $item->attributes->variant_name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 border-b border-gray-200">Toman {{ number_format($item->price) }}</td>
                                <td class="px-6 py-4 border-b border-gray-200">
                                    <form action="{{ route('cart.update', $item->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" 
                                               class="w-20 px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                                        <button type="submit" class="ml-2 px-3 py-1 text-xs bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Update</button>
                                    </form>
                                </td>
                                <td class="px-6 py-4 border-b border-gray-200">${{ number_format($item->price * $item->quantity) }}</td>
                                <td class="px-6 py-4 border-b border-gray-200 text-right">
                                    <form action="{{ route('cart.destroy', $item->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 font-semibold">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Cart Totals & Actions -->
            <div class="flex justify-between items-start">
                <div>
                    <form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('Are you sure you want to clear your cart?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200">
                            Clear Cart
                        </button>
                    </form>
                </div>
                <div class="text-right">
                    <h2 class="text-2xl font-semibold">
                        Subtotal: <span class="font-bold text-blue-600">${{ number_format($cartTotal) }}</span>
                    </h2>
                    <p class="text-gray-500 text-sm mb-4">Shipping & taxes calculated at checkout.</p>
                    <a href="{{ route('checkout.index') }}" class="w-full inline-block text-center px-8 py-4 bg-blue-600 text-white text-lg font-semibold rounded-lg shadow hover:bg-blue-700">
                        Proceed to Checkout
                    </a>
                </div>
            </div>
        @endif
    </div>
@endsection
