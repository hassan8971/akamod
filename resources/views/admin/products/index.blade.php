@extends('admin.layouts.app')

@section('title', 'مدیریت محصولات')

@section('content')
<script defer src="http://localhost/alpine/dist/cdn.min.js"></script>

<div dir="rtl">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold">محصولات</h1>
            
            <p class="text-sm text-gray-600 mt-2">
                @if($selectedCategory)
                    <span>
                        نمایش <span class="font-bold text-blue-600">{{ number_format($productCount) }}</span> محصول در دسته‌بندی: <span class="font-bold text-blue-600">{{ $selectedCategory->name }}</span>
                    </span>
                    <a href="{{ route('admin.products.index') }}" class="text-xs text-red-500 hover:underline">[حذف فیلتر]</a>
                @else
                    <span>
                        نمایش کل محصولات: <span class="font-bold text-gray-800">{{ number_format($productCount) }}</span> عدد
                    </span>
                @endif
            </p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 mt-4 md:mt-0">
            + ایجاد محصول
        </a>
    </div>

    <div class="mb-6" 
         x-data="{
             isOpen: false,
             searchQuery: '',
             // لیست کامل دسته‌بندی‌ها از کنترلر می‌آید
             allCategories: {{ $categories->toJson() }},
             
             // فیلتر کردن لیست بر اساس جستجو
             get filteredCategories() {
                 if (this.searchQuery === '') {
                     return this.allCategories;
                 }
                 return this.allCategories.filter(category => 
                     category.name.toLowerCase().includes(this.searchQuery.toLowerCase())
                 );
             },
             
             // اعمال فیلتر با ریدایرکت کردن صفحه
             selectCategory(categoryId) {
                 let url = new URL(window.location.href);
                 url.searchParams.set('category_id', categoryId);
                 url.searchParams.delete('page'); // بازگشت به صفحه ۱
                 window.location.href = url.href;
             }
         }">
        
        <div class="relative">
            <label for="category_search" class="block text-sm font-medium text-gray-700">فیلتر بر اساس دسته‌بندی</label>
            <input type="text"
                   id="category_search"
                   x-model="searchQuery"
                   @focus="isOpen = true"
                   @click.away="isOpen = false"
                   placeholder="جستجو یا انتخاب دسته‌بندی..."
                   autocomplete="off"
                   class="mt-1 block w-full md:w-1/3 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            
            <div x-show="isOpen" 
                 x-transition
                 class="absolute z-10 mt-1 w-full md:w-1/3 bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm"
                 style="display: none;">
                
                <ul class="py-1">
                    <template x-for="category in filteredCategories" :key="category.id">
                        <li @click="selectCategory(category.id)"
                            class="text-gray-900 cursor-pointer select-none relative py-2 px-4 hover:bg-gray-100">
                            <span x-text="category.name"></span>
                        </li>
                    </template>
                    <li x-show="filteredCategories.length === 0 && searchQuery !== ''" class="py-2 px-4 text-gray-500">
                        دسته‌بندی با این نام یافت نشد.
                    </li>
                </ul>
            </div>
        </div>
    </div>
    @if(session('success'))
        <div class="bg-green-100 border-r-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">نام</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">ایجادکننده</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">دسته‌بندی</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">جنسیت</th>
                         <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        شناسه محصول
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">وضعیت</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-4 border-b ...">
                                <p class="text-gray-900 whitespace-no-wrap">{{ $product->name }}</p>
                            </td>
                            <td class="px-5 py-4 border-b ...">
                                <p class="text-gray-600 whitespace-no-wrap">
                                    {{ $product->admin?->name ?? 'نامشخص' }}
                                </p>
                            </td>
                            <td class="px-5 py-4 border-b ...">
                                <p class="text-gray-900 whitespace-no-wrap">{{ $product->category->name ?? 'N/A' }}</p>
                            </td>
                            <td class="px-5 py-4 border-b">
                                @if($product->is_for_men && $product->is_for_women)
                                    <span class="font-semibold text-gray-800">هردو</span>
                                @elseif($product->is_for_men)
                                    <span class="font-semibold text-blue-600">آقایان</span>
                                @elseif($product->is_for_women)
                                    <span class="font-semibold text-pink-600">بانوان</span>
                                @else
                                    <span class="text-gray-400">---</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 border-b">
                                <p class="text-gray-900 whitespace-no-wrap">{{ $product->product_id }}</p>
                            </td>
                            <td class="px-5 py-4 border-b ...">
                                @if($product->is_visible)
                                    <span class="relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight">
                                        <span aria-hidden class="absolute inset-0 bg-green-200 opacity-50 rounded-full"></span>
                                        <span class="relative">قابل مشاهده</span>
                                    </span>
                                @else
                                    <span class="relative inline-block px-3 py-1 font-semibold text-gray-700 leading-tight">
                                        <span aria-hidden class="absolute inset-0 bg-gray-200 opacity-50 rounded-full"></span>
                                        <span class="relative">مخفی</span>
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 border-b ... text-left whitespace-nowrap">
                                <a href="{{ route('admin.products.edit', $product) }}" class="text-blue-600 hover:text-blue-900 ml-4">ویرایش</a>
                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline-block" onsubmit="return confirm('آیا از حذف این محصول مطمئن هستید؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">حذف</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">
                                @if($selectedCategory)
                                    هیچ محصولی در این دسته‌بندی یافت نشد.
                                @else
                                    هیچ محصولی یافت نشد. <a href="{{ route('admin.products.create') }}" class="text-blue-600 hover:underline">یکی ایجاد کنید!</a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    
        <div class="p-4">
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection