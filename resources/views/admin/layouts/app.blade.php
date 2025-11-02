<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-g">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - {{ config('app.name', 'Laravel') }}</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Simple style for the layout */
        .admin-body {
            font-family: 'Inter', sans-serif;
        }
        /* Add Inter font */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
    </style>
</head>
<body class="bg-gray-100 admin-body">
    <div id="app" class="min-h-screen flex flex-col">
        <!-- Top Navigation Bar -->
        <nav class="bg-white shadow-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Logo/Title -->
                    <div class="flex-shrink-0 flex items-center">
                        <h1 class="text-xl font-bold text-gray-800">Admin Panel</h1>
                    </div>
                    
                    <!-- Right Side Navigation -->
                    <div class="flex items-center">
                        <!-- Admin User Name -->
                        <span class="mr-4 text-gray-600">
                            Welcome, {{ Auth::guard('admin')->user()->name ?? 'Admin' }}
                        </span>

                        <!-- Logout Button -->
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content Area -->
        <main class="flex-grow p-6">
            <div class="max-w-7xl mx-auto">
                <!-- Page Title -->
                <h2 class="text-2xl font-semibold text-gray-900 mb-4">
                    @yield('title')
                </h2>

                <!-- Page Content -->
                <div class="bg-white shadow rounded-lg p-6">
                    @yield('content')
                </div>
            </div>
        </main>
    </div>
</body>
</html>
