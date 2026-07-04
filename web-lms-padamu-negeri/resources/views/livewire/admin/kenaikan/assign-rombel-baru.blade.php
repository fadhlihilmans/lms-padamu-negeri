<div class="max-w-6xl mx-auto space-y-4">

    @php
        $statusMeta = [
            'naik'           => ['Naik Tingkat',   'bg-blue-50 text-blue-700 border-blue-200'],
            'tinggal'        => ['Tinggal',        'bg-amber-50 text-amber-700 border-amber-200'],
            'pindah_paket'   => ['Pindah Paket',   'bg-purple-50 text-purple-700 border-purple-200'],
            'pindah_wilayah' => ['Pindah Wilayah', 'bg-purple-50 text-purple-700 border-purple-200'],
        ];
    @endphp

    {{-- ── Heading ─────────────────────────────────────────────────────────── --}}
    <div>
        <h1 class="text-xl font-bold text-on-surface">Assign Rombel Baru</h1>
        <p class="text-sm text-[#505f76] mt-0.5">Pilih peserta didik dari ruang tunggu, lalu tentukan rombel tujuan untuk tahun ajaran berjalan.</p>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">

        {{-- ══ Panel kiri: daftar tunggu ══════════════════════════════════════ --}}
        <div class="xl:col-span-2 bg-white border border-[#c5c5d7] rounded-xl overflow-hidden flex flex-col" style="max-height:600px">
            <div class="px-4 py-3 border-b border-[#c5c5d7] flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <p class="text-sm font-semibold text-on-surface">Daftar Tunggu Peserta Didik</p>
                    <p class="text-xs text-[#757686]">Terpilih: <strong class="text-[#3c50e0]">{{ count($selected) }}</strong> dari {{ $totalTunggu }} peserta didik</p>
                </div>
                <div class="relative w-full sm:w-auto">
                    <span class="material-symbols-outlined absolute left-2 top-1/2 -translate-y-1/2 text-[#757686] text-[18px] pointer-events-none">search</span>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama…"
                           class="w-full sm:w-52 pl-9 pr-3 py-2 border border-[#c5c5d7] rounded-lg text-sm bg-white focus:outline-none focus:border-[#3c50e0] focus:ring-1 focus:ring-[#3c50e0]">
                </div>
            </div>

            <div class="flex-1 overflow-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-white border-b border-[#c5c5d7] sticky top-0 z-10">
                            <th class="px-4 py-2.5 w-10"></th>
                            <th class="px-4 py-2.5 text-xs font-semibold text-[#505f76] uppercase tracking-wide">Nama Peserta Didik</th>
                            <th class="px-4 py-2.5 text-xs font-semibold text-[#505f76] uppercase tracking-wide">Status</th>
                            <th class="px-4 py-2.5 text-xs font-semibold text-[#505f76] uppercase tracking-wide">Rombel Asal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#c5c5d7]">
                        @forelse ($waiting as $k)
                            @php
                                $pd     = $k->pesertaDidik;
                                $meta   = $statusMeta[$k->status_keputusan] ?? [$k->status_keputusan, 'bg-[#f0f4f8] text-[#757686] border-[#c5c5d7]'];
                                $isSel  = in_array((string) $k->id, $selected, true) || in_array($k->id, $selected);
                                $ini    = strtoupper(\Illuminate\Support\Str::of($pd->nama_lengkap)->explode(' ')->take(2)->map(fn($w) => mb_substr($w, 0, 1))->join(''));
                            @endphp
                            <tr class="transition-colors {{ $isSel ? 'bg-[#EEF2FF] hover:bg-[#e6eeff]' : 'hover:bg-[#f6fafe]' }}">
                                <td class="px-4 py-3 text-center">
                                    <input type="checkbox" wire:model.live="selected" value="{{ $k->id }}"
                                           class="rounded border-[#c5c5d7] text-[#3c50e0] cursor-pointer">
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0 {{ $isSel ? 'bg-[#3c50e0] text-white' : 'bg-[#e4e9ed] text-[#757686]' }}">{{ $ini }}</div>
                                        <div>
                                            <p class="text-sm font-medium text-on-surface">{{ $pd->nama_lengkap }}</p>
                                            <p class="text-xs text-[#757686]">NIPD: {{ $pd->nipd }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="text-xs px-2 py-0.5 rounded-full border font-medium {{ $meta[1] }}">{{ $meta[0] }}</span>
                                </td>
                                <td class="px-4 py-3 text-xs text-[#757686] whitespace-nowrap">{{ $k->rombelAsal?->nama ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-16 text-center">
                                    <span class="material-symbols-outlined text-[48px] text-[#c5c5d7] mb-3 block">inbox</span>
                                    <p class="text-[15px] font-medium text-on-surface mb-1">
                                        {{ $search ? 'Tidak ada yang cocok' : 'Ruang tunggu kosong' }}
                                    </p>
                                    <p class="text-[13px] text-[#757686]">
                                        {{ $search ? 'Coba kata kunci lain.' : 'Peserta didik muncul di sini setelah Wali Kelas memutuskan Naik/Tinggal/Pindah.' }}
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ══ Panel kanan: ringkasan + form ══════════════════════════════════ --}}
        <div class="flex flex-col gap-4">
            {{-- Ringkasan terpilih --}}
            <div class="bg-[#EEF2FF] border border-[#3c50e0]/20 rounded-xl px-4 py-4">
                <p class="text-xs font-semibold text-[#505f76] uppercase tracking-wide mb-3">Peserta Didik Terpilih ({{ $selectedRecords->count() }})</p>
                @forelse ($selectedRecords as $rec)
                    <div class="flex items-center gap-2 mb-2 last:mb-0">
                        <div class="w-7 h-7 rounded-full bg-[#3c50e0] text-white flex items-center justify-center text-xs font-bold flex-shrink-0">
                            {{ strtoupper(\Illuminate\Support\Str::of($rec->pesertaDidik?->nama_lengkap ?? '?')->explode(' ')->take(2)->map(fn($w) => mb_substr($w, 0, 1))->join('')) }}
                        </div>
                        <span class="text-sm font-medium text-on-surface truncate">{{ $rec->pesertaDidik?->nama_lengkap ?? '—' }}</span>
                    </div>
                @empty
                    <p class="text-xs text-[#505f76]">Belum ada peserta didik dipilih.</p>
                @endforelse
            </div>

            {{-- Form rombel tujuan --}}
            <div class="bg-white border border-[#c5c5d7] rounded-xl px-4 py-4 space-y-4">
                <p class="text-sm font-semibold text-on-surface">Pilih Rombel Tujuan</p>
                <div>
                    <label class="block text-xs font-medium text-[#505f76] mb-1">Tahun Ajaran</label>
                    <select wire:model.live="targetPeriodeId"
                            class="w-full px-3 py-2 border border-[#c5c5d7] rounded-lg text-sm bg-white cursor-pointer focus:outline-none focus:border-[#3c50e0] focus:ring-1 focus:ring-[#3c50e0]">
                        <option value="">Pilih tahun ajaran…</option>
                        @foreach ($periodes as $p)
                            <option value="{{ $p->id }}">{{ $p->tahun_ajaran }} — {{ ucfirst($p->semester) }}@if ($p->is_aktif) (Aktif) @endif</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-[#505f76] mb-1">Paket</label>
                    <select wire:model.live="targetPaketId" @disabled(! $targetPeriodeId)
                            class="w-full px-3 py-2 border border-[#c5c5d7] rounded-lg text-sm bg-white cursor-pointer focus:outline-none focus:border-[#3c50e0] focus:ring-1 focus:ring-[#3c50e0] disabled:bg-[#f0f4f8] disabled:cursor-not-allowed">
                        <option value="">Pilih paket…</option>
                        @foreach ($pakets as $p)
                            <option value="{{ $p->id }}">{{ $p->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-[#505f76] mb-1">Rombel Tujuan</label>
                    <select wire:model.live="targetRombelId" @disabled(! $targetPaketId)
                            class="w-full px-3 py-2 border border-[#c5c5d7] rounded-lg text-sm bg-white cursor-pointer focus:outline-none focus:border-[#3c50e0] focus:ring-1 focus:ring-[#3c50e0] disabled:bg-[#f0f4f8] disabled:cursor-not-allowed">
                        <option value="">Pilih rombel…</option>
                        @foreach ($rombelsTujuan as $r)
                            <option value="{{ $r->id }}">{{ $r->nama }}</option>
                        @endforeach
                    </select>
                    @if ($targetPaketId && $rombelsTujuan->isEmpty())
                        <p class="text-xs text-amber-600 mt-1">Belum ada rombel untuk paket & tahun ajaran ini. Buat rombel dulu di menu Rombel.</p>
                    @endif
                </div>

                @if ($targetRombel)
                    <div class="bg-[#f0f4f8] rounded-lg px-3 py-2 text-xs text-[#505f76]">
                        <span class="font-medium text-on-surface">{{ $targetRombel->nama }}</span> — anggota saat ini: <strong>{{ $targetRombel->peserta_didik_rombel_count }}</strong> peserta didik
                    </div>
                @endif

                <button wire:click="assign" wire:loading.attr="disabled"
                        @disabled(empty($selected) || ! $targetRombelId)
                        class="w-full py-2.5 bg-[#3c50e0] text-white rounded-lg text-sm font-semibold hover:bg-[#2a3db0] transition-colors flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading wire:target="assign" class="material-symbols-outlined text-[18px] animate-spin">progress_activity</span>
                    <span wire:loading.remove wire:target="assign" class="material-symbols-outlined text-[18px]">assignment_ind</span>
                    Assign ke Rombel
                </button>
            </div>
        </div>

    </div>
</div>
