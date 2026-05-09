@extends('admin.layouts.app')

@section('title', 'افزودن دسته بندی جدید')

@section('content')
    {{-- اضافه کردن Alpine.js برای مدیریت باز و بسته شدن درخت --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <h1 class="text-2xl font-semibold mb-4">افزودن دسته بندی جدید</h1>

    <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6" dir="rtl">
            <div class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">نام دسته‌بندی</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $category->name ?? '') }}" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label for="slug" class="block text-sm font-medium text-gray-700">اسلاگ (Slug)</label>
                    <input type="text" name="slug" id="slug" value="{{ old('slug', $category->slug ?? '') }}" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" dir="ltr">
                    @error('slug') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">دسته‌بندی والد</label>
                    
                    @php
                        // ۱. پیدا کردن والد انتخاب شده
                        $selectedParentId = old('parent_id', $category->parent_id ?? null);
                        
                        // ۲. تبدیل آبجکت‌ها به آرایه برای ساخت درخت
                        $catTree = [];
                        $catFlat = [];
                        foreach($categories as $c) {
                            $catFlat[$c->id] = [
                                'id'        => $c->id,
                                'name'      => $c->name,
                                'parent_id' => $c->parent_id,
                                'children'  => []
                            ];
                        }
                        
                        // ۳. پیدا کردن مسیر والدها برای باز ماندن خودکار فولدرها در درخت
                        $expandedIds = [];
                        if ($selectedParentId && isset($catFlat[$selectedParentId])) {
                            $pid = $catFlat[$selectedParentId]['parent_id'] ?? null;
                            while ($pid && isset($catFlat[$pid])) {
                                $expandedIds[] = $pid;
                                $pid = $catFlat[$pid]['parent_id'] ?? null;
                            }
                        }

                        // ۴. ساخت ساختار درختی تودرتو
                        foreach($catFlat as $id => &$node) {
                            if(!empty($node['parent_id']) && isset($catFlat[$node['parent_id']])) {
                                $catFlat[$node['parent_id']]['children'][] = &$node;
                            } else {
                                $catTree[$id] = &$node;
                            }
                        }
                    @endphp

                    <div class="bg-white border border-gray-300 rounded-md p-3 max-h-64 overflow-y-auto" 
                         x-data="{ expanded: {{ json_encode($expandedIds) }} }">
                        
                        {{-- گزینه پیش‌فرض: بدون والد --}}
                        <div class="flex items-center p-1.5 hover:bg-gray-50 rounded mb-2 border-b border-gray-100 pb-2">
                            <input type="radio" name="parent_id" value="" id="parent_none" 
                                   class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500 cursor-pointer" 
                                   {{ empty($selectedParentId) ? 'checked' : '' }}>
                            <label for="parent_none" class="mr-2 text-sm font-bold text-gray-700 cursor-pointer">
                                -- بدون والد (ایجاد به عنوان دسته اصلی) --
                            </label>
                        </div>

                        {{-- تابع بازگشتی برای رندر کردن درخت --}}
                        @if(!function_exists('renderCategoryParentTree'))
                            @php
                            function renderCategoryParentTree($nodes, $selectedId = null, $level = 0, $currentCatId = null) {
                                $html = '<ul class="'.($level > 0 ? 'mt-1 mr-4 border-r border-gray-200 pr-3' : 'space-y-1').'">';
                                
                                foreach($nodes as $node) {
                                    // جلوگیری از انتخاب دسته به عنوان والدِ خودش (در حالت ویرایش)
                                    if ($currentCatId && $node['id'] == $currentCatId) continue;

                                    $hasChildren = !empty($node['children']);
                                    $isChecked = ($selectedId == $node['id']) ? 'checked' : '';
                                    
                                    $html .= '<li class="mt-1">';
                                    $html .= '<div class="flex items-center justify-between p-1.5 hover:bg-gray-50 rounded transition-colors">';
                                    
                                    // دکمه رادیویی و لیبل
                                    $html .= '<div class="flex items-center">';
                                    $html .= '<input type="radio" name="parent_id" value="'.$node['id'].'" id="parent_'.$node['id'].'" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500 cursor-pointer" '.$isChecked.'>';
                                    $html .= '<label for="parent_'.$node['id'].'" class="mr-2 text-sm text-gray-700 cursor-pointer select-none">'.$node['name'].'</label>';
                                    $html .= '</div>';

                                    // دکمه باز و بسته کردن زیردسته‌ها
                                    if($hasChildren) {
                                        $html .= '<button type="button" @click.prevent="expanded.includes('.$node['id'].') ? expanded = expanded.filter(i => i !== '.$node['id'].') : expanded.push('.$node['id'].')" class="text-gray-400 hover:text-blue-600 focus:outline-none p-1">';
                                        $html .= '<svg class="w-4 h-4 transition-transform duration-200" :class="expanded.includes('.$node['id'].') ? \'-rotate-90\' : \'\'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>';
                                        $html .= '</button>';
                                    }
                                    
                                    $html .= '</div>';
                                    
                                    // فراخوانی مجدد تابع برای فرزندان
                                    if($hasChildren) {
                                        $html .= '<div x-show="expanded.includes('.$node['id'].')" x-transition style="display:none;">';
                                        $html .= renderCategoryParentTree($node['children'], $selectedId, $level + 1, $currentCatId);
                                        $html .= '</div>';
                                    }
                                    
                                    $html .= '</li>';
                                }
                                $html .= '</ul>';
                                return $html;
                            }
                            @endphp
                        @endif

                        @if(empty($catTree))
                            <p class="text-sm text-gray-500 text-center py-2">هنوز دسته‌بندی‌ای ساخته نشده است.</p>
                        @else
                            {!! renderCategoryParentTree($catTree, $selectedParentId, 0, $category->id ?? null) !!}
                        @endif

                    </div>
                    @error('parent_id') <span class="text-red-500 text-sm block mt-1">{{ $message }}</span> @enderror
                </div>
                <div class="flex items-center pt-2">
                    <input type="hidden" name="is_visible" value="0">
                    <input type="checkbox" name="is_visible" id="is_visible" value="1" 
                           {{ old('is_visible', $category->is_visible ?? true) ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 border-gray-300 rounded ml-2">
                    <label for="is_visible" class="text-sm font-medium text-gray-700">قابل مشاهده برای عموم</label>
                </div>
            </div>

            <div class="space-y-6">
                <div>
                    <div x-data="imageUploader()">
                        <label for="image" class="block text-sm font-medium text-gray-700">تصویر دسته‌بندی</label>
                        <input type="file" name="image" id="image" accept=".webp" @change="handleFile"
                            class="mt-1 block w-full text-sm text-gray-500
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-md file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-blue-50 file:text-blue-700
                                    hover:file:bg-blue-100">
                        <p class="text-xs text-gray-500 mt-1">فرمت مجاز: فقط WEBP. حداکثر حجم: 2MB</p>

                        <p x-show="error" x-text="error" class="text-red-500 text-sm mt-1" style="display: none;"></p>

                        <div x-show="preview" class="mt-4 p-4 border rounded-md bg-gray-50" style="display: none;">
                            <p class="text-sm font-medium text-gray-700 mb-3">پیش‌نمایش تصویر:</p>
                            <div class="flex items-start space-x-4 space-x-reverse">
                                <img :src="preview" class="h-24 w-24 object-cover rounded-md shadow-sm border border-gray-200">
                                <div class="text-sm text-gray-600 space-y-1">
                                    <p><span class="font-semibold text-gray-800">فرمت:</span> <span x-text="type" class="uppercase"></span></p>
                                    <p><span class="font-semibold text-gray-800">حجم:</span> <span x-text="size" dir="ltr"></span></p>
                                    <p><span class="font-semibold text-gray-800">ابعاد:</span> <span x-text="dimensions" dir="ltr"></span></p>
                                </div>
                            </div>
                        </div>

                        @error('image') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
                
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">توضیحات (داخلی)</label>
                    <textarea name="description" id="description" rows="3" 
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old('description', $category->description ?? '') }}</textarea>
                    @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                {{-- فیلدهای جدید آکاردئون --}}
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 space-y-4">
                    <h3 class="font-medium text-gray-800 border-b border-gray-200 pb-2">تنظیمات نمایشی صفحه دسته‌بندی (آکاردئون)</h3>
                    
                    <div>
                        <label for="accordion_title" class="block text-sm font-medium text-gray-700">عنوان آکاردئون</label>
                        <input type="text" name="accordion_title" id="accordion_title" value="{{ old('accordion_title', $category->accordion_title ?? '') }}" 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               placeholder="پیش‌فرض: نام دسته‌بندی">
                        <p class="text-xs text-gray-500 mt-1">اگر خالی بگذارید، از نام دسته‌بندی استفاده می‌شود.</p>
                        @error('accordion_title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="accordion_description" class="block text-sm font-medium text-gray-700">توضیحات آکاردئون</label>
                        <textarea name="accordion_description" id="accordion_description" rows="4" 
                                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="پیش‌فرض: توضیحات پیش فرض">{{ old('accordion_description', $category->accordion_description ?? '') }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">این متن در سایت با کلیک روی عنوان نمایش داده می‌شود.</p>
                        @error('accordion_description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 flex justify-end border-t border-gray-200 pt-6" dir="rtl">
            <button type="submit" class="px-8 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 shadow-md transition-colors">
                ایجاد دسته‌بندی
            </button>
        </div>

    </form>

    <script>
    function imageUploader() {
        return {
            preview: null,
            size: null,
            type: null,
            dimensions: null,
            error: null,
            handleFile(event) {
                const file = event.target.files[0];
                this.error = null;
                this.preview = null;
                this.size = null;
                this.type = null;
                this.dimensions = null;

                if (!file) return;

                // Validate WEBP format explicitly
                if (file.type !== 'image/webp' && !file.name.toLowerCase().endsWith('.webp')) {
                    this.error = 'فقط آپلود تصاویر با فرمت WEBP مجاز است.';
                    event.target.value = ''; // Reset the input
                    return;
                }

                // Calculate file size
                let sizeKB = file.size / 1024;
                this.size = sizeKB > 1024 ? (sizeKB / 1024).toFixed(2) + ' MB' : sizeKB.toFixed(2) + ' KB';
                
                // Set file type extension
                this.type = 'WEBP';

                // Read file to get preview and dimensions
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.preview = e.target.result;
                    
                    // Create an off-screen image element to read dimensions
                    const img = new Image();
                    img.onload = () => {
                        this.dimensions = img.width + ' × ' + img.height + ' px';
                    };
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        };
    }
</script>

@endsection