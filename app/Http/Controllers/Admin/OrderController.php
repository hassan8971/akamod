<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all orders, newest first.
        // Eager load the 'user' relationship to prevent N+1 queries
        $orders = Order::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        // Eager load all relationships for this single order
        $order->load('user', 'items', 'items.productVariant', 'address');

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update the specified resource in storage.
     * (e.g., Change status from 'Pending' to 'Shipped')
     */
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string|in:pending,processing,shipped,completed,cancelled',
        ]);

        $order->update([
            'status' => $request->status,
        ]);

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Order status updated successfully.');
    }
}
