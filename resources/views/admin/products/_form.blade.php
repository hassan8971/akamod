<!-- This file is "included" by create.blade.php and edit.blade.php -->
<!-- This prevents us from writing the same form code twice! -->

@if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <strong class="font-bold">Whoops!</strong>
        <span class="block sm:inline">There were some problems with your input.</span>
        <ul class="mt-3 list-disc list-inside text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Main Content Column -->
    <div class="md:col-span-2 space-y-6">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Product Name</label>
            <input type="text" name="name" id="name" value="{{ old('name', $product->name ?? '') }}" 
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
        </div>
        
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
            <textarea name="description" id="description" rows="8" 
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old('description', $product->description ?? '') }}</textarea>
        </div>

        <div>
            <label for="boxing_type" class="block text-sm font-medium text-gray-700">Boxing Type (e.g., "Single", "Box of 6")</label>
            <input type="text" name="boxing_type" id="boxing_type" value="{{ old('boxing_type', $product->boxing_type ?? '') }}" 
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>
    </div>

    <!-- Sidebar Column -->
    <div class="md:col-span-1 space-y-6">
        <div>
            <label for="category_id" class="block text-sm font-medium text-gray-700">Category</label>
            <select name="category_id" id="category_id" 
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                <option value="">Select a category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" 
                        @isset($product) {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }} @endisset>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div>
            <label for="product_id" class="block text-sm font-medium text-gray-700">Product ID / SKU</label>
            <input type="text" name="product_id" id="product_id" value="{{ old('product_id', $product->product_id ?? '') }}" 
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div class="flex items-center">
            <input type="hidden" name="is_visible" value="0"> <!-- Default value if checkbox is unchecked -->
            <input type="checkbox" name="is_visible" id="is_visible" value="1" 
                   @isset($product) {{ old('is_visible', $product->is_visible) ? 'checked' : '' }} @else checked @endisset
                   class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
            <label for="is_visible" class="ml-2 block text-sm font-medium text-gray-900">Visible on Storefront</label>
        </div>
    </div>
</div>
