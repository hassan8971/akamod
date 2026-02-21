<?php

namespace App\Http\Controllers\Api\Bridge;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class BridgeOrderController extends Controller
{
    // دریافت لیست سفارشات
    public function index()
    {
        $orders = Order::with('user:id,name') // فقط نام کاربر کافیه
            ->latest()
            ->paginate(20); // صفحه‌بندی سمت سرور

        return response()->json($orders);
    }

    // دریافت جزئیات یک سفارش
    public function show($id)
    {
        // اصلاح مسیر: رسیدن به محصول از طریق productVariant
        $order = Order::with([
            'user', 
            'items.productVariant.product', // <--- این خط کلید حل مشکل است
            'address'
        ])->findOrFail($id);

        return response()->json($order);
    }

    // تغییر وضعیت سفارش
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled'
        ]);

        $order = Order::findOrFail($id);
        $order->update(['status' => $request->status]);

        return response()->json(['message' => 'Status updated successfully']);
    }
}