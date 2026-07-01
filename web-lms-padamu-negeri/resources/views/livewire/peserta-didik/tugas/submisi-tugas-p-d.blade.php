<div class="max-w-3xl mx-auto">

    {{-- ── Back ───────────────────────────────────────────────────────────────── --}}
    <div class="mb-6">
        <a href="{{ route('peserta-didik.tugas') }}"
           class="inline-flex items-center gap-1.5 text-[13px] text-[#505f76] hover:text-[#3c50e0] transition-colors cursor-pointer">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            Kembali ke Daftar Tugas
        </a>
    </div>

    @php
        $isOverdue   = $tugas->deadline->isPast();
        $isSudah     = $submisi && $submisi->waktu_submit->lte($tugas->deadline);
        $isLate      = $submisi && $submisi->waktu_submit->gt($tugas->deadline);
        $sudahDinilai = $submisi && $submisi->nilai !== null;
    @endphp

    {{-- ── Info Tugas ───────────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-[#c5c5d7] shadow-sm p-6 mb-5">
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-4">
            <div>
                <h1 class="text-[22px] font-bold text-on-surface mb-1">{{ $tugas->judul }}</h1>
                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-[13px] text-[#505f76]">
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-[15px]">book</span>
                        {{ $tugas->guruMapelRombel?->mapel?->nama }}
                    </span>
                    <span class="text-[#c5c5d7]">·</span>
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-[15px]">person</span>
                        {{ $tugas->guruMapelRombel?->guru?->nama_lengkap }}
                    </span>
                </div>
            </div>
            <div @class([
                'flex items-center gap-2 px-4 py-2 rounded-full border text-[13px] font-semibold flex-shrink-0',
                'bg-[#ffdad6] border-[#ffb4ab] text-[#ba1a1a]' => $isOverdue,
                'bg-[#fff3cd] border-[#ffd966] text-[#856404]' => ! $isOverdue,
            ])>
                <span class="material-symbols-outlined text-[16px]">{{ $isOverdue ? 'event_busy' : 'timer' }}</span>
                Deadline: {{ $tugas->deadline->translatedFormat('d M Y, H:i') }}
                @if (! $isOverdue)
                    <span class="opacity-70 font-normal">({{ $tugas->deadline->diffForHumans() }})</span>
                @endif
            </div>
        </div>

        {{-- Deskripsi (rich text dari Trix) --}}
        @if ($tugas->deskripsi)
            <div class="p-4 bg-[#f6fafe] rounded-xl border border-[#c5d0ff] mb-4">
                <p class="text-[12px] font-semibold text-[#3c50e0] mb-2 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[14px]">assignment</span>
                    Instruksi Tugas
                </p>
                <div class="trix-content text-[14px] text-on-surface leading-relaxed">
                    {!! $tugas->deskripsi !!}
                </div>
            </div>
        @endif

        {{-- Lampiran guru --}}
        @if ($tugas->lampiran_path)
            <a href="{{ Storage::url($tugas->lampiran_path) }}" target="_blank"
               class="flex sm:inline-flex items-center gap-2 px-4 py-2.5 bg-[#f6fafe] border border-[#c5d0ff] rounded-xl text-[13px] text-[#3c50e0] hover:bg-[#EEF2FF] hover:border-[#3c50e0] transition-colors cursor-pointer min-w-0">
                <span class="material-symbols-outlined text-[18px] flex-shrink-0">download</span>
                <span class="truncate">Unduh Lampiran Guru — {{ basename($tugas->lampiran_path) }}</span>
            </a>
        @endif
    </div>

    {{-- ── Status Submisi ─────────────────────────────────────────────────────── --}}
    @if ($submisi)
        <div @class([
            'rounded-xl border p-4 mb-5 flex items-start gap-3',
            'bg-[#d1f5e0] border-[#1c8c4e]' => $isSudah,
            'bg-[#ffdad6] border-[#ba1a1a]'  => $isLate,
        ])>
            <span @class([
                'material-symbols-outlined text-[22px] flex-shrink-0 mt-0.5',
                'text-[#0d6e34]' => $isSudah,
                'text-[#ba1a1a]' => $isLate,
            ])>{{ $isSudah ? 'check_circle' : 'history_toggle_off' }}</span>
            <div class="flex-1">
                <p @class(['text-[14px] font-semibold', 'text-[#0d6e34]' => $isSudah, 'text-[#ba1a1a]' => $isLate])>
                    {{ $isSudah ? 'Tugas Sudah Dikumpulkan' : 'Dikumpulkan Terlambat' }}
                </p>
                <p @class(['text-[13px]', 'text-[#0d6e34]/80' => $isSudah, 'text-[#ba1a1a]/80' => $isLate])>
                    Dikumpulkan: {{ $submisi->waktu_submit->translatedFormat('d M Y, H:i') }}
                    @if ($isLate)
                        <span class="font-medium">({{ $submisi->waktu_submit->diffForHumans($tugas->deadline) }} setelah deadline)</span>
                    @endif
                </p>
            </div>
            @if ($submisi->nilai !== null)
                <div class="flex-shrink-0 text-center pl-3 border-l {{ $isSudah ? 'border-[#1c8c4e]/30' : 'border-[#ba1a1a]/30' }}">
                    <div @class([
                        'text-[30px] font-bold leading-none',
                        'text-[#0d6e34]' => $submisi->nilai >= 75,
                        'text-[#856404]' => $submisi->nilai >= 60 && $submisi->nilai < 75,
                        'text-[#ba1a1a]' => $submisi->nilai < 60,
                    ])>{{ $submisi->nilai }}</div>
                    <div class="text-[11px] text-[#505f76] font-semibold mt-0.5">
                        {{ $submisi->nilai >= 75 ? 'Tuntas' : ($submisi->nilai >= 60 ? 'Cukup' : 'Belum Tuntas') }}
                    </div>
                </div>
            @endif
        </div>

        {{-- Preview jawaban sebelumnya --}}
        @if ($submisi->file_path || $submisi->isi_text)
            <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm p-5 mb-5">
                <h3 class="text-[13px] font-semibold text-[#757686] uppercase tracking-widest mb-3 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[15px]">inventory</span>Jawaban Anda Saat Ini
                </h3>
                @if ($submisi->file_path)
                    <a href="{{ Storage::url($submisi->file_path) }}" target="_blank"
                       class="flex items-center gap-3 p-3.5 bg-[#f6fafe] border border-[#c5d0ff] rounded-xl mb-3 hover:border-[#3c50e0] hover:bg-[#EEF2FF] transition-colors cursor-pointer group">
                        <div class="w-9 h-9 rounded-lg bg-[#EEF2FF] group-hover:bg-white flex items-center justify-center flex-shrink-0 transition-colors">
                            <span class="material-symbols-outlined text-[#3c50e0] text-[20px]">description</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[13px] font-medium text-on-surface truncate">{{ basename($submisi->file_path) }}</p>
                            <p class="text-[12px] text-[#505f76]">Klik untuk unduh / lihat</p>
                        </div>
                        <span class="material-symbols-outlined text-[18px] text-[#505f76]">open_in_new</span>
                    </a>
                @endif
                @if ($submisi->isi_text)
                    <div class="p-4 bg-[#f6fafe] border border-[#c5c5d7] rounded-xl">
                        <p class="text-[12px] font-semibold text-[#757686] mb-2">Jawaban Teks:</p>
                        <div class="trix-content text-[14px] text-on-surface leading-relaxed">
                            {!! $submisi->isi_text !!}
                        </div>
                    </div>
                @endif
            </div>
        @endif
    @endif

    {{-- ── Form Kumpulkan / Perbarui ────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-[#c5c5d7] shadow-sm overflow-hidden">

        {{-- Header --}}
        <div class="px-4 py-3.5 border-b border-[#c5c5d7] bg-[#f6fafe] flex items-center justify-between gap-3 flex-wrap">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-[#EEF2FF] flex items-center justify-center">
                    <span class="material-symbols-outlined text-[#3c50e0] text-[20px]">
                        {{ $submisi ? 'edit_note' : 'cloud_upload' }}
                    </span>
                </div>
                <div>
                    <h3 class="text-[15px] font-semibold text-on-surface">
                        {{ $submisi ? ($sudahDinilai ? 'Kirim Ulang Jawaban' : 'Perbarui Jawaban') : 'Kumpulkan Tugas' }}
                    </h3>
                    <p class="text-[12px] text-[#505f76]">Gunakan salah satu atau keduanya</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                @if ($isOverdue && ! $submisi)
                    <span class="inline-flex items-center gap-1 text-[12px] font-semibold text-[#ba1a1a] bg-[#ffdad6] px-3 py-1.5 rounded-full">
                        <span class="material-symbols-outlined text-[14px]">warning</span>
                        Melewati deadline — akan ditandai terlambat
                    </span>
                @endif
                @if ($sudahDinilai)
                    <span class="inline-flex items-center gap-1 text-[12px] font-semibold text-[#856404] bg-[#fff3cd] px-3 py-1.5 rounded-full">
                        <span class="material-symbols-outlined text-[14px]">info</span>
                        Nilai akan direset jika kirim ulang
                    </span>
                @endif
            </div>
        </div>

        <div
            x-data="{ tab: 'file' }"
            x-on:clear-trix-submisi.window="
                const editor = $el.querySelector('trix-editor');
                if (editor) editor.editor.loadHTML('');
            "
            class="p-6">

            {{-- Tabs --}}
            <div class="flex border-b border-[#c5c5d7] mb-5 gap-1">
                <button x-on:click="tab = 'file'" type="button"
                        :class="tab === 'file'
                            ? 'border-[#3c50e0] text-[#3c50e0] bg-[#EEF2FF]'
                            : 'border-transparent text-[#505f76] hover:text-on-surface hover:bg-[#f0f4f8]'"
                        class="flex items-center gap-2 px-5 py-2.5 text-[14px] font-medium border-b-2 rounded-t-lg transition-colors cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">upload_file</span>
                    Upload File
                    @if ($fileBaru)
                        <span class="inline-block w-2 h-2 rounded-full bg-[#3c50e0]"></span>
                    @endif
                </button>
                <button x-on:click="tab = 'text'" type="button"
                        :class="tab === 'text'
                            ? 'border-[#3c50e0] text-[#3c50e0] bg-[#EEF2FF]'
                            : 'border-transparent text-[#505f76] hover:text-on-surface hover:bg-[#f0f4f8]'"
                        class="flex items-center gap-2 px-5 py-2.5 text-[14px] font-medium border-b-2 rounded-t-lg transition-colors cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">edit_document</span>
                    Tulis Jawaban
                </button>
            </div>

            {{-- Tab Upload File --}}
            <div x-show="tab === 'file'" x-cloak>
                <label class="flex flex-col items-center gap-4 p-8 border-2 border-dashed border-[#c5c5d7] rounded-xl cursor-pointer hover:border-[#3c50e0] hover:bg-[#f6fafe] transition-all group">
                    @if ($fileBaru)
                        <div class="w-14 h-14 rounded-full bg-[#EEF2FF] flex items-center justify-center">
                            <span class="material-symbols-outlined text-[#3c50e0] text-[30px]">task_alt</span>
                        </div>
                        <div class="text-center">
                            <p class="text-[15px] font-semibold text-[#3c50e0]">{{ $fileBaru->getClientOriginalName() }}</p>
                            <p class="text-[13px] text-[#505f76] mt-0.5">{{ round($fileBaru->getSize() / 1024, 1) }} KB · Klik untuk ganti</p>
                        </div>
                    @else
                        <div class="w-14 h-14 rounded-full bg-[#f0f4f8] group-hover:bg-[#EEF2FF] flex items-center justify-center transition-colors">
                            <span class="material-symbols-outlined text-[#505f76] group-hover:text-[#3c50e0] text-[30px] transition-colors">cloud_upload</span>
                        </div>
                        <div class="text-center">
                            <p class="text-[15px] font-semibold text-on-surface">Tarik & lepas file, atau klik untuk pilih</p>
                            <p class="text-[13px] text-[#505f76] mt-1">PDF, Word, gambar — maks {{ app(\App\Services\SettingService::class)->get('max_upload_tugas_mb', 10) }} MB</p>
                        </div>
                    @endif
                    <input wire:model="fileBaru" type="file"
                           accept=".pdf,.doc,.docx,.ppt,.pptx,image/*"
                           class="hidden">
                </label>
                @error('fileBaru')
                    <p class="text-[12px] text-[#ba1a1a] mt-2 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">error</span> {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Tab Tulis Jawaban — Trix editor --}}
            <div x-show="tab === 'text'" x-cloak>
                <p class="text-[13px] font-medium text-on-surface mb-2">
                    Tulis jawaban Anda secara lengkap:
                </p>
                <div wire:ignore class="border border-[#c5c5d7] rounded-xl overflow-hidden trix-wrapper">
                    <input id="trix-submisi-pd" type="hidden" value="{{ $isiText }}">
                    <trix-editor
                        input="trix-submisi-pd"
                        placeholder="Mulai menulis jawaban Anda di sini. Gunakan toolbar untuk format teks..."
                        class="trix-content min-h-[200px]"
                        x-on:trix-change="$wire.setIsiText($event.target.value)"
                        x-on:trix-file-accept.prevent>
                    </trix-editor>
                </div>
                @error('isiText')
                    <p class="text-[12px] text-[#ba1a1a] mt-2 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">error</span> {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Footer --}}
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 mt-5 pt-5 border-t border-[#c5c5d7]">
                <p class="text-[12px] text-[#505f76]">
                    @if ($submisi && $sudahDinilai)
                        Mengirim ulang akan mereset nilai — guru perlu menilai ulang.
                    @elseif ($submisi)
                        Memperbarui akan menggantikan jawaban sebelumnya.
                    @else
                        Pastikan jawaban sudah benar sebelum mengumpulkan.
                    @endif
                </p>
                <button wire:click="submit" wire:loading.attr="disabled"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 text-[14px] font-semibold text-white bg-[#3c50e0] rounded-xl hover:bg-[#2e3eb0] transition-colors disabled:opacity-60 cursor-pointer shadow-sm">
                    <span wire:loading wire:target="submit" class="material-symbols-outlined text-[16px] animate-spin">progress_activity</span>
                    <span class="material-symbols-outlined text-[18px]" wire:loading.class="hidden" wire:target="submit">send</span>
                    {{ $submisi ? 'Perbarui Jawaban' : 'Kumpulkan Tugas' }}
                </button>
            </div>

        </div>
    </div>

</div>

@push('scripts')
<link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
<script src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>
<style>
    trix-editor { border: none !important; padding: 0.75rem 1rem !important; min-height: 200px; }
    .trix-content h1 { font-size: 1.2rem; font-weight: 700; margin-bottom: 0.5rem; }
    .trix-content ul { list-style: disc; padding-left: 1.25rem; margin-bottom: 0.5rem; }
    .trix-content ol { list-style: decimal; padding-left: 1.25rem; margin-bottom: 0.5rem; }
    .trix-content blockquote { border-left: 3px solid #c5c5d7; padding-left: 1rem; color: #505f76; }
    .trix-content pre { background: #f0f4f8; border-radius: 0.5rem; padding: 0.75rem 1rem; font-size: 13px; }
    .trix-content strong { font-weight: 600; }
    .trix-content p { margin-bottom: 0.4rem; }
</style>
@endpush
