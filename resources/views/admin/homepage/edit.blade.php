@extends('admin.layouts.app')
@section('title', 'تنظیمات جامع صفحه اصلی')

@section('content')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('imageUploader', (initialImage = '') => ({
            preview: initialImage,
            fileSize: '',
            width: '',
            height: '',
            handleFile(e) {
                const file = e.target.files[0];
                if (!file) return;
                
                this.fileSize = (file.size / 1024).toFixed(2);
                this.preview = URL.createObjectURL(file);
                
                let img = new Image();
                img.onload = () => {
                    this.width = img.width;
                    this.height = img.height;
                };
                img.src = this.preview;
            }
        }));
    });
</script>

<div dir="rtl" class="max-w-7xl mx-auto pb-10">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">ویرایشگر صفحه اصلی (Home Page)</h1>
        <button type="submit" form="homepage-form" class="px-8 py-3 bg-green-600 text-white font-bold rounded-lg shadow-lg hover:bg-green-700 transition">
            ذخیره کل تغییرات
        </button>
    </div>

    @if(session('success'))
        <div class="flash-message bg-green-100 border-r-4 border-green-500 text-green-700 p-4 mb-4 rounded relative transition-all">
            {{ session('success') }}
            <button type="button" onclick="this.parentElement.remove()" class="absolute left-4 top-4 text-green-900 hover:text-green-600 font-bold text-xl leading-none">&times;</button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="flash-message bg-red-100 border-r-4 border-red-500 text-red-700 p-4 mb-4 rounded relative transition-all">
            {{ session('error') }}
            <button type="button" onclick="this.parentElement.remove()" class="absolute left-4 top-4 text-red-900 hover:text-red-600 font-bold text-xl leading-none">&times;</button>
        </div>
    @endif

    <form id="homepage-form" action="{{ route('admin.homepage.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div x-data="{ activeTab: 'slider' }" class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200">
            <div class="flex border-b border-gray-200 bg-gray-50 overflow-x-auto sticky top-0 z-10" @click="document.querySelectorAll('.flash-message').forEach(el => el.remove())">
                <button type="button" @click="activeTab = 'slider'" :class="activeTab === 'slider' ? 'border-blue-500 text-blue-600 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-5 py-4 border-b-2 font-medium text-sm transition">اسلایدر اصلی</button>
                <button type="button" @click="activeTab = 'category'" :class="activeTab === 'category' ? 'border-blue-500 text-blue-600 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-5 py-4 border-b-2 font-medium text-sm transition">بخش دسته‌بندی (گرید)</button>
                <button type="button" @click="activeTab = 'banners'" :class="activeTab === 'banners' ? 'border-blue-500 text-blue-600 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-5 py-4 border-b-2 font-medium text-sm transition">بنرها و اصالت</button>
                <button type="button" @click="activeTab = 'carousels'" :class="activeTab === 'carousels' ? 'border-blue-500 text-blue-600 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-5 py-4 border-b-2 font-medium text-sm transition">متن کاروسل‌ها</button>
                <button type="button" @click="activeTab = 'info'" :class="activeTab === 'info' ? 'border-blue-500 text-blue-600 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-5 py-4 border-b-2 font-medium text-sm transition">بخش اطلاعات و آکاردئون</button>
                <button type="button" @click="activeTab = 'ribbon'" :class="activeTab === 'ribbon' ? 'border-blue-500 text-blue-600 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-5 py-4 border-b-2 font-medium text-sm transition">نوار ریبون</button>
            </div>

            <div class="p-6 bg-white min-h-[500px]">

                <div x-show="activeTab === 'slider'" x-data="{ slides: {{ json_encode($data['main_slider']) }} }">
                  	
                  <div class="bg-blue-50 p-4 rounded-lg border border-blue-100 mb-6">
                    <h3 class="font-bold text-gray-800 mb-3 text-sm">تنظیمات عمومی اسلایدر</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">سرعت تغییر خودکار (میلی‌ثانیه)</label>
                            <input type="number" name="slider_duration" value="{{ $data['slider_duration'] ?? 3000 }}" class="mt-1 w-full border rounded p-2 bg-white" placeholder="مثال: 3000">
                            <span class="text-[10px] text-gray-500 block mt-1">۱۰۰۰ میلی‌ثانیه = ۱ ثانیه (پیش‌فرض: 3000)</span>
                        </div>
                    </div>
                </div>
                  
                    <div class="flex justify-between items-center mb-4 border-b pb-2">
                        <h2 class="text-xl font-semibold">اسلایدر اصلی سایت</h2>
                        <button type="button" @click="slides.push({})" class="bg-blue-100 text-blue-700 px-3 py-1 rounded text-sm hover:bg-blue-200">+ افزودن اسلاید جدید</button>
                    </div>

                    <div class="space-y-6">
                        <template x-for="(slide, index) in slides" :key="index">
                            <div class="bg-gray-50 p-5 rounded-lg border border-gray-200 relative">
                                <button type="button" @click="slides.splice(index, 1)" class="absolute top-4 left-4 text-red-500 hover:text-red-700 text-sm font-bold bg-red-50 px-2 py-1 rounded">حذف اسلاید</button>
                                <h3 class="font-bold text-lg mb-4 text-blue-600" x-text="'اسلاید شماره ' + (index + 1)"></h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div><label class="block text-sm">عنوان اصلی</label><input type="text" :name="'main_slider['+index+'][title]'" :value="slide.title" class="w-full border rounded p-2"></div>
                                    <div><label class="block text-sm">زیرعنوان</label><input type="text" :name="'main_slider['+index+'][subtitle]'" :value="slide.subtitle" class="w-full border rounded p-2"></div>
                                    <div><label class="block text-sm">متن دکمه</label><input type="text" :name="'main_slider['+index+'][btn_text]'" :value="slide.btn_text" class="w-full border rounded p-2"></div>
                                    <div><label class="block text-sm">لینک دکمه</label><input type="text" :name="'main_slider['+index+'][btn_link]'" :value="slide.btn_link" dir="ltr" class="w-full border rounded p-2"></div>
                                    <div class="md:col-span-2"><label class="block text-sm">متن باکس روی عکس (Badge)</label><input type="text" :name="'main_slider['+index+'][badge_text]'" :value="slide.badge_text" class="w-full border rounded p-2"></div>
                                    
                                    <div class="p-3 bg-white border rounded">
                                        <label class="block text-sm font-bold">عکس پس‌زمینه اسلاید</label>
                                        <div x-data="imageUploader(slide.image)">
                                            <input type="file" :name="'main_slider['+index+'][image]'" @change="handleFile" class="mt-2 text-sm w-full">
                                            <div class="mt-2 flex items-center gap-3 bg-gray-50 p-2 rounded border" x-show="preview">
                                                <img :src="preview" class="h-12 object-contain rounded bg-white border shadow-sm">
                                                <div class="text-[10px] text-gray-600 space-y-1" x-show="fileSize" x-cloak>
                                                    <span class="block font-bold"><span class="text-gray-400">حجم:</span> <span class="text-blue-600" x-text="fileSize + ' KB'"></span></span>
                                                    <span class="block font-bold"><span class="text-gray-400">ابعاد:</span> <span class="text-green-600" x-text="width + 'x' + height + ' px'"></span></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="p-3 bg-white border rounded">
                                        <label class="block text-sm font-bold">عکس بج شناور</label>
                                        <div x-data="imageUploader(slide.badge_img)">
                                            <input type="file" :name="'main_slider['+index+'][badge_img]'" @change="handleFile" class="mt-2 text-sm w-full">
                                            <div class="mt-2 flex items-center gap-3 bg-gray-50 p-2 rounded border" x-show="preview">
                                                <img :src="preview" class="h-12 object-contain rounded bg-white border shadow-sm">
                                                <div class="text-[10px] text-gray-600 space-y-1" x-show="fileSize" x-cloak>
                                                    <span class="block font-bold"><span class="text-gray-400">حجم:</span> <span class="text-blue-600" x-text="fileSize + ' KB'"></span></span>
                                                    <span class="block font-bold"><span class="text-gray-400">ابعاد:</span> <span class="text-green-600" x-text="width + 'x' + height + ' px'"></span></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div x-show="activeTab === 'category'" x-data="{ gridItems: {{ json_encode($data['category_grid']) }} }">
                    <h2 class="text-xl font-semibold mb-4 border-b pb-2">تنظیمات بخش گرید دسته‌بندی‌ها</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8 bg-blue-50 p-4 rounded-lg border border-blue-100">
                        <div><label class="block text-sm">عنوان سایدبار</label><input type="text" name="category_section[title]" value="{{ $data['category_section']['title'] ?? 'دسته بندی محصولات' }}" class="w-full border rounded p-2"></div>
                        <div><label class="block text-sm">متن لینک سایدبار</label><input type="text" name="category_section[link_text]" value="{{ $data['category_section']['link_text'] ?? 'سایر دسته‌ها' }}" class="w-full border rounded p-2"></div>
                        <div><label class="block text-sm">آدرس لینک سایدبار</label><input type="text" name="category_section[link_url]" value="{{ $data['category_section']['link_url'] ?? '#' }}" dir="ltr" class="w-full border rounded p-2"></div>
                    </div>

                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold">تصاویر گرید (کارت‌ها)</h3>
                        <button type="button" @click="gridItems.push({})" class="bg-gray-200 text-gray-800 px-3 py-1 rounded text-sm hover:bg-gray-300">+ افزودن کارت جدید به گرید</button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                        <template x-for="(item, index) in gridItems" :key="index">
                            <div class="bg-gray-50 p-3 border rounded relative">
                                <button type="button" @click="gridItems.splice(index, 1)" class="absolute top-2 left-2 text-red-500 hover:text-red-700 text-xl font-bold">&times;</button>
                                  <div class="mb-2">
                                    <div class="flex justify-between items-center mb-1">
                                        <label class="block text-xs font-bold">
                                            لینک تصویر <span class="text-red-500">*</span>
                                        </label>
                                        <span x-show="!item.link_url" class="text-[10px] text-red-600 font-bold" x-cloak>
                                            (پر کردن فیلد اجباری است)
                                        </span>
                                    </div>

                                    <input type="text" 
                                           :name="'category_grid['+index+'][link_url]'" 
                                           x-model="item.link_url" 
                                           required
                                           dir="ltr" 
                                           class="w-full text-xs border rounded p-1 outline-none transition-colors" 
                                           :class="!item.link_url ? 'border-red-500 bg-red-50 focus:border-red-600' : 'border-gray-300 focus:border-blue-500'"
                                           placeholder="https://...">
                                  </div>
                                <div x-data="imageUploader(item.image)" class="mt-3">
                                    <input type="file" :name="'category_grid['+index+'][image]'" @change="handleFile" class="w-full text-xs">
                                    <div class="mt-2 flex items-center gap-2 bg-white p-2 rounded border" x-show="preview">
                                        <img :src="preview" class="h-12 object-cover rounded border">
                                        <div class="text-[9px] text-gray-600" x-show="fileSize" x-cloak>
                                            <span class="block text-blue-600 font-bold" x-text="fileSize + ' KB'"></span>
                                            <span class="block text-green-600 font-bold" x-text="width + 'x' + height"></span>
                                        </div>
                                    </div>
                                </div>
                                <template x-if="item.image"><img :src="item.image" class="h-20 w-full object-cover mt-2 rounded"></template>
                            </div>
                        </template>
                    </div>
                </div>

                <div x-show="activeTab === 'banners'" class="space-y-8">
                    <div>
                        <h2 class="text-xl font-semibold mb-4 border-b pb-2">دو بنر تصویری وسط صفحه</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="border p-4 rounded bg-gray-50">
                                <label class="block font-bold">بنر راست</label>
                                <input type="text" name="middle_images[link_1]" value="{{ $data['middle_images']['link_1'] ?? '#' }}" class="w-full border rounded p-2 my-2 text-sm" dir="ltr" placeholder="لینک بنر">

                                <div x-data="imageUploader('{{ $data['middle_images']['image_1'] ?? '' }}')">
                                    <input type="file" name="middle_images[image_1]" @change="handleFile" class="text-xs w-full">
                                    <div class="mt-2 flex items-center gap-3 bg-white p-2 rounded border" x-show="preview">
                                        <img :src="preview" class="h-12 object-contain rounded border">
                                        <div class="text-[10px] text-gray-600" x-show="fileSize" x-cloak>
                                            <span class="block text-blue-600 font-bold" x-text="'حجم: ' + fileSize + ' KB'"></span>
                                            <span class="block text-green-600 font-bold" x-text="'ابعاد: ' + width + 'x' + height + ' px'"></span>
                                        </div>
                                    </div>
                                </div>
                                @if(!empty($data['middle_images']['image_1'])) <img src="{{ $data['middle_images']['image_1'] }}" class="mt-2 h-24 rounded"> @endif
                            </div>
                            <div class="border p-4 rounded bg-gray-50">
                                <label class="block font-bold">بنر چپ</label>
                                <input type="text" name="middle_images[link_2]" value="{{ $data['middle_images']['link_2'] ?? '#' }}" class="w-full border rounded p-2 my-2 text-sm" dir="ltr" placeholder="لینک بنر">
                                <div x-data="imageUploader('{{ $data['middle_images']['image_2'] ?? '' }}')">
                                    <input type="file" name="middle_images[image_2]" @change="handleFile" class="text-xs w-full">
                                    <div class="mt-2 flex items-center gap-3 bg-white p-2 rounded border" x-show="preview">
                                        <img :src="preview" class="h-12 object-contain rounded border">
                                        <div class="text-[10px] text-gray-600" x-show="fileSize" x-cloak>
                                            <span class="block text-blue-600 font-bold" x-text="'حجم: ' + fileSize + ' KB'"></span>
                                            <span class="block text-green-600 font-bold" x-text="'ابعاد: ' + width + 'x' + height + ' px'"></span>
                                        </div>
                                    </div>
                                </div>
                                @if(!empty($data['middle_images']['image_2'])) <img src="{{ $data['middle_images']['image_2'] }}" class="mt-2 h-24 rounded"> @endif
                            </div>
                        </div>
                    </div>

                    <div>
                        <h2 class="text-xl font-semibold mb-4 border-b pb-2">بخش آیتم‌های اصیل (Authentic)</h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div><label class="text-sm">عنوان بخش</label><input type="text" name="authentic_section[title]" value="{{ $data['authentic_section']['title'] ?? 'آیتم های اصیل' }}" class="w-full border p-2 rounded"></div>
                            <div><label class="text-sm">متن لینک</label><input type="text" name="authentic_section[link_text]" value="{{ $data['authentic_section']['link_text'] ?? 'مشاهده همه' }}" class="w-full border p-2 rounded"></div>
                            <div><label class="text-sm">آدرس لینک</label><input type="text" name="authentic_section[link_url]" value="{{ $data['authentic_section']['link_url'] ?? '#' }}" class="w-full border p-2 rounded" dir="ltr"></div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    
                            <div class="border p-3 rounded bg-blue-50">
                                <label class="font-bold text-sm text-blue-800">کارت بزرگ</label>
                                <input type="text" name="authentic_section[big_card][title]" value="{{ $data['authentic_section']['big_card']['title'] ?? '' }}" class="w-full border p-1 my-1 text-xs" placeholder="عنوان کارت">
                                <input type="text" name="authentic_section[big_card][link]" value="{{ $data['authentic_section']['big_card']['link'] ?? '' }}" class="w-full border p-1 mb-2 text-xs" dir="ltr" placeholder="لینک مقصد">
                                
                                <div x-data="imageUploader('{{ $data['authentic_section']['big_card']['image'] ?? '' }}')">
                                    <input type="file" name="authentic_section[big_card][image]" @change="handleFile" class="text-xs w-full">
                                    <div class="mt-2 flex items-center gap-3 bg-white p-2 rounded border" x-show="preview">
                                        <img :src="preview" class="h-12 object-contain rounded border">
                                        <div class="text-[10px] text-gray-600" x-show="fileSize" x-cloak>
                                            <span class="block text-blue-600 font-bold" x-text="'حجم: ' + fileSize + ' KB'"></span>
                                            <span class="block text-green-600 font-bold" x-text="'ابعاد: ' + width + 'x' + height + ' px'"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="border p-3 rounded">
                                <label class="font-bold text-sm">کارت کوچک 1</label>
                                <input type="text" name="authentic_section[small_card_1][title]" value="{{ $data['authentic_section']['small_card_1']['title'] ?? '' }}" class="w-full border p-1 my-1 text-xs" placeholder="عنوان کارت">
                                <input type="text" name="authentic_section[small_card_1][link]" value="{{ $data['authentic_section']['small_card_1']['link'] ?? '' }}" class="w-full border p-1 mb-2 text-xs" dir="ltr" placeholder="لینک مقصد">
                                
                                <div x-data="imageUploader('{{ $data['authentic_section']['small_card_1']['image'] ?? '' }}')">
                                    <input type="file" name="authentic_section[small_card_1][image]" @change="handleFile" class="text-xs w-full">
                                    <div class="mt-2 flex items-center gap-3 bg-white p-2 rounded border" x-show="preview">
                                        <img :src="preview" class="h-12 object-contain rounded border">
                                        <div class="text-[10px] text-gray-600" x-show="fileSize" x-cloak>
                                            <span class="block text-blue-600 font-bold" x-text="'حجم: ' + fileSize + ' KB'"></span>
                                            <span class="block text-green-600 font-bold" x-text="'ابعاد: ' + width + 'x' + height + ' px'"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="border p-3 rounded">
                                <label class="font-bold text-sm">کارت کوچک 2</label>
                                <input type="text" name="authentic_section[small_card_2][title]" value="{{ $data['authentic_section']['small_card_2']['title'] ?? '' }}" class="w-full border p-1 my-1 text-xs" placeholder="عنوان کارت">
                                <input type="text" name="authentic_section[small_card_2][link]" value="{{ $data['authentic_section']['small_card_2']['link'] ?? '' }}" class="w-full border p-1 mb-2 text-xs" dir="ltr" placeholder="لینک مقصد">
                                
                                <div x-data="imageUploader('{{ $data['authentic_section']['small_card_2']['image'] ?? '' }}')">
                                    <input type="file" name="authentic_section[small_card_2][image]" @change="handleFile" class="text-xs w-full">
                                    <div class="mt-2 flex items-center gap-3 bg-white p-2 rounded border" x-show="preview">
                                        <img :src="preview" class="h-12 object-contain rounded border">
                                        <div class="text-[10px] text-gray-600" x-show="fileSize" x-cloak>
                                            <span class="block text-blue-600 font-bold" x-text="'حجم: ' + fileSize + ' KB'"></span>
                                            <span class="block text-green-600 font-bold" x-text="'ابعاد: ' + width + 'x' + height + ' px'"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div x-show="activeTab === 'carousels'" class="space-y-8">
                    @foreach(['carousel_1' => 'کاروسل اول (ساده)', 'carousel_2' => 'کاروسل دوم (با پس‌زمینه)', 'carousel_3' => 'کاروسل سوم (با عکس کناری)'] as $key => $title)
                    <div class="bg-gray-50 p-5 rounded-lg border border-gray-200">
                        <h3 class="font-bold text-lg mb-4 text-blue-600">{{ $title }}</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 border-b pb-6">
                            <div><label class="block text-sm">عنوان بزرگ</label><input type="text" name="{{$key}}[title]" value="{{ $data[$key]['title'] ?? '' }}" class="w-full border rounded p-2"></div>
                            <div><label class="block text-sm">زیر عنوان</label><input type="text" name="{{$key}}[subtitle]" value="{{ $data[$key]['subtitle'] ?? '' }}" class="w-full border rounded p-2"></div>
                            <div><label class="block text-sm">متن نشان/بج</label><input type="text" name="{{$key}}[badge]" value="{{ $data[$key]['badge'] ?? '' }}" class="w-full border rounded p-2"></div>
                            <div><label class="block text-sm">متن دکمه (مشاهده همه)</label><input type="text" name="{{$key}}[link_text]" value="{{ $data[$key]['link_text'] ?? '' }}" class="w-full border rounded p-2"></div>
                            <div class="md:col-span-2"><label class="block text-sm">آدرس لینک مشاهده همه</label><input type="text" name="{{$key}}[link_url]" value="{{ $data[$key]['link_url'] ?? '#' }}" dir="ltr" class="w-full border rounded p-2"></div>
                            
                            @if($key === 'carousel_2')
                                <div class="md:col-span-2 bg-white p-3 border rounded mt-2">
                                    <label class="block text-sm font-bold">عکس پس‌زمینه کاروسل دوم</label>
                                    <div x-data="imageUploader('{{ $data[$key]['background_image'] ?? '' }}')">
                                        <input type="file" name="{{$key}}[background_image]" @change="handleFile" class="mt-1 text-sm w-full">
                                        <div class="mt-2 flex items-center gap-3 bg-gray-50 p-2 rounded border" x-show="preview">
                                            <img :src="preview" class="h-12 object-contain rounded bg-white border">
                                            <div class="text-[10px] text-gray-600" x-show="fileSize" x-cloak>
                                                <span class="block text-blue-600 font-bold" x-text="'حجم: ' + fileSize + ' KB'"></span>
                                                <span class="block text-green-600 font-bold" x-text="'ابعاد: ' + width + 'x' + height + ' px'"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @elseif($key === 'carousel_3')
                                <div class="md:col-span-2 bg-white p-3 border rounded mt-2">
                                    <label class="block text-sm font-bold">عکس کناری کاروسل سوم</label>
                                    <input type="file" name="{{$key}}[side_image]" class="mt-1">
                                    @if(!empty($data[$key]['side_image'])) <img src="{{ $data[$key]['side_image'] }}" class="h-20 mt-2 rounded"> @endif
                                </div>
                            @endif
                        </div>

                        <div class="bg-white p-4 rounded border border-blue-100" x-data="{ 
                            queryType: '{{ $data[$key]['query_type'] ?? 'latest' }}',
                            customItems: {{ json_encode($data[$key]['custom_items'] ?? []) }},
                            handleImageLoad(e, index) {
                                const file = e.target.files[0];
                                if (!file) return;
                                
                                let item = this.customItems[index];
                                item.fileSize = (file.size / 1024).toFixed(2); // محاسبه حجم به کیلوبایت
                                item.preview = URL.createObjectURL(file); // ساخت لینک موقت پیش‌نمایش
                                
                                // خواندن ابعاد تصویر
                                let img = new Image();
                                img.onload = () => {
                                    item.width = img.width;
                                    item.height = img.height;
                                };
                                img.src = item.preview;
                            }
                        }">
                            <h4 class="font-bold text-gray-800 mb-3 text-sm">تنظیمات نمایش محصولات این کاروسل</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">منبع محصولات</label>
                                    <select name="{{$key}}[query_type]" x-model="queryType" class="mt-1 w-full border rounded p-2 bg-gray-50 focus:bg-white">
                                        <option value="latest">جدیدترین محصولات (پیش‌فرض)</option>
                                        <option value="category">بر اساس دسته‌بندی</option>
                                        <option value="manual">انتخاب دستی محصولات</option>
                                        <option value="custom">آیتم‌های سفارشی (بدون محصول)</option> 
                                    </select>
                                </div>

                                <div x-show="queryType === 'category'" style="display: none;" class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">انتخاب دسته‌بندی</label>
                                    <select name="{{$key}}[category_slug]" class="mt-1 w-full border rounded p-2">
                                        <option value="">-- یک دسته انتخاب کنید --</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->slug }}" {{ ($data[$key]['category_slug'] ?? '') == $cat->slug ? 'selected' : '' }}>
                                                {{ $cat->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div x-show="queryType === 'manual'" style="display: none;" class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">انتخاب محصولات</label>
                                    <span class="text-[10px] text-gray-500 block mb-1">برای انتخاب چند مورد، کلید Ctrl (یا Cmd در مک) را نگه دارید.</span>
                                    <select name="{{$key}}[manual_products][]" multiple class="w-full border rounded p-2 h-40 bg-gray-50">
                                        @foreach($products as $prod)
                                            <option value="{{ $prod->slug }}" {{ in_array($prod->slug, $data[$key]['manual_products'] ?? []) ? 'selected' : '' }}>
                                                {{ $prod->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div x-show="queryType === 'custom'" style="display: none;" class="col-span-1 md:col-span-3 mt-4 border-t pt-4">
                                    <div class="flex justify-between items-center mb-4">
                                        <label class="block text-sm font-bold text-blue-700">لیست آیتم‌های سفارشی</label>
                                        <button type="button" @click="customItems.push({title: '', link: '', image: '', preview: '', width: '', height: '', fileSize: ''})" class="bg-blue-100 text-blue-800 px-3 py-1 rounded text-sm hover:bg-blue-200 shadow-sm">+ افزودن آیتم جدید</button>
                                    </div>

                                    <div class="space-y-4">
                                        <template x-for="(item, index) in customItems" :key="index">
                                            <div class="bg-gray-50 p-4 border rounded-lg relative grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <button type="button" @click="customItems.splice(index, 1)" class="absolute top-2 left-2 text-red-500 hover:text-red-700 text-sm bg-red-100 px-2 py-1 rounded font-bold">حذف</button>
                                                
                                                <div>
                                                    <label class="block text-xs font-bold text-gray-700 mb-1">عنوان / متن نمایشی</label>
                                                    <input type="text" :name="`{{$key}}[custom_items][${index}][title]`" x-model="item.title" class="w-full border rounded p-2 text-sm">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-bold text-gray-700 mb-1">لینک مقصد</label>
                                                    <input type="text" :name="`{{$key}}[custom_items][${index}][link]`" x-model="item.link" dir="ltr" class="w-full border rounded p-2 text-sm" placeholder="https://...">
                                                </div>
                                                
                                                <div class="md:col-span-2 p-3 bg-white border rounded">
                                                    <label class="block text-xs font-bold text-gray-700 mb-2">تصویر آیتم</label>
                                                    <input type="file" :name="`{{$key}}[custom_items][${index}][image]`" @change="handleImageLoad($event, index)" class="text-sm w-full">
                                                    
                                                    <div class="mt-3 flex items-center gap-4 bg-gray-50 p-2 rounded" x-show="item.preview || item.image">
                                                        <img :src="item.preview || item.image" class="h-20 w-auto object-contain rounded border bg-white shadow-sm">
                                                        
                                                        <div class="text-xs text-gray-600 space-y-2" x-show="item.preview">
                                                            <p class="flex items-center gap-2"><span class="font-bold bg-gray-200 px-2 py-1 rounded">حجم:</span> <span x-text="item.fileSize" class="text-blue-600 font-bold"></span> KB</p>
                                                            <p class="flex items-center gap-2"><span class="font-bold bg-gray-200 px-2 py-1 rounded">ابعاد:</span> <span x-text="item.width" class="text-green-600 font-bold"></span> <span class="text-gray-400">x</span> <span x-text="item.height" class="text-green-600 font-bold"></span> px</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                        <p x-show="customItems.length === 0" class="text-sm text-gray-400 text-center py-4 border-2 border-dashed rounded">هیچ آیتم سفارشی اضافه نشده است.</p>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                    @endforeach
                </div>

                <div x-show="activeTab === 'info'" class="space-y-6" x-data="{ accordions: {{ json_encode($data['info_accordions']) }} }">
                    <h2 class="text-xl font-semibold mb-4 border-b pb-2">بخش اطلاعات (پایین صفحه)</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-4 border rounded">
                        <div><label class="text-sm">عنوان اصلی</label><input type="text" name="info_section[title]" value="{{ $data['info_section']['title'] ?? 'آیتم های این لوک' }}" class="w-full border p-2 rounded"></div>
                        <div><label class="text-sm">زیرعنوان</label><input type="text" name="info_section[subtitle]" value="{{ $data['info_section']['subtitle'] ?? '' }}" class="w-full border p-2 rounded"></div>
                        <div>
                            <label class="text-sm font-bold">عکس اول</label>
                            <div x-data="imageUploader('{{ $data['info_section']['image_1'] ?? '' }}')">
                                <input type="file" name="info_section[image_1]" @change="handleFile" class="w-full mt-1 text-sm">
                                <div class="mt-2 flex items-center gap-3 bg-white p-2 rounded border" x-show="preview">
                                    <img :src="preview" class="h-12 object-contain rounded border">
                                    <div class="text-[10px] text-gray-600" x-show="fileSize" x-cloak>
                                        <span class="block text-blue-600 font-bold" x-text="'حجم: ' + fileSize + ' KB'"></span>
                                        <span class="block text-green-600 font-bold" x-text="'ابعاد: ' + width + 'x' + height + ' px'"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="text-sm font-bold">عکس دوم</label>
                            <div x-data="imageUploader('{{ $data['info_section']['image_2'] ?? '' }}')">
                                <input type="file" name="info_section[image_2]" @change="handleFile" class="w-full mt-1 text-sm">
                                <div class="mt-2 flex items-center gap-3 bg-white p-2 rounded border" x-show="preview">
                                    <img :src="preview" class="h-12 object-contain rounded border">
                                    <div class="text-[10px] text-gray-600" x-show="fileSize" x-cloak>
                                        <span class="block text-blue-600 font-bold" x-text="'حجم: ' + fileSize + ' KB'"></span>
                                        <span class="block text-green-600 font-bold" x-text="'ابعاد: ' + width + 'x' + height + ' px'"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 border-t pt-4">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-bold">آکاردئون‌ها (تب‌های باز شونده)</h3>
                            <button type="button" @click="accordions.push({})" class="bg-blue-100 text-blue-700 px-3 py-1 rounded text-sm hover:bg-blue-200">+ افزودن تب جدید</button>
                        </div>
                        <div class="space-y-3">
                            <template x-for="(acc, index) in accordions" :key="index">
                                <div class="bg-white p-3 border rounded relative flex gap-4">
                                    <div class="w-1/4">
                                        <label class="text-xs">عنوان تب</label>
                                        <input type="text" :name="'info_accordions['+index+'][title]'" :value="acc.title" class="w-full border p-2 rounded">
                                    </div>
                                    <div class="w-2/4">
                                        <label class="text-xs">محتوا</label>
                                        <textarea :name="'info_accordions['+index+'][content]'" x-text="acc.content" rows="2" class="w-full border p-2 rounded"></textarea>
                                    </div>
                                    <div class="w-1/4 flex items-center justify-end">
                                        <button type="button" @click="accordions.splice(index, 1)" class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600">حذف تب</button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div x-show="activeTab === 'ribbon'" class="space-y-6">
                    <h2 class="text-xl font-semibold mb-4 border-b pb-2">تنظیمات نوار بالای سایت (Ribbon)</h2>
                    <div class="flex items-center mb-4">
                        <input type="hidden" name="ribbon[is_visible]" value="0">
                        <input type="checkbox" name="ribbon[is_visible]" value="1" {{ ($data['ribbon']['is_visible'] ?? 1) ? 'checked' : '' }} class="h-5 w-5 text-blue-600 rounded">
                        <label class="mr-3 font-medium text-gray-700">نمایش ریبون در سایت</label>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div><label class="block text-sm">متن اصلی</label><input type="text" name="ribbon[text]" value="{{ $data['ribbon']['text'] ?? '' }}" class="w-full border p-2 rounded"></div>
                        <div><label class="block text-sm">متن دکمه لینک</label><input type="text" name="ribbon[link_text]" value="{{ $data['ribbon']['link_text'] ?? '' }}" class="w-full border p-2 rounded"></div>
                        <div class="md:col-span-2"><label class="block text-sm">آدرس لینک</label><input type="text" name="ribbon[link_url]" value="{{ $data['ribbon']['link_url'] ?? '#' }}" dir="ltr" class="w-full border p-2 rounded"></div>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>
@endsection