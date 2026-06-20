<div>

    {{-- ── Header ─────────────────────────────────────────────────────────────── --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-[24px] font-bold tracking-tight text-on-surface">Tugas</h2>
            <p class="text-[14px] text-[#505f76] mt-0.5">Kelola tugas untuk peserta didik</p>
        </div>
        @if (! $showForm)
            <button wire:click="openCreateForm"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#3c50e0] text-white text-[14px] font-semibold rounded-xl hover:bg-[#2e3eb0] transition-colors shadow-sm cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Buat Tugas
            </button>
        @endif
    </div>

    {{-- ── Form Buat / Edit ──────────────────────────────────────────────────── --}}
    @if ($showForm)
        <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-[17px] font-semibold text-on-surface">
                    {{ $editId ? 'Edit Tugas' : 'Buat Tugas Baru' }}
                </h3>
                <button wire:click="cancelForm" class="text-[#505f76] hover:text-[#ba1a1a] transition-colors cursor-pointer">
                    <span class="material-symbols-outlined text-[22px]">close</span>
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- Mapel - Rombel --}}
                <div>
                    <label class="block text-[13px] font-medium text-on-surface mb-1.5">
                        Mapel / Rombel <span class="text-[#ba1a1a]">*</span>
                    </label>
                    <select wire:model="formGmrId"
                            class="w-full border border-[#c5c5d7] rounded-lg px-3.5 py-2.5 text-[14px] focus:outline-none focus:ring-1 focus:ring-[#3c50e0] cursor-pointer @error('formGmrId') border-[#ba1a1a] @enderror">
                        <option value="">-- Pilih Mapel / Rombel --</option>
                        @foreach ($gmrList as $gmr)
                            <option value="{{ $gmr->id }}">
                                {{ $gmr->mapel?->nama }} — {{ $gmr->rombel?->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('formGmrId') <p class="text-[12px] text-[#ba1a1a] mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Deadline --}}
                <div>
                    <label class="block text-[13px] font-medium text-on-surface mb-1.5">
                        Deadline <span class="text-[#ba1a1a]">*</span>
                    </label>
                    <input wire:model="deadline" type="datetime-local"
                           class="w-full border border-[#c5c5d7] rounded-lg px-3.5 py-2.5 text-[14px] focus:outline-none focus:ring-1 focus:ring-[#3c50e0] @error('deadline') border-[#ba1a1a] @enderror">
                    @error('deadline') <p class="text-[12px] text-[#ba1a1a] mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Judul --}}
                <div class="md:col-span-2">
                    <label class="block text-[13px] font-medium text-on-surface mb-1.5">
                        Judul Tugas <span class="text-[#ba1a1a]">*</span>
                    </label>
                    <input wire:model="judul" type="text" placeholder="Contoh: Latihan Soal Bab 3"
                           class="w-full border border-[#c5c5d7] rounded-lg px-3.5 py-2.5 text-[14px] focus:outline-none focus:ring-1 focus:ring-[#3c50e0] @error('judul') border-[#ba1a1a] @enderror">
                    @error('judul') <p class="text-[12px] text-[#ba1a1a] mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Deskripsi --}}
                <div class="md:col-span-2">
                    <label class="block text-[13px] font-medium text-on-surface mb-1.5">Deskripsi</label>
                    <textarea wire:model="deskripsi" rows="4"
                              placeholder="Instruksi pengerjaan tugas, halaman buku yang dirujuk, dsb."
                              class="w-full border border-[#c5c5d7] rounded-lg px-3.5 py-2.5 text-[14px] focus:outline-none focus:ring-1 focus:ring-[#3c50e0] resize-y @error('deskripsi') border-[#ba1a1a] @enderror"></textarea>
                    @error('deskripsi') <p class="text-[12px] text-[#ba1a1a] mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Lampiran (opsional) --}}
                <div class="md:col-span-2">
                    <label class="block text-[13px] font-medium text-on-surface mb-1.5">
                        Lampiran <span class="text-[12px] text-[#505f76] font-normal">(opsional — PDF, Word, Excel, gambar)</span>
                    </label>

                    {{-- Lampiran existing saat edit --}}
                    @if ($lampiranExisting && ! $hapusLampiran)
                        <div class="flex items-center gap-3 p-3 bg-[#f6fafe] border border-[#c5c5d7] rounded-lg mb-2">
                            <span class="material-symbols-outlined text-[#3c50e0] text-[20px]">attach_file</span>
                            <a href="{{ Storage::url($lampiranExisting) }}" target="_blank"
                               class="text-[13px] text-[#3c50e0] hover:underline flex-1 truncate">
                                {{ basename($lampiranExisting) }}
                            </a>
                            <button wire:click="$set('hapusLampiran', true)" type="button"
                                    class="text-[#ba1a1a] hover:text-[#93000a] cursor-pointer text-[12px] flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px]">delete</span>Hapus
                            </button>
                        </div>
                    @endif

                    <input wire:model="lampiranBaru" type="file"
                           accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,image/*"
                           class="block w-full text-[14px] text-[#505f76] file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-[13px] file:font-medium file:bg-[#EEF2FF] file:text-[#3c50e0] hover:file:bg-[#d9dbff] file:cursor-pointer">
                    @error('lampiranBaru') <p class="text-[12px] text-[#ba1a1a] mt-1">{{ $message }}</p> @enderror

                    @if ($lampiranBaru)
                        <div class="mt-2 flex items-center gap-2 text-[13px] text-[#505f76]">
                            <span class="material-symbols-outlined text-[16px] text-[#3c50e0]">attach_file</span>
                            <span>{{ $lampiranBaru->getClientOriginalName() }}</span>
                            <span class="text-[12px]">({{ round($lampiranBaru->getSize() / 1024, 1) }} KB)</span>
                        </div>
                    @endif
                </div>

            </div>

            {{-- Action buttons --}}
            <div class="flex justify-end gap-3 mt-6 pt-5 border-t border-[#c5c5d7]">
                <button wire:click="cancelForm" type="button"
                        class="px-5 py-2.5 text-[14px] font-medium text-[#505f76] bg-white border border-[#c5c5d7] rounded-xl hover:bg-[#f0f4f8] transition-colors cursor-pointer">
                    Batal
                </button>
                <button wire:click="save" wire:loading.attr="disabled"
                        class="px-5 py-2.5 text-[14px] font-semibold text-white bg-[#3c50e0] rounded-xl hover:bg-[#2e3eb0] transition-colors disabled:opacity-60 cursor-pointer flex items-center gap-2">
                    <span wire:loading wire:target="save" class="material-symbols-outlined text-[16px] animate-spin">progress_activity</span>
                    {{ $editId ? 'Simpan Perubahan' : 'Buat Tugas' }}
                </button>
            </div>
        </div>
    @endif

    {{-- ── Filter Bar ──────────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm p-4 mb-6 flex flex-col sm:flex-row gap-3">
        {{-- Search --}}
        <div class="flex-1 relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#505f76] text-[18px] pointer-events-none">search</span>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari judul tugas..."
                   class="w-full pl-10 pr-4 py-2.5 border border-[#c5c5d7] rounded-lg text-[14px] focus:outline-none focus:ring-1 focus:ring-[#3c50e0]">
        </div>
        {{-- Filter Mapel-Rombel --}}
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
        {{-- Per page --}}
        <div class="flex-shrink-0">
            <select wire:model.live="perPage"
                    class="border border-[#c5c5d7] rounded-lg px-4 py-2.5 text-[14px] focus:outline-none focus:ring-1 focus:ring-[#3c50e0] cursor-pointer">
                <option value="12">12 / hal</option>
                <option value="24">24 / hal</option>
                <option value="48">48 / hal</option>
            </select>
        </div>
    </div>

    {{-- ── Empty State: tidak ada GMR --}}
    @if ($gmrList->isEmpty())
        <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm px-6 py-16 text-center">
            <span class="material-symbols-outlined text-[48px] text-[#c5c5d7] mb-3 block">school</span>
            <p class="text-[15px] font-medium text-on-surface mb-1">Belum ada pemetaan mapel</p>
            <p class="text-[13px] text-[#505f76]">Hubungi Admin untuk menambahkan pemetaan mengajar Anda.</p>
        </div>

    {{-- ── Empty State: belum ada tugas --}}
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
        {{-- ── Grid Kartu Tugas ──────────────────────────────────────────────── --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            @foreach ($tugas as $t)
                @php
                    $isOverdue  = $t->deadline->isPast();
                    $totalPD    = $pdCountByGmrId[$t->guru_mapel_rombel_id] ?? 0;
                    $submisiCount = $t->submisi_count;
                    $progress   = $totalPD > 0 ? round(($submisiCount / $totalPD) * 100) : 0;
                    $deadlineIn = $t->deadline->diffForHumans();
                @endphp
                <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm flex flex-col relative overflow-hidden group hover:shadow-md transition-shadow">
                    {{-- Left border indicator --}}
                    <div class="absolute top-0 left-0 w-1 h-full {{ $isOverdue ? 'bg-[#ba1a1a]' : 'bg-[#3c50e0]' }}"></div>

                    <div class="p-5 flex flex-col flex-1 pl-6">
                        {{-- Header --}}
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <h3 class="text-[15px] font-semibold text-on-surface line-clamp-2 flex-1">{{ $t->judul }}</h3>
                            <span @class([
                                'flex-shrink-0 inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[12px] font-medium',
                                'bg-[#ffdad6] text-[#ba1a1a]' => $isOverdue,
                                'bg-[#EEF2FF] text-[#3c50e0]' => ! $isOverdue,
                            ])>
                                <span class="material-symbols-outlined text-[13px]">{{ $isOverdue ? 'error' : 'schedule' }}</span>
                                {{ $isOverdue ? 'Lewat Batas' : 'Aktif' }}
                            </span>
                        </div>

                        {{-- Mapel + Rombel --}}
                        <p class="text-[12px] text-[#505f76] mb-2">
                            {{ $t->guruMapelRombel?->mapel?->nama }} &nbsp;·&nbsp;
                            {{ $t->guruMapelRombel?->rombel?->nama }}
                        </p>

                        {{-- Deadline --}}
                        <div class="flex items-center gap-1.5 text-[13px] {{ $isOverdue ? 'text-[#ba1a1a]' : 'text-[#505f76]' }} mb-4">
                            <span class="material-symbols-outlined text-[16px]">calendar_today</span>
                            <span>Deadline: {{ $t->deadline->translatedFormat('d M Y, H:i') }}</span>
                            <span class="text-[11px] opacity-70">({{ $deadlineIn }})</span>
                        </div>

                        {{-- Lampiran badge --}}
                        @if ($t->lampiran_path)
                            <div class="mb-3">
                                <a href="{{ Storage::url($t->lampiran_path) }}" target="_blank"
                                   class="inline-flex items-center gap-1 text-[12px] text-[#3c50e0] hover:underline">
                                    <span class="material-symbols-outlined text-[14px]">attach_file</span>
                                    {{ basename($t->lampiran_path) }}
                                </a>
                            </div>
                        @endif

                        {{-- Progress submisi --}}
                        <div class="mt-auto pt-4 border-t border-[#f0f4f8]">
                            <div class="flex justify-between text-[12px] text-[#505f76] mb-1.5">
                                <span>Progress Pengumpulan</span>
                                <span class="font-semibold text-on-surface">{{ $submisiCount }}/{{ $totalPD }}</span>
                            </div>
                            <div class="w-full bg-[#f0f4f8] rounded-full h-2 mb-4 overflow-hidden">
                                <div class="h-2 rounded-full transition-all duration-300
                                    {{ $progress >= 100 ? 'bg-[#1c8c4e]' : 'bg-[#3c50e0]' }}"
                                     style="width: {{ $progress }}%"></div>
                            </div>

                            {{-- Action buttons --}}
                            <div class="flex items-center gap-2">
                                <button wire:click="openEditForm({{ $t->id }})"
                                        class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 text-[13px] font-medium text-[#505f76] border border-[#c5c5d7] rounded-lg hover:bg-[#f0f4f8] transition-colors cursor-pointer">
                                    <span class="material-symbols-outlined text-[15px]">edit</span>
                                    Edit
                                </button>
                                <button wire:click="confirmDelete({{ $t->id }})"
                                        class="flex items-center justify-center gap-1.5 px-3 py-2 text-[13px] font-medium text-[#ba1a1a] border border-[#ffdad6] rounded-lg hover:bg-[#ffdad6] transition-colors cursor-pointer">
                                    <span class="material-symbols-outlined text-[15px]">delete</span>
                                </button>
                                <a href="#"
                                   title="Daftar Submisi (segera hadir)"
                                   class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 text-[13px] font-medium text-[#505f76] border border-[#c5c5d7] rounded-lg opacity-50 cursor-not-allowed">
                                    <span class="material-symbols-outlined text-[15px]">folder_open</span>
                                    Submisi
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if ($tugas->hasPages())
            <div class="mt-6">{{ $tugas->links() }}</div>
        @endif
    @endif

    {{-- ── Modal Konfirmasi Hapus ──────────────────────────────────────────── --}}
    @if ($confirmDeleteId)
        <div class="fixed inset-0 z-[100] flex items-center justify-center px-4"
             style="background: rgba(0,0,0,0.5);">
            <div class="bg-white rounded-2xl shadow-xl max-w-sm w-full p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 rounded-full bg-[#ffdad6] flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-[#ba1a1a] text-[24px]">delete_forever</span>
                    </div>
                    <div>
                        <h4 class="text-[16px] font-semibold text-on-surface">Hapus Tugas?</h4>
                        <p class="text-[13px] text-[#505f76] mt-0.5">Semua submisi peserta didik untuk tugas ini juga akan dihapus.</p>
                    </div>
                </div>
                <div class="flex gap-3 justify-end">
                    <button wire:click="cancelDelete"
                            class="px-5 py-2 text-[14px] font-medium text-[#505f76] border border-[#c5c5d7] rounded-xl hover:bg-[#f0f4f8] transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button wire:click="delete"
                            class="px-5 py-2 text-[14px] font-semibold text-white bg-[#ba1a1a] rounded-xl hover:bg-[#93000a] transition-colors cursor-pointer">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
