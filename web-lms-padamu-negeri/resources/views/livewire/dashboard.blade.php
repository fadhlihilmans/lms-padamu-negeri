<div>

    {{-- ═══════════════════════════ ADMIN (2.1) ═══════════════════════════ --}}
    @if ($role === 'admin')
        {{-- Page header --}}
        <div class="mb-5">
            <h1 class="text-[20px] sm:text-[22px] font-bold" style="color:#171c1f">Selamat Datang, Admin</h1>
            <p class="text-[13px] mt-0.5" style="color:#757686">
                {{ $tanggalIndo }}
                @if ($periode) · TA {{ $periode->tahun_ajaran }} Semester {{ ucfirst($periode->semester) }} @endif
            </p>
        </div>

        {{-- Stat cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
            <div class="bg-white rounded-xl border p-5 flex items-center justify-between" style="border-color:#c5c5d7; box-shadow:0 1px 3px rgba(0,0,0,0.05)">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider mb-1.5" style="color:#757686">Peserta Didik</p>
                    <p class="text-[30px] font-bold leading-none" style="color:#171c1f">{{ number_format($totalPeserta, 0, ',', '.') }}</p>
                    <p class="text-[11px] mt-1.5" style="color:#757686">Aktif periode ini</p>
                </div>
                <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0" style="background:#EEF2FF">
                    <span class="material-symbols-outlined text-[22px]" style="color:#3c50e0; font-variation-settings:'FILL' 1">group</span>
                </div>
            </div>
            <div class="bg-white rounded-xl border p-5 flex items-center justify-between" style="border-color:#c5c5d7; box-shadow:0 1px 3px rgba(0,0,0,0.05)">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider mb-1.5" style="color:#757686">Guru</p>
                    <p class="text-[30px] font-bold leading-none" style="color:#171c1f">{{ number_format($totalGuru, 0, ',', '.') }}</p>
                    <p class="text-[11px] mt-1.5" style="color:#757686">Total terdaftar</p>
                </div>
                <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0" style="background:#f0fdf4">
                    <span class="material-symbols-outlined text-[22px]" style="color:#16a34a; font-variation-settings:'FILL' 1">badge</span>
                </div>
            </div>
            <div class="bg-white rounded-xl border p-5 flex items-center justify-between" style="border-color:#c5c5d7; box-shadow:0 1px 3px rgba(0,0,0,0.05)">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider mb-1.5" style="color:#757686">Rombel</p>
                    <p class="text-[30px] font-bold leading-none" style="color:#171c1f">{{ number_format($totalRombel, 0, ',', '.') }}</p>
                    <p class="text-[11px] mt-1.5" style="color:#757686">Aktif periode ini</p>
                </div>
                <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0" style="background:#fffbeb">
                    <span class="material-symbols-outlined text-[22px]" style="color:#d97706; font-variation-settings:'FILL' 1">groups</span>
                </div>
            </div>
            <div class="bg-white rounded-xl border p-5 flex items-center justify-between" style="border-color:#c5c5d7; box-shadow:0 1px 3px rgba(0,0,0,0.05)">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider mb-1.5" style="color:#757686">Periode Aktif</p>
                    <p class="text-[18px] font-bold leading-tight" style="color:#171c1f">{{ $periode?->tahun_ajaran ?? '—' }}</p>
                    <p class="text-[11px] mt-0.5" style="color:#3c50e0; font-weight:600">{{ $periode ? 'Semester ' . ucfirst($periode->semester) : 'Belum diset' }}</p>
                </div>
                <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0" style="background:#f0f4f8">
                    <span class="material-symbols-outlined text-[22px]" style="color:#505f76">calendar_today</span>
                </div>
            </div>
        </div>

        {{-- Bottom row: Rombel table + Absensi summary --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
            {{-- Rombel Terbaru --}}
            <div class="xl:col-span-2 bg-white rounded-xl border overflow-hidden" style="border-color:#c5c5d7; box-shadow:0 1px 3px rgba(0,0,0,0.05)">
                <div class="px-5 py-4 border-b flex items-center justify-between" style="border-color:#c5c5d7">
                    <h3 class="text-[14px] font-semibold" style="color:#171c1f">Rombel Terbaru</h3>
                    <a href="{{ route('admin.master.rombel') }}" class="text-[12.5px] font-medium" style="color:#3c50e0">Lihat Semua</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[520px]">
                        <thead style="background:white">
                            <tr>
                                <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wider whitespace-nowrap border-b" style="color:#505f76; border-color:#c5c5d7">Nama Rombel</th>
                                <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wider border-b" style="color:#505f76; border-color:#c5c5d7">Paket</th>
                                <th class="text-center px-5 py-3 text-[11px] font-semibold uppercase tracking-wider border-b" style="color:#505f76; border-color:#c5c5d7">Peserta Didik</th>
                                <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wider border-b" style="color:#505f76; border-color:#c5c5d7">Wali Kelas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rombelTerbaru as $r)
                                <tr class="border-b transition-colors" style="border-color:#f0f4f8"
                                    onmouseover="this.style.background='#f6fafe'" onmouseout="this.style.background='transparent'">
                                    <td class="px-5 py-3.5 text-[13.5px] font-medium" style="color:#171c1f">{{ $r->nama }}</td>
                                    <td class="px-5 py-3.5 text-[13px]" style="color:#505f76">{{ $r->paket?->nama ?? '—' }}</td>
                                    <td class="px-5 py-3.5 text-center">
                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-[12px] font-semibold" style="background:#EEF2FF; color:#3c50e0">{{ $r->peserta_didik_rombel_count }}</span>
                                    </td>
                                    <td class="px-5 py-3.5 text-[13px]" style="color:#505f76">{{ $r->waliKelas?->nama_lengkap ?? 'Belum ditentukan' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-10 text-center text-[13px]" style="color:#757686">Belum ada rombel pada periode ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Absensi Hari Ini (rekap ditampilkan oleh modul Absensi) --}}
            <div class="bg-white rounded-xl border flex flex-col" style="border-color:#c5c5d7; box-shadow:0 1px 3px rgba(0,0,0,0.05)">
                <div class="px-5 py-4 border-b flex-shrink-0 flex items-center justify-between" style="border-color:#c5c5d7">
                    <div>
                        <h3 class="text-[14px] font-semibold" style="color:#171c1f">Absensi Hari Ini</h3>
                        <p class="text-[12px] mt-0.5" style="color:#757686">{{ $absensiSesiCount }} sesi berjalan hari ini</p>
                    </div>
                    <a href="{{ route('admin.absensi.rekap') }}" class="text-[12.5px] font-medium flex-shrink-0" style="color:#3c50e0">Rekap</a>
                </div>
                @php $totalAbsen = array_sum($absensiHariIni); @endphp
                @if ($totalAbsen === 0)
                    <div class="p-5 flex-1 flex flex-col items-center justify-center text-center gap-2">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background:#f0f4f8">
                            <span class="material-symbols-outlined text-[24px]" style="color:#9da4b0">event_available</span>
                        </div>
                        <p class="text-[13px]" style="color:#757686">Belum ada kehadiran tercatat hari ini.</p>
                    </div>
                @else
                    <div class="p-5 grid grid-cols-2 gap-3">
                        @foreach ([
                            ['Hadir', $absensiHariIni['hadir'], '#16a34a', '#f0fdf4'],
                            ['Izin',  $absensiHariIni['izin'],  '#d97706', '#fffbeb'],
                            ['Sakit', $absensiHariIni['sakit'], '#3c50e0', '#EEF2FF'],
                            ['Alpa',  $absensiHariIni['alpa'],  '#ba1a1a', '#ffdad6'],
                        ] as [$labelA, $val, $c, $bg])
                            <div class="rounded-xl p-3.5 flex flex-col items-center justify-center" style="background:{{ $bg }}">
                                <span class="text-[24px] font-bold leading-none" style="color:{{ $c }}">{{ $val }}</span>
                                <span class="text-[11px] font-semibold uppercase tracking-wider mt-1" style="color:{{ $c }}">{{ $labelA }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    {{-- ═══════════════════════════ GURU (2.2) ═══════════════════════════ --}}
    @elseif ($role === 'guru')
        {{-- Welcome banner --}}
        <div class="bg-white rounded-xl border p-5 mb-5" style="border-color:#c5c5d7; box-shadow:0 1px 3px rgba(0,0,0,0.05)">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0" style="background:#EEF2FF">
                        <span class="material-symbols-outlined text-[24px]" style="color:#3c50e0; font-variation-settings:'FILL' 1">person</span>
                    </div>
                    <div>
                        <p class="text-[12px] font-medium" style="color:#757686">Selamat datang kembali</p>
                        <h2 class="text-[18px] sm:text-[20px] font-bold leading-tight" style="color:#171c1f">{{ $guru?->nama_lengkap ?? 'Guru' }}</h2>
                        <p class="text-[13px] mt-0.5" style="color:#505f76">
                            <span style="color:#3c50e0; font-weight:600">{{ $jadwalHariIni->count() }} kelas hari ini</span>
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 px-3 py-2 rounded-lg self-start sm:self-auto flex-shrink-0" style="background:#f6fafe; border:1px solid #c5c5d7">
                    <span class="material-symbols-outlined text-[16px]" style="color:#757686">today</span>
                    <span class="text-[12.5px] font-medium" style="color:#505f76">{{ $tanggalIndo }}</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            {{-- Jadwal Hari Ini --}}
            <div class="lg:col-span-2 bg-white rounded-xl border overflow-hidden" style="border-color:#c5c5d7; box-shadow:0 1px 3px rgba(0,0,0,0.05)">
                <div class="px-5 py-4 border-b flex items-center justify-between" style="border-color:#c5c5d7">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]" style="color:#3c50e0">event_upcoming</span>
                        <h3 class="text-[14px] font-semibold" style="color:#171c1f">Jadwal Hari Ini</h3>
                    </div>
                    <a href="{{ route('guru.jadwal') }}" class="text-[12.5px] font-medium" style="color:#3c50e0">Lihat Semua</a>
                </div>
                <div class="p-4 space-y-3">
                    @forelse ($jadwalHariIni as $j)
                        <div class="flex items-center gap-4 p-4 rounded-xl border transition-colors" style="border-color:#c5c5d7; background:white"
                             onmouseover="this.style.background='#f6fafe'" onmouseout="this.style.background='white'">
                            <div class="flex-shrink-0 w-16 h-14 rounded-xl flex flex-col items-center justify-center border" style="background:#f6fafe; border-color:#c5c5d7">
                                <span class="text-[10px] font-semibold uppercase tracking-wide" style="color:#757686">Mulai</span>
                                <span class="text-[14px] font-bold leading-tight" style="color:#3c50e0">{{ substr($j->jam_mulai, 0, 5) }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold" style="background:#EEF2FF; color:#3c50e0">{{ $j->guruMapelRombel?->rombel?->nama ?? '—' }}</span>
                                <p class="text-[14px] font-semibold truncate mt-1" style="color:#171c1f">{{ $j->guruMapelRombel?->mapel?->nama ?? 'Mata Pelajaran' }}</p>
                                <span class="flex items-center gap-1 text-[12px] mt-1" style="color:#757686">
                                    <span class="material-symbols-outlined text-[13px]">schedule</span>{{ substr($j->jam_mulai, 0, 5) }} – {{ substr($j->jam_selesai, 0, 5) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="flex items-center gap-3 px-4 py-3 rounded-xl" style="background:#f6fafe">
                            <span class="material-symbols-outlined text-[18px]" style="color:#c5c5d7">check_circle</span>
                            <p class="text-[13px]" style="color:#757686">Tidak ada jadwal mengajar hari ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Perlu Dinilai --}}
            <div class="bg-white rounded-xl border overflow-hidden" style="border-color:#c5c5d7; box-shadow:0 1px 3px rgba(0,0,0,0.05)">
                <div class="px-5 py-4 border-b flex items-center justify-between" style="border-color:#c5c5d7">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]" style="color:#d97706">assignment_turned_in</span>
                        <h3 class="text-[14px] font-semibold" style="color:#171c1f">Perlu Dinilai</h3>
                    </div>
                    <a href="{{ route('guru.tugas') }}" class="text-[12.5px] font-medium" style="color:#3c50e0">Semua</a>
                </div>
                <div class="divide-y" style="border-color:#f0f4f8">
                    @forelse ($perluDinilai as $t)
                        <a href="{{ route('guru.tugas.submisi', ['tugasId' => $t->id]) }}"
                           class="px-5 py-3.5 flex items-center justify-between transition-colors"
                             onmouseover="this.style.background='#f6fafe'" onmouseout="this.style.background='transparent'">
                            <div class="min-w-0">
                                <p class="text-[13.5px] font-semibold truncate" style="color:#171c1f">{{ $t->judul }}</p>
                                <p class="text-[12px] mt-0.5" style="color:#757686">{{ $t->guruMapelRombel?->rombel?->nama ?? '—' }}</p>
                            </div>
                            <span class="inline-flex items-center justify-center min-w-[28px] h-7 px-2 rounded-full text-[12px] font-bold flex-shrink-0" style="background:#ffdad6; color:#ba1a1a">{{ $t->belum_dinilai_count }}</span>
                        </a>
                    @empty
                        <div class="px-5 py-8 text-center">
                            <span class="material-symbols-outlined text-[28px] mb-1 block" style="color:#c5c5d7">task_alt</span>
                            <p class="text-[13px]" style="color:#757686">Semua submisi sudah dinilai.</p>
                        </div>
                    @endforelse

                    @if ($perluDinilai->isNotEmpty())
                        <div class="px-5 py-3.5 flex items-center justify-between" style="background:#f6fafe">
                            <span class="text-[12px]" style="color:#757686">Total belum dinilai</span>
                            <span class="text-[13px] font-bold" style="color:#171c1f">{{ $perluDinilai->sum('belum_dinilai_count') }} submisi</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    {{-- ═══════════════════════ PESERTA DIDIK (2.3) ═══════════════════════ --}}
    @elseif ($role === 'peserta_didik')
        <div class="max-w-2xl mx-auto lg:max-w-none space-y-4">

            {{-- Welcome card --}}
            <div class="rounded-2xl p-5 text-white relative overflow-hidden" style="background:linear-gradient(135deg,#1c33c8 0%,#3c50e0 60%,#5b6ff5 100%); box-shadow:0 6px 20px rgba(60,80,224,0.35)">
                <div class="absolute top-0 right-0 w-40 h-40 rounded-full opacity-10" style="background:white; transform:translate(30%,-30%)"></div>
                <div class="absolute bottom-0 left-0 w-28 h-28 rounded-full opacity-10" style="background:white; transform:translate(-30%,30%)"></div>
                <div class="relative">
                    <p class="text-[12px] font-medium mb-1" style="color:rgba(255,255,255,0.7)">Selamat Datang 👋</p>
                    <h2 class="text-[20px] sm:text-[22px] font-bold leading-tight">{{ $pd?->nama_lengkap ?? 'Peserta Didik' }}</h2>
                    <p class="text-[13px] mt-1 mb-4" style="color:rgba(255,255,255,0.8)">
                        {{ $rombelPd?->nama ?? 'Belum ada rombel' }}@if ($rombelPd?->paket) — {{ $rombelPd->paket->nama }} @endif
                        @if ($periode) · TA {{ $periode->tahun_ajaran }} @endif
                    </p>
                    @if ($absensiStatus === 'sudah')
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-[12px] font-semibold" style="background:rgba(255,255,255,0.18); border:1px solid rgba(255,255,255,0.25)">
                            <span class="w-2 h-2 rounded-full bg-green-300"></span>
                            Sudah Absen Hari Ini
                        </div>
                    @elseif ($absensiStatus === 'belum')
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-[12px] font-semibold" style="background:rgba(255,255,255,0.18); border:1px solid rgba(255,255,255,0.25)">
                            <span class="w-2 h-2 rounded-full bg-red-400"></span>
                            Belum Absen Hari Ini
                        </div>
                    @else
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-[12px] font-semibold" style="background:rgba(255,255,255,0.12); border:1px solid rgba(255,255,255,0.2)">
                            <span class="w-2 h-2 rounded-full" style="background:rgba(255,255,255,0.6)"></span>
                            Tidak Ada Sesi Absensi
                        </div>
                    @endif
                </div>
            </div>

            {{-- Absensi CTA — hanya jika modul absensi aktif (route terdaftar) --}}
            @if (\Route::has('peserta-didik.absensi'))
                @if ($absensiStatus === 'belum')
                    {{-- Ada sesi terbuka yang belum diisi → ajak absen --}}
                    <a href="{{ route('peserta-didik.absensi') }}" class="rounded-xl p-4 flex items-center gap-4 transition-all" style="background:white; border:1.5px solid #3c50e0; box-shadow:0 2px 8px rgba(60,80,224,0.12)"
                       onmouseover="this.style.background='#EEF2FF'" onmouseout="this.style.background='white'">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:#EEF2FF">
                            <span class="material-symbols-outlined text-[24px]" style="color:#3c50e0; font-variation-settings:'FILL' 1">touch_app</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[14px] font-semibold" style="color:#171c1f">Absensi Hari Ini</p>
                            <p class="text-[12px] mt-0.5" style="color:#505f76">{{ $sesiTerbukaCount }} sesi menunggu — absen sebelum ditutup.</p>
                        </div>
                        <span class="flex-shrink-0 px-4 py-2 rounded-lg text-[13px] font-semibold text-white" style="background:#3c50e0">Absen</span>
                    </a>
                @elseif ($absensiStatus === 'sudah')
                    {{-- Semua sesi hari ini sudah diisi --}}
                    <a href="{{ route('peserta-didik.absensi') }}" class="rounded-xl p-4 flex items-center gap-4 transition-all" style="background:#f0fdf4; border:1.5px solid #16a34a33"
                       onmouseover="this.style.background='#e7f9ee'" onmouseout="this.style.background='#f0fdf4'">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:#dcfce7">
                            <span class="material-symbols-outlined text-[24px]" style="color:#16a34a; font-variation-settings:'FILL' 1">check_circle</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[14px] font-semibold" style="color:#171c1f">Absensi Selesai</p>
                            <p class="text-[12px] mt-0.5" style="color:#505f76">Anda sudah mengisi semua absensi hari ini.</p>
                        </div>
                        <span class="flex-shrink-0 material-symbols-outlined text-[20px]" style="color:#16a34a">chevron_right</span>
                    </a>
                @else
                    {{-- Tidak ada sesi absensi hari ini --}}
                    <div class="rounded-xl p-4 flex items-center gap-4" style="background:white; border:1.5px dashed #c5c5d7">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:#f0f4f8">
                            <span class="material-symbols-outlined text-[24px]" style="color:#9da4b0">event_busy</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[14px] font-semibold" style="color:#171c1f">Tidak Ada Absensi Hari Ini</p>
                            <p class="text-[12px] mt-0.5" style="color:#757686">Belum ada sesi absensi yang dibuka guru untuk hari ini.</p>
                        </div>
                    </div>
                @endif
            @endif

            {{-- Quick menu — hanya menu yang modulnya aktif (route terdaftar) --}}
            @php
                $quickMenu = collect([
                    ['peserta-didik.jadwal', 'Jadwal', 'schedule',   '#3c50e0', '#EEF2FF', 0],
                    ['peserta-didik.materi', 'Materi', 'menu_book',  '#16a34a', '#f0fdf4', 0],
                    ['peserta-didik.tugas',  'Tugas',  'assignment', '#d97706', '#fffbeb', $tugasMendatang->count()],
                    ['peserta-didik.cbt',    'CBT',    'quiz',       '#9333ea', '#fdf2f8', $cbtMendatang->count()],
                ])->filter(fn ($m) => \Route::has($m[0]))->values();
            @endphp
            @if ($quickMenu->isNotEmpty())
                <div class="grid gap-3" style="grid-template-columns:repeat({{ $quickMenu->count() }}, minmax(0, 1fr))">
                    @foreach ($quickMenu as [$route, $label, $icon, $color, $bg, $badge])
                        <a href="{{ route($route) }}" class="flex flex-col items-center gap-2 py-4 rounded-xl bg-white border transition-colors text-center relative" style="border-color:#c5c5d7"
                           onmouseover="this.style.background='#f6fafe'" onmouseout="this.style.background='white'">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center relative" style="background:{{ $bg }}">
                                <span class="material-symbols-outlined text-[20px]" style="color:{{ $color }}">{{ $icon }}</span>
                                @if ($badge > 0)
                                    <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full text-[10px] font-bold text-white flex items-center justify-center" style="background:#ba1a1a">{{ $badge }}</span>
                                @endif
                            </div>
                            <span class="text-[11px] font-semibold" style="color:#505f76">{{ $label }}</span>
                        </a>
                    @endforeach
                </div>
            @endif

            {{-- Jadwal Hari Ini --}}
            <div class="bg-white rounded-xl border overflow-hidden" style="border-color:#c5c5d7; box-shadow:0 1px 3px rgba(0,0,0,0.05)">
                <div class="px-5 py-4 border-b flex items-center justify-between" style="border-color:#c5c5d7">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[17px]" style="color:#3c50e0">event_upcoming</span>
                        <h3 class="text-[14px] font-semibold" style="color:#171c1f">Jadwal Hari Ini</h3>
                    </div>
                    <a href="{{ route('peserta-didik.jadwal') }}" class="text-[12px] font-medium" style="color:#3c50e0">Lihat Semua</a>
                </div>
                <div class="divide-y" style="border-color:#f0f4f8">
                    @forelse ($jadwalHariIni as $j)
                        <div class="flex items-center gap-3 px-4 py-3.5">
                            <div class="w-14 h-10 rounded-lg flex flex-col items-center justify-center flex-shrink-0 border" style="background:#f6fafe; border-color:#c5c5d7">
                                <span class="text-[13px] font-bold leading-none" style="color:#505f76">{{ substr($j->jam_mulai, 0, 5) }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-[13.5px] font-semibold truncate" style="color:#171c1f">{{ $j->guruMapelRombel?->mapel?->nama ?? 'Mata Pelajaran' }}</p>
                                <p class="text-[11.5px]" style="color:#505f76">{{ substr($j->jam_mulai, 0, 5) }} – {{ substr($j->jam_selesai, 0, 5) }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="px-4 py-8 text-center">
                            <span class="material-symbols-outlined text-[28px] mb-1 block" style="color:#c5c5d7">event_available</span>
                            <p class="text-[13px]" style="color:#757686">Tidak ada jadwal hari ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Bottom grid: Tugas + CBT mendatang --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Tugas mendatang --}}
                <div class="bg-white rounded-xl border overflow-hidden" style="border-color:#c5c5d7; box-shadow:0 1px 3px rgba(0,0,0,0.05)">
                    <div class="px-5 py-4 border-b flex items-center justify-between" style="border-color:#c5c5d7">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[17px]" style="color:#d97706">assignment</span>
                            <h3 class="text-[13.5px] font-semibold" style="color:#171c1f">Tugas Mendatang</h3>
                        </div>
                        @if (\Route::has('peserta-didik.tugas'))
                            <a href="{{ route('peserta-didik.tugas') }}" class="text-[12px] font-medium" style="color:#3c50e0">Semua</a>
                        @endif
                    </div>
                    <div class="divide-y" style="border-color:#f0f4f8">
                        @forelse ($tugasMendatang as $t)
                            <div class="px-4 py-3.5 flex items-start gap-3 transition-colors"
                                 onmouseover="this.style.background='#f6fafe'" onmouseout="this.style.background='transparent'">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5" style="background:#fffbeb">
                                    <span class="material-symbols-outlined text-[15px]" style="color:#d97706">assignment</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[13px] font-semibold truncate" style="color:#171c1f">{{ $t->judul }}</p>
                                    <p class="text-[11.5px] flex items-center gap-1 mt-0.5" style="color:#757686">
                                        <span class="material-symbols-outlined text-[13px]">schedule</span>{{ \Illuminate\Support\Carbon::parse($t->deadline)->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="px-4 py-8 text-center">
                                <span class="material-symbols-outlined text-[28px] mb-1 block" style="color:#c5c5d7">task_alt</span>
                                <p class="text-[13px]" style="color:#757686">Tidak ada tugas mendatang.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- CBT mendatang --}}
                <div class="bg-white rounded-xl border overflow-hidden" style="border-color:#c5c5d7; box-shadow:0 1px 3px rgba(0,0,0,0.05)">
                    <div class="px-5 py-4 border-b flex items-center justify-between" style="border-color:#c5c5d7">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[17px]" style="color:#9333ea">quiz</span>
                            <h3 class="text-[13.5px] font-semibold" style="color:#171c1f">CBT Mendatang</h3>
                        </div>
                        @if (\Route::has('peserta-didik.cbt'))
                            <a href="{{ route('peserta-didik.cbt') }}" class="text-[12px] font-medium" style="color:#3c50e0">Semua</a>
                        @endif
                    </div>
                    <div class="p-4 space-y-3">
                        @forelse ($cbtMendatang as $c)
                            @php
                                $mulaiAt     = \Illuminate\Support\Carbon::parse($c->tanggal_mulai);
                                $selesaiAt   = $mulaiAt->copy()->addMinutes((int) $c->durasi_menit);
                                $berlangsung = now()->between($mulaiAt, $selesaiAt);
                            @endphp
                            <div class="p-4 rounded-xl border transition-colors"
                                 style="border-color:{{ $berlangsung ? '#16a34a' : '#c5c5d7' }}; background:{{ $berlangsung ? '#f0fdf4' : 'white' }}">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex-1 min-w-0">
                                        {{-- Tanda status: tetap ada saat CBT sedang berlangsung --}}
                                        @if ($berlangsung)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold mb-1.5" style="background:#dcfce7; color:#15803d">
                                                <span class="relative flex w-1.5 h-1.5">
                                                    <span class="animate-ping absolute inline-flex w-full h-full rounded-full opacity-75" style="background:#16a34a"></span>
                                                    <span class="relative inline-flex w-1.5 h-1.5 rounded-full" style="background:#16a34a"></span>
                                                </span>
                                                SEDANG BERLANGSUNG
                                            </span>
                                        @else
                                            <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-semibold mb-1.5" style="background:#f0f4f8; color:#757686">{{ ucfirst(str_replace('_', ' ', $c->jenis_cbt ?? 'CBT')) }}</span>
                                        @endif
                                        <p class="text-[13.5px] font-semibold truncate" style="color:#171c1f">{{ $c->nama_ujian }}</p>
                                        <p class="text-[11.5px] mt-0.5" style="color:#505f76">{{ $c->guruMapelRombel?->mapel?->nama ?? '' }}</p>
                                    </div>
                                    <div class="text-right flex-shrink-0">
                                        <p class="text-[22px] font-bold leading-none" style="color:{{ $berlangsung ? '#16a34a' : '#3c50e0' }}">{{ $mulaiAt->format('d') }}</p>
                                        <p class="text-[10px] font-semibold uppercase" style="color:#757686">{{ $mulaiAt->format('M') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 mt-3 pt-3 border-t" style="border-color:{{ $berlangsung ? '#bbf7d0' : '#f0f4f8' }}">
                                    <span class="flex items-center gap-1 text-[12px]" style="color:#505f76">
                                        <span class="material-symbols-outlined text-[13px]">schedule</span>{{ $mulaiAt->format('H:i') }}
                                    </span>
                                    <span class="flex items-center gap-1 text-[12px]" style="color:#505f76">
                                        <span class="material-symbols-outlined text-[13px]">timer</span>{{ $c->durasi_menit }} menit
                                    </span>
                                    @if ($berlangsung && \Route::has('peserta-didik.cbt.kerjakan'))
                                        <a href="{{ route('peserta-didik.cbt.kerjakan', ['cbtId' => $c->id]) }}"
                                           class="ml-auto px-3 py-1.5 rounded-lg text-[12px] font-semibold text-white" style="background:#16a34a">
                                            Kerjakan
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="px-4 py-8 text-center">
                                <span class="material-symbols-outlined text-[28px] mb-1 block" style="color:#c5c5d7">quiz</span>
                                <p class="text-[13px]" style="color:#757686">Tidak ada CBT mendatang.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

    {{-- ═══════════════════════════ FALLBACK ═══════════════════════════ --}}
    @else
        <div class="w-full min-h-[calc(100vh-10rem)] rounded-xl border-2 border-dashed border-outline-variant/50 bg-white/50 flex items-center justify-center">
            <div class="text-center text-secondary p-8">
                <span class="material-symbols-outlined text-5xl mb-3 opacity-40 block">dashboard_customize</span>
                <h3 class="text-headline-md opacity-50">Dashboard</h3>
                <p class="text-body-md opacity-40 mt-1">Peran pengguna belum dikenali.</p>
            </div>
        </div>
    @endif

</div>
