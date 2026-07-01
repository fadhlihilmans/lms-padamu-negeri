<div>

    {{-- ── Header ─────────────────────────────────────────────────────────────── --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-[18px] font-bold text-on-surface">Tugas</h2>
            <p class="text-[13px] text-[#757686] mt-0.5">Kelola tugas untuk peserta didik</p>
        </div>
        @if (! $showForm)
            <button wire:click="openCreateForm"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#3c50e0] text-white text-[14px] font-semibold rounded-xl hover:bg-[#2e3eb0] transition-colors shadow-sm cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Buat Tugas
            </button>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    {{-- Form Buat / Edit — inline card, muncul di atas daftar                    --}}
    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    @if ($showForm)
        <div class="bg-white rounded-2xl border border-[#c5c5d7] shadow-sm mb-6 overflow-hidden">

            {{-- Form header --}}
            <div class="flex items-center justify-between px-4 py-3.5 border-b border-[#c5c5d7] bg-white">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-[#EEF2FF] flex items-center justify-center">
                        <span class="material-symbols-outlined text-[#3c50e0] text-[20px]">assignment_add</span>
                    </div>
                    <div>
                        <h3 class="text-[16px] font-semibold text-on-surface">
                            {{ $editId ? 'Edit Tugas' : 'Buat Tugas Baru' }}
                        </h3>
                        <p class="text-[12px] text-[#505f76]">
                            {{ $editId ? 'Perbarui informasi tugas' : 'Isi detail tugas untuk peserta didik' }}
                        </p>
                    </div>
                </div>
                <button wire:click="cancelForm"
                        class="w-8 h-8 flex items-center justify-center rounded-lg text-[#505f76] hover:bg-[#f0f4f8] hover:text-[#ba1a1a] transition-colors cursor-pointer">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            {{-- Form body --}}
            <div class="p-6 space-y-5">

                {{-- ── Seksi 1: Penempatan ─────────────────────────────────────── --}}
                <div>
                    <p class="text-[11px] font-semibold text-[#757686] uppercase tracking-widest mb-3 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[14px]">school</span>Penempatan Tugas
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-1">
                            <label class="block text-[13px] font-medium text-on-surface mb-1.5">
                                Mata Pelajaran / Rombel <span class="text-[#ba1a1a]">*</span>
                            </label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#505f76] text-[18px] pointer-events-none">school</span>
                                <select wire:model="formGmrId"
                                        class="w-full pl-10 pr-8 py-2.5 border border-[#c5c5d7] rounded-lg text-[14px] appearance-none focus:outline-none focus:ring-2 focus:ring-[#3c50e0]/30 focus:border-[#3c50e0] cursor-pointer transition-colors @error('formGmrId') border-[#ba1a1a] bg-[#fff9f9] @enderror">
                                    <option value="">-- Pilih Mapel / Rombel --</option>
                                    @foreach ($gmrList as $gmr)
                                        <option value="{{ $gmr->id }}">{{ $gmr->mapel?->nama }} — {{ $gmr->rombel?->nama }}</option>
                                    @endforeach
                                </select>
                                <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-[#505f76] text-[18px] pointer-events-none">expand_more</span>
                            </div>
                            @error('formGmrId')
                                <p class="text-[12px] text-[#ba1a1a] mt-1 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">error</span> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-[13px] font-medium text-on-surface mb-1.5">
                                Batas Waktu Pengumpulan <span class="text-[#ba1a1a]">*</span>
                            </label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#505f76] text-[18px] pointer-events-none">calendar_clock</span>
                                <input wire:model="deadline" type="datetime-local"
                                       class="w-full pl-10 pr-4 py-2.5 border border-[#c5c5d7] rounded-lg text-[14px] focus:outline-none focus:ring-2 focus:ring-[#3c50e0]/30 focus:border-[#3c50e0] transition-colors cursor-pointer @error('deadline') border-[#ba1a1a] bg-[#fff9f9] @enderror">
                            </div>
                            @error('deadline')
                                <p class="text-[12px] text-[#ba1a1a] mt-1 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">error</span> {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="border-t border-dashed border-[#c5c5d7]"></div>

                {{-- ── Seksi 2: Judul & Deskripsi ─────────────────────────────── --}}
                <div>
                    <p class="text-[11px] font-semibold text-[#757686] uppercase tracking-widest mb-3 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[14px]">edit_note</span>Detail Tugas
                    </p>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-[13px] font-medium text-on-surface mb-1.5">
                                Judul Tugas <span class="text-[#ba1a1a]">*</span>
                            </label>
                            <input wire:model="judul" type="text"
                                   placeholder="Contoh: Latihan Soal Bab 3 — Persamaan Linear"
                                   class="w-full border border-[#c5c5d7] rounded-lg px-3.5 py-2.5 text-[14px] focus:outline-none focus:ring-2 focus:ring-[#3c50e0]/30 focus:border-[#3c50e0] transition-colors @error('judul') border-[#ba1a1a] bg-[#fff9f9] @enderror">
                            @error('judul')
                                <p class="text-[12px] text-[#ba1a1a] mt-1 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">error</span> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Deskripsi: Trix Editor --}}
                        <div>
                            <label class="block text-[13px] font-medium text-on-surface mb-1.5">
                                Deskripsi & Instruksi
                                <span class="font-normal text-[#757686]">(opsional)</span>
                            </label>
                            <div wire:ignore class="border border-[#c5c5d7] rounded-lg overflow-hidden trix-wrapper">
                                <input id="trix-tugas-{{ $editId ?? 'new' }}"
                                       type="hidden"
                                       value="{{ $deskripsi }}">
                                <trix-editor
                                    input="trix-tugas-{{ $editId ?? 'new' }}"
                                    placeholder="Jelaskan instruksi pengerjaan, halaman buku yang dirujuk, format pengumpulan, dsb."
                                    class="trix-content min-h-[140px]"
                                    x-on:trix-change="$wire.setDeskripsi($event.target.value)"
                                    x-on:trix-file-accept.prevent>
                                </trix-editor>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-dashed border-[#c5c5d7]"></div>

                {{-- ── Seksi 3: Lampiran ─────────────────────────────────────── --}}
                <div>
                    <p class="text-[11px] font-semibold text-[#757686] uppercase tracking-widest mb-3 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[14px]">attach_file</span>Lampiran Guru
                        <span class="font-normal normal-case tracking-normal text-[#757686] ml-1">(opsional)</span>
                    </p>

                    @if ($lampiranExisting && ! $hapusLampiran)
                        <div class="flex items-center gap-3 p-3 mb-3 bg-[#f6fafe] border border-[#c5d0ff] rounded-xl">
                            <div class="w-9 h-9 rounded-lg bg-[#EEF2FF] flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined text-[#3c50e0] text-[18px]">attach_file</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <a href="{{ Storage::url($lampiranExisting) }}" target="_blank"
                                   class="text-[13px] font-medium text-[#3c50e0] hover:underline truncate block">
                                    {{ basename($lampiranExisting) }}
                                </a>
                                <p class="text-[11px] text-[#505f76]">Lampiran saat ini</p>
                            </div>
                            <button wire:click="$set('hapusLampiran', true)" type="button"
                                    class="flex items-center gap-1 text-[12px] text-[#ba1a1a] hover:text-[#93000a] cursor-pointer px-2 py-1 rounded hover:bg-[#ffdad6] transition-colors">
                                <span class="material-symbols-outlined text-[14px]">delete</span>Hapus
                            </button>
                        </div>
                    @endif

                    <label class="flex flex-col items-center gap-3 p-6 border-2 border-dashed border-[#c5c5d7] rounded-xl cursor-pointer hover:border-[#3c50e0] hover:bg-[#f6fafe] transition-all group">
                        <div class="w-12 h-12 rounded-full bg-[#f0f4f8] group-hover:bg-[#EEF2FF] flex items-center justify-center transition-colors">
                            <span class="material-symbols-outlined text-[#505f76] group-hover:text-[#3c50e0] text-[26px] transition-colors">upload_file</span>
                        </div>
                        <div class="text-center">
                            @if ($lampiranBaru)
                                <p class="text-[14px] font-semibold text-[#3c50e0]">{{ $lampiranBaru->getClientOriginalName() }}</p>
                                <p class="text-[12px] text-[#505f76] mt-0.5">{{ round($lampiranBaru->getSize() / 1024, 1) }} KB · Klik untuk ganti</p>
                            @else
                                <p class="text-[14px] font-medium text-on-surface">Klik untuk pilih file lampiran</p>
                                <p class="text-[12px] text-[#505f76] mt-0.5">PDF, Word, Excel, gambar — maks 10 MB</p>
                            @endif
                        </div>
                        <input wire:model="lampiranBaru" type="file"
                               accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,image/*"
                               class="hidden">
                    </label>
                    @error('lampiranBaru')
                        <p class="text-[12px] text-[#ba1a1a] mt-2 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">error</span> {{ $message }}
                        </p>
                    @enderror
                </div>

            </div>{{-- /form body --}}

            {{-- Form footer --}}
            <div class="flex items-center justify-between px-4 py-3.5 border-t border-[#c5c5d7] bg-white">
                <p class="text-[12px] text-[#757686]"><span class="text-[#ba1a1a]">*</span> Wajib diisi</p>
                <div class="flex gap-3">
                    <button wire:click="cancelForm" type="button"
                            class="px-5 py-2.5 text-[14px] font-medium text-[#505f76] bg-white border border-[#c5c5d7] rounded-xl hover:bg-[#f0f4f8] transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button wire:click="save" wire:loading.attr="disabled"
                            class="px-6 py-2.5 text-[14px] font-semibold text-white bg-[#3c50e0] rounded-xl hover:bg-[#2e3eb0] transition-colors disabled:opacity-60 cursor-pointer flex items-center gap-2 shadow-sm">
                        <span wire:loading wire:target="save" class="material-symbols-outlined text-[16px] animate-spin">progress_activity</span>
                        {{ $editId ? 'Simpan Perubahan' : 'Buat Tugas' }}
                    </button>
                </div>
            </div>

        </div>
    @endif

    {{-- ── Filter Bar ──────────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm p-4 mb-6 flex flex-col sm:flex-row gap-3">
        <div class="flex-1 relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#505f76] text-[18px] pointer-events-none">search</span>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari judul tugas..."
                   class="w-full pl-10 pr-4 py-2.5 border border-[#c5c5d7] rounded-lg text-[14px] focus:outline-none focus:ring-1 focus:ring-[#3c50e0]">
        </div>
        @if ($gmrList->isNotEmpty())
            <div class="sm:w-56">
                <select wire:model.live="gmrId"
                        class="w-full border border-[#c5c5d7] rounded-lg px-4 py-2.5 text-[14px] focus:outline-none focus:ring-1 focus:ring-[#3c50e0] cursor-pointer">
                    <option value="">Semua Mapel</option>
                    @foreach ($gmrList as $gmr)
                        <option value="{{ $gmr->id }}">{{ $gmr->mapel?->nama }} — {{ $gmr->rombel?->nama }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        <div class="flex-shrink-0">
            <select wire:model.live="perPage"
                    class="border border-[#c5c5d7] rounded-lg px-4 py-2.5 text-[14px] focus:outline-none focus:ring-1 focus:ring-[#3c50e0] cursor-pointer">
                <option value="12">12 / hal</option>
                <option value="24">24 / hal</option>
                <option value="48">48 / hal</option>
            </select>
        </div>
    </div>

    {{-- ── Empty States ──────────────────────────────────────────────────────── --}}
    @if ($gmrList->isEmpty())
        <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm px-6 py-16 text-center">
            <span class="material-symbols-outlined text-[48px] text-[#c5c5d7] mb-3 block">school</span>
            <p class="text-[15px] font-medium text-on-surface mb-1">Belum ada pemetaan mapel</p>
            <p class="text-[13px] text-[#505f76]">Hubungi Admin untuk menambahkan pemetaan mengajar Anda.</p>
        </div>

    @elseif ($tugas->isEmpty())
        <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm px-6 py-16 text-center">
            <span class="material-symbols-outlined text-[48px] text-[#c5c5d7] mb-3 block">assignment</span>
            <p class="text-[15px] font-medium text-on-surface mb-1">
                {{ $search ? 'Tidak ada tugas yang cocok' : 'Belum ada tugas' }}
            </p>
            <p class="text-[13px] text-[#505f76]">
                {{ $search ? 'Coba ubah kata kunci pencarian.' : 'Klik "Buat Tugas" untuk mulai menambahkan.' }}
            </p>
        </div>

    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            @foreach ($tugas as $t)
                @php
                    $isOverdue    = $t->deadline->isPast();
                    $totalPD      = $pdCountByGmrId[$t->guru_mapel_rombel_id] ?? 0;
                    $submisiCount = $t->submisi_count;
                    $progress     = $totalPD > 0 ? round(($submisiCount / $totalPD) * 100) : 0;
                @endphp
                <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm flex flex-col relative overflow-hidden hover:shadow-md transition-shadow">
                    <div class="absolute top-0 left-0 w-1 h-full {{ $isOverdue ? 'bg-[#ba1a1a]' : 'bg-[#3c50e0]' }}"></div>

                    <div class="p-5 flex flex-col flex-1 pl-6">
                        <div class="flex items-start justify-between gap-3 mb-2">
                            <h3 class="text-[15px] font-semibold text-on-surface line-clamp-2 flex-1 leading-snug">{{ $t->judul }}</h3>
                            <span @class([
                                'flex-shrink-0 inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold',
                                'bg-[#ffdad6] text-[#ba1a1a]' => $isOverdue,
                                'bg-[#EEF2FF] text-[#3c50e0]' => ! $isOverdue,
                            ])>
                                <span class="material-symbols-outlined text-[12px]">{{ $isOverdue ? 'error' : 'schedule' }}</span>
                                {{ $isOverdue ? 'Lewat Batas' : 'Aktif' }}
                            </span>
                        </div>

                        <p class="text-[12px] text-[#505f76] mb-3">
                            {{ $t->guruMapelRombel?->mapel?->nama }}
                            <span class="text-[#c5c5d7] mx-1">·</span>
                            {{ $t->guruMapelRombel?->rombel?->nama }}
                        </p>

                        <div class="flex items-center gap-1.5 text-[12px] {{ $isOverdue ? 'text-[#ba1a1a]' : 'text-[#505f76]' }} mb-4">
                            <span class="material-symbols-outlined text-[14px]">calendar_today</span>
                            {{ $t->deadline->translatedFormat('d M Y, H:i') }}
                            <span class="opacity-60">({{ $t->deadline->diffForHumans() }})</span>
                        </div>

                        @if ($t->lampiran_path)
                            <div class="mb-3">
                                <a href="{{ Storage::url($t->lampiran_path) }}" target="_blank"
                                   class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-[#f6fafe] border border-[#c5d0ff] rounded-lg text-[12px] text-[#3c50e0] hover:bg-[#EEF2FF] hover:border-[#3c50e0] transition-colors max-w-full overflow-hidden cursor-pointer">
                                    <span class="material-symbols-outlined text-[14px] flex-shrink-0">attachment</span>
                                    <span class="truncate">{{ basename($t->lampiran_path) }}</span>
                                </a>
                            </div>
                        @endif

                        <div class="mt-auto pt-4 border-t border-[#f0f4f8]">
                            <div class="flex justify-between text-[12px] mb-1.5">
                                <span class="text-[#505f76]">Pengumpulan</span>
                                <span class="font-semibold text-on-surface">{{ $submisiCount }}/{{ $totalPD }}</span>
                            </div>
                            <div class="w-full bg-[#f0f4f8] rounded-full h-1.5 mb-4 overflow-hidden">
                                <div class="h-1.5 rounded-full transition-all duration-300 {{ $progress >= 100 ? 'bg-[#1c8c4e]' : 'bg-[#3c50e0]' }}"
                                     style="width: {{ $progress }}%"></div>
                            </div>

                            <div class="flex items-center gap-2">
                                <a href="{{ route('guru.tugas.submisi', $t->id) }}"
                                   class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 text-[13px] font-medium text-white bg-[#3c50e0] rounded-lg hover:bg-[#2e3eb0] transition-colors cursor-pointer">
                                    <span class="material-symbols-outlined text-[15px]">folder_open</span>
                                    Lihat Submisi
                                </a>
                                <button wire:click="openEditForm({{ $t->id }})"
                                        class="flex items-center justify-center px-3 py-2 text-[#505f76] border border-[#c5c5d7] rounded-lg hover:bg-[#f0f4f8] transition-colors cursor-pointer" title="Edit">
                                    <span class="material-symbols-outlined text-[16px]">edit</span>
                                </button>
                                <button wire:click="confirmDelete({{ $t->id }})"
                                        class="flex items-center justify-center px-3 py-2 text-[#ba1a1a] border border-[#ffdad6] rounded-lg hover:bg-[#ffdad6] transition-colors cursor-pointer" title="Hapus">
                                    <span class="material-symbols-outlined text-[16px]">delete</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if ($tugas->hasPages())
            <div class="mt-6">{{ $tugas->links() }}</div>
        @endif
    @endif

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

</div>

@push('scripts')
<link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
<script src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>
<style>
    /* Bersihkan default trix border agar pas dengan design */
    trix-editor { border: none !important; padding: 0.75rem 1rem !important; }
    trix-toolbar .trix-button-group { margin-bottom: 0; }
</style>
@endpush
