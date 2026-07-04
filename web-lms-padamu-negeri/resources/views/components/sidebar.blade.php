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

    $currentRoute = request()->route()?->getName() ?? '';
@endphp

<div class="flex flex-col h-full">

    {{-- Logo / Brand --}}
    <div class="px-5 py-5 border-b border-outline-variant flex items-center gap-3 flex-shrink-0">
        <div class="w-9 h-9 rounded-xl bg-[#3c50e0] flex items-center justify-center flex-shrink-0">
            <span class="material-symbols-outlined text-white text-[18px]" style="font-variation-settings:'FILL' 1">school</span>
        </div>
        <div class="min-w-0">
            <h1 class="text-[14px] font-bold text-[#3c50e0] leading-tight truncate">{{ config('app.name', 'LMS Padamu Negeri') }}</h1>
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
            <x-sidebar-item route="kenaikan.index" icon="trending_up" label="Kenaikan Kelas" />
            <x-sidebar-item route="penilaian.index" icon="grade" label="Penilaian Akhir" />

            <p class="px-3 pt-4 pb-1 text-[10px] font-bold uppercase tracking-[0.08em] text-[#9da4b0]">Sistem</p>
            <x-sidebar-item route="pengaturan.index" icon="settings" label="Pengaturan" />
            <x-sidebar-item route="error-log.index" icon="bug_report" label="Log Error" />
            <x-sidebar-item route="bug-report.index" icon="flag" label="Lapor Bug" />

        {{-- ===== GURU MENU ===== --}}
        @elseif($isGuru)

            <x-sidebar-item route="dashboard" icon="dashboard" label="Dashboard" />

            <p class="px-3 pt-4 pb-1 text-[10px] font-bold uppercase tracking-[0.08em] text-[#9da4b0]">Kelas Saya</p>
            <x-sidebar-item route="guru.jadwal" icon="schedule" label="Jadwal Pelajaran" />
            <x-sidebar-item route="guru.absensi" icon="how_to_reg" label="Absensi" />
            <x-sidebar-item route="guru.materi" icon="menu_book" label="Materi" />
            <x-sidebar-item route="guru.tugas" icon="assignment" label="Tugas" />
            <x-sidebar-item route="guru.cbt" icon="quiz" label="CBT" />
            <x-sidebar-item route="penilaian.index" icon="grade" label="Penilaian Akhir" />

            <p class="px-3 pt-4 pb-1 text-[10px] font-bold uppercase tracking-[0.08em] text-[#9da4b0]">Lainnya</p>
            <x-sidebar-item route="bug-report.index" icon="flag" label="Lapor Bug" />

        {{-- ===== PESERTA DIDIK MENU ===== --}}
        @elseif($isPesertaDidik)

            <x-sidebar-item route="dashboard" icon="dashboard" label="Dashboard" />

            <p class="px-3 pt-4 pb-1 text-[10px] font-bold uppercase tracking-[0.08em] text-[#9da4b0]">Belajar</p>
            <x-sidebar-item route="peserta-didik.jadwal" icon="schedule" label="Jadwal Pelajaran" />
            <x-sidebar-item route="peserta-didik.absensi" icon="how_to_reg" label="Absensi" />
            <x-sidebar-item route="peserta-didik.materi" icon="menu_book" label="Materi" />
            <x-sidebar-item route="peserta-didik.tugas" icon="assignment" label="Tugas" />
            <x-sidebar-item route="peserta-didik.cbt" icon="quiz" label="CBT" />
            <x-sidebar-item route="penilaian.index" icon="grade" label="Rapor Saya" />

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
