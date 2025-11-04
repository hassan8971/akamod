@extends('admin.layouts.app')

@section('title', 'سفارش #' . $order->id)

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">جزئیات سفارش</h1>
        <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 text-gray-600 rounded-lg hover:bg-gray-100">
            بازگشت به سفارشات &rarr;
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 text-right" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2 space-y-8">
            
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4 text-right">اقلام سفارش ({{ $order->items->count() }})</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold uppercase">محصول</th>
                                <th class="px-4 py-2 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold uppercase">قیمت واحد</th>
                                <th class="px-4 py-2 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold uppercase">تعداد</th>
                                <th class="px-4 py-2 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold uppercase">مجموع</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 border-b border-gray-200 text-right">
                                        <div class="font-medium">{{ $item->name }}</div>
                                        <div class="text-sm text-gray-600">
                                            @if ($item->productVariant)
                                                {{ $item->productVariant->size }}, {{ $item->productVariant->color }}
                                            @else
                                                (مدل یافت نشد)
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 border-b border-gray-200 text-right">{{ number_format($item->price) }} تومان</td>
                                    <td class="px-4 py-3 border-b border-gray-200 text-right">x {{ $item->quantity }}</td>
                                    <td class="px-4 py-3 border-b border-gray-200 text-right">{{ number_format(($item->price * $item->quantity)) }} تومان</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4 text-right">اطلاعات ارسال</h2>
                
                
                @if ($order->address)
                    <div class="text-gray-700 space-y-2 text-right">
                        <p class_="font-medium">
                            {{ $order->address->first_name }} {{ $order->address->last_name }}
                        </p>
                        <p>{{ $order->address->address }}</p>
                        <p>{{ $order->address->city }}, {{ $order->address->state }} {{ $order->address->zip_code }}</p>
                        <hr class="my-2">
                        <p><span class="font-medium">ایمیل:</span> {{ $order->address->email }}</p>
                        <p><span class="font-medium">تلفن:</span> {{ $order->address->phone }}</p>
                    </div>
                @else
                    <p class="text-gray-500 text-right">هیچ آدرس ارسالی برای این سفارش ثبت نشده است.</p>
                @endif

            </div>

        </div>

        <div class="lg:col-span-1 space-y-8">
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4 text-right">خلاصه سفارش</h2>

                <dl class="space-y-3">
                    <div class="flex justify-between items-center">
                        <dt class="text-gray-600">شناسه سفارش</dt>
                        <dd class="font-medium">#{{ $order->id }}</dd>
                    </div>
                    <div class="flex justify-between items-center">
                        <dt class="text-gray-600">تاریخ سفارش</dt>
                        <dd class="font-medium">{{ $order->created_at->format('M d, Y') }}</dd>
                    </div>
                    <div class="flex justify-between items-center">
                        <dt class="text-gray-600">مشتری</dt>
                        <dd class="font-medium">{{ $order->user ? $order->user->name : ($order->address->first_name ?? 'مهمان') }}</dd>
                    </div>
                    <div class="flex justify-between items-center">
                        <dt class="text-gray-600">روش پرداخت</dt>
                        <dd class="font-medium">{{ $order->payment_method }}</dd>
                    </div>
                    <div class="flex justify-between items-center">
                        <dt class="text-gray-600">وضعیت پرداخت</dt>
                        <dd class="font-medium">
                            @if ($order->payment_status === 'paid')
                                <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">پرداخت شده</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded-full">در انتظار</span>
                            @endif
                        </dd>
                    </div>
                    <div class="flex justify-between items-center text-lg font-bold pt-2 border-t mt-2">
                        <dt>مبلغ نهایی</dt>
                        <dd>{{ number_format($order->total) }} تومان</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4 text-right">بروزرسانی وضعیت سفارش</h2>
                <form action="{{ route('admin.orders.update', $order) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 text-right">وضعیت</Labe>
                        <select id="status" name="status" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-right">
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>در انتظار</option>
                            <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>در حال پردازش</option>
                            <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>ارسال شده</option>
                            <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>تکمیل شده</option>
                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>لغو شده</option>
                        </select>
                    </div>
                    
                    <div class="flex justify-start mt-4">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            بروزرسانی وضعیت
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
    </div>
@endsection