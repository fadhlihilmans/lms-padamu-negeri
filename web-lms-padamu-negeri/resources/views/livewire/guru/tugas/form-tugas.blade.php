<div class="max-w-2xl mx-auto">

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <div class="flex items-center gap-3 mb-5">
        <a href="{{ $gmrId ? route('guru.tugas', ['gmr' => $gmrId]) : route('guru.tugas') }}"
           class="w-9 h-9 flex items-center justify-center rounded-lg border cursor-pointer transition-colors flex-shrink-0"
           style="border-color: #c5c5d7; background: white; color: #505f76"
           onmouseover="this.style.background='#f0f4f8'" onmouseout="this.style.background='white'">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        </a>
        <div>
            <h1 class="text-[18px] font-bold" style="color: #171c1f">{{ $editId ? 'Edit Tugas' : 'Buat Tugas Baru' }}</h1>
            <p class="text-[13px] mt-0.5" style="color: #757686">{{ $editId ? 'Perbarui informasi tugas.' : 'Isi detail tugas untuk peserta didik.' }}</p>
        </div>
    </div>

    {{-- ── Konteks Mapel & Rombel ──────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm p-4 sm:p-5 mb-6">
        <label class="block text-[13px] font-medium mb-1.5" style="color: #171c1f">Mata Pelajaran & Rombel <span style="color: #ba1a1a">*</span></label>
        <select wire:model="gmrId"
                class="w-full border rounded-lg px-3 py-2.5 text-[14px] bg-white outline-none focus:border-[#3c50e0] focus:ring-1 focus:ring-[#3c50e0]"
                style="border-color: {{ $errors->has('gmrId') ? '#ba1a1a' : '#c5c5d7' }}">
            <option value="">— Pilih Mapel & Rombel —</option>
            @foreach ($gmrList as $g)
                <option value="{{ $g->id }}">{{ $g->mapel?->nama }} — {{ $g->rombel?->nama }}</option>
            @endforeach
        </select>
        @error('gmrId') <p class="text-[12px] mt-1.5" style="color: #ba1a1a">{{ $message }}</p> @enderror
    </div>

    {{-- ── Form ─────────────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm p-4 sm:p-6">
        <form wire:submit="save" class="space-y-5">

            {{-- Judul --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-[13px] font-medium" style="color: #171c1f">
                    Judul Tugas <span style="color: #ba1a1a">*</span>
                </label>
                <input wire:model="judul" type="text" placeholder="Contoh: Latihan Soal Bab 3 — Persamaan Linear"
                       class="w-full border rounded-lg px-3 py-2 text-[14px] focus:outline-none focus:ring-1 transition-shadow"
                       style="border-color: {{ $errors->has('judul') ? '#ba1a1a' : '#c5c5d7' }}">
                @error('judul') <p class="text-[12px]" style="color: #ba1a1a">{{ $message }}</p> @enderror
            </div>

            {{-- Deadline --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-[13px] font-medium" style="color: #171c1f">
                    Batas Waktu Pengumpulan <span style="color: #ba1a1a">*</span>
                </label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[17px] pointer-events-none" style="color: #505f76">calendar_clock</span>
                    <input wire:model="deadline" type="datetime-local"
                           class="w-full pl-10 pr-3 py-2 border rounded-lg text-[14px] focus:outline-none focus:ring-1 transition-shadow cursor-pointer"
                           style="border-color: {{ $errors->has('deadline') ? '#ba1a1a' : '#c5c5d7' }}">
                </div>
                @error('deadline') <p class="text-[12px]" style="color: #ba1a1a">{{ $message }}</p> @enderror
            </div>

            {{-- Deskripsi: Trix Editor --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-[13px] font-medium" style="color: #171c1f">Deskripsi & Instruksi <span class="text-[12px] font-normal" style="color: #505f76">(opsional)</span></label>
                <div wire:ignore class="border rounded-lg overflow-hidden trix-wrapper" style="border-color: #c5c5d7">
                    <input id="trix-tugas-{{ $editId ?? 'new' }}" type="hidden" value="{{ $deskripsi }}">
                    <trix-editor
                        input="trix-tugas-{{ $editId ?? 'new' }}"
                        placeholder="Jelaskan instruksi pengerjaan, halaman buku yang dirujuk, format pengumpulan, dsb."
                        class="trix-content min-h-[140px]"
                        x-on:trix-change="$wire.setDeskripsi($event.target.value)"
                        x-on:trix-file-accept.prevent>
                    </trix-editor>
                </div>
            </div>

            {{-- Lampiran existing (edit mode) --}}
            @if ($editId && $lampiranExisting && ! $hapusLampiran)
                <div class="flex items-center gap-3 p-3 rounded-lg border" style="background: #f6fafe; border-color: #c5d0ff">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0" style="background: #EEF2FF">
                        <span class="material-symbols-outlined text-[18px]" style="color: #3c50e0">attach_file</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <a href="{{ Storage::url($lampiranExisting) }}" target="_blank"
                           class="text-[13px] font-medium truncate block" style="color: #3c50e0">
                            {{ basename($lampiranExisting) }}
                        </a>
                        <p class="text-[11px]" style="color: #505f76">Lampiran saat ini</p>
                    </div>
                    <button wire:click="$set('hapusLampiran', true)" type="button"
                            class="flex items-center gap-1 text-[12px] cursor-pointer px-2 py-1 rounded transition-colors" style="color: #ba1a1a">
                        <span class="material-symbols-outlined text-[14px]">delete</span>Hapus
                    </button>
                </div>
            @endif

            {{-- Upload Lampiran --}}
            <div class="flex flex-col gap-2">
                <label class="text-[13px] font-medium" style="color: #171c1f">
                    Lampiran <span class="text-[12px] font-normal" style="color: #505f76">(opsional, maks {{ $maxMb }}MB)</span>
                </label>
                <label class="flex flex-col items-center gap-3 p-6 border-2 border-dashed rounded-xl cursor-pointer transition-all"
                       style="border-color: #c5c5d7; background: #f6fafe"
                       onmouseover="this.style.borderColor='#3c50e0'; this.style.background='#EEF2FF'"
                       onmouseout="this.style.borderColor='#c5c5d7'; this.style.background='#f6fafe'">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background: #f0f4f8">
                        <span class="material-symbols-outlined text-[26px]" style="color: #505f76">upload_file</span>
                    </div>
                    <div class="text-center" wire:loading.remove wire:target="lampiranBaru">
                        @if ($lampiranBaru)
                            <p class="text-[14px] font-semibold" style="color: #3c50e0">{{ $lampiranBaru->getClientOriginalName() }}</p>
                            <p class="text-[12px] mt-0.5" style="color: #505f76">{{ round($lampiranBaru->getSize() / 1024, 1) }} KB · Klik untuk ganti</p>
                        @else
                            <p class="text-[14px] font-medium" style="color: #171c1f">Klik untuk pilih file lampiran</p>
                            <p class="text-[12px] mt-0.5" style="color: #505f76">PDF, Word, Excel, gambar — maks {{ $maxMb }}MB</p>
                        @endif
                    </div>
                    {{-- Indikator sedang mengunggah --}}
                    <div class="text-center flex items-center gap-2" wire:loading wire:target="lampiranBaru">
                        <span class="material-symbols-outlined text-[18px] animate-spin" style="color:#3c50e0">progress_activity</span>
                        <span class="text-[14px] font-semibold" style="color:#3c50e0">Mengunggah file…</span>
                    </div>
                    <input wire:model="lampiranBaru" type="file" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,image/*" class="hidden">
                </label>
                @error('lampiranBaru') <p class="text-[12px]" style="color: #ba1a1a">{{ $message }}</p> @enderror
            </div>

            {{-- Footer --}}
            <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-4 border-t" style="border-color: #c5c5d7">
                <a href="{{ $gmrId ? route('guru.tugas', ['gmr' => $gmrId]) : route('guru.tugas') }}"
                   class="px-5 py-2.5 rounded-lg border text-[14px] font-medium transition-colors cursor-pointer text-center"
                   style="border-color: #c5c5d7; color: #505f76; background: white">
                    Batal
                </a>
                <button type="submit"
                        class="px-5 py-2.5 rounded-lg text-white text-[14px] font-medium transition-colors flex items-center justify-center gap-2 cursor-pointer"
                        style="background: #3c50e0"
                        wire:loading.attr="disabled" wire:loading.class="opacity-70 cursor-not-allowed">
                    <span wire:loading wire:target="save" class="material-symbols-outlined text-[16px] animate-spin">progress_activity</span>
                    {{ $editId ? 'Simpan Perubahan' : 'Buat Tugas' }}
                </button>
            </div>
        </form>
    </div>

</div>

@push('scripts')
<link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
<script src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>
<style>
    trix-editor { border: none !important; padding: 0.75rem 1rem !important; min-height: 140px; }
    trix-toolbar .trix-button-group { margin-bottom: 0; border: 1px solid #c5c5d7; }
    .trix-wrapper trix-toolbar { background: #f0f4f8; border-bottom: 1px solid #c5c5d7; padding: 0.5rem; }
</style>
@endpush
