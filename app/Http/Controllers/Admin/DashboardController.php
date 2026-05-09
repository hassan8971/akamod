<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem; // <-- Add this
use App\Models\Product;
use App\Models\User;
use App\Models\MenuItem; // <-- Add this
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. آمار کلی (کارت‌های بالا)
        $completedStatuses = ['shipped', 'delivered', 'completed']; 
        
        $totalRevenue = Order::whereIn('status', $completedStatuses)->sum('total');
        $totalOrders = Order::count();
        $totalCustomers = User::count();
        $totalProducts = Product::count();

        // 2. سفارشات اخیر 
        $recentOrders = Order::with('user')
                             ->latest()
                             ->take(5) 
                             ->get();

        // 3. داده‌های نمودار درآمد
        $salesData = Order::whereIn('status', $completedStatuses) 
            ->where('created_at', '>=', now()->subDays(7))
            ->select(
                DB::raw('DATE(created_at) as date'), 
                DB::raw('SUM(total) as revenue')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $chartLabels = $salesData->pluck('date')->map(function ($date) {
            return \Morilog\Jalali\Jalalian::fromCarbon(Carbon::parse($date))->format('Y/m/d'); 
        });
        $chartData = $salesData->pluck('revenue');
        
        
        // ==============================================================
        // 4. داده‌های نمودار دسته‌بندی‌ها (Load from Menus)
        // ==============================================================
        
        // Step A: Get top-level header menus
        // Adjust 'main_header' if your menu_group name is different in DB
        $mainMenus = MenuItem::whereNull('parent_id')
                             ->where('menu_group', 'main_header') 
                             ->orderBy('order', 'asc')
                             ->get();

        $categoryChartLabels = $mainMenus->pluck('name')->toArray();

        // Step B: Calculate actual sales per category by joining order items
        // (Assuming order_items -> product_variants -> products -> categories)
        $salesByCategory = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('product_variants', 'order_items.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->whereIn('orders.status', $completedStatuses)
            ->select('categories.name', DB::raw('SUM(order_items.price * order_items.quantity) as total_revenue'))
            ->groupBy('categories.name')
            ->pluck('total_revenue', 'name'); // Creates an array like ['کفش' => 500000]

        // Step C: Match the sales data to the Menu items
        $categoryChartData = [];
        foreach ($mainMenus as $menu) {
            // It tries to find a category that has the exact same name as the menu item.
            // If it doesn't find any sales for it, it defaults to 0.
            $categoryChartData[] = $salesByCategory->get($menu->name, 0); 
        }

        return view('admin.dashboard', compact(
            'totalRevenue',
            'totalOrders',
            'totalCustomers',
            'totalProducts',
            'recentOrders',
            'chartLabels',
            'chartData',
            'categoryChartLabels', // <-- Dynamically loaded from Menus
            'categoryChartData'    // <-- Dynamically calculated from Order Items
        ));
    }
}