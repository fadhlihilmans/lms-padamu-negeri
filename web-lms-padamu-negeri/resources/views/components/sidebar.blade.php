{{--
    Sidebar — menu ditampilkan sesuai role pengguna yang sedang login.
    Menu modul (CBT, Tugas, Absensi, Materi) akan disembunyikan via SettingService
    di Langkah 19 (toggle modul). Saat ini masih statis.
--}}
@php
    $user = auth()->user();
    $isAdmin        = $user?->hasRole('admin');
    $isGuru         = $user?->hasRole('guru');
    $isPesertaDidik = $user?->hasRole('peserta_didik');

    // Wali kelas = guru yang menjadi wali di salah satu rombel.
    $isWaliKelas = $isGuru && $user?->guru
        && \App\Models\Rombel::where('wali_kelas_id', $user->guru->id)->exists();

    // Toggle modul (lapis 1/UX — CLAUDE.md #11) + identitas dari SettingService.
    $setting    = app(\App\Services\SettingService::class);
    $namaPkbm   = $setting->get('nama_pkbm', 'LMS Padamu Negeri');
    $logoApp    = $setting->get('logo_aplikasi_path', '');
    // Toggle modul per role (Guru & Peserta Didik).
    $modMateriGuru  = $setting->get('modul_materi_guru_aktif', true);
    $modTugasGuru   = $setting->get('modul_tugas_guru_aktif', true);
    $modAbsensiGuru = $setting->get('modul_absensi_guru_aktif', true);
    $modCbtGuru     = $setting->get('modul_cbt_guru_aktif', true);
    $modMateriPd    = $setting->get('modul_materi_pd_aktif', true);
    $modTugasPd     = $setting->get('modul_tugas_pd_aktif', true);
    $modAbsensiPd   = $setting->get('modul_absensi_pd_aktif', true);
    $modCbtPd       = $setting->get('modul_cbt_pd_aktif', true);

    $currentRoute = request()->route()?->getName() ?? '';
@endphp

