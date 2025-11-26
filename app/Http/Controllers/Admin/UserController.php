<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     * (نمایش لیست مشتریان با قابلیت جستجو)
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Handle search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('mobile', 'LIKE', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Display the specified resource.
     * (نمایش پروفایل کامل مشتری)
     */
    public function show(User $user)
    {
        // Load relationships we need
        // We paginate the orders directly from the relationship
        $orders = $user->orders()
                       ->with('items')
                       ->latest()
                       ->paginate(10, ['*'], 'orders_page'); // Paginate orders with a custom name
                       
        $addresses = $user->addresses; // Load all addresses

        // Calculate stats
        $completedStatuses = ['shipped', 'delivered', 'completed']; // (یا هر وضعیتی که شما کامل می‌دانید)
        $totalSpent = $user->orders()->whereIn('status', $completedStatuses)->sum('total');
        $totalOrders = $user->orders()->count();

        return view('admin.users.show', compact('user', 'orders', 'addresses', 'totalSpent', 'totalOrders'));
    }

    // (در آینده متدهای edit, update, destroy را می‌توان در اینجا اضافه کرد)
}