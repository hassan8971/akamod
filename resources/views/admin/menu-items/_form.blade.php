<div class="bg-white shadow-md rounded-lg p-6 space-y-6"
    x-data="{ 
        parentId: {{ json_encode(old('parent_id', $menuItem->parent_id ?? '')) }}, 
        linkUrl: {{ json_encode(old('link_url', $menuItem->link_url ?? '')) }},
        linkType: 'url', 
        selectedSlug: '',

        syncSlug() {
            if (this.linkType === 'category' && this.selectedSlug) {
                this.linkUrl = '/category/' + this.selectedSlug;
            }
        },
        
        init() {
            if (this.linkUrl && this.linkUrl.includes('/category/')) {
                this.linkType = 'category';
                this.selectedSlug = this.linkUrl.replace('/category/', '');
            } else {
                this.linkType = 'url';
            }
        }
    }">
    
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">نام (متن لینک)</label>
        <input type="text" name="name" id="name" value="{{ old('name', $menuItem->name ?? '') }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500" placeholder="مثال: صفحه اصلی" required>
        @error('name') <span class="text-red-500 text-sm block mt-1">{{ $message }}</span> @enderror
    </div>
    
    <div class="border border-gray-200 p-4 rounded-lg bg-gray-50">
        <label class="block text-sm font-medium text-gray-700 mb-3">نحوه تنظیم لینک</label>
        
        <div class="flex items-center gap-6 mb-4">
            <label class="inline-flex items-center cursor-pointer">
                <input type="radio" name="link_type" x-model="linkType" value="url" class="text-blue-600 w-4 h-4 focus:ring-blue-500 border-gray-300">
                <span class="mr-2 text-sm text-gray-700">ورود دستی (URL)</span>
            </label>
            <label class="inline-flex items-center cursor-pointer">
                <input type="radio" name="link_type" x-model="linkType" value="category" class="text-blue-600 w-4 h-4 focus:ring-blue-500 border-gray-300">
                <span class="mr-2 text-sm text-gray-700">انتخاب از دسته‌بندی‌ها</span>
            </label>
        </div>

        {{-- دراپ‌داون دسته‌بندی‌ها --}}
        <div x-show="linkType === 'category'" x-transition style="display: none;" class="mb-4">
            <select x-model="selectedSlug" @change="syncSlug()" class="block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500">
                <option value="">-- یک دسته‌بندی انتخاب کنید --</option>
                @if(isset($categories) && count($categories) > 0)
                    @foreach($categories as $cat)
                        <option value="{{ $cat->slug ?? '' }}">
                            {{ $cat->name ?? 'بدون نام' }} ({{ $cat->slug ?? 'بدون اسلاگ' }})
                        </option>
                    @endforeach
                @endif
            </select>
        </div>

        {{-- فیلد نهایی لینک --}}
        <div>
            <label for="link_url" class="block text-sm font-medium text-gray-700">لینک نهایی</label>
            <input type="text" name="link_url" id="link_url" x-model="linkUrl" value="{{ old('link_url', $menuItem->link_url ?? '') }}"
                   :readonly="linkType === 'category'" 
                   :class="linkType === 'category' ? 'bg-gray-200 cursor-not-allowed text-gray-600' : 'bg-white'" 
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 font-mono" 
                   placeholder="مثال: / یا /categories/clothing" required dir="ltr">
            @error('link_url') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">والد (برای منوی تودرتو)</label>
            
            @php
                $selectedParentId = old('parent_id', $menuItem->parent_id ?? null);
                $currentMenuId = $menuItem->id ?? null; 
                
                $menuTree = [];
                $menuFlat = [];
                
                if (isset($menuItems) && count($menuItems) > 0) {
                    foreach($menuItems as $m) {
                        $menuFlat[$m->id] = [
                            'id'         => $m->id,
                            'name'       => $m->name,
                            'parent_id'  => $m->parent_id,
                            'menu_group' => $m->menu_group,
                            'children'   => []
                        ];
                    }
                    
                    $expandedIds = [];
                    if ($selectedParentId && isset($menuFlat[$selectedParentId])) {
                        $pid = $menuFlat[$selectedParentId]['parent_id'] ?? null;
                        while ($pid && isset($menuFlat[$pid])) {
                            $expandedIds[] = $pid;
                            $pid = $menuFlat[$pid]['parent_id'] ?? null;
                        }
                    }

                    foreach($menuFlat as $id => &$node) {
                        if(!empty($node['parent_id']) && isset($menuFlat[$node['parent_id']])) {
                            $menuFlat[$node['parent_id']]['children'][] = &$node;
                        } else {
                            $menuTree[$id] = &$node;
                        }
                    }
                } else {
                    $expandedIds = [];
                }
            @endphp

            <div class="bg-white border border-gray-300 rounded-md p-3 max-h-64 overflow-y-auto" 
                 x-data="{ expanded: {{ json_encode($expandedIds) }} }">
                
                <div class="flex items-center p-1.5 hover:bg-gray-50 rounded mb-2 border-b border-gray-100 pb-2">
                    <input type="radio" name="parent_id" value="" id="parent_none" x-model="parentId"
                           class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500 cursor-pointer" 
                           {{ empty($selectedParentId) ? 'checked' : '' }}>
                    <label for="parent_none" class="mr-2 text-sm font-bold text-gray-700 cursor-pointer">
                        -- بدون والد (ایجاد به عنوان منوی اصلی) --
                    </label>
                </div>

                @if(!function_exists('renderMenuParentTree'))
                    @php
                    function renderMenuParentTree($nodes, $selectedId = null, $level = 0, $currentMenuId = null) {
                        $html = '<ul class="'.($level > 0 ? 'mt-1 mr-4 border-r border-gray-200 pr-3' : 'space-y-1').'">';
                        
                        foreach($nodes as $node) {
                            if ($currentMenuId && $node['id'] == $currentMenuId) continue;

                            $hasChildren = !empty($node['children']);
                            $isChecked = ($selectedId == $node['id']) ? 'checked' : '';
                            
                            $html .= '<li class="mt-1">';
                            $html .= '<div class="flex items-center justify-between p-1.5 hover:bg-gray-50 rounded transition-colors">';
                            
                            $html .= '<div class="flex items-center">';
                            $html .= '<input type="radio" name="parent_id" value="'.$node['id'].'" id="parent_'.$node['id'].'" x-model="parentId" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500 cursor-pointer" '.$isChecked.'>';
                            $html .= '<label for="parent_'.$node['id'].'" class="mr-2 text-sm text-gray-700 cursor-pointer select-none">'.$node['name'].' <span class="text-[10px] text-gray-400">('.$node['menu_group'].')</span></label>';
                            $html .= '</div>';

                            if($hasChildren) {
                                $html .= '<button type="button" @click.prevent="expanded.includes('.$node['id'].') ? expanded = expanded.filter(i => i !== '.$node['id'].') : expanded.push('.$node['id'].')" class="text-gray-400 hover:text-blue-600 focus:outline-none p-1">';
                                $html .= '<svg class="w-4 h-4 transition-transform duration-200" :class="expanded.includes('.$node['id'].') ? \'-rotate-90\' : \'\'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>';
                                $html .= '</button>';
                            }
                            
                            $html .= '</div>';
                            
                            if($hasChildren) {
                                $html .= '<div x-show="expanded.includes('.$node['id'].')" x-transition style="display:none;">';
                                $html .= renderMenuParentTree($node['children'], $selectedId, $level + 1, $currentMenuId);
                                $html .= '</div>';
                            }
                            
                            $html .= '</li>';
                        }
                        $html .= '</ul>';
                        return $html;
                    }
                    @endphp
                @endif

                @if(empty($menuTree))
                    <p class="text-sm text-gray-500 text-center py-2">هنوز منویی ساخته نشده است.</p>
                @else
                    {!! renderMenuParentTree($menuTree, $selectedParentId, 0, $currentMenuId) !!}
                @endif

            </div>
            @error('parent_id') <span class="text-red-500 text-sm block mt-1">{{ $message }}</span> @enderror
        </div>
        <div class="space-y-6">
            <div>
                <label for="menu_group" class="block text-sm font-medium text-gray-700">گروه منو</label>
                <select name="menu_group" id="menu_group" class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500">
                    <option value="main_header" @selected(old('menu_group', $menuItem->menu_group ?? '') == 'main_header')>هدر اصلی</option>
                    <option value="footer_links" @selected(old('menu_group', $menuItem->menu_group ?? '') == 'footer_links')>لینک‌های فوتر</option>
                </select>
                @error('menu_group') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label for="order" class="block text-sm font-medium text-gray-700">ترتیب نمایش</label>
                <input type="number" name="order" id="order" value="{{ old('order', $menuItem->order ?? 0) }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500" required>
                @error('order') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>

    {{-- فیلد تصویر مگامنو / آیکون --}}
    <div class="border-t border-gray-200 pt-6 mt-6">
        <label for="image" class="block text-sm font-medium text-gray-700">
            <span x-text="parentId ? 'تصویر منو (آیکون کوچک)' : 'تصویر مگامنو (بنر بزرگ)'">تصویر</span>
        </label>
        
        <input type="file" name="image" id="image" accept="image/*" class="mt-2 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
        
        @if(isset($menuItem->image_path) && $menuItem->image_path)
            <div class="mt-4 bg-gray-50 p-3 rounded border border-gray-200 inline-block">
                <p class="text-xs text-gray-500 mb-2 font-medium">تصویر فعلی:</p>
                <img src="{{ Storage::url($menuItem->image_path) }}" class="h-20 object-contain rounded shadow-sm">
            </div>
        @endif
        @error('image') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
    </div>

    <div class="flex justify-end pt-4">
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 shadow transition-colors">ذخیره تنظیمات</button>
    </div>
</div>