@props(['category', 'level' => 0])

<tr class="hover:bg-gray-50">
    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
        @if($category->image_path)
            <img src="{{ Storage::url($category->image_path) }}" alt="{{ $category->name }}" class="w-10 h-10 object-cover rounded-md">
        @else
            <span class="w-10 h-10 bg-gray-200 rounded-md flex items-center justify-center text-xs text-gray-500">ندارد</span>
        @endif
    </td>

    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
        <div style="margin-right: {{ $level * 30 }}px; position: relative;" class="flex items-center">
            @if($level > 0)
                <span class="text-gray-300 ml-2">↳</span>
            @endif
            <span class="{{ $level === 0 ? 'font-bold' : '' }}">
                {{ $category->name }}
            </span>
        </div>
    </td>

    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm" dir="ltr">
        {{ $category->slug }}
    </td>

    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-left">
        <div class="flex items-center justify-end space-x-reverse space-x-2">
            <a href="{{ route('admin.categories.edit', $category) }}" class="text-blue-600 hover:text-blue-900 ml-3">ویرایش</a>
            
            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline-block" onsubmit="return confirm('آیا مطمئن هستید؟ حذف والد باعث حذف تمام فرزندان می‌شود.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600 hover:text-red-900">حذف</button>
            </form>
        </div>
    </td>
</tr>

@if ($category->children)
    @foreach ($category->children as $child)
        @include('admin.categories.partials.category-row', ['category' => $child, 'level' => $level + 1])
    @endforeach
@endif