@if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 text-right" role="alert">
        <strong class="font-bold">خطا!</strong>
        <span class="block sm:inline">ورودی شما دارای چند مشکل است.</span>
        <ul class="mt-3 list-disc list-inside text-sm text-right">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="md:col-span-2 space-y-6">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 text-right">نام محصول</label>
            <input type="text" name="name" id="name" value="{{ old('name', $product->name ?? '') }}" 
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-right" required>
        </div>

        <div>
            <label for="slug" class="block text-sm font-medium text-gray-700">اسلاگ (URL)</label>
            <input type="text" 
                   name="slug" 
                   id="slug" 
                   value="{{ old('slug', $product->slug ?? '') }}" 
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-white text-gray-600" 
                   placeholder="product-name-keyword (example: arsham-leather-sneaker)"
                   dir="ltr">
            <p class="text-xs text-gray-500 mt-1">در صورت خالی گذاشتن، به طور خودکار از روی نام محصول ساخته می‌شود.</p>
        </div>
        
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 text-right">توضیحات</label>
            <textarea name="description" id="description" rows="8" 
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-right">{{ old('description', $product->description ?? '') }}</textarea>
        </div>

        <div>
            <label for="care_and_maintenance" class="block text-sm font-medium text-gray-700">مراقبت و نگهداری (اختیاری)</label>
            <textarea name="care_and_maintenance" id="care_and_maintenance" rows="5" 
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                      placeholder="مثال: شستشو فقط با آب سرد...">{{ old('care_and_maintenance', $product->care_and_maintenance ?? '') }}</textarea>
        </div>
    </div>

    <div class="md:col-span-1 space-y-6">
        
        <div>
            <label class="block text-sm font-medium text-gray-700 text-right mb-2">دسته بندی</label>
            @php
                // ۱. پیدا کردن دسته‌بندی انتخاب شده
                $selectedCategoryId = old('category_id', $product->category_id ?? null);
                
                $catTree = [];
                $catFlat = [];
                
                // 💡 حل مشکل آبجکت الکوئنت: تبدیل مستقیم به آرایه
                foreach($categories as $c) {
                    $catFlat[$c->id] = [
                        'id'        => $c->id,
                        'name'      => $c->name,
                        'parent_id' => $c->parent_id,
                        'children'  => []
                    ];
                }
                
                // ۲. استخراج مسیر والدها (Parents) برای باز ماندن خودکار درختی
                $expandedIds = [];
                if ($selectedCategoryId && isset($catFlat[$selectedCategoryId])) {
                    $parentId = $catFlat[$selectedCategoryId]['parent_id'] ?? null;
                    while ($parentId && isset($catFlat[$parentId])) {
                        $expandedIds[] = $parentId;
                        $parentId = $catFlat[$parentId]['parent_id'] ?? null;
                    }
                }

                // ۳. ساخت ساختار درختی
                foreach($catFlat as $id => &$node) {
                    if(!empty($node['parent_id']) && isset($catFlat[$node['parent_id']])) {
                        $catFlat[$node['parent_id']]['children'][] = &$node;
                    } else {
                        $catTree[$id] = &$node;
                    }
                }
            @endphp

            <div class="bg-white border border-gray-300 rounded-md p-3 max-h-64 overflow-y-auto" dir="rtl" x-data="{ expanded: {{ json_encode($expandedIds) }} }">
                @if(!function_exists('renderAkamodeCategoryTree'))
                    @php
                    function renderAkamodeCategoryTree($nodes, $selectedId = null, $level = 0) {
                        $html = '<ul class="'.($level > 0 ? 'mt-1 mr-4 border-r border-gray-200 pr-3' : 'space-y-1').'">';
                        foreach($nodes as $node) {
                            $hasChildren = !empty($node['children']);
                            $isChecked = ($selectedId == $node['id']) ? 'checked' : '';
                            $html .= '<li class="mt-1">';
                            $html .= '<div class="flex items-center justify-between p-1.5 hover:bg-gray-50 rounded transition-colors">';
                            $html .= '<div class="flex items-center">';
                            $html .= '<input type="radio" name="category_id" value="'.$node['id'].'" id="cat_'.$node['id'].'" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500 cursor-pointer" '.$isChecked.' required>';
                            $html .= '<label for="cat_'.$node['id'].'" class="mr-2 text-sm text-gray-700 cursor-pointer select-none">'.$node['name'].'</label>';
                            $html .= '</div>';
                            if($hasChildren) {
                                $html .= '<button type="button" @click.prevent="expanded.includes('.$node['id'].') ? expanded = expanded.filter(i => i !== '.$node['id'].') : expanded.push('.$node['id'].')" class="text-gray-400 hover:text-blue-600 focus:outline-none p-1">';
                                $html .= '<svg class="w-4 h-4 transition-transform duration-200" :class="expanded.includes('.$node['id'].') ? \'-rotate-90\' : \'\'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>';
                                $html .= '</button>';
                            }
                            $html .= '</div>';
                            if($hasChildren) {
                                $html .= '<div x-show="expanded.includes('.$node['id'].')" x-transition style="display:none;">';
                                $html .= renderAkamodeCategoryTree($node['children'], $selectedId, $level + 1);
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
                    <p class="text-sm text-gray-500 text-center py-4">دسته‌بندی یافت نشد.</p>
                @else
                    {!! renderAkamodeCategoryTree($catTree, $selectedCategoryId) !!}
                @endif
            </div>
        </div>

        <div>
            <label for="invoice_number" class="block text-sm font-medium text-gray-700">شماره فاکتور (اختیاری)</label>
            <input type="text" 
                name="invoice_number" 
                id="invoice_number" 
                value="{{ old('invoice_number', $product->invoice_number ?? '') }}"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm"
                placeholder="مثال: F-1403-25">
        </div>

        <div class="bg-white shadow rounded-lg p-4">
            <label class="block text-sm font-medium text-gray-700">جنسیت</label>
            <div class="space-y-2 mt-3">
                <div class="flex items-center">
                    <input type="hidden" name="is_for_men" value="0">
                    <input id="is_for_men" name="is_for_men" type="checkbox" value="1" 
                           @checked(old('is_for_men', $product->is_for_men ?? false))
                           class="h-4 w-4 text-blue-600 border-gray-300 rounded ml-2">
                    <label for="is_for_men" class="text-sm text-gray-900">برای آقایان</label>
                </div>
                <div class="flex items-center">
                    <input type="hidden" name="is_for_women" value="0">
                    <input id="is_for_women" name="is_for_women" type="checkbox" value="1" 
                           @checked(old('is_for_women', $product->is_for_women ?? false))
                           class="h-4 w-4 text-blue-600 border-gray-300 rounded ml-2">
                    <label for="is_for_women" class="text-sm text-gray-900">برای بانوان</label>
                </div>
                <div class="flex items-center">
                    <input type="hidden" name="is_for_kids" value="0">
                    <input id="is_for_kids" name="is_for_kids" type="checkbox" value="1" 
                           @checked(old('is_for_kids', $product->is_for_kids ?? false))
                           class="h-4 w-4 text-blue-600 border-gray-300 rounded ml-2">
                    <label for="is_for_kids" class="text-sm text-gray-900">برای کودکان</label>
                </div>
            </div>
        </div>
        
        <div class="bg-white shadow rounded-lg p-4 flex items-center">
            <input type="hidden" name="is_visible" value="0">
            <input type="checkbox" name="is_visible" id="is_visible" value="1" 
                   @checked(old('is_visible', $product->is_visible ?? true))
                   class="h-4 w-4 text-blue-600 border-gray-300 rounded ml-2">
            <label for="is_visible" class="ml-2 block text-sm font-medium text-gray-900">قابل مشاهده در فروشگاه</label>
        </div>
    </div>

    <div class="md:col-span-3 mt-6 border-t pt-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">اطلاعات لایه ویژه (هاور تاریک)</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label for="apple_title" class="block text-sm font-medium text-gray-700">عنوان لایه تاریک</label>
                <input type="text" name="apple_title" id="apple_title" value="{{ old('apple_title', $product->apple_title ?? '') }}" 
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500" placeholder="مثلاً: Curated by experts.">
            </div>
            <div class="md:col-span-2">
                <label for="apple_description" class="block text-sm font-medium text-gray-700">توضیحات لایه تاریک</label>
                <textarea name="apple_description" id="apple_description" rows="3" 
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500" placeholder="توضیحاتی درباره این محصول ویژه...">{{ old('apple_description', $product->apple_description ?? '') }}</textarea>
            </div>
        </div>
    </div>
</div>