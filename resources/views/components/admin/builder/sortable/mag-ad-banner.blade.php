<template x-if="section.type === 'mag_ad_banner'">
    <div class="mt-4 px-4">
        <div class="h-32 bg-gray-100 rounded-xl border border-gray-200 flex items-center justify-center overflow-hidden" style="position: relative">

            <template x-if="section.config.image">
                <img :src="section.config.image" class="w-full h-full object-cover" style="position: absolute;">
            </template>
            <template x-if="!section.config.image">
                <span class="text-gray-400 font-bold" style="position: absolute;">بنر تبلیغ</span>
            </template>

            <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: #00000099; color: #fff;padding: 0.3rem;border-radius: 8px;"></div>

            <template x-if="section.config.icon">
                <img :src="section.config.icon" class="w-full h-full object-cover" style="    position: absolute;width: 60px;height: 60px;top: 9%;border-radius: 13px;">
            </template>
            <template x-if="!section.config.icon">
                <span class="text-gray-400 font-bold" class="w-full h-full object-cover" style="    position: absolute;width: 60px;height: 60px;top: 9%;border-radius: 13px;">آیکون تبلیغ</span>
            </template>

            <template x-if="section.config.title">
                <h2 x-text="section.config.title" class="w-full h-full" style="position: absolute;top: 60%; right: 9%; font-size: 14px;"></h2>
            </template>
            <template x-if="!section.config.title">
                <span class="text-gray-400 font-bold" class="w-full h-full" style="position: absolute;top: 60%; right: 9%; font-size: 14px;">عنوان تبلیغ</span>
            </template>

            <template x-if="section.config.desc">
                <h2 x-text="section.config.desc" class="w-full h-full" style="position: absolute;top: 75%; right: 9%; font-size: 12px;"></h2>
            </template>
            <template x-if="!section.config.desc">
                <span class="text-gray-400 font-bold" class="w-full h-full" style="position: absolute;top: 75%; right: 9%; font-size: 12px;">توضیحات تبلیغ</span>
            </template>

            <div style="position: absolute;top: 60%; left: 9%; font-size: 14px;border-radius: 8px;background: #ffffff42;color: #fff;padding: 0.3rem;">دریافت</div>
        </div>
    </div>
</template>