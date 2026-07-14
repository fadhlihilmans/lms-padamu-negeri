<div>

    {{-- ── Header (7.1) ───────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center gap-3 justify-between mb-4">
        <div>
            <h1 class="text-[20px] font-bold text-on-surface">Materi Pembelajaran</h1>
            <p class="text-[14px] text-[#757686] mt-0.5">Pilih mata pelajaran & rombel untuk mengelola materi</p>
        </div>
        @if ($gmrId)
            <a href="{{ route('guru.materi.create', ['gmr' => $gmrId]) }}"
               class="inline-flex items-center gap-1.5 bg-[#3c50e0] text-white text-[14px] font-medium px-4 py-2 rounded-lg hover:bg-[#2a3db0] transition-colors shadow-sm cursor-pointer flex-shrink-0 whitespace-nowrap">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Tambah Materi
            </a>
        @endif
    </div>

    {{-- ── GMR Filter Card ─────────────────────────────────────────────────── --}}
    <div class="bg-white border border-[#c5c5d7] rounded-xl p-4 mb-4">
        <label class="text-[12px] font-medium text-[#505f76] block mb-1">Mata Pelajaran & Rombel</label>
        <select wire:model.live="gmrId"
                class="w-full sm:max-w-md border border-[#c5c5d7] rounded-lg px-3 py-2 text-[14px] bg-white cursor-pointer focus:outline-none focus:ring-1 focus:ring-[#3c50e0]">
            <option value="">— Pilih Mapel & Rombel —</option>
            @foreach ($pemetaan as $p)
                <option value="{{ $p->id }}">{{ $p->mapel->nama }} — {{ $p->rombel->nama }}</option>
            @endforeach
        </select>
    </div>

    {{-- ── Context banner ──────────────────────────────────────────────────── --}}
    @if ($gmrSelected)
        <div class="bg-[#EEF2FF] border border-[#3c50e0]/30 rounded-lg px-4 py-2.5 flex flex-wrap items-center gap-x-2 gap-y-0.5 text-[14px] mb-4">
            <span class="material-symbols-outlined text-[18px] text-[#3c50e0]">info</span>
            <span class="text-[#3c50e0] font-medium">{{ $gmrSelected->mapel->nama }}</span>
            <span class="w-full sm:w-auto text-[#505f76]">
                — {{ $gmrSelected->rombel->paket->nama ?? '' }} · {{ $gmrSelected->rombel->nama }}
                @if ($gmrSelected->rombel->tahun_ajaran)
                    · TA {{ $gmrSelected->rombel->tahun_ajaran }}
                @endif
            </span>
        </div>
    @endif

    {{-- ── Toolbar ──────────────────────────────────────────────────────────── --}}
    @if ($gmrId)
        <div class="bg-white border border-[#c5c5d7] rounded-xl px-4 py-3 mb-4">
            <div class="flex flex-col sm:flex-row sm:flex-wrap gap-2 sm:items-center sm:justify-between">
                <div class="relative w-full sm:w-auto sm:min-w-[220px]">
                    <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-[17px] text-[#757686] pointer-events-none">search</span>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari judul materi…"
                           class="w-full pl-9 pr-3 py-2 border border-[#c5c5d7] rounded-lg text-[14px] bg-white outline-none focus:border-[#3c50e0] focus:ring-1 focus:ring-[#3c50e0]/20">
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
    @endif

    {{-- ── Modal Konfirmasi Hapus ───────────────────────────────────────────── --}}
    @if ($confirmDeleteId)
        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/50 backdrop-blur-sm p-0 sm:p-4">
            <div class="bg-white rounded-t-2xl sm:rounded-xl shadow-xl shadow-black/10 w-full max-w-4xl sm:max-w-sm border border-[#c5c5d7] p-6 flex flex-col gap-4">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full bg-[#ffdad6] flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-[#ba1a1a]">delete</span>
                    </div>
                    <div>
                        <h4 class="text-[16px] font-semibold text-on-surface">Hapus materi ini?</h4>
                        <p class="text-[14px] text-[#505f76] mt-1">Materi dan semua lampirannya akan dihapus permanen.</p>
                    </div>
                </div>
                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                    <button wire:click="$set('confirmDeleteId', null)"
                            class="w-full sm:w-auto px-4 py-2.5 rounded-lg border border-[#c5c5d7] text-[14px] text-[#505f76] hover:bg-[#f0f4f8] transition-colors cursor-pointer text-center">
                        Batal
                    </button>
                    <button wire:click="delete"
                            class="w-full sm:w-auto px-4 py-2.5 rounded-lg bg-[#ba1a1a] text-white text-[14px] font-medium hover:bg-[#93000a] transition-colors cursor-pointer text-center">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ── Content ──────────────────────────────────────────────────────────── --}}
    @if ($pemetaan->isEmpty())
        <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm px-6 py-16 text-center">
            <span class="material-symbols-outlined text-[48px] text-[#c5c5d7] mb-3 block">hub</span>
            <p class="text-[15px] font-medium text-on-surface mb-1">Belum ada pemetaan di periode ini</p>
            <p class="text-[13px] text-[#505f76]">Hubungi Admin untuk menambahkan pemetaan Anda.</p>
        </div>

    @elseif (! $gmrId)
        <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm px-6 py-16 text-center">
            <span class="material-symbols-outlined text-[48px] text-[#c5c5d7] mb-3 block">menu_book</span>
            <p class="text-[14px] text-[#505f76]">Pilih mata pelajaran dan rombel di atas.</p>
        </div>

    @elseif ($materi->isEmpty())
        <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm px-6 py-16 text-center">
            <span class="material-symbols-outlined text-[48px] text-[#c5c5d7] mb-3 block">menu_book</span>
            <p class="text-[15px] font-medium text-on-surface mb-1">{{ $search ? 'Tidak ada hasil' : 'Belum ada materi' }}</p>
            @if (! $search)
                <a href="{{ route('guru.materi.create', ['gmr' => $gmrId]) }}"
                   class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[#3c50e0] text-white text-[14px] font-medium hover:bg-[#1c33c8] transition-colors cursor-pointer">
                    <span class="material-symbols-outlined text-[16px]">add</span>
                    Tambah Materi Pertama
                </a>
            @endif
        </div>

    @else
        <div class="flex flex-col gap-3">
            @foreach ($materi as $m)
                @php
                    $tipeGroups = $m->lampiran->groupBy('tipe');
                    if ($tipeGroups->has('link_video')) {
                        $iconBg = 'bg-[#EEF2FF]'; $iconColor = 'text-[#3c50e0]'; $icon = 'play_circle';
                    } elseif ($tipeGroups->has('gambar')) {
                        $iconBg = 'bg-purple-50'; $iconColor = 'text-purple-600'; $icon = 'image';
                    } elseif ($tipeGroups->has('file')) {
                        $iconBg = 'bg-red-50'; $iconColor = 'text-red-600'; $icon = 'picture_as_pdf';
                    } else {
                        $iconBg = 'bg-green-50'; $iconColor = 'text-green-600'; $icon = 'article';
                    }
                    $deskripsi = $m->ringkasan(110);
                @endphp
                <div class="bg-white border border-[#c5c5d7] rounded-xl p-4 hover:border-[#3c50e0]/40 hover:shadow-sm transition-all group">
                    <div class="flex items-start gap-3 sm:gap-4">
                        <div class="w-11 h-11 rounded-lg {{ $iconBg }} {{ $iconColor }} flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-[22px]" style="font-variation-settings:'FILL' 1">{{ $icon }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <h3 class="text-[14px] font-semibold text-on-surface">{{ $m->judul }}</h3>
                                <span class="hidden sm:block text-[12px] text-[#757686] whitespace-nowrap ml-2 flex-shrink-0">{{ $m->created_at->translatedFormat('d M Y') }}</span>
                            </div>
                            @if ($deskripsi)
                                <p class="text-[12px] text-[#757686] mt-0.5 line-clamp-2">{{ $deskripsi }}</p>
                            @endif
                            <div class="flex flex-wrap items-center gap-2 mt-2">
                                @if ($deskripsi)
                                    <span class="inline-flex items-center gap-1 text-[12px] px-2 py-0.5 rounded-full bg-green-50 text-green-600 font-medium">
                                        <span class="material-symbols-outlined text-[12px]">article</span> Teks
                                    </span>
                                @endif
                                @foreach ($tipeGroups as $tipe => $items)
                                    @php
                                        $badge = match ($tipe) {
                                            'file'       => ['bg-red-50', 'text-red-600', 'description', 'File'],
                                            'gambar'     => ['bg-purple-50', 'text-purple-600', 'image', 'Gambar'],
                                            'link_video' => ['bg-[#EEF2FF]', 'text-[#3c50e0]', 'link', 'Link Video'],
                                            default      => ['bg-[#f0f4f8]', 'text-[#505f76]', 'attachment', $tipe],
                                        };
                                    @endphp
                                    <span class="inline-flex items-center gap-1 text-[12px] px-2 py-0.5 rounded-full {{ $badge[0] }} {{ $badge[1] }} font-medium">
                                        <span class="material-symbols-outlined text-[12px]">{{ $badge[2] }}</span> {{ $items->count() }} {{ $badge[3] }}
                                    </span>
                                @endforeach
                            </div>
                            <p class="text-[12px] text-[#757686] mt-2 sm:hidden">{{ $m->created_at->translatedFormat('d M Y') }}</p>
                        </div>
                        <div class="hidden sm:flex items-center gap-1 flex-shrink-0">
                            <a href="{{ route('guru.materi.show', $m->id) }}" title="Lihat detail"
                               class="w-8 h-8 rounded flex items-center justify-center text-[#505f76] hover:text-[#3c50e0] hover:bg-[#EEF2FF] transition-colors">
                                <span class="material-symbols-outlined text-[18px]">visibility</span>
                            </a>
                            <a href="{{ route('guru.materi.edit', $m->id) }}" aria-label="Edit"
                               class="w-8 h-8 rounded flex items-center justify-center text-[#505f76] hover:text-[#3c50e0] hover:bg-[#EEF2FF] transition-colors">
                                <span class="material-symbols-outlined text-[18px]">edit</span>
                            </a>
                            <button wire:click="confirmDelete({{ $m->id }})" aria-label="Hapus"
                                    class="w-8 h-8 rounded flex items-center justify-center text-[#505f76] hover:text-red-600 hover:bg-red-50 transition-colors cursor-pointer">
                                <span class="material-symbols-outlined text-[18px]">delete</span>
                            </button>
                        </div>
                    </div>
                    <div class="flex sm:hidden items-center justify-end gap-2 mt-3 pt-3 border-t border-[#c5c5d7]">
                        <a href="{{ route('guru.materi.show', $m->id) }}"
                           class="flex items-center gap-1 px-3 py-1.5 rounded-lg border border-[#c5c5d7] text-[12px] font-medium text-[#505f76] hover:bg-[#f0f4f8]">
                            <span class="material-symbols-outlined text-[16px]">visibility</span> Detail
                        </a>
                        <a href="{{ route('guru.materi.edit', $m->id) }}"
                           class="flex items-center gap-1 px-3 py-1.5 rounded-lg border border-[#c5c5d7] text-[12px] font-medium text-[#505f76] hover:bg-[#f0f4f8]">
                            <span class="material-symbols-outlined text-[16px]">edit</span> Edit
                        </a>
                        <button wire:click="confirmDelete({{ $m->id }})"
                                class="flex items-center gap-1 px-3 py-1.5 rounded-lg border border-[#c5c5d7] text-[12px] font-medium text-red-600 hover:bg-red-50 cursor-pointer">
                            <span class="material-symbols-outlined text-[16px]">delete</span> Hapus
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        @if ($materi->total() > 0)
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mt-6">
                <p class="text-[13px]" style="color: #505f76">
                    Menampilkan {{ $materi->firstItem() }}–{{ $materi->lastItem() }} dari {{ $materi->total() }} materi
                </p>
                @if ($materi->lastPage() > 1)
                    <div class="flex items-center gap-1">
                        <button wire:click="previousPage" @disabled($materi->onFirstPage())
                                class="w-8 h-8 inline-flex items-center justify-center rounded-lg transition-colors {{ $materi->onFirstPage() ? 'cursor-not-allowed' : 'cursor-pointer hover:bg-[#f0f4f8]' }}"
                                style="color: {{ $materi->onFirstPage() ? '#c5c5d7' : '#505f76' }}">
                            <span class="material-symbols-outlined text-[16px]">chevron_left</span>
                        </button>
                        @foreach (range(1, $materi->lastPage()) as $page)
                            <button wire:click="gotoPage({{ $page }})"
                                    class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-[13px] transition-colors {{ $page == $materi->currentPage() ? 'font-semibold text-white' : 'cursor-pointer hover:bg-[#f0f4f8]' }}"
                                    style="{{ $page == $materi->currentPage() ? 'background:#3c50e0' : 'color:#505f76' }}">
                                {{ $page }}
                            </button>
                        @endforeach
                        <button wire:click="nextPage" @disabled(! $materi->hasMorePages())
                                class="w-8 h-8 inline-flex items-center justify-center rounded-lg transition-colors {{ ! $materi->hasMorePages() ? 'cursor-not-allowed' : 'cursor-pointer hover:bg-[#f0f4f8]' }}"
                                style="color: {{ ! $materi->hasMorePages() ? '#c5c5d7' : '#505f76' }}">
                            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                        </button>
                    </div>
                @endif
            </div>
        @endif
    @endif

</div>
