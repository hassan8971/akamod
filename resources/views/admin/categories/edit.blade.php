@extends('admin.layouts.app')

@section('title', 'ویرایش دسته: ' . $category->name)

@section('content')
    {{-- اضافه کردن Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <div class="flex justify-between items-center mb-4" dir="rtl">
        <h1 class="text-2xl font-semibold">ویرایش دسته: {{ $category->name }}</h1>
        <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 text-gray-600 rounded-lg hover:bg-gray-100">
            &rarr; بازگشت به لیست
        </a>
    </div>

    <form id="update-form" action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6" dir="rtl">
            
            <div class="md:col-span-2 space-y-6">
                <div class="bg-white shadow-md rounded-lg p-6 space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">نام دسته‌بندی</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label for="slug" class="block text-sm font-medium text-gray-700">اسلاگ (Slug)</label>
                        <input type="text" name="slug" id="slug" value="{{ old('slug', $category->slug) }}" 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" dir="ltr">
                        @error('slug') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">توضیحات (داخلی)</label>
                        <textarea name="description" id="description" rows="3" 
                                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old('description', $category->description) }}</textarea>
                        @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- فیلدهای جدید آکاردئون --}}
                <div class="bg-white shadow-md rounded-lg p-6 space-y-4">
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

            <div class="md:col-span-1 space-y-6">
                
                <div class="bg-white shadow-md rounded-lg p-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">دسته‌بندی والد</label>
                    
                    @php
                        $selectedParentId = old('parent_id', $category->parent_id ?? null);
                        $currentCatId = $category->id; // آیدی دسته در حال ویرایش
                        
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
                        
                        // باز ماندن مسیر دسته انتخاب شده
                        $expandedIds = [];
                        if ($selectedParentId && isset($catFlat[$selectedParentId])) {
                            $pid = $catFlat[$selectedParentId]['parent_id'] ?? null;
                            while ($pid && isset($catFlat[$pid])) {
                                $expandedIds[] = $pid;
                                $pid = $catFlat[$pid]['parent_id'] ?? null;
                            }
                        }

                        // ساخت ساختار درختی
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
                        
                        <div class="flex items-center p-1.5 hover:bg-gray-50 rounded mb-2 border-b border-gray-100 pb-2">
                            <input type="radio" name="parent_id" value="" id="parent_none" 
                                   class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500 cursor-pointer" 
                                   {{ empty($selectedParentId) ? 'checked' : '' }}>
                            <label for="parent_none" class="mr-2 text-sm font-bold text-gray-700 cursor-pointer">
                                -- بدون والد (سطح بالا) --
                            </label>
                        </div>

                        @if(!function_exists('renderCategoryParentTree'))
                            @php
                            function renderCategoryParentTree($nodes, $selectedId = null, $level = 0, $currentCatId = null) {
                                $html = '<ul class="'.($level > 0 ? 'mt-1 mr-4 border-r border-gray-200 pr-3' : 'space-y-1').'">';
                                
                                foreach($nodes as $node) {
                                    // محافظت حیاتی: جلوگیری از انتخاب خود یا فرزندان به عنوان والد
                                    if ($currentCatId && $node['id'] == $currentCatId) continue;

                                    $hasChildren = !empty($node['children']);
                                    $isChecked = ($selectedId == $node['id']) ? 'checked' : '';
                                    
                                    $html .= '<li class="mt-1">';
                                    $html .= '<div class="flex items-center justify-between p-1.5 hover:bg-gray-50 rounded transition-colors">';
                                    
                                    $html .= '<div class="flex items-center">';
                                    $html .= '<input type="radio" name="parent_id" value="'.$node['id'].'" id="parent_'.$node['id'].'" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500 cursor-pointer" '.$isChecked.'>';
                                    $html .= '<label for="parent_'.$node['id'].'" class="mr-2 text-sm text-gray-700 cursor-pointer select-none">'.$node['name'].'</label>';
                                    $html .= '</div>';

                                    if($hasChildren) {
                                        $html .= '<button type="button" @click.prevent="expanded.includes('.$node['id'].') ? expanded = expanded.filter(i => i !== '.$node['id'].') : expanded.push('.$node['id'].')" class="text-gray-400 hover:text-blue-600 focus:outline-none p-1">';
                                        $html .= '<svg class="w-4 h-4 transition-transform duration-200" :class="expanded.includes('.$node['id'].') ? \'-rotate-90\' : \'\'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>';
                                        $html .= '</button>';
                                    }
                                    
                                    $html .= '</div>';
                                    
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

                        {!! renderCategoryParentTree($catTree, $selectedParentId, 0, $currentCatId) !!}

                    </div>
                    @error('parent_id') <span class="text-red-500 text-sm block mt-1">{{ $message }}</span> @enderror
                </div>
                <div class="bg-white shadow-md rounded-lg p-6">
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-2">تصویر دسته‌بندی (جدید)</label>
                    <input type="file" name="image" id="image" class="mt-1 block w-full text-sm text-gray-500
                        file:mr-4 file:py-2 file:px-4 file:ml-4
                        file:rounded-full file:border-0
                        file:text-sm file:font-semibold
                        file:bg-blue-50 file:text-blue-700
                        hover:file:bg-blue-100 cursor-pointer">
                    @error('image') <span class="text-red-500 text-sm block mt-1">{{ $message }}</span> @enderror

                    @if($category->image_path)
                        <div class="mt-4 p-2 bg-gray-50 border border-gray-200 rounded inline-block">
                            <p class="text-xs text-gray-500 mb-2 font-medium">تصویر فعلی:</p>
                            <img src="{{ Storage::url($category->image_path) }}" alt="{{ $category->name }}" class="w-32 h-32 object-cover rounded-md shadow-sm">
                        </div>
                    @endif
                </div>
                
                <div class="bg-white shadow-md rounded-lg p-6 flex items-center">
                    <input type="hidden" name="is_visible" value="0">
                    <input type="checkbox" name="is_visible" id="is_visible" value="1" 
                           {{ old('is_visible', $category->is_visible) ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 ml-2">
                    <label for="is_visible" class="text-sm font-medium text-gray-700 cursor-pointer">قابل مشاهده برای عموم</label>
                </div>
            </div>
        </div>

    </form>

    <div class="mt-6 flex justify-between" dir="rtl">
        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('آیا از حذف این دسته مطمئن هستید؟ فرزندان این دسته نیز حذف خواهند شد.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-6 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 shadow-md transition-colors">
                حذف دسته
            </button>
        </form>
        
        <button type="submit" form="update-form" class="px-8 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 shadow-md transition-colors">
            ذخیره تغییرات
        </button>
    </div>

@endsection