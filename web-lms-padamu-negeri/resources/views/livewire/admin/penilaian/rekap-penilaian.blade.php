<div>

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
        <div>
            <h2 class="text-[18px] font-bold text-[#171c1f]">Rekap Penilaian</h2>
            <p class="text-[13px] text-[#757686] mt-0.5">
                Pantau progres input nilai &amp; status rapor tiap rombel
                @if ($periode) · TA {{ $periode->tahun_ajaran }} {{ ucfirst($periode->semester) }} @endif
            </p>
        </div>
    </div>

    @if (! $periode)
        <div class="bg-white rounded-xl border p-10 text-center" style="border-color:#c5c5d7">
            <span class="material-symbols-outlined text-[40px] mb-2 block" style="color:#c5c5d7">event_busy</span>
            <p class="text-[13.5px]" style="color:#757686">Belum ada periode ajaran aktif. Atur periode aktif terlebih dahulu.</p>
        </div>
    @else

    {{-- ── Tabel ───────────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border overflow-hidden" style="border-color:#c5c5d7; box-shadow:0 1px 3px rgba(0,0,0,0.05)">

        {{-- Toolbar --}}
        <div class="flex flex-col sm:flex-row sm:flex-wrap sm:items-center gap-2.5 px-4 py-3 border-b bg-white" style="border-color:#d1d5db">
            <div class="relative w-full sm:flex-1 sm:min-w-[180px]">
                <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-[17px] pointer-events-none" style="color:#9da4b0">search</span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama rombel..."
                       class="w-full pl-9 pr-3 py-2 rounded-lg text-[13.5px] bg-white outline-none transition-all"
                       style="border:1.5px solid #c5c5d7; color:#171c1f"
                       onfocus="this.style.borderColor='#3c50e0'; this.style.boxShadow='0 0 0 3px rgba(60,80,224,0.1)'"
                       onblur="this.style.borderColor='#c5c5d7'; this.style.boxShadow='none'">
            </div>
            <select wire:model.live="status" class="w-full sm:w-auto py-2 pl-2.5 pr-8 rounded-lg text-[13px] cursor-pointer outline-none sm:flex-shrink-0"
                    style="border:1.5px solid #c5c5d7; color:#505f76; background:white">
                <option value="">Semua Status</option>
                <option value="belum">Belum Dinilai</option>
                <option value="draft">Draft</option>
                <option value="terbit">Terbit</option>
            </select>
            <div class="flex items-center gap-1.5 w-full sm:w-auto justify-between sm:justify-start sm:flex-shrink-0">
                <span class="text-[12.5px] whitespace-nowrap" style="color:#757686">Tampil</span>
                <select wire:model.live="perPage" class="py-2 pl-2.5 pr-6 rounded-lg text-[13px] cursor-pointer outline-none"
                        style="border:1.5px solid #c5c5d7; color:#505f76; background:white">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
                <span class="text-[12.5px] whitespace-nowrap" style="color:#757686">Data</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left min-w-[760px]">
                <thead>
                    <tr class="border-b" style="border-color:#c5c5d7">
                        <th class="px-4 py-3 text-[11.5px] font-semibold uppercase tracking-wide w-12" style="color:#757686">No</th>
                        <th class="px-4 py-3 text-[11.5px] font-semibold uppercase tracking-wide" style="color:#757686">Nama Rombel</th>
                        <th class="px-4 py-3 text-[11.5px] font-semibold uppercase tracking-wide" style="color:#757686">Wali Kelas</th>
                        <th class="px-4 py-3 text-[11.5px] font-semibold uppercase tracking-wide text-center w-16" style="color:#757686">PD</th>
                        <th class="px-4 py-3 text-[11.5px] font-semibold uppercase tracking-wide w-52" style="color:#757686">Progres Mapel</th>
                        <th class="px-4 py-3 text-[11.5px] font-semibold uppercase tracking-wide text-center w-32" style="color:#757686">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#c5c5d7]">
                    @forelse ($paginator as $i => $row)
                        <tr class="transition-colors" onmouseover="this.style.background='#f6fafe'" onmouseout="this.style.background='transparent'">
                            <td class="px-4 py-3.5 text-[13px]" style="color:#9da4b0">{{ $paginator->firstItem() + $i }}</td>
                            <td class="px-4 py-3.5">
                                <p class="text-[13.5px] font-semibold" style="color:#171c1f">{{ $row->rombel->nama }}</p>
                                <span class="inline-block mt-0.5 px-2 py-0.5 rounded-full text-[10.5px] font-semibold" style="background:#EEF2FF; color:#3c50e0">{{ $row->rombel->paket?->nama ?? '—' }}</span>
                            </td>
                            <td class="px-4 py-3.5 text-[13px]" style="color:#505f76">{{ $row->rombel->waliKelas?->nama_lengkap ?? 'Belum ditentukan' }}</td>
                            <td class="px-4 py-3.5 text-center text-[13.5px] font-semibold" style="color:#171c1f">{{ $row->totalPd }}</td>
                            <td class="px-4 py-3.5">
                                @if ($row->totalMapel === 0)
                                    <span class="text-[12px]" style="color:#9da4b0">Belum ada mapel</span>
                                @else
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 h-2 rounded-full overflow-hidden" style="background:#f0f4f8">
                                            <div class="h-full rounded-full" style="width:{{ $row->pct }}%; background:{{ $row->pct == 100 ? '#16a34a' : '#3c50e0' }}"></div>
                                        </div>
                                        <span class="text-[12px] font-medium whitespace-nowrap" style="color:#505f76">{{ $row->lengkapMapel }}/{{ $row->totalMapel }}</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                @php
                                    $badge = match ($row->status) {
                                        'terbit' => ['Terbit', '#16a34a', '#f0fdf4'],
                                        'draft'  => ['Draft', '#d97706', '#fffbeb'],
                                        default  => ['Belum Dinilai', '#757686', '#f0f4f8'],
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold whitespace-nowrap"
                                      style="background:{{ $badge[2] }}; color:{{ $badge[1] }}">{{ $badge[0] }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-16 text-center">
                                @if ($search || $status)
                                    <span class="material-symbols-outlined text-[40px] mb-2 block" style="color:#c5c5d7">search_off</span>
                                    <p class="text-[13px]" style="color:#757686">Tidak ada rombel yang cocok dengan filter.</p>
                                    <button wire:click="resetFilter" class="mt-2 text-[12.5px] cursor-pointer hover:underline" style="color:#3c50e0">Hapus Filter</button>
                                @else
                                    <span class="material-symbols-outlined text-[40px] mb-2 block" style="color:#c5c5d7">grade</span>
                                    <p class="text-[13px]" style="color:#757686">Belum ada rombel pada periode ini.</p>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($paginator->total() > 0)
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-4 py-3.5 border-t" style="border-color:#c5c5d7">
                <p class="text-[12.5px]" style="color:#757686">
                    Menampilkan {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} dari {{ $paginator->total() }} data
                </p>
                @if ($paginator->lastPage() > 1)
                    <div class="flex items-center gap-1">
                        <button wire:click="previousPage" @disabled($paginator->onFirstPage())
                                class="w-8 h-8 inline-flex items-center justify-center rounded-lg transition-colors {{ $paginator->onFirstPage() ? 'cursor-not-allowed' : 'cursor-pointer hover:bg-[#f0f4f8]' }}"
                                style="color:{{ $paginator->onFirstPage() ? '#c5c5d7' : '#505f76' }}">
                            <span class="material-symbols-outlined text-[16px]">chevron_left</span>
                        </button>
                        @foreach (range(1, $paginator->lastPage()) as $page)
                            <button wire:click="gotoPage({{ $page }})"
                                    class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-[13px] transition-colors {{ $page == $paginator->currentPage() ? 'font-semibold text-white' : 'cursor-pointer hover:bg-[#f0f4f8]' }}"
                                    style="{{ $page == $paginator->currentPage() ? 'background:#3c50e0' : 'color:#505f76' }}">
                                {{ $page }}
                            </button>
                        @endforeach
                        <button wire:click="nextPage" @disabled(! $paginator->hasMorePages())
                                class="w-8 h-8 inline-flex items-center justify-center rounded-lg transition-colors {{ ! $paginator->hasMorePages() ? 'cursor-not-allowed' : 'cursor-pointer hover:bg-[#f0f4f8]' }}"
                                style="color:{{ ! $paginator->hasMorePages() ? '#c5c5d7' : '#505f76' }}">
                            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                        </button>
                    </div>
                @endif
            </div>
        @endif

    </div>
    @endif

</div>
