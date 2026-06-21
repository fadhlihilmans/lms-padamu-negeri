<div>

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-[24px] font-bold tracking-tight text-on-surface">Manajemen Peserta Didik</h2>
            <p class="text-[14px] text-[#505f76] mt-0.5">Kelola data peserta didik dan akun login mereka.</p>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0 flex-wrap">
            <a href="{{ route('admin.import.peserta-didik') }}"
               class="inline-flex items-center gap-2 bg-white border border-[#c5c5d7] text-[#505f76] text-[14px] font-medium px-4 py-2.5 rounded-lg hover:bg-[#f0f4f8] transition-colors shadow-sm cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">upload_file</span>
                Import Excel
            </a>
            <button wire:click="openCreateForm"
                    class="inline-flex items-center gap-2 bg-[#3c50e0] text-white text-[14px] font-medium px-4 py-2.5 rounded-lg hover:bg-[#1c33c8] transition-colors shadow-sm cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Tambah Peserta Didik
            </button>
        </div>
    </div>

    {{-- ── Modal Form Tambah / Edit ─────────────────────────────────────────── --}}
    @if ($showForm)
        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/50 backdrop-blur-sm p-0 sm:p-4">
            <div class="relative bg-white rounded-t-2xl sm:rounded-xl shadow-xl shadow-black/10 w-full max-w-4xl sm:max-w-2xl border border-[#c5c5d7]">
                <button wire:click="closeForm" type="button"
                        class="absolute top-4 right-4 text-[#505f76] hover:text-[#ba1a1a] transition-colors p-1 cursor-pointer z-10 bg-white rounded-full">
                    <span class="material-symbols-outlined">close</span>
                </button>
                <div class="p-6 sm:p-8 max-h-[90vh] overflow-y-auto">
                <div class="mb-6 border-b border-[#c5c5d7] pb-4 pr-8">
                    <h3 class="text-[20px] font-semibold text-on-surface">
                        {{ $editId ? 'Edit Data Peserta Didik' : 'Tambah Peserta Didik Baru' }}
                    </h3>
                </div>
                <form wire:submit="save" class="flex flex-col gap-4">

                    {{-- NIPD + NISN --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[14px] font-medium text-on-surface" for="nipd">
                                NIPD <span class="text-[#ba1a1a]">*</span>
                            </label>
                            <input wire:model="nipd" id="nipd" type="text" placeholder="Contoh: 1718"
                                   class="w-full border rounded-lg px-4 py-2.5 text-[14px] text-on-surface focus:outline-none focus:ring-1 transition-shadow
                                          {{ $errors->has('nipd') ? 'border-[#ba1a1a] focus:ring-[#ba1a1a]' : 'border-[#c5c5d7] focus:ring-[#3c50e0] focus:border-[#3c50e0]' }}">
                            @error('nipd')
                                <p class="text-[12px] text-[#ba1a1a]">{{ $message }}</p>
                            @enderror
                            @if (! $editId)
                                <p class="text-[12px] text-[#757686]">Digunakan sebagai username & password awal.</p>
                            @else
                                <p class="text-[12px] text-amber-600">Mengubah NIPD akan otomatis memperbarui username akun login.</p>
                            @endif
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[14px] font-medium text-on-surface" for="nisn">
                                NISN <span class="text-[12px] font-normal text-[#757686]">(opsional)</span>
                            </label>
                            <input wire:model="nisn" id="nisn" type="text" placeholder="10 digit NISN"
                                   class="w-full border rounded-lg px-4 py-2.5 text-[14px] text-on-surface focus:outline-none focus:ring-1 transition-shadow
                                          {{ $errors->has('nisn') ? 'border-[#ba1a1a] focus:ring-[#ba1a1a]' : 'border-[#c5c5d7] focus:ring-[#3c50e0] focus:border-[#3c50e0]' }}">
                            @error('nisn')
                                <p class="text-[12px] text-[#ba1a1a]">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Nama + Jenis Kelamin --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1.5 sm:col-span-2">
                            <label class="text-[14px] font-medium text-on-surface" for="namaLengkap">
                                Nama Lengkap <span class="text-[#ba1a1a]">*</span>
                            </label>
                            <input wire:model="namaLengkap" id="namaLengkap" type="text" placeholder="Nama lengkap peserta didik"
                                   class="w-full border rounded-lg px-4 py-2.5 text-[14px] text-on-surface focus:outline-none focus:ring-1 transition-shadow
                                          {{ $errors->has('namaLengkap') ? 'border-[#ba1a1a] focus:ring-[#ba1a1a]' : 'border-[#c5c5d7] focus:ring-[#3c50e0] focus:border-[#3c50e0]' }}">
                            @error('namaLengkap')
                                <p class="text-[12px] text-[#ba1a1a]">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- NIK + Jenis Kelamin --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[14px] font-medium text-on-surface" for="nik">
                                NIK <span class="text-[12px] font-normal text-[#757686]">(opsional)</span>
                            </label>
                            <input wire:model="nik" id="nik" type="text" placeholder="16 digit NIK"
                                   class="w-full border border-[#c5c5d7] rounded-lg px-4 py-2.5 text-[14px] text-on-surface focus:outline-none focus:ring-1 focus:ring-[#3c50e0] transition-shadow">
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[14px] font-medium text-on-surface" for="jenisKelamin">
                                Jenis Kelamin <span class="text-[#ba1a1a]">*</span>
                            </label>
                            <select wire:model="jenisKelamin" id="jenisKelamin"
                                    class="w-full border rounded-lg px-4 py-2.5 text-[14px] text-on-surface focus:outline-none focus:ring-1 transition-shadow cursor-pointer
                                           {{ $errors->has('jenisKelamin') ? 'border-[#ba1a1a] focus:ring-[#ba1a1a]' : 'border-[#c5c5d7] focus:ring-[#3c50e0] focus:border-[#3c50e0]' }}">
                                <option value="">— Pilih —</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                            @error('jenisKelamin')
                                <p class="text-[12px] text-[#ba1a1a]">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Tempat + Tanggal Lahir --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[14px] font-medium text-on-surface" for="tempatLahir">
                                Tempat Lahir <span class="text-[12px] font-normal text-[#757686]">(opsional)</span>
                            </label>
                            <input wire:model="tempatLahir" id="tempatLahir" type="text" placeholder="Kota kelahiran"
                                   class="w-full border border-[#c5c5d7] rounded-lg px-4 py-2.5 text-[14px] text-on-surface focus:outline-none focus:ring-1 focus:ring-[#3c50e0] transition-shadow">
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[14px] font-medium text-on-surface" for="tanggalLahir">
                                Tanggal Lahir <span class="text-[12px] font-normal text-[#757686]">(opsional)</span>
                            </label>
                            <input wire:model="tanggalLahir" id="tanggalLahir" type="date"
                                   class="w-full border border-[#c5c5d7] rounded-lg px-4 py-2.5 text-[14px] text-on-surface focus:outline-none focus:ring-1 focus:ring-[#3c50e0] transition-shadow cursor-pointer">
                        </div>
                    </div>

                    {{-- Agama + No HP --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[14px] font-medium text-on-surface" for="agama">
                                Agama <span class="text-[12px] font-normal text-[#757686]">(opsional)</span>
                            </label>
                            <select wire:model="agama" id="agama"
                                    class="w-full border border-[#c5c5d7] rounded-lg px-4 py-2.5 text-[14px] text-on-surface focus:outline-none focus:ring-1 focus:ring-[#3c50e0] transition-shadow cursor-pointer">
                                <option value="">— Pilih Agama —</option>
                                <option value="Islam">Islam</option>
                                <option value="Kristen">Kristen</option>
                                <option value="Katolik">Katolik</option>
                                <option value="Hindu">Hindu</option>
                                <option value="Buddha">Buddha</option>
                                <option value="Konghucu">Konghucu</option>
                            </select>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[14px] font-medium text-on-surface" for="noHp">
                                No. HP <span class="text-[12px] font-normal text-[#757686]">(opsional)</span>
                            </label>
                            <input wire:model="noHp" id="noHp" type="text" placeholder="Contoh: 08123456789"
                                   class="w-full border border-[#c5c5d7] rounded-lg px-4 py-2.5 text-[14px] text-on-surface focus:outline-none focus:ring-1 focus:ring-[#3c50e0] transition-shadow">
                        </div>
                    </div>

                    {{-- Status Akademik (hanya edit) --}}
                    @if ($editId)
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[14px] font-medium text-on-surface" for="statusAkademik">
                                Status Akademik
                            </label>
                            <select wire:model="statusAkademik" id="statusAkademik"
                                    class="w-full border border-[#c5c5d7] rounded-lg px-4 py-2.5 text-[14px] text-on-surface focus:outline-none focus:ring-1 focus:ring-[#3c50e0] transition-shadow cursor-pointer">
                                <option value="aktif">Aktif</option>
                                <option value="lulus">Lulus</option>
                                <option value="pindah">Pindah</option>
                                <option value="keluar">Keluar</option>
                            </select>
                        </div>
                    @endif

                    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-4 border-t border-[#c5c5d7] mt-2">
                        <button type="button" wire:click="closeForm"
                                class="w-full sm:w-auto px-4 py-2.5 rounded-lg border border-[#c5c5d7] text-[#505f76] text-[14px] font-medium hover:bg-[#f0f4f8] transition-colors cursor-pointer text-center">
                            Batal
                        </button>
                        <button type="submit"
                                class="w-full sm:w-auto px-4 py-2.5 rounded-lg bg-[#3c50e0] text-white text-[14px] font-medium hover:bg-[#1c33c8] transition-colors shadow-sm flex items-center justify-center gap-2 cursor-pointer"
                                wire:loading.attr="disabled" wire:loading.class="opacity-70 cursor-not-allowed">
                            <span wire:loading wire:target="save" class="material-symbols-outlined text-[16px] animate-spin">progress_activity</span>
                            Simpan
                        </button>
                    </div>
                </form>
                </div>{{-- end scrollable --}}
            </div>
        </div>
    @endif

    {{-- ── Modal Konfirmasi Reset Password ────────────────────────────────────── --}}
    @if ($confirmResetId)
        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/50 backdrop-blur-sm p-0 sm:p-4">
            <div class="bg-white rounded-t-2xl sm:rounded-xl shadow-xl shadow-black/10 w-full max-w-4xl sm:max-w-sm border border-[#c5c5d7] p-6 flex flex-col gap-4">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full bg-[#fff3cd] flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-[#856404]">lock_reset</span>
                    </div>
                    <div>
                        <h4 class="text-[16px] font-semibold text-on-surface">Reset Password?</h4>
                        <p class="text-[14px] text-[#505f76] mt-1">Password akan direset ke NIPD peserta didik. Mereka akan diminta ganti password saat login berikutnya.</p>
                    </div>
                </div>
                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                    <button wire:click="$set('confirmResetId', null)"
                            class="w-full sm:w-auto px-4 py-2.5 rounded-lg border border-[#c5c5d7] text-[14px] text-[#505f76] hover:bg-[#f0f4f8] transition-colors cursor-pointer text-center">
                        Batal
                    </button>
                    <button wire:click="resetPassword"
                            class="w-full sm:w-auto px-4 py-2.5 rounded-lg bg-amber-500 text-white text-[14px] font-medium hover:bg-amber-600 transition-colors cursor-pointer text-center">
                        Ya, Reset
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ── Modal Konfirmasi Hapus ───────────────────────────────────────────── --}}
    @if ($confirmDeleteId)
        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/50 backdrop-blur-sm p-0 sm:p-4">
            <div class="bg-white rounded-t-2xl sm:rounded-xl shadow-xl shadow-black/10 w-full max-w-4xl sm:max-w-sm border border-[#c5c5d7] p-6 flex flex-col gap-4">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full bg-[#ffdad6] flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-[#ba1a1a]">delete</span>
                    </div>
                    <div>
                        <h4 class="text-[16px] font-semibold text-on-surface">Hapus Peserta Didik ini?</h4>
                        <p class="text-[14px] text-[#505f76] mt-1">Akun login juga akan dihapus. Tindakan ini tidak dapat dibatalkan.</p>
                    </div>
                </div>
                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                    <button wire:click="$set('confirmDeleteId', null)"
                            class="w-full sm:w-auto px-4 py-2.5 rounded-lg border border-[#c5c5d7] text-[14px] text-[#505f76] hover:bg-[#f0f4f8] transition-colors cursor-pointer text-center">
                        Batal
                    </button>
                    <button wire:click="delete"
                            class="w-full sm:w-auto px-4 py-2.5 rounded-lg bg-[#ba1a1a] text-white text-[14px] font-medium hover:bg-[#93000a] transition-colors cursor-pointer text-center">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ── Tabel ───────────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm overflow-hidden">

        <x-table-controls searchPlaceholder="Cari nama, NIPD, atau NISN...">
            <x-slot:filters>
                <select wire:model.live="filterStatusAkademik"
                        class="border border-[#c5c5d7] rounded-lg px-3 py-2 text-[14px] text-on-surface bg-white focus:outline-none focus:ring-1 focus:ring-[#3c50e0] cursor-pointer transition-shadow flex-shrink-0">
                    <option value="">Semua Status</option>
                    <option value="aktif">Aktif</option>
                    <option value="lulus">Lulus</option>
                    <option value="pindah">Pindah</option>
                    <option value="keluar">Keluar</option>
                </select>
            </x-slot:filters>
        </x-table-controls>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-[#f0f4f8] border-b border-[#c5c5d7] text-[12px] font-semibold text-[#505f76] uppercase tracking-wider">
                        <th class="px-6 py-4 w-12">No.</th>
                        <th class="px-6 py-4">Peserta Didik</th>
                        <th class="px-6 py-4">NIPD / NISN</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Akun</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-[14px] text-on-surface divide-y divide-[#c5c5d7]">
                    @forelse ($pesertaDidiks as $i => $pd)
                        @php
                            $statusColors = [
                                'aktif'  => 'bg-green-100 text-green-700',
                                'lulus'  => 'bg-blue-100 text-blue-700',
                                'pindah' => 'bg-amber-100 text-amber-700',
                                'keluar' => 'bg-red-100 text-red-700',
                            ];
                        @endphp
                        <tr class="hover:bg-[#f6fafe] transition-colors">
                            <td class="px-6 py-4 text-[#505f76]">{{ $pesertaDidiks->firstItem() + $i }}</td>
                            <td class="px-6 py-4">
                                <p class="font-medium">{{ $pd->nama_lengkap }}</p>
                                <p class="text-[12px] text-[#505f76] mt-0.5">
                                    {{ $pd->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                    @if ($pd->tanggal_lahir)
                                        &bull; {{ $pd->tanggal_lahir->format('d M Y') }}
                                    @endif
                                </p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-mono text-[13px] bg-[#f0f4f8] px-2 py-0.5 rounded inline-block">{{ $pd->nipd }}</p>
                                @if ($pd->nisn)
                                    <p class="text-[12px] text-[#505f76] mt-0.5">NISN: {{ $pd->nisn }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[12px] font-semibold {{ $statusColors[$pd->status_akademik] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ ucfirst($pd->status_akademik) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button wire:click="toggleActive({{ $pd->id }})"
                                        title="{{ $pd->user?->is_active ? 'Klik untuk nonaktifkan' : 'Klik untuk aktifkan' }}"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[12px] font-semibold transition-colors cursor-pointer
                                               {{ $pd->user?->is_active
                                                   ? 'bg-green-100 text-green-700 hover:bg-green-200'
                                                   : 'bg-red-100 text-red-700 hover:bg-red-200' }}">
                                    <span class="material-symbols-outlined text-[14px]">
                                        {{ $pd->user?->is_active ? 'check_circle' : 'cancel' }}
                                    </span>
                                    {{ $pd->user?->is_active ? 'Aktif' : 'Nonaktif' }}
                                </button>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.pengguna.peserta-didik.show', $pd->id) }}"
                                       class="p-1.5 text-[#505f76] hover:text-[#3c50e0] hover:bg-[#EEF2FF] rounded-lg transition-colors cursor-pointer"
                                       title="Lihat detail">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    </a>
                                    <button wire:click="openEditForm({{ $pd->id }})"
                                            class="p-1.5 text-[#505f76] hover:text-[#1c33c8] hover:bg-[#eaeef2] rounded-lg transition-colors cursor-pointer"
                                            title="Edit data">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </button>
                                    <button wire:click="confirmReset({{ $pd->id }})"
                                            class="p-1.5 text-[#505f76] hover:text-amber-700 hover:bg-amber-50 rounded-lg transition-colors cursor-pointer"
                                            title="Reset password ke NIPD">
                                        <span class="material-symbols-outlined text-[18px]">lock_reset</span>
                                    </button>
                                    <button wire:click="confirmDelete({{ $pd->id }})"
                                            class="p-1.5 text-[#505f76] hover:text-[#ba1a1a] hover:bg-[#ffdad6] rounded-lg transition-colors cursor-pointer"
                                            title="Hapus">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                @if ($search || $filterStatusAkademik)
                                    <span class="material-symbols-outlined text-[48px] text-[#c5c5d7] mb-3 block">search_off</span>
                                    <p class="text-[14px] text-[#505f76]">Tidak ada peserta didik yang cocok dengan filter.</p>
                                    <button wire:click="$set('search', ''); $set('filterStatusAkademik', '')"
                                            class="mt-3 text-[13px] text-[#3c50e0] hover:underline cursor-pointer">
                                        Hapus Filter
                                    </button>
                                @else
                                    <span class="material-symbols-outlined text-[48px] text-[#c5c5d7] mb-3 block">person</span>
                                    <p class="text-[14px] font-medium text-on-surface">Belum ada data peserta didik.</p>
                                    <p class="text-[13px] text-[#505f76] mt-1">Klik "Tambah Peserta Didik" atau gunakan fitur Import Excel.</p>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination footer --}}
        @if ($pesertaDidiks->hasPages())
            <div class="px-6 py-4 border-t border-[#c5c5d7] flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-[#f8fafc]">
                <p class="text-[13px] text-[#505f76]">
                    Menampilkan {{ $pesertaDidiks->firstItem() }}–{{ $pesertaDidiks->lastItem() }} dari {{ $pesertaDidiks->total() }} data
                </p>
                {{ $pesertaDidiks->links() }}
            </div>
        @elseif ($pesertaDidiks->total() > 0)
            <div class="px-6 py-3 border-t border-[#c5c5d7] bg-[#f8fafc]">
                <p class="text-[13px] text-[#505f76]">Menampilkan {{ $pesertaDidiks->total() }} data</p>
            </div>
        @endif

    </div>

</div>
