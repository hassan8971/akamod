@extends('admin.layouts.app')

@section('title', 'افزودن محصول جدید')

@section('content')
<script defer src="http://localhost/alpine/dist/cdn.min.js"></script>

<div class="flex justify-between items-center mb-6" dir="rtl">
    <h1 class="text-3xl font-bold">افزودن محصول جدید</h1>
    <a href="{{ route('admin.products.index') }}" class="px-4 py-2 text-gray-600 rounded-lg hover:bg-gray-100">
        &larr; بازگشت به محصولات
    </a>
</div>

@if ($errors->any())
    <div class="bg-red-100 border-r-4 border-red-500 text-red-700 px-4 py-3 rounded relative mb-4" role="alert" dir="rtl">
        <strong class="font-bold">خطا!</strong>
        <span class="block sm:inline">مشکلاتی در ورودی شما وجود دارد.</span>
        <ul class="mt-3 list-disc list-inside text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form id="product-create-form" action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" 
      x-data="{ 
          variants: [], 
          sizes: {{ json_encode($sizes) }},
          colors: {{ json_encode($colors) }},
          buySources: {{ json_encode($buySources) }},
          embedVideos: ['']
      }">
    @csrf
    <div class="space-y-8" dir="rtl">
        
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4">۱. اطلاعات اصلی محصول</h2>
            @include('admin.products._form')
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4">۲. متغیرها (اختیاری)</h2>
            <p class="text-sm text-gray-600 mb-4">متغیرهایی مانند رنگ، اندازه، قیمت و موجودی را در اینجا اضافه کنید.</p>

            <template x-for="(variant, index) in variants" :key="index">
                <div class="grid grid-cols-1 md:grid-cols-7 gap-4 items-center mb-4 p-4 border rounded-lg">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">سایز</label>
                        <select :name="'variants[' + index + '][size]'" x-model="variant.size"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                            <option value="">انتخاب...</option>
                            <template x-for="size in sizes" :key="size.id">
                            <option :value="size.name" x-text="size.name"></option>
                        </template>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">رنگ</label>
                        <select :name="'variants[' + index + '][color]'" x-model="variant.color"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                            <option value="">انتخاب...</option>
                            <template x-for="color in colors" :key="color.id">
                                <option :value="color.name" x-text="color.name"></option>
                            </template>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">قیمت (تومان)</label>
                        <input type="number" :name="'variants[' + index + '][price]'" x-model="variant.price" placeholder="50000"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">قیمت با تخفیف</label>
                        <input type="number" :name="'variants[' + index + '][discount_price]'" x-model="variant.discount_price" placeholder="45000"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">قیمت خرید</label>
                        <input type="number" :name="'variants[' + index + '][buy_price]'" x-model="variant.buy_price" placeholder="30000"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">موجودی</label>
                        <input type="number" :name="'variants[' + index + '][stock]'" x-model="variant.stock" placeholder="100"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required>
                    </div>
                    <template x-for="(variant, index) in variants" :key="index">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">سورس خرید</label>
                            <select :name="'variants[' + index + '][buy_source_id]'" x-model="variant.buy_source_id"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                                <option value="">انتخاب...</option>
                                <template x-for="source in buySources" :key="source.id">
                                    <option :value="source.id" x-text="source.name"></option>
                                </template>
                            </select>
                        </div>
                    </template>

                    <div class="md:col-span-7 flex justify-end">
                        <button type="button" @click="variants.splice(index, 1)"
                                class="px-3 py-2 bg-red-500 text-white rounded-md hover:bg-red-600">
                            حذف
                        </button>
                    </div>
                </div>
            </template>

            <button type="button" @click="variants.push({ id: Date.now(), size: '', color: '', price: 0, discount_price: null, buy_price: null, stock: 0, buy_source: '' })"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                + افزودن ردیف متغیر
            </button>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4">۳. تصاویر (اختیاری)</h2>
            <div>
                <label for="images" class="block text-sm font-medium text-gray-700">انتخاب تصاویر (می‌توانید چند تصویر انتخاب کنید)</label>
                <input type="file" name="images[]" id="images" multiple 
                       class="mt-1 block w-full text-sm text-gray-500
                              file:mr-4 file:py-2 file:px-4 file:ml-4
                              file:rounded-full file:border-0
                              file:text-sm file:font-semibold
                              file:bg-blue-50 file:text-blue-700
                              hover:file:bg-blue-100">
            </div>
        </div>

         
        <div class="bg-white shadow-md rounded-lg p-6" 
             dir="rtl"
             x-data="{
                 isOpen: false,
                 searchQuery: '',
                 allProducts: {{ $allProducts }},
                 selectedProducts: [], // <-- FIX: Start with empty array
                 formId: 'product-create-form', // <-- FIX: Set form ID
                 
                 get filteredProducts() {
                     if (this.searchQuery === '') {
                         return this.allProducts.filter(p => !this.selectedProducts.includes(p.id)).slice(0, 50);
                     }
                     return this.allProducts.filter(p => 
                         p.name.toLowerCase().includes(this.searchQuery.toLowerCase()) && 
                         !this.selectedProducts.includes(p.id)
                     ).slice(0, 50);
                 },
                 
                 addProduct(productId) {
                     if (!this.selectedProducts.includes(productId)) {
                         this.selectedProducts.push(productId);
                     }
                     this.searchQuery = '';
                     this.isOpen = false;
                 },
                 
                 removeProduct(productId) {
                     this.selectedProducts = this.selectedProducts.filter(id => id !== productId);
                 },
                 
                 getProductName(id) {
                     // --- FIX: Simplified for create page ---
                     const product = this.allProducts.find(p => p.id === id);
                     return product ? product.name : 'محصول یافت نشد';
                 }
             }">
            
            <h2 class="text-xl font-semibold mb-4">۴. محصولات مرتبط (اختیاری)</h2>
            <p class="text-sm text-gray-500 mb-4">محصولاتی را که می‌خواهید در کنار این محصول نمایش داده شوند، انتخاب کنید.</p>
            
            <template x-for="productId in selectedProducts" :key="productId">
                <input type="hidden" name="related_product_ids[]" :value="productId" :form="formId">
            </template>
            
            <div class="flex flex-wrap gap-2 mb-4">
                <template x-for="productId in selectedProducts" :key="productId">
                    <span class="flex items-center bg-blue-100 text-blue-800 text-sm font-medium px-3 py-1 rounded-full">
                        <span x-text="getProductName(productId)"></span>
                        <button type="button" @click="removeProduct(productId)" class="mr-2 text-blue-600 hover:text-blue-800">
                            &times;
                        </button>
                    </span>
                </template>
                <p x-show="selectedProducts.length === 0" class="text-sm text-gray-500">
                    هنوز محصول مرتبطی انتخاب نشده است.
                </p>
            </div>

            <div class="relative">
                <label for="related_product_search" class="block text-sm font-medium text-gray-700">افزودن محصول</label>
                <input type="text"
                       id="related_product_search"
                       x-model="searchQuery"
                       @focus="isOpen = true"
                       @click.away="isOpen = false"
                       placeholder="جستجوی نام محصول..."
                       autocomplete="off"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                
                <div x-show="isOpen" 
                     x-transition
                     class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm"
                     style="display: none;">
                    
                    <ul class="py-1">
                        <template x-for="product in filteredProducts" :key="product.id">
                            <li @click="addProduct(product.id)"
                                class="text-gray-900 cursor-pointer select-none relative py-2 px-4 hover:bg-gray-100">
                                <span x-text="product.name"></span>
                            </li>
                        </template>
                        <li x-show="filteredProducts.length === 0 && searchQuery !== ''" class="py-2 px-4 text-gray-500">
                            محصولی با این نام یافت نشد.
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="bg-white shadow-md rounded-lg p-6" 
             dir="rtl"
             x-data="{
                 isOpen: false,
                 searchQuery: '',
                 allVideos: {{ $allVideos }},
                 selectedVideos: [], // <-- FIX: Start with empty array
                 formId: 'product-create-form', // <-- FIX: Set form ID

                 get filteredVideos() {
                     if (this.searchQuery === '') {
                         return this.allVideos.filter(v => !this.selectedVideos.includes(v.id)).slice(0, 50);
                     }
                     return this.allVideos.filter(v => 
                         v.name.toLowerCase().includes(this.searchQuery.toLowerCase()) && 
                         !this.selectedVideos.includes(v.id)
                     ).slice(0, 50);
                 },
                 
                 addVideo(videoId) {
                     if (!this.selectedVideos.includes(videoId)) {
                         this.selectedVideos.push(videoId);
                     }
                     this.searchQuery = '';
                     this.isOpen = false;
                 },
                 
                 removeVideo(videoId) {
                     this.selectedVideos = this.selectedVideos.filter(id => id !== videoId);
                 },
                 
                 getVideoName(id) {
                     // --- FIX: Simplified for create page ---
                     const video = this.allVideos.find(v => v.id === id);
                     return video ? video.name : 'ویدیو یافت نشد';
                 }
             }">
            
            <h2 class="text-xl font-semibold mb-4">۵. ویدیوهای محصول (اختیاری)</h2>
            <p class="text-sm text-gray-500 mb-4">ویدیوها را از کتابخانه انتخاب کنید.</p>
            
            <template x-for="videoId in selectedVideos" :key="videoId">
                <input type="hidden" name="video_ids[]" :value="videoId" :form="formId">
            </template>
            
            <div class="flex flex-wrap gap-2 mb-4">
                <template x-for="videoId in selectedVideos" :key="videoId">
                    <span class="flex items-center bg-purple-100 text-purple-800 text-sm font-medium px-3 py-1 rounded-full">
                        <span x-text="getVideoName(videoId)"></span>
                        <button type="button" @click="removeVideo(videoId)" class="mr-2 text-purple-600 hover:text-purple-800">
                            &times;
                        </button>
                    </span>
                </template>
                <p x-show="selectedVideos.length === 0" class="text-sm text-gray-500">
                    هنوز ویدیویی برای این محصول انتخاب نشده است.
                </p>
            </div>

            <div class="relative">
                <label for="video_search" class="block text-sm font-medium text-gray-700">افزودن ویدیو از کتابخانه</label>
                <input type="text"
                       id="video_search"
                       x-model="searchQuery"
                       @focus="isOpen = true"
                       @click.away="isOpen = false"
                       placeholder="جستجوی نام ویدیو..."
                       autocomplete="off"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                
                <div x-show="isOpen" 
                     x-transition
                     class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm"
                     style="display: none;">
                    
                    <ul class="py-1">
                        <template x-for="video in filteredVideos" :key="video.id">
                            <li @click="addVideo(video.id)"
                                class="text-gray-900 cursor-pointer select-none relative py-2 px-4 hover:bg-gray-100">
                                <span x-text="video.name"></span>
                            </li>
                        </template>
                        <li x-show="filteredVideos.length === 0 && searchQuery !== ''" class="py-2 px-4 text-gray-500">
                            ویدیویی با این نام یافت نشد.
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="bg-white shadow-md rounded-lg p-6" 
     dir="rtl"
     x-data="{
         isOpen: false,
         searchQuery: '',
         allPackagingOptions: {{ $allPackagingOptions }},
         selectedPackagingOptions: [], // شروع با آرایه خالی
         formId: 'product-create-form',

         get filteredPackagingOptions() {
             if (this.searchQuery === '') {
                 return this.allPackagingOptions.filter(p => !this.selectedPackagingOptions.includes(p.id)).slice(0, 50);
             }
             return this.allPackagingOptions.filter(p => 
                 p.name.toLowerCase().includes(this.searchQuery.toLowerCase()) && 
                 !this.selectedPackagingOptions.includes(p.id)
             ).slice(0, 50);
         },
         
         addPackagingOption(optionId) {
             if (!this.selectedPackagingOptions.includes(optionId)) {
                 this.selectedPackagingOptions.push(optionId);
             }
             this.searchQuery = '';
             this.isOpen = false;
         },
         
         removePackagingOption(optionId) {
             this.selectedPackagingOptions = this.selectedPackagingOptions.filter(id => id !== optionId);
         },
         
         getPackagingOptionName(id) {
             const option = this.allPackagingOptions.find(p => p.id === id);
             return option ? option.name : 'بسته‌بندی یافت نشد';
         }
     }">
    
    <h2 class="text-xl font-semibold mb-4">۶. انواع بسته‌بندی (اختیاری)</h2>
    <p class="text-sm text-gray-500 mb-4">انواع بسته‌بندی قابل انتخاب برای این محصول را مشخص کنید.</p>
    
    <template x-for="optionId in selectedPackagingOptions" :key="optionId">
        <input type="hidden" name="packaging_option_ids[]" :value="optionId" :form="formId">
    </template>
    
    <div class="flex flex-wrap gap-2 mb-4">
        <template x-for="optionId in selectedPackagingOptions" :key="optionId">
            <span class="flex items-center bg-green-100 text-green-800 text-sm font-medium px-3 py-1 rounded-full">
                <span x-text="getPackagingOptionName(optionId)"></span>
                <button type="button" @click="removePackagingOption(optionId)" class="mr-2 text-green-600 hover:text-green-800">
                    &times;
                </button>
            </span>
        </template>
        <p x-show="selectedPackagingOptions.length === 0" class="text-sm text-gray-500">
            هنوز بسته‌بندی برای این محصول انتخاب نشده است. (بسته‌بندی‌های پیش‌فرض نمایش داده می‌شود)
        </p>
    </div>

    <div class="relative">
        <label for="packaging_search" class="block text-sm font-medium text-gray-700">افزودن بسته‌بندی</label>
        <input type="text"
               id="packaging_search"
               x-model="searchQuery"
               @focus="isOpen = true"
               @click.away="isOpen = false"
               placeholder="جستجوی نام بسته‌بندی..."
               autocomplete="off"
               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        
        <div x-show="isOpen" 
             x-transition
             class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm"
             style="display: none;">
            
            <ul class="py-1">
                <template x-for="option in filteredPackagingOptions" :key="option.id">
                    <li @click="addPackagingOption(option.id)"
                        class="text-gray-900 cursor-pointer select-none relative py-2 px-4 hover:bg-gray-100">
                        <span x-text="option.name"></span>
                    </li>
                </template>
                <li x-show="filteredPackagingOptions.length === 0 && searchQuery !== ''" class="py-2 px-4 text-gray-500">
                    بسته‌بندی با این نام یافت نشد.
                </li>
            </ul>
        </div>
    </div>
</div>
        <div class="flex justify-end mt-6">
            <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-lg font-semibold">
                ایجاد محصول
            </button>
        </div>
    </div>
</form>
@endsection