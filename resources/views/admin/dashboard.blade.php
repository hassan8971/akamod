{{-- Extends the new admin layout --}}
@extends('admin.layouts.app')

{{-- Sets the title for the page --}}
@section('title', 'Dashboard')

{{-- Main content section --}}
@section('content')
    <div class="bg-white shadow-md rounded-lg p-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-4">Welcome to your dashboard!</h1>
        <p class="text-gray-600">From here you will be able to manage your store's products, categories, orders, and more.</p>

        <!-- You can add stats/links here later -->
        <div class="mt-8 border-t pt-6">
            <h3 class="text-xl font-semibold text-gray-900">Next Steps</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-4">
                
                <!-- Category Card -->
                <a href="{{ route('admin.categories.index') }}" class="block p-6 bg-blue-50 rounded-lg shadow-sm hover:shadow-md transition-shadow">
                    <h4 class="text-lg font-semibold text-blue-800">Manage Categories</h4>
                    <p class="text-sm text-blue-600 mt-1">Add, edit, or delete product categories.</p>
                </a>
                
                <!-- Product Card -->
                <a href="{{ route('admin.products.index') }}" class="block p-6 bg-green-50 rounded-lg shadow-sm hover:shadow-md transition-shadow">
                    <h4 class="text-lg font-semibold text-green-800">Manage Products</h4>
                    <p class="text-sm text-green-600 mt-1">Create new products and manage their details.</p>
                </a>
                
                <!-- Orders Card (Example) -->
                <div class="block p-6 bg-gray-50 rounded-lg shadow-sm">
                    <h4 class="text-lg font-semibold text-gray-800">View Orders</h4>
                    <p class="text-sm text-gray-600 mt-1">(Coming soon) See and fulfill customer orders.</p>
                </div>

            </div>
        </div>
    </div>
@endsection

