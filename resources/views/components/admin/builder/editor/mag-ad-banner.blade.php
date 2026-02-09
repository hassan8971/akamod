<template x-if="selectedItem.type === 'mag_ad_banner'">
    <div class="space-y-3">
        <div>
            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">تصویر بنر</label>
            <div class="relative w-full h-32 bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center cursor-pointer hover:bg-gray-50 overflow-hidden"
                    @click="$refs.bannerInput.click()">
                <img x-show="selectedItem.config.image" :src="selectedItem.config.image" class="w-full h-full object-cover absolute inset-0">
                <div x-show="!selectedItem.config.image" class="text-gray-400 flex flex-col items-center">
                    <i class="fas fa-cloud-upload-alt fa-2x"></i>
                    <span class="text-xs mt-1">آپلود</span>
                </div>
            </div>
            <input type="file" x-ref="bannerInput" class="hidden" @change="uploadBannerImage($event)">
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">آیکون بنر</label>
            <div class="relative h-32 bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center cursor-pointer hover:bg-gray-50 overflow-hidden" style="width: 100px;"
                    @click="$refs.bannerIconInput.click()">
                <img x-show="selectedItem.config.icon" :src="selectedItem.config.icon" class="w-full h-full object-cover absolute inset-0">
                <div x-show="!selectedItem.config.icon" class="text-gray-400 flex flex-col items-center">
                    <i class="fas fa-cloud-upload-alt fa-2x"></i>
                    <span class="text-xs mt-1">آپلود</span>
                </div>
            </div>
            <input type="file" x-ref="bannerIconInput" class="hidden" @change="uploadBannerIcon($event)">
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">عنوان بنر</label>
            <input type="text" x-model="selectedItem.config.title" placeholder="" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-dark-bg dark:text-white rounded-md dir-ltr text-left">
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">توضیحات بنر</label>
            <input type="text" x-model="selectedItem.config.desc" placeholder="" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-dark-bg dark:text-white rounded-md dir-ltr text-left">
        </div>
        
        <div>
            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">لینک مقصد</label>
            <input type="text" x-model="selectedItem.config.link" placeholder="https://..." class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-dark-bg dark:text-white rounded-md dir-ltr text-left">
        </div>
    </div>
</template>