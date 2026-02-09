<template x-if="section.type === 'scroll_bar'">
    <div class="mt-4 px-4" x-data="{ currentSlide: 0 }">
        <div class="h-40 flex items-center relative overflow-auto group">
            <template x-for="slide in section.config.slides">
                <img :src="slide.image" 
                        class="w-full h-full object-cover transition-opacity duration-300 ml-2">
            </template>
            <template x-if="!section.config.slides || section.config.slides.length === 0">
                <div class="text-gray-400 flex flex-col items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 mb-1"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a2.25 2.25 0 002.25-2.25V6a2.25 2.25 0 00-2.25-2.25H3.75A2.25 2.25 0 001.5 6v12a2.25 2.25 0 002.25 2.25zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>
                    <span class="text-xs">اسلایدر خالی</span>
                </div>
            </template>
        </div>
    </div>
</template>