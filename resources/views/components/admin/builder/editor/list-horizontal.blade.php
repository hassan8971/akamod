<template x-if="selectedItem.type === 'list_horizontal'">
    <div class="space-y-3 p-3 bg-gray-50 dark:bg-dark-hover rounded-lg border dark:border-gray-700">

        <div>
            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">عنوان</label>
            <input type="text" x-model="selectedItem.config.name" placeholder="" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-dark-bg dark:text-white rounded-md dir-ltr text-left">
        </div>

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
</template>