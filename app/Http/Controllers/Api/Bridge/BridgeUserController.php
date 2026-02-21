<?php

namespace App\Http\Controllers\Api\Bridge;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class BridgeUserController extends Controller
{
    // لیست کاربران + جستجو
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('mobile', 'LIKE', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(20);
        return response()->json($users);
    }

    // جزئیات کاربر + آدرس‌ها + سفارشات + آمار
    public function show($id)
    {
        $user = User::with(['addresses', 'orders' => function($q) {
            $q->latest(); // سفارشات را به ترتیب جدیدترین بفرست
        }])->findOrFail($id);

        // محاسبه آمار سمت سرور (تا بار محاسباتی روی کلاینت نباشد)
        $completedStatuses = ['shipped', 'delivered', 'completed'];
        $totalSpent = $user->orders()->whereIn('status', $completedStatuses)->sum('total');
        $totalOrders = $user->orders()->count();

        return response()->json([
            'user' => $user,
            'addresses' => $user->addresses,
            'orders' => $user->orders,
            'totalSpent' => $totalSpent,
            'totalOrders' => $totalOrders
        ]);
    }
}