@extends('user.layouts.app')
@section('title', 'دفترچه آدرس')

@section('panel-content')
<div class="bg-white shadow-md rounded-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">آدرس‌های من</h1>
        <a href="{{ route('user.addresses.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
            + افزودن آدرس جدید
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-r-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-6">
        @forelse ($addresses as $address)
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex justify-between items-start">
                    <div class="space-y-1">
                        <p class="font-semibold">{{ $address->full_name }}</p>
                        <p class="text-sm text-gray-600">{{ $address->address_line_1 }}</p>
                        @if ($address->address_line_2)
                            <p class="text-sm text-gray-600">{{ $address->address_line_2 }}</p>
                        @endif
                        <p class="text-sm text-gray-600">
                            {{ $address->city }}، {{ $address->state }}، {{ $address->zip_code }}
                        </p>
                        <p class="text-sm text-gray-600">{{ $address->country }}</p>
                        <p class="text-sm text-gray-600 mt-2">تلفن: {{ $address->phone }}</p>
                    </div>
                    <div class="flex-shrink-0 flex space-x-2 space-x-reverse">
                        <a href="{{ route('user.addresses.edit', $address) }}" class="text-blue-600 hover:text-blue-800 text-sm">ویرایش</a>
                        <form action="{{ route('user.addresses.destroy', $address) }}" method="POST" onsubmit="return confirm('آیا از حذف این آدرس مطمئن هستید؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm">حذف</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-gray-500">شما هنوز هیچ آدرسی ذخیره نکرده‌اید.</p>
        @endforelse
    </div>
</div>
@endsection