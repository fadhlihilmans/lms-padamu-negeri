<div>

    {{-- ── Header ─────────────────────────────────────────────────────────── --}}
    <div class="mb-6">
        <h2 class="text-[18px] font-bold text-on-surface">Rekap Absensi</h2>
        <p class="text-[13px] text-[#757686] mt-0.5">
            Rekap kehadiran peserta didik per sesi pengajaran
            {{ $periode ? '— ' . $periode->tahun_ajaran : '' }}.
        </p>
    </div>

    @if (! $periode)
        <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm px-6 py-16 text-center">
            <p class="text-[14px] text-[#757686]">Belum ada periode ajaran aktif. Set periode terlebih dahulu.</p>
        </div>
    @else

        {{-- ── Filter bar ──────────────────────────────────────────────────── --}}
        <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm p-5 mb-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Search --}}
                <div class="relative">
                    <span class="material-symbols-outlined text-[18px] text-[#757686] absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none">search</span>
                    <input wire:model.live.debounce.300ms="search"
                           type="text"
                           placeholder="Cari mapel, rombel, guru..."
                           class="w-full border border-[#c5c5d7] rounded-lg pl-9 pr-4 py-2.5 text-[14px] text-on-surface bg-white focus:outline-none focus:ring-1 focus:ring-[#3c50e0]">
                </div>
                {{-- Filter rombel --}}
                <select wire:model.live="filterRombelId"
                        class="border border-[#c5c5d7] rounded-lg px-4 py-2.5 text-[14px] bg-white text-on-surface focus:outline-none focus:ring-1 focus:ring-[#3c50e0] cursor-pointer">
                    <option value="">Semua Rombel</option>
                    @foreach ($rombels as $rombel)
                        <option value="{{ $rombel->id }}">{{ $rombel->nama }}</option>
                    @endforeach
                </select>
                {{-- Tanggal mulai --}}
                <input wire:model.live="filterTanggalMulai"
                       type="date"
                       class="border border-[#c5c5d7] rounded-lg px-4 py-2.5 text-[14px] bg-white text-on-surface focus:outline-none focus:ring-1 focus:ring-[#3c50e0] cursor-pointer">
                {{-- Tanggal akhir --}}
                <input wire:model.live="filterTanggalAkhir"
                       type="date"
                       class="border border-[#c5c5d7] rounded-lg px-4 py-2.5 text-[14px] bg-white text-on-surface focus:outline-none focus:ring-1 focus:ring-[#3c50e0] cursor-pointer">
            </div>
            <div class="flex items-center justify-between mt-4">
                <select wire:model.live="perPage"
                        class="border border-[#c5c5d7] rounded-lg px-3 py-1.5 text-[13px] bg-white cursor-pointer focus:outline-none focus:ring-1 focus:ring-[#3c50e0]">
                    <option value="10">10 / halaman</option>
                    <option value="15">15 / halaman</option>
                    <option value="25">25 / halaman</option>
                    <option value="50">50 / halaman</option>
                </select>
                <button wire:click="resetFilter"
                        class="text-[13px] text-[#3c50e0] hover:underline cursor-pointer flex items-center gap-1">
                    <span class="material-symbols-outlined text-[15px]">filter_alt_off</span>
                    Reset Filter
                </button>
            </div>
        </div>

        {{-- ── Tabel ───────────────────────────────────────────────────────── --}}
        <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-[14px]">
                    <thead class="bg-white border-b border-[#c5c5d7]">
                        <tr>
                            <th class="px-4 py-3 text-left text-[12px] font-semibold text-[#505f76] uppercase tracking-wide w-12">No</th>
                            <th class="px-4 py-3 text-left text-[12px] font-semibold text-[#505f76] uppercase tracking-wide">Tanggal</th>
                            <th class="px-4 py-3 text-left text-[12px] font-semibold text-[#505f76] uppercase tracking-wide">Rombel</th>
                            <th class="px-4 py-3 text-left text-[12px] font-semibold text-[#505f76] uppercase tracking-wide">Mata Pelajaran</th>
                            <th class="px-4 py-3 text-left text-[12px] font-semibold text-[#505f76] uppercase tracking-wide">Guru</th>
                            <th class="px-4 py-3 text-center text-[12px] font-semibold text-[#505f76] uppercase tracking-wide">Hadir</th>
                            <th class="px-4 py-3 text-center text-[12px] font-semibold text-[#505f76] uppercase tracking-wide">Izin</th>
                            <th class="px-4 py-3 text-center text-[12px] font-semibold text-[#505f76] uppercase tracking-wide">Sakit</th>
                            <th class="px-4 py-3 text-center text-[12px] font-semibold text-[#505f76] uppercase tracking-wide">Alpa</th>
                            <th class="px-4 py-3 text-center text-[12px] font-semibold text-[#505f76] uppercase tracking-wide">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#e8e8f0]">
                        @forelse ($sesis as $i => $sesi)
                            @php
                                $counts = $sesi->detail->groupBy('status')->map->count();
                                $total  = $sesi->detail->count();
                            @endphp
                            <tr class="hover:bg-[#f8f9fb] transition-colors">
                                <td class="px-4 py-3 text-[#757686]">{{ $sesis->firstItem() + $i }}</td>
                                <td class="px-4 py-3 text-on-surface font-medium whitespace-nowrap">
                                    {{ $sesi->tanggal->translatedFormat('d M Y') }}
                                    <p class="text-[11px] text-[#757686]">{{ $sesi->tanggal->translatedFormat('l') }}</p>
                                </td>
                                <td class="px-4 py-3 text-on-surface">{{ $sesi->guruMapelRombel->rombel->nama }}</td>
                                <td class="px-4 py-3 text-on-surface">{{ $sesi->guruMapelRombel->mapel->nama }}</td>
                                <td class="px-4 py-3 text-on-surface">{{ $sesi->guruMapelRombel->guru->nama_lengkap }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-block px-2 py-0.5 rounded text-[12px] font-semibold bg-green-100 text-green-800">
                                        {{ $counts['hadir'] ?? 0 }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-block px-2 py-0.5 rounded text-[12px] font-semibold bg-amber-100 text-amber-800">
                                        {{ $counts['izin'] ?? 0 }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-block px-2 py-0.5 rounded text-[12px] font-semibold bg-blue-100 text-blue-800">
                                        {{ $counts['sakit'] ?? 0 }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-block px-2 py-0.5 rounded text-[12px] font-semibold bg-[#ffdad6] text-[#93000a]">
                                        {{ $counts['alpa'] ?? 0 }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if ($sesi->status_sesi === 'terbuka')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-green-100 text-green-700 border border-green-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                                            Terbuka
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#f0f4f8] text-[#505f76] border border-[#c5c5d7]">
                                            <span class="material-symbols-outlined text-[12px]">lock</span>
                                            Ditutup
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-4 py-16 text-center text-[14px] text-[#757686]">
                                    <span class="material-symbols-outlined text-[40px] block mb-3 text-[#c5c5d7]">event_busy</span>
                                    Tidak ada data absensi sesuai filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($sesis->hasPages())
                <div class="px-4 py-3 border-t border-[#e8e8f0]">
                    {{ $sesis->links() }}
                </div>
            @endif
        </div>
    @endif

</div>
