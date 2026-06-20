@props(['route', 'icon', 'label'])

@php
    $isActive = request()->routeIs($route) || request()->routeIs($route . '.*');
@endphp

@if(\Route::has($route))
    <a href="{{ route($route) }}"
       @class([
           'flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors text-label-md',
           'text-primary bg-primary/8 font-semibold border-l-4 border-primary pl-2' => $isActive,
           'text-secondary hover:bg-surface-container-low hover:text-on-surface' => !$isActive,
       ])>
        <span class="material-symbols-outlined text-[20px] flex-shrink-0">{{ $icon }}</span>
        <span class="truncate">{{ $label }}</span>
    </a>
@else
    {{-- Route belum ada — tampil tapi non-aktif, untuk development --}}
    <span class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-label-md text-secondary/40 cursor-not-allowed">
        <span class="material-symbols-outlined text-[20px] flex-shrink-0">{{ $icon }}</span>
        <span class="truncate">{{ $label }}</span>
    </span>
@endif
