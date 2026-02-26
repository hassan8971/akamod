@extends('admin.layouts.app')
@section('title', 'تنظیمات جامع صفحه اصلی')

@section('content')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div dir="rtl" class="max-w-7xl mx-auto pb-10">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">ویرایشگر صفحه اصلی (Home Page)</h1>
        <button type="submit" form="homepage-form" class="px-8 py-3 bg-green-600 text-white font-bold rounded-lg shadow-lg hover:bg-green-700 transition">
            ذخیره کل تغییرات
        </button>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-r-4 border-green-500 text-green-700 p-4 mb-4 rounded">{{ session('success') }}</div>
    @endif

    <form id="homepage-form" action="{{ route('admin.homepage.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div x-data="{ activeTab: 'slider' }" class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200">
            <div class="flex border-b border-gray-200 bg-gray-50 overflow-x-auto sticky top-0 z-10">
                <button type="button" @click="activeTab = 'slider'" :class="activeTab === 'slider' ? 'border-blue-500 text-blue-600 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-5 py-4 border-b-2 font-medium text-sm transition">اسلایدر اصلی</button>
                <button type="button" @click="activeTab = 'category'" :class="activeTab === 'category' ? 'border-blue-500 text-blue-600 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-5 py-4 border-b-2 font-medium text-sm transition">بخش دسته‌بندی (گرید)</button>
                <button type="button" @click="activeTab = 'banners'" :class="activeTab === 'banners' ? 'border-blue-500 text-blue-600 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-5 py-4 border-b-2 font-medium text-sm transition">بنرها و اصالت</button>
                <button type="button" @click="activeTab = 'carousels'" :class="activeTab === 'carousels' ? 'border-blue-500 text-blue-600 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-5 py-4 border-b-2 font-medium text-sm transition">متن کاروسل‌ها</button>
                <button type="button" @click="activeTab = 'info'" :class="activeTab === 'info' ? 'border-blue-500 text-blue-600 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-5 py-4 border-b-2 font-medium text-sm transition">بخش اطلاعات و آکاردئون</button>
                <button type="button" @click="activeTab = 'ribbon'" :class="activeTab === 'ribbon' ? 'border-blue-500 text-blue-600 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-5 py-4 border-b-2 font-medium text-sm transition">نوار ریبون</button>
            </div>

            <div class="p-6 bg-white min-h-[500px]">

                <div x-show="activeTab === 'slider'" x-data="{ slides: {{ json_encode($data['main_slider']) }} }">
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
                                        <input type="file" :name="'main_slider['+index+'][image]'" class="mt-2 text-sm w-full">
                                        <template x-if="slide.image"><img :src="slide.image" class="h-16 mt-2 rounded"></template>
                                    </div>
                                    <div class="p-3 bg-white border rounded">
                                        <label class="block text-sm font-bold">عکس بج شناور</label>
                                        <input type="file" :name="'main_slider['+index+'][badge_img]'" class="mt-2 text-sm w-full">
                                        <template x-if="slide.badge_img"><img :src="slide.badge_img" class="h-16 mt-2 rounded"></template>
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
                                <label class="block text-xs mb-1">لینک تصویر</label>
                                <input type="text" :name="'category_grid['+index+'][link_url]'" :value="item.link_url" dir="ltr" class="w-full text-xs border rounded p-1 mb-2" placeholder="URL اختیاری">
                                <input type="file" :name="'category_grid['+index+'][image]'" class="w-full text-xs">
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
                                <input type="file" name="middle_images[image_1]">
                                @if(!empty($data['middle_images']['image_1'])) <img src="{{ $data['middle_images']['image_1'] }}" class="mt-2 h-24 rounded"> @endif
                            </div>
                            <div class="border p-4 rounded bg-gray-50">
                                <label class="block font-bold">بنر چپ</label>
                                <input type="text" name="middle_images[link_2]" value="{{ $data['middle_images']['link_2'] ?? '#' }}" class="w-full border rounded p-2 my-2 text-sm" dir="ltr" placeholder="لینک بنر">
                                <input type="file" name="middle_images[image_2]">
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
                                <input type="file" name="authentic_section[big_card][image]" class="text-xs w-full">
                                @if(!empty($data['authentic_section']['big_card']['image'])) <img src="{{ $data['authentic_section']['big_card']['image'] }}" class="h-16 mt-1 rounded"> @endif
                            </div>
                            <div class="border p-3 rounded">
                                <label class="font-bold text-sm">کارت کوچک 1</label>
                                <input type="text" name="authentic_section[small_card_1][title]" value="{{ $data['authentic_section']['small_card_1']['title'] ?? '' }}" class="w-full border p-1 my-1 text-xs" placeholder="عنوان کارت">
                                <input type="text" name="authentic_section[small_card_1][link]" value="{{ $data['authentic_section']['small_card_1']['link'] ?? '' }}" class="w-full border p-1 mb-2 text-xs" dir="ltr" placeholder="لینک مقصد">
                                <input type="file" name="authentic_section[small_card_1][image]" class="text-xs w-full">
                                @if(!empty($data['authentic_section']['small_card_1']['image'])) <img src="{{ $data['authentic_section']['small_card_1']['image'] }}" class="h-16 mt-1 rounded"> @endif
                            </div>
                            <div class="border p-3 rounded">
                                <label class="font-bold text-sm">کارت کوچک 2</label>
                                <input type="text" name="authentic_section[small_card_2][title]" value="{{ $data['authentic_section']['small_card_2']['title'] ?? '' }}" class="w-full border p-1 my-1 text-xs" placeholder="عنوان کارت">
                                <input type="text" name="authentic_section[small_card_2][link]" value="{{ $data['authentic_section']['small_card_2']['link'] ?? '' }}" class="w-full border p-1 mb-2 text-xs" dir="ltr" placeholder="لینک مقصد">
                                <input type="file" name="authentic_section[small_card_2][image]" class="text-xs w-full">
                                @if(!empty($data['authentic_section']['small_card_2']['image'])) <img src="{{ $data['authentic_section']['small_card_2']['image'] }}" class="h-16 mt-1 rounded"> @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div x-show="activeTab === 'carousels'" class="space-y-8">
                    @foreach(['carousel_1' => 'کاروسل اول (ساده)', 'carousel_2' => 'کاروسل دوم (با پس‌زمینه)', 'carousel_3' => 'کاروسل سوم (با عکس کناری)'] as $key => $title)
                    <div class="bg-gray-50 p-5 rounded-lg border border-gray-200">
                        <h3 class="font-bold text-lg mb-4 text-blue-600">{{ $title }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div><label class="block text-sm">عنوان بزرگ</label><input type="text" name="{{$key}}[title]" value="{{ $data[$key]['title'] ?? '' }}" class="w-full border rounded p-2"></div>
                            <div><label class="block text-sm">زیر عنوان</label><input type="text" name="{{$key}}[subtitle]" value="{{ $data[$key]['subtitle'] ?? '' }}" class="w-full border rounded p-2"></div>
                            <div><label class="block text-sm">متن نشان/بج</label><input type="text" name="{{$key}}[badge]" value="{{ $data[$key]['badge'] ?? '' }}" class="w-full border rounded p-2"></div>
                            <div><label class="block text-sm">متن دکمه (مشاهده همه)</label><input type="text" name="{{$key}}[link_text]" value="{{ $data[$key]['link_text'] ?? '' }}" class="w-full border rounded p-2"></div>
                            <div class="md:col-span-2"><label class="block text-sm">آدرس لینک مشاهده همه</label><input type="text" name="{{$key}}[link_url]" value="{{ $data[$key]['link_url'] ?? '#' }}" dir="ltr" class="w-full border rounded p-2"></div>
                            
                            @if($key === 'carousel_2')
                                <div class="md:col-span-2 bg-white p-3 border rounded mt-2">
                                    <label class="block text-sm font-bold">عکس پس‌زمینه کاروسل دوم</label>
                                    <input type="file" name="{{$key}}[background_image]" class="mt-1">
                                    @if(!empty($data[$key]['background_image'])) <img src="{{ $data[$key]['background_image'] }}" class="h-20 mt-2 rounded"> @endif
                                </div>
                            @elseif($key === 'carousel_3')
                                <div class="md:col-span-2 bg-white p-3 border rounded mt-2">
                                    <label class="block text-sm font-bold">عکس کناری کاروسل سوم</label>
                                    <input type="file" name="{{$key}}[side_image]" class="mt-1">
                                    @if(!empty($data[$key]['side_image'])) <img src="{{ $data[$key]['side_image'] }}" class="h-20 mt-2 rounded"> @endif
                                </div>
                            @endif
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
                            <input type="file" name="info_section[image_1]" class="w-full mt-1 text-sm">
                            @if(!empty($data['info_section']['image_1'])) <img src="{{ $data['info_section']['image_1'] }}" class="h-16 mt-1 rounded"> @endif
                        </div>
                        <div>
                            <label class="text-sm font-bold">عکس دوم</label>
                            <input type="file" name="info_section[image_2]" class="w-full mt-1 text-sm">
                            @if(!empty($data['info_section']['image_2'])) <img src="{{ $data['info_section']['image_2'] }}" class="h-16 mt-1 rounded"> @endif
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