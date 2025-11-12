<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'فروشگاه من')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <link href="{{ asset('css/all.css') }}" rel="stylesheet">
    <style>
        body { font-family: IRANSansX;  }
        /* برای نمایش بهتر فونت فارسی، بهتر است یک فونت فارسی مانند وزیر یا ایران‌سنس اضافه کنید */

    </style>
</head>
<body class="font-sans antialiased text-gray-900 bg-gray-100">

    <nav class="bg-white shadow-md p-6">
        <div class="container mx-auto flex justify-between items-center">
            <a href="{{ route('shop.index') }}" class="text-2xl font-bold text-blue-600">فروشگاه من</a>
            <div class="text-right">
                <a href="{{ route('shop.index') }}" class="px-4 py-2 text-gray-600 hover:text-blue-600">فروشگاه</a>

                <a href="{{ route('cart.index') }}" class="relative text-gray-600 hover:text-blue-600">
                    <svg class="w-6 h-6 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    
                    <span class="absolute -top-2 -left-3 bg-red-500 text-white rounded-full text-xs w-5 h-5 flex items-center justify-center">
                        {{ \Darryldecode\Cart\Facades\CartFacade::getContent()->count() }}
                    </span>
                </a>

                <div class="flex-1 flex items-center justify-center px-2 lg:ml-6 lg:justify-end">
                    <div class="max-w-lg w-full lg:max-w-xs">
                        <form action="{{ route('search.index') }}" method="GET" class="relative">
                            <label for="search" class="sr-only">جستجو</label>
                            <div class="relative">
                                <button type="submit" class="absolute inset-y-0 left-0 pl-3 flex items-center justify-center">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <input id="search" name="q" 
                                       class="block w-full text-right bg-white border border-gray-300 rounded-md py-2 pl-10 pr-3 text-sm placeholder-gray-500 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" 
                                       placeholder="جستجو..." type="search"
                                       value="{{ request('q') ?? '' }}">
                            </div>
                        </form>
                    </div>
                </div>

                @guest
                    <a href="{{ route('login') }}" class="px-4 py-2 text-gray-600 hover:text-blue-600">ورود</a>
                @else
                    <a href="{{ route('home') }}" class="px-4 py-2 text-gray-600 hover:text-blue-600">حساب کاربری من</a>
                    <a href="{{ route('logout') }}"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                       class="px-4 py-2 text-gray-600 hover:text-blue-600">
                        خروج
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
                @endguest
            </div>
        </div>
    </nav>

    <main class="container mx-auto p-6 my-8">
        @yield('content')
    </main>

    <footer class="bg-white shadow-inner mt-12 p-6">
        <div class="container mx-auto text-center text-gray-600">
            &copy; {{ date('Y') }} فروشگاه من. تمام حقوق محفوظ است.
        </div>
    </footer>

</body>
</html>