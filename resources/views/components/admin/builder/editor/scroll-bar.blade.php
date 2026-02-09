<template x-if="selectedItem.type === 'scroll_bar'">
    <div class="space-y-3">
        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400">مدیریت اسلایدها</label>
        
        <div class="space-y-2">
            <template x-for="(slide, index) in (selectedItem.config.slides || [])" :key="index">
                <div class="p-2 border rounded-lg bg-gray-50 dark:bg-dark-hover dark:border-gray-700 relative group">
                    <div class="h-20 bg-gray-200 rounded mb-2 overflow-hidden relative">
                        <img x-show="slide.image" :src="slide.image" class="w-full h-full object-cover">
                        <div class="absolute inset-0 flex items-center justify-center bg-black/30 opacity-0 group-hover:opacity-100 transition cursor-pointer" 
                                @click="document.getElementById('slide_upload_' + index).click()">
                            <i class="fas fa-camera text-white"></i>
                        </div>
                    </div>
                    <input type="file" :id="'slide_upload_' + index" class="hidden" @change="uploadSlideImage($event, index)">
                    
                    <input type="text" x-model="slide.link" placeholder="لینک مقصد..." class="w-full text-xs border-gray-300 dark:bg-dark-bg rounded dark:border-gray-600 mb-1">
                    
                    <button @click="removeSlide(index)" class="absolute top-1 left-1 text-red-500 bg-white rounded-full w-5 h-5 flex items-center justify-center shadow hover:bg-red-50">
                        <x-icons.close />
                    </button>
                </div>
            </template>
        </div>

        <button @click="addSlide()" class="w-full py-2 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg text-gray-500 text-xs hover:bg-gray-50 dark:hover:bg-dark-hover">
            + افزودن اسلاید جدید
        </button>
    </div>
</template>