{{-- This layout extends the main public-facing layout --}}
@extends('layouts.app')

{{-- Sets the page title --}}
@section('title', 'حساب کاربری من')

{{-- Main content section --}}
@section('content')
<div class="container mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
        
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        
        <!-- Sidebar Navigation -->
        <aside class="md:col-span-1">
            <div class="bg-white shadow-md rounded-lg p-4">
                <h2 class="text-lg font-semibold mb-4">حساب کاربری</h2>
                <nav class="space-y-2">
                    <a href="{{ route('user.index') }}" 
                       class="flex items-center px-3 py-2 text-base font-medium rounded-md text-gray-700 hover:bg-gray-100
                       @if(request()->routeIs('user.index') || request()->routeIs('user.order.show')) bg-blue-600 text-white @endif">
                        <!-- Icon (e.g., Heroicons) -->
                        <svg class="h-6 w-6 mr-2 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0z" /></svg>
                        <span>تاریخچه سفارشات</span>
                    </a>
                    
                    <a href="{{ route('user.profile') }}" 
                       class="flex items-center px-3 py-2 text-base font-medium rounded-md text-gray-700 hover:bg-gray-100
                       @if(request()->routeIs('user.profile')) bg-blue-600 text-white @endif">
                        <svg class="h-6 w-6 mr-2 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0zM4.501 20.118a7.5 7.5 0 0 1 14.998 0A1.75 1.75 0 0 1 18 21.868H6a1.75 1.75 0 0 1-1.501-1.75z" /></svg>
                        <span>مدیریت پروفایل</span>
                    </a>
                    
                    <a href="{{ route('user.addresses.index') }}" 
                       class="flex items-center px-3 py-2 text-base font-medium rounded-md text-gray-700 hover:bg-gray-100
                       @if(request()->routeIs('user.addresses.*')) bg-blue-600 text-white @endif">
                       <svg class="h-6 w-6 mr-2 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0z" /></svg>
                        <span>دفترچه آدرس</span>
                    </a>
                    
                    <hr class="my-2">
                    
                    <!-- Logout Form -->
                    <form action="{{ route('logout') }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit" 
                                class="w-full flex items-center px-3 py-2 text-base font-medium rounded-md text-red-600 hover:bg-red-50">
                            <svg class="h-6 w-6 mr-2 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" /></svg>
                            <span>خروج از حساب</span>
                        </button>
                    </form>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="md:col-span-3">
            <!-- Session Messages -->
            @if(session('success'))
                <div class="bg-green-100 border-r-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg" role="alert">
                    <p class="font-bold">موفقیت</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border-r-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg" role="alert">
                    <p class="font-bold">خطا</p>
                    <p>{{ session('error') }}</p>
                </div>
            @endif
            
            <!-- This is where the content from index.blade.php or profile.blade.php will go -->
            @yield('panel-content')
        </main>

    </div>
</div>
@endsection

