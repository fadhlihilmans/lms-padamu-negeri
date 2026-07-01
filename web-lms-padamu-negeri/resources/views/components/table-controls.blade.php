@props([
    'searchPlaceholder' => 'Cari...',
])

<div class="flex flex-col sm:flex-row sm:items-center gap-2.5 px-4 py-3 bg-white border-b border-[#d1d5db]">

    {{-- Search --}}
    <div class="flex-1 min-w-0">
        <div class="relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[17px] text-[#9da4b0] pointer-events-none select-none">
                search
            </span>
            <input
                wire:model.live.debounce.300ms="search"
                type="search"
                placeholder="{{ $searchPlaceholder }}"
                class="w-full pl-9 pr-3 py-2 border border-[#c5c5d7] rounded-lg text-[13.5px] text-on-surface bg-white
                       focus:outline-none focus:ring-2 focus:ring-[#3c50e0]/30 focus:border-[#3c50e0] transition-shadow"
            >
        </div>
    </div>

    {{-- Extra filters (optional named slot) --}}
    @isset($filters)
        {{ $filters }}
    @endisset

    {{-- Per-page selector --}}
    <div class="flex items-center gap-2 text-[13px] text-[#757686] flex-shrink-0">
        <span class="whitespace-nowrap">Tampilkan</span>
        <select
            wire:model.live="perPage"
            class="border border-[#c5c5d7] rounded-lg px-3 py-2 text-[13px] text-on-surface bg-white
                   focus:outline-none focus:ring-2 focus:ring-[#3c50e0]/30 focus:border-[#3c50e0] cursor-pointer transition-shadow"
        >
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
            <option value="500">500</option>
        </select>
        <span>Data</span>
    </div>

</div>
