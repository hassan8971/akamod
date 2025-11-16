<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // <-- 1. این را اضافه کنید
use Carbon\Carbon; // <-- 2. این را اضافه کنید

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     * --- این متد کاملاً بازنویسی شده است ---
     */
    public function index()
    {
        // 1. آمار کلی (کارت‌های بالا)
        // (ما فقط سفارشات تکمیل شده را به عنوان درآمد محاسبه می‌کنیم)
        $completedStatuses = ['shipped', 'delivered', 'completed']; // وضعیت‌هایی که نشانه درآمد قطعی است
        
        $totalRevenue = Order::whereIn('status', $completedStatuses)->sum('total');
        $totalOrders = Order::count();
        $totalCustomers = User::count();
        $totalProducts = Product::count();

        // 2. سفارشات اخیر (برای جدول پایین)
        $recentOrders = Order::with('user')
                             ->latest()
                             ->take(5) // 5 سفارش آخر
                             ->get();

        // 3. داده‌های نمودار درآمد (مثلاً ۷ روز گذشته)
        $salesData = Order::where('status', 'processing') // یا وضعیت‌های تکمیل‌شده شما
            ->where('created_at', '>=', now()->subDays(7))
            ->select(
                DB::raw('DATE(created_at) as date'), 
                DB::raw('SUM(total) as revenue')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // آماده‌سازی داده‌ها برای Chart.js
        $chartLabels = $salesData->pluck('date')->map(function ($date) {
            // تبدیل تاریخ میلادی به شمسی برای نمایش در نمودار
            return jdate($date)->format('Y/m/d'); 
        });
        $chartData = $salesData->pluck('revenue');
        
        // 4. داده‌های نمودار دسته‌بندی‌ها (مثال)
        // (این یک کوئری پیچیده‌تر است، فعلاً داده‌های ساختگی می‌فرستیم)
        $categoryChartLabels = ['پوشاک', 'کفش', 'لوازم جانبی'];
        $categoryChartData = [50, 30, 20]; // (شما باید این را با کوئری واقعی جایگزین کنید)


        return view('admin.dashboard', compact(
            'totalRevenue',
            'totalOrders',
            'totalCustomers',
            'totalProducts',
            'recentOrders',
            'chartLabels',
            'chartData',
            'categoryChartLabels',
            'categoryChartData'
        ));
    }
}