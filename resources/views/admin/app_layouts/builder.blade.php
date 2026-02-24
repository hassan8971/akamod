@extends('admin.layouts.app')

@section('title', 'ویژوال بیلدر - ' . $page->title)

@push('styles')
<link href="{{ asset('css/builder.css') }}" rel="stylesheet" />
@endpush

@section('content')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>\

<div x-data="pageBuilder()" class="h-[calc(100vh-35px)] flex flex-col dir-ltr text-right">
    
    <div class="bg-white dark:bg-dark-paper border-b dark:border-gray-700 px-6 py-3 flex justify-between items-center shadow-sm z-20">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.layouts.index') }}" class="text-gray-500 hover:text-gray-800 dark:text-gray-300">
                <i class="fas fa-arrow-right"></i>
            </a>
            <h1 class="font-bold text-lg text-gray-800 dark:text-white">طراحی صفحه: <span class="text-blue-600">{{ $page->title }}</span></h1>
            <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Live Editor</span>
        </div>
        <button @click="saveChanges()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-bold shadow transition flex items-center gap-2">
            <i class="fas fa-save"></i> ذخیره تغییرات
        </button>
    </div>

    <div class="w-full bg-white dark:bg-dark-paper p-4">
        <h3 class="font-bold text-gray-700 dark:text-gray-200 mb-4 text-right">ابزارها</h3>
        <p class="text-[10px] text-gray-400 mb-2">برای افزودن، بکشید و رها کنید</p>
        
        <div id="tools-list" class="space-y-2 flex gap-3">

            <x-admin.builder.element dataType="slider_main" dataTitle="اسلایدر">
                <x-slot:icon>
                    <x-icons.image />
                </x-slot:icon>
            </x-admin.builder.element>

            <x-admin.builder.element dataType="scroll_bar" dataTitle="اسکرول بار">
                <x-slot:icon>
                    <x-icons.image />
                </x-slot:icon>
            </x-admin.builder.element>

            <x-admin.builder.element dataType="list_horizontal" dataTitle="لیست افقی">
                <x-slot:icon>
                    <x-icons.list />
                </x-slot:icon>
            </x-admin.builder.element>

            <x-admin.builder.element dataType="list_horizontal_big" dataTitle="لیست افقی بزرگ">
                <x-slot:icon>
                    <x-icons.list />
                </x-slot:icon>
            </x-admin.builder.element>

            <x-admin.builder.element dataType="list_two" dataTitle="لیست دوتایی">
                <x-slot:icon>
                    <x-icons.list />
                </x-slot:icon>
            </x-admin.builder.element>

            <x-admin.builder.element dataType="single_product" dataTitle="تک محصول">
                <x-slot:icon>
                    <x-icons.list />
                </x-slot:icon>
            </x-admin.builder.element>

            <x-admin.builder.element dataType="banner_single" dataTitle="بنر ویترین">
                <x-slot:icon>
                    <x-icons.ad />
                </x-slot:icon>
            </x-admin.builder.element>
            

            <x-admin.builder.element dataType="mag_ad_banner" dataTitle="بنر تبلیغ">
                <x-slot:icon>
                    <x-icons.ad />
                </x-slot:icon>
            </x-admin.builder.element>

            <x-admin.builder.element dataType="mag_list_vertical" dataTitle="لیست عمودی">
                <x-slot:icon>
                    <x-icons.list />
                </x-slot:icon>
            </x-admin.builder.element>

            <x-admin.builder.element dataType="mag_video_banner" dataTitle="بنر ویدیویی">
                <x-slot:icon>
                    <x-icons.list />
                </x-slot:icon>
            </x-admin.builder.element>

            <x-admin.builder.element dataType="mag_update_banner" dataTitle="بنر آپدیت">
                <x-slot:icon>
                    <x-icons.list />
                </x-slot:icon>
            </x-admin.builder.element>

        </div>
    </div>

    <div class="flex-1 flex overflow-hidden bg-gray-100 dark:bg-dark-bg">
        
        <div class="w-80 bg-white dark:bg-dark-paper border-r dark:border-gray-700 overflow-y-auto p-4 transition-all" 
             x-show="selectedItem" 
             x-transition:enter="transition ease-out duration-300" 
             x-transition:enter-start="opacity-0 -translate-x-full">
            
            <div class="flex justify-between items-center mb-4 border-b pb-2 dark:border-gray-700">
                <h3 class="font-bold text-gray-700 dark:text-gray-200">تنظیمات</h3> <span x-text="selectedItem.type"></span>
                <button @click="selectedItem = null" class="text-gray-400 hover:text-red-500">
                    <x-icons.close />
                </button>
            </div>

            <template x-if="selectedItem">
                <div class="space-y-4">
                    
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">
                            <span x-text="selectedItem.is_tab ? 'عنوان تب' : 'عنوان بخش'"></span>
                        </label>
                        <input type="text" x-model="selectedItem.title" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-dark-bg dark:text-white rounded-md">
                    </div>

                    
                    <x-admin.builder.editor.slider-main />
                    <x-admin.builder.editor.scroll-bar />
                    <x-admin.builder.editor.list-horizontal />
                    <x-admin.builder.editor.list-horizontal-big />
                    <x-admin.builder.editor.list-two />
                    <x-admin.builder.editor.single-product />
                    <x-admin.builder.editor.banner-single />

                    <!-- MAG -->
                    <x-admin.builder.editor.mag-list-vertical />
                    <x-admin.builder.editor.mag-video-banner />
                    <x-admin.builder.editor.mag-update-banner />
                    <x-admin.builder.editor.mag-ad-banner />
                    <!-- MAG -->


                    <template x-if="selectedItem.is_tab">
                        <div class="space-y-4 p-4 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg border border-indigo-100 dark:border-indigo-800"
                            x-data="{ tabMode: selectedItem.image ? 'image' : 'font' }">
                            
                            <div>
                                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-2">نوع نمایش</label>
                                <div class="flex bg-white dark:bg-dark-bg rounded-lg p-1 border dark:border-gray-600">
                                    <button @click="tabMode = 'font'; selectedItem.image = null; selectedItem.image_path = null" 
                                            :class="{'bg-indigo-100 text-indigo-700': tabMode === 'font', 'text-gray-500': tabMode !== 'font'}"
                                            class="flex-1 py-1.5 text-xs font-bold rounded transition">
                                        <i class="fas fa-font ml-1"></i> فونت
                                    </button>
                                    <button @click="tabMode = 'image'" 
                                            :class="{'bg-indigo-100 text-indigo-700': tabMode === 'image', 'text-gray-500': tabMode !== 'image'}"
                                            class="flex-1 py-1.5 text-xs font-bold rounded transition">
                                        <i class="fas fa-image ml-1"></i> تصویر
                                    </button>
                                </div>
                            </div>

                            <div x-show="tabMode === 'font'">
                                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">کلاس آیکون</label>
                                <div class="flex gap-2">
                                    <input type="text" x-model="selectedItem.icon" placeholder="fas fa-home" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-dark-bg dark:text-white rounded-md font-mono dir-ltr text-left">
                                    <div class="w-9 h-9 bg-white dark:bg-dark-hover border rounded flex items-center justify-center text-blue-600">
                                        <i :class="selectedItem.icon"></i>
                                    </div>
                                </div>
                            </div>

                            <div x-show="tabMode === 'image'">
                                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">آپلود آیکون</label>
                                
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-white border rounded-lg flex items-center justify-center overflow-hidden relative cursor-pointer hover:border-blue-500 transition"
                                        @click="$refs.tabImageInput.click()">
                                        
                                        <template x-if="selectedItem.image">
                                            <img :src="selectedItem.image" class="w-8 h-8 object-contain">
                                        </template>
                                        
                                        <template x-if="!selectedItem.image">
                                            <i class="fas fa-plus text-gray-300"></i>
                                        </template>
                                        
                                        <div class="absolute inset-0 bg-black/40 opacity-0 hover:opacity-100 flex items-center justify-center text-white text-xs">
                                            <i class="fas fa-pen"></i>
                                        </div>
                                    </div>

                                    <div class="flex-1">
                                        <button @click="$refs.tabImageInput.click()" class="text-xs text-blue-600 font-bold hover:underline">
                                            انتخاب تصویر جدید
                                        </button>
                                        <p class="text-[10px] text-gray-400 mt-1">سایز پیشنهادی: 64x64 پیکسل</p>
                                    </div>

                                    <input type="file" x-ref="tabImageInput" class="hidden" @change="uploadTabIcon($event)">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">لینک مقصد (Slug)</label>
                                <input type="text" x-model="selectedItem.link" placeholder="home" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-dark-bg dark:text-white rounded-md dir-ltr text-left">
                            </div>
                            
                            <div class="pt-2 border-t dark:border-indigo-800 mt-2">
                                <button @click="deleteTab(selectedItem)" class="w-full text-red-500 text-xs py-2 hover:bg-red-50 rounded transition flex items-center justify-center gap-1">
                                    <i class="fas fa-trash"></i> حذف این تب
                                </button>
                            </div>
                        </div>
                    </template>
                    
                    <template x-if="!selectedItem.is_tab">
                        <div class="pt-4 border-t dark:border-gray-700">
                            <button @click="deleteItem(selectedItem)" class="w-full text-red-600 border border-red-200 hover:bg-red-50 dark:hover:bg-red-900/20 py-2 rounded-md text-sm transition">
                                <i class="fas fa-trash"></i> حذف این بخش
                            </button>
                        </div>
                    </template>
                </div>
            </template>
        </div>

        <div class="flex-1 flex flex-col justify-center items-center pt-2 overflow-auto relative bg-dot-pattern">

            
    
            <div class="mobile-wrapper">
                
                <div class="mobile-screen">
                    

                    <div id="canvas-area" class="mobile-content relative">
                        
                        <div id="sortable-sections" class="pb-4 min-h-[200px]">
                            <template x-for="section in sections" :key="section.id">
                                <div class="editable-element relative group" 
                                        :class="{'is-selected': selectedItem && selectedItem.id === section.id}"
                                        @click.stop="selectItem(section)">
                                    
                                    <x-admin.builder.sortable.slider-main />
                                    <x-admin.builder.sortable.scroll-bar />
                                    <x-admin.builder.sortable.list-horizontal />
                                    <x-admin.builder.sortable.list-horizontal-big />
                                    <x-admin.builder.sortable.list-two />
                                    <x-admin.builder.sortable.single-product />
                                    <x-admin.builder.sortable.banner-single />

                                    <!-- MAG -->
                                    <x-admin.builder.sortable.mag-update-banner />
                                    <x-admin.builder.sortable.mag-video-banner />
                                    <x-admin.builder.sortable.mag-ad-banner />
                                    <x-admin.builder.sortable.mag-list-vertical />
                                    <!-- END MAG -->

                                    <button @click.stop="deleteItem(section)" class="absolute -top-2 -right-2 bg-red-500 text-white w-5 h-5 rounded-full text-xs hidden group-hover:flex items-center justify-center z-50 shadow">
                                        <x-icons.close />
                                    </button>
                                </div>
                                </template>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        
    </div>

    <div x-show="showAppSelector" class="fixed inset-0 z-[100] overflow-y-auto" x-cloak>
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showAppSelector = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            
            <template x-if="selectedItem && selectedItem.config">
                
                <div class="relative w-full max-w-2xl bg-white dark:bg-dark-paper rounded-2xl shadow-2xl border dark:border-gray-700">
                    <div class="p-4 border-b dark:border-gray-700 flex justify-between">
                        <h3 class="font-bold text-gray-800 dark:text-white">انتخاب اپلیکیشن‌ها</h3>
                        <button @click="showAppSelector = false"><i class="fas fa-times"></i></button>
                    </div>
                    
                    <div class="p-4 border-b dark:border-gray-700">
                        <input x-model="manualSearch" type="text" placeholder="جستجو..." class="w-full rounded-xl border-gray-300 bg-dark-bg text-white">
                    </div>
                    <div class="max-h-[400px] overflow-y-auto p-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                        <template x-for="product in allProducts.filter(p => p.name.toLowerCase().includes(manualSearch.toLowerCase()))" :key="product.id">
                            <label class="flex items-center gap-3 p-3 rounded-xl border dark:border-gray-700 cursor-pointer hover:bg-blue-50 dark:hover:bg-blue-900/20" 
                                   :class="{
                                       'border-blue-500 bg-blue-50 dark:bg-blue-900/20': 
                                       selectedItem.config.manual_ids && selectedItem.config.manual_ids.includes(product.id)
                                   }">
                                
                                <input type="checkbox" :value="product.id" x-model="selectedItem.config.manual_ids" class="rounded text-blue-600">
                                <img :src="product.images" class="w-10 h-10 rounded-lg object-cover">
                                <div class="flex-1 text-right">
                                    <div class="text-sm font-bold" x-text="product.name"></div>
                                    <div class="text-xs text-gray-500" x-text="product.variants.price"></div>
                                </div>
                            </label>
                        </template>
                    </div>
                    
                    <div class="p-4 flex justify-end bg-gray-50 dark:bg-dark-hover rounded-b-2xl">
                        <button @click="showAppSelector = false" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold">
                            تایید (<span x-text="selectedItem.config.manual_ids ? selectedItem.config.manual_ids.length : 0"></span>)
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>

