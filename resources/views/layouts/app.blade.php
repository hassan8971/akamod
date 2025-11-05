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