<div>

    {{-- ── Header ─────────────────────────────────────────────────────────── --}}
    <div class="mb-6">
        <h2 class="text-[24px] font-bold tracking-tight text-on-surface">Absensi</h2>
        <p class="text-[14px] text-[#505f76] mt-0.5">Kelola sesi absensi untuk kelas yang Anda ampu.</p>
    </div>

    {{-- ── Pilih Mapel-Rombel ───────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm p-5 mb-5">
        <label class="block text-[13px] font-semibold text-[#505f76] uppercase tracking-wide mb-2">
            Pilih Mapel & Rombel
        </label>
        @if ($gmrs->isEmpty())
            <p class="text-[14px] text-[#757686] italic">Anda belum memiliki pemetaan mengajar pada periode aktif.</p>
        @else
            <select wire:model.live="selectedGmrId"
                    class="w-full sm:max-w-md border border-[#c5c5d7] rounded-lg px-4 py-2.5 text-[14px] text-on-surface bg-white focus:outline-none focus:ring-1 focus:ring-[#3c50e0] cursor-pointer">
                <option value="">-- Pilih Mapel & Rombel --</option>
                @foreach ($gmrs as $gmr)
                    <option value="{{ $gmr->id }}">{{ $gmr->mapel->nama }} — {{ $gmr->rombel->nama }}</option>
                @endforeach
            </select>
        @endif
    </div>

    @if ($selectedGmrId)

        @if (! $sesi)
            {{-- ── Belum ada sesi hari ini ─────────────────────────────────── --}}
            <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm px-6 py-16 text-center">
                <div class="w-16 h-16 rounded-full bg-[#EEF2FF] flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-[32px] text-[#3c50e0]">event_available</span>
                </div>
                <p class="text-[16px] font-semibold text-on-surface mb-1">Belum ada sesi absensi hari ini</p>
                <p class="text-[14px] text-[#505f76] mb-6">Buka sesi untuk memulai pencatatan kehadiran peserta didik.</p>
                <button wire:click="openSession"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-lg bg-[#3c50e0] text-white text-[14px] font-medium hover:bg-[#1c33c8] transition-colors cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">play_circle</span>
                    Buka Sesi Absensi
                </button>
            </div>

        @else
            {{-- ── Sesi aktif ──────────────────────────────────────────────── --}}

            {{-- Header sesi --}}
            <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm p-5 mb-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    @if ($sesi->status_sesi === 'terbuka')
                        <span class="flex items-center gap-1.5 px-3 py-1 rounded-full text-[12px] font-semibold bg-green-100 text-green-800 border border-green-200">
                            <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                            Sesi Terbuka
                        </span>
                    @else
                        <span class="flex items-center gap-1.5 px-3 py-1 rounded-full text-[12px] font-semibold bg-[#f0f4f8] text-[#505f76] border border-[#c5c5d7]">
                            <span class="material-symbols-outlined text-[14px]">lock</span>
                            Sesi Ditutup
                        </span>
                    @endif
                    <div>
                        <p class="text-[15px] font-semibold text-on-surface">{{ $sesi->guruMapelRombel->mapel->nama }}</p>
                        <p class="text-[13px] text-[#505f76]">{{ $sesi->guruMapelRombel->rombel->nama }} · {{ $sesi->tanggal->translatedFormat('l, d F Y') }}</p>
                    </div>
                </div>
                @if ($sesi->status_sesi === 'terbuka')
                    <button wire:click="closeSession"
                            wire:confirm="Tutup sesi absensi? Peserta didik tidak dapat absen mandiri setelah sesi ditutup."
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-[#ba1a1a] text-[#ba1a1a] text-[14px] font-medium hover:bg-[#ffdad6] transition-colors cursor-pointer">
                        <span class="material-symbols-outlined text-[18px]">stop_circle</span>
                        Tutup Sesi
                    </button>
                @endif
            </div>

            {{-- Stats ─ dengan polling realtime --}}
            <div wire:poll.5000ms class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-5">
                @php
                    $statCards = [
                        ['label' => 'Total',      'value' => $stats['total'], 'bg' => 'bg-white',        'text' => 'text-on-surface',   'border' => 'border-[#c5c5d7]'],
                        ['label' => 'Hadir',      'value' => $stats['hadir'], 'bg' => 'bg-green-50',     'text' => 'text-green-700',    'border' => 'border-green-200'],
                        ['label' => 'Izin',       'value' => $stats['izin'],  'bg' => 'bg-amber-50',     'text' => 'text-amber-700',    'border' => 'border-amber-200'],
                        ['label' => 'Sakit',      'value' => $stats['sakit'], 'bg' => 'bg-blue-50',      'text' => 'text-blue-700',     'border' => 'border-blue-200'],
                        ['label' => 'Alpa',       'value' => $stats['alpa'],  'bg' => 'bg-[#ffdad6]',    'text' => 'text-[#93000a]',    'border' => 'border-[#ba1a1a]/20'],
                        ['label' => 'Belum Absen','value' => $stats['belum'], 'bg' => 'bg-[#f0f4f8]',    'text' => 'text-[#505f76]',    'border' => 'border-[#c5c5d7]'],
                    ];
                @endphp
                @foreach ($statCards as $card)
                    <div class="{{ $card['bg'] }} {{ $card['border'] }} border rounded-xl p-4 text-center shadow-sm">
                        <p class="text-[24px] font-bold {{ $card['text'] }}">{{ $card['value'] }}</p>
                        <p class="text-[11px] uppercase tracking-wider {{ $card['text'] }} opacity-70 mt-0.5">{{ $card['label'] }}</p>
                    </div>
                @endforeach
            </div>

            {{-- Roster tabel --}}
            <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm overflow-hidden">
                {{-- Toolbar --}}
                <div class="p-4 border-b border-[#c5c5d7] flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between">
                    <p class="text-[14px] font-semibold text-on-surface">Daftar Peserta Didik</p>
                    <div class="flex items-center gap-2">
                        <span class="text-[13px] text-[#505f76]">Filter:</span>
                        <select wire:model.live="filterStatus"
                                class="border border-[#c5c5d7] rounded-lg px-3 py-1.5 text-[13px] bg-white cursor-pointer focus:outline-none focus:ring-1 focus:ring-[#3c50e0]">
                            <option value="">Semua Status</option>
                            <option value="hadir">Hadir</option>
                            <option value="izin">Izin</option>
                            <option value="sakit">Sakit</option>
                            <option value="alpa">Alpa</option>
                            <option value="belum">Belum Absen</option>
                        </select>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-[14px]">
                        <thead class="bg-[#f8f9fb] border-b border-[#c5c5d7]">
                            <tr>
                                <th class="px-4 py-3 text-left text-[12px] font-semibold text-[#505f76] uppercase tracking-wide w-12">No</th>
                                <th class="px-4 py-3 text-left text-[12px] font-semibold text-[#505f76] uppercase tracking-wide">Nama Peserta Didik</th>
                                <th class="px-4 py-3 text-left text-[12px] font-semibold text-[#505f76] uppercase tracking-wide w-36">Status</th>
                                <th class="px-4 py-3 text-left text-[12px] font-semibold text-[#505f76] uppercase tracking-wide w-36">Waktu / Sumber</th>
                                <th class="px-4 py-3 text-center text-[12px] font-semibold text-[#505f76] uppercase tracking-wide w-44">
                                    {{ $sesi->status_sesi === 'terbuka' ? 'Override Status' : '' }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#e8e8f0]">
                            @forelse ($roster as $i => $pd)
                                @php $detail = $details[$pd->id] ?? null; @endphp
                                <tr class="hover:bg-[#f8f9fb] transition-colors {{ $detail && $detail->status === 'hadir' && !$detail->diubah_manual_oleh ? 'bg-green-50/40' : '' }}">
                                    <td class="px-4 py-3 text-[#757686]">{{ $i + 1 }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-full bg-[#EEF2FF] flex items-center justify-center flex-shrink-0">
                                                <span class="text-[11px] font-bold text-[#3c50e0]">
                                                    {{ strtoupper(substr($pd->nama_lengkap, 0, 2)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <p class="font-medium text-on-surface">{{ $pd->nama_lengkap }}</p>
                                                <p class="text-[12px] text-[#757686]">NIPD: {{ $pd->nipd }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if (! $detail)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-[#f0f4f8] text-[#505f76] border border-[#c5c5d7]">
                                                Belum Absen
                                            </span>
                                        @elseif ($detail->status === 'hadir')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-green-100 text-green-800 border border-green-200">Hadir</span>
                                        @elseif ($detail->status === 'izin')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-amber-100 text-amber-800 border border-amber-200">Izin</span>
                                        @elseif ($detail->status === 'sakit')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-blue-100 text-blue-800 border border-blue-200">Sakit</span>
                                        @elseif ($detail->status === 'alpa')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-[#ffdad6] text-[#93000a] border border-[#ba1a1a]/20">Alpa</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-[12px] text-[#757686]">
                                        @if ($detail)
                                            @if ($detail->waktu_klik)
                                                {{ $detail->waktu_klik->format('H:i') }} WIB
                                            @else
                                                <span class="text-[#3c50e0]" title="Diubah manual oleh guru">Manual</span>
                                            @endif
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($sesi->status_sesi === 'terbuka')
                                            <div class="flex justify-center gap-1">
                                                <button wire:click="setStatus({{ $pd->id }}, 'hadir')"
                                                        class="p-1.5 rounded transition-colors cursor-pointer {{ ($detail->status ?? '') === 'hadir' ? 'text-green-600 bg-green-100' : 'text-[#757686] hover:text-green-600 hover:bg-green-50' }}"
                                                        title="Hadir">
                                                    <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' {{ ($detail->status ?? '') === 'hadir' ? 1 : 0 }}">check_circle</span>
                                                </button>
                                                <button wire:click="setStatus({{ $pd->id }}, 'izin')"
                                                        class="p-1.5 rounded transition-colors cursor-pointer {{ ($detail->status ?? '') === 'izin' ? 'text-amber-600 bg-amber-100' : 'text-[#757686] hover:text-amber-600 hover:bg-amber-50' }}"
                                                        title="Izin">
                                                    <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' {{ ($detail->status ?? '') === 'izin' ? 1 : 0 }}">info</span>
                                                </button>
                                                <button wire:click="setStatus({{ $pd->id }}, 'sakit')"
                                                        class="p-1.5 rounded transition-colors cursor-pointer {{ ($detail->status ?? '') === 'sakit' ? 'text-blue-600 bg-blue-100' : 'text-[#757686] hover:text-blue-600 hover:bg-blue-50' }}"
                                                        title="Sakit">
                                                    <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' {{ ($detail->status ?? '') === 'sakit' ? 1 : 0 }}">local_hospital</span>
                                                </button>
                                                <button wire:click="setStatus({{ $pd->id }}, 'alpa')"
                                                        class="p-1.5 rounded transition-colors cursor-pointer {{ ($detail->status ?? '') === 'alpa' ? 'text-[#ba1a1a] bg-[#ffdad6]' : 'text-[#757686] hover:text-[#ba1a1a] hover:bg-[#ffdad6]' }}"
                                                        title="Alpa">
                                                    <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' {{ ($detail->status ?? '') === 'alpa' ? 1 : 0 }}">cancel</span>
                                                </button>
                                            </div>
                                        @else
                                            <p class="text-center text-[12px] text-[#c5c5d7]">—</p>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-12 text-center text-[14px] text-[#757686]">
                                        @if ($filterStatus)
                                            Tidak ada peserta didik dengan status "{{ $filterStatus }}".
                                        @else
                                            Belum ada peserta didik di rombel ini.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @endif

</div>
