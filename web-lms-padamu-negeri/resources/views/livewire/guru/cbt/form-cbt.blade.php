<div>

    {{-- ── Header / Breadcrumb ─────────────────────────────────────────────── --}}
    <div class="mb-6">
        <a href="{{ route('guru.cbt') }}" wire:navigate
           class="inline-flex items-center gap-1.5 text-[13px] text-[#505f76] hover:text-[#3c50e0] transition-colors mb-3 cursor-pointer">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            Kembali ke daftar CBT
        </a>
        <h2 class="text-[24px] font-bold tracking-tight text-on-surface">
            {{ $editId ? 'Edit CBT' : 'Buat CBT Baru' }}
        </h2>
        <p class="text-[14px] text-[#505f76] mt-0.5">
            {{ $editId ? 'Perbarui pengaturan ujian.' : 'Atur jadwal & pengaturan ujian. Soal ditambahkan pada langkah berikutnya.' }}
        </p>
    </div>

    {{-- ── Empty state: belum ada pemetaan ─────────────────────────────────── --}}
    @if ($gmrList->isEmpty())
        <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm px-6 py-16 text-center">
            <span class="material-symbols-outlined text-[48px] text-[#c5c5d7] mb-3 block">school</span>
            <p class="text-[15px] font-medium text-on-surface mb-1">Belum ada pemetaan mapel</p>
            <p class="text-[13px] text-[#505f76]">Hubungi Admin untuk menambahkan pemetaan mengajar Anda.</p>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-[#c5c5d7] shadow-sm overflow-hidden max-w-3xl">

            {{-- Card header --}}
            <div class="flex items-center gap-3 px-6 py-4 border-b border-[#c5c5d7] bg-[#f6fafe]">
                <div class="w-9 h-9 rounded-xl bg-[#EEF2FF] flex items-center justify-center">
                    <span class="material-symbols-outlined text-[#3c50e0] text-[20px]">quiz</span>
                </div>
                <div>
                    <h3 class="text-[16px] font-semibold text-on-surface">Pengaturan Ujian</h3>
                    <p class="text-[12px] text-[#505f76]">Lengkapi informasi dasar CBT</p>
                </div>
            </div>

            {{-- Form body --}}
            <form wire:submit="save" class="p-6 space-y-5">

                {{-- Penempatan --}}
                <div>
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

                {{-- Nama ujian --}}
                <div>
                    <label class="block text-[13px] font-medium text-on-surface mb-1.5">
                        Nama Ujian <span class="text-[#ba1a1a]">*</span>
                    </label>
                    <input wire:model="namaUjian" type="text"
                           placeholder="Contoh: Penilaian Tengah Semester — Matematika"
                           class="w-full border border-[#c5c5d7] rounded-lg px-3.5 py-2.5 text-[14px] focus:outline-none focus:ring-2 focus:ring-[#3c50e0]/30 focus:border-[#3c50e0] transition-colors @error('namaUjian') border-[#ba1a1a] bg-[#fff9f9] @enderror">
                    @error('namaUjian')
                        <p class="text-[12px] text-[#ba1a1a] mt-1 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">error</span> {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- KKM + Durasi --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[13px] font-medium text-on-surface mb-1.5">
                            KKM <span class="text-[#ba1a1a]">*</span>
                        </label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#505f76] text-[18px] pointer-events-none">flag</span>
                            <input wire:model="kkm" type="number" min="0" max="100" placeholder="75"
                                   class="w-full pl-10 pr-4 py-2.5 border border-[#c5c5d7] rounded-lg text-[14px] focus:outline-none focus:ring-2 focus:ring-[#3c50e0]/30 focus:border-[#3c50e0] transition-colors @error('kkm') border-[#ba1a1a] bg-[#fff9f9] @enderror">
                        </div>
                        @error('kkm')
                            <p class="text-[12px] text-[#ba1a1a] mt-1 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">error</span> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-[13px] font-medium text-on-surface mb-1.5">
                            Durasi (menit) <span class="text-[#ba1a1a]">*</span>
                        </label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#505f76] text-[18px] pointer-events-none">timer</span>
                            <input wire:model="durasiMenit" type="number" min="1" max="1440" placeholder="90"
                                   class="w-full pl-10 pr-4 py-2.5 border border-[#c5c5d7] rounded-lg text-[14px] focus:outline-none focus:ring-2 focus:ring-[#3c50e0]/30 focus:border-[#3c50e0] transition-colors @error('durasiMenit') border-[#ba1a1a] bg-[#fff9f9] @enderror">
                        </div>
                        @error('durasiMenit')
                            <p class="text-[12px] text-[#ba1a1a] mt-1 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">error</span> {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                {{-- Tanggal mulai --}}
                <div>
                    <label class="block text-[13px] font-medium text-on-surface mb-1.5">
                        Tanggal & Waktu Mulai <span class="text-[#ba1a1a]">*</span>
                    </label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#505f76] text-[18px] pointer-events-none">calendar_clock</span>
                        <input wire:model="tanggalMulai" type="datetime-local"
                               class="w-full pl-10 pr-4 py-2.5 border border-[#c5c5d7] rounded-lg text-[14px] focus:outline-none focus:ring-2 focus:ring-[#3c50e0]/30 focus:border-[#3c50e0] transition-colors cursor-pointer @error('tanggalMulai') border-[#ba1a1a] bg-[#fff9f9] @enderror">
                    </div>
                    @error('tanggalMulai')
                        <p class="text-[12px] text-[#ba1a1a] mt-1 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">error</span> {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="border-t border-dashed border-[#c5c5d7]"></div>

                {{-- Tampilkan nilai otomatis --}}
                <div class="flex items-start justify-between gap-4 p-4 rounded-xl bg-[#f6fafe] border border-[#c5d0ff]">
                    <div class="flex-1">
                        <p class="text-[14px] font-medium text-on-surface">Tampilkan Nilai Otomatis</p>
                        <p class="text-[12px] text-[#505f76] mt-0.5">
                            Jika aktif, peserta didik langsung melihat nilai bagian Pilihan Ganda setelah submit.
                            Hanya berlaku untuk CBT yang mengandung soal Pilihan Ganda.
                        </p>
                    </div>
                    <button type="button" wire:click="$toggle('tampilkanNilaiOtomatis')"
                            class="relative inline-flex h-6 w-11 flex-shrink-0 items-center rounded-full transition-colors cursor-pointer {{ $tampilkanNilaiOtomatis ? 'bg-[#3c50e0]' : 'bg-[#c5c5d7]' }}"
                            role="switch" aria-checked="{{ $tampilkanNilaiOtomatis ? 'true' : 'false' }}">
                        <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $tampilkanNilaiOtomatis ? 'translate-x-6' : 'translate-x-1' }}"></span>
                    </button>
                </div>

                {{-- Footer actions --}}
                <div class="flex items-center justify-between pt-2">
                    <p class="text-[12px] text-[#757686]"><span class="text-[#ba1a1a]">*</span> Wajib diisi</p>
                    <div class="flex gap-3">
                        <a href="{{ route('guru.cbt') }}" wire:navigate
                           class="px-5 py-2.5 text-[14px] font-medium text-[#505f76] bg-white border border-[#c5c5d7] rounded-xl hover:bg-[#f0f4f8] transition-colors cursor-pointer">
                            Batal
                        </a>
                        <button type="submit" wire:loading.attr="disabled"
                                class="px-6 py-2.5 text-[14px] font-semibold text-white bg-[#3c50e0] rounded-xl hover:bg-[#2e3eb0] transition-colors disabled:opacity-60 cursor-pointer flex items-center gap-2 shadow-sm">
                            <span wire:loading wire:target="save" class="material-symbols-outlined text-[16px] animate-spin">progress_activity</span>
                            {{ $editId ? 'Simpan Perubahan' : 'Buat & Lanjut ke Soal' }}
                        </button>
                    </div>
                </div>

            </form>
        </div>
    @endif

</div>
