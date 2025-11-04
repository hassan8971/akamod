@extends('admin.layouts.app')

@section('title', 'ویرایش محصول: ' . $product->name)

@section('content')
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

    <!-- Product Details Form -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-8" dir="rtl">
        <h2 class="text-xl font-semibold mb-4">اطلاعات محصول</h2>
        <form action="{{ route('admin.products.update', $product) }}" method="POST">
            @csrf
            @method('PUT')
            
            @include('admin.products._form')

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
                            <th class="px-4 py-2 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold uppercase">نام</th>
                            <th class="px-4 py-2 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold uppercase">قیمت (تومان)</th>
                            <th class="px-4 py-2 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold uppercase">موجودی</th>
                            <th class="px-4 py-2 border-b-2 border-gray-200 bg-gray-100"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($product->variants as $variant)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 border-b border-gray-200">{{ $variant->name }} ({{ $variant->size }}, {{ $variant->color }})</td>
                                <td class="px-4 py-3 border-b border-gray-200">{{ number_format($variant->price) }}</td>
                                <td class="px-4 py-3 border-b border-gray-200">{{ $variant->stock }}</td>
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
        <h3 class="text-lg font-medium mb-2">افزودن متغیر جدید</h3>
        <form action="{{ route('admin.products.variants.store', $product) }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
                <div>
                    <label for="variant_name" class="block text-sm font-medium text-gray-700">نام</label>
                    <input type="text" name="name" id="variant_name" placeholder="مثال: کوچک، قرمز"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required>
                </div>
                <div>
                    <label for="variant_size" class="block text-sm font-medium text-gray-700">اندازه</label>
                    <input type="text" name="size" id="variant_size" placeholder="مثال: Small"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="variant_color" class="block text-sm font-medium text-gray-700">رنگ</label>
                    <input type="text" name="color" id="variant_color" placeholder="مثال: قرمز"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="variant_price" class="block text-sm font-medium text-gray-700">قیمت (تومان)</label>
                    <input type="number" name="price" id="variant_price" placeholder="مثال: 50000"
                           step="1" min="0" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required>
                </div>
                <div>
                    <label for="variant_stock" class="block text-sm font-medium text-gray-700">موجودی انبار</label>
                    <input type="number" name="stock" id="variant_stock" placeholder="مثال: 100"
                           min="0" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required>
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

    <!-- START: Manage Videos (NEW SECTION) -->
    <div class="bg-white shadow-md rounded-lg p-6" dir="rtl">
        <h2 class="text-xl font-semibold mb-4">مدیریت ویدیوها</h2>
        <!-- List Existing Videos -->
        <div class="mb-6">
            <h3 class="text-lg font-medium mb-2">ویدیوهای موجود</h3>
            
            <!-- FIXED: Check for null before calling isEmpty() -->
            @if (is_null($product->videos) || $product->videos->isEmpty())
                <p class="text-gray-500">هنوز ویدیویی آپلود نشده است.</p>
            @else
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @foreach ($product->videos as $video)
                        <div class="relative border rounded-lg overflow-hidden shadow bg-gray-900">
                            <video controls class="w-full h-32 object-cover">
                                <source src="{{ Storage::url($video->path) }}" type="video/mp4">
                                مرورگر شما از تگ ویدیو پشتیبانی نمی‌کند.
                            </video>
                            <div class="absolute top-1 left-1">
                                <form action="{{ route('admin.videos.destroy', $video) }}" method="POST" onsubmit="return confirm('آیا از حذف این ویدیو مطمئن هستید؟');">
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
        <!-- Add New Videos Form -->
        <hr class="my-6">
        <h3 class="text-lg font-medium mb-2">آپلود ویدیوهای جدید</h3>
        <form action="{{ route('admin.products.videos.store', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div>
                <label for="videos" class="block text-sm font-medium text-gray-700">انتخاب ویدیو (mp4, mov, ...)</label>
                <input type="file" name="videos[]" id="videos" multiple accept="video/*"
                       class="mt-1 block w-full text-sm text-gray-500
                              file:mr-4 file:py-2 file:px-4 file:ml-4
                              file:rounded-full file:border-0
                              file:text-sm file:font-semibold
                              file:bg-purple-50 file:text-purple-700
                              hover:file:bg-purple-100" required>
            </div>
            <div class="flex justify-end mt-4">
                <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                    آپلود ویدیوها
                </button>
            </div>
        </form>
    </div>
    <!-- END: Manage Videos -->

@endsection

