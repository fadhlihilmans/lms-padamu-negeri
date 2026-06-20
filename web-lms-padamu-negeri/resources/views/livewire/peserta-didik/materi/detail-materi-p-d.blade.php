<div class="max-w-3xl mx-auto">

    {{-- ── Breadcrumb / Back ───────────────────────────────────────────────── --}}
    <div class="mb-6">
        <a href="{{ route('peserta-didik.materi') }}"
           class="inline-flex items-center gap-1.5 text-[13px] text-[#505f76] hover:text-[#3c50e0] transition-colors cursor-pointer">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            Kembali ke Daftar Materi
        </a>
    </div>

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm p-6 mb-6">
        <h1 class="text-[22px] font-bold text-on-surface mb-2">{{ $materi->judul }}</h1>
        <div class="flex flex-wrap items-center gap-3 text-[13px] text-[#505f76]">
            <span class="flex items-center gap-1">
                <span class="material-symbols-outlined text-[15px]">book</span>
                {{ $materi->guruMapelRombel?->mapel?->nama ?? '—' }}
            </span>
            <span class="text-[#c5c5d7]">·</span>
            <span class="flex items-center gap-1">
                <span class="material-symbols-outlined text-[15px]">person</span>
                {{ $materi->guruMapelRombel?->guru?->nama_lengkap ?? '—' }}
            </span>
            <span class="text-[#c5c5d7]">·</span>
            <span class="flex items-center gap-1">
                <span class="material-symbols-outlined text-[15px]">calendar_month</span>
                {{ $materi->created_at->translatedFormat('d M Y') }}
            </span>
        </div>
    </div>

    @php
        $lampiranVideos = $materi->lampiran->where('tipe', 'link_video');
        $lampiranFiles  = $materi->lampiran->where('tipe', 'file');
        $lampiranGambar = $materi->lampiran->where('tipe', 'gambar');
    @endphp

    {{-- ── Isi Teks (Rich Text) ───────────────────────────────────────────── --}}
    @if ($materi->isi)
        <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm p-6 mb-6">
            <div class="trix-content prose-sm text-on-surface leading-relaxed">
                {!! $materi->isi !!}
            </div>
        </div>
    @endif

    {{-- ── Video Embed ─────────────────────────────────────────────────────── --}}
    @if ($lampiranVideos->isNotEmpty())
        <div class="mb-6 flex flex-col gap-4">
            <h2 class="text-[15px] font-semibold text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px] text-[#3c50e0]">play_circle</span>
                Video
            </h2>
            @foreach ($lampiranVideos as $v)
                @php $ytId = $v->youtubeId(); @endphp
                @if ($ytId)
                    <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm overflow-hidden">
                        <div class="relative w-full" style="padding-bottom: 56.25%;">
                            <iframe
                                src="https://www.youtube.com/embed/{{ $ytId }}"
                                class="absolute inset-0 w-full h-full"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen
                                loading="lazy">
                            </iframe>
                        </div>
                    </div>
                @else
                    {{-- Non-YouTube video link --}}
                    <a href="{{ $v->url }}" target="_blank" rel="noopener noreferrer"
                       class="flex items-center gap-3 p-4 bg-white rounded-xl border border-[#c5d0ff] hover:bg-[#EEF2FF] transition-colors cursor-pointer">
                        <span class="material-symbols-outlined text-[#3c50e0] text-[24px]">play_circle</span>
                        <div class="flex-1 min-w-0">
                            <p class="text-[14px] font-medium text-[#1c33c8] truncate">{{ $v->url }}</p>
                            <p class="text-[12px] text-[#505f76]">Klik untuk membuka di tab baru</p>
                        </div>
                        <span class="material-symbols-outlined text-[16px] text-[#505f76]">open_in_new</span>
                    </a>
                @endif
            @endforeach
        </div>
    @endif

    {{-- ── Gambar ───────────────────────────────────────────────────────────── --}}
    @if ($lampiranGambar->isNotEmpty())
        <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm p-6 mb-6">
            <h2 class="text-[15px] font-semibold text-on-surface flex items-center gap-2 mb-4">
                <span class="material-symbols-outlined text-[18px] text-[#505f76]">image</span>
                Gambar
            </h2>
            <div x-data="{ lightbox: null }" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2">
                @foreach ($lampiranGambar as $g)
                    <button type="button"
                            x-on:click="lightbox = '{{ Storage::url($g->file_path) }}'"
                            class="block rounded-lg overflow-hidden border border-[#c5c5d7] hover:border-[#3c50e0] transition-colors cursor-pointer group aspect-square">
                        <img src="{{ Storage::url($g->file_path) }}"
                             alt="{{ $g->nama_asli ?? 'Gambar' }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200"
                             loading="lazy">
                    </button>
                @endforeach

                {{-- Lightbox overlay --}}
                <template x-teleport="body">
                    <div x-show="lightbox"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         x-on:click.self="lightbox = null"
                         x-on:keydown.escape.window="lightbox = null"
                         class="fixed inset-0 z-[200] flex items-center justify-center"
                         style="background: rgba(0,0,0,0.88); display: none;">

                        {{-- Tombol close — fixed di pojok kanan atas, selalu terlihat --}}
                        <button x-on:click="lightbox = null"
                                class="fixed top-4 right-4 z-[201] w-10 h-10 rounded-full bg-black/50 border border-white/20 text-white flex items-center justify-center hover:bg-black/70 transition-colors cursor-pointer">
                            <span class="material-symbols-outlined text-[22px]">close</span>
                        </button>

                        {{-- Gambar — klik area kosong untuk tutup --}}
                        <div class="w-full h-full flex items-center justify-center p-4 sm:p-12 cursor-pointer"
                             x-on:click="lightbox = null">
                            <img :src="lightbox" alt="Gambar"
                                 class="max-w-full max-h-[90vh] object-contain rounded-lg shadow-2xl cursor-default"
                                 x-on:click.stop
                                 loading="lazy">
                        </div>
                    </div>
                </template>
            </div>
        </div>
    @endif

    {{-- ── File Dokumen ────────────────────────────────────────────────────── --}}
    @if ($lampiranFiles->isNotEmpty())
        <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm p-6 mb-6">
            <h2 class="text-[15px] font-semibold text-on-surface flex items-center gap-2 mb-4">
                <span class="material-symbols-outlined text-[18px] text-[#ba1a1a]">description</span>
                File Dokumen
            </h2>
            <div class="flex flex-col gap-3">
                @foreach ($lampiranFiles as $f)
                    @php
                        $ext  = strtolower(pathinfo($f->nama_asli ?? '', PATHINFO_EXTENSION));
                        $isPdf = $ext === 'pdf';
                    @endphp

                    {{-- PDF Preview inline --}}
                    @if ($isPdf)
                        <div x-data="{ expanded: false }" class="border border-[#c5c5d7] rounded-xl overflow-hidden">
                            <div class="flex items-center gap-3 px-4 py-3 bg-[#f6fafe] cursor-pointer hover:bg-[#f0f4f8] transition-colors"
                                 x-on:click="expanded = !expanded">
                                <span class="material-symbols-outlined text-[#ba1a1a] text-[22px] flex-shrink-0">picture_as_pdf</span>
                                <span class="text-[14px] font-medium text-on-surface flex-1 truncate">{{ $f->nama_asli }}</span>
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <a href="{{ Storage::url($f->file_path) }}" target="_blank"
                                       x-on:click.stop
                                       class="text-[12px] text-[#3c50e0] hover:underline flex items-center gap-0.5">
                                        <span class="material-symbols-outlined text-[14px]">download</span>
                                        Unduh
                                    </a>
                                    <span class="material-symbols-outlined text-[18px] text-[#505f76] transition-transform"
                                          :class="expanded ? 'rotate-180' : ''">expand_more</span>
                                </div>
                            </div>
                            <div x-show="expanded" x-cloak class="border-t border-[#c5c5d7]">
                                <iframe src="{{ Storage::url($f->file_path) }}"
                                        class="w-full"
                                        style="height: 600px;"
                                        loading="lazy">
                                </iframe>
                            </div>
                        </div>
                    @else
                        <a href="{{ Storage::url($f->file_path) }}" target="_blank"
                           class="flex items-center gap-3 p-4 rounded-xl border border-[#c5c5d7] bg-[#f6fafe] hover:bg-[#f0f4f8] hover:border-[#3c50e0]/40 transition-colors cursor-pointer">
                            <span class="material-symbols-outlined text-[#ba1a1a] text-[22px] flex-shrink-0">description</span>
                            <span class="text-[14px] font-medium text-on-surface flex-1 truncate">{{ $f->nama_asli ?? 'Unduh File' }}</span>
                            <span class="material-symbols-outlined text-[16px] text-[#505f76]">download</span>
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    @if (! $materi->isi && $materi->lampiran->isEmpty())
        <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm px-6 py-12 text-center">
            <span class="material-symbols-outlined text-[40px] text-[#c5c5d7] mb-2 block">description</span>
            <p class="text-[14px] text-[#505f76]">Materi ini belum memiliki konten.</p>
        </div>
    @endif

</div>

@push('scripts')
<link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
<style>
    /* Render output Trix tanpa border/chrome editor */
    .trix-content h1 { font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem; }
    .trix-content h2 { font-size: 1.1rem; font-weight: 600; margin-bottom: 0.5rem; }
    .trix-content ul { list-style: disc; padding-left: 1.25rem; margin-bottom: 0.5rem; }
    .trix-content ol { list-style: decimal; padding-left: 1.25rem; margin-bottom: 0.5rem; }
    .trix-content blockquote { border-left: 3px solid #c5c5d7; padding-left: 1rem; color: #505f76; margin: 0.5rem 0; }
    .trix-content pre { background: #f0f4f8; border-radius: 0.5rem; padding: 0.75rem 1rem; font-size: 13px; overflow-x: auto; }
    .trix-content p { margin-bottom: 0.5rem; }
    .trix-content strong { font-weight: 600; }
</style>
@endpush
