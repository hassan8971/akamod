@extends('admin.layouts.app')

@section('title', 'Order #' . $order->id)

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Order Details</h1>
        <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 text-gray-600 rounded-lg hover:bg-gray-100">
            &larr; Back to Orders
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            
            <!-- Order Items -->
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Order Items ({{ $order->items->count() }})</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold uppercase">Product</th>
                                <th class="px-4 py-2 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold uppercase">Unit Price</th>
                                <th class="px-4 py-2 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold uppercase">Qty</th>
                                <th class="px-4 py-2 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 border-b border-gray-200">
                                        <div class="font-medium">{{ $item->name }}</div>
                                        <div class="text-sm text-gray-600">
                                            @if ($item->productVariant)
                                                {{ $item->productVariant->size }}, {{ $item->productVariant->color }}
                                            @else
                                                (Variant not found)
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 border-b border-gray-200">${{ number_format($item->price) }}</td>
                                    <td class="px-4 py-3 border-b border-gray-200">x {{ $item->quantity }}</td>
                                    <td class="px-4 py-3 border-b border-gray-200">${{ number_format(($item->price * $item->quantity)) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Shipping Information -->
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Shipping Information</h2>
                
                
                @if ($order->address)
                    <div class="text-gray-700 space-y-2">
                        <p class_="font-medium">
                            {{ $order->address->first_name }} {{ $order->address->last_name }}
                        </p>
                        <p>{{ $order->address->address_line_1 }}</p>
                        @if ($order->address->address_line_2)
                            <p>{{ $order->address->address_line_2 }}</p>
                        @endif
                        <p>{{ $order->address->city }}, {{ $order->address->state }} {{ $order->address->zip_code }}</p>
                        <p>{{ $order->address->country }}</p>
                        <hr class="my-2">
                        <p><span class="font-medium">Email:</span> {{ $order->address->email }}</p>
                        <p><span class="font-medium">Phone:</span> {{ $order->address->phone }}</p>
                    </div>
                @else
                    <p class="text-gray-500">No shipping address provided for this order.</p>
                @endif

            </div>

        </div>

        <!-- Sidebar / Order Summary -->
        <div class="lg:col-span-1 space-y-8">
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Order Summary</h2>

                <dl class="space-y-3">
                    <div class="flex justify-between items-center">
                        <dt class="text-gray-600">Order ID</dt>
                        <dd class="font-medium">#{{ $order->id }}</dd>
                    </div>
                    <div class="flex justify-between items-center">
                        <dt class="text-gray-600">Order Date</dt>
                        <dd class="font-medium">{{ $order->created_at->format('M d, Y') }}</dd>
                    </div>
                    <div class="flex justify-between items-center">
                        <dt class="text-gray-600">Customer</dt>
                        <dd class="font-medium">{{ $order->user ? $order->user->name : ($order->address->first_name ?? 'Guest') }}</dd>
                    </div>
                    <div class="flex justify-between items-center">
                        <dt class="text-gray-600">Payment Method</dt>
                        <dd class="font-medium">{{ $order->payment_method }}</dd>
                    </div>
                    <div class="flex justify-between items-center">
                        <dt class="text-gray-600">Payment Status</dt>
                        <dd class="font-medium">
                            @if ($order->payment_status === 'paid')
                                <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Paid</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded-full">Pending</span>
                            @endif
                        </dd>
                    </div>
                    <div class="flex justify-between items-center text-lg font-bold pt-2 border-t mt-2">
                        <dt>Total Amount</dt>
                        <dd>${{ number_format($order->total) }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Update Status -->
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Update Order Status</h2>
                <form action="{{ route('admin.orders.update', $order) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select id="status" name="status" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    
                    <div class="flex justify-end mt-4">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Update Status
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
    </div>
@endsection

