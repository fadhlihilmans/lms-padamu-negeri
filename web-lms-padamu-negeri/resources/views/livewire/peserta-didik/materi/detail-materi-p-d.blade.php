<div>

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <div class="flex items-center gap-3 mb-5">
        <a href="{{ route('peserta-didik.materi') }}"
           class="w-9 h-9 flex items-center justify-center rounded-lg border cursor-pointer transition-colors flex-shrink-0"
           style="border-color: #c5c5d7; background: white; color: #505f76"
           onmouseover="this.style.background='#f0f4f8'" onmouseout="this.style.background='white'">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        </a>
        <div>
            <h1 class="text-[18px] font-bold" style="color: #171c1f">Detail Materi</h1>
            <p class="text-[13px] mt-0.5" style="color: #757686">Baca materi & unduh lampiran yang tersedia.</p>
        </div>
    </div>

    {{-- ── Info Card ────────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm p-4 sm:p-5 mb-6">
        <h2 class="text-[16px] sm:text-[18px] font-bold text-on-surface">{{ $materi->judul }}</h2>
        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-[12.5px] mt-2" style="color: #757686">
            <span class="flex items-center gap-1">
                <span class="material-symbols-outlined text-[14px]">book</span>
                {{ $materi->guruMapelRombel?->mapel?->nama ?? '—' }}
            </span>
            <span style="color: #c5c5d7">·</span>
            <span class="flex items-center gap-1">
                <span class="material-symbols-outlined text-[14px]">person</span>
                {{ $materi->guruMapelRombel?->guru?->nama_lengkap ?? '—' }}
            </span>
            <span style="color: #c5c5d7">·</span>
            <span class="flex items-center gap-1">
                <span class="material-symbols-outlined text-[14px]">calendar_month</span>
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
        <div class="flex items-center gap-2 px-4 py-2.5 rounded-lg mb-4" style="background: #EEF2FF; border: 1px solid #3c50e04d; color: #3c50e0">
            <span class="material-symbols-outlined text-[16px]">schedule</span>
            <span class="text-[12.5px]">Perkiraan waktu baca: {{ $estimasiBaca }} menit</span>
        </div>
        <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm p-4 sm:p-6 mb-6">
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
        <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm p-4 sm:p-6 mb-6">
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
        <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm p-4 sm:p-6 mb-6" x-data="{ previewUrl: null, previewName: '' }">
            <h2 class="text-[15px] font-semibold text-on-surface flex items-center gap-2 mb-4">
                <span class="material-symbols-outlined text-[18px] text-red-600">description</span>
                File Dokumen
            </h2>
            <div class="flex flex-col gap-3">
                @foreach ($lampiranFiles as $f)
                    @php
                        [$icon, $iconColor, $iconBg] = $f->ikonWarna();
                        $isPdf = $f->ekstensi() === 'pdf';
                    @endphp
                    <div class="p-4 rounded-xl border border-[#c5c5d7] bg-[#f6fafe]">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg {{ $iconBg }} {{ $iconColor }} flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined text-[20px]">{{ $icon }}</span>
                            </div>
                            <span class="text-[14px] font-medium text-on-surface flex-1 min-w-0 truncate">{{ $f->nama_asli ?? 'File' }}</span>
                        </div>
                        <div class="flex items-center gap-2 mt-3">
                            @if ($isPdf)
                                <button type="button"
                                        x-on:click="previewUrl = '{{ Storage::url($f->file_path) }}'; previewName = '{{ addslashes($f->nama_asli) }}'"
                                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-[#c5c5d7] text-[12.5px] font-medium text-[#505f76] hover:bg-white transition-colors cursor-pointer">
                                    <span class="material-symbols-outlined text-[16px]">visibility</span> Preview
                                </button>
                            @endif
                            <a href="{{ Storage::url($f->file_path) }}" target="_blank"
                               class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-[#c5c5d7] text-[12.5px] font-medium text-[#505f76] hover:bg-white transition-colors cursor-pointer">
                                <span class="material-symbols-outlined text-[16px]">download</span> Unduh
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Modal preview PDF --}}
            <template x-teleport="body">
                <div x-show="previewUrl"
                     x-on:keydown.escape.window="previewUrl = null"
                     class="fixed inset-0 z-[200] flex items-center justify-center p-0 sm:p-4"
                     style="background: rgba(0,0,0,0.6); display: none;">
                    <div class="bg-white w-full h-full sm:h-[85vh] sm:max-w-4xl sm:rounded-xl overflow-hidden shadow-xl flex flex-col">
                        <div class="px-4 py-3 border-b border-[#c5c5d7] flex items-center justify-between flex-shrink-0">
                            <span class="text-[14px] font-medium text-on-surface truncate" x-text="previewName"></span>
                            <button type="button" x-on:click="previewUrl = null"
                                    class="text-[#757686] hover:text-[#171c1f] transition-colors cursor-pointer flex-shrink-0 ml-3">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>
                        <iframe :src="previewUrl" class="w-full flex-1" loading="lazy"></iframe>
                    </div>
                </div>
            </template>
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
    .trix-content { overflow-wrap: break-word; word-break: break-word; }
    .trix-content .attachment { max-width: 100%; margin: 0.75rem 0; }
    .trix-content .attachment img { border-radius: 0.5rem; max-width: 100% !important; width: auto !important; height: auto !important; }
    .trix-content .attachment__caption { font-size: 12px; color: #757686; margin-top: 0.25rem; overflow-wrap: break-word; word-break: break-word; }
</style>
@endpush
