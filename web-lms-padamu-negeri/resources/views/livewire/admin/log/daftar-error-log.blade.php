<div class="max-w-5xl mx-auto space-y-4">

    {{-- ── Heading ─────────────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-on-surface">Log Error</h1>
            <p class="text-sm text-[#505f76] mt-0.5">Error teknis yang tercatat otomatis oleh sistem.</p>
        </div>
        @if ($logs->total() > 0)
            <button wire:click="$set('showDeleteAll', true)"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-[#ffdad6] bg-[#fff9f9] text-[#ba1a1a] text-sm font-medium hover:bg-[#ffdad6] transition-colors cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">delete_sweep</span> Hapus Semua
            </button>
        @endif
    </div>

    {{-- ── Filter ──────────────────────────────────────────────────────────── --}}
    <div class="bg-white border border-[#c5c5d7] rounded-xl p-4 flex flex-col sm:flex-row gap-3">
        <div class="flex-1 relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#757686] text-[18px] pointer-events-none">search</span>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari aksi / pesan error…"
                   class="w-full pl-10 pr-4 py-2.5 border border-[#c5c5d7] rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-[#3c50e0]">
        </div>
        <input wire:model.live="tanggal" type="date"
               class="px-3 py-2.5 border border-[#c5c5d7] rounded-lg text-sm bg-white cursor-pointer focus:outline-none focus:ring-1 focus:ring-[#3c50e0]">
    </div>

    {{-- ── Tabel ───────────────────────────────────────────────────────────── --}}
    <div class="bg-white border border-[#c5c5d7] rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-white border-b border-[#c5c5d7]">
                        <th class="px-4 py-3 text-xs font-semibold text-[#505f76] uppercase tracking-wide">Waktu</th>
                        <th class="px-4 py-3 text-xs font-semibold text-[#505f76] uppercase tracking-wide">Aksi</th>
                        <th class="px-4 py-3 text-xs font-semibold text-[#505f76] uppercase tracking-wide">Pesan Error</th>
                        <th class="px-4 py-3 text-xs font-semibold text-[#505f76] uppercase tracking-wide">Pengguna</th>
                        <th class="px-4 py-3 text-xs font-semibold text-[#505f76] uppercase tracking-wide text-center w-16">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#c5c5d7]">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-[#f6fafe] transition-colors align-top">
                            <td class="px-4 py-3 text-xs text-[#757686] whitespace-nowrap">{{ $log->created_at->format('d/m/y H:i') }}</td>
                            <td class="px-4 py-3 whitespace-nowrap"><span class="font-medium text-on-surface">{{ $log->aksi }}</span></td>
                            <td class="px-4 py-3 text-[#505f76] max-w-md"><span class="line-clamp-2">{{ $log->pesan_error }}</span>
                                @if ($log->url) <p class="text-[11px] text-[#9da4b0] truncate mt-0.5">{{ $log->url }}</p> @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-[#505f76] whitespace-nowrap">{{ $log->user?->name ?? $log->user?->username ?? '—' }}</td>
                            <td class="px-4 py-3 text-center">
                                <button wire:click="$set('confirmDeleteId', {{ $log->id }})"
                                        class="p-1.5 text-[#505f76] hover:text-[#ba1a1a] hover:bg-[#ffdad6] rounded-lg transition-colors cursor-pointer" title="Hapus">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-16 text-center">
                                <span class="material-symbols-outlined text-[48px] text-[#c5c5d7] mb-3 block">task_alt</span>
                                <p class="text-[15px] font-medium text-on-surface mb-1">{{ $search || $tanggal ? 'Tidak ada log yang cocok' : 'Tidak ada error tercatat' }}</p>
                                <p class="text-[13px] text-[#757686]">{{ $search || $tanggal ? 'Coba ubah filter.' : 'Bagus! Sistem berjalan tanpa error.' }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($logs->hasPages())
            <div class="px-4 py-3 border-t border-[#c5c5d7] flex items-center justify-between text-sm text-[#505f76]">
                <span>Menampilkan {{ $logs->firstItem() ?? 0 }}–{{ $logs->lastItem() ?? 0 }} dari {{ $logs->total() }}</span>
                <div>{{ $logs->links() }}</div>
            </div>
        @endif
    </div>

    {{-- ── Modal hapus satu ────────────────────────────────────────────────── --}}
    @if ($confirmDeleteId)
        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/40 backdrop-blur-sm p-0 sm:p-4">
            <div class="bg-white rounded-t-2xl sm:rounded-xl shadow-xl w-full max-w-4xl sm:max-w-sm p-6">
                <h4 class="text-[16px] font-semibold text-on-surface mb-1">Hapus log ini?</h4>
                <p class="text-[13px] text-[#757686] mb-4">Log akan dihapus permanen.</p>
                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2">
                    <button wire:click="$set('confirmDeleteId', null)" class="w-full sm:w-auto px-4 py-2 text-sm rounded-lg border border-[#c5c5d7] text-[#505f76] hover:bg-[#f0f4f8] cursor-pointer text-center">Batal</button>
                    <button wire:click="delete" class="w-full sm:w-auto px-4 py-2 text-sm rounded-lg bg-[#ba1a1a] text-white hover:bg-[#93000a] font-medium cursor-pointer text-center">Ya, Hapus</button>
                </div>
            </div>
        </div>
    @endif

    {{-- ── Modal hapus semua ───────────────────────────────────────────────── --}}
    @if ($showDeleteAll)
        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/40 backdrop-blur-sm p-0 sm:p-4">
            <div class="bg-white rounded-t-2xl sm:rounded-xl shadow-xl w-full max-w-4xl sm:max-w-sm p-6">
                <div class="flex items-start gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-[#ffdad6] flex items-center justify-center flex-shrink-0"><span class="material-symbols-outlined text-[#ba1a1a]">delete_sweep</span></div>
                    <div>
                        <h4 class="text-[16px] font-semibold text-on-surface">Hapus semua log error?</h4>
                        <p class="text-[13px] text-[#757686] mt-1">Seluruh {{ $logs->total() }} log akan dihapus permanen dan tidak bisa dikembalikan.</p>
                    </div>
                </div>
                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2">
                    <button wire:click="$set('showDeleteAll', false)" class="w-full sm:w-auto px-4 py-2 text-sm rounded-lg border border-[#c5c5d7] text-[#505f76] hover:bg-[#f0f4f8] cursor-pointer text-center">Batal</button>
                    <button wire:click="deleteAll" class="w-full sm:w-auto px-4 py-2 text-sm rounded-lg bg-[#ba1a1a] text-white hover:bg-[#93000a] font-medium cursor-pointer text-center">Ya, Hapus Semua</button>
                </div>
            </div>
        </div>
    @endif

</div>
