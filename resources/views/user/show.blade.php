@extends('user.layouts.app')

@section('title', 'جزئیات سفارش #' . $order->id)

@section('panel-content')
    <div class="mb-4">
        <a href="{{ route('user.index') }}" class="text-blue-600 hover:text-blue-800">
            &rarr; بازگشت به تاریخچه سفارشات
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <!-- Order Header -->
        <div class="bg-gray-50 p-6 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row justify-between items-start">
                <div>
                    <h1 class="text-2xl font-semibold">سفارش #{{ $order->id }}</h1>
                    <p class="text-gray-600 mt-1">
                        ثبت شده در: {{ $order->created_at->format('Y/m/d') }}
                    </p>
                </div>
                <div class="mt-4 sm:mt-0 sm:text-right">
                    <span class="text-lg font-bold">{{ number_format($order->total) }} تومان</span>
                    <span class="block text-sm text-gray-600">
                        وضعیت:
                        <span class="font-medium
                            @if ($order->status === 'completed') text-green-600
                            @elseif ($order->status === 'shipped') text-blue-600
                            @elseif ($order->status === 'processing') text-yellow-600
                            @elseif ($order->status === 'cancelled') text-red-600
                            @else text-gray-600 @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Order Body -->
        <div class="p-6 space-y-8">
            
            <!-- Order Items -->
            <div>
                <h2 class="text-xl font-semibold mb-4">آیتم‌های سفارش</h2>
                <ul class="divide-y divide-gray-200">
                    @foreach ($order->items as $item)
                        <li class="py-4 flex">
                            <!-- In a real app, you'd link to the product image -->
                            <div class="h-20 w-20 flex-shrink-0 overflow-hidden rounded-md border border-gray-200">
                                <img src="https://placehold.co/80x80/e2e8f0/333?text=Img" alt="{{ $item->name }}" class="h-full w-full object-cover object-center">
                            </div>

                            <div class="mr-4 flex flex-1 flex-col">
                                <div>
                                    <div class="flex justify-between text-base font-medium">
                                        <h3>{{ $item->name }}</h3>
                                        <p class="ml-4">{{ number_format($item->price * $item->quantity) }} تومان</p>
                                    </div>
                                    @if ($item->productVariant)
                                        <p class="mt-1 text-sm text-gray-500">
                                            {{ $item->productVariant->size }}، {{ $item->productVariant->color }}
                                        </p>
                                    @endif
                                </div>
                                <div class="flex flex-1 items-end justify-between text-sm">
                                    <p class="text-gray-500">تعداد: {{ $item->quantity }}</p>
                                    <p class="text-gray-500">قیمت واحد: {{ number_format($item->price) }} تومان</p>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Shipping Information -->
            @if ($order->address)
                <div>
                    <h2 class="text-xl font-semibold mb-4">آدرس ارسال</h2>
                    <div class="text-gray-700 space-y-2 bg-gray-50 p-4 rounded-lg">
                        <p class="font-medium">
                            {{ $order->address->first_name }} {{ $order->address->last_name }}
                        </p>
                        <p>{{ $order->address->address }}</p>
                        <p>{{ $order->address->city }}، {{ $order->address->state }} {{ $order->address->zip_code }}</p>
                        <hr class="my-2">
                        <p><span class="font-medium">ایمیل:</span> {{ $order->address->email }}</p>
                        <p><span class="font-medium">تلفن:</span> {{ $order->address->phone }}</p>
                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection

