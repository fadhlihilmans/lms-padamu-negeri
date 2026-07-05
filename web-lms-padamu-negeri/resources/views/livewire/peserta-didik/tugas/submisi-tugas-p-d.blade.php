<div>

    {{-- ── Back ───────────────────────────────────────────────────────────────── --}}
    <div class="mb-5">
        <a href="{{ route('peserta-didik.tugas') }}"
           class="w-9 h-9 flex items-center justify-center rounded-lg border cursor-pointer transition-colors"
           style="border-color: #c5c5d7; background: white; color: #505f76"
           onmouseover="this.style.background='#f0f4f8'" onmouseout="this.style.background='white'">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        </a>
    </div>

    @php
        $isOverdue   = $tugas->deadline->isPast();
        $isSudah     = $submisi && $submisi->waktu_submit->lte($tugas->deadline);
        $isLate      = $submisi && $submisi->waktu_submit->gt($tugas->deadline);
        $sudahDinilai = $submisi && $submisi->nilai !== null;
    @endphp

    {{-- ── Info Tugas (8.4.2) ──────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm p-4 sm:p-5 mb-5">
        <div class="flex flex-col gap-3 mb-4">
            <div class="min-w-0">
                <h1 class="text-[16px] sm:text-[18px] font-bold text-on-surface">{{ $tugas->judul }}</h1>
                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1 text-[12.5px] sm:text-[13px] text-[#505f76]">
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">book</span>
                        {{ $tugas->guruMapelRombel?->mapel?->nama }}
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">person</span>
                        {{ $tugas->guruMapelRombel?->guru?->nama_lengkap }}
                    </span>
                </div>
            </div>
            {{-- Deadline: teks berwarna saja, tanpa card/border. Keterangan relatif pindah ke baris baru di mobile --}}
            <div @class([
                'flex items-start gap-1.5 text-[12.5px] sm:text-[13px] font-semibold',
                'text-[#ba1a1a]' => $isOverdue,
                'text-[#856404]' => ! $isOverdue,
            ])>
                <span class="material-symbols-outlined text-[16px] mt-0.5 sm:mt-0">{{ $isOverdue ? 'event_busy' : 'timer' }}</span>
                <span>
                    Deadline: {{ $tugas->deadline->translatedFormat('d M Y, H:i') }}
                    @if (! $isOverdue)
                        <span class="block sm:inline opacity-70 font-normal">({{ $tugas->deadline->diffForHumans() }})</span>
                    @endif
                </span>
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

    {{-- ── Jawaban Anda Saat Ini: status + tanggal + nilai + preview digabung ──── --}}
    @if ($submisi)
        <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm overflow-hidden mb-5">
            <div class="px-4 sm:px-5 py-3.5 border-b border-[#c5c5d7] flex items-center justify-between gap-3">
                <h3 class="text-[13px] sm:text-[14px] font-semibold text-on-surface flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px] text-[#3c50e0]">inventory</span>
                    Jawaban Anda Saat Ini
                </h3>
                @if ($isSudah)
                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2.5 py-1 rounded-full whitespace-nowrap" style="background:#d1f5e0;color:#0d6e34">
                        <span class="material-symbols-outlined text-[12px]" style="font-variation-settings:'FILL' 1">check_circle</span> Sudah Dikumpulkan
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2.5 py-1 rounded-full whitespace-nowrap" style="background:#ffdad6;color:#ba1a1a">
                        <span class="material-symbols-outlined text-[12px]">history_toggle_off</span> Terlambat
                    </span>
                @endif
            </div>

            <div class="p-4 sm:p-5">
                {{-- Tanggal kumpul + nilai digabung dalam satu baris --}}
                <div class="flex flex-wrap items-center justify-between gap-3 mb-4 pb-4 border-b border-[#f0f4f8]">
                    <p class="text-[12.5px] sm:text-[13px] text-[#505f76]">
                        Dikumpulkan:
                        <span class="font-medium text-on-surface">{{ $submisi->waktu_submit->translatedFormat('d M Y, H:i') }}</span>
                        @if ($isLate)
                            <span class="text-[#ba1a1a] font-medium">({{ $submisi->waktu_submit->diffForHumans($tugas->deadline) }} setelah deadline)</span>
                        @endif
                    </p>
                    @if ($submisi->nilai !== null)
                        <div class="flex items-center gap-2">
                            <span @class([
                                'text-[20px] font-bold leading-none',
                                'text-[#0d6e34]' => $submisi->nilai >= 75,
                                'text-[#856404]' => $submisi->nilai >= 60 && $submisi->nilai < 75,
                                'text-[#ba1a1a]' => $submisi->nilai < 60,
                            ])>{{ $submisi->nilai }}</span>
                            <span @class([
                                'text-[11px] font-semibold px-2 py-0.5 rounded-full',
                                'bg-[#d1f5e0] text-[#0d6e34]' => $submisi->nilai >= 75,
                                'bg-[#fff3cd] text-[#856404]' => $submisi->nilai >= 60 && $submisi->nilai < 75,
                                'bg-[#ffdad6] text-[#ba1a1a]' => $submisi->nilai < 60,
                            ])>{{ $submisi->nilai >= 75 ? 'Tuntas' : ($submisi->nilai >= 60 ? 'Cukup' : 'Belum Tuntas') }}</span>
                        </div>
                    @else
                        <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-[#f0f4f8] text-[#505f76]">Belum dinilai</span>
                    @endif
                </div>

                {{-- Preview jawaban --}}
                @if ($submisi->file_path)
                    <a href="{{ Storage::url($submisi->file_path) }}" target="_blank"
                       class="flex items-center gap-3 p-3.5 bg-[#f6fafe] border border-[#c5d0ff] rounded-xl {{ $submisi->isi_text ? 'mb-3' : '' }} hover:border-[#3c50e0] hover:bg-[#EEF2FF] transition-colors cursor-pointer group">
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

                {{-- Tombol buka modal kirim ulang --}}
                <button wire:click="openSubmitModal" type="button"
                        class="w-full sm:w-auto mt-4 inline-flex items-center justify-center gap-2 px-5 py-2.5 text-[14px] font-semibold rounded-xl border transition-colors cursor-pointer"
                        style="color: #3c50e0; border-color: #c5d0ff; background: #EEF2FF"
                        onmouseover="this.style.background='#dde5ff'" onmouseout="this.style.background='#EEF2FF'">
                    <span class="material-symbols-outlined text-[18px]">refresh</span>
                    Kirim Ulang Jawaban
                </button>
            </div>
        </div>
    @else
        {{-- Belum ada jawaban sama sekali --}}
        <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm p-6 sm:p-8 text-center mb-5">
            <div class="w-14 h-14 rounded-full bg-[#f0f4f8] flex items-center justify-center mx-auto mb-3">
                <span class="material-symbols-outlined text-[28px] text-[#757686]">cloud_upload</span>
            </div>
            <p class="text-[14px] sm:text-[15px] font-semibold text-on-surface mb-1">Belum Ada Jawaban Dikumpulkan</p>
            <p class="text-[12.5px] sm:text-[13px] text-[#505f76] mb-4">Klik tombol di bawah untuk mulai mengumpulkan jawaban.</p>
            @if ($isOverdue)
                <span class="inline-flex items-center gap-1 text-[11.5px] sm:text-[12px] font-semibold text-[#ba1a1a] bg-[#ffdad6] px-3 py-1.5 rounded-full mb-4 whitespace-nowrap">
                    <span class="material-symbols-outlined text-[14px]">warning</span>
                    Melewati deadline — akan ditandai terlambat
                </span>
                <br>
            @endif
            <button wire:click="openSubmitModal" type="button"
                    class="inline-flex items-center gap-2 px-6 py-2.5 text-[14px] font-semibold text-white rounded-xl transition-colors shadow-sm cursor-pointer"
                    style="background: #3c50e0" onmouseover="this.style.background='#2e3eb0'" onmouseout="this.style.background='#3c50e0'">
                <span class="material-symbols-outlined text-[18px]">send</span>
                Kumpulkan Jawaban
            </button>
        </div>
    @endif

    {{-- ── Modal: Kumpulkan / Kirim Ulang Jawaban ──────────────────────────────── --}}
    @if ($showSubmitModal)
        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/40 backdrop-blur-sm p-0 sm:p-4"
             wire:keydown.escape="closeSubmitModal">
            {{-- Desktop: modal dilebarkan & dibuat penuh atas-bawah --}}
            <div class="bg-white w-full sm:max-w-3xl rounded-t-2xl sm:rounded-xl overflow-hidden shadow-xl flex flex-col max-h-[92vh] sm:h-[90vh]"
                 x-data="{ tab: 'file' }"
                 x-on:clear-trix-submisi.window="
                    const editor = $el.querySelector('trix-editor');
                    if (editor) editor.editor.loadHTML('');
                 ">

                {{-- Header --}}
                <div class="px-5 py-4 border-b border-[#c5c5d7] flex items-center justify-between flex-shrink-0">
                    <h2 class="text-[15px] sm:text-[16px] font-semibold text-on-surface">
                        {{ $submisi ? ($sudahDinilai ? 'Kirim Ulang Jawaban' : 'Perbarui Jawaban') : 'Kumpulkan Jawaban' }}
                    </h2>
                    <button wire:click="closeSubmitModal" type="button" class="text-[#757686] hover:text-on-surface transition-colors cursor-pointer">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <div class="p-4 sm:p-5 overflow-y-auto space-y-4 flex-1">
                    @if ($sudahDinilai)
                        <span class="inline-flex items-center gap-1 text-[11.5px] sm:text-[12px] font-semibold text-[#856404] bg-[#fff3cd] px-3 py-1.5 rounded-full whitespace-nowrap">
                            <span class="material-symbols-outlined text-[14px]">info</span>
                            Nilai akan direset jika kirim ulang
                        </span>
                    @endif

                    {{-- Tabs --}}
                    <div class="grid grid-cols-2 border-b border-[#c5c5d7] gap-1">
                        <button x-on:click="tab = 'file'" type="button"
                                :class="tab === 'file'
                                    ? 'border-[#3c50e0] text-[#3c50e0] bg-[#EEF2FF]'
                                    : 'border-transparent text-[#505f76] hover:text-on-surface hover:bg-[#f0f4f8]'"
                                class="flex items-center justify-center gap-2 px-3 py-2.5 text-[13px] sm:text-[14px] font-medium border-b-2 rounded-t-lg transition-colors cursor-pointer">
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
                                class="flex items-center justify-center gap-2 px-3 py-2.5 text-[13px] sm:text-[14px] font-medium border-b-2 rounded-t-lg transition-colors cursor-pointer">
                            <span class="material-symbols-outlined text-[18px]">edit_document</span>
                            Tulis Jawaban
                        </button>
                    </div>

                    {{-- Tab Upload File --}}
                    <div x-show="tab === 'file'" x-cloak>
                        <label class="flex flex-col items-center gap-3 sm:gap-4 p-6 sm:p-10 border-2 border-dashed border-[#c5c5d7] rounded-xl cursor-pointer hover:border-[#3c50e0] hover:bg-[#f6fafe] transition-all group">
                            {{-- Indikator sedang mengunggah --}}
                            <div wire:loading wire:target="fileBaru" class="flex flex-col items-center gap-3">
                                <div class="w-14 h-14 rounded-full bg-[#EEF2FF] flex items-center justify-center">
                                    <span class="material-symbols-outlined text-[#3c50e0] text-[30px] animate-spin">progress_activity</span>
                                </div>
                                <p class="text-[15px] font-semibold text-[#3c50e0]">Mengunggah file…</p>
                            </div>
                            <div class="contents" wire:loading.remove wire:target="fileBaru">
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
                            </div>
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
                </div>

                {{-- Footer --}}
                <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-t border-[#c5c5d7] flex-shrink-0">
                    <p class="text-[12px] text-[#505f76] hidden sm:block">
                        @if ($submisi && $sudahDinilai)
                            Mengirim ulang akan mereset nilai — guru perlu menilai ulang.
                        @elseif ($submisi)
                            Memperbarui akan menggantikan jawaban sebelumnya.
                        @else
                            Pastikan jawaban sudah benar sebelum mengumpulkan.
                        @endif
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button wire:click="closeSubmitModal" type="button"
                                class="px-4 py-2.5 text-[14px] font-medium rounded-lg border transition-colors cursor-pointer"
                                style="border-color: #c5c5d7; color: #505f76; background: white"
                                onmouseover="this.style.background='#f0f4f8'" onmouseout="this.style.background='white'">
                            Batal
                        </button>
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
    @endif

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
