<div class="max-w-4xl mx-auto">

    {{-- ── Back ───────────────────────────────────────────────────────────────── --}}
    <div class="flex items-center gap-3 mb-5">
        <a href="{{ route('guru.tugas.submisi', $tugas->id) }}"
           class="w-9 h-9 flex items-center justify-center rounded-lg border cursor-pointer transition-colors flex-shrink-0"
           style="border-color: #c5c5d7; background: white; color: #505f76"
           onmouseover="this.style.background='#f0f4f8'" onmouseout="this.style.background='white'">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        </a>
        <div>
            <h1 class="text-[18px] font-bold" style="color: #171c1f">Detail Submisi</h1>
            <p class="text-[13px] mt-0.5" style="color: #757686">Lihat jawaban & beri nilai peserta didik.</p>
        </div>
    </div>

    @php
        $isOverdue  = $tugas->deadline->isPast();
        $isTerlambat = $submisi && $submisi->waktu_submit->gt($tugas->deadline);
        $isTepat     = $submisi && $submisi->waktu_submit->lte($tugas->deadline);
    @endphp

    {{-- ── Header Tugas (compact) ─────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm px-6 py-5 mb-6">
        <h2 class="text-[18px] font-bold text-on-surface mb-1.5">{{ $tugas->judul }}</h2>
        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-[13px] text-[#505f76]">
            <span class="flex items-center gap-1">
                <span class="material-symbols-outlined text-[14px]">book</span>
                {{ $tugas->guruMapelRombel?->mapel?->nama }}
            </span>
            <span class="text-[#c5c5d7]">·</span>
            <span class="flex items-center gap-1">
                <span class="material-symbols-outlined text-[14px]">groups</span>
                {{ $tugas->guruMapelRombel?->rombel?->nama }}
            </span>
            <span class="text-[#c5c5d7]">·</span>
            <span class="flex items-center gap-1">
                <span class="material-symbols-outlined text-[14px]">calendar_clock</span>
                Deadline: {{ $tugas->deadline->translatedFormat('d M Y, H:i') }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- ── Kiri: Info PD + Jawaban ─────────────────────────────────────────── --}}
        <div class="lg:col-span-2 flex flex-col gap-5">

            {{-- Info PD + status --}}
            <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm p-5">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-2">
                    <div class="min-w-0">
                        <p class="text-[16px] font-semibold text-on-surface">{{ $pd->nama_lengkap }}</p>
                        <p class="text-[13px] text-[#505f76]">NIPD: {{ $pd->nipd }}</p>
                    </div>
                    {{-- Status badge --}}
                    <div class="flex-shrink-0">
                        @if ($isTepat)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[12px] font-semibold bg-[#d1f5e0] text-[#0d6e34] whitespace-nowrap">
                                <span class="material-symbols-outlined text-[14px]">check_circle</span>Tepat Waktu
                            </span>
                        @elseif ($isTerlambat)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[12px] font-semibold bg-[#ffdad6] text-[#ba1a1a] whitespace-nowrap">
                                <span class="material-symbols-outlined text-[14px]">history_toggle_off</span>Terlambat
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[12px] font-semibold bg-[#f0f4f8] text-[#505f76] border border-[#c5c5d7] whitespace-nowrap">
                                <span class="material-symbols-outlined text-[14px]">schedule</span>Belum Mengumpulkan
                            </span>
                        @endif
                    </div>
                </div>

                @if ($submisi)
                    <div class="mt-3 pt-3 border-t border-[#f0f4f8] flex items-center gap-3 text-[12px] text-[#505f76]">
                        <span class="material-symbols-outlined text-[14px]">schedule</span>
                        Dikumpulkan: {{ $submisi->waktu_submit->translatedFormat('l, d M Y · H:i') }}
                        @if ($isTerlambat)
                            <span class="text-[#ba1a1a] font-medium">
                                ({{ $submisi->waktu_submit->diffForHumans($tugas->deadline) }} setelah deadline)
                            </span>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Jawaban --}}
            @if ($submisi)
                {{-- File --}}
                @if ($submisi->file_path)
                    @php
                        $fileUrl  = Storage::url($submisi->file_path);
                        $fileName = basename($submisi->file_path);
                        $ext      = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                        $isImage  = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
                        $isPdf    = $ext === 'pdf';
                    @endphp
                    <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm p-5"
                         x-data="{ preview: null }">
                        <p class="text-[12px] font-semibold text-[#757686] uppercase tracking-widest mb-3 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[14px]">attach_file</span>File Jawaban
                        </p>

                        @if ($isImage)
                            {{-- Thumbnail + klik untuk preview modal --}}
                            <div class="flex flex-col sm:flex-row items-start gap-4">
                                <button x-on:click="preview = '{{ $fileUrl }}'"
                                        class="flex-shrink-0 cursor-pointer group">
                                    <img src="{{ $fileUrl }}" alt="{{ $fileName }}"
                                         class="w-28 h-28 object-cover rounded-xl border border-[#c5c5d7] group-hover:border-[#3c50e0] transition-colors shadow-sm">
                                </button>
                                <div class="min-w-0 w-full">
                                    <p class="text-[14px] font-medium text-on-surface mb-1 break-all">{{ $fileName }}</p>
                                    <div class="flex flex-wrap gap-2 mt-2">
                                        <button x-on:click="preview = '{{ $fileUrl }}'"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[12px] font-medium text-[#3c50e0] bg-[#EEF2FF] rounded-lg hover:bg-[#3c50e0] hover:text-white transition-colors cursor-pointer whitespace-nowrap">
                                            <span class="material-symbols-outlined text-[14px]">zoom_in</span>Lihat Gambar
                                        </button>
                                        <a href="{{ $fileUrl }}" download
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[12px] font-medium text-[#505f76] border border-[#c5c5d7] rounded-lg hover:bg-[#f0f4f8] transition-colors cursor-pointer whitespace-nowrap">
                                            <span class="material-symbols-outlined text-[14px]">download</span>Unduh
                                        </a>
                                    </div>
                                </div>
                            </div>

                            {{-- Lightbox modal --}}
                            <template x-teleport="body">
                                <div x-show="preview" x-cloak
                                     class="fixed inset-0 z-[200] flex items-center justify-center"
                                     style="background: rgba(0,0,0,0.88); display: none;"
                                     x-on:keydown.escape.window="preview = null">
                                    <button x-on:click="preview = null"
                                            class="fixed top-4 right-4 z-[201] w-10 h-10 rounded-full bg-black/50 hover:bg-black/80 flex items-center justify-center text-white transition-colors cursor-pointer">
                                        <span class="material-symbols-outlined text-[22px]">close</span>
                                    </button>
                                    <div class="w-full h-full flex items-center justify-center p-4 sm:p-12 cursor-pointer"
                                         x-on:click="preview = null">
                                        <img :src="preview" class="max-w-full max-h-[90vh] object-contain rounded-xl shadow-2xl"
                                             x-on:click.stop>
                                    </div>
                                </div>
                            </template>

                        @elseif ($isPdf)
                            {{-- PDF: tombol preview di modal iframe --}}
                            <div class="flex items-center gap-3 p-4 bg-[#f6fafe] border border-[#c5d0ff] rounded-xl">
                                <div class="w-10 h-10 rounded-lg bg-[#ffdad6] flex items-center justify-center flex-shrink-0">
                                    <span class="material-symbols-outlined text-[#ba1a1a] text-[22px]">picture_as_pdf</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[14px] font-medium text-on-surface truncate">{{ $fileName }}</p>
                                    <p class="text-[12px] text-[#505f76]">Dokumen PDF</p>
                                </div>
                                <div class="flex gap-2 flex-shrink-0">
                                    <button x-on:click="preview = '{{ $fileUrl }}'"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[12px] font-medium text-[#3c50e0] bg-[#EEF2FF] rounded-lg hover:bg-[#3c50e0] hover:text-white transition-colors cursor-pointer">
                                        <span class="material-symbols-outlined text-[14px]">visibility</span>Preview
                                    </button>
                                    <a href="{{ $fileUrl }}" target="_blank"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[12px] font-medium text-[#505f76] border border-[#c5c5d7] rounded-lg hover:bg-[#f0f4f8] transition-colors cursor-pointer">
                                        <span class="material-symbols-outlined text-[14px]">open_in_new</span>Buka
                                    </a>
                                </div>
                            </div>

                            {{-- PDF preview modal --}}
                            <template x-teleport="body">
                                <div x-show="preview" x-cloak
                                     class="fixed inset-0 z-[200] flex items-center justify-center p-4"
                                     style="background: rgba(0,0,0,0.85); display: none;"
                                     x-on:keydown.escape.window="preview = null">
                                    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl h-[90vh] flex flex-col overflow-hidden">
                                        <div class="flex items-center justify-between px-5 py-3 border-b border-[#c5c5d7]">
                                            <p class="text-[14px] font-semibold text-on-surface flex items-center gap-2">
                                                <span class="material-symbols-outlined text-[#ba1a1a] text-[18px]">picture_as_pdf</span>
                                                {{ $fileName }}
                                            </p>
                                            <button x-on:click="preview = null"
                                                    class="w-8 h-8 flex items-center justify-center rounded-lg text-[#505f76] hover:bg-[#f0f4f8] cursor-pointer">
                                                <span class="material-symbols-outlined text-[20px]">close</span>
                                            </button>
                                        </div>
                                        <iframe :src="preview" class="flex-1 w-full border-0"></iframe>
                                    </div>
                                </div>
                            </template>

                        @else
                            {{-- File lain: download --}}
                            <a href="{{ $fileUrl }}" target="_blank"
                               class="flex items-center gap-3 p-4 bg-[#f6fafe] border border-[#c5d0ff] rounded-xl hover:bg-[#EEF2FF] hover:border-[#3c50e0] transition-colors cursor-pointer group">
                                <div class="w-10 h-10 rounded-lg bg-[#EEF2FF] group-hover:bg-white flex items-center justify-center flex-shrink-0 transition-colors">
                                    <span class="material-symbols-outlined text-[#3c50e0] text-[22px]">description</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[14px] font-medium text-[#3c50e0] truncate">{{ $fileName }}</p>
                                    <p class="text-[12px] text-[#505f76]">Klik untuk unduh atau lihat</p>
                                </div>
                                <span class="material-symbols-outlined text-[20px] text-[#505f76]">open_in_new</span>
                            </a>
                        @endif
                    </div>
                @endif

                {{-- Jawaban Teks (rich text dari Trix) --}}
                @if ($submisi->isi_text)
                    <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm p-5">
                        <p class="text-[12px] font-semibold text-[#757686] uppercase tracking-widest mb-3 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[14px]">notes</span>Jawaban Teks
                        </p>
                        <div class="trix-content prose-sm text-on-surface leading-relaxed">
                            {!! $submisi->isi_text !!}
                        </div>
                    </div>
                @endif

                @if (! $submisi->file_path && ! $submisi->isi_text)
                    <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm px-5 py-10 text-center">
                        <span class="material-symbols-outlined text-[36px] text-[#c5c5d7] mb-2 block">inbox</span>
                        <p class="text-[14px] text-[#505f76]">Peserta didik belum mengisi jawaban.</p>
                    </div>
                @endif

            @else
                <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm px-5 py-14 text-center">
                    <span class="material-symbols-outlined text-[48px] text-[#c5c5d7] mb-3 block">assignment_late</span>
                    <p class="text-[15px] font-medium text-on-surface mb-1">Belum Mengumpulkan</p>
                    <p class="text-[13px] text-[#505f76]">Peserta didik belum mengumpulkan jawaban untuk tugas ini.</p>
                </div>
            @endif
        </div>

        {{-- ── Kanan: Panel Nilai ───────────────────────────────────────────────── --}}
        <div>
            <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm p-5 sticky top-6">
                <p class="text-[12px] font-semibold text-[#757686] uppercase tracking-widest mb-4 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[14px]">grade</span>Penilaian
                </p>

                @if ($submisi)
                    {{-- Input nilai --}}
                    <div class="space-y-3">
                        <label class="block text-[13px] font-medium text-on-surface">
                            {{ $submisi->nilai !== null ? 'Ubah Nilai' : 'Masukkan Nilai' }} (0–100)
                        </label>
                        <input wire:model="nilaiInput"
                               type="number" min="0" max="100"
                               placeholder="0 – 100"
                               class="w-full border border-[#c5c5d7] rounded-xl px-4 py-3 text-[22px] font-bold text-center text-on-surface focus:outline-none focus:ring-2 focus:ring-[#3c50e0]/30 focus:border-[#3c50e0] transition-colors @error('nilaiInput') border-[#ba1a1a] @enderror">
                        @error('nilaiInput')
                            <p class="text-[12px] text-[#ba1a1a] text-center">{{ $message }}</p>
                        @enderror

                        <button wire:click="saveNilai" wire:loading.attr="disabled"
                                class="w-full py-2.5 text-[14px] font-semibold text-white bg-[#3c50e0] rounded-xl hover:bg-[#2e3eb0] transition-colors disabled:opacity-60 cursor-pointer flex items-center justify-center gap-2">
                            <span wire:loading wire:target="saveNilai" class="material-symbols-outlined text-[16px] animate-spin">progress_activity</span>
                            <span class="material-symbols-outlined text-[16px]" wire:loading.class="hidden" wire:target="saveNilai">save</span>
                            {{ $submisi->nilai !== null ? 'Simpan Perubahan' : 'Simpan Nilai' }}
                        </button>
                    </div>

                @else
                    <div class="text-center py-6">
                        <span class="material-symbols-outlined text-[36px] text-[#c5c5d7] mb-2 block">block</span>
                        <p class="text-[13px] text-[#505f76]">Tidak dapat menilai — peserta didik belum mengumpulkan.</p>
                    </div>
                @endif
            </div>
        </div>

    </div>

</div>

@push('scripts')
<link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
<style>
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
