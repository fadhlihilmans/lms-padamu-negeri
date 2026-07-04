<div>

    {{-- ── Header (6.3) ──────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
        <div>
            <h1 class="text-[18px] font-bold" style="color: #171c1f">Rekap Absensi</h1>
            <p class="text-[13px] mt-0.5" style="color: #757686">Rekap seluruh sesi absensi per rombel & rentang tanggal{{ $periode ? ' — ' . $periode->tahun_ajaran : '' }}.</p>
        </div>
    </div>

    @if (! $periode)
        <div class="bg-white rounded-xl border px-6 py-16 text-center" style="border-color: #c5c5d7; box-shadow: 0 1px 3px rgba(0,0,0,0.05)">
            <span class="material-symbols-outlined text-[48px] mb-3 block" style="color: #c5c5d7">event_busy</span>
            <p class="text-[14px]" style="color: #757686">Belum ada Periode Ajaran aktif. Set periode terlebih dahulu.</p>
        </div>
    @else

        {{-- ── Table Card ────────────────────────────────────────────────── --}}
        <div class="bg-white rounded-xl border overflow-hidden" style="border-color: #c5c5d7; box-shadow: 0 1px 3px rgba(0,0,0,0.05)">

            {{-- Toolbar standar --}}
            <div class="flex flex-col sm:flex-row sm:flex-wrap sm:items-center gap-2.5 px-4 py-3 border-b bg-white" style="border-color: #d1d5db">
                <div class="relative w-full sm:flex-1 sm:min-w-[180px]">
                    <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-[17px] pointer-events-none" style="color: #9da4b0">search</span>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari mapel, rombel, atau guru..."
                           class="w-full pl-9 pr-3 py-2 rounded-lg text-[13.5px] bg-white outline-none transition-all"
                           style="border: 1.5px solid #c5c5d7; color: #171c1f"
                           onfocus="this.style.borderColor='#3c50e0'; this.style.boxShadow='0 0 0 3px rgba(60,80,224,0.1)'"
                           onblur="this.style.borderColor='#c5c5d7'; this.style.boxShadow='none'">
                </div>
                <select wire:model.live="filterRombelId"
                        class="py-2 px-3 rounded-lg text-[13px] cursor-pointer outline-none"
                        style="border: 1.5px solid #c5c5d7; color: #505f76; background: white">
                    <option value="">Semua Rombel</option>
                    @foreach ($rombels as $rombel)
                        <option value="{{ $rombel->id }}">{{ $rombel->nama }}</option>
                    @endforeach
                </select>
                <div class="flex items-center gap-1.5 w-full sm:w-auto">
                    <input wire:model.live="filterTanggalMulai" type="date"
                           class="flex-1 sm:flex-none py-2 px-3 rounded-lg text-[13px] cursor-pointer outline-none"
                           style="border: 1.5px solid #c5c5d7; color: #505f76; background: white">
                    <span class="text-[12px]" style="color: #757686">s/d</span>
                    <input wire:model.live="filterTanggalAkhir" type="date"
                           class="flex-1 sm:flex-none py-2 px-3 rounded-lg text-[13px] cursor-pointer outline-none"
                           style="border: 1.5px solid #c5c5d7; color: #505f76; background: white">
                </div>
                <div class="flex items-center gap-1.5 w-full sm:w-auto justify-between sm:justify-start sm:flex-shrink-0">
                    <span class="text-[12.5px] whitespace-nowrap" style="color: #757686">Tampil</span>
                    <select wire:model.live="perPage" class="py-2 pl-2.5 rounded-lg text-[13px] cursor-pointer outline-none"
                            style="border: 1.5px solid #c5c5d7; color: #505f76; background: white">
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                    <span class="text-[12.5px] whitespace-nowrap" style="color: #757686">Data</span>
                </div>
                <button wire:click="resetFilter" class="text-[12.5px] flex items-center gap-1 cursor-pointer hover:underline flex-shrink-0" style="color: #3c50e0">
                    <span class="material-symbols-outlined text-[15px]">filter_alt_off</span>Reset
                </button>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left min-w-[760px]">
                    <thead>
                        <tr class="border-b" style="border-color: #c5c5d7">
                            <th class="px-4 py-3 text-[11.5px] font-semibold uppercase tracking-wide w-10" style="color: #757686">No</th>
                            <th class="px-4 py-3 text-[11.5px] font-semibold uppercase tracking-wide" style="color: #757686">Tanggal</th>
                            <th class="px-4 py-3 text-[11.5px] font-semibold uppercase tracking-wide" style="color: #757686">Mapel / Rombel</th>
                            <th class="px-4 py-3 text-[11.5px] font-semibold uppercase tracking-wide" style="color: #757686">Guru</th>
                            <th class="px-4 py-3 text-[11.5px] font-semibold uppercase tracking-wide text-center" style="color: #757686">Rekap (H·I·S·A)</th>
                            <th class="px-4 py-3 text-[11.5px] font-semibold uppercase tracking-wide" style="color: #757686">Status Sesi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#f0f4f8]">
                        @forelse ($sesis as $i => $sesi)
                            @php
                                $counts = $sesi->detail->groupBy('status')->map->count();
                            @endphp
                            <tr class="transition-colors" onmouseover="this.style.background='#f6fafe'" onmouseout="this.style.background='transparent'">
                                <td class="px-4 py-3.5 text-[13px]" style="color: #9da4b0">{{ $sesis->firstItem() + $i }}</td>
                                <td class="px-4 py-3.5 text-[13px] font-medium whitespace-nowrap" style="color: #505f76">{{ $sesi->tanggal->translatedFormat('d M Y') }}</td>
                                <td class="px-4 py-3.5">
                                    <p class="text-[13.5px] font-medium" style="color: #171c1f">{{ $sesi->guruMapelRombel->mapel->nama }}</p>
                                    <p class="text-[12px]" style="color: #757686">{{ $sesi->guruMapelRombel->rombel->nama }}</p>
                                </td>
                                <td class="px-4 py-3.5 text-[13px]" style="color: #505f76">{{ $sesi->guruMapelRombel->guru->nama_lengkap }}</td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1.5 flex-nowrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold" style="background: #dcfce7; color: #15803d">{{ $counts['hadir'] ?? 0 }}</span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold" style="background: #fef3c7; color: #b45309">{{ $counts['izin'] ?? 0 }}</span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold" style="background: #dbeafe; color: #1d4ed8">{{ $counts['sakit'] ?? 0 }}</span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold" style="background: #ffdad6; color: #93000a">{{ $counts['alpa'] ?? 0 }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    @if ($sesi->status_sesi === 'terbuka')
                                        <span class="inline-flex items-center gap-1.5 text-[12px] font-semibold w-fit px-2 py-0.5 rounded-full whitespace-nowrap" style="background: #dcfce7; color: #15803d">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>Terbuka
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 text-[12px] font-semibold w-fit px-2 py-0.5 rounded-full whitespace-nowrap" style="background: #f0f4f8; color: #505f76">
                                            <span class="material-symbols-outlined text-[13px]">lock</span>Ditutup
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-16 text-center">
                                    @if ($search || $filterRombelId)
                                        <span class="material-symbols-outlined text-[40px] mb-2 block" style="color: #c5c5d7">search_off</span>
                                        <p class="text-[13px]" style="color: #757686">Tidak ada data absensi sesuai filter.</p>
                                        <button wire:click="resetFilter" class="mt-2 text-[12.5px] cursor-pointer hover:underline" style="color: #3c50e0">Hapus Filter</button>
                                    @else
                                        <span class="material-symbols-outlined text-[40px] mb-2 block" style="color: #c5c5d7">event_busy</span>
                                        <p class="text-[13px]" style="color: #757686">Tidak ada data absensi sesuai filter.</p>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Legend --}}
            @if ($sesis->total() > 0)
                <div class="flex items-center gap-3 flex-wrap px-4 py-2.5 border-t text-[11.5px]" style="border-color: #f0f4f8; color: #757686; background: #fbfcfe">
                    <span class="flex items-center gap-1"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold" style="background: #dcfce7; color: #15803d">H</span>Hadir</span>
                    <span class="flex items-center gap-1"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold" style="background: #fef3c7; color: #b45309">I</span>Izin</span>
                    <span class="flex items-center gap-1"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold" style="background: #dbeafe; color: #1d4ed8">S</span>Sakit</span>
                    <span class="flex items-center gap-1"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold" style="background: #ffdad6; color: #93000a">A</span>Alpa</span>
                </div>
            @endif

            {{-- Pagination standar --}}
            @if ($sesis->total() > 0)
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-4 py-3.5 border-t" style="border-color: #c5c5d7">
                    <p class="text-[12.5px]" style="color: #757686">
                        Menampilkan {{ $sesis->firstItem() }}–{{ $sesis->lastItem() }} dari {{ $sesis->total() }} sesi
                    </p>
                    @if ($sesis->lastPage() > 1)
                        <div class="flex items-center gap-1">
                            <button wire:click="previousPage" @disabled($sesis->onFirstPage())
                                    class="w-8 h-8 inline-flex items-center justify-center rounded-lg transition-colors {{ $sesis->onFirstPage() ? 'cursor-not-allowed' : 'cursor-pointer hover:bg-[#f0f4f8]' }}"
                                    style="color: {{ $sesis->onFirstPage() ? '#c5c5d7' : '#505f76' }}">
                                <span class="material-symbols-outlined text-[16px]">chevron_left</span>
                            </button>
                            @foreach (range(1, $sesis->lastPage()) as $page)
                                <button wire:click="gotoPage({{ $page }})"
                                        class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-[13px] transition-colors {{ $page == $sesis->currentPage() ? 'font-semibold text-white' : 'cursor-pointer hover:bg-[#f0f4f8]' }}"
                                        style="{{ $page == $sesis->currentPage() ? 'background:#3c50e0' : 'color:#505f76' }}">
                                    {{ $page }}
                                </button>
                            @endforeach
                            <button wire:click="nextPage" @disabled(! $sesis->hasMorePages())
                                    class="w-8 h-8 inline-flex items-center justify-center rounded-lg transition-colors {{ ! $sesis->hasMorePages() ? 'cursor-not-allowed' : 'cursor-pointer hover:bg-[#f0f4f8]' }}"
                                    style="color: {{ ! $sesis->hasMorePages() ? '#c5c5d7' : '#505f76' }}">
                                <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                            </button>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    @endif

</div>
