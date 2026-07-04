<div class="max-w-5xl mx-auto space-y-4">

    @php
        $pelapor = fn ($u) => $u?->guru?->nama_lengkap ?? $u?->pesertaDidik?->nama_lengkap ?? $u?->username ?? '—';
    @endphp

    {{-- ── Heading ─────────────────────────────────────────────────────────── --}}
    <div>
        <h1 class="text-xl font-bold text-on-surface">Laporan Bug</h1>
        <p class="text-sm text-[#505f76] mt-0.5">Tindak lanjuti laporan masalah yang dikirim pengguna.</p>
    </div>

    {{-- ── Filter ──────────────────────────────────────────────────────────── --}}
    <div class="bg-white border border-[#c5c5d7] rounded-xl p-4 flex flex-col sm:flex-row gap-3">
        <div class="flex-1 relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#757686] text-[18px] pointer-events-none">search</span>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari judul laporan…"
                   class="w-full pl-10 pr-4 py-2.5 border border-[#c5c5d7] rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-[#3c50e0]">
        </div>
        <select wire:model.live="filterStatus"
                class="sm:w-44 px-3 py-2.5 border border-[#c5c5d7] rounded-lg text-sm bg-white cursor-pointer focus:outline-none focus:ring-1 focus:ring-[#3c50e0]">
            <option value="">Semua Status</option>
            <option value="baru">Baru</option>
            <option value="diproses">Diproses</option>
            <option value="selesai">Selesai</option>
            <option value="ditolak">Ditolak</option>
        </select>
    </div>

    {{-- ── Tabel ───────────────────────────────────────────────────────────── --}}
    <div class="bg-white border border-[#c5c5d7] rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-white border-b border-[#c5c5d7]">
                        <th class="px-4 py-3 text-xs font-semibold text-[#505f76] uppercase tracking-wide">Judul</th>
                        <th class="px-4 py-3 text-xs font-semibold text-[#505f76] uppercase tracking-wide">Pelapor</th>
                        <th class="px-4 py-3 text-xs font-semibold text-[#505f76] uppercase tracking-wide">Status</th>
                        <th class="px-4 py-3 text-xs font-semibold text-[#505f76] uppercase tracking-wide">Waktu</th>
                        <th class="px-4 py-3 text-xs font-semibold text-[#505f76] uppercase tracking-wide text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#c5c5d7]">
                    @forelse ($reports as $b)
                        @php $meta = $statusMeta[$b->status] ?? [$b->status, 'bg-[#f0f4f8] text-[#757686] border-[#c5c5d7]']; @endphp
                        <tr class="hover:bg-[#f6fafe] transition-colors">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="font-medium text-on-surface flex items-center gap-1.5">
                                    @if ($b->screenshot_path) <span class="material-symbols-outlined text-[15px] text-[#757686]">image</span> @endif
                                    {{ $b->judul }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-[#505f76] whitespace-nowrap">{{ $pelapor($b->user) }}</td>
                            <td class="px-4 py-3 whitespace-nowrap"><span class="text-xs px-2 py-0.5 rounded-full border font-medium {{ $meta[1] }}">{{ $meta[0] }}</span></td>
                            <td class="px-4 py-3 text-xs text-[#757686] whitespace-nowrap">{{ $b->created_at->format('d/m/y H:i') }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-1">
                                    <button wire:click="openDetail({{ $b->id }})"
                                            class="p-1.5 text-[#505f76] hover:text-[#3c50e0] hover:bg-[#EEF2FF] rounded-lg transition-colors cursor-pointer" title="Detail & Tindak Lanjut">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    </button>
                                    <button wire:click="$set('confirmDeleteId', {{ $b->id }})"
                                            class="p-1.5 text-[#505f76] hover:text-[#ba1a1a] hover:bg-[#ffdad6] rounded-lg transition-colors cursor-pointer" title="Hapus">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-16 text-center">
                                <span class="material-symbols-outlined text-[48px] text-[#c5c5d7] mb-3 block">inbox</span>
                                <p class="text-[15px] font-medium text-on-surface mb-1">{{ $search || $filterStatus ? 'Tidak ada laporan yang cocok' : 'Belum ada laporan' }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($reports->hasPages())
            <div class="px-4 py-3 border-t border-[#c5c5d7] flex items-center justify-between text-sm text-[#505f76]">
                <span>Menampilkan {{ $reports->firstItem() ?? 0 }}–{{ $reports->lastItem() ?? 0 }} dari {{ $reports->total() }}</span>
                <div>{{ $reports->links() }}</div>
            </div>
        @endif
    </div>

    {{-- ── Modal detail & tindak lanjut ────────────────────────────────────── --}}
    @if ($detail)
        <div class="fixed inset-0 z-50 bg-black/40 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4">
            <div class="relative bg-white w-full max-w-4xl sm:max-w-lg rounded-t-2xl sm:rounded-xl border border-[#c5c5d7]">
                <button wire:click="closeDetail" type="button" class="absolute top-4 right-4 text-[#505f76] hover:text-[#ba1a1a] p-1 cursor-pointer z-10 bg-white rounded-full">
                    <span class="material-symbols-outlined">close</span>
                </button>
                <div class="p-6 sm:p-7 max-h-[90vh] overflow-y-auto">
                    <div class="mb-4 pr-8">
                        <h3 class="text-[18px] font-semibold text-on-surface">{{ $detail->judul }}</h3>
                        <p class="text-xs text-[#757686] mt-1">{{ $pelapor($detail->user) }} · {{ $detail->created_at->translatedFormat('d M Y, H:i') }}</p>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <p class="text-xs font-medium text-[#505f76] mb-1">Deskripsi</p>
                            <p class="text-sm text-on-surface leading-relaxed whitespace-pre-line bg-[#f6fafe] border border-[#c5c5d7] rounded-lg p-3">{{ $detail->deskripsi }}</p>
                        </div>

                        @if ($detail->halaman_url)
                            <p class="text-xs text-[#757686] flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">link</span> <span class="truncate">{{ $detail->halaman_url }}</span></p>
                        @endif

                        @if ($detail->screenshot_path)
                            <div>
                                <p class="text-xs font-medium text-[#505f76] mb-1">Lampiran</p>
                                <a href="{{ Storage::url($detail->screenshot_path) }}" target="_blank" class="block">
                                    <img src="{{ Storage::url($detail->screenshot_path) }}" class="max-h-56 rounded-lg border border-[#c5c5d7]" alt="screenshot">
                                </a>
                            </div>
                        @endif

                        <div class="border-t border-[#c5c5d7] pt-4 space-y-3">
                            <div>
                                <label class="block text-[13px] font-medium text-on-surface mb-1.5">Status</label>
                                <select wire:model="editStatus"
                                        class="w-full px-3 py-2 border border-[#c5c5d7] rounded-lg text-sm bg-white cursor-pointer focus:outline-none focus:border-[#3c50e0]">
                                    <option value="baru">Baru</option>
                                    <option value="diproses">Diproses</option>
                                    <option value="selesai">Selesai</option>
                                    <option value="ditolak">Ditolak</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[13px] font-medium text-on-surface mb-1.5">Catatan Admin <span class="text-[12px] font-normal text-[#757686]">(opsional)</span></label>
                                <textarea wire:model="editCatatan" rows="3" placeholder="Tindak lanjut / catatan…"
                                          class="w-full px-3 py-2 border border-[#c5c5d7] rounded-lg text-sm resize-y focus:outline-none focus:border-[#3c50e0]"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2 pt-5 mt-2 border-t border-[#c5c5d7]">
                        <button wire:click="closeDetail" class="w-full sm:w-auto px-4 py-2 text-sm rounded-lg border border-[#c5c5d7] text-[#505f76] hover:bg-[#f0f4f8] cursor-pointer text-center">Tutup</button>
                        <button wire:click="saveStatus" class="w-full sm:w-auto px-4 py-2 text-sm rounded-lg bg-[#3c50e0] text-white hover:bg-[#2a3db0] font-medium cursor-pointer text-center">Simpan</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ── Modal hapus ─────────────────────────────────────────────────────── --}}
    @if ($confirmDeleteId)
        <div class="fixed inset-0 z-[60] flex items-end sm:items-center justify-center bg-black/40 backdrop-blur-sm p-0 sm:p-4">
            <div class="bg-white rounded-t-2xl sm:rounded-xl shadow-xl w-full max-w-4xl sm:max-w-sm p-6">
                <h4 class="text-[16px] font-semibold text-on-surface mb-1">Hapus laporan ini?</h4>
                <p class="text-[13px] text-[#757686] mb-4">Laporan & lampirannya akan dihapus permanen.</p>
                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2">
                    <button wire:click="$set('confirmDeleteId', null)" class="w-full sm:w-auto px-4 py-2 text-sm rounded-lg border border-[#c5c5d7] text-[#505f76] hover:bg-[#f0f4f8] cursor-pointer text-center">Batal</button>
                    <button wire:click="delete" class="w-full sm:w-auto px-4 py-2 text-sm rounded-lg bg-[#ba1a1a] text-white hover:bg-[#93000a] font-medium cursor-pointer text-center">Ya, Hapus</button>
                </div>
            </div>
        </div>
    @endif

</div>
