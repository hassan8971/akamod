<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow, noarchive">
    <title>@yield('title', 'پنل مدیریت') - فروشگاه من</title>
    <script src="{{ asset('js/tailwindcss.js') }}"></script>
    <!-- <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"> -->
    <style>
        body { font-family: IRANSansX;  }
        /* برای نمایش بهتر فونت فارسی، بهتر است یک فونت فارسی مانند وزیر یا ایران‌سنس اضافه کنید */

    </style>
    <link href="{{ asset('css/all.css') }}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @stack('styles')

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

                <a href="{{ route('admin.orders.index') }}" 
                class="flex items-center space-x-3 space-x-reverse px-4 py-3 rounded-lg text-gray-200 hover:bg-slate-700 hover:text-white
                        {{ request()->routeIs('admin.orders.*') ? 'bg-slate-900 text-white' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span> سفارشات </span>
                </a>

                <a href="{{ route('admin.users.index') }}" 
                class="flex items-center space-x-3 space-x-reverse px-4 py-3 rounded-lg text-gray-200 hover:bg-slate-700 hover:text-white
                        {{ request()->routeIs('admin.users.*') ? 'bg-slate-900 text-white' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <span>مشتریان</span>
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

                <a href="{{ route('admin.packaging-options.index') }}" 
                class="flex items-center space-x-3 px-4 py-3 rounded-lg space-x-reverse text-gray-200 hover:bg-slate-700 hover:text-white
                        {{ request()->routeIs('admin.packaging-options.*') ? 'bg-slate-900 text-white' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    <span>انواع بسته‌بندی</span>
                </a>

                <a href="{{ route('admin.shipping-methods.index') }}" 
                class="flex items-center space-x-3 px-4 py-3 rounded-lg space-x-reverse text-gray-200 hover:bg-slate-700 hover:text-white
                        {{ request()->routeIs('admin.shipping-methods.*') ? 'bg-slate-900 text-white' : '' }}">
                    
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v8m-6 4h6m2 0h.01M16 16v-4h4l3 3v5h-3m-6 0a2 2 0 10-4 0 2 2 0 004 0zm10 0a2 2 0 10-4 0 2 2 0 004 0z"></path>
                    </svg>
                    
                    <span>روش های ارسال</span>
                </a>

                <a href="{{ route('admin.discounts.index') }}" 
                class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-200 space-x-reverse hover:bg-slate-700 hover:text-white
                        {{ request()->routeIs('admin.discounts.*') ? 'bg-slate-900 text-white' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                    <span>کدهای تخفیف</span>
                </a>

                <div class="px-4 py-2">
                    <span class="text-xs font-semibold text-gray-500 uppercase">ویژگی‌ها</span>
                </div>

                <a href="{{ route('admin.videos.index') }}" 
                class="flex items-center space-x-3 px-4 py-3 rounded-lg space-x-reverse text-gray-200 hover:bg-slate-700 hover:text-white
                        {{ request()->routeIs('admin.videos.*') ? 'bg-slate-900 text-white' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M4 5h11a1 1 0 011 1v12a1 1 0 01-1 1H4a1 1 0 01-1-1V6a1 1 0 011-1z"></path></svg>
                    <span>کتابخانه ویدیو</span>
                </a>

                <a href="{{ route('admin.sizes.index') }}" 
                class="flex items-center space-x-3 px-4 py-3 rounded-lg space-x-reverse text-gray-200 hover:bg-slate-700 hover:text-white
                        {{ request()->routeIs('admin.sizes.*') ? 'bg-slate-900 text-white' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 1v4m0 0h-4m4 0l-5-5"></path></svg>
                    <span>مدیریت سایزها</span>
                </a>

                <a href="{{ route('admin.colors.index') }}" 
                class="flex items-center space-x-3 px-4 py-3 rounded-lg space-x-reverse text-gray-200 hover:bg-slate-700 hover:text-white
                        {{ request()->routeIs('admin.colors.*') ? 'bg-slate-900 text-white' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                    <span>مدیریت رنگ‌ها</span>
                </a>

                <a href="{{ route('admin.buy-sources.index') }}" 
                class="flex items-center space-x-3 px-4 py-3 rounded-lg space-x-reverse text-gray-200 hover:bg-slate-700 hover:text-white
                        {{ request()->routeIs('admin.buy-sources.*') ? 'bg-slate-900 text-white' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0v-4a1 1 0 011-1h2a1 1 0 011 1v4m-4 0V9m0 0h14m-14 0V5m14 16v-4a1 1 0 00-1-1h-2a1 1 0 00-1 1v4m-4 0V9"></path></svg>
                    <span>منابع خرید</span>
                </a>

                <a href="{{ route('admin.menu-items.index') }}" 
                class="flex items-center space-x-3 px-4 py-3 rounded-lg space-x-reverse text-gray-200 hover:bg-slate-700 hover:text-white
                        {{ request()->routeIs('admin.menu-items.*') ? 'bg-slate-900 text-white' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    <span>مدیریت منوها</span>
                </a>

                <a href="{{ route('admin.posts.index') }}" 
                class="flex items-center space-x-3 px-4 py-3 rounded-lg space-x-reverse text-gray-200 hover:bg-slate-700 hover:text-white
                        {{ request()->routeIs('admin.posts.*') ? 'bg-slate-900 text-white' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-12.75c-.621 0-1.125-.504-1.125-1.125v-11.25c0-.621.504-1.125 1.125-1.125H6.75m0 0H5.625c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V8.625c0-.621-.504-1.125-1.125-1.125H12m0 0V4.875c0-.621-.504-1.125-1.125-1.125H9.375c-.621 0-1.125.504-1.125 1.125v2.625m3 0v2.625" />
                    </svg>
                    <span>مقالات</span>
                </a>
                <a href="{{ route('admin.blog-categories.index') }}" 
                class="flex items-center space-x-3 px-4 py-3 rounded-lg space-x-reverse text-gray-200 hover:bg-slate-700 hover:text-white
                        {{ request()->routeIs('admin.blog-categories.*') ? 'bg-slate-900 text-white' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
                    </svg>
                    <span>دسته‌بندی‌های وبلاگ</span>
                </a>

                <a href="{{ route('admin.homepage.edit') }}" 
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg space-x-reverse text-gray-200 hover:bg-slate-700 hover:text-white
                    {{ request()->routeIs('admin.homepage.*') ? 'bg-slate-900 text-white' : '' }}">
    
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 0 0-5.78 1.128 2.25 2.25 0 0 1-2.4 2.245 4.5 4.5 0 0 0 8.4-2.245c0-.399-.078-.78-.22-1.128Zm0 0a15.998 15.998 0 0 0 3.388-1.62m-5.043-.025a15.994 15.994 0 0 1 1.622-3.395m3.42 3.42a15.995 15.995 0 0 0 4.764-4.648l3.879-3.879a1.5 1.5 0 1 0-2.12-2.122l-3.88 3.88a15.996 15.996 0 0 0-4.648 4.764m3.42 3.42a6.75 6.75 0 0 0-3.42-3.42" />
                    </svg>
                    
                    <span>تنظیمات صفحه اصلی</span>
                </a>

                <a href="{{ route('admin.newsletter.index') }}" 
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg space-x-reverse text-gray-200 hover:bg-slate-700 hover:text-white
                            {{ request()->routeIs('admin.newsletter.*') ? 'bg-slate-900 text-white' : '' }}">
                    
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                    </svg>
                    
                    <span>اعضای خبرنامه</span>
                </a>

                <a href="{{ route('admin.contacts.index') }}" 
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg space-x-reverse text-gray-200 hover:bg-slate-700 hover:text-white
                            {{ request()->routeIs('admin.contacts.*') ? 'bg-slate-900 text-white' : '' }}">
                    
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 0 1-.825-.242m9.345-8.334a2.126 2.126 0 0 0-.476-.095 48.64 48.64 0 0 0-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0 0 11.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155" />
                    </svg>
                    
                    <span>پیام‌های تماس با ما</span>
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

@stack('scripts')

</body>
</html>