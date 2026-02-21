<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\MenuItem;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // We use a View Composer to share menu items with the main layout
        View::composer('layouts.app', function ($view) {
            
            // ۱. Get Main Header Menu (منوی اصلی هدر)
            $headerMenuItems = MenuItem::where('menu_group', 'main_header')
                                       ->whereNull('parent_id') // Get only top-level items
                                       ->with('children') // Eager load sub-menus
                                       ->orderBy('order', 'asc')
                                       ->get();
            
            // ۲. Get Footer Links (لینک‌های فوتر)
            $footerMenuItems = MenuItem::where('menu_group', 'footer_links')
                                       ->whereNull('parent_id')
                                       ->orderBy('order', 'asc')
                                       ->get();

            // ۳. Share data with the view
            $view->with('headerMenuItems', $headerMenuItems)
                 ->with('footerMenuItems', $footerMenuItems);
        });

        View::composer('*', function ($view) {
            $view->with('activeProject', session('active_project', 'sibaneh'));
        });
    }
}
