@extends('admin.layouts.app')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-800">پیام‌های تماس با ما</h2>
        <span class="bg-blue-100 text-blue-800 text-sm font-medium px-3 py-1 rounded-full">
            کل پیام‌ها: {{ $messages->total() }}
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-right text-sm">
            <thead class="bg-gray-50 text-gray-700">
                <tr>
                    <th class="px-4 py-3 border-b">ردیف</th>
                    <th class="px-4 py-3 border-b">نام</th>
                    <th class="px-4 py-3 border-b">شماره تماس</th>
                    <th class="px-4 py-3 border-b w-1/2">متن پیام</th>
                    <th class="px-4 py-3 border-b">تاریخ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($messages as $index => $msg)
                <tr class="hover:bg-gray-50 border-b">
                    <td class="px-4 py-3">{{ $messages->firstItem() + $index }}</td>
                    <td class="px-4 py-3 font-medium">{{ $msg->name }}</td>
                    <td class="px-4 py-3 font-mono text-left" dir="ltr">{{ $msg->phone }}</td>
                    <td class="px-4 py-3 text-gray-600 leading-relaxed">{{ $msg->message }}</td>
                    <td class="px-4 py-3 text-gray-500 whitespace-nowrap">
                        {{ \Morilog\Jalali\Jalalian::fromCarbon($msg->created_at)->format('Y/m/d H:i') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">هیچ پیامی ثبت نشده است.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $messages->links() }}
    </div>
</div>
@endsection