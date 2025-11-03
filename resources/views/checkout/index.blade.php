@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="container mx-auto px-4 py-12">
    <h1 class="text-3xl font-bold text-center mb-8">Checkout</h1>

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            {{ session('error') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <strong class="font-bold">Whoops!</strong>
            <span class="block sm:inline">There were some problems with your input.</span>
            <ul class="mt-3 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="flex flex-col lg:flex-row gap-12">

        <!-- Shipping Form -->
        <div class="w-full lg:w-2/3">
            <form id="checkout-form" action="{{ route('checkout.store') }}" method="POST">
                @csrf
                <div class="bg-white shadow-md rounded-lg p-6">
                    <h2 class="text-2xl font-semibold mb-6">Shipping Information</h2>

                    <!-- TODO: Add a dropdown to select from $addresses -->

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="full_name" class="block text-sm font-medium text-gray-700">Full Name</label>
                            <input type="text" id="full_name" name="full_name" value="{{ old('full_name', Auth::user()->name ?? '') }}"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', Auth::user()->mobile ?? '') }}"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" placeholder="Optional">
                        </div>
                    </div>

                    <div class="mt-6">
                        <label for="address_line_1" class="block text-sm font-medium text-gray-700">Address Line 1</label>
                        <input type="text" id="address_line_1" name="address_line_1" value="{{ old('address_line_1') }}"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                    </div>

                    <div class="mt-6">
                        <label for="address_line_2" class="block text-sm font-medium text-gray-700">Address Line 2 (Optional)</label>
                        <input type="text" id="address_line_2" name="address_line_2" value="{{ old('address_line_2') }}"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                            <input type="text" id="city" name="city" value="{{ old('city') }}"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                        <div>
                            <label for="state" class="block text-sm font-medium text-gray-700">State / Province</label>
                            <input type="text" id="state" name="state" value="{{ old('state') }}"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                        <div>
                            <label for="zip_code" class="block text-sm font-medium text-gray-700">ZIP / Postal Code</label>
                            <input type="text" id="zip_code" name="zip_code" value="{{ old('zip_code') }}"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                    </div>

                    <div class="mt-6">
                        <label for="country" class="block text-sm font-medium text-gray-700">Country</label>
                        <input type="text" id="country" name="country" value="{{ old('country') }}"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    
                    @auth
                    <div class="mt-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="save_address" value="1" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-600">Save this address to my account</span>
                        </label>
                    </div>
                    @endauth

                    <h2 class="text-2xl font-semibold mt-8 mb-6">Payment Method</h2>
                    <div class="space-y-4">
                        <!-- We will replace this with Stripe Elements later -->
                        <label class="flex items-center p-4 border rounded-lg cursor-pointer">
                            <input type="radio" name="payment_method" value="cod" class="text-blue-600" checked>
                            <span class="ml-3 text-sm font-medium text-gray-700">Cash on Delivery (COD)</span>
                        </label>
                        <label class="flex items-center p-4 border rounded-lg cursor-pointer bg-gray-50 opacity-50">
                            <input type="radio" name="payment_method" value="stripe" class="text-blue-600" disabled>
                            <span class="ml-3 text-sm font-medium text-gray-400">Credit Card (Stripe - Coming Soon)</span>
                        </label>
                    </div>
                </div>
            </form>
        </div>

        <!-- Order Summary -->
        <div class="w-full lg:w-1/3">
            <div class="bg-white shadow-md rounded-lg p-6 sticky top-8">
                <h2 class="text-2xl font-semibold mb-6">Order Summary</h2>
                <div class="space-y-4">
                    @foreach ($cartItems->sortBy('name') as $item)
                        <div class="flex justify-between items-center">
                            <div class="flex-grow">
                                <p class="font-medium">{{ $item->name }}</p>
                                <p class="text-sm text-gray-500">Qty: {{ $item->quantity }}</p>
                            </div>
                            <p class="text-gray-700">${{ number_format($item->getPriceSum()) }}</p>
                        </div>
                    @endforeach
                </div>
                <div class="border-t my-6"></div>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <p class="text-gray-600">Subtotal</p>
                        <p class="font-medium">${{ number_format(Cart::getSubTotal()) }}</p>
                    </div>
                    <div class="flex justify-between">
                        <p class="text-gray-600">Shipping</p>
                        <p class="font-medium">Free</p>
                    </div>
                    <div class="flex justify-between text-lg font-bold">
                        <p>Total</p>
                        <p>${{ number_format($total) }}</p>
                    </div>
                </div>
                <button type="submit" form="checkout-form"
                        class="w-full mt-8 bg-blue-600 text-white text-lg font-semibold py-3 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                    Place Order
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
