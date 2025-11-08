@extends('admin.layouts.app')
@section('title', 'مدیریت کدهای تخفیف')

@section('content')
<div dir="rtl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">مدیریت کدهای تخفیف</h1>
        <a href="{{ route('admin.discounts.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            + افزودن کد تخفیف
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-r-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border-r-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 text-right text-xs font-semibold uppercase">کد</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 text-right text-xs font-semibold uppercase">نوع</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 text-right text-xs font-semibold uppercase">مقدار</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 text-right text-xs font-semibold uppercase">استفاده شده</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 text-right text-xs font-semibold uppercase">وضعیت</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($discounts as $discount)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <span class="font-bold text-gray-900">{{ $discount->code }}</span>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        {{ $discount->type === 'percent' ? 'درصدی' : 'مبلغ ثابت' }}
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        @if($discount->type === 'percent')
                            {{ $discount->value }}%
                        @else
                            {{ number_format($discount->value) }} تومان
                        @endif
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        {{ $discount->times_used }} / {{ $discount->usage_limit ?? '∞' }}
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        @if(!$discount->is_active)
                            <span class="px-2 py-1 font-semibold leading-tight text-gray-700 bg-gray-100 rounded-full">غیرفعال</span>
                        @elseif($discount->expires_at && $discount->expires_at->isPast())
                            <span class="px-2 py-1 font-semibold leading-tight text-red-700 bg-red-100 rounded-full">منقضی شده</span>
                        @else
                            <span class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full">فعال</span>
                        @endif
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-left">
                        <a href="{{ route('admin.discounts.edit', $discount) }}" class="text-blue-600 hover:text-blue-900 ml-4">ویرایش</a>
                        <form action="{{ route('admin.discounts.destroy', $discount) }}" method="POST" class="inline-block" onsubmit="return confirm('آیا از حذف این کد تخفیف مطمئن هستید؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">حذف</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-10 text-center text-gray-500">
                        هیچ کد تخفیفی تعریف نشده است.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection