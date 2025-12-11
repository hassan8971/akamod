@extends('admin.layouts.app')
@section('title', 'جزئیات مشتری: ' . $user->name)

@section('content')
<div dir="rtl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">پروفایل مشتری: {{ $user->name ?? '---' }}</h1>
        <a href="{{ route('admin.users.index') }}" class="px-4 py-2 text-gray-600 rounded-lg hover:bg-gray-100">
            &larr; بازگشت به لیست مشتریان
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-1 space-y-8">
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4">اطلاعات پایه</h2>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-500">نام:</span>
                        <span class="font-medium">{{ $user->name ?? '---' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">موبایل:</span>
                        <span class="font-medium" dir="ltr">{{ $user->mobile }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">ایمیل:</span>
                        <span class="font-medium">{{ $user->email ?? '---' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">تاریخ عضویت:</span>
                        <span class="font-medium">{{ jdate($user->created_at)->format('Y/m/d') }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4">آمار</h2>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-500">مجموع سفارشات:</span>
                        <span class="font-medium text-blue-600">{{ number_format($totalOrders) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">مجموع خرید (قطعی):</span>
                        <span class="font-medium text-green-600">{{ number_format($totalSpent) }} تومان</span>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4">آدرس‌های ذخیره شده ({{ $addresses->count() }})</h2>
                <div class="space-y-4">
                    @forelse($addresses as $address)
                        <div class="border-b pb-2">
                            <p class="font-medium">{{ $address->full_name }}</p>
                            <p class="text-sm text-gray-600">{{ $address->address_line_1 }}</p>
                            <p class="text-sm text-gray-600">{{ $address->city }}، {{ $address->state }}</p>
                            <p class="text-sm text-gray-500" dir="ltr">{{ $address->phone }}</p>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">این کاربر هیچ آدرس ذخیره‌شده‌ای ندارد.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white shadow-md rounded-lg">
                <h2 class="text-xl font-semibold p-6">تاریخچه سفارشات</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase">شماره سفارش</th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase">تاریخ</th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase">وضعیت</th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase">مبلغ کل</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($orders as $order)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 font-medium">{{ $order->order_code ?? "#".$order->id }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ jdate($order->created_at)->format('Y/m/d') }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                                            @switch($order->status)
                                                @case('pending') bg-yellow-100 text-yellow-800 @break
                                                @case('processing') bg-blue-100 text-blue-800 @break
                                                @case('shipped') bg-green-100 text-green-800 @break
                                                @case('delivered') bg-green-200 text-green-900 @break
                                                @case('cancelled') bg-red-100 text-red-800 @break
                                            @endswitch">
                                            {{ $order->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">{{ number_format($order->total) }} تومان</td>
                                    <td class="px-6 py-4 text-left">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-600 hover:text-blue-900 text-sm">
                                            (مشاهده)
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-10 text-center text-gray-500">
                                        این کاربر هنوز سفارشی ثبت نکرده است.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4">
                    {{ $orders->links('pagination::tailwind') }}
                </div>
            </div>
        </div>
        
    </div>
</div>
@endsection