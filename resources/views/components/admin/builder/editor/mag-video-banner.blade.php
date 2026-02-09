<template x-if="selectedItem.type === 'mag_video_banner'">
    <div>
        <div class="space-y-3 p-3 bg-gray-50 dark:bg-dark-hover rounded-lg border dark:border-gray-700">
            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400">منبع داده</label>
            <select x-model="selectedItem.source_type" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-dark-bg dark:text-white rounded-md">
                <option value="auto">🤖 خودکار (هوشمند)</option>
                <option value="manual">✋ دستی (انتخابی)</option>
            </select>

            <div x-show="selectedItem.source_type === 'auto'" class="space-y-2">
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">سناریو نمایش</label>
                    <select x-model="selectedItem.config.sort_type" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-dark-bg dark:text-white rounded-md">
                        <option value="newest">🔥 جدیدترین‌ها</option>
                        <option value="popular">⭐ محبوب‌ترین (امتیاز بالا)</option>
                        <option value="most_downloaded">📥 پر دانلودترین</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">تعداد</label>
                    <input type="number" x-model="selectedItem.config.limit" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-dark-bg dark:text-white rounded-md">
                </div>
            </div>

            <div x-show="selectedItem.source_type === 'manual'" class="mt-2">
                <button @click="openAppSelector()" type="button" class="w-full bg-white dark:bg-dark-bg border border-blue-200 dark:border-blue-900 hover:bg-blue-50 text-xs py-2 rounded-lg text-blue-600 font-bold flex items-center justify-center gap-2">
                    <i class="fas fa-plus-circle"></i> انتخاب دستی
                </button>
                <div class="mt-2 text-xs text-gray-500 text-center">
                    <span x-text="(selectedItem.config.manual_ids || []).length"></span> مورد انتخاب شده
                </div>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">تصویر بنر</label>
            <div class="relative w-full h-32 bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center cursor-pointer hover:bg-gray-50 overflow-hidden"
                    @click="$refs.videoBannerInput.click()">
                <img x-show="selectedItem.config.image" :src="selectedItem.config.image" class="w-full h-full object-cover absolute inset-0">
                <div x-show="!selectedItem.config.image" class="text-gray-400 flex flex-col items-center">
                    <i class="fas fa-cloud-upload-alt fa-2x"></i>
                    <span class="text-xs mt-1">آپلود</span>
                </div>
            </div>
            <input type="file" x-ref="videoBannerInput" class="hidden" @change="uploadBannerImage($event)">
        </div>

        <div class="mt-2">
            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">آیکون اپ</label>
            <div class="relative w-full h-16 bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center cursor-pointer hover:bg-gray-50 overflow-hidden"
                    @click="$refs.videoBannerAppIcon.click()">
                <img x-show="selectedItem.config.appIcon" :src="selectedItem.config.appIcon" class="w-[60px] h-[60px] object-cover absolute">
                <div x-show="!selectedItem.config.appIcon" class="text-gray-400 flex flex-col items-center">
                    <i class="fas fa-cloud-upload-alt fa-2x"></i>
                    <span class="text-xs mt-1">آپلود</span>
                </div>
            </div>
            <input type="file" x-ref="videoBannerAppIcon" class="hidden" @change="uploadBannerAppIcon($event)">
            
            <button x-show="selectedItem.config.appIcon" @click="removeBannerAppIcon($event)" class="bg-red-600 hover:bg-red-700 text-white px-2 py-2 rounded-lg font-bold shadow transition flex items-center gap-2 text-xs my-2">
                <i class="fas fa-save"></i> حذف آیکون
            </button>
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">لینک ویدیو</label>
            <input type="text" x-model="selectedItem.config.video" placeholder="" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-dark-bg dark:text-white rounded-md dir-ltr text-left">
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">عنوان</label>
            <input type="text" x-model="selectedItem.config.title" placeholder="" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-dark-bg dark:text-white rounded-md dir-ltr text-left">
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">عنوان اصلی</label>
            <input type="text" x-model="selectedItem.config.titleMain" placeholder="" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-dark-bg dark:text-white rounded-md dir-ltr text-left">
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">عنوان اپ</label>
            <input type="text" x-model="selectedItem.config.appTitle" placeholder="" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-dark-bg dark:text-white rounded-md dir-ltr text-left">
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">توضیحات اپ</label>
            <input type="text" x-model="selectedItem.config.appDesc" placeholder="" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-dark-bg dark:text-white rounded-md dir-ltr text-left">
        </div>

    </div>
    
</template>