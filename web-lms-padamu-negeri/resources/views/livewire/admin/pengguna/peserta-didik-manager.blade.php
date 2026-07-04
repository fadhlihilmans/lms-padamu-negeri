<div>

    {{-- ── Header (3.4.1) ──────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
        <div>
            <h1 class="text-[18px] font-bold" style="color: #171c1f">Manajemen Akun — Peserta Didik</h1>
            <p class="text-[13px] mt-0.5" style="color: #757686">Kelola akun peserta didik dalam sistem.</p>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0 flex-wrap">
            <a href="{{ route('admin.import.peserta-didik') }}"
               class="flex items-center gap-2 px-4 py-2.5 rounded-lg text-[13.5px] font-semibold border cursor-pointer transition-colors"
               style="color: #505f76; border-color: #c5c5d7; background: white"
               onmouseover="this.style.background='#f0f4f8'" onmouseout="this.style.background='white'">
                <span class="material-symbols-outlined text-[17px]">upload_file</span>Import Excel
            </a>
            <button wire:click="openCreateForm"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-lg text-[13.5px] font-semibold text-white transition-colors cursor-pointer bg-[#3c50e0] hover:bg-[#2e3eb0] shadow-sm">
                <span class="material-symbols-outlined text-[17px]">person_add</span>Tambah Peserta Didik
            </button>
        </div>
    </div>

    {{-- ── Modal Form Tambah / Edit (3.1.2) ─────────────────────────────────── --}}
    @if ($showForm)
        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4"
             style="background: rgba(0,0,0,0.5); backdrop-filter: blur(3px); -webkit-backdrop-filter: blur(3px)"
             wire:keydown.escape="closeForm">
            <div class="relative z-10 w-full sm:max-w-2xl bg-white rounded-t-2xl sm:rounded-xl overflow-hidden shadow-xl flex flex-col max-h-[92dvh] sm:max-h-[88vh]"
                 style="box-shadow: 0 20px 60px -10px rgba(0,0,0,0.25)">

                <div class="flex items-center justify-between px-5 py-4 border-b flex-shrink-0" style="border-color: #c5c5d7">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: #EEF2FF">
                            <span class="material-symbols-outlined text-[18px]" style="color: #3c50e0">person</span>
                        </div>
                        <h3 class="text-[15px] font-semibold" style="color: #171c1f">{{ $editId ? 'Edit Data Peserta Didik' : 'Tambah Peserta Didik Baru' }}</h3>
                    </div>
                    <button type="button" wire:click="closeForm"
                            class="w-8 h-8 flex items-center justify-center rounded-lg cursor-pointer transition-colors" style="color: #757686"
                            onmouseover="this.style.background='#f0f4f8'" onmouseout="this.style.background='transparent'">
                        <span class="material-symbols-outlined text-[18px]">close</span>
                    </button>
                </div>

                <form wire:submit="save" class="flex flex-col flex-1 min-h-0">
                    <div class="px-5 py-5 space-y-4 overflow-y-auto flex-1">

                        {{-- NIPD + NISN --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="nipd" class="block text-[13px] font-medium mb-1.5" style="color: #171c1f">NIPD <span style="color: #ba1a1a">*</span></label>
                                <input wire:model="nipd" id="nipd" type="text" placeholder="Contoh: 1718"
                                       class="w-full px-3 py-2.5 rounded-lg text-[14px] bg-white outline-none transition-all"
                                       style="border: 1.5px solid {{ $errors->has('nipd') ? '#ba1a1a' : '#c5c5d7' }}; color: #171c1f"
                                       onfocus="this.style.borderColor='#3c50e0'; this.style.boxShadow='0 0 0 3px rgba(60,80,224,0.1)'"
                                       onblur="this.style.borderColor='{{ $errors->has('nipd') ? '#ba1a1a' : '#c5c5d7' }}'; this.style.boxShadow='none'">
                                @error('nipd')
                                    <p class="text-[12px] mt-1.5" style="color: #ba1a1a">{{ $message }}</p>
                                @else
                                    @if (! $editId)
                                        <p class="text-[12px] mt-1.5" style="color: #9da4b0">Digunakan sebagai username & password awal.</p>
                                    @else
                                        <p class="text-[12px] mt-1.5 text-amber-600">Mengubah NIPD akan otomatis memperbarui username akun login.</p>
                                    @endif
                                @enderror
                            </div>
                            <div>
                                <label for="nisn" class="block text-[13px] font-medium mb-1.5" style="color: #171c1f">
                                    NISN <span class="font-normal" style="color: #9da4b0">(opsional)</span>
                                </label>
                                <input wire:model="nisn" id="nisn" type="text" placeholder="10 digit NISN"
                                       class="w-full px-3 py-2.5 rounded-lg text-[14px] bg-white outline-none transition-all"
                                       style="border: 1.5px solid {{ $errors->has('nisn') ? '#ba1a1a' : '#c5c5d7' }}; color: #171c1f"
                                       onfocus="this.style.borderColor='#3c50e0'; this.style.boxShadow='0 0 0 3px rgba(60,80,224,0.1)'"
                                       onblur="this.style.borderColor='{{ $errors->has('nisn') ? '#ba1a1a' : '#c5c5d7' }}'; this.style.boxShadow='none'">
                                @error('nisn')
                                    <p class="text-[12px] mt-1.5" style="color: #ba1a1a">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Nama Lengkap --}}
                        <div>
                            <label for="namaLengkap" class="block text-[13px] font-medium mb-1.5" style="color: #171c1f">Nama Lengkap <span style="color: #ba1a1a">*</span></label>
                            <input wire:model="namaLengkap" id="namaLengkap" type="text" placeholder="Nama lengkap peserta didik"
                                   class="w-full px-3 py-2.5 rounded-lg text-[14px] bg-white outline-none transition-all"
                                   style="border: 1.5px solid {{ $errors->has('namaLengkap') ? '#ba1a1a' : '#c5c5d7' }}; color: #171c1f"
                                   onfocus="this.style.borderColor='#3c50e0'; this.style.boxShadow='0 0 0 3px rgba(60,80,224,0.1)'"
                                   onblur="this.style.borderColor='{{ $errors->has('namaLengkap') ? '#ba1a1a' : '#c5c5d7' }}'; this.style.boxShadow='none'">
                            @error('namaLengkap')
                                <p class="text-[12px] mt-1.5" style="color: #ba1a1a">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- NIK + Jenis Kelamin --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="nik" class="block text-[13px] font-medium mb-1.5" style="color: #171c1f">
                                    NIK <span class="font-normal" style="color: #9da4b0">(opsional)</span>
                                </label>
                                <input wire:model="nik" id="nik" type="text" placeholder="16 digit NIK"
                                       class="w-full px-3 py-2.5 rounded-lg text-[14px] bg-white outline-none transition-all"
                                       style="border: 1.5px solid #c5c5d7; color: #171c1f"
                                       onfocus="this.style.borderColor='#3c50e0'; this.style.boxShadow='0 0 0 3px rgba(60,80,224,0.1)'"
                                       onblur="this.style.borderColor='#c5c5d7'; this.style.boxShadow='none'">
                            </div>
                            <div>
                                <label for="jenisKelamin" class="block text-[13px] font-medium mb-1.5" style="color: #171c1f">Jenis Kelamin <span style="color: #ba1a1a">*</span></label>
                                <select wire:model="jenisKelamin" id="jenisKelamin"
                                        class="w-full px-3 py-2.5 rounded-lg text-[14px] bg-white outline-none cursor-pointer transition-all"
                                        style="border: 1.5px solid {{ $errors->has('jenisKelamin') ? '#ba1a1a' : '#c5c5d7' }}; color: #171c1f"
                                        onfocus="this.style.borderColor='#3c50e0'; this.style.boxShadow='0 0 0 3px rgba(60,80,224,0.1)'"
                                        onblur="this.style.borderColor='{{ $errors->has('jenisKelamin') ? '#ba1a1a' : '#c5c5d7' }}'; this.style.boxShadow='none'">
                                    <option value="">— Pilih —</option>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                                @error('jenisKelamin')
                                    <p class="text-[12px] mt-1.5" style="color: #ba1a1a">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Tempat + Tanggal Lahir --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="tempatLahir" class="block text-[13px] font-medium mb-1.5" style="color: #171c1f">
                                    Tempat Lahir <span class="font-normal" style="color: #9da4b0">(opsional)</span>
                                </label>
                                <input wire:model="tempatLahir" id="tempatLahir" type="text" placeholder="Kota kelahiran"
                                       class="w-full px-3 py-2.5 rounded-lg text-[14px] bg-white outline-none transition-all"
                                       style="border: 1.5px solid #c5c5d7; color: #171c1f"
                                       onfocus="this.style.borderColor='#3c50e0'; this.style.boxShadow='0 0 0 3px rgba(60,80,224,0.1)'"
                                       onblur="this.style.borderColor='#c5c5d7'; this.style.boxShadow='none'">
                            </div>
                            <div>
                                <label for="tanggalLahir" class="block text-[13px] font-medium mb-1.5" style="color: #171c1f">
                                    Tanggal Lahir <span class="font-normal" style="color: #9da4b0">(opsional)</span>
                                </label>
                                <input wire:model="tanggalLahir" id="tanggalLahir" type="date"
                                       class="w-full px-3 py-2.5 rounded-lg text-[14px] bg-white outline-none cursor-pointer transition-all"
                                       style="border: 1.5px solid #c5c5d7; color: #171c1f"
                                       onfocus="this.style.borderColor='#3c50e0'; this.style.boxShadow='0 0 0 3px rgba(60,80,224,0.1)'"
                                       onblur="this.style.borderColor='#c5c5d7'; this.style.boxShadow='none'">
                            </div>
                        </div>

                        {{-- Agama + No HP --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="agama" class="block text-[13px] font-medium mb-1.5" style="color: #171c1f">
                                    Agama <span class="font-normal" style="color: #9da4b0">(opsional)</span>
                                </label>
                                <select wire:model="agama" id="agama"
                                        class="w-full px-3 py-2.5 rounded-lg text-[14px] bg-white outline-none cursor-pointer transition-all"
                                        style="border: 1.5px solid #c5c5d7; color: #171c1f"
                                        onfocus="this.style.borderColor='#3c50e0'; this.style.boxShadow='0 0 0 3px rgba(60,80,224,0.1)'"
                                        onblur="this.style.borderColor='#c5c5d7'; this.style.boxShadow='none'">
                                    <option value="">— Pilih Agama —</option>
                                    <option value="Islam">Islam</option>
                                    <option value="Kristen">Kristen</option>
                                    <option value="Katolik">Katolik</option>
                                    <option value="Hindu">Hindu</option>
                                    <option value="Buddha">Buddha</option>
                                    <option value="Konghucu">Konghucu</option>
                                </select>
                            </div>
                            <div>
                                <label for="noHp" class="block text-[13px] font-medium mb-1.5" style="color: #171c1f">
                                    No. HP <span class="font-normal" style="color: #9da4b0">(opsional)</span>
                                </label>
                                <input wire:model="noHp" id="noHp" type="text" placeholder="Contoh: 08123456789"
                                       class="w-full px-3 py-2.5 rounded-lg text-[14px] bg-white outline-none transition-all"
                                       style="border: 1.5px solid #c5c5d7; color: #171c1f"
                                       onfocus="this.style.borderColor='#3c50e0'; this.style.boxShadow='0 0 0 3px rgba(60,80,224,0.1)'"
                                       onblur="this.style.borderColor='#c5c5d7'; this.style.boxShadow='none'">
                            </div>
                        </div>

                        {{-- Status Akademik (hanya edit) --}}
                        @if ($editId)
                            <div>
                                <label for="statusAkademik" class="block text-[13px] font-medium mb-1.5" style="color: #171c1f">Status Akademik</label>
                                <select wire:model="statusAkademik" id="statusAkademik"
                                        class="w-full px-3 py-2.5 rounded-lg text-[14px] bg-white outline-none cursor-pointer transition-all"
                                        style="border: 1.5px solid #c5c5d7; color: #171c1f"
                                        onfocus="this.style.borderColor='#3c50e0'; this.style.boxShadow='0 0 0 3px rgba(60,80,224,0.1)'"
                                        onblur="this.style.borderColor='#c5c5d7'; this.style.boxShadow='none'">
                                    <option value="aktif">Aktif</option>
                                    <option value="lulus">Lulus</option>
                                    <option value="pindah">Pindah</option>
                                    <option value="keluar">Keluar</option>
                                </select>
                            </div>
                        @endif
                    </div>

                    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 px-5 py-4 border-t flex-shrink-0" style="border-color: #d1d5db; background: white">
                        <button type="button" wire:click="closeForm"
                                class="px-4 py-2.5 rounded-lg text-[13px] font-medium border cursor-pointer transition-colors"
                                style="color: #505f76; border-color: #c5c5d7; background: white"
                                onmouseover="this.style.background='#f0f4f8'" onmouseout="this.style.background='white'">Batal</button>
                        <button type="submit"
                                class="px-5 py-2.5 rounded-lg text-[13.5px] font-semibold text-white cursor-pointer transition-colors flex items-center justify-center gap-2"
                                style="background: #3c50e0" onmouseover="this.style.background='#2e3eb0'" onmouseout="this.style.background='#3c50e0'"
                                wire:loading.attr="disabled" wire:loading.class="opacity-70 cursor-not-allowed">
                            <span wire:loading wire:target="save" class="material-symbols-outlined text-[16px] animate-spin">progress_activity</span>
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ── Modal Konfirmasi Reset Password (3.4.2) ──────────────────────────── --}}
    @if ($confirmResetId)
        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4"
             style="background: rgba(0,0,0,0.5); backdrop-filter: blur(3px); -webkit-backdrop-filter: blur(3px)">
            <div class="w-full sm:max-w-sm bg-white rounded-t-2xl sm:rounded-xl overflow-hidden shadow-xl flex flex-col"
                 style="box-shadow: 0 20px 60px -10px rgba(0,0,0,0.25)">
                <div class="flex items-center justify-between px-5 py-4 border-b flex-shrink-0" style="border-color: #c5c5d7">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: #fffbeb">
                            <span class="material-symbols-outlined text-[18px]" style="color: #d97706">lock_reset</span>
                        </div>
                        <h3 class="text-[15px] font-semibold" style="color: #171c1f">Reset Password</h3>
                    </div>
                </div>
                <div class="px-5 py-5">
                    <div class="flex items-start gap-2.5 p-3.5 rounded-lg border" style="background: #fffbeb; border-color: #fde68a">
                        <span class="material-symbols-outlined text-[17px] flex-shrink-0 mt-0.5" style="color: #d97706">warning</span>
                        <p class="text-[13px] leading-relaxed" style="color: #92400e">Password akan direset ke NIPD peserta didik. Mereka akan diminta ganti password saat login berikutnya.</p>
                    </div>
                </div>
                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 px-5 py-4 border-t flex-shrink-0" style="border-color: #d1d5db; background: white">
                    <button wire:click="$set('confirmResetId', null)"
                            class="px-4 py-2.5 rounded-lg text-[13px] font-medium border cursor-pointer transition-colors text-center"
                            style="color: #505f76; border-color: #c5c5d7; background: white"
                            onmouseover="this.style.background='#f0f4f8'" onmouseout="this.style.background='white'">Batal</button>
                    <button wire:click="resetPassword"
                            class="px-5 py-2.5 rounded-lg text-[13.5px] font-semibold text-white cursor-pointer transition-colors text-center"
                            style="background: #d97706" onmouseover="this.style.background='#b45309'" onmouseout="this.style.background='#d97706'">Ya, Reset</button>
                </div>
            </div>
        </div>
    @endif

    {{-- ── Modal Konfirmasi Hapus ───────────────────────────────────────────── --}}
    @if ($confirmDeleteId)
        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4"
             style="background: rgba(0,0,0,0.5); backdrop-filter: blur(3px); -webkit-backdrop-filter: blur(3px)">
            <div class="w-full sm:max-w-sm bg-white rounded-t-2xl sm:rounded-xl overflow-hidden shadow-xl flex flex-col"
                 style="box-shadow: 0 20px 60px -10px rgba(0,0,0,0.25)">
                <div class="px-5 py-5 flex items-start gap-3">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0" style="background: #ffdad6">
                        <span class="material-symbols-outlined text-[18px]" style="color: #ba1a1a">delete</span>
                    </div>
                    <div>
                        <h4 class="text-[15px] font-semibold" style="color: #171c1f">Hapus Peserta Didik ini?</h4>
                        <p class="text-[13px] mt-0.5" style="color: #505f76">Akun login juga akan dihapus. Tindakan ini tidak dapat dibatalkan.</p>
                    </div>
                </div>
                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 px-5 py-4 border-t flex-shrink-0" style="border-color: #d1d5db; background: white">
                    <button wire:click="$set('confirmDeleteId', null)"
                            class="px-4 py-2.5 rounded-lg text-[13px] font-medium border cursor-pointer transition-colors text-center"
                            style="color: #505f76; border-color: #c5c5d7; background: white"
                            onmouseover="this.style.background='#f0f4f8'" onmouseout="this.style.background='white'">Batal</button>
                    <button wire:click="delete"
                            class="px-5 py-2.5 rounded-lg text-[13.5px] font-semibold text-white cursor-pointer transition-colors text-center"
                            style="background: #ba1a1a" onmouseover="this.style.background='#93000a'" onmouseout="this.style.background='#ba1a1a'">Ya, Hapus</button>
                </div>
            </div>
        </div>
    @endif

    {{-- ── Table Card (3.4.1) ────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border overflow-hidden" style="border-color: #c5c5d7; box-shadow: 0 1px 3px rgba(0,0,0,0.05)">

        {{-- Toolbar standar + filter Status Akademik --}}
        <div class="flex flex-col sm:flex-row sm:flex-wrap sm:items-center gap-2.5 px-4 py-3 border-b bg-white" style="border-color: #d1d5db">
            <div class="relative w-full sm:flex-1 sm:min-w-[180px]">
                <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-[17px] pointer-events-none" style="color: #9da4b0">search</span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama, NIPD, atau NISN..."
                       class="w-full pl-9 pr-3 py-2 rounded-lg text-[13.5px] bg-white outline-none transition-all"
                       style="border: 1.5px solid #c5c5d7; color: #171c1f"
                       onfocus="this.style.borderColor='#3c50e0'; this.style.boxShadow='0 0 0 3px rgba(60,80,224,0.1)'"
                       onblur="this.style.borderColor='#c5c5d7'; this.style.boxShadow='none'">
            </div>
            <select wire:model.live="filterStatusAkademik" class="w-full sm:w-auto py-2 pl-2.5 rounded-lg text-[13px] cursor-pointer outline-none sm:flex-shrink-0"
                    style="border: 1.5px solid #c5c5d7; color: #505f76; background: white">
                <option value="">Semua Status</option>
                <option value="aktif">Aktif</option>
                <option value="lulus">Lulus</option>
                <option value="pindah">Pindah</option>
                <option value="keluar">Keluar</option>
            </select>
            <div class="flex items-center gap-1.5 w-full sm:w-auto justify-between sm:justify-start sm:flex-shrink-0">
                <span class="text-[12.5px] whitespace-nowrap" style="color: #757686">Tampil</span>
                <select wire:model.live="perPage" class="py-2 pl-2.5 rounded-lg text-[13px] cursor-pointer outline-none"
                        style="border: 1.5px solid #c5c5d7; color: #505f76; background: white">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
                <span class="text-[12.5px] whitespace-nowrap" style="color: #757686">Data</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left min-w-[720px]">
                <thead>
                    <tr class="border-b" style="border-color: #c5c5d7">
                        <th class="px-4 py-3 text-[11.5px] font-semibold uppercase tracking-wide w-10" style="color: #757686">No</th>
                        <th class="px-4 py-3 text-[11.5px] font-semibold uppercase tracking-wide" style="color: #757686">Peserta Didik</th>
                        <th class="px-4 py-3 text-[11.5px] font-semibold uppercase tracking-wide" style="color: #757686">NIPD / NISN</th>
                        <th class="px-4 py-3 text-[11.5px] font-semibold uppercase tracking-wide text-center" style="color: #757686">Status</th>
                        <th class="px-4 py-3 text-[11.5px] font-semibold uppercase tracking-wide text-center" style="color: #757686">Akun</th>
                        <th class="px-4 py-3 text-[11.5px] font-semibold uppercase tracking-wide text-center w-32" style="color: #757686">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#c5c5d7]">
                    @forelse ($pesertaDidiks as $i => $pd)
                        @php
                            $statusColors = [
                                'aktif'  => 'background:#f0fdf4;color:#16a34a',
                                'lulus'  => 'background:#EEF2FF;color:#3c50e0',
                                'pindah' => 'background:#fffbeb;color:#d97706',
                                'keluar' => 'background:#ffdad6;color:#ba1a1a',
                            ];
                            $initials = collect(explode(' ', $pd->nama_lengkap))->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode('');
                            $avatarColors = ['#3c50e0', '#16a34a', '#9333ea', '#d97706', '#0891b2'];
                            $avatarColor = $avatarColors[$pd->id % count($avatarColors)];
                        @endphp
                        <tr class="transition-colors" onmouseover="this.style.background='#f6fafe'" onmouseout="this.style.background='transparent'">
                            <td class="px-4 py-3.5 text-[13px]" style="color: #9da4b0">{{ $pesertaDidiks->firstItem() + $i }}</td>
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 text-[12px] font-bold text-white" style="background: {{ $avatarColor }}">{{ strtoupper($initials) }}</div>
                                    <span class="text-[13.5px] font-medium" style="color: #171c1f">{{ $pd->nama_lengkap }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 font-mono text-[12px]" style="color: #505f76">
                                {{ $pd->nipd }}
                                @if ($pd->nisn)
                                    <br><span style="color: #9da4b0">NISN: {{ $pd->nisn }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold whitespace-nowrap" style="{{ $statusColors[$pd->status_akademik] ?? 'background:#eaeef2;color:#505f76' }}">
                                    {{ ucfirst($pd->status_akademik) }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <button wire:click="toggleActive({{ $pd->id }})"
                                        title="{{ $pd->user?->is_active ? 'Klik untuk nonaktifkan' : 'Klik untuk aktifkan' }}"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[12px] font-semibold transition-colors cursor-pointer whitespace-nowrap"
                                        style="{{ $pd->user?->is_active ? 'background:#f0fdf4;color:#16a34a' : 'background:#ffdad6;color:#ba1a1a' }}">
                                    <span class="material-symbols-outlined text-[14px]">{{ $pd->user?->is_active ? 'check_circle' : 'cancel' }}</span>
                                    {{ $pd->user?->is_active ? 'Aktif' : 'Nonaktif' }}
                                </button>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('admin.pengguna.peserta-didik.show', $pd->id) }}" title="Lihat detail"
                                       class="w-8 h-8 inline-flex items-center justify-center rounded-lg cursor-pointer transition-colors" style="color: #505f76"
                                       onmouseover="this.style.background='#EEF2FF'; this.style.color='#3c50e0'" onmouseout="this.style.background='transparent'; this.style.color='#505f76'">
                                        <span class="material-symbols-outlined text-[17px]">visibility</span>
                                    </a>
                                    <button wire:click="confirmReset({{ $pd->id }})" title="Reset Password"
                                            class="w-8 h-8 inline-flex items-center justify-center rounded-lg cursor-pointer transition-colors" style="color: #505f76"
                                            onmouseover="this.style.background='#fffbeb'; this.style.color='#d97706'" onmouseout="this.style.background='transparent'; this.style.color='#505f76'">
                                        <span class="material-symbols-outlined text-[17px]">lock_reset</span>
                                    </button>
                                    <button wire:click="openEditForm({{ $pd->id }})" title="Edit"
                                            class="w-8 h-8 inline-flex items-center justify-center rounded-lg cursor-pointer transition-colors" style="color: #505f76"
                                            onmouseover="this.style.background='#EEF2FF'; this.style.color='#3c50e0'" onmouseout="this.style.background='transparent'; this.style.color='#505f76'">
                                        <span class="material-symbols-outlined text-[17px]">edit</span>
                                    </button>
                                    <button wire:click="confirmDelete({{ $pd->id }})" title="Hapus"
                                            class="w-8 h-8 inline-flex items-center justify-center rounded-lg cursor-pointer transition-colors" style="color: #505f76"
                                            onmouseover="this.style.background='#ffdad6'; this.style.color='#ba1a1a'" onmouseout="this.style.background='transparent'; this.style.color='#505f76'">
                                        <span class="material-symbols-outlined text-[17px]">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-16 text-center">
                                @if ($search || $filterStatusAkademik)
                                    <span class="material-symbols-outlined text-[40px] mb-2 block" style="color: #c5c5d7">search_off</span>
                                    <p class="text-[13px]" style="color: #757686">Tidak ada peserta didik yang cocok dengan filter.</p>
                                    <button wire:click="$set('search', ''); $set('filterStatusAkademik', '')" class="mt-2 text-[12.5px] cursor-pointer hover:underline" style="color: #3c50e0">Hapus Filter</button>
                                @else
                                    <span class="material-symbols-outlined text-[40px] mb-2 block" style="color: #c5c5d7">person</span>
                                    <p class="text-[13px]" style="color: #757686">Belum ada data peserta didik.</p>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination standar --}}
        @if ($pesertaDidiks->total() > 0)
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-4 py-3.5 border-t" style="border-color: #c5c5d7">
                <p class="text-[12.5px]" style="color: #757686">
                    Menampilkan {{ $pesertaDidiks->firstItem() }}–{{ $pesertaDidiks->lastItem() }} dari {{ $pesertaDidiks->total() }} data
                </p>
                @if ($pesertaDidiks->lastPage() > 1)
                    <div class="flex items-center gap-1">
                        <button wire:click="previousPage" @disabled($pesertaDidiks->onFirstPage())
                                class="w-8 h-8 inline-flex items-center justify-center rounded-lg transition-colors {{ $pesertaDidiks->onFirstPage() ? 'cursor-not-allowed' : 'cursor-pointer hover:bg-[#f0f4f8]' }}"
                                style="color: {{ $pesertaDidiks->onFirstPage() ? '#c5c5d7' : '#505f76' }}">
                            <span class="material-symbols-outlined text-[16px]">chevron_left</span>
                        </button>
                        @foreach (range(1, $pesertaDidiks->lastPage()) as $page)
                            <button wire:click="gotoPage({{ $page }})"
                                    class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-[13px] transition-colors {{ $page == $pesertaDidiks->currentPage() ? 'font-semibold text-white' : 'cursor-pointer hover:bg-[#f0f4f8]' }}"
                                    style="{{ $page == $pesertaDidiks->currentPage() ? 'background:#3c50e0' : 'color:#505f76' }}">
                                {{ $page }}
                            </button>
                        @endforeach
                        <button wire:click="nextPage" @disabled(! $pesertaDidiks->hasMorePages())
                                class="w-8 h-8 inline-flex items-center justify-center rounded-lg transition-colors {{ ! $pesertaDidiks->hasMorePages() ? 'cursor-not-allowed' : 'cursor-pointer hover:bg-[#f0f4f8]' }}"
                                style="color: {{ ! $pesertaDidiks->hasMorePages() ? '#c5c5d7' : '#505f76' }}">
                            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                        </button>
                    </div>
                @endif
            </div>
        @endif

    </div>

</div>