</div>


<script>
    function pageBuilder() {
        return {
            sections: @json($page->sections),
            pageSlug: '{{ $page->slug }}', // اسلاگ صفحه فعلی برای تشخیص تب فعال
            selectedItem: null,
            showAppSelector: false,
            manualSearch: '',
            
            allProducts: {!! $products->map(fn($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'images' => Storage::url($p->images->first()->path),
                'variants' => $p->variants->first(),
            ])->toJson() !!},

            

            init() {
                // 1. تنظیم لیست وسط (Canvas)
                Sortable.create(document.getElementById('sortable-sections'), {
                    group: {
                        name: 'builder-group', // نام گروه مشترک
                        pull: true,
                        put: true // اجازه دریافت آیتم
                    },
                    animation: 150,
                    ghostClass: 'opacity-50',
                    draggable: '.editable-element', // فقط سکشن‌ها قابل جابجایی باشند
                    
                    // وقتی آیتمی از ابزارها به اینجا انداخته شد
                    onAdd: (evt) => {
                        // 1. نوع ابزار را از اتریبیوت HTML می‌خوانیم
                        const type = evt.item.getAttribute('data-type');
                        
                        // 2. المان HTML که Sortable ساخته را حذف می‌کنیم (چون Alpine خودش می‌سازد)
                        evt.item.remove();
                        
                        // 3. داده را به آرایه اضافه می‌کنیم (در ایندکس رها شده)
                        if (type) {
                            this.addSectionAt(type, evt.newIndex);
                        }
                    },

                    // وقتی آیتم‌های داخلی جابجا شدند (Reorder)
                    onEnd: (evt) => {
                        if (evt.from === evt.to) { // فقط اگر جابجایی داخلی بود
                            const rawSections = Alpine.raw(this.sections);
                            const item = rawSections[evt.oldIndex];
                            rawSections.splice(evt.oldIndex, 1);
                            rawSections.splice(evt.newIndex, 0, item);
                        }
                    }
                });

                // 2. تنظیم لیست ابزارها (Sidebar)
                Sortable.create(document.getElementById('tools-list'), {
                    group: {
                        name: 'builder-group',
                        pull: 'clone', // کپی کردن به جای جابجایی
                        put: false // اجازه نده چیزی برگردد به اینجا
                    },
                    sort: false, // اجازه نده ابزارها جابجا شوند
                    animation: 150
                });
            },

            // تابع جدید برای افزودن در موقعیت خاص (Drop)
            addSectionAt(type, index) {
                const newId = 'new_' + Date.now();
                let config = {};
                
                if(type === 'slider_main') config = { slides: [] };
                if(type === 'scroll_bar') config = { slides: [] };
                if(type === 'list_horizontal') config = { limit: 10, sort_type: 'newest', manual_ids: [] };
                if(type === 'list_horizontal_big') config = { limit: 12, sort_type: 'newest', manual_ids: [] };
                if(type === 'list_two') config = { limit: 2, sort_type: 'newest', manual_ids: [] };
                if(type === 'single_product') config = { limit: 1, sort_type: 'newest', manual_ids: [] };
                if(type === 'banner_single') config = { image: null, link: '' };
                if(type === 'mag_ad_banner') config = { image: null, icon: null, link: '' };
                if(type === 'mag_list_vertical') config = { limit: 10, sort_type: 'newest', manual_ids: [] };
                if(type === 'mag_video_banner') config = { limit: 1, sort_type: 'newest', manual_ids: [], title : '', image: null, video: ''};
                if(type === 'mag_update_banner') config = { limit: 1, sort_type: 'newest', manual_ids: [], title : '', image: null, video: ''};

                const newSection = {
                    id: newId, 
                    type: type, 
                    title: 'بخش جدید',
                    source_type: 'auto', 
                    config: config, 
                    is_new: true
                };

                // اضافه کردن به آرایه در ایندکس مشخص شده
                // اگر ایندکس تعریف نشده بود (کلیک معمولی)، به ته لیست اضافه کن
                if (index !== undefined && index !== null) {
                    this.sections.splice(index, 0, newSection);
                } else {
                    this.sections.push(newSection);
                    this.$nextTick(() => {
                        const c = document.getElementById('canvas-area'); c.scrollTop = c.scrollHeight;
                    });
                }
            },

            // --- تابع جدید: تشخیص تب فعال ---
            isTabActive(item) {
                // اگر لینک منو دقیقاً برابر با اسلاگ صفحه فعلی باشد، فعال است
                // مثلا home == home
                // نکته: اسلش‌های اول لینک را حذف می‌کنیم تا مقایسه دقیق‌تر باشد
                const link = (item.link_url || '').replace(/^\//, '');
                return link === this.pageSlug;
            },

            addSection(type) {
                const newId = 'new_' + Date.now();
                let config = {};
                if(type === 'slider_main') config = { slides: [] };
                if(type === 'scroll_bar') config = { slides: [] };
                if(type === 'list_horizontal') config = { limit: 10, sort_type: 'newest', manual_ids: [] };
                if(type === 'list_horizontal_big') config = { limit: 12, sort_type: 'newest', manual_ids: [] };
                if(type === 'list_two') config = { limit: 2, sort_type: 'newest', manual_ids: [] };
                if(type === 'single_product') config = { limit: 1, sort_type: 'newest', manual_ids: [] };
                if(type === 'banner_single') config = { image: null, link: '' };
                if(type === 'mag_ad_banner') config = { image: null, icon: nulll, link: '' };
                if(type === 'mag_list_vertical') config = { limit: 10, sort_type: 'newest', manual_ids: [] };
                if(type === 'mag_video_banner') config = { limit: 1, sort_type: 'newest', manual_ids: [], title : '', image: null, video: ''};
                if(type === 'mag_update_banner') config = { limit: 1, sort_type: 'newest', manual_ids: [], title : '', image: null, video: ''};

                this.sections.push({
                    id: newId, type: type, title: 'بخش جدید',
                    source_type: 'auto', config: config, is_new: true
                });
                
                this.$nextTick(() => {
                    const c = document.getElementById('canvas-area'); c.scrollTop = c.scrollHeight;
                });
            },

            selectItem(item, isTab = false) {
                this.selectedItem = item;
                this.selectedItem.is_tab = isTab;
                
                // FIX: همیشه مطمئن شویم config وجود دارد تا ارور undefined ندهد
                if (!this.selectedItem.config) {
                    this.selectedItem.config = {};
                }

                if(!isTab) {
                    if(item.type === 'slider_main' && !this.selectedItem.config.slides) this.selectedItem.config.slides = [];
                    // حل مشکل تیک خوردن همه: آرایه manual_ids را حتما بساز
                    if((item.type === 'list_horizontal' || item.type === 'mag_list_vertical' || item.type === 'grid_categories') && !this.selectedItem.config.manual_ids) {
                        this.selectedItem.config.manual_ids = [];
                    }
                } else {
                    // برای تب‌ها، اگر لینک خالی بود، از link_url کپی کن
                     if(!this.selectedItem.link && this.selectedItem.link_url) {
                        this.selectedItem.link = this.selectedItem.link_url;
                    }
                }
            },

            deleteItem(item) {
                if(!confirm('حذف شود؟')) return;
                this.sections = this.sections.filter(s => s.id !== item.id);
                this.selectedItem = null;
            },

            getPreviewApps(section) {
                if (!section.config) return [];
                
                let apps = [];
                
                if (section.source_type === 'manual') {
                    // اطمینان از اینکه manual_ids یک آرایه است
                    const rawIds = Array.isArray(section.config.manual_ids) ? section.config.manual_ids : [];
                    
                    // تبدیل همه IDها به عدد صحیح برای مقایسه دقیق
                    const ids = rawIds.map(id => parseInt(id)); 
                    
                    // فیلتر کردن محصولات
                    apps = this.allProducts.filter(p => ids.includes(parseInt(p.id)));
                    
                } else {
                    // منطق خودکار (بدون تغییر)
                    let sorted = [...this.allProducts];
                    const sortType = section.config.sort_type || 'newest';
                    
                    if (sortType === 'newest') sorted.sort((a, b) => b.id - a.id);
                    else if (sortType === 'popular') sorted.sort((a, b) => b.rating - a.rating);
                    else if (sortType === 'most_downloaded') sorted.sort((a, b) => b.download_count - a.download_count);
                    
                    apps = sorted.slice(0, section.config.limit || 10);
                }
                
                return apps;
            },

            async uploadImage(file) {
                const formData = new FormData();
                formData.append('image', file);
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                try {
                    const res = await fetch('{{ route("admin.layouts.upload_image") }}', {
                        method: 'POST', body: formData, headers: { 'X-CSRF-TOKEN': token }
                    });
                    const data = await res.json();
                    return data.success ? data.url : null;
                } catch(e) { return null; }
            },

            async uploadVideo(file) {
                const formData = new FormData();
                formData.append('video', file);
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                try {
                    const res = await fetch('{{ route("admin.layouts.upload_video") }}', {
                        method: 'POST', body: formData, headers: { 'X-CSRF-TOKEN': token }
                    });
                    const data = await res.json();
                    return data.success ? data.url : null;
                } catch(e) { return null; }
            },

            async uploadSlideImage(event, index) {
                const file = event.target.files[0];
                if(!file) return;
                const url = await this.uploadImage(file);
                if(url) this.selectedItem.config.slides[index].image = url;
            },

            async uploadBannerImage(event) {
                const file = event.target.files[0];
                if(!file) return;
                const url = await this.uploadImage(file);
                if(url) this.selectedItem.config.image = url;
            },

            async uploadBannerVideo(event) {
                const file = event.target.files[0];
                if(!file) return;
                const url = await this.uploadVideo(file);
                if(url) this.selectedItem.config.video = url;
            },

            async uploadBannerIcon(event) {
                const file = event.target.files[0];
                if(!file) return;
                const url = await this.uploadImage(file);
                if(url) this.selectedItem.config.icon = url;
            },

            async uploadBannerAppIcon(event) {
                const file = event.target.files[0];
                if(!file) return;
                const url = await this.uploadImage(file);
                if(url) this.selectedItem.config.appIcon = url;
            },
            async removeBannerAppIcon(event) {
                this.selectedItem.config.appIcon = '';
            },
            
            openAppSelector() { this.showAppSelector = true; },

            // باقی متدها (addSlide, removeSlide, saveChanges) دقیقاً مثل قبل...
            addSlide() {
                if(!this.selectedItem.config.slides) this.selectedItem.config.slides = [];
                this.selectedItem.config.slides.push({ image: null, link: '' });
            },
            removeSlide(index) {
                this.selectedItem.config.slides.splice(index, 1);
            },
            saveChanges() {
                const payload = { sections: this.sections };
                fetch('{{ route("admin.layouts.save_all", $page->id) }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify(payload)
                })
                .then(res => res.json())
                .then(data => { alert('ذخیره شد!'); window.location.reload(); })
                .catch(err => alert('خطا در ذخیره'));
            },

            async uploadTabIcon(event) {
                const file = event.target.files[0];
                if(!file) return;

                // استفاده از همان اندپوینت آپلود تصویر که قبلاً داشتیم
                const formData = new FormData();
                formData.append('image', file);
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                try {
                    const res = await fetch('{{ route("admin.layouts.upload_image") }}', {
                        method: 'POST', body: formData, headers: { 'X-CSRF-TOKEN': token }
                    });
                    const data = await res.json();
                    
                    if(data.success) {
                        // آپدیت کردن آبجکت انتخابی با لینک جدید و مسیر جدید
                        this.selectedItem.image = data.url;      // برای نمایش
                        this.selectedItem.image_path = data.path; // برای ذخیره در دیتابیس
                    }
                } catch(e) { 
                    alert('خطا در آپلود تصویر');
                }
            },

            

            showTabCreator: false,
                newTabForm: {
                    title: '',
                    link: '',
                    iconType: 'font',
                    icon: 'fas fa-home',
                    image: null,
                    sort_order: null
                },

                // باز کردن پاپ‌آپ روی جایگاه خالی
                openTabCreator(orderIndex) {
                    this.newTabForm = {
                        title: '',
                        link: '', // می‌توانیم به صورت هوشمند slug صفحه جاری را پیشنهاد دهیم
                        iconType: 'font',
                        icon: 'fas fa-home',
                        image: null,
                        sort_order: orderIndex // ترتیب بر اساس جایگاهی که کلیک شده
                    };
                    this.showTabCreator = true;
                },

                // هندل کردن آپلود عکس در فرم جدید
                handleNewTabImage(event) {
                    const file = event.target.files[0];
                    if(file) this.newTabForm.image = file;
                },

        }
    }
</script>
@endsection