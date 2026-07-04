<div>

    {{-- ── Header ─────────────────────────────────────────────────────────── --}}
    <div class="mb-6">
        <h2 class="text-[18px] font-bold text-on-surface">Absensi</h2>
        <p class="text-[13px] text-[#757686] mt-0.5">{{ now()->translatedFormat('l, d F Y') }}</p>
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
                    <div class="px-5 py-3.5 bg-white border-b border-[#d1d5db] flex items-center gap-3">
                        <span class="material-symbols-outlined text-[20px] text-[#3c50e0]">class</span>
                        <div class="min-w-0">
                            <p class="text-[14.5px] font-semibold text-[#1c33c8]">{{ $sesi->guruMapelRombel->mapel->nama }}</p>
                            <p class="text-[11.5px] text-[#3c50e0]">Pengajar: {{ $sesi->guruMapelRombel->guru->nama_lengkap }}</p>
                        </div>
                        <span class="ml-auto flex items-center gap-1 text-[11px] font-semibold text-green-700 bg-green-100 border border-green-200 px-2 py-0.5 rounded-full flex-shrink-0">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                            Terbuka
                        </span>
                    </div>

                    {{-- Timer dibuka & ditutup --}}
                    @php
                        $tsTutupPD = $sesi->tutup_pada ? $sesi->tutup_pada->timestamp : 0;
                    @endphp
                    <div class="px-5 py-2.5 bg-white border-b border-[#d1d5db] flex flex-wrap gap-x-6 gap-y-1.5">
                        @if ($sesi->tanggal_buka)
                            <div class="flex items-center gap-1.5 text-[12px] text-[#3c50e0]">
                                <span class="material-symbols-outlined text-[14px]">play_circle</span>
                                <span>Dibuka: <strong>{{ $sesi->tanggal_buka->format('H:i') }} WIB</strong></span>
                            </div>
                        @endif
                        @if ($sesi->tutup_pada)
                            <div x-data="{
                                    dl: {{ $tsTutupPD }},
                                    rem: '--:--',
                                    urg: false,
                                    init() { this.tick(); setInterval(() => this.tick(), 1000); },
                                    tick() {
                                        var diff = this.dl - Math.floor(Date.now() / 1000);
                                        if (diff <= 0) { this.rem = 'Habis'; this.urg = true; return; }
                                        var h = Math.floor(diff / 3600);
                                        var m = Math.floor((diff % 3600) / 60);
                                        var s = diff % 60;
                                        this.urg = diff < 300;
                                        this.rem = (h > 0 ? h + 'j ' : '') + String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
                                    }
                                 }"
                                 class="flex items-center gap-1.5 text-[12px]"
                                 :class="urg ? 'text-[#ba1a1a] font-semibold' : 'text-[#856404]'">
                                <span class="material-symbols-outlined text-[14px]">timer</span>
                                <span>Tutup: <strong>{{ $sesi->tutup_pada->format('H:i') }} WIB</strong>
                                    &mdash; sisa <span class="font-mono font-bold" x-text="rem"></span>
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="p-5">
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
                            <p class="text-center text-[12px] font-semibold uppercase tracking-wider mb-4" style="color: #171c1f">Pilih Status Kehadiran</p>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                                {{-- Hadir --}}
                                <button wire:click="absen({{ $sesi->id }}, 'hadir')"
                                        wire:loading.attr="disabled"
                                        class="group flex flex-row items-center justify-center gap-3.5 py-[18px] px-4 rounded-2xl border-2 cursor-pointer transition-transform active:scale-95"
                                        style="border-color: #86efac; background: #f0fdf4"
                                        onmouseover="this.style.background='#dcfce7'" onmouseout="this.style.background='#f0fdf4'">
                                    <span class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0 text-white transition-transform group-hover:scale-110" style="background: #16a34a">
                                        <span class="material-symbols-outlined text-[30px]" style="font-variation-settings:'FILL' 1">check_circle</span>
                                    </span>
                                    <span class="text-[17px] font-bold" style="color: #15803d">Hadir</span>
                                </button>
                                {{-- Izin --}}
                                <button wire:click="absen({{ $sesi->id }}, 'izin')"
                                        wire:loading.attr="disabled"
                                        class="group flex flex-row items-center justify-center gap-3.5 py-[18px] px-4 rounded-2xl border-2 cursor-pointer transition-transform active:scale-95"
                                        style="border-color: #fcd34d; background: #fffbeb"
                                        onmouseover="this.style.background='#fef3c7'" onmouseout="this.style.background='#fffbeb'">
                                    <span class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0 text-white transition-transform group-hover:scale-110" style="background: #d97706">
                                        <span class="material-symbols-outlined text-[30px]" style="font-variation-settings:'FILL' 1">info</span>
                                    </span>
                                    <span class="text-[17px] font-bold" style="color: #b45309">Izin</span>
                                </button>
                                {{-- Sakit --}}
                                <button wire:click="absen({{ $sesi->id }}, 'sakit')"
                                        wire:loading.attr="disabled"
                                        class="group flex flex-row items-center justify-center gap-3.5 py-[18px] px-4 rounded-2xl border-2 cursor-pointer transition-transform active:scale-95"
                                        style="border-color: #93c5fd; background: #eff6ff"
                                        onmouseover="this.style.background='#dbeafe'" onmouseout="this.style.background='#eff6ff'">
                                    <span class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0 text-white transition-transform group-hover:scale-110" style="background: #2563eb">
                                        <span class="material-symbols-outlined text-[30px]" style="font-variation-settings:'FILL' 1">local_hospital</span>
                                    </span>
                                    <span class="text-[17px] font-bold" style="color: #1d4ed8">Sakit</span>
                                </button>
                            </div>
                            <p class="text-center text-[12px] mt-4" style="color: #757686">
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
            <div class="grid grid-cols-4 sm:grid-cols-7 gap-2">
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
                    <div class="border rounded-xl p-3 text-center {{ $dayColor }} {{ $isToday ? 'ring-2 ring-[#3c50e0]' : '' }}">
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
