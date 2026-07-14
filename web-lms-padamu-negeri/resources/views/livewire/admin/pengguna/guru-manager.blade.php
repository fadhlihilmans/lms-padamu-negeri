<div>

    {{-- ── Header (3.4.1) ──────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
        <div>
            <h1 class="text-[18px] font-bold" style="color: #171c1f">Manajemen Akun — Guru</h1>
            <p class="text-[13px] mt-0.5" style="color: #757686">Kelola akun guru dalam sistem.</p>
        </div>
        <button wire:click="openCreateForm"
                class="flex items-center gap-2 px-4 py-2.5 rounded-lg text-[13.5px] font-semibold text-white transition-colors cursor-pointer self-start sm:self-auto bg-[#3c50e0] hover:bg-[#2e3eb0] shadow-sm">
            <span class="material-symbols-outlined text-[17px]">person_add</span>Tambah Guru
        </button>
    </div>

    {{-- ── Modal Form Tambah / Edit (3.1.2) ─────────────────────────────────── --}}
    @if ($showForm)
        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4"
             style="background: rgba(0,0,0,0.5); backdrop-filter: blur(3px); -webkit-backdrop-filter: blur(3px)"
             wire:keydown.escape="closeForm">
            <div class="relative z-10 w-full sm:max-w-md bg-white rounded-t-2xl sm:rounded-xl overflow-hidden shadow-xl flex flex-col max-h-[85dvh] sm:max-h-[80vh]"
                 style="box-shadow: 0 20px 60px -10px rgba(0,0,0,0.25)">

                <div class="flex items-center justify-between px-5 py-4 border-b flex-shrink-0" style="border-color: #c5c5d7">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: #EEF2FF">
                            <span class="material-symbols-outlined text-[18px]" style="color: #3c50e0">badge</span>
                        </div>
                        <h3 class="text-[15px] font-semibold" style="color: #171c1f">{{ $editId ? 'Edit Data Guru' : 'Tambah Guru Baru' }}</h3>
                    </div>
                    <button type="button" wire:click="closeForm"
                            class="w-8 h-8 flex items-center justify-center rounded-lg cursor-pointer transition-colors" style="color: #757686"
                            onmouseover="this.style.background='#f0f4f8'" onmouseout="this.style.background='transparent'">
                        <span class="material-symbols-outlined text-[18px]">close</span>
                    </button>
                </div>

                <form wire:submit="save" class="flex flex-col flex-1 min-h-0">
                    <div class="px-5 py-5 space-y-4 overflow-y-auto flex-1">

                        {{-- NIP --}}
                        <div>
                            <label for="nip" class="block text-[13px] font-medium mb-1.5" style="color: #171c1f">NIP <span style="color: #ba1a1a">*</span></label>
                            <input wire:model="nip" id="nip" type="text" placeholder="Masukkan NIP guru"
                                   class="w-full px-3 py-2.5 rounded-lg text-[14px] bg-white outline-none transition-all"
                                   style="border: 1.5px solid {{ $errors->has('nip') ? '#ba1a1a' : '#c5c5d7' }}; color: #171c1f"
                                   onfocus="this.style.borderColor='#3c50e0'; this.style.boxShadow='0 0 0 3px rgba(60,80,224,0.1)'"
                                   onblur="this.style.borderColor='{{ $errors->has('nip') ? '#ba1a1a' : '#c5c5d7' }}'; this.style.boxShadow='none'">
                            @error('nip')
                                <p class="text-[12px] mt-1.5" style="color: #ba1a1a">{{ $message }}</p>
                            @else
                                @if (! $editId)
                                    <p class="text-[12px] mt-1.5" style="color: #9da4b0">NIP akan digunakan sebagai username dan password awal akun.</p>
                                @else
                                    <p class="text-[12px] mt-1.5 text-amber-600">Mengubah NIP akan otomatis memperbarui username akun login.</p>
                                @endif
                            @enderror
                        </div>

                        {{-- Nama Lengkap --}}
                        <div>
                            <label for="namaLengkap" class="block text-[13px] font-medium mb-1.5" style="color: #171c1f">Nama Lengkap <span style="color: #ba1a1a">*</span></label>
                            <input wire:model="namaLengkap" id="namaLengkap" type="text" placeholder="Nama lengkap beserta gelar"
                                   class="w-full px-3 py-2.5 rounded-lg text-[14px] bg-white outline-none transition-all"
                                   style="border: 1.5px solid {{ $errors->has('namaLengkap') ? '#ba1a1a' : '#c5c5d7' }}; color: #171c1f"
                                   onfocus="this.style.borderColor='#3c50e0'; this.style.boxShadow='0 0 0 3px rgba(60,80,224,0.1)'"
                                   onblur="this.style.borderColor='{{ $errors->has('namaLengkap') ? '#ba1a1a' : '#c5c5d7' }}'; this.style.boxShadow='none'">
                            @error('namaLengkap')
                                <p class="text-[12px] mt-1.5" style="color: #ba1a1a">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- No HP --}}
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
                        <p class="text-[13px] leading-relaxed" style="color: #92400e">Password akan direset ke NIP guru. Guru akan diminta ganti password saat login berikutnya.</p>
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

    {{-- ── Modal Peringatan Nonaktifkan Guru ────────────────────────────────────
         Akun nonaktif TIDAK BISA LOGIN. Bila guru masih wali kelas / masih
         mengampu mapel di TA aktif, tugasnya (rapor, kenaikan kelas, materi,
         tugas, CBT) akan macet. Karena itu Admin diperingatkan lebih dulu. --}}
    @if ($confirmToggleId && $toggleGuru && $toggleInfo)
        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4"
             style="background: rgba(0,0,0,0.5); backdrop-filter: blur(3px); -webkit-backdrop-filter: blur(3px)">
            <div class="w-full sm:max-w-md bg-white rounded-t-2xl sm:rounded-xl overflow-hidden shadow-xl flex flex-col">

                <div class="px-5 py-5 flex items-start gap-3">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0" style="background: #fffbeb">
                        <span class="material-symbols-outlined text-[18px]" style="color: #d97706">warning</span>
                    </div>
                    <div class="min-w-0">
                        <h4 class="text-[15px] font-semibold" style="color: #171c1f">Nonaktifkan {{ $toggleGuru->nama_lengkap }}?</h4>
                        <p class="text-[13px] mt-1" style="color: #505f76">
                            Guru ini <strong>masih punya tugas berjalan</strong> di Tahun Ajaran aktif.
                            Akun yang dinonaktifkan <strong>tidak bisa login</strong>.
                        </p>

                        <ul class="mt-3 flex flex-col gap-1.5">
                            @if (count($toggleInfo['rombelWali']))
                                <li class="flex items-start gap-1.5 text-[13px]" style="color: #171c1f">
                                    <span class="material-symbols-outlined text-[15px] flex-shrink-0 mt-0.5" style="color: #ba1a1a">groups</span>
                                    <span>Wali Kelas di <strong>{{ implode(', ', $toggleInfo['rombelWali']) }}</strong></span>
                                </li>
                            @endif
                            @if ($toggleInfo['jumlahMapel'] > 0)
                                <li class="flex items-start gap-1.5 text-[13px]" style="color: #171c1f">
                                    <span class="material-symbols-outlined text-[15px] flex-shrink-0 mt-0.5" style="color: #ba1a1a">book</span>
                                    <span>Mengampu <strong>{{ $toggleInfo['jumlahMapel'] }} mapel</strong> pada TA aktif</span>
                                </li>
                            @endif
                        </ul>

                        <p class="text-[12.5px] mt-3 p-2.5 rounded-lg" style="background: #fffbeb; color: #92400e">
                            Sebaiknya <strong>pindahkan dulu</strong> wali kelas &amp; pemetaan mapelnya ke guru lain,
                            agar rapor, kenaikan kelas, materi, tugas, dan CBT tidak macet.
                        </p>
                    </div>
                </div>

                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 px-5 py-4 border-t flex-shrink-0" style="border-color: #d1d5db; background: white">
                    <button wire:click="cancelToggle"
                            class="px-4 py-2.5 rounded-lg text-[13px] font-medium border cursor-pointer transition-colors text-center"
                            style="color: #505f76; border-color: #c5c5d7; background: white"
                            onmouseover="this.style.background='#f0f4f8'" onmouseout="this.style.background='white'">
                        Batal
                    </button>
                    <button wire:click="confirmNonaktif"
                            class="px-5 py-2.5 rounded-lg text-[13.5px] font-semibold text-white cursor-pointer transition-colors text-center"
                            style="background: #d97706" onmouseover="this.style.background='#b45309'" onmouseout="this.style.background='#d97706'">
                        Tetap Nonaktifkan
                    </button>
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
                        <h4 class="text-[15px] font-semibold" style="color: #171c1f">Hapus Guru ini?</h4>
                        <p class="text-[13px] mt-0.5" style="color: #505f76">Akun login guru juga akan dihapus. Tindakan ini tidak dapat dibatalkan.</p>
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

        {{-- Toolbar standar --}}
        <div class="flex flex-col sm:flex-row sm:flex-wrap sm:items-center gap-2.5 px-4 py-3 border-b bg-white" style="border-color: #d1d5db">
            <div class="relative w-full sm:flex-1 sm:min-w-[180px]">
                <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-[17px] pointer-events-none" style="color: #9da4b0">search</span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama atau NIP guru..."
                       class="w-full pl-9 pr-3 py-2 rounded-lg text-[13.5px] bg-white outline-none transition-all"
                       style="border: 1.5px solid #c5c5d7; color: #171c1f"
                       onfocus="this.style.borderColor='#3c50e0'; this.style.boxShadow='0 0 0 3px rgba(60,80,224,0.1)'"
                       onblur="this.style.borderColor='#c5c5d7'; this.style.boxShadow='none'">
            </div>
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
            <table class="w-full text-left min-w-[640px]">
                <thead>
                    <tr class="border-b" style="border-color: #c5c5d7">
                        <th class="px-4 py-3 text-[11.5px] font-semibold uppercase tracking-wide w-10" style="color: #757686">No</th>
                        <th class="px-4 py-3 text-[11.5px] font-semibold uppercase tracking-wide" style="color: #757686">Nama</th>
                        <th class="px-4 py-3 text-[11.5px] font-semibold uppercase tracking-wide" style="color: #757686">Username</th>
                        <th class="px-4 py-3 text-[11.5px] font-semibold uppercase tracking-wide" style="color: #757686">No. HP</th>
                        <th class="px-4 py-3 text-[11.5px] font-semibold uppercase tracking-wide text-center" style="color: #757686">Status</th>
                        <th class="px-4 py-3 text-[11.5px] font-semibold uppercase tracking-wide text-center w-28" style="color: #757686">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#c5c5d7]">
                    @forelse ($gurus as $i => $guru)
                        @php
                            $initials = collect(explode(' ', $guru->nama_lengkap))->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode('');
                            $avatarColors = ['#3c50e0', '#16a34a', '#9333ea', '#d97706', '#0891b2'];
                            $avatarColor = $avatarColors[$guru->id % count($avatarColors)];
                        @endphp
                        <tr class="transition-colors" onmouseover="this.style.background='#f6fafe'" onmouseout="this.style.background='transparent'">
                            <td class="px-4 py-3.5 text-[13px]" style="color: #9da4b0">{{ $gurus->firstItem() + $i }}</td>
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 text-[12px] font-bold text-white" style="background: {{ $avatarColor }}">{{ strtoupper($initials) }}</div>
                                    <span class="text-[13.5px] font-medium" style="color: #171c1f">{{ $guru->nama_lengkap }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 font-mono text-[12px]" style="color: #505f76">{{ $guru->nip }}</td>
                            <td class="px-4 py-3.5 text-[13px]" style="color: #505f76">{{ $guru->no_hp ?? '—' }}</td>
                            <td class="px-4 py-3.5 text-center">
                                <button wire:click="toggleActive({{ $guru->id }})"
                                        title="{{ $guru->user?->is_active ? 'Klik untuk nonaktifkan' : 'Klik untuk aktifkan' }}"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[12px] font-semibold transition-colors cursor-pointer whitespace-nowrap"
                                        style="{{ $guru->user?->is_active ? 'background:#f0fdf4;color:#16a34a' : 'background:#ffdad6;color:#ba1a1a' }}">
                                    <span class="material-symbols-outlined text-[14px]">{{ $guru->user?->is_active ? 'check_circle' : 'cancel' }}</span>
                                    {{ $guru->user?->is_active ? 'Aktif' : 'Nonaktif' }}
                                </button>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <div class="inline-flex items-center gap-1">
                                    <button wire:click="confirmReset({{ $guru->id }})" title="Reset Password"
                                            class="w-8 h-8 inline-flex items-center justify-center rounded-lg cursor-pointer transition-colors" style="color: #505f76"
                                            onmouseover="this.style.background='#fffbeb'; this.style.color='#d97706'" onmouseout="this.style.background='transparent'; this.style.color='#505f76'">
                                        <span class="material-symbols-outlined text-[17px]">lock_reset</span>
                                    </button>
                                    <button wire:click="openEditForm({{ $guru->id }})" title="Edit"
                                            class="w-8 h-8 inline-flex items-center justify-center rounded-lg cursor-pointer transition-colors" style="color: #505f76"
                                            onmouseover="this.style.background='#EEF2FF'; this.style.color='#3c50e0'" onmouseout="this.style.background='transparent'; this.style.color='#505f76'">
                                        <span class="material-symbols-outlined text-[17px]">edit</span>
                                    </button>
                                    <button wire:click="confirmDelete({{ $guru->id }})" title="Hapus"
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
                                @if ($search)
                                    <span class="material-symbols-outlined text-[40px] mb-2 block" style="color: #c5c5d7">search_off</span>
                                    <p class="text-[13px]" style="color: #757686">Tidak ada guru yang cocok dengan pencarian.</p>
                                    <button wire:click="$set('search', '')" class="mt-2 text-[12.5px] cursor-pointer hover:underline" style="color: #3c50e0">Hapus Pencarian</button>
                                @else
                                    <span class="material-symbols-outlined text-[40px] mb-2 block" style="color: #c5c5d7">badge</span>
                                    <p class="text-[13px]" style="color: #757686">Belum ada data guru.</p>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination standar --}}
        @if ($gurus->total() > 0)
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-4 py-3.5 border-t" style="border-color: #c5c5d7">
                <p class="text-[12.5px]" style="color: #757686">
                    Menampilkan {{ $gurus->firstItem() }}–{{ $gurus->lastItem() }} dari {{ $gurus->total() }} data
                </p>
                @if ($gurus->lastPage() > 1)
                    <div class="flex items-center gap-1">
                        <button wire:click="previousPage" @disabled($gurus->onFirstPage())
                                class="w-8 h-8 inline-flex items-center justify-center rounded-lg transition-colors {{ $gurus->onFirstPage() ? 'cursor-not-allowed' : 'cursor-pointer hover:bg-[#f0f4f8]' }}"
                                style="color: {{ $gurus->onFirstPage() ? '#c5c5d7' : '#505f76' }}">
                            <span class="material-symbols-outlined text-[16px]">chevron_left</span>
                        </button>
                        @foreach (range(1, $gurus->lastPage()) as $page)
                            <button wire:click="gotoPage({{ $page }})"
                                    class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-[13px] transition-colors {{ $page == $gurus->currentPage() ? 'font-semibold text-white' : 'cursor-pointer hover:bg-[#f0f4f8]' }}"
                                    style="{{ $page == $gurus->currentPage() ? 'background:#3c50e0' : 'color:#505f76' }}">
                                {{ $page }}
                            </button>
                        @endforeach
                        <button wire:click="nextPage" @disabled(! $gurus->hasMorePages())
                                class="w-8 h-8 inline-flex items-center justify-center rounded-lg transition-colors {{ ! $gurus->hasMorePages() ? 'cursor-not-allowed' : 'cursor-pointer hover:bg-[#f0f4f8]' }}"
                                style="color: {{ ! $gurus->hasMorePages() ? '#c5c5d7' : '#505f76' }}">
                            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                        </button>
                    </div>
                @endif
            </div>
        @endif

    </div>

</div>
