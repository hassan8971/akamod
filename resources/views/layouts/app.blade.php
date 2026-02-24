<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'فروشگاه من')</title>
    <script src="{{ asset('js/tailwindcss.js') }}"></script>
    <style>
        body { font-family: 'Inter', sans-serif; /* فونت فارسی شما */ }
        /* This is for Alpine.js dropdowns */
        .group:hover .group-hover\:block { display: block; }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 text-gray-900">

    <nav class="bg-white shadow-md" x-data="{ open: false }">
        <div class="container mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <a href="{{ route('home') }}" class="flex-shrink-0 flex items-center">
                        <span class="text-xl font-bold text-blue-600">فروشگاه من</span>
                    </a>
                    
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8 sm:mr-6">
                        @foreach ($headerMenuItems as $item)
                            @if ($item->children->isEmpty())
                                <a href="{{ url($item->link_url) }}" 
                                   class="inline-flex items-center px-1 pt-1 border-b-2 
                                          {{ request()->is(ltrim($item->link_url, '/')) ? 'border-blue-500' : 'border-transparent' }} 
                                          text-sm font-medium hover:border-gray-300">
                                    {{ $item->name }}
                                </a>
                            @else
                                <div class="relative group" x-data="{ open: false }">
                                    <button @click="open = !open" @click.away="open = false" 
                                            class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium hover:border-gray-300 h-full">
                                        <span>{{ $item->name }}</span>
                                        <svg class="w-5 h-5 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    <div x-show="open" 
                                         x-transition
                                         class="absolute z-20 -mr-4 mt-0 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 group-hover:block"
                                         style="display: none;">
                                        <div class="py-1">
                                            @foreach ($item->children as $child)
                                                <a href="{{ url($child->link_url) }}" 
                                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                   {{ $child->name }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    </div>

                </div>
        </div>

        <div class="sm:hidden" id="mobile-menu" x-show="open" @click.away="open = false" style="display: none;">
            <div class="pt-2 pb-3 space-y-1">
                @foreach ($headerMenuItems as $item)
                    @if ($item->children->isEmpty())
                        <a href="{{ url($item->link_url) }}" 
                           class="block pl-3 pr-4 py-2 border-r-4 
                                  {{ request()->is(ltrim($item->link_url, '/')) ? 'border-blue-500 bg-blue-50' : 'border-transparent' }} 
                                  text-base font-medium hover:bg-gray-50">
                           {{ $item->name }}
                        </a>
                    @else
                        <span class="block pl-3 pr-4 py-2 text-base font-medium text-gray-500">{{ $item->name }} (دارای زیرمنو)</span>
                    @endif
                @endforeach
                
                </div>
            </div>
        </nav>

    <main>
        @yield('content')
    </main>

    <footer class="bg-white mt-12 border-t border-gray-200">
        <div class="container mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
            <div class="flex justify-center space-x-6 space-x-reverse mb-8">
                @foreach ($footerMenuItems as $item)
                    <a href="{{ url($item->link_url) }}" class="text-sm text-gray-500 hover:text-gray-900">
                        {{ $item->name }}
                    </a>
                @endforeach
            </div>
            <p class="text-center text-sm text-gray-500">
                &copy; {{ date('Y') }} فروشگاه من. تمام حقوق محفوظ است.
            </p>
        </div>
    </footer>

</body>
</html>