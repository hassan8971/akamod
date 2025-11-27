<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MenuItem;

class MenuController extends Controller
{
    /**
     * Display a listing of all menu items, grouped by their menu_group.
     */
    public function index()
    {
        // 1. Fetch all top-level menu items (where parent_id is null)
        // 2. Eager-load their children (sub-menu items)
        // 3. Order them correctly
        $menus = MenuItem::whereNull('parent_id')
                         ->with('children') // Eager load sub-menus
                         ->orderBy('menu_group', 'asc')
                         ->orderBy('order', 'asc')
                         ->get();

        // 4. Group them by 'menu_group' to make it easy for the frontend
        // (This will create { "main_header": [...], "footer_links": [...] })
        $groupedMenus = $menus->groupBy('menu_group');

        // 5. Return as JSON
        return response()->json($groupedMenus);
    }
}