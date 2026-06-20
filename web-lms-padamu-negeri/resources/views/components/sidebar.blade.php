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
    <div class="px-6 py-5 border-b border-outline-variant flex items-center gap-3 flex-shrink-0">
        <div class="w-10 h-10 rounded-lg bg-primary-container flex items-center justify-center flex-shrink-0">
            <span class="material-symbols-outlined text-on-primary text-[22px]">school</span>
        </div>
        <div class="min-w-0">
            <h1 class="text-label-md text-primary font-bold leading-tight truncate">{{ config('app.name', 'LMS Padamu Negeri') }}</h1>
            <p class="text-label-sm text-secondary truncate">Sistem Akademik PKBM</p>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto py-4 flex flex-col gap-0.5 px-2">

        {{-- ===== ADMIN MENU ===== --}}
        @if($isAdmin)

            <x-sidebar-item route="dashboard" icon="dashboard" label="Dashboard" />

            <p class="px-3 pt-4 pb-1 text-label-sm text-secondary/60 uppercase tracking-wider">Master Data</p>
            <x-sidebar-item route="admin.master.periode" icon="calendar_month" label="Periode Ajaran" />
            <x-sidebar-item route="admin.master.wilayah" icon="location_on" label="Wilayah" />
            <x-sidebar-item route="admin.master.paket" icon="layers" label="Paket" />
            <x-sidebar-item route="admin.master.tingkat" icon="bar_chart" label="Tingkat" />
            <x-sidebar-item route="master.rombel" icon="groups" label="Rombel" />
            <x-sidebar-item route="master.mapel" icon="book" label="Mata Pelajaran" />
            <x-sidebar-item route="master.pemetaan" icon="hub" label="Pemetaan Guru-Mapel" />

            <p class="px-3 pt-4 pb-1 text-label-sm text-secondary/60 uppercase tracking-wider">Pengguna</p>
            <x-sidebar-item route="pengguna.guru" icon="badge" label="Guru" />
            <x-sidebar-item route="pengguna.peserta-didik" icon="person" label="Peserta Didik" />

            <p class="px-3 pt-4 pb-1 text-label-sm text-secondary/60 uppercase tracking-wider">Akademik</p>
            <x-sidebar-item route="jadwal.index" icon="schedule" label="Jadwal Pelajaran" />
            <x-sidebar-item route="import.index" icon="upload_file" label="Import Excel" />
            <x-sidebar-item route="absensi.index" icon="how_to_reg" label="Absensi" />
            <x-sidebar-item route="kenaikan.index" icon="trending_up" label="Kenaikan Kelas" />
            <x-sidebar-item route="penilaian.index" icon="grade" label="Penilaian Akhir" />

            <p class="px-3 pt-4 pb-1 text-label-sm text-secondary/60 uppercase tracking-wider">Sistem</p>
            <x-sidebar-item route="pengaturan.index" icon="settings" label="Pengaturan" />
            <x-sidebar-item route="error-log.index" icon="bug_report" label="Log Error" />

        {{-- ===== GURU MENU ===== --}}
        @elseif($isGuru)

            <x-sidebar-item route="dashboard" icon="dashboard" label="Dashboard" />

            <p class="px-3 pt-4 pb-1 text-label-sm text-secondary/60 uppercase tracking-wider">Kelas Saya</p>
            <x-sidebar-item route="jadwal.index" icon="schedule" label="Jadwal Pelajaran" />
            <x-sidebar-item route="absensi.index" icon="how_to_reg" label="Absensi" />
            <x-sidebar-item route="materi.index" icon="menu_book" label="Materi" />
            <x-sidebar-item route="tugas.index" icon="assignment" label="Tugas" />
            <x-sidebar-item route="cbt.index" icon="quiz" label="CBT" />
            <x-sidebar-item route="penilaian.index" icon="grade" label="Penilaian Akhir" />

        {{-- ===== PESERTA DIDIK MENU ===== --}}
        @elseif($isPesertaDidik)

            <x-sidebar-item route="dashboard" icon="dashboard" label="Dashboard" />

            <p class="px-3 pt-4 pb-1 text-label-sm text-secondary/60 uppercase tracking-wider">Belajar</p>
            <x-sidebar-item route="jadwal.index" icon="schedule" label="Jadwal Pelajaran" />
            <x-sidebar-item route="absensi.index" icon="how_to_reg" label="Absensi" />
            <x-sidebar-item route="materi.index" icon="menu_book" label="Materi" />
            <x-sidebar-item route="tugas.index" icon="assignment" label="Tugas" />
            <x-sidebar-item route="cbt.index" icon="quiz" label="CBT" />
            <x-sidebar-item route="penilaian.index" icon="grade" label="Rapor Saya" />

        @endif

    </nav>

    {{-- Footer: Logout --}}
    <div class="flex-shrink-0 border-t border-outline-variant p-4">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="w-full flex items-center gap-3 px-3 py-2.5 text-secondary hover:bg-surface-container-low rounded-lg transition-colors text-label-md group">
                <span class="material-symbols-outlined text-[20px] group-hover:text-error transition-colors">logout</span>
                <span>Keluar</span>
            </button>
        </form>
    </div>

</div>
