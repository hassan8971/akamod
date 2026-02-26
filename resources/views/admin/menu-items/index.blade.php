@extends('admin.layouts.app')
@section('title', 'مدیریت منوها')

@section('content')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div dir="rtl" 
     x-data="{ 
        expanded: [], 
        modalOpen: false,
        newChild: { parent_id: '', parent_name: '', menu_group: '' },

        toggle(id) {
            if (this.expanded.includes(id)) {
                this.expanded = this.expanded.filter(item => item !== id);
            } else {
                this.expanded.push(id);
            }
        },
        
        openAddModal(parentId, parentName, menuGroup) {
            this.newChild.parent_id = parentId;
            this.newChild.parent_name = parentName;
            this.newChild.menu_group = menuGroup;
            this.modalOpen = true;
        }
     }">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">مدیریت منوها</h1>
        <a href="{{ route('admin.menu-items.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow transition">
            + افزودن آیتم اصلی
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-r-4 border-green-500 text-green-700 p-4 mb-4 rounded">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border-r-4 border-red-500 text-red-700 p-4 mb-4 rounded">{{ session('error') }}</div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden border border-gray-200">
        <table class="min-w-full leading-normal">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 text-right text-xs font-semibold uppercase text-gray-600">نام منو</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 text-right text-xs font-semibold uppercase text-gray-600">تصویر</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 text-right text-xs font-semibold uppercase text-gray-600">لینک</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 text-right text-xs font-semibold uppercase text-gray-600">گروه</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 text-right text-xs font-semibold uppercase text-gray-600">ترتیب</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold uppercase text-gray-600">عملیات</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                @forelse ($rootItems as $root)
                    @php
                        $children = $groupedChildren->get($root->id) ?? collect();
                        $hasChild = $children->isNotEmpty();
                    @endphp

                    {{-- ================= لایه اول (پدر بزرگ) ================= --}}
                    <tr class="hover:bg-gray-50 transition border-b border-gray-100">
                        <td class="px-5 py-4 text-sm font-bold flex items-center gap-2">
                            @if($hasChild)
                                <button @click="toggle({{ $root->id }})" class="focus:outline-none transition-transform duration-200 p-1 rounded hover:bg-gray-200"
                                        :class="expanded.includes({{ $root->id }}) ? '-rotate-90' : ''">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                </button>
                            @else
                                <span class="w-6"></span>
                            @endif
                            
                            {{ $root->name }}

                            <button @click="openAddModal({{ $root->id }}, '{{ $root->name }}', '{{ $root->menu_group }}')" 
                                    class="mr-2 text-xs bg-blue-50 text-blue-600 px-2 py-0.5 rounded border border-blue-100 hover:bg-blue-100 transition"
                                    title="افزودن زیرمنو">
                                +
                            </button>
                        </td>
                        <td class="px-5 py-4 text-sm">
                            @if($root->image_path)
                                <img src="{{ Storage::url($root->image_path) }}" class="w-10 h-10 object-cover rounded shadow-sm border border-gray-200">
                            @else
                                <span class="text-gray-400 text-xs">---</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-sm font-mono text-blue-600" dir="ltr">{{ $root->link_url }}</td>
                        <td class="px-5 py-4 text-sm">
                            <span class="px-2 py-1 bg-gray-100 rounded text-xs">{{ $root->menu_group }}</span>
                        </td>
                        <td class="px-5 py-4 text-sm">{{ $root->order }}</td>
                        <td class="px-5 py-4 text-sm text-left">
                            <a href="{{ route('admin.menu-items.edit', $root->id) }}" class="text-blue-600 hover:text-blue-900 ml-4">ویرایش</a>
                            <form action="{{ route('admin.menu-items.destroy', $root->id) }}" method="POST" class="inline-block" onsubmit="return confirm('با حذف والد، تمام زیرمنوها هم حذف می‌شوند. مطمئن هستید؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">حذف</button>
                            </form>
                        </td>
                    </tr>

                    {{-- ================= لایه دوم (فرزندان) ================= --}}
                    @foreach($children->sortBy('order') as $child)
                        @php
                            $grandchildren = $groupedChildren->get($child->id) ?? collect();
                            $hasGrandchild = $grandchildren->isNotEmpty();
                        @endphp
                        
                        {{-- 💡 تغییر اصلی: استفاده از x-show مستقیماً روی tr --}}
                        <tr x-show="expanded.includes({{ $root->id }})" style="display: none;" class="bg-gray-50 hover:bg-gray-100 transition">
                            <td class="px-5 py-3 text-sm pr-12 flex items-center gap-2 relative">
                                {{-- خط راهنما لایه ۲ --}}
                                <div class="absolute right-6 top-0 bottom-0 w-0.5 bg-gray-300 h-full"></div>
                                <div class="absolute right-6 top-1/2 w-4 h-0.5 bg-gray-300"></div>
                                
                                {{-- دکمه باز کردن لایه سوم (اگر نوه داشته باشد) --}}
                                @if($hasGrandchild)
                                    <button @click="toggle({{ $child->id }})" class="focus:outline-none transition-transform duration-200 p-1 rounded hover:bg-gray-200 bg-white shadow-sm z-10"
                                            :class="expanded.includes({{ $child->id }}) ? '-rotate-90' : ''">
                                        <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                    </button>
                                @else
                                    <span class="w-5"></span>
                                @endif

                                <span class="font-medium text-gray-800">{{ $child->name }}</span>
                                
                                {{-- دکمه افزودن سریع لایه سوم --}}
                                <button @click="openAddModal({{ $child->id }}, '{{ $child->name }}', '{{ $child->menu_group }}')" 
                                        class="mr-1 text-[10px] bg-green-50 text-green-600 px-1.5 py-0.5 rounded border border-green-200 hover:bg-green-100 transition z-10"
                                        title="افزودن زیرمنو لایه سوم">
                                    + نوه
                                </button>
                            </td>
                            <td class="px-5 py-3 text-sm">
                                @if($child->image_path)
                                    <img src="{{ Storage::url($child->image_path) }}" class="w-8 h-8 object-cover rounded shadow-sm">
                                @else
                                    <span class="text-gray-400 text-xs">---</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-sm font-mono text-gray-500" dir="ltr">{{ $child->link_url }}</td>
                            <td class="px-5 py-3 text-sm text-gray-400 text-xs">فرزندِ {{ $root->name }}</td>
                            <td class="px-5 py-3 text-sm">{{ $child->order }}</td>
                            <td class="px-5 py-3 text-sm text-left">
                                <a href="{{ route('admin.menu-items.edit', $child->id) }}" class="text-blue-600 hover:text-blue-900 ml-4 text-xs">ویرایش</a>
                                <form action="{{ route('admin.menu-items.destroy', $child->id) }}" method="POST" class="inline-block" onsubmit="return confirm('آیا از حذف این زیرمنو مطمئن هستید؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 text-xs">حذف</button>
                                </form>
                            </td>
                        </tr>

                        {{-- ================= لایه سوم (نوه‌ها) ================= --}}
                        @foreach($grandchildren->sortBy('order') as $grandchild)
                            {{-- 💡 تغییر اصلی: اگر والدِ این نوه باز بود و والدِ والدش هم باز بود، نمایش بده --}}
                            <tr x-show="expanded.includes({{ $root->id }}) && expanded.includes({{ $child->id }})" style="display: none;" class="bg-gray-100 hover:bg-gray-200 transition">
                                <td class="px-5 py-2 text-sm pr-20 flex items-center relative">
                                    {{-- خط راهنما لایه ۳ --}}
                                    <div class="absolute right-6 top-0 bottom-0 w-0.5 bg-gray-300 h-full"></div>
                                    <div class="absolute right-14 top-0 bottom-0 w-0.5 bg-gray-400 h-full"></div>
                                    <div class="absolute right-14 top-1/2 w-4 h-0.5 bg-gray-400"></div>
                                    
                                    <span class="text-gray-600 text-xs">{{ $grandchild->name }}</span>
                                </td>
                                <td class="px-5 py-2 text-sm">
                                    @if($grandchild->image_path)
                                        <img src="{{ Storage::url($grandchild->image_path) }}" class="w-6 h-6 object-cover rounded shadow-sm">
                                    @else
                                        <span class="text-gray-300 text-xs">---</span>
                                    @endif
                                </td>
                                <td class="px-5 py-2 text-sm font-mono text-gray-400 text-xs" dir="ltr">{{ $grandchild->link_url }}</td>
                                <td class="px-5 py-2 text-sm text-gray-400 text-xs">فرزندِ {{ $child->name }}</td>
                                <td class="px-5 py-2 text-sm text-xs">{{ $grandchild->order }}</td>
                                <td class="px-5 py-2 text-sm text-left">
                                    <a href="{{ route('admin.menu-items.edit', $grandchild->id) }}" class="text-blue-500 hover:text-blue-800 ml-4 text-[11px]">ویرایش</a>
                                    <form action="{{ route('admin.menu-items.destroy', $grandchild->id) }}" method="POST" class="inline-block" onsubmit="return confirm('آیا از حذف این آیتم مطمئن هستید؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-800 text-[11px]">حذف</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach

                    @endforeach

                @empty
                    <tr>
                        <td colspan="6" class="py-10 text-center text-gray-500">
                            هیچ منویی تعریف نشده است.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- مودال افزودن سریع زیرمنو --}}
    <div x-show="modalOpen" 
         style="display: none;"
         x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm px-4">
        
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md overflow-hidden" @click.away="modalOpen = false">
            <div class="bg-gray-100 px-4 py-3 border-b flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">
                    افزودن زیرمنو برای: <span class="text-blue-600" x-text="newChild.parent_name"></span>
                </h3>
                <button @click="modalOpen = false" class="text-gray-500 hover:text-gray-700">&times;</button>
            </div>
            
            <form action="{{ route('admin.menu-items.store') }}" method="POST" enctype="multipart/form-data" class="p-4 space-y-4">
                @csrf
                <input type="hidden" name="parent_id" x-model="newChild.parent_id">
                <input type="hidden" name="menu_group" x-model="newChild.menu_group">

                <div>
                    <label class="block text-sm font-medium text-gray-700">نام زیرمنو</label>
                    <input type="text" name="name" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" required autofocus>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">لینک</label>
                    <input type="text" name="link_url" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 font-mono" required dir="ltr">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">ترتیب</label>
                    <input type="number" name="order" value="0" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">تصویر (اختیاری)</label>
                    <input type="file" name="image" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="modalOpen = false" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">انصراف</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 shadow">ذخیره</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection