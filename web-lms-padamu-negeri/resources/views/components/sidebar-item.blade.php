@props(['route', 'icon', 'label'])

@php
    $isActive = request()->routeIs($route) || request()->routeIs($route . '.*');
@endphp

@if(\Route::has($route))
    <a href="{{ route($route) }}"
       @class([
           'flex items-center gap-2.5 px-3 py-2 rounded-lg transition-colors text-[13.5px]',
           'text-[#3c50e0] font-semibold bg-[#3c50e014] border-l-[3px] border-[#3c50e0] pl-[9px]' => $isActive,
           'text-[#505f76] font-medium hover:bg-[#f0f4f8] hover:text-[#171c1f]' => ! $isActive,
       ])>
        <span class="material-symbols-outlined text-[18px] flex-shrink-0">{{ $icon }}</span>
        <span class="truncate">{{ $label }}</span>
    </a>
@else
    {{-- Route belum ada — tampil tapi non-aktif, untuk development --}}
    <span class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-[13.5px] font-medium text-[#9da4b0] opacity-60 cursor-not-allowed">
        <span class="material-symbols-outlined text-[18px] flex-shrink-0">{{ $icon }}</span>
        <span class="truncate">{{ $label }}</span>
    </span>
@endif
