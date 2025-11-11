@extends('admin.layouts.app')
@section('title', 'مدیریت منابع خرید')

@section('content')
<div dir="rtl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">مدیریت منابع خرید</h1>
        <a href="{{ route('admin.buy-sources.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            + افزودن منبع جدید
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
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 ... text-right ... uppercase">نام منبع</th>
                    <th class="px-5 py-3 border-b-2 ... text-right ... uppercase">تلفن</th>
                    <th class="px-5 py-3 border-b-2 ... text-right ... uppercase">شهر</th>
                    <th class="px-5 py-3 border-b-2 ..."></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($sources as $source)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-5 border-b ...">{{ $source->name }}</td>
                    <td class="px-5 py-5 border-b ...">{{ $source->phone ?? '---' }}</td>
                    <td class="px-5 py-5 border-b ...">{{ $source->city ?? '---' }}</td>
                    <td class="px-5 py-5 border-b ... text-left">
                        <a href="{{ route('admin.buy-sources.edit', $source) }}" class="text-blue-600 hover:text-blue-900 ml-4">ویرایش</a>
                        <form action="{{ route('admin.buy-sources.destroy', $source) }}" method="POST" class="inline-block" onsubmit="return confirm('آیا از حذف این منبع مطمئن هستید؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">حذف</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-10 text-center text-gray-500">
                        هیچ منبع خریدی تعریف نشده است.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection