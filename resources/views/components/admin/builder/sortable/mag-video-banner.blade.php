<template x-if="section.type === 'mag_video_banner'">
    <div class="mt-6">
        <div class="flex flex-col overflow-x-auto px-4 pb-2 no-scrollbar">
            <div class="w-full" style="position: relative;">
                <template x-if="section.config.image">
                <img :src="section.config.image" class="w-full h-full object-cover">
                </template>
                <template x-if="!section.config.image">
                    <span class="text-gray-400 font-bold">بنر</span>
                </template>

                <template x-if="section.config.title">
                    <h3 x-text="section.config.title" style="position: absolute;bottom: 22%;right: 5%; font-size: 0.8rem;"></h3>
                </template>
                <template x-if="!section.config.title">
                    <span class="text-gray-400 font-bold" style="position: absolute;bottom: 22%;right: 5%; font-size: 0.8rem;">عنوان</span>
                </template>

                <template x-if="section.config.titleMain">
                    <h2 x-text="section.config.titleMain" style="position: absolute;bottom: 14%;right: 5%;color: #fff;font-size: 1.1rem;"></h3>
                </template>
                <template x-if="!section.config.titleMain">
                    <span class="text-gray-400 font-bold" style="position: absolute;bottom: 14%;right: 5%;color: #fff;font-size: 1.1rem;">عنوان اصلی</span>
                </template>
            </div>
            <template x-for="app in getPreviewApps(section)" :key="app.id">
                <div class="w-full flex gap-1 cursor-default" style="background: #0000001c;padding: 0.7rem;border-bottom-right-radius: 12px;border-bottom-left-radius: 12px; position: relative;">

                    <template x-if="section.config.appIcon">
                        <img :src="section.config.appIcon" class="w-[60px] h-[60px] rounded-2xl shadow-sm border border-gray-100 object-cover bg-white">
                    </template>
                    <template x-if="!section.config.appIcon">
                        <img :src="app.icon" class="w-[60px] h-[60px] rounded-2xl shadow-sm border border-gray-100 object-cover bg-white">
                    </template>

                    <div class="flex flex-col">
                        <template x-if="section.config.appTitle">
                            <span class="text-xs font-medium text-gray-700 mr-2 mt-3" x-text="section.config.appTitle"></span>
                        </template>
                        <template x-if="!section.config.appTitle">
                            <span class="text-xs font-medium text-gray-700 mr-2 mt-3" x-text="app.title"></span>
                        </template>

                        <template x-if="section.config.appDesc">
                            <span class="font-medium text-gray-500 mr-2 mt-2" x-text="section.config.appDesc" style="font-size: 0.65rem;"></span>
                        </template>
                        <template x-if="!section.config.appDesc">
                            <span class="font-medium text-gray-500 mr-2 mt-2" style="font-size: 0.65rem;">توضیحات اپ</span>
                        </template>
                    </div>
                    
                    


                    <a href="#" style="color: #fff;background: #bfbfbf;align-self: center;position: absolute;left: 6%;padding: 7px 10px 5px 10px;font-size: 13px;border-radius: 16px;">دریافت</a>
                </div>
            </template>
            <div x-show="getPreviewApps(section).length === 0" class="w-full text-center text-xs text-gray-400 py-4">
                ...
            </div>
        </div>
    </div>
</template>