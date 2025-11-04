<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'پنل مدیریت') - فروشگاه من</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: IRANSansX;  }
        /* برای نمایش بهتر فونت فارسی، بهتر است یک فونت فارسی مانند وزیر یا ایران‌سنس اضافه کنید */

    </style>
    <link href="{{ asset('css/all.css') }}" rel="stylesheet">
</head>
<body class="bg-gray-100">

    <div class="flex min-h-screen flex-row">
        
        <aside class="w-64 bg-slate-800 text-gray-200 flex flex-col">
            <div class="p-6 text-center">
                <a href="{{ route('admin.dashboard') }}" class="text-2xl font-bold text-white">مدیریت فروشگاه</a>
            </div>

            <nav class="flex-grow px-4 py-2 space-y-2">
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center space-x-3 space-x-reverse px-4 py-3 rounded-lg text-gray-200 hover:bg-slate-700 hover:text-white
                          {{ request()->routeIs('admin.dashboard') ? 'bg-slate-900 text-white' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span>داشبورد</span>
                </a>
                
                <a href="{{ route('admin.categories.index') }}" 
                   class="flex items-center space-x-3 space-x-reverse px-4 py-3 rounded-lg text-gray-200 hover:bg-slate-700 hover:text-white
                          {{ request()->routeIs('admin.categories.*') ? 'bg-slate-900 text-white' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    <span>دسته بندی ها</span>
                </a>
                
                <a href="{{ route('admin.products.index') }}" 
                   class="flex items-center space-x-3 space-x-reverse px-4 py-3 rounded-lg text-gray-200 hover:bg-slate-700 hover:text-white
                          {{ request()->routeIs('admin.products.*') ? 'bg-slate-900 text-white' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    <span>محصولات</span>
                </a>
                
                </nav>

            <div class="p-4 border-t border-slate-700">
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="flex items-center space-x-3 space-x-reverse w-full text-right px-4 py-3 rounded-lg text-gray-300 hover:bg-slate-700 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        <span>خروج</span>
                    </button>
                </form>
            </div>
        </aside>

        <main class="flex-grow p-8">
            <div class="max-w-7xl mx-auto">
                @yield('content')
            </div>
        </main>
        
    </div>

</body>
</html>