@props(['dataType', 'dataTitle'])


<div data-type="{{$dataType}}" class="tool-item flex items-center bg-gray-50 dark:bg-dark-hover hover:bg-white hover:shadow-md transition text-right cursor-grab active:cursor-grabbing">
    <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded flex items-center justify-center pointer-events-none relative" title="{{$dataTitle}}">
        {{$icon}}
        <div class="w-auto absolute bottom-[-20px] right-0 tooltip">{{$dataTitle}}</div>
    </div>
</div>