@if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 text-right" role="alert">
        <strong class="font-bold">خطا!</strong>
        <span class="block sm:inline">ورودی شما دارای چند مشکل است.</span>
        <ul class="mt-3 list-disc list-inside text-sm text-right">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="md:col-span-2 space-y-6">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 text-right">نام محصول</label>
            <input type="text" name="name" id="name" value="{{ old('name', $product->name ?? '') }}" 
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-right" required>
        </div>
        
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 text-right">توضیحات</label>
            <textarea name="description" id="description" rows="8" 
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-right">{{ old('description', $product->description ?? '') }}</textarea>
        </div>

        <div>
            <label for="boxing_type" class="block text-sm font-medium text-gray-700 text-right">نوع بسته‌بندی (مثلاً: «تکی»، «بسته ۶ عددی»)</label>
            <input type="text" name="boxing_type" id="boxing_type" value="{{ old('boxing_type', $product->boxing_type ?? '') }}" 
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-right">
        </div>
    </div>

    <div class="md:col-span-1 space-y-6">
        <div>
            <label for="category_id" class="block text-sm font-medium text-gray-700 text-right">دسته بندی</label>
            <select name="category_id" id="category_id" 
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-right" required>
                <option value="">یک دسته بندی انتخاب کنید</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" 
                        @isset($product) {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }} @endisset>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div>
            <label for="product_id" class="block text-sm font-medium text-gray-700 text-right">شناسه محصول / SKU</label>
            <input type="text" name="product_id" id="product_id" value="{{ old('product_id', $product->product_id ?? '') }}" 
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-right">
        </div>

        <div class="flex items-center text-right">
            <input type="hidden" name="is_visible" value="0"> <input type="checkbox" name="is_visible" id="is_visible" value="1" 
                   @isset($product) {{ old('is_visible', $product->is_visible) ? 'checked' : '' }} @else checked @endisset
                   class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
            <label for="is_visible" class="mr-2 block text-sm font-medium text-gray-900">قابل مشاهده در فروشگاه</label>
        </div>
    </div>
</div>