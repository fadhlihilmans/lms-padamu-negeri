<div>

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-[24px] font-bold tracking-tight text-on-surface">Periode Ajaran</h2>
            <p class="text-[14px] text-[#505f76] mt-0.5">Kelola tahun ajaran dan semester aktif.</p>
        </div>
        <button
            wire:click="bukaFormTambah"
            class="inline-flex items-center gap-2 bg-[#3c50e0] text-white text-[14px] font-medium px-4 py-2.5 rounded-lg hover:bg-[#1c33c8] transition-colors shadow-sm"
        >
            <span class="material-symbols-outlined text-[18px]">add</span>
            Tambah Periode
        </button>
    </div>

    {{-- ── Modal Form Tambah / Edit ─────────────────────────────────────────── --}}
    @if ($showForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
            <div class="bg-white rounded-xl shadow-md w-full max-w-md border border-[#c5c5d7]">

                {{-- Header modal --}}
                <div class="flex items-center justify-between p-6 border-b border-[#c5c5d7]">
                    <h3 class="text-[20px] font-semibold text-on-surface">
                        {{ $editId ? 'Edit Periode Ajaran' : 'Tambah Periode Ajaran' }}
                    </h3>
                    <button wire:click="tutupForm" class="text-[#505f76] hover:text-[#ba1a1a] transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                {{-- Body modal --}}
                <form wire:submit="simpan" class="p-6 flex flex-col gap-4">

                    {{-- Tahun Ajaran --}}
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[14px] font-medium text-on-surface" for="tahunAjaran">
                            Tahun Ajaran <span class="text-[#ba1a1a]">*</span>
                        </label>
                        <input
                            wire:model="tahunAjaran"
                            id="tahunAjaran"
                            type="text"
                            placeholder="Contoh: 2024/2025"
                            class="w-full border rounded-lg px-4 py-2.5 text-[14px] text-on-surface focus:outline-none focus:ring-1 transition-shadow
                                   {{ $errors->has('tahunAjaran') ? 'border-[#ba1a1a] focus:ring-[#ba1a1a]' : 'border-[#c5c5d7] focus:ring-[#3c50e0] focus:border-[#3c50e0]' }}"
                        >
                        @error('tahunAjaran')
                            <p class="text-[12px] text-[#ba1a1a]">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Semester --}}
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[14px] font-medium text-on-surface" for="semester">
                            Semester <span class="text-[#ba1a1a]">*</span>
                        </label>
                        <select
                            wire:model="semester"
                            id="semester"
                            class="w-full border rounded-lg px-4 py-2.5 text-[14px] text-on-surface focus:outline-none focus:ring-1 transition-shadow
                                   {{ $errors->has('semester') ? 'border-[#ba1a1a] focus:ring-[#ba1a1a]' : 'border-[#c5c5d7] focus:ring-[#3c50e0] focus:border-[#3c50e0]' }}"
                        >
                            <option value="">— Pilih Semester —</option>
                            <option value="ganjil">Ganjil</option>
                            <option value="genap">Genap</option>
                        </select>
                        @error('semester')
                            <p class="text-[12px] text-[#ba1a1a]">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Footer modal --}}
                    <div class="flex justify-end gap-3 pt-2 border-t border-[#c5c5d7] mt-1">
                        <button
                            type="button"
                            wire:click="tutupForm"
                            class="px-4 py-2.5 rounded-lg border border-[#c5c5d7] text-[#505f76] text-[14px] font-medium hover:bg-[#f0f4f8] transition-colors"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2.5 rounded-lg bg-[#3c50e0] text-white text-[14px] font-medium hover:bg-[#1c33c8] transition-colors shadow-sm flex items-center gap-2"
                            wire:loading.attr="disabled" wire:loading.class="opacity-70 cursor-not-allowed"
                        >
                            <span wire:loading wire:target="simpan" class="material-symbols-outlined text-[16px] animate-spin">progress_activity</span>
                            Simpan
                        </button>
                    </div>

                </form>
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
                        <h4 class="text-[16px] font-semibold text-on-surface">Hapus Periode Ajaran?</h4>
                        <p class="text-[14px] text-[#505f76] mt-1">Tindakan ini tidak dapat dibatalkan.</p>
                    </div>
                </div>
                <div class="flex justify-end gap-3">
                    <button wire:click="$set('confirmDeleteId', null)"
                            class="px-4 py-2 rounded-lg border border-[#c5c5d7] text-[14px] text-[#505f76] hover:bg-[#f0f4f8] transition-colors">
                        Batal
                    </button>
                    <button wire:click="hapus"
                            class="px-4 py-2 rounded-lg bg-[#ba1a1a] text-white text-[14px] font-medium hover:bg-[#93000a] transition-colors">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ── Modal Konfirmasi Set Aktif ──────────────────────────────────────── --}}
    @if ($confirmAktifId)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
            <div class="bg-white rounded-xl shadow-md w-full max-w-sm border border-[#c5c5d7] p-6 flex flex-col gap-4">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full bg-[#EEF2FF] flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-[#3c50e0]">check_circle</span>
                    </div>
                    <div>
                        <h4 class="text-[16px] font-semibold text-on-surface">Jadikan Periode Aktif?</h4>
                        <p class="text-[14px] text-[#505f76] mt-1">Periode aktif saat ini akan dinonaktifkan secara otomatis.</p>
                    </div>
                </div>
                <div class="flex justify-end gap-3">
                    <button wire:click="$set('confirmAktifId', null)"
                            class="px-4 py-2 rounded-lg border border-[#c5c5d7] text-[14px] text-[#505f76] hover:bg-[#f0f4f8] transition-colors">
                        Batal
                    </button>
                    <button wire:click="setAktif"
                            class="px-4 py-2 rounded-lg bg-[#3c50e0] text-white text-[14px] font-medium hover:bg-[#1c33c8] transition-colors">
                        Ya, Aktifkan
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ── Tabel ───────────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-[#f0f4f8] border-b border-[#c5c5d7] text-[12px] font-semibold text-[#505f76] uppercase tracking-wider">
                        <th class="px-6 py-4 w-16">No.</th>
                        <th class="px-6 py-4">Tahun Ajaran</th>
                        <th class="px-6 py-4">Semester</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-[14px] text-on-surface divide-y divide-[#c5c5d7]">

                    @forelse ($periodes as $i => $p)
                        <tr class="hover:bg-[#f6fafe] transition-colors group">
                            <td class="px-6 py-4 text-[#505f76]">{{ $i + 1 }}</td>
                            <td class="px-6 py-4 font-medium">{{ $p->tahun_ajaran }}</td>
                            <td class="px-6 py-4">{{ ucfirst($p->semester) }}</td>
                            <td class="px-6 py-4">
                                @if ($p->is_aktif)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[12px] font-semibold bg-green-100 text-green-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[12px] font-semibold bg-[#eaeef2] text-[#505f76]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#757686]"></span>
                                        Tidak Aktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">

                                    @unless ($p->is_aktif)
                                        <button
                                            wire:click="konfirmasiAktif({{ $p->id }})"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-[12px] font-medium text-[#3c50e0] border border-[#3c50e0] hover:bg-[#3c50e0] hover:text-white transition-colors cursor-pointer"
                                            title="Jadikan Aktif"
                                        >
                                            <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                            Aktifkan
                                        </button>
                                    @endunless

                                    <button
                                        wire:click="bukaFormEdit({{ $p->id }})"
                                        class="p-1.5 text-[#505f76] hover:text-[#1c33c8] hover:bg-[#eaeef2] rounded-lg transition-colors"
                                        title="Edit"
                                    >
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </button>

                                    @unless ($p->is_aktif)
                                        <button
                                            wire:click="konfirmasiHapus({{ $p->id }})"
                                            class="p-1.5 text-[#505f76] hover:text-[#ba1a1a] hover:bg-[#ffdad6] rounded-lg transition-colors"
                                            title="Hapus"
                                        >
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    @endunless

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <span class="material-symbols-outlined text-[48px] text-[#c5c5d7] mb-3 block">calendar_month</span>
                                <p class="text-[14px] text-[#505f76]">Belum ada data Periode Ajaran.</p>
                                <button wire:click="bukaFormTambah"
                                        class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[#3c50e0] text-white text-[14px] font-medium hover:bg-[#1c33c8] transition-colors">
                                    <span class="material-symbols-outlined text-[16px]">add</span>
                                    Tambah Sekarang
                                </button>
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>
    </div>

</div>
