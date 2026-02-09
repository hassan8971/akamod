<template x-if="section.type === 'banner_single'">
    <div class="mt-4 px-4">
        <div class="h-32 bg-gray-100 rounded-xl border border-gray-200 flex items-center justify-center overflow-hidden">
            <template x-if="section.config.image">
                <img :src="section.config.image" class="w-full h-full object-cover">
            </template>
            <template x-if="!section.config.image">
                <span class="text-gray-400 font-bold">بنر</span>
            </template>
        </div>
    </div>
</template>