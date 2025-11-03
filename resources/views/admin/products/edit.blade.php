@extends('admin.layouts.app')

@section('title', 'Edit Product: ' . $product->name)

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Edit Product</h1>
        <a href="{{ route('admin.products.index') }}" class="px-4 py-2 text-gray-600 rounded-lg hover:bg-gray-100">
            &larr; Back to Products
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Whoops!</strong>
            <span class="block sm:inline">There were some problems with your input. (Did you check the variants section?)</span>
            <ul class="mt-3 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4">Product Details</h2>
        <form action="{{ route('admin.products.update', $product) }}" method="POST">
            @csrf
            @method('PUT')
            
            @include('admin.products._form')

            <div class="flex justify-end mt-6">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4">Manage Variants</h2>

        <div class="mb-6">
            <h3 class="text-lg font-medium mb-2">Existing Variants</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold uppercase">Name</th>
                            <th class="px-4 py-2 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold uppercase">Price</th>
                            <th class="px-4 py-2 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold uppercase">Stock</th>
                            <th class="px-4 py-2 border-b-2 border-gray-200 bg-gray-100"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($product->variants as $variant)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 border-b border-gray-200">{{ $variant->name }} ({{ $variant->size }}, {{ $variant->color }})</td>
                                <td class="px-4 py-3 border-b border-gray-200">${{ number_format($variant->price / 100, 2) }}</td>
                                <td class="px-4 py-3 border-b border-gray-200">{{ $variant->stock }}</td>
                                <td class="px-4 py-3 border-b border-gray-200 text-right">
                                    <a href="{{ route('admin.variants.edit', $variant) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                                    <form action="{{ route('admin.variants.destroy', $variant) }}" method="POST" class="inline-block ml-4" onsubmit="return confirm('Delete this variant?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-3 border-b border-gray-200 text-center text-gray-500">
                                    No variants created yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <hr class="my-6">
        <h3 class="text-lg font-medium mb-2">Add New Variant</h3>
        <form action="{{ route('admin.products.variants.store', $product) }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
                <div>
                    <label for="variant_name" class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" name="name" id="variant_name" placeholder="e.g., Small, Red"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required>
                </div>
                <div>
                    <label for="variant_size" class="block text-sm font-medium text-gray-700">Size</label>
                    <input type="text" name="size" id="variant_size" placeholder="e.g., Small"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="variant_color" class="block text-sm font-medium text-gray-700">Color</label>
                    <input type="text" name="color" id="variant_color" placeholder="e.g., Red"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="variant_price" class="block text-sm font-medium text-gray-700">Price ($)</label>
                    <input type="number" name="price" id="variant_price" placeholder="e.g., 19.99"
                           step="0.01" min="0" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required>
                </div>
                <div>
                    <label for="variant_stock" class="block text-sm font-medium text-gray-700">Stock</label>
                    <input type="number" name="stock" id="variant_stock" placeholder="e.g., 100"
                           min="0" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required>
                </div>
            </div>
            <div class="flex justify-end mt-4">
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    + Add Variant
                </button>
            </div>
        </form>
    </div>
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Manage Images</h2>
        <div class="mb-6">
            <h3 class="text-lg font-medium mb-2">Existing Images</h3>
            @if ($product->images->isEmpty())
                <p class="text-gray-500">No images uploaded yet.</p>
            @else
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @foreach ($product->images as $image)
                        <div class="relative border rounded-lg overflow-hidden shadow">
                            <img src="{{ Storage::url($image->path) }}" alt="{{ $image->alt_text ?? 'Product Image' }}" class="w-full h-32 object-cover">
                            <div class="absolute top-1 right-1">
                                <form action="{{ route('admin.images.destroy', $image) }}" method="POST" onsubmit="return confirm('Delete this image?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 bg-red-600 text-white rounded-full text-xs leading-none hover:bg-red-700">
                                        &times;
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <hr class="my-6">
        <h3 class="text-lg font-medium mb-2">Upload New Images</h3>
        <form action="{{ route('admin.products.images.store', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div>
                <label for="images" class="block text-sm font-medium text-gray-700">Choose images (you can select multiple)</label>
                <input type="file" name="images[]" id="images" multiple 
                       class="mt-1 block w-full text-sm text-gray-500
                              file:mr-4 file:py-2 file:px-4
                              file:rounded-full file:border-0
                              file:text-sm file:font-semibold
                              file:bg-blue-50 file:text-blue-700
                              hover:file:bg-blue-100" required>
            </div>
            
            <div class="flex justify-end mt-4">
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Upload Images
                </button>
            </div>
        </form>
    </div>
    @endsection