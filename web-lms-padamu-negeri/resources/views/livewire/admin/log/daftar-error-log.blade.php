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
                                <div class="inline-flex items-center gap-1">
                                    <button wire:click="showDetail({{ $log->id }})"
                                            class="p-1.5 text-[#505f76] hover:text-[#3c50e0] hover:bg-[#EEF2FF] rounded-lg transition-colors cursor-pointer" title="Lihat Detail">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    </button>
                                    <button wire:click="$set('confirmDeleteId', {{ $log->id }})"
                                            class="p-1.5 text-[#505f76] hover:text-[#ba1a1a] hover:bg-[#ffdad6] rounded-lg transition-colors cursor-pointer" title="Hapus">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                </div>
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

    {{-- ── Modal Detail Error ──────────────────────────────────────────────── --}}
    @if ($detail)
        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/50 backdrop-blur-sm p-0 sm:p-4"
             wire:key="detail-{{ $detail->id }}">
            <div class="bg-white rounded-t-2xl sm:rounded-xl shadow-xl w-full sm:max-w-2xl flex flex-col max-h-[88vh]">
                {{-- Header --}}
                <div class="flex items-start justify-between gap-3 px-5 py-4 border-b border-[#c5c5d7] flex-shrink-0">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[20px] text-[#ba1a1a]">bug_report</span>
                            <h4 class="text-[15px] font-semibold text-on-surface truncate">{{ $detail->aksi }}</h4>
                        </div>
                        <p class="text-[12px] text-[#757686] mt-0.5">{{ $detail->created_at->format('d/m/Y H:i:s') }}</p>
                    </div>
                    <button wire:click="closeDetail" class="p-1.5 -mr-1.5 text-[#505f76] hover:bg-[#f0f4f8] rounded-lg cursor-pointer flex-shrink-0" title="Tutup">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>

                {{-- Body (scrollable) --}}
                <div class="px-5 py-4 overflow-y-auto space-y-4">
                    {{-- Meta --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-[#9da4b0] mb-0.5">Pengguna</p>
                            <p class="text-[13px] text-on-surface">{{ $detail->user?->username ?? '—' }}</p>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-[#9da4b0] mb-0.5">URL</p>
                            <p class="text-[13px] text-on-surface break-all">{{ $detail->url ?? '—' }}</p>
                        </div>
                        @if (! empty($detail->context['exception']))
                            <div class="min-w-0">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-[#9da4b0] mb-0.5">Tipe Exception</p>
                                <p class="text-[13px] text-on-surface break-all">{{ $detail->context['exception'] }}</p>
                            </div>
                        @endif
                        @if (! empty($detail->context['lokasi']))
                            <div class="min-w-0">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-[#9da4b0] mb-0.5">Lokasi</p>
                                <p class="text-[13px] text-on-surface break-all">{{ $detail->context['lokasi'] }}</p>
                            </div>
                        @endif
                    </div>

                    {{-- Pesan error --}}
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-[#9da4b0] mb-1">Pesan Error</p>
                        <div class="bg-[#fff9f9] border border-[#ffdad6] rounded-lg p-3 max-h-48 overflow-y-auto">
                            <p class="text-[13px] text-[#93000a] whitespace-pre-wrap break-words leading-relaxed">{{ $detail->pesan_error }}</p>
                        </div>
                    </div>

                    {{-- Trace / context tambahan --}}
                    @php
                        $extra = collect($detail->context ?? [])->except(['exception', 'lokasi', 'trace']);
                    @endphp
                    @if (! empty($detail->context['trace']))
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-[#9da4b0] mb-1">Stack Trace (ringkas)</p>
                            <div class="bg-[#0f172a] rounded-lg p-3 max-h-56 overflow-auto">
                                <pre class="text-[11.5px] text-[#cbd5e1] leading-relaxed whitespace-pre">{{ $detail->context['trace'] }}</pre>
                            </div>
                        </div>
                    @endif
                    @if ($extra->isNotEmpty())
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-[#9da4b0] mb-1">Context</p>
                            <div class="bg-[#f6fafe] border border-[#c5c5d7] rounded-lg p-3 max-h-48 overflow-auto">
                                <pre class="text-[11.5px] text-[#505f76] leading-relaxed whitespace-pre-wrap break-words">{{ json_encode($extra, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Footer --}}
                <div class="px-5 py-3.5 border-t border-[#c5c5d7] flex justify-end flex-shrink-0">
                    <button wire:click="closeDetail" class="px-4 py-2 text-sm rounded-lg border border-[#c5c5d7] text-[#505f76] hover:bg-[#f0f4f8] cursor-pointer">Tutup</button>
                </div>
            </div>
        </div>
    @endif

</div>
