<div>

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-[24px] font-bold tracking-tight text-on-surface">Tingkat</h2>
            <p class="text-[14px] text-[#505f76] mt-0.5">Kelola data tingkat kelas per Paket.</p>
        </div>
        <button wire:click="bukaFormTambah"
                class="inline-flex items-center gap-2 bg-[#3c50e0] text-white text-[14px] font-medium px-4 py-2.5 rounded-lg hover:bg-[#1c33c8] transition-colors shadow-sm">
            <span class="material-symbols-outlined text-[18px]">add</span>
            Tambah Tingkat
        </button>
    </div>

    {{-- ── Modal Form ──────────────────────────────────────────────────────── --}}
    @if ($showForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
            <div class="bg-white rounded-xl shadow-md w-full max-w-sm border border-[#c5c5d7]">
                <div class="flex items-center justify-between p-6 border-b border-[#c5c5d7]">
                    <h3 class="text-[20px] font-semibold text-on-surface">
                        {{ $editId ? 'Edit Tingkat' : 'Tambah Tingkat' }}
                    </h3>
                    <button wire:click="tutupForm" class="text-[#505f76] hover:text-[#ba1a1a] transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <form wire:submit="simpan" class="p-6 flex flex-col gap-4">

                    {{-- Paket --}}
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[14px] font-medium text-on-surface" for="paketId">
                            Paket <span class="text-[#ba1a1a]">*</span>
                        </label>
                        <select wire:model="paketId" id="paketId"
                                class="w-full border rounded-lg px-4 py-2.5 text-[14px] text-on-surface focus:outline-none focus:ring-1 transition-shadow
                                       {{ $errors->has('paketId') ? 'border-[#ba1a1a] focus:ring-[#ba1a1a]' : 'border-[#c5c5d7] focus:ring-[#3c50e0] focus:border-[#3c50e0]' }}">
                            <option value="">— Pilih Paket —</option>
                            @foreach ($pakets as $pk)
                                <option value="{{ $pk->id }}">{{ $pk->nama }}</option>
                            @endforeach
                        </select>
                        @error('paketId')
                            <p class="text-[12px] text-[#ba1a1a]">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nama Tingkat --}}
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[14px] font-medium text-on-surface" for="nama">
                            Nama Tingkat <span class="text-[#ba1a1a]">*</span>
                        </label>
                        <input wire:model="nama" id="nama" type="text" placeholder="Contoh: Kelas 10"
                               class="w-full border rounded-lg px-4 py-2.5 text-[14px] text-on-surface focus:outline-none focus:ring-1 transition-shadow
                                      {{ $errors->has('nama') ? 'border-[#ba1a1a] focus:ring-[#ba1a1a]' : 'border-[#c5c5d7] focus:ring-[#3c50e0] focus:border-[#3c50e0]' }}">
                        @error('nama')
                            <p class="text-[12px] text-[#ba1a1a]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-2 border-t border-[#c5c5d7] mt-1">
                        <button type="button" wire:click="tutupForm"
                                class="px-4 py-2.5 rounded-lg border border-[#c5c5d7] text-[#505f76] text-[14px] font-medium hover:bg-[#f0f4f8] transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-4 py-2.5 rounded-lg bg-[#3c50e0] text-white text-[14px] font-medium hover:bg-[#1c33c8] transition-colors shadow-sm flex items-center gap-2"
                                wire:loading.attr="disabled" wire:loading.class="opacity-70 cursor-not-allowed">
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
                        <h4 class="text-[16px] font-semibold text-on-surface">Hapus Tingkat?</h4>
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

    {{-- ── Tabel ───────────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-[#f0f4f8] border-b border-[#c5c5d7] text-[12px] font-semibold text-[#505f76] uppercase tracking-wider">
                        <th class="px-6 py-4 w-16">No.</th>
                        <th class="px-6 py-4">Nama Tingkat</th>
                        <th class="px-6 py-4">Paket</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-[14px] text-on-surface divide-y divide-[#c5c5d7]">
                    @forelse ($tingkats as $i => $t)
                        <tr class="hover:bg-[#f6fafe] transition-colors">
                            <td class="px-6 py-4 text-[#505f76]">{{ $i + 1 }}</td>
                            <td class="px-6 py-4 font-medium">{{ $t->nama }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[12px] font-semibold bg-[#EEF2FF] text-[#1c33c8]">
                                    {{ $t->paket?->nama ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:click="bukaFormEdit({{ $t->id }})"
                                            class="p-1.5 text-[#505f76] hover:text-[#1c33c8] hover:bg-[#eaeef2] rounded-lg transition-colors"
                                            title="Edit">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </button>
                                    <button wire:click="konfirmasiHapus({{ $t->id }})"
                                            class="p-1.5 text-[#505f76] hover:text-[#ba1a1a] hover:bg-[#ffdad6] rounded-lg transition-colors"
                                            title="Hapus">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-16 text-center">
                                <span class="material-symbols-outlined text-[48px] text-[#c5c5d7] mb-3 block">bar_chart</span>
                                <p class="text-[14px] text-[#505f76]">Belum ada data Tingkat.</p>
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
