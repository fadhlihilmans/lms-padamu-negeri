<div>

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <div class="mb-6">
        <h2 class="text-[24px] font-bold tracking-tight text-on-surface">Materi</h2>
        <p class="text-[14px] text-[#505f76] mt-0.5">Materi pembelajaran yang tersedia untuk Anda</p>
    </div>

    {{-- ── Filter Bar ───────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm p-4 mb-6 flex flex-col sm:flex-row gap-3">
        <div class="flex-1 relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#505f76] text-[18px] pointer-events-none">search</span>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari judul materi..."
                   class="w-full pl-10 pr-4 py-2.5 border border-[#c5c5d7] rounded-lg text-[14px] focus:outline-none focus:ring-1 focus:ring-[#3c50e0]">
        </div>
        @if ($mapels->isNotEmpty())
            <div class="sm:w-48">
                <select wire:model.live="filterMapel"
                        class="w-full border border-[#c5c5d7] rounded-lg px-4 py-2.5 text-[14px] focus:outline-none focus:ring-1 focus:ring-[#3c50e0] cursor-pointer">
                    <option value="">Semua Mapel</option>
                    @foreach ($mapels as $m)
                        <option value="{{ $m->id }}">{{ $m->nama }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        <div class="flex-shrink-0">
            <select wire:model.live="perPage"
                    class="border border-[#c5c5d7] rounded-lg px-4 py-2.5 text-[14px] focus:outline-none focus:ring-1 focus:ring-[#3c50e0] cursor-pointer">
                <option value="15">15 / halaman</option>
                <option value="30">30 / halaman</option>
                <option value="50">50 / halaman</option>
            </select>
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
                @endphp
                <a href="{{ route('peserta-didik.materi.detail', $m->id) }}"
                   class="flex items-start gap-4 bg-white rounded-xl border border-[#c5c5d7] shadow-sm px-5 py-4 hover:bg-[#f6fafe] hover:border-[#3c50e0]/40 hover:shadow transition-all cursor-pointer group">
                    {{-- Icon --}}
                    <div class="w-10 h-10 rounded-lg bg-[#EEF2FF] text-[#3c50e0] flex-shrink-0 flex items-center justify-center group-hover:bg-[#d9dbff] transition-colors mt-0.5">
                        <span class="material-symbols-outlined text-[20px]">article</span>
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <h3 class="text-[15px] font-semibold text-on-surface group-hover:text-[#3c50e0] transition-colors">{{ $m->judul }}</h3>
                        <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                            <span class="text-[12px] text-[#505f76]">{{ $m->guruMapelRombel?->mapel?->nama ?? '—' }}</span>
                            <span class="text-[#c5c5d7]">·</span>
                            <span class="text-[12px] text-[#505f76]">{{ $m->created_at->translatedFormat('d M Y') }}</span>
                            {{-- Badge lampiran --}}
                            @if ($m->isi)
                                <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full bg-[#d0e1fb]/60 text-[#505f76] text-[11px] font-medium">
                                    <span class="material-symbols-outlined text-[11px]">notes</span> Teks
                                </span>
                            @endif
                            @if ($hasVideo)
                                <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full bg-[#EEF2FF] text-[#3c50e0] text-[11px] font-medium">
                                    <span class="material-symbols-outlined text-[11px]">play_circle</span> Video
                                </span>
                            @endif
                            @if ($hasFile)
                                <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full bg-[#ffdad6]/60 text-[#ba1a1a] text-[11px] font-medium">
                                    <span class="material-symbols-outlined text-[11px]">description</span> File
                                </span>
                            @endif
                            @if ($hasGambar)
                                <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full bg-[#d3e4fe]/60 text-[#505f76] text-[11px] font-medium">
                                    <span class="material-symbols-outlined text-[11px]">image</span> Gambar
                                </span>
                            @endif
                        </div>
                    </div>

                    <span class="material-symbols-outlined text-[20px] text-[#505f76] group-hover:text-[#3c50e0] flex-shrink-0 transition-colors mt-1">chevron_right</span>
                </a>
            @endforeach
        </div>

        @if ($materi->hasPages())
            <div class="mt-6">{{ $materi->links() }}</div>
        @endif
    @endif

</div>
