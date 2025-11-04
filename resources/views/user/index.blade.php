@extends('user.layouts.app')

@section('title', 'تاریخچه سفارشات')

@section('panel-content')
    <div class="bg-white shadow-md rounded-lg p-6">
        <h1 class="text-2xl font-semibold mb-6">تاریخچه سفارشات</h1>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b-2 border-gray-200 bg-gray-50">
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase">شماره سفارش</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase">تاریخ</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase">وضعیت</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase">مبلغ کل</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase"></th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @forelse ($orders as $order)
                        <tr class="hover:bg-gray-50 border-b border-gray-200">
                            <td class="px-4 py-3 font-medium">#{{ $order->order_code }}</td>
                            <td class="px-4 py-3">{{ $order->created_at->format('Y/m/d') }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    @if ($order->status === 'completed') bg-green-100 text-green-800
                                    @elseif ($order->status === 'shipped') bg-blue-100 text-blue-800
                                    @elseif ($order->status === 'processing') bg-yellow-100 text-yellow-800
                                    @elseif ($order->status === 'cancelled') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">{{ number_format($order->total) }} تومان</td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('user.order.show', $order->id) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                    مشاهده جزئیات
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-gray-500">
                                شما هنوز هیچ سفارشی ثبت نکرده‌اید.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    </div>
@endsection

