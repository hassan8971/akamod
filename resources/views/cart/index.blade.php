@extends('layouts.app')

@section('title', 'سبد خرید شما')

@section('content')
    <div class="bg-white shadow-lg rounded-lg overflow-hidden p-8">
        <h1 class="text-3xl font-bold mb-6 text-right">سبد خرید شما</h1>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 text-right" role="alert">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 text-right" role="alert">
                {{ session('error') }}
            </div>
        @endif

        @if ($cartItems->isEmpty())
            <p class="text-gray-600 text-lg text-right">سبد خرید شما خالی است.</p>
            <a href="{{ route('shop.index') }}" class="mt-4 inline-block px-6 py-3 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700">
                شروع خرید
            </a>
        @else
            <!-- Cart Items Table -->
            <div class="overflow-x-auto mb-6">
                <table class="min-w-full">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold uppercase">محصول</th>
                            <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold uppercase">قیمت</th>
                            <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold uppercase">تعداد</th>
                            <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold uppercase">مجموع</th>
                            <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-100"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cartItems->sortBy('name') as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 border-b border-gray-200 text-right">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 w-20 h-20">
                                            <img class="w-full h-full object-cover rounded" 
                                                 src="{{ $item->attributes->image ? Storage::url($item->attributes->image) : 'https://placehold.co/100x100/e2e8f0/cccccc?text=بدون+تصویر' }}" 
                                                 alt="{{ $item->name }}">
                                        </div>
                                        <div class="mr-4">
                                            <a href="{{ route('shop.show', $item->attributes->slug) }}" class="font-semibold text-gray-800 hover:text-blue-600">{{ $item->name }}</a>
                                            <div class="text-sm text-gray-600">{{ $item->attributes->variant_name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 border-b border-gray-200 text-right">{{ number_format($item->price) }} تومان</td>
                                <td class="px-6 py-4 border-b border-gray-200 text-right">
                                    <form action="{{ route('cart.update', $item->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" 
                                               class="w-20 px-3 py-2 border border-gray-300 rounded-md shadow-sm text-right">
                                        <button type="submit" class="mr-2 px-3 py-1 text-xs bg-gray-200 text-gray-700 rounded hover:bg-gray-300">بروزرسانی</button>
                                    </form>
                                </td>
                                <td class="px-6 py-4 border-b border-gray-200 text-right">{{ number_format($item->price * $item->quantity) }} تومان</td>
                                <td class="px-6 py-4 border-b border-gray-200 text-left">
                                    <form action="{{ route('cart.destroy', $item->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 font-semibold">حذف</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Cart Totals & Actions -->
            <div class="flex justify-between items-start">
                <div class="text-right">
                    <form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('آیا از خالی کردن سبد خرید اطمینان دارید؟');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200">
                            خالی کردن سبد
                        </button>
                    </form>
                </div>
                <div class="text-left">
                    <h2 class="text-2xl font-semibold text-right">
                        <span class="font-bold text-blue-600">{{ number_format($cartTotal) }} تومان</span> :مجموع
                    </h2>
                    <p class="text-gray-500 text-sm mb-4 text-right">هزینه ارسال و مالیات در تسویه حساب محاسبه می‌شود.</p>
                    <a href="{{ route('checkout.index') }}" class="w-full inline-block text-center px-8 py-4 bg-blue-600 text-white text-lg font-semibold rounded-lg shadow hover:bg-blue-700">
                        ادامه جهت تسویه حساب
                    </a>
                </div>
            </div>
        @endif
    </div>
@endsection