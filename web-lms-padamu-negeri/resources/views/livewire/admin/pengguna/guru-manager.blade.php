<div>

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-[24px] font-bold tracking-tight text-on-surface">Manajemen Guru</h2>
            <p class="text-[14px] text-[#505f76] mt-0.5">Kelola data guru dan akun login mereka.</p>
        </div>
        <button wire:click="openCreateForm"
                class="inline-flex items-center gap-2 bg-[#3c50e0] text-white text-[14px] font-medium px-4 py-2.5 rounded-lg hover:bg-[#1c33c8] transition-colors shadow-sm cursor-pointer flex-shrink-0">
            <span class="material-symbols-outlined text-[18px]">add</span>
            Tambah Guru
        </button>
    </div>

    {{-- ── Modal Form Tambah / Edit ─────────────────────────────────────────── --}}
    @if ($showForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
            <div class="bg-white rounded-xl shadow-md w-full max-w-md border border-[#c5c5d7]">
                <div class="flex items-center justify-between p-6 border-b border-[#c5c5d7]">
                    <h3 class="text-[20px] font-semibold text-on-surface">
                        {{ $editId ? 'Edit Data Guru' : 'Tambah Guru Baru' }}
                    </h3>
                    <button wire:click="closeForm" class="text-[#505f76] hover:text-[#ba1a1a] transition-colors p-1 cursor-pointer">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <form wire:submit="save" class="p-6 flex flex-col gap-4">

                    {{-- NIP --}}
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[14px] font-medium text-on-surface" for="nip">
                            NIP <span class="text-[#ba1a1a]">*</span>
                        </label>
                        <input wire:model="nip" id="nip" type="text" placeholder="Masukkan NIP guru"
                               class="w-full border rounded-lg px-4 py-2.5 text-[14px] text-on-surface focus:outline-none focus:ring-1 transition-shadow
                                      {{ $errors->has('nip') ? 'border-[#ba1a1a] focus:ring-[#ba1a1a]' : 'border-[#c5c5d7] focus:ring-[#3c50e0] focus:border-[#3c50e0]' }}">
                        @error('nip')
                            <p class="text-[12px] text-[#ba1a1a]">{{ $message }}</p>
                        @enderror
                        @if (! $editId)
                            <p class="text-[12px] text-[#757686]">NIP akan digunakan sebagai username dan password awal akun.</p>
                        @else
                            <p class="text-[12px] text-amber-600">Mengubah NIP akan otomatis memperbarui username akun login.</p>
                        @endif
                    </div>

                    {{-- Nama Lengkap --}}
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[14px] font-medium text-on-surface" for="namaLengkap">
                            Nama Lengkap <span class="text-[#ba1a1a]">*</span>
                        </label>
                        <input wire:model="namaLengkap" id="namaLengkap" type="text" placeholder="Nama lengkap beserta gelar"
                               class="w-full border rounded-lg px-4 py-2.5 text-[14px] text-on-surface focus:outline-none focus:ring-1 transition-shadow
                                      {{ $errors->has('namaLengkap') ? 'border-[#ba1a1a] focus:ring-[#ba1a1a]' : 'border-[#c5c5d7] focus:ring-[#3c50e0] focus:border-[#3c50e0]' }}">
                        @error('namaLengkap')
                            <p class="text-[12px] text-[#ba1a1a]">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- No HP --}}
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[14px] font-medium text-on-surface" for="noHp">
                            No. HP <span class="text-[12px] font-normal text-[#757686]">(opsional)</span>
                        </label>
                        <input wire:model="noHp" id="noHp" type="text" placeholder="Contoh: 08123456789"
                               class="w-full border border-[#c5c5d7] rounded-lg px-4 py-2.5 text-[14px] text-on-surface focus:outline-none focus:ring-1 focus:ring-[#3c50e0] focus:border-[#3c50e0] transition-shadow">
                    </div>

                    <div class="flex justify-end gap-3 pt-2 border-t border-[#c5c5d7] mt-1">
                        <button type="button" wire:click="closeForm"
                                class="px-4 py-2.5 rounded-lg border border-[#c5c5d7] text-[#505f76] text-[14px] font-medium hover:bg-[#f0f4f8] transition-colors cursor-pointer">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-4 py-2.5 rounded-lg bg-[#3c50e0] text-white text-[14px] font-medium hover:bg-[#1c33c8] transition-colors shadow-sm flex items-center gap-2 cursor-pointer"
                                wire:loading.attr="disabled" wire:loading.class="opacity-70 cursor-not-allowed">
                            <span wire:loading wire:target="save" class="material-symbols-outlined text-[16px] animate-spin">progress_activity</span>
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ── Modal Konfirmasi Reset Password ────────────────────────────────────── --}}
    @if ($confirmResetId)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
            <div class="bg-white rounded-xl shadow-md w-full max-w-sm border border-[#c5c5d7] p-6 flex flex-col gap-4">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full bg-[#fff3cd] flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-[#856404]">lock_reset</span>
                    </div>
                    <div>
                        <h4 class="text-[16px] font-semibold text-on-surface">Reset Password?</h4>
                        <p class="text-[14px] text-[#505f76] mt-1">Password akan direset ke NIP guru. Guru akan diminta ganti password saat login berikutnya.</p>
                    </div>
                </div>
                <div class="flex justify-end gap-3">
                    <button wire:click="$set('confirmResetId', null)"
                            class="px-4 py-2 rounded-lg border border-[#c5c5d7] text-[14px] text-[#505f76] hover:bg-[#f0f4f8] transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button wire:click="resetPassword"
                            class="px-4 py-2 rounded-lg bg-amber-500 text-white text-[14px] font-medium hover:bg-amber-600 transition-colors cursor-pointer">
                        Ya, Reset
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ── Modal Konfirmasi Hapus ───────────────────────────────────────────── --}}
    @if ($confirmDeleteId)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
            <div class="bg-white rounded-xl shadow-md w-full max-w-sm border border-[#c5c5d7] p-6 flex flex-col gap-4">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full bg-[#ffdad6] flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-[#ba1a1a]">delete</span>
                    </div>
                    <div>
                        <h4 class="text-[16px] font-semibold text-on-surface">Hapus Guru ini?</h4>
                        <p class="text-[14px] text-[#505f76] mt-1">Akun login guru juga akan dihapus. Tindakan ini tidak dapat dibatalkan.</p>
                    </div>
                </div>
                <div class="flex justify-end gap-3">
                    <button wire:click="$set('confirmDeleteId', null)"
                            class="px-4 py-2 rounded-lg border border-[#c5c5d7] text-[14px] text-[#505f76] hover:bg-[#f0f4f8] transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button wire:click="delete"
                            class="px-4 py-2 rounded-lg bg-[#ba1a1a] text-white text-[14px] font-medium hover:bg-[#93000a] transition-colors cursor-pointer">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ── Tabel ───────────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm overflow-hidden">

        <x-table-controls searchPlaceholder="Cari nama atau NIP guru..." />

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-[#f0f4f8] border-b border-[#c5c5d7] text-[12px] font-semibold text-[#505f76] uppercase tracking-wider">
                        <th class="px-6 py-4 w-12">No.</th>
                        <th class="px-6 py-4">Guru</th>
                        <th class="px-6 py-4">NIP / Username</th>
                        <th class="px-6 py-4">No. HP</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-[14px] text-on-surface divide-y divide-[#c5c5d7]">
                    @forelse ($gurus as $i => $guru)
                        <tr class="hover:bg-[#f6fafe] transition-colors">
                            <td class="px-6 py-4 text-[#505f76]">{{ $gurus->firstItem() + $i }}</td>
                            <td class="px-6 py-4">
                                <p class="font-medium">{{ $guru->nama_lengkap }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-mono text-[13px] bg-[#f0f4f8] px-2 py-0.5 rounded">{{ $guru->nip }}</span>
                            </td>
                            <td class="px-6 py-4 text-[#505f76]">{{ $guru->no_hp ?? '—' }}</td>
                            <td class="px-6 py-4 text-center">
                                <button wire:click="toggleActive({{ $guru->id }})"
                                        title="{{ $guru->user?->is_active ? 'Klik untuk nonaktifkan' : 'Klik untuk aktifkan' }}"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[12px] font-semibold transition-colors cursor-pointer
                                               {{ $guru->user?->is_active
                                                   ? 'bg-green-100 text-green-700 hover:bg-green-200'
                                                   : 'bg-red-100 text-red-700 hover:bg-red-200' }}">
                                    <span class="material-symbols-outlined text-[14px]">
                                        {{ $guru->user?->is_active ? 'check_circle' : 'cancel' }}
                                    </span>
                                    {{ $guru->user?->is_active ? 'Aktif' : 'Nonaktif' }}
                                </button>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-1">
                                    <button wire:click="openEditForm({{ $guru->id }})"
                                            class="p-1.5 text-[#505f76] hover:text-[#1c33c8] hover:bg-[#eaeef2] rounded-lg transition-colors cursor-pointer"
                                            title="Edit data guru">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </button>
                                    <button wire:click="confirmReset({{ $guru->id }})"
                                            class="p-1.5 text-[#505f76] hover:text-amber-700 hover:bg-amber-50 rounded-lg transition-colors cursor-pointer"
                                            title="Reset password ke NIP">
                                        <span class="material-symbols-outlined text-[18px]">lock_reset</span>
                                    </button>
                                    <button wire:click="confirmDelete({{ $guru->id }})"
                                            class="p-1.5 text-[#505f76] hover:text-[#ba1a1a] hover:bg-[#ffdad6] rounded-lg transition-colors cursor-pointer"
                                            title="Hapus guru">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                @if ($search)
                                    <span class="material-symbols-outlined text-[48px] text-[#c5c5d7] mb-3 block">search_off</span>
                                    <p class="text-[14px] text-[#505f76]">Tidak ada guru yang cocok dengan pencarian.</p>
                                    <button wire:click="$set('search', '')"
                                            class="mt-3 text-[13px] text-[#3c50e0] hover:underline cursor-pointer">
                                        Hapus Pencarian
                                    </button>
                                @else
                                    <span class="material-symbols-outlined text-[48px] text-[#c5c5d7] mb-3 block">badge</span>
                                    <p class="text-[14px] font-medium text-on-surface">Belum ada data guru.</p>
                                    <p class="text-[13px] text-[#505f76] mt-1">Klik "Tambah Guru" untuk menambahkan guru baru.</p>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination footer --}}
        @if ($gurus->hasPages())
            <div class="px-6 py-4 border-t border-[#c5c5d7] flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-[#f8fafc]">
                <p class="text-[13px] text-[#505f76]">
                    Menampilkan {{ $gurus->firstItem() }}–{{ $gurus->lastItem() }} dari {{ $gurus->total() }} data
                </p>
                {{ $gurus->links() }}
            </div>
        @elseif ($gurus->total() > 0)
            <div class="px-6 py-3 border-t border-[#c5c5d7] bg-[#f8fafc]">
                <p class="text-[13px] text-[#505f76]">Menampilkan {{ $gurus->total() }} data</p>
            </div>
        @endif

    </div>

</div>
