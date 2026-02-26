@extends('admin.layouts.app')
@section('title', 'تنظیمات صفحه اصلی')

@section('content')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div dir="rtl" class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">تنظیمات صفحه اصلی سایت</h1>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-r-4 border-green-500 text-green-700 p-4 mb-4 rounded">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.homepage.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div x-data="{ activeTab: 'ribbon' }" class="bg-white shadow-md rounded-lg overflow-hidden border border-gray-200">
            <div class="flex border-b border-gray-200 bg-gray-50 overflow-x-auto">
                <button type="button" @click="activeTab = 'ribbon'" :class="activeTab === 'ribbon' ? 'border-blue-500 text-blue-600 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-100'" class="px-6 py-4 border-b-2 font-medium text-sm whitespace-nowrap transition">
                    نوار ریبون (بالا)
                </button>
                <button type="button" @click="activeTab = 'slider'" :class="activeTab === 'slider' ? 'border-blue-500 text-blue-600 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-100'" class="px-6 py-4 border-b-2 font-medium text-sm whitespace-nowrap transition">
                    اسلایدر اصلی
                </button>
                <button type="button" @click="activeTab = 'banners'" :class="activeTab === 'banners' ? 'border-blue-500 text-blue-600 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-100'" class="px-6 py-4 border-b-2 font-medium text-sm whitespace-nowrap transition">
                    بنرهای میانی
                </button>
                <button type="button" @click="activeTab = 'carousels'" :class="activeTab === 'carousels' ? 'border-blue-500 text-blue-600 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-100'" class="px-6 py-4 border-b-2 font-medium text-sm whitespace-nowrap transition">
                    متن کاروسل‌ها
                </button>
            </div>

            <div class="p-6">
                <div x-show="activeTab === 'ribbon'" class="space-y-6">
                    <h2 class="text-xl font-semibold mb-4 border-b pb-2">تنظیمات نوار بالای سایت (Ribbon)</h2>
                    
                    <div class="flex items-center mb-4">
                        <input type="hidden" name="ribbon[is_visible]" value="0">
                        <input type="checkbox" name="ribbon[is_visible]" value="1" {{ ($data['ribbon']['is_visible'] ?? 1) ? 'checked' : '' }} class="h-4 w-4 text-blue-600 rounded">
                        <label class="mr-2 font-medium text-gray-700">نمایش ریبون در سایت</label>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">متن اصلی</label>
                            <input type="text" name="ribbon[text]" value="{{ $data['ribbon']['text'] ?? 'جشنواره آکامد شروع شد تخفیف ۹۰٪' }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">متن لینک</label>
                            <input type="text" name="ribbon[link_text]" value="{{ $data['ribbon']['link_text'] ?? 'مشاهده محصولات جشنواره' }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">آدرس لینک (URL)</label>
                            <input type="text" name="ribbon[link_url]" value="{{ $data['ribbon']['link_url'] ?? '#' }}" dir="ltr" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <div x-show="activeTab === 'slider'" class="space-y-6" x-data="{ slides: {{ count($data['main_slider'] ?? [1]) }} }">
                    <h2 class="text-xl font-semibold mb-4 border-b pb-2">اسلایدر اصلی (Hero)</h2>
                    
                    @php $sliders = $data['main_slider'] ?? [['title' => '']]; @endphp
                    
                    @foreach($sliders as $index => $slide)
                    <div class="bg-gray-50 p-5 rounded-lg border border-gray-200 mb-6 relative">
                        <h3 class="font-bold text-lg mb-4 text-blue-600">اسلاید شماره {{ $index + 1 }}</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">عنوان بزرگ (H2)</label>
                                <input type="text" name="main_slider[{{$index}}][title]" value="{{ $slide['title'] ?? '' }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">زیرعنوان (P)</label>
                                <input type="text" name="main_slider[{{$index}}][subtitle]" value="{{ $slide['subtitle'] ?? '' }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">متن دکمه</label>
                                <input type="text" name="main_slider[{{$index}}][btn_text]" value="{{ $slide['btn_text'] ?? '' }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">لینک دکمه (URL)</label>
                                <input type="text" name="main_slider[{{$index}}][btn_link]" value="{{ $slide['btn_link'] ?? '' }}" dir="ltr" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                            </div>
                            
                            <div class="md:col-span-2 border-t pt-4 mt-2">
                                <label class="block text-sm font-medium text-gray-700">متن بج (باکس شناور)</label>
                                <input type="text" name="main_slider[{{$index}}][badge_text]" value="{{ $slide['badge_text'] ?? '' }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">تصویر پس‌زمینه اسلاید</label>
                                <input type="file" name="main_slider[{{$index}}][image]" accept="image/*" class="mt-2 text-sm">
                                @if(!empty($slide['image']))
                                    <img src="{{ $slide['image'] }}" class="h-16 mt-2 rounded shadow">
                                @endif
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">تصویر بج (شناور)</label>
                                <input type="file" name="main_slider[{{$index}}][badge_img]" accept="image/*" class="mt-2 text-sm">
                                @if(!empty($slide['badge_img']))
                                    <img src="{{ $slide['badge_img'] }}" class="h-16 mt-2 rounded shadow">
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                    
                    <p class="text-sm text-gray-500 mt-2">برای اضافه کردن اسلاید جدید، از یک برنامه‌نویس بخواهید یا مقادیر را تغییر دهید.</p>
                </div>

                <div x-show="activeTab === 'banners'" class="space-y-6">
                    <h2 class="text-xl font-semibold mb-4 border-b pb-2">بنرهای میانی سایت</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="bg-gray-50 p-4 border rounded">
                            <label class="block text-sm font-bold text-gray-700 mb-2">بنر سمت راست</label>
                            <input type="file" name="middle_images[image_1]" accept="image/*" class="block w-full text-sm">
                            @if(!empty($data['middle_images']['image_1']))
                                <img src="{{ $data['middle_images']['image_1'] }}" class="mt-3 w-full h-40 object-cover rounded shadow">
                            @endif
                        </div>
                        <div class="bg-gray-50 p-4 border rounded">
                            <label class="block text-sm font-bold text-gray-700 mb-2">بنر سمت چپ</label>
                            <input type="file" name="middle_images[image_2]" accept="image/*" class="block w-full text-sm">
                            @if(!empty($data['middle_images']['image_2']))
                                <img src="{{ $data['middle_images']['image_2'] }}" class="mt-3 w-full h-40 object-cover rounded shadow">
                            @endif
                        </div>
                    </div>
                </div>

                <div x-show="activeTab === 'carousels'" class="space-y-8">
                    <h2 class="text-xl font-semibold mb-4 border-b pb-2">متن‌های کاروسل محصولات</h2>
                    
                    @foreach(['carousel_1' => 'کاروسل اول (ساده)', 'carousel_2' => 'کاروسل دوم (با پس‌زمینه)', 'carousel_3' => 'کاروسل سوم (با عکس کناری)'] as $key => $title)
                    <div class="bg-gray-50 p-5 rounded-lg border border-gray-200">
                        <h3 class="font-bold text-lg mb-4 text-blue-600">{{ $title }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">عنوان بزرگ (H3)</label>
                                <input type="text" name="{{$key}}[title]" value="{{ $data[$key]['title'] ?? '' }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">زیر عنوان (Small)</label>
                                <input type="text" name="{{$key}}[subtitle]" value="{{ $data[$key]['subtitle'] ?? '' }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">متن لینک (مشاهده همه)</label>
                                <input type="text" name="{{$key}}[link_text]" value="{{ $data[$key]['link_text'] ?? '' }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">آدرس لینک (URL)</label>
                                <input type="text" name="{{$key}}[link_url]" value="{{ $data[$key]['link_url'] ?? '#' }}" dir="ltr" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

            </div>

            <div class="bg-gray-100 px-6 py-4 flex justify-end border-t">
                <button type="submit" class="px-8 py-3 bg-green-600 text-white font-bold rounded-lg shadow-lg hover:bg-green-700 transition">
                    ذخیره و همگام‌سازی با سایت
                </button>
            </div>
        </div>
    </form>
</div>
@endsection