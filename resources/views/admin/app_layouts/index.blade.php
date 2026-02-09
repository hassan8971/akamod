@extends('admin.layouts.app')

@section('title', 'مدیریت چینش صفحات')

@section('content')
<div dir="rtl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold dark:text-white">مدیریت صفحات (App Layouts)</h1>
        {{-- دکمه افزودن (در صورت نیاز) --}}
        <a href="{{ route('admin.layouts.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-lg transition">+ صفحه جدید</a>
    </div>

    <div class="bg-white dark:bg-dark-paper shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead class="bg-gray-100 dark:bg-dark-hover">
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 text-right text-xs font-semibold text-gray-600 dark:text-gray-300">#</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 text-right text-xs font-semibold text-gray-600 dark:text-gray-300">عنوان صفحه</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 text-right text-xs font-semibold text-gray-600 dark:text-gray-300">پلتفرم</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 text-right text-xs font-semibold text-gray-600 dark:text-gray-300">تعداد ماژول</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 text-right text-xs font-semibold text-gray-600 dark:text-gray-300">وضعیت</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 text-left text-xs font-semibold text-gray-600 dark:text-gray-300">عملیات</th>
                </tr>
            </thead>
            <tbody class="dark:text-gray-200">
                @forelse($pages as $page)
                <tr class="hover:bg-gray-50 dark:hover:bg-dark-hover transition-colors">
                    <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                        {{ $page->id }}
                    </td>
                    <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="font-bold text-gray-800 dark:text-gray-100">{{ $page->title }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 font-mono mt-1">{{ $page->slug }}</div>
                    </td>
                    <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                        @if($page->platform == 'web')
                            <span class="px-2 py-1 text-xs font-semibold leading-tight text-blue-700 bg-blue-100 rounded-full dark:bg-blue-900 dark:text-blue-200">
                                وب‌سایت
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold leading-tight text-green-700 bg-green-100 rounded-full dark:bg-green-900 dark:text-green-200">
                                اپلیکیشن
                            </span>
                        @endif
                    </td>
                    <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-sm">{{ $page->sections_count ?? $page->sections()->count() }} بخش</span>
                    </td>
                    <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                        @if($page->is_active)
                            <span class="px-2 py-1 text-xs font-bold text-green-600 dark:text-green-400">فعال</span>
                        @else
                            <span class="px-2 py-1 text-xs font-bold text-red-500 dark:text-red-400">غیرفعال</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 text-left">
                        <a href="{{ route('admin.layouts.builder', $page->id) }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md transition-colors shadow-sm">
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                            مدیریت چینش
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-8 text-center text-gray-500 dark:text-gray-400">
                        هیچ صفحه‌ای یافت نشد.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection