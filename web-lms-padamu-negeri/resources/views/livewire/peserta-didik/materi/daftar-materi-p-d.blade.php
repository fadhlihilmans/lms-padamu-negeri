<div>

    {{-- ── Header (7.2) ───────────────────────────────────────────────────── --}}
    <div class="mb-4">
        <h1 class="text-[20px] font-bold text-on-surface">Materi Pembelajaran</h1>
        <p class="text-[14px] text-[#757686] mt-0.5">Daftar materi yang tersedia untukmu</p>
    </div>

    {{-- ── Mapel filter tabs ────────────────────────────────────────────────── --}}
    @if ($mapels->isNotEmpty())
        <div class="flex gap-2 overflow-x-auto pb-1 mb-4" style="scrollbar-width: none">
            <button wire:click="$set('filterMapel', '')"
                    class="px-3 py-1.5 rounded-full text-[12px] font-semibold whitespace-nowrap transition-colors cursor-pointer flex-shrink-0
                           {{ $filterMapel === '' ? 'bg-[#3c50e0] text-white' : 'border border-[#c5c5d7] text-[#505f76] hover:border-[#3c50e0] hover:text-[#3c50e0]' }}">
                Semua
            </button>
            @foreach ($mapels as $m)
                <button wire:click="$set('filterMapel', '{{ $m->id }}')"
                        class="px-3 py-1.5 rounded-full text-[12px] font-medium whitespace-nowrap transition-colors cursor-pointer flex-shrink-0
                               {{ (string) $filterMapel === (string) $m->id ? 'bg-[#3c50e0] text-white font-semibold' : 'border border-[#c5c5d7] text-[#505f76] hover:border-[#3c50e0] hover:text-[#3c50e0]' }}">
                    {{ $m->nama }}
                </button>
            @endforeach
        </div>
    @endif

    {{-- ── Toolbar ──────────────────────────────────────────────────────────── --}}
    <div class="bg-white border border-[#c5c5d7] rounded-xl px-4 py-3 mb-4 flex flex-col sm:flex-row gap-2 sm:items-center sm:justify-between">
        <div class="relative w-full sm:w-auto sm:min-w-[220px]">
            <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-[17px] text-[#757686] pointer-events-none">search</span>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari judul materi…"
                   class="w-full pl-9 pr-3 py-2 border border-[#c5c5d7] rounded-lg text-[14px] bg-white outline-none focus:border-[#3c50e0] focus:ring-1 focus:ring-[#3c50e0]/20">
        </div>
        <div class="flex items-center gap-1.5 text-[14px] text-[#505f76] w-full sm:w-auto justify-between sm:justify-start">
            Tampilkan
            <select wire:model.live="perPage" class="border border-[#c5c5d7] rounded-lg px-2 py-1.5 text-[14px] bg-white cursor-pointer">
                <option value="15">15</option>
                <option value="30">30</option>
                <option value="50">50</option>
            </select>
            Data
        </div>
    </div>

    {{-- ── Empty States ────────────────────────────────────────────────────── --}}
    @if ($rombels->isEmpty())
        <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm px-6 py-16 text-center">
            <span class="material-symbols-outlined text-[48px] text-[#c5c5d7] mb-3 block">groups</span>
            <p class="text-[15px] font-medium text-on-surface mb-1">Anda belum terdaftar di rombel aktif</p>
            <p class="text-[13px] text-[#505f76]">Hubungi Admin untuk mendaftarkan Anda ke rombel pada periode ini.</p>
        </div>

    @elseif ($materi->isEmpty())
        <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm px-6 py-16 text-center">
            <span class="material-symbols-outlined text-[48px] text-[#c5c5d7] mb-3 block">menu_book</span>
            <p class="text-[15px] font-medium text-on-surface mb-1">
                {{ $search || $filterMapel ? 'Tidak ada materi yang cocok' : 'Belum ada materi' }}
            </p>
            <p class="text-[13px] text-[#505f76]">
                {{ $search || $filterMapel ? 'Coba ubah kata kunci atau filter.' : 'Guru belum menambahkan materi.' }}
            </p>
        </div>

    @else
        {{-- ── List Materi ─────────────────────────────────────────────────── --}}
        <div class="flex flex-col gap-2">
            @foreach ($materi as $m)
                @php
                    $hasVideo  = $m->lampiran->where('tipe', 'link_video')->isNotEmpty();
                    $hasFile   = $m->lampiran->where('tipe', 'file')->isNotEmpty();
                    $hasGambar = $m->lampiran->where('tipe', 'gambar')->isNotEmpty();
                    if ($hasVideo) {
                        $iconBg = 'bg-[#EEF2FF]'; $iconColor = 'text-[#3c50e0]'; $icon = 'play_circle';
                    } elseif ($hasGambar) {
                        $iconBg = 'bg-purple-50'; $iconColor = 'text-purple-600'; $icon = 'image';
                    } elseif ($hasFile) {
                        $iconBg = 'bg-red-50'; $iconColor = 'text-red-600'; $icon = 'picture_as_pdf';
                    } else {
                        $iconBg = 'bg-green-50'; $iconColor = 'text-green-600'; $icon = 'article';
                    }
                @endphp
                <a href="{{ route('peserta-didik.materi.detail', $m->id) }}"
                   class="block bg-white border border-[#c5c5d7] rounded-xl p-4 hover:border-[#3c50e0]/40 hover:shadow-sm transition-all group">
                    <div class="flex items-start gap-3 sm:gap-4">
                        {{-- Icon --}}
                        <div class="w-11 h-11 rounded-lg {{ $iconBg }} {{ $iconColor }} flex-shrink-0 flex items-center justify-center">
                            <span class="material-symbols-outlined text-[22px]" style="font-variation-settings:'FILL' 1">{{ $icon }}</span>
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <h3 class="text-[14px] font-semibold text-on-surface group-hover:text-[#3c50e0] transition-colors">{{ $m->judul }}</h3>
                                <span class="hidden sm:block text-[12px] text-[#757686] whitespace-nowrap ml-2 flex-shrink-0">{{ $m->created_at->translatedFormat('d M Y') }}</span>
                            </div>
                            <p class="text-[12px] text-[#757686] mt-0.5">{{ $m->guruMapelRombel?->mapel?->nama ?? '—' }}</p>
                            <div class="flex flex-wrap items-center gap-2 mt-2">
                                @if ($m->isi)
                                    <span class="inline-flex items-center gap-1 text-[12px] px-2 py-0.5 rounded-full bg-green-50 text-green-600 font-medium">
                                        <span class="material-symbols-outlined text-[12px]">article</span> Teks
                                    </span>
                                @endif
                                @if ($hasVideo)
                                    <span class="inline-flex items-center gap-1 text-[12px] px-2 py-0.5 rounded-full bg-[#EEF2FF] text-[#3c50e0] font-medium">
                                        <span class="material-symbols-outlined text-[12px]">link</span> Link Video
                                    </span>
                                @endif
                                @if ($hasFile)
                                    <span class="inline-flex items-center gap-1 text-[12px] px-2 py-0.5 rounded-full bg-red-50 text-red-600 font-medium">
                                        <span class="material-symbols-outlined text-[12px]">description</span> File
                                    </span>
                                @endif
                                @if ($hasGambar)
                                    <span class="inline-flex items-center gap-1 text-[12px] px-2 py-0.5 rounded-full bg-purple-50 text-purple-600 font-medium">
                                        <span class="material-symbols-outlined text-[12px]">image</span> Gambar
                                    </span>
                                @endif
                                <span class="hidden sm:inline-flex items-center gap-1 text-[12px] text-[#757686]">
                                    <span class="material-symbols-outlined text-[12px]">person</span> oleh {{ $m->guruMapelRombel?->guru?->nama_lengkap ?? '—' }}
                                </span>
                            </div>
                            <p class="text-[12px] text-[#757686] mt-2 sm:hidden">{{ $m->created_at->translatedFormat('d M Y') }}</p>
                        </div>

                        <span class="material-symbols-outlined text-[20px] text-[#c5c5d7] group-hover:text-[#3c50e0] flex-shrink-0 transition-colors mt-1">chevron_right</span>
                    </div>
                    <div class="flex sm:hidden items-center gap-1 mt-3 pt-3 border-t border-[#c5c5d7] text-[12px] text-[#757686]">
                        <span class="material-symbols-outlined text-[14px]">person</span> oleh {{ $m->guruMapelRombel?->guru?->nama_lengkap ?? '—' }}
                    </div>
                </a>
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
