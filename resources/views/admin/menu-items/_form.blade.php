<div class="bg-white shadow-md rounded-lg p-6 space-y-6"
    x-data="{ 
        // فرار از کاراکترهای خاص با استفاده از json_encode لاراول
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
            // در زمان لود صفحه بررسی کن که آیا لینک ذخیره شده مربوط به دسته‌بندی است یا خیر
            if (this.linkUrl && this.linkUrl.startsWith('/category/')) {
                this.linkType = 'category';
                // اسلاگ را از دل آدرس بیرون بکش (مثلا shoes را از /category/shoes جدا کن)
                this.selectedSlug = this.linkUrl.replace('/category/', '');
            } else {
                this.linkType = 'url';
            }
        }
    }">
    
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">نام (متن لینک)</label>
        <input type="text" name="name" id="name" value="{{ old('name', $menuItem->name ?? '') }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500" placeholder="مثال: صفحه اصلی" required>
        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>
    
    <div class="border border-gray-200 p-4 rounded-lg bg-gray-50">
        <label class="block text-sm font-medium text-gray-700 mb-3">نحوه تنظیم لینک</label>
        
        <div class="flex items-center gap-6 mb-4">
            <label class="inline-flex items-center cursor-pointer">
                <input type="radio" x-model="linkType" value="url" class="text-blue-600 w-4 h-4 focus:ring-blue-500 border-gray-300">
                <span class="mr-2 text-sm text-gray-700">ورود دستی (URL)</span>
            </label>
            <label class="inline-flex items-center cursor-pointer">
                <input type="radio" x-model="linkType" value="category" class="text-blue-600 w-4 h-4 focus:ring-blue-500 border-gray-300">
                <span class="mr-2 text-sm text-gray-700">انتخاب از دسته‌بندی‌ها</span>
            </label>
        </div>

        {{-- دراپ‌داون دسته‌بندی‌ها --}}
        <div x-show="linkType === 'category'" x-transition class="mb-4">
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
            <input type="text" name="link_url" id="link_url" x-model="linkUrl" 
                   :readonly="linkType === 'category'" 
                   :class="linkType === 'category' ? 'bg-gray-200 cursor-not-allowed text-gray-600' : 'bg-white'" 
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 font-mono" 
                   placeholder="مثال: / یا /categories/clothing" required dir="ltr">
            @error('link_url') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <label for="parent_id" class="block text-sm font-medium text-gray-700">والد (برای منوی تودرتو)</label>
            <select name="parent_id" id="parent_id" x-model="parentId" class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500">
                <option value="">-- بدون والد (آیتم اصلی) --</option>
                @foreach ($menuItems as $item)
                    {{-- جلوگیری از انتخاب خود آیتم به عنوان والد (حلقه بی‌نهایت) --}}
                    @if(!isset($menuItem) || $menuItem->id !== $item->id)
                        <option value="{{ $item->id }}" 
                            @selected(old('parent_id', $menuItem->parent_id ?? '') == $item->id)>
                            {{ $item->name }} ({{ $item->menu_group }})
                        </option>
                    @endif
                @endforeach
            </select>
            @error('parent_id') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
        </div>
        
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

    {{-- فیلد تصویر مگامنو / آیکون --}}
    <div class="border-t border-gray-200 pt-6 mt-6">
        <label for="image" class="block text-sm font-medium text-gray-700">
            <span x-text="parentId ? 'تصویر منو (آیکون کوچک)' : 'تصویر مگامنو (بنر بزرگ)'"></span>
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