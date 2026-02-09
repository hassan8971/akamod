<template x-if="section.type === 'list_horizontal'">
    <div class="mt-6">
        <div class="flex justify-between px-4 mb-2">
            <h3 class="font-bold text-gray-800 text-sm" x-text="section.config.title"></h3>
            <span class="text-blue-500 text-xs">مشاهده همه</span>
        </div>
        <div class="flex gap-3 overflow-x-auto px-4 pb-2 no-scrollbar">
            <template x-for="app in getPreviewApps(section)" :key="app.id">
                <img :src="app.images" class="w-full h-full rounded-2xl shadow-sm border border-gray-100 bg-white">
                <span class="text-xs font-medium text-gray-700 truncate" x-text="app.name"></span>
                <span class="text-[10px] text-gray-400" x-text="app.price"></span>
            </template>
            <div x-show="getPreviewApps(section).length === 0" class="w-full text-center text-xs text-gray-400 py-4">
                ...
            </div>
        </div>
    </div>
</template>