<div class="flex flex-col h-full">

    {{-- Logo / Brand --}}
    <div class="px-5 py-5 border-b border-outline-variant flex items-center gap-3 flex-shrink-0">
        @if ($logoApp)
            <img src="{{ \Illuminate\Support\Facades\Storage::url($logoApp) }}" alt="Logo"
                 class="w-9 h-9 rounded-xl object-contain flex-shrink-0">
        @else
            <div class="w-9 h-9 rounded-xl bg-[#3c50e0] flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-white text-[18px]" style="font-variation-settings:'FILL' 1">school</span>
            </div>
        @endif
        <div class="min-w-0">
            <h1 class="text-[14px] font-bold text-[#3c50e0] leading-tight truncate">{{ $namaPkbm }}</h1>
            <p class="text-[11px] text-[#757686] truncate">Sistem Akademik PKBM</p>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto py-4 flex flex-col gap-0.5 px-3">

        {{-- ===== ADMIN MENU ===== --}}
        @if($isAdmin)

            <x-sidebar-item route="dashboard" icon="dashboard" label="Dashboard" />

            <p class="px-3 pt-4 pb-1 text-[10px] font-bold uppercase tracking-[0.08em] text-[#9da4b0]">Master Data</p>
            <x-sidebar-item route="admin.master.periode" icon="calendar_month" label="Periode Ajaran" />
            <x-sidebar-item route="admin.master.wilayah" icon="location_on" label="Wilayah" />
            <x-sidebar-item route="admin.master.paket" icon="layers" label="Paket" />
            <x-sidebar-item route="admin.master.tingkat" icon="bar_chart" label="Tingkat Kelas" />
            <x-sidebar-item route="admin.master.rombel" icon="groups" label="Rombel" />
            <x-sidebar-item route="admin.master.mapel" icon="book" label="Mata Pelajaran" />
            <x-sidebar-item route="admin.master.pemetaan" icon="hub" label="Pemetaan Guru-Mapel" />

            <p class="px-3 pt-4 pb-1 text-[10px] font-bold uppercase tracking-[0.08em] text-[#9da4b0]">Pengguna</p>
            <x-sidebar-item route="admin.pengguna.guru" icon="badge" label="Guru" />
            <x-sidebar-item route="admin.pengguna.peserta-didik" icon="person" label="Peserta Didik" />

            <p class="px-3 pt-4 pb-1 text-[10px] font-bold uppercase tracking-[0.08em] text-[#9da4b0]">Akademik</p>
            <x-sidebar-item route="admin.akademik.jadwal" icon="schedule" label="Jadwal Pelajaran" />
            <x-sidebar-item route="admin.import.peserta-didik" icon="upload_file" label="Import Excel" />
            <x-sidebar-item route="admin.absensi.rekap" icon="how_to_reg" label="Rekap Absensi" />
            <x-sidebar-item route="admin.kenaikan" icon="trending_up" label="Kenaikan Kelas" />
            <x-sidebar-item route="admin.penilaian" icon="grade" label="Rekap Penilaian" />

            <p class="px-3 pt-4 pb-1 text-[10px] font-bold uppercase tracking-[0.08em] text-[#9da4b0]">Sistem</p>
            <x-sidebar-item route="admin.pengaturan" icon="settings" label="Pengaturan" />
            <x-sidebar-item route="admin.grade" icon="tune" label="Konfigurasi Nilai" />
            <x-sidebar-item route="admin.error-log" icon="bug_report" label="Log Error" />
            <x-sidebar-item route="admin.bug-report" icon="pest_control" label="Laporan Bug" />
            <x-sidebar-item route="bug-report.index" icon="flag" label="Lapor Bug" />

        {{-- ===== GURU MENU ===== --}}
        @elseif($isGuru)

            <x-sidebar-item route="dashboard" icon="dashboard" label="Dashboard" />

            <p class="px-3 pt-4 pb-1 text-[10px] font-bold uppercase tracking-[0.08em] text-[#9da4b0]">Kelas Saya</p>
            <x-sidebar-item route="guru.jadwal" icon="schedule" label="Jadwal Pelajaran" />
            @if($modAbsensiGuru)<x-sidebar-item route="guru.absensi" icon="how_to_reg" label="Absensi" />@endif
            @if($modMateriGuru)<x-sidebar-item route="guru.materi" icon="menu_book" label="Materi" />@endif
            @if($modTugasGuru)<x-sidebar-item route="guru.tugas" icon="assignment" label="Tugas" />@endif
            @if($modCbtGuru)<x-sidebar-item route="guru.cbt" icon="quiz" label="CBT" />@endif
            @if($isWaliKelas)
                <x-sidebar-item route="guru.kenaikan" icon="trending_up" label="Kenaikan Kelas" />
            @endif
            <x-sidebar-item route="guru.penilaian" icon="grade" label="Penilaian Akhir" />
            @if($isWaliKelas)
                <x-sidebar-item route="guru.rapor" icon="fact_check" label="Progres Rapor" />
            @endif

            <p class="px-3 pt-4 pb-1 text-[10px] font-bold uppercase tracking-[0.08em] text-[#9da4b0]">Lainnya</p>
            <x-sidebar-item route="bug-report.index" icon="flag" label="Lapor Bug" />

        {{-- ===== PESERTA DIDIK MENU ===== --}}
        @elseif($isPesertaDidik)

            <x-sidebar-item route="dashboard" icon="dashboard" label="Dashboard" />

            <p class="px-3 pt-4 pb-1 text-[10px] font-bold uppercase tracking-[0.08em] text-[#9da4b0]">Belajar</p>
            <x-sidebar-item route="peserta-didik.jadwal" icon="schedule" label="Jadwal Pelajaran" />
            @if($modAbsensiPd)<x-sidebar-item route="peserta-didik.absensi" icon="how_to_reg" label="Absensi" />@endif
            @if($modMateriPd)<x-sidebar-item route="peserta-didik.materi" icon="menu_book" label="Materi" />@endif
            @if($modTugasPd)<x-sidebar-item route="peserta-didik.tugas" icon="assignment" label="Tugas" />@endif
            @if($modCbtPd)<x-sidebar-item route="peserta-didik.cbt" icon="quiz" label="CBT" />@endif
            <x-sidebar-item route="peserta-didik.rapor" icon="grade" label="Rapor Saya" />

            <p class="px-3 pt-4 pb-1 text-[10px] font-bold uppercase tracking-[0.08em] text-[#9da4b0]">Lainnya</p>
            <x-sidebar-item route="bug-report.index" icon="flag" label="Lapor Bug" />

        @endif

    </nav>

    {{-- Footer: Logout --}}
    <div class="flex-shrink-0 border-t border-outline-variant p-3">
        <button type="button"
                @click="$dispatch('open-logout-modal')"
                class="w-full flex items-center gap-3 px-3 py-2.5 text-secondary hover:bg-surface-container-low rounded-lg transition-colors text-label-md group cursor-pointer">
            <span class="material-symbols-outlined text-[20px] group-hover:text-error transition-colors">logout</span>
            <span>Keluar</span>
        </button>
    </div>

</div>
