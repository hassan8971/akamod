{{-- Extends the admin layout --}}
@extends('admin.layouts.app')

{{-- Sets the title for the page --}}
@section('title', 'Dashboard')

{{-- Main content section --}}
@section('content')
    <p>Welcome to your admin dashboard!</p>
    <p>From here you will be able to manage your store's products, categories, orders, and more.</p>

    <!-- You can add stats/links here later -->
    <div class="mt-6">
        <h3 class="text-lg font-medium text-gray-900">Next Steps</h3>
        <ul class="list-disc list-inside mt-2 text-gray-700">
            <li>Manage Categories</li>
            <li>Manage Products</li>
            <li>View Orders</li>
        </ul>
    </div>
@endsection
