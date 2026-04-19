@extends('admin.layouts.app') 

@section('content')
<div class="bg-white shadow-md rounded-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-800">اعضای خبرنامه</h2>
        <span class="bg-blue-100 text-blue-800 text-sm font-medium px-3 py-1 rounded-full">
            کل اعضا: {{ $subscribers->total() }}
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-right text-sm">
            <thead class="bg-gray-50 text-gray-700">
                <tr>
                    <th class="px-4 py-3 border-b">ردیف</th>
                    <th class="px-4 py-3 border-b">نام و نام خانوادگی</th> <th class="px-4 py-3 border-b">ایمیل</th>
                    <th class="px-4 py-3 border-b">تاریخ عضویت</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subscribers as $index => $subscriber)
                <tr class="hover:bg-gray-50 border-b">
                    <td class="px-4 py-3">{{ $subscribers->firstItem() + $index }}</td>
                    <td class="px-4 py-3 font-medium">{{ $subscriber->name ?? '---' }}</td> <td class="px-4 py-3 font-mono text-left" dir="ltr">{{ $subscriber->email }}</td>
                    <td class="px-4 py-3 text-gray-500">
                        {{ \Morilog\Jalali\Jalalian::fromCarbon($subscriber->created_at)->format('Y/m/d H:i') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-4 py-8 text-center text-gray-500">هیچ ایمیلی ثبت نشده است.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $subscribers->links() }}
    </div>
</div>
@endsection