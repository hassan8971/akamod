@extends('admin.layouts.app')
@section('title', 'مدیریت مشتریان')

@section('content')
<div dir="rtl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">مدیریت مشتریان</h1>
    </div>

    <div class="mb-4">
        <form method="GET" action="{{ route('admin.users.index') }}">
            <div class="flex rounded-md shadow-sm">
                <input type="text" name="search" value="{{ request('search') }}"
                       class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-md border-gray-300"
                       placeholder="جستجو بر اساس نام، ایمیل یا موبایل...">
                <button type="submit"
                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-gray-50 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-l-md">
                    جستجو
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-5 py-3 border-b-2 ... text-right ... uppercase">نام</th>
                    <th class="px-5 py-3 border-b-2 ... text-right ... uppercase">ایمیل</th>
                    <th class="px-5 py-3 border-b-2 ... text-right ... uppercase">موبایل</th>
                    <th class="px-5 py-3 border-b-2 ... text-right ... uppercase">تاریخ عضویت</th>
                    <th class="px-5 py-3 border-b-2 ..."></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-5 border-b ...">
                        <p class="font-semibold">{{ $user->name ?? '---' }}</p>
                    </td>
                    <td class="px-5 py-5 border-b ...">{{ $user->email ?? '---' }}</td>
                    <td class="px-5 py-5 border-b ..." dir="ltr">{{ $user->mobile }}</td>
                    <td class="px-5 py-5 border-b ...">
                        {{ $user->created_at ? jdate($user->created_at)->format('Y/m/d') : '---' }}
                    </td>
                    <td class="px-5 py-5 border-b ... text-left">
                        <a href="{{ route('admin.users.show', $user) }}" class="text-blue-600 hover:text-blue-900">
                            مشاهده جزئیات
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-10 text-center text-gray-500">
                        مشتری با این مشخصات یافت نشد.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="p-4">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection