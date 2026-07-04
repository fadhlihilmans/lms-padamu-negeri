<div>

    {{-- ── Header (8.1) ───────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center gap-3 justify-between mb-4">
        <div>
            <h1 class="text-[20px] font-bold text-on-surface">Tugas</h1>
            <p class="text-[14px] text-[#757686] mt-0.5">Pilih mata pelajaran & rombel untuk mengelola tugas</p>
        </div>
        @if ($gmrList->isNotEmpty())
            <a href="{{ route('guru.tugas.create', ['gmr' => $gmrId]) }}"
               class="inline-flex items-center gap-1.5 bg-[#3c50e0] text-white text-[14px] font-medium px-4 py-2 rounded-lg hover:bg-[#2a3db0] transition-colors shadow-sm cursor-pointer flex-shrink-0 whitespace-nowrap">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Buat Tugas
            </a>
        @endif
    </div>

    {{-- ── Modal Konfirmasi Hapus ──────────────────────────────────────────── --}}
    @if ($confirmDeleteId)
        <div class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center bg-black/50 backdrop-blur-sm p-0 sm:p-4">
            <div class="bg-white rounded-t-2xl sm:rounded-xl shadow-xl shadow-black/10 w-full max-w-4xl sm:max-w-sm p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 rounded-full bg-[#ffdad6] flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-[#ba1a1a] text-[24px]">delete_forever</span>
                    </div>
                    <div>
                        <h4 class="text-[16px] font-semibold text-on-surface">Hapus Tugas?</h4>
                        <p class="text-[13px] text-[#505f76] mt-0.5">Semua submisi peserta didik untuk tugas ini juga akan dihapus.</p>
                    </div>
                </div>
                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                    <button wire:click="cancelDelete"
                            class="w-full sm:w-auto px-5 py-2.5 text-[14px] font-medium text-[#505f76] border border-[#c5c5d7] rounded-xl hover:bg-[#f0f4f8] transition-colors cursor-pointer text-center">
                        Batal
                    </button>
                    <button wire:click="delete"
                            class="w-full sm:w-auto px-5 py-2.5 text-[14px] font-semibold text-white bg-[#ba1a1a] rounded-xl hover:bg-[#93000a] transition-colors cursor-pointer text-center">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ── Content ──────────────────────────────────────────────────────────── --}}
    @if ($gmrList->isEmpty())
        <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm px-6 py-16 text-center">
            <span class="material-symbols-outlined text-[48px] text-[#c5c5d7] mb-3 block">school</span>
            <p class="text-[15px] font-medium text-on-surface mb-1">Belum ada pemetaan mapel</p>
            <p class="text-[13px] text-[#505f76]">Hubungi Admin untuk menambahkan pemetaan mengajar Anda.</p>
        </div>
    @else

        {{-- ── GMR Filter Card ─────────────────────────────────────────────── --}}
        <div class="bg-white border border-[#c5c5d7] rounded-xl p-4 mb-4">
            <label class="text-[12px] font-medium text-[#505f76] block mb-1">Mata Pelajaran & Rombel</label>
            <select wire:model.live="gmrId"
                    class="w-full sm:max-w-md border border-[#c5c5d7] rounded-lg px-3 py-2 text-[14px] bg-white cursor-pointer focus:outline-none focus:ring-1 focus:ring-[#3c50e0]">
                <option value="">Semua Mapel</option>
                @foreach ($gmrList as $g)
                    <option value="{{ $g->id }}">{{ $g->mapel?->nama }} — {{ $g->rombel?->nama }}</option>
                @endforeach
            </select>
        </div>

        {{-- ── Toolbar ──────────────────────────────────────────────────────── --}}
        <div class="bg-white border border-[#c5c5d7] rounded-xl px-4 py-3 mb-4">
            <div class="flex flex-col sm:flex-row sm:flex-wrap gap-2 sm:items-center sm:justify-between">
                <div class="flex flex-col sm:flex-row gap-2 sm:items-center w-full sm:w-auto">
                    <div class="relative w-full sm:w-auto sm:min-w-[200px]">
                        <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-[17px] text-[#757686] pointer-events-none">search</span>
                        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari judul tugas…"
                               class="w-full pl-9 pr-3 py-2 border border-[#c5c5d7] rounded-lg text-[14px] bg-white outline-none focus:border-[#3c50e0] focus:ring-1 focus:ring-[#3c50e0]/20">
                    </div>
                    <select wire:model.live="filterStatus"
                            class="w-full sm:w-auto px-3 py-2 border border-[#c5c5d7] rounded-lg text-[14px] bg-white text-[#505f76] cursor-pointer">
                        <option value="">Semua Status</option>
                        <option value="aktif">Aktif</option>
                        <option value="lewat">Lewat Tenggat</option>
                    </select>
                </div>
                <div class="flex items-center gap-1.5 text-[14px] text-[#505f76] w-full sm:w-auto justify-between sm:justify-start">
                    Tampilkan
                    <select wire:model.live="perPage" class="border border-[#c5c5d7] rounded-lg px-2 py-1.5 text-[14px] bg-white cursor-pointer">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                    Data
                </div>
            </div>
        </div>

        @if ($tugas->isEmpty())
            <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm px-6 py-16 text-center">
                <span class="material-symbols-outlined text-[48px] text-[#c5c5d7] mb-3 block">assignment</span>
                <p class="text-[15px] font-medium text-on-surface mb-1">
                    {{ $search || $filterStatus ? 'Tidak ada tugas yang cocok' : 'Belum ada tugas' }}
                </p>
                <p class="text-[13px] text-[#505f76]">
                    {{ $search || $filterStatus ? 'Coba ubah kata kunci atau filter.' : 'Klik "Buat Tugas" untuk mulai menambahkan.' }}
                </p>
            </div>
        @else
            {{-- ── Table ──────────────────────────────────────────────────── --}}
            <div class="bg-white border border-[#c5c5d7] rounded-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-[14px]">
                        <thead>
                            <tr class="bg-white border-b border-[#c5c5d7]">
                                <th class="px-4 py-3 text-[11.5px] font-semibold text-[#505f76] uppercase tracking-wide w-10">No</th>
                                <th class="px-4 py-3 text-[11.5px] font-semibold text-[#505f76] uppercase tracking-wide">Judul Tugas</th>
                                <th class="px-4 py-3 text-[11.5px] font-semibold text-[#505f76] uppercase tracking-wide">Tenggat</th>
                                <th class="px-4 py-3 text-[11.5px] font-semibold text-[#505f76] uppercase tracking-wide">Progres</th>
                                <th class="px-4 py-3 text-[11.5px] font-semibold text-[#505f76] uppercase tracking-wide">Status</th>
                                <th class="px-4 py-3 text-[11.5px] font-semibold text-[#505f76] uppercase tracking-wide text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#c5c5d7]">
                            @foreach ($tugas as $i => $t)
                                @php
                                    $isOverdue    = $t->deadline->isPast();
                                    $totalPD      = $pdCountByGmrId[$t->guru_mapel_rombel_id] ?? 0;
                                    $submisiCount = $t->submisi_count;
                                    $progress     = $totalPD > 0 ? round(($submisiCount / $totalPD) * 100) : 0;
                                @endphp
                                <tr class="transition-colors {{ $isOverdue ? 'bg-red-50/30' : '' }}" onmouseover="this.style.background='#f6fafe'" onmouseout="this.style.background='{{ $isOverdue ? '#fef2f2' : 'transparent' }}'">
                                    <td class="px-4 py-3 text-[#757686]">{{ $tugas->firstItem() + $i }}</td>
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-on-surface">{{ $t->judul }}</p>
                                        <p class="text-[12px] text-[#757686] mt-0.5">Ditugaskan {{ $t->created_at->translatedFormat('d M Y') }}</p>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap {{ $isOverdue ? 'text-red-600 font-medium' : 'text-[#505f76]' }}">
                                        {{ $t->deadline->translatedFormat('d M Y, H:i') }}
                                    </td>
                                    <td class="px-4 py-3 min-w-[140px]">
                                        <div class="flex items-center gap-2">
                                            <div class="flex-1 h-1.5 bg-[#e4e9ed] rounded-full overflow-hidden">
                                                <div class="h-1.5 bg-[#3c50e0] rounded-full" style="width: {{ $progress }}%"></div>
                                            </div>
                                            <span class="text-[12px] text-[#505f76] whitespace-nowrap">{{ $submisiCount }}/{{ $totalPD }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($isOverdue)
                                            <span class="inline-flex items-center gap-1 text-[12px] px-2 py-0.5 rounded-full bg-red-50 text-red-600 font-medium whitespace-nowrap">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500 flex-shrink-0 inline-block"></span> Lewat Tenggat
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-[12px] px-2 py-0.5 rounded-full bg-[#EEF2FF] text-[#3c50e0] font-medium">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#3c50e0]"></span> Aktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-center gap-1">
                                            <a href="{{ route('guru.tugas.submisi', $t->id) }}" title="Lihat Submisi"
                                               class="w-8 h-8 rounded flex items-center justify-center text-[#505f76] hover:text-[#3c50e0] hover:bg-[#EEF2FF] transition-colors">
                                                <span class="material-symbols-outlined text-[18px]">folder_open</span>
                                            </a>
                                            <a href="{{ route('guru.tugas.edit', $t->id) }}" title="Edit"
                                               class="w-8 h-8 rounded flex items-center justify-center text-[#505f76] hover:text-[#3c50e0] hover:bg-[#EEF2FF] transition-colors">
                                                <span class="material-symbols-outlined text-[18px]">edit</span>
                                            </a>
                                            <button wire:click="confirmDelete({{ $t->id }})" title="Hapus"
                                                    class="w-8 h-8 rounded flex items-center justify-center text-[#505f76] hover:text-red-600 hover:bg-red-50 transition-colors cursor-pointer">
                                                <span class="material-symbols-outlined text-[18px]">delete</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($tugas->total() > 0)
                    <div class="px-4 py-3 border-t border-[#c5c5d7] flex flex-col sm:flex-row items-center justify-between gap-3 text-[13px] text-[#505f76]">
                        <span>Menampilkan {{ $tugas->firstItem() }}–{{ $tugas->lastItem() }} dari {{ $tugas->total() }} tugas</span>
                        @if ($tugas->lastPage() > 1)
                            <div class="flex items-center gap-1">
                                <button wire:click="previousPage" @disabled($tugas->onFirstPage())
                                        class="w-8 h-8 inline-flex items-center justify-center rounded-lg transition-colors {{ $tugas->onFirstPage() ? 'cursor-not-allowed' : 'cursor-pointer hover:bg-[#f0f4f8]' }}"
                                        style="color: {{ $tugas->onFirstPage() ? '#c5c5d7' : '#505f76' }}">
                                    <span class="material-symbols-outlined text-[16px]">chevron_left</span>
                                </button>
                                @foreach (range(1, $tugas->lastPage()) as $page)
                                    <button wire:click="gotoPage({{ $page }})"
                                            class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-[13px] transition-colors {{ $page == $tugas->currentPage() ? 'font-semibold text-white' : 'cursor-pointer hover:bg-[#f0f4f8]' }}"
                                            style="{{ $page == $tugas->currentPage() ? 'background:#3c50e0' : 'color:#505f76' }}">
                                        {{ $page }}
                                    </button>
                                @endforeach
                                <button wire:click="nextPage" @disabled(! $tugas->hasMorePages())
                                        class="w-8 h-8 inline-flex items-center justify-center rounded-lg transition-colors {{ ! $tugas->hasMorePages() ? 'cursor-not-allowed' : 'cursor-pointer hover:bg-[#f0f4f8]' }}"
                                        style="color: {{ ! $tugas->hasMorePages() ? '#c5c5d7' : '#505f76' }}">
                                    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                                </button>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @endif
    @endif

</div>
