<div class="hidden md:block relative" x-data="{ open: false }" @keydown.escape.window="open = false" @click.outside="open = false">

    {{-- Tombol trigger --}}
    <button
        @click="open = !open"
        type="button"
        class="flex items-center gap-2 px-3 py-1.5 rounded-md text-[14px] font-medium transition-colors cursor-pointer
               {{ $isReadOnlyMode
                  ? 'bg-amber-50 border border-amber-300 text-amber-800 hover:bg-amber-100'
                  : 'bg-[#f0f4f8] border border-[#c5c5d7] text-[#505f76] hover:bg-[#eaeef2]' }}"
    >
        <span class="material-symbols-outlined text-[16px]">
            {{ $isReadOnlyMode ? 'history' : 'calendar_month' }}
        </span>
        <span>
            @if ($selected)
                TA {{ $selected->tahun_ajaran }} &ndash; {{ ucfirst($selected->semester) }}
            @else
                Pilih Periode
            @endif
        </span>
        @if ($isReadOnlyMode)
            <span class="text-[11px] font-semibold px-1.5 py-0.5 rounded bg-amber-200 text-amber-900">Arsip</span>
        @endif
        <span class="material-symbols-outlined text-[16px] transition-transform duration-150"
              :class="open ? 'rotate-180' : ''">arrow_drop_down</span>
    </button>

    {{-- Dropdown --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-1"
        class="absolute right-0 top-full mt-1 w-64 bg-white border border-[#c5c5d7] rounded-lg shadow-lg z-50 overflow-hidden"
        style="display: none;"
    >
        <div class="px-4 py-2.5 border-b border-[#c5c5d7] bg-[#f0f4f8]">
            <p class="text-[11px] font-semibold text-[#505f76] uppercase tracking-wider">Pilih Periode</p>
        </div>

        <ul class="py-1 max-h-64 overflow-y-auto">
            @forelse ($daftar as $p)
                <li>
                    <button
                        wire:click="switchPeriod({{ $p->id }})"
                        @click="open = false"
                        type="button"
                        class="w-full flex items-center justify-between px-4 py-3 text-[14px] hover:bg-[#f0f4f8] transition-colors cursor-pointer
                               {{ $selected?->id === $p->id ? 'text-[#1c33c8] font-semibold bg-[#EEF2FF]' : 'text-on-surface' }}"
                    >
                        <span>TA {{ $p->tahun_ajaran }} &ndash; {{ ucfirst($p->semester) }}</span>
                        <div class="flex items-center gap-2">
                            @if ($p->is_aktif)
                                <span class="text-[11px] font-semibold px-1.5 py-0.5 rounded bg-green-100 text-green-800">Aktif</span>
                            @endif
                            @if ($selected?->id === $p->id)
                                <span class="material-symbols-outlined text-[16px] text-[#1c33c8]">check</span>
                            @endif
                        </div>
                    </button>
                </li>
            @empty
                <li class="px-4 py-4 text-[14px] text-[#505f76] text-center">Belum ada periode.</li>
            @endforelse
        </ul>
    </div>
</div>
