<?php

namespace App\Http\Controllers\Api\Bridge;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BridgeDashboardController extends Controller
{
    public function index()
    {
        // 1. آمار کلی
        $completedStatuses = ['shipped', 'delivered', 'completed'];
        
        $totalRevenue = Order::whereIn('status', $completedStatuses)->sum('total');
        $totalOrders = Order::count();
        $totalCustomers = User::count();
        $totalProducts = Product::count();

        // 2. سفارشات اخیر
        $recentOrders = Order::with('user:id,name') // فقط نام کاربر کافیه
            ->latest()
            ->take(5)
            ->get()
            ->map(function($order) {
                // فرمت کردن داده‌ها برای ارسال تمیز
                return [
                    'id' => $order->id,
                    'order_code' => $order->order_code,
                    'user_name' => $order->user->name ?? 'کاربر مهمان',
                    'total' => $order->total,
                    'status' => $order->status,
                    'created_at' => $order->created_at->toIso8601String(),
                ];
            });

        // 3. داده‌های نمودار درآمد (۷ روز گذشته)
        $salesData = Order::whereIn('status', ['processing', 'shipped', 'delivered', 'completed'])
            ->where('created_at', '>=', now()->subDays(7))
            ->select(
                DB::raw('DATE(created_at) as date'), 
                DB::raw('SUM(total) as revenue')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $chartLabels = $salesData->pluck('date')->map(function ($date) {
            // اگر jdate در آکامد نصب است:
            return function_exists('jdate') ? jdate($date)->format('Y/m/d') : $date;
        });
        $chartData = $salesData->pluck('revenue');

        // 4. نمودار دسته‌بندی (واقعی)
        $categoryData = DB::table('order_items')
            ->join('product_variants', 'order_items.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('count(*) as total'))
            ->groupBy('categories.name')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        return response()->json([
            'totalRevenue' => $totalRevenue,
            'totalOrders' => $totalOrders,
            'totalCustomers' => $totalCustomers,
            'totalProducts' => $totalProducts,
            'recentOrders' => $recentOrders,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
            'categoryChartLabels' => $categoryData->pluck('name'),
            'categoryChartData' => $categoryData->pluck('total'),
        ]);
    }
}