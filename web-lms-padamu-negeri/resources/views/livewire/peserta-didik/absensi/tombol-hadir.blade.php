<div>

    {{-- ── Header ─────────────────────────────────────────────────────────── --}}
    <div class="mb-6">
        <h2 class="text-[24px] font-bold tracking-tight text-on-surface">Absensi</h2>
        <p class="text-[14px] text-[#505f76] mt-0.5">{{ now()->translatedFormat('l, d F Y') }}</p>
    </div>

    @if (! $pd)
        <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm px-6 py-16 text-center">
            <p class="text-[14px] text-[#757686]">Data peserta didik tidak ditemukan.</p>
        </div>
    @elseif ($sesiTerbuka->isEmpty())
        {{-- Tidak ada sesi terbuka --}}
        <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm px-6 py-16 text-center">
            <div class="w-16 h-16 rounded-full bg-[#f0f4f8] flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-[32px] text-[#757686]">event_busy</span>
            </div>
            <p class="text-[16px] font-semibold text-on-surface">Tidak ada sesi absensi yang terbuka</p>
            <p class="text-[14px] text-[#505f76] mt-1">Tunggu guru membuka sesi absensi untuk memulai.</p>
        </div>
    @else
        {{-- Sesi-sesi terbuka --}}
        <div class="flex flex-col gap-5">
            @foreach ($sesiTerbuka as $sesi)
                @php $sudahAbsen = isset($detailHariIni[$sesi->id]); @endphp
                <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm overflow-hidden">
                    {{-- Info sesi --}}
                    <div class="px-6 py-4 bg-[#EEF2FF] border-b border-[#c5d0ff] flex items-center gap-3">
                        <span class="material-symbols-outlined text-[20px] text-[#3c50e0]">class</span>
                        <div>
                            <p class="text-[15px] font-semibold text-[#1c33c8]">{{ $sesi->guruMapelRombel->mapel->nama }}</p>
                            <p class="text-[12px] text-[#3c50e0]">Pengajar: {{ $sesi->guruMapelRombel->guru->nama_lengkap }}</p>
                        </div>
                        <span class="ml-auto flex items-center gap-1 text-[11px] font-semibold text-green-700 bg-green-100 border border-green-200 px-2 py-0.5 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                            Terbuka
                        </span>
                    </div>

                    <div class="p-6">
                        @if ($sudahAbsen)
                            {{-- Sudah absen --}}
                            @php $d = $detailHariIni[$sesi->id]; @endphp
                            <div class="text-center py-6">
                                @php
                                    $statusConfig = [
                                        'hadir' => ['icon' => 'check_circle', 'color' => 'text-green-600', 'bg' => 'bg-green-100', 'label' => 'Hadir'],
                                        'izin'  => ['icon' => 'info',         'color' => 'text-amber-600', 'bg' => 'bg-amber-100', 'label' => 'Izin'],
                                        'sakit' => ['icon' => 'local_hospital','color' => 'text-blue-600',  'bg' => 'bg-blue-100',  'label' => 'Sakit'],
                                        'alpa'  => ['icon' => 'cancel',       'color' => 'text-[#ba1a1a]', 'bg' => 'bg-[#ffdad6]', 'label' => 'Alpa'],
                                    ];
                                    $sc = $statusConfig[$d->status] ?? $statusConfig['alpa'];
                                @endphp
                                <div class="w-16 h-16 rounded-full {{ $sc['bg'] }} flex items-center justify-center mx-auto mb-3">
                                    <span class="material-symbols-outlined text-[36px] {{ $sc['color'] }}" style="font-variation-settings:'FILL' 1;">{{ $sc['icon'] }}</span>
                                </div>
                                <p class="text-[20px] font-bold {{ $sc['color'] }}">{{ $sc['label'] }}</p>
                                <p class="text-[13px] text-[#757686] mt-1">
                                    @if ($d->waktu_klik)
                                        Dicatat pada {{ $d->waktu_klik->format('H:i') }} WIB
                                    @else
                                        Diubah manual oleh guru
                                    @endif
                                </p>
                            </div>
                        @else
                            {{-- Belum absen — tampilkan tombol besar --}}
                            <p class="text-center text-[13px] font-semibold text-[#505f76] uppercase tracking-wider mb-5">Pilih Status Kehadiran</p>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                {{-- Hadir --}}
                                <button wire:click="absen({{ $sesi->id }}, 'hadir')"
                                        wire:loading.attr="disabled"
                                        class="group flex flex-col items-center justify-center p-8 rounded-xl border-2 border-green-400 bg-green-50 hover:bg-green-100 transition-all active:scale-95 cursor-pointer h-44">
                                    <div class="w-14 h-14 rounded-full bg-green-500 flex items-center justify-center text-white mb-3 group-hover:scale-110 transition-transform">
                                        <span class="material-symbols-outlined text-[32px]" style="font-variation-settings:'FILL' 1;">check_circle</span>
                                    </div>
                                    <span class="text-[18px] font-bold text-green-800">Hadir</span>
                                </button>
                                {{-- Izin --}}
                                <button wire:click="absen({{ $sesi->id }}, 'izin')"
                                        wire:loading.attr="disabled"
                                        class="group flex flex-col items-center justify-center p-8 rounded-xl border-2 border-amber-400 bg-amber-50 hover:bg-amber-100 transition-all active:scale-95 cursor-pointer h-44">
                                    <div class="w-14 h-14 rounded-full bg-amber-500 flex items-center justify-center text-white mb-3 group-hover:scale-110 transition-transform">
                                        <span class="material-symbols-outlined text-[32px]" style="font-variation-settings:'FILL' 1;">info</span>
                                    </div>
                                    <span class="text-[18px] font-bold text-amber-800">Izin</span>
                                </button>
                                {{-- Sakit --}}
                                <button wire:click="absen({{ $sesi->id }}, 'sakit')"
                                        wire:loading.attr="disabled"
                                        class="group flex flex-col items-center justify-center p-8 rounded-xl border-2 border-blue-400 bg-blue-50 hover:bg-blue-100 transition-all active:scale-95 cursor-pointer h-44">
                                    <div class="w-14 h-14 rounded-full bg-blue-500 flex items-center justify-center text-white mb-3 group-hover:scale-110 transition-transform">
                                        <span class="material-symbols-outlined text-[32px]" style="font-variation-settings:'FILL' 1;">local_hospital</span>
                                    </div>
                                    <span class="text-[18px] font-bold text-blue-800">Sakit</span>
                                </button>
                            </div>
                            <p class="text-center text-[12px] text-[#757686] mt-4">
                                Status yang dipilih tidak dapat diubah sendiri. Hubungi guru jika terjadi kesalahan.
                            </p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- ── Riwayat 7 Hari Terakhir ─────────────────────────────────────── --}}
    @if ($pd)
        <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm p-5 mt-6">
            <h3 class="text-[15px] font-semibold text-on-surface mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px] text-[#3c50e0]">history</span>
                Riwayat 7 Hari Terakhir
            </h3>
            <div class="flex flex-wrap gap-2">
                @for ($i = 6; $i >= 0; $i--)
                    @php
                        $day = today()->subDays($i);
                        $key = $day->format('Y-m-d');
                        $dayDetails = $riwayatMinggu->get($key, collect());
                        // Jika ada hadir dalam hari itu, tampilkan hadir; else ambil status pertama
                        $dayStatus = null;
                        if ($dayDetails->isNotEmpty()) {
                            $dayStatus = $dayDetails->firstWhere('status', 'hadir') ? 'hadir'
                                : $dayDetails->first()->status;
                        }
                        $dayLabel = $day->translatedFormat('D');
                        $dateLabel = $day->format('d/m');
                        $isToday = $day->isToday();
                        $colors = [
                            'hadir' => 'bg-green-50 border-green-400 text-green-800',
                            'izin'  => 'bg-amber-50 border-amber-400 text-amber-800',
                            'sakit' => 'bg-blue-50 border-blue-400 text-blue-800',
                            'alpa'  => 'bg-[#ffdad6] border-[#ba1a1a]/40 text-[#93000a]',
                        ];
                        $dayColor = $dayStatus ? ($colors[$dayStatus] ?? 'bg-[#f0f4f8] border-[#c5c5d7] text-[#505f76]') : 'bg-[#f0f4f8] border-dashed border-[#c5c5d7] text-[#757686] opacity-60';
                    @endphp
                    <div class="flex-1 min-w-[80px] max-w-[110px] border rounded-xl p-3 text-center {{ $dayColor }} {{ $isToday ? 'ring-2 ring-[#3c50e0]' : '' }}">
                        <p class="text-[11px] font-semibold uppercase tracking-wider">{{ $dayLabel }}</p>
                        <p class="text-[10px] opacity-70">{{ $dateLabel }}</p>
                        <p class="text-[13px] font-bold mt-1">
                            {{ $dayStatus ? ucfirst($dayStatus) : '—' }}
                        </p>
                        @if ($isToday)
                            <p class="text-[9px] font-semibold text-[#3c50e0] mt-0.5">Hari ini</p>
                        @endif
                    </div>
                @endfor
            </div>
        </div>
    @endif

</div>
