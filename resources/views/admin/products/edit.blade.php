@extends('admin.layouts.app')

@section('title', 'ویرایش محصول: ' . $product->name)

@section('content')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <div class="flex justify-between items-center mb-6" dir="rtl">
        <h1 class="text-3xl font-bold">ویرایش محصول: {{ $product->name }}</h1>
        <a href="{{ route('admin.products.index') }}" class="px-4 py-2 text-gray-600 rounded-lg hover:bg-gray-100">
            &larr; بازگشت به محصولات
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert" dir="rtl">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert" dir="rtl">
            <strong class="font-bold">خطا!</strong>
            <span class="block sm:inline">مشکلاتی در ورودی شما وجود دارد.</span>
            <ul class="mt-3 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg p-6 mb-8" dir="rtl">
        <h2 class="text-xl font-semibold mb-4">اطلاعات محصول</h2>
        <form id="product-update-form" action="{{ route('admin.products.update', $product) }}" method="POST">
            @csrf
            @method('PUT')
            
            @include('admin.products._form')

            <!-- Start of Manage Related Products -->
    <div class="bg-white rounded-lg p-6 mb-7" 
         dir="rtl"
         x-data="{
             isOpen: false,
             searchQuery: '',
             allProducts: {{ $allProducts ?? '[]' }},
             selectedProducts: {{ $product->relatedProducts->pluck('id') ?? '[]' }},
             
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
                 const all = {{ $allProducts ?? '[]' }};
                 const related = {{ $product->relatedProducts->keyBy('id') ?? '[]' }};
                 const product = all.find(p => p.id === id) || related[id];
                 return product ? product.name : 'محصول یافت نشد';
             }
         }">
        
        <h2 class="text-xl font-semibold mb-4">محصولات مرتبط</h2>
        <p class="text-sm text-gray-500 mb-4">محصولاتی را که می‌خواهید در کنار این محصول نمایش داده شوند، انتخاب کنید.</p>
        
        <template x-for="productId in selectedProducts" :key="productId">
            <input type="hidden" name="related_product_ids[]" :value="productId" form="product-update-form">
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
                    <li x-show="filteredProducts.length === 0 && searchQuery === ''" class="py-2 px-4 text-gray-500">
                        تمام محصولات انتخاب شده‌اند یا محصول دیگری برای نمایش وجود ندارد.
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!-- End of Manage Related Products -->
            
            <div class="flex justify-end mt-6">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    ذخیره تغییرات
                </button>
            </div>
        </form>
    </div>

    <!-- Manage Variants -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-8" dir="rtl">
        <h2 class="text-xl font-semibold mb-4">مدیریت متغیرها (Variants)</h2>

        <!-- List Existing Variants -->
        <div class="mb-6">
            <h3 class="text-lg font-medium mb-2">متغیرهای موجود</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold uppercase">سایز</th>
                            <th class="px-4 py-2 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold uppercase">رنگ</th>
                            <th class="px-4 py-2 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold uppercase">قیمت (تومان)</th>
                            <th class="px-4 py-2 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold uppercase">قیمت با تخفیف</th>
                            <th class="px-4 py-2 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold uppercase">قیمت خرید </th>
                            <th class="px-4 py-2 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold uppercase">موجودی</th>
                            <th class="px-4 py-2 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold uppercase">سورس خرید</th>
                            <th class="px-4 py-2 border-b-2 border-gray-200 bg-gray-100">عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($product->variants as $variant)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 border-b border-gray-200">{{ $variant->size }}</td>
                                <td class="px-4 py-3 border-b border-gray-200">{{ $variant->color }}</td>
                                <td class="px-4 py-3 border-b border-gray-200">{{ number_format($variant->price) }}</td>
                                <td class="px-4 py-3 border-b border-gray-200">{{ number_format($variant->discount_price) }}</td>
                                <td class="px-4 py-3 border-b border-gray-200">{{ number_format($variant->buy_price) }}</td>
                                <td class="px-4 py-3 border-b border-gray-200">{{ $variant->stock }}</td>
                                <td class="px-4 py-3 border-b border-gray-200">
                                    {{ $variant->buySource->name ?? '---' }}
                                </td>
                                <td class="px-4 py-3 border-b border-gray-200 text-left">
                                    <a href="{{ route('admin.variants.edit', $variant) }}" class="text-blue-600 hover:text-blue-900">ویرایش</a>
                                    <form action="{{ route('admin.variants.destroy', $variant) }}" method="POST" class="inline-block mr-4" onsubmit="return confirm('آیا از حذف این متغیر مطمئن هستید؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">حذف</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-3 border-b border-gray-200 text-center text-gray-500">
                                    هنوز متغیری ایجاد نشده است.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add New Variant Form -->
        <hr class="my-6">
        <div class="flex justify-between items-center mb-2">
            <h3 class="text-lg font-medium">افزودن متغیر جدید</h3>
            <button type="button" onclick="window.dispatchEvent(new CustomEvent('clear-variant-form'))" 
                    class="text-xs text-gray-500 hover:text-red-500 underline">
                پاک کردن فرم
            </button>
        </div>

        <form action="{{ route('admin.products.variants.store', $product) }}" method="POST" id="add-variant-form"
              x-data="{
                  // 1. تعریف متغیرها با اولویت: 1. مقدار خطا (old) 2. حافظه مرورگر 3. خالی
                  size: '{{ old('size') }}' || localStorage.getItem('v_size_{{ $product->id }}') || '',
                  color: '{{ old('color') }}' || localStorage.getItem('v_color_{{ $product->id }}') || '',
                  price: '{{ old('price') }}' || localStorage.getItem('v_price_{{ $product->id }}') || '',
                  discount_price: '{{ old('discount_price') }}' || localStorage.getItem('v_discount_{{ $product->id }}') || '',
                  buy_price: '{{ old('buy_price') }}' || localStorage.getItem('v_buy_{{ $product->id }}') || '',
                  stock: '{{ old('stock') }}' || localStorage.getItem('v_stock_{{ $product->id }}') || '',
                  buy_source_id: '{{ old('buy_source_id') }}' || localStorage.getItem('v_source_{{ $product->id }}') || '',

                  init() {
                      // 2. هر تغییری دادی، سریع تو حافظه ذخیره کن
                      this.$watch('size', val => localStorage.setItem('v_size_{{ $product->id }}', val));
                      this.$watch('color', val => localStorage.setItem('v_color_{{ $product->id }}', val));
                      this.$watch('price', val => localStorage.setItem('v_price_{{ $product->id }}', val));
                      this.$watch('discount_price', val => localStorage.setItem('v_discount_{{ $product->id }}', val));
                      this.$watch('buy_price', val => localStorage.setItem('v_buy_{{ $product->id }}', val));
                      this.$watch('stock', val => localStorage.setItem('v_stock_{{ $product->id }}', val));
                      this.$watch('buy_source_id', val => localStorage.setItem('v_source_{{ $product->id }}', val));

                      // شنیدن رویداد پاکسازی
                      window.addEventListener('clear-variant-form', () => {
                          this.size = ''; this.color = ''; this.price = ''; 
                          this.discount_price = ''; this.buy_price = ''; 
                          this.stock = ''; this.buy_source_id = '';
                          // پاک کردن از حافظه
                          const keys = ['size', 'color', 'price', 'discount', 'buy', 'stock', 'source'];
                          keys.forEach(k => localStorage.removeItem('v_' + k + '_{{ $product->id }}'));
                      });
                  }
              }"
        >
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
                <div>
                    <label for="variant_size" class="block text-sm font-medium text-gray-700">سایز</label>
                    <select name="size" id="variant_size" x-model="size"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                        <option value="">انتخاب کنید...</option>
                        @foreach ($sizes as $size)
                            <option value="{{ $size->name }}">{{ $size->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="variant_color" class="block text-sm font-medium text-gray-700">رنگ</label>
                    <select name="color" id="color" x-model="color"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                        <option value="">انتخاب کنید...</option>
                        @foreach ($colors as $color)
                            <option value="{{ $color->name }}">
                                {{ $color->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="variant_price" class="block text-sm font-medium text-gray-700">قیمت (تومان)</label>
                    <input type="number" name="price" id="variant_price" x-model="price" placeholder="مثال: 50000"
                           step="1" min="0" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required>
                </div>
                <div>
                    <label for="discount_price" class="block text-sm font-medium text-gray-700">قیمت با تخفیف (تومان)</label>
                    <input type="number" name="discount_price" id="discount_price" x-model="discount_price" placeholder="مثال: 50000"
                           step="1" min="0" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required>
                        @if(isset($avg_sale_price) && $avg_sale_price > 0)
                        <p class="mt-1 text-xs text-red-600">
                            (میانگین فروش: {{ number_format($avg_sale_price) }} تومان)
                        </p>
                        @endif
                </div>
                <div>
                    <label for="buy_price" class="block text-sm font-medium text-gray-700">قیمت خرید</label>
                    <input type="number" name="buy_price" id="buy_price" x-model="buy_price" placeholder="مثال: 50000"
                           step="1" min="0" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required>
                    @if(isset($avg_buy_price) && $avg_buy_price > 0)
                        <p class="mt-1 text-xs text-red-600">
                            (میانگین خرید: {{ number_format($avg_buy_price) }} تومان)
                        </p>
                    @endif
                </div>
                <div>
                    <label for="variant_stock" class="block text-sm font-medium text-gray-700">موجودی انبار</label>
                    <input type="number" name="stock" id="variant_stock" x-model="stock" placeholder="مثال: 100"
                           min="0" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required>
                </div>
                <div>
                    <label for="buy_source" class="block text-sm font-medium text-gray-700">سورس خرید</label>
                    <select name="buy_source_id" id="buy_source" x-model="buy_source_id"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                        <option value="">انتخاب کنید...</option>
                        @foreach ($buySources as $source)
                            <option value="{{ $source->id }}">{{ $source->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex justify-end mt-4">
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    + افزودن متغیر
                </button>
            </div>
        </form>
    </div>

    

    <!-- Manage Images -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-8" dir="rtl">
        <h2 class="text-xl font-semibold mb-4">مدیریت تصاویر</h2>
        <!-- List Existing Images -->
        <div class="mb-6">
            <h3 class="text-lg font-medium mb-2">تصاویر موجود</h3>
            <!-- FIXED: Check for null as well -->
            @if (is_null($product->images) || $product->images->isEmpty())
                <p class="text-gray-500">هنوز تصویری آپلود نشده است.</p>
            @else
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @foreach ($product->images as $image)
                        <div class="relative border rounded-lg overflow-hidden shadow">
                            <img src="{{ Storage::url($image->path) }}" alt="{{ $image->alt_text ?? 'تصویر محصول' }}" class="w-full h-32 object-cover">
                            <div class="absolute top-1 left-1">
                                <form action="{{ route('admin.images.destroy', $image) }}" method="POST" onsubmit="return confirm('آیا از حذف این تصویر مطمئن هستید؟');">
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
        <!-- Add New Images Form -->
        <hr class="my-6">
        <h3 class="text-lg font-medium mb-2">آپلود تصاویر جدید</h3>
        <form action="{{ route('admin.products.images.store', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div>
                <label for="images" class="block text-sm font-medium text-gray-700">انتخاب تصاویر (می‌توانید چند تصویر انتخاب کنید)</label>
                <input type="file" name="images[]" id="images" multiple 
                       class="mt-1 block w-full text-sm text-gray-500
                              file:mr-4 file:py-2 file:px-4 file:ml-4
                              file:rounded-full file:border-0
                              file:text-sm file:font-semibold
                              file:bg-blue-50 file:text-blue-700
                              hover:file:bg-blue-100" required>
            </div>
            <div class="flex justify-end mt-4">
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    آپلود تصاویر
                </button>
            </div>
        </form>
    </div>

    <!-- Hover image start -->

    <div class="bg-white shadow-md rounded-lg p-6 mb-8" dir="rtl"
     x-data="{
        isOpen: false,
        // Get the initial ID and URL from the server-side product object
        selectedId: '{{ $product->hover_image_id }}',
        selectedUrl: '{{ $product->hoverImage ? Storage::url($product->hoverImage->path) : '' }}',

        openModal() {
            this.isOpen = true;
            // Lock body scroll
            document.body.style.overflow = 'hidden';
        },

        closeModal() {
            this.isOpen = false;
            // Unlock body scroll
            document.body.style.overflow = 'auto';
        },

        selectImage(id, url) {
            this.selectedId = id;
            this.selectedUrl = url;
            this.closeModal();
        },

        removeHoverImage() {
            this.selectedId = '';
            this.selectedUrl = '';
        }
     }">

        <h2 class="text-xl font-semibold mb-4">تصویر هاور (Hover Image)</h2>
        <p class="text-sm text-gray-500 mb-4">
            تصویری را انتخاب کنید که وقتی کاربر موس را روی محصول نگه می‌دارد نمایش داده شود.
            (از بین تصاویر آپلود شده انتخاب کنید)
        </p>

        <input type="hidden" name="hover_image_id" :value="selectedId" form="product-update-form">

        <div class="flex items-start gap-6">
            <div class="relative group cursor-pointer border-2 border-dashed border-gray-300 rounded-lg w-48 h-64 flex items-center justify-center hover:border-blue-500 transition overflow-hidden bg-gray-50"
                @click="openModal">
                
                <template x-if="selectedUrl">
                    <img :src="selectedUrl" class="w-full h-full object-cover">
                </template>

                <template x-if="!selectedUrl">
                    <div class="text-center p-4">
                        <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span class="mt-2 block text-sm font-medium text-gray-600">انتخاب تصویر</span>
                    </div>
                </template>

                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition flex items-center justify-center">
                    <span class="text-white font-bold opacity-0 group-hover:opacity-100">تغییر</span>
                </div>
            </div>

            <button type="button" x-show="selectedUrl" @click="removeHoverImage" class="text-red-500 text-sm hover:underline mt-2">
                حذف تصویر هاور
            </button>
        </div>

        <div x-show="isOpen" 
            style="display: none;"
            x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 backdrop-blur-sm p-4">
            
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-5xl max-h-[90vh] flex flex-col overflow-hidden"
                @click.away="closeModal">
                
                <div class="p-4 border-b flex justify-between items-center bg-gray-50">
                    <h3 class="text-lg font-bold text-gray-800">انتخاب تصویر از گالری محصول</h3>
                    <button @click="closeModal" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
                </div>

                <div class="p-6 overflow-y-auto bg-gray-100 flex-1">
                    @if($product->images->isEmpty())
                        <div class="text-center text-gray-500 py-10">
                            هیچ تصویری برای این محصول آپلود نشده است. ابتدا تصویر آپلود کنید.
                        </div>
                    @else
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                            @foreach($product->images as $img)
                                <div class="group relative cursor-pointer border-4 rounded-lg overflow-hidden shadow-sm hover:shadow-lg transition transform hover:scale-105"
                                    :class="selectedId == '{{ $img->id }}' ? 'border-blue-600 ring-2 ring-blue-300' : 'border-transparent bg-white'"
                                    @click="selectImage('{{ $img->id }}', '{{ Storage::url($img->path) }}')">
                                    
                                    <img src="{{ Storage::url($img->path) }}" loading="lazy" class="w-full h-48 object-contain bg-gray-50">
                                    
                                    <div class="absolute top-2 right-2 bg-blue-600 text-white rounded-full p-1"
                                        x-show="selectedId == '{{ $img->id }}'">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </div>

                                    <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white text-xs p-1 text-center opacity-0 group-hover:opacity-100 transition">
                                        انتخاب این تصویر
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="p-4 border-t bg-gray-50 text-right">
                    <button type="button" @click="closeModal" class="px-6 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                        انصراف
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Hover image end -->

    <!-- Add videos Start -->

    <div class="bg-white shadow-md rounded-lg p-6" 
     dir="rtl"
     x-data="{
         isOpen: false,
         searchQuery: '',
         allVideos: {{ $allVideos ?? '[]' }},
         selectedVideos: {{ $product->videos->pluck('id') ?? '[]' }},
         formId: 'product-update-form',
         
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
             const video = this.allVideos.find(v => v.id === id);
             return video ? (video.name || video.alt_text || 'ویدیو بدون نام') : 'ویدیو یافت نشد';
         }
     }">
    
    <h2 class="text-xl font-semibold mb-4">ویدیوهای محصول</h2>
    <p class="text-sm text-gray-500 mb-4">ویدیوها را از کتابخانه انتخاب کنید. (ابتدا آن‌ها را در بخش "کتابخانه ویدیو" آپلود کنید)</p>
    
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

<!-- Add Videos End -->

<!-- Add Packaging Options Start -->

<div class="bg-white shadow-md rounded-lg p-6 mt-7" 
     dir="rtl"
     x-data="{
         isOpen: false,
         searchQuery: '',
         allPackagingOptions: {{ $allPackagingOptions ?? '[]' }},
         
         // --- تفاوت اصلی: پر کردن داده‌های قبلی ---
         selectedPackagingOptions: {{ $product->packagingOptions->pluck('id') ?? '[]' }}, 
         
         formId: 'product-update-form',

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
    
    <h2 class="text-xl font-semibold mb-4">انواع بسته‌بندی</h2>
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
            هنوز بسته‌بندی برای این محصول انتخاب نشده است.
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

<!-- Add Packaging Options End -->

    
    @endsection

@push('scripts')
<script>
        document.addEventListener("DOMContentLoaded", function() {
            const scrollKey = 'scroll_pos_' + window.location.pathname;
            const savedPosition = localStorage.getItem(scrollKey);

            // 1. Check if we need to scroll to the Variant Form
            if (savedPosition === 'variant_form') {
                const formElement = document.getElementById('add-variant-form');
                if (formElement) {
                    // Scrolls the form into the center of the view
                    formElement.scrollIntoView({ behavior: 'auto', block: 'center' });
                }
                localStorage.removeItem(scrollKey);
            } 
            // 2. Otherwise, restore the exact pixel position (for other forms)
            else if (savedPosition) {
                window.scrollTo(0, parseInt(savedPosition));
                localStorage.removeItem(scrollKey);
            }

            // 3. Listen for submits
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function() {
                    // Check if this is the "Add Variant" form (by ID)
                    if (this.id === 'add-variant-form') {
                        // Save a special flag
                        localStorage.setItem(scrollKey, 'variant_form');
                    } else {
                        // For all other forms, save exact pixel position
                        localStorage.setItem(scrollKey, window.scrollY);
                    }
                });
            });
        });
    </script>
@endpush