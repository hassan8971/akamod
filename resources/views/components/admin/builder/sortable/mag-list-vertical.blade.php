<template x-if="section.type === 'mag_list_vertical'">
    <div class="mt-6">
        <div class="flex justify-between px-4 mb-2">
            <h3 class="font-bold text-gray-500 text-xs" x-show="section.config.title" x-text="section.config.title"></h3>
            <h3 class="font-bold text-gray-500 text-xs" x-show="!section.config.title">عنوان</h3>
        </div>
        <div class="flex justify-between px-4 mb-2">
            <h3 class="font-bold text-gray-800 text-sm" x-show="section.config.titleMain"  x-text="section.config.titleMain"></h3>
            <h3 class="font-bold text-gray-800 text-sm" x-show="!section.config.titleMain">عنوان اصلی</h3>
            <span class="text-blue-500 text-xs">مشاهده همه</span>
        </div>
        <div class="flex gap-3 overflow-x-auto px-4 pb-2 no-scrollbar" style="flex-direction: column;">
            <template x-for="app in getPreviewApps(section)" :key="app.id">
                <div class="width-full flex justify-between" style="align-items: center;">
                    <div class="min-w-[90px] w-[90px] flex gap-1 cursor-default">
                        <img :src="app.icon" class="w-[75px] h-[75px] rounded-2xl shadow-sm border border-gray-100 object-cover bg-white">
                        <div class="flex flex-col justify-evenly">
                            
                            <span class="text-xs font-medium text-gray-700 truncate" x-text="app.title"></span>
                            <span class="text-xs font-medium text-gray-500 truncate">توضیحات اپ</span>

                        </div>
                        
                    </div>
                    <div style="background: #0000000f;color: #3e84f6;padding: 0.4rem 0.7rem;border-radius: 9px;">
                        دریافت
                    </div>
                </div>
                
            </template>
            <div x-show="getPreviewApps(section).length === 0" class="w-full text-center text-xs text-gray-400 py-4">
                ...
            </div>
        </div>
    </div>
</template>