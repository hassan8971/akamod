@extends('admin.layouts.app')
@section('title', 'مدیریت منوها')

@section('content')
<div dir="rtl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">مدیریت منوها</h1>
        <a href="{{ route('admin.menu-items.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            + افزودن آیتم جدید
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
                    <th class="px-5 py-3 border-b-2 ... text-right ... uppercase">نام (متن لینک)</th>
                    <th class="px-5 py-3 border-b-2 ... text-right ... uppercase">لینک (URL)</th>
                    <th class="px-5 py-3 border-b-2 ... text-right ... uppercase">والد</th>
                    <th class="px-5 py-3 border-b-2 ... text-right ... uppercase">گروه</th>
                    <th class="px-5 py-3 border-b-2 ... text-right ... uppercase">ترتیب</th>
                    <th class="px-5 py-3 border-b-2 ..."></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($menuItems as $item)
                <tr class="hover:bg-gray-50 {{ $item->parent_id ? 'bg-gray-50' : 'bg-white' }}">
                    <td class="px-5 py-5 border-b border-gray-200 text-sm">
                        <p class="font-semibold {{ $item->parent_id ? 'mr-4' : '' }}">
                            @if($item->parent_id) <span class="text-gray-400">&Larr;</span> @endif
                            {{ $item->name }}
                        </p>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 text-sm" dir="ltr">{{ $item->link_url }}</td>
                    <td class="px-5 py-5 border-b border-gray-200 text-sm">{{ $item->parent->name ?? '---' }}</td>
                    <td class="px-5 py-5 border-b border-gray-200 text-sm">{{ $item->menu_group }}</td>
                    <td class="px-5 py-5 border-b border-gray-200 text-sm">{{ $item->order }}</td>
                    <td class="px-5 py-5 border-b border-gray-200 text-sm text-left">
                        <a href="{{ route('admin.menu-items.edit', $item) }}" class="text-blue-600 hover:text-blue-900 ml-4">ویرایش</a>
                        <form action="{{ route('admin.menu-items.destroy', $item) }}" method="POST" class="inline-block" onsubmit="return confirm('آیا از حذف این آیتم مطمئن هستید؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">حذف</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-10 text-center text-gray-500">
                        هیچ آیتم منویی تعریف نشده است.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection