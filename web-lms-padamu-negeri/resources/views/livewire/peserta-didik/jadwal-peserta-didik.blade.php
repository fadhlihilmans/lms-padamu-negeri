<div>

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <div class="mb-6">
        <h2 class="text-[24px] font-bold tracking-tight text-on-surface">Jadwal Pelajaran</h2>
        <p class="text-[14px] text-[#505f76] mt-0.5">{{ $pd?->nama_lengkap ?? 'Peserta Didik' }}</p>
    </div>

    @if ($rombels->isEmpty())
        {{-- Belum terdaftar di rombel apapun --}}
        <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm px-6 py-20 text-center">
            <span class="material-symbols-outlined text-[56px] text-[#c5c5d7] mb-3 block">groups_off</span>
            <p class="text-[16px] font-semibold text-on-surface">Belum terdaftar di rombel</p>
            <p class="text-[14px] text-[#505f76] mt-1">Hubungi Admin untuk mendaftarkan Anda ke dalam rombel.</p>
        </div>
    @else

        {{-- Pilih Rombel (jika lebih dari satu) --}}
        @if ($rombels->count() > 1)
            <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm p-5 mb-6">
                <p class="text-[13px] font-semibold text-[#505f76] uppercase tracking-wide mb-3">Pilih Rombel</p>
                <select wire:model.live="filterRombelId"
                        class="w-full sm:max-w-sm border border-[#c5c5d7] rounded-lg px-4 py-2.5 text-[14px] text-on-surface bg-white focus:outline-none focus:ring-1 focus:ring-[#3c50e0] cursor-pointer transition-shadow">
                    @foreach ($rombels as $r)
                        <option value="{{ $r->id }}">{{ $r->nama }}</option>
                    @endforeach
                </select>
            </div>
        @else
            <div class="flex items-center gap-2 mb-4">
                <span class="material-symbols-outlined text-[18px] text-[#3c50e0]">groups</span>
                <p class="text-[14px] font-semibold text-on-surface">{{ $rombelSelected?->nama }}</p>
            </div>
        @endif

        {{-- Jadwal per hari --}}
        @if ($jadwalByHari->isEmpty())
            <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm px-6 py-16 text-center">
                <span class="material-symbols-outlined text-[48px] text-[#c5c5d7] mb-3 block">calendar_today</span>
                <p class="text-[14px] text-[#505f76]">Belum ada jadwal untuk rombel ini.</p>
            </div>
        @else
            <div class="flex flex-col gap-4">
                @foreach ($hariOrder as $h)
                    @if ($jadwalByHari->has($h))
                        <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm overflow-hidden">
                            <div class="px-5 py-3 bg-[#EEF2FF] border-b border-[#c5d0ff] flex items-center justify-between">
                                <p class="text-[14px] font-bold text-[#1c33c8] uppercase tracking-wide">{{ $hariLabel[$h] }}</p>
                                <span class="text-[12px] text-[#3c50e0]">{{ $jadwalByHari[$h]->count() }} sesi</span>
                            </div>
                            <div class="divide-y divide-[#c5c5d7]">
                                @foreach ($jadwalByHari[$h] as $jadwal)
                                    <div class="flex items-center gap-5 px-5 py-3.5">
                                        <span class="font-mono text-[13px] text-[#1c33c8] font-semibold w-28 flex-shrink-0">
                                            {{ substr($jadwal->jam_mulai, 0, 5) }} – {{ substr($jadwal->jam_selesai, 0, 5) }}
                                        </span>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-[14px] font-medium text-on-surface truncate">{{ $jadwal->guruMapelRombel?->mapel?->nama ?? '—' }}</p>
                                            <p class="text-[12px] text-[#505f76]">{{ $jadwal->guruMapelRombel?->guru?->nama_lengkap ?? '—' }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    @endif

</div>
