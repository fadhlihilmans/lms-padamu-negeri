<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TrixUploadController;
use App\Livewire\Auth\LoginForm;
use App\Livewire\Dashboard;
use App\Livewire\Admin\MasterData\MapelManager;
use App\Livewire\Admin\MasterData\PemetaanGuruMapelRombel;
use App\Livewire\Admin\MasterData\PeriodeAjaranManager;
use App\Livewire\Admin\MasterData\RombelManager;
use App\Livewire\Admin\Kenaikan\AssignRombelBaru;
use App\Livewire\Admin\Pengaturan\SettingManager;
use App\Livewire\Admin\Pengaturan\GradeManager;
use App\Livewire\Admin\Log\DaftarErrorLog;
use App\Livewire\Admin\BugReport\DaftarBugReport;
use App\Livewire\BugReport\FormLaporBug;
use App\Livewire\Admin\MasterData\FormRombel;
use App\Livewire\Admin\MasterData\DetailRombel;
use App\Livewire\Admin\MasterData\WilayahManager;
use App\Livewire\Admin\MasterData\PaketManager;
use App\Livewire\Admin\MasterData\TingkatManager;
use App\Livewire\Admin\Akademik\JadwalManager;
use App\Livewire\Admin\Absensi\RekapAbsensi;
use App\Livewire\Admin\ImportExcel\ImportPesertaDidik;
use App\Livewire\Admin\Pengguna\GuruManager;
use App\Livewire\Admin\Pengguna\PesertaDidikManager;
use App\Livewire\Admin\Pengguna\DetailPesertaDidik;
use App\Livewire\Guru\Absensi\SesiAbsensi;
use App\Livewire\Guru\JadwalGuru;
use App\Livewire\Guru\Materi\DaftarMateri;
use App\Livewire\Guru\Materi\DetailMateri;
use App\Livewire\Guru\Materi\FormMateri;
use App\Livewire\Guru\Tugas\DaftarTugas;
use App\Livewire\Guru\Tugas\FormTugas;
use App\Livewire\Guru\Tugas\DaftarSubmisi;
use App\Livewire\Guru\Tugas\DetailSubmisi;
use App\Livewire\Guru\Cbt\DaftarCbt;
use App\Livewire\Guru\Cbt\FormSoalCbt;
use App\Livewire\Guru\Cbt\DaftarHasilCbt;
use App\Livewire\Guru\Cbt\FormKoreksiUraian;
use App\Livewire\Guru\Kenaikan\PenentuanStatus;
use App\Livewire\Guru\Penilaian\FormNilaiKomponen;
use App\Livewire\Guru\Penilaian\ProgresRapor;
use App\Livewire\Guru\Penilaian\PreviewRapor;
use App\Livewire\PesertaDidik\Tugas\DaftarTugasPD;
use App\Livewire\PesertaDidik\Tugas\SubmisiTugasPD;
use App\Livewire\PesertaDidik\Absensi\TombolHadir;
use App\Livewire\PesertaDidik\JadwalPesertaDidik;
use App\Livewire\PesertaDidik\Materi\DaftarMateriPD;
use App\Livewire\PesertaDidik\Cbt\DaftarCbtTersedia;
use App\Livewire\PesertaDidik\Cbt\PengerjaanCbt;
use App\Livewire\PesertaDidik\Materi\DetailMateriPD;
use App\Services\PeriodeService;
use Illuminate\Support\Facades\Route;

// ── Root redirect ────────────────────────────────────────────────────────────
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// ── Guest routes ─────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', LoginForm::class)->name('login');
});

// ── Logout ──────────────────────────────────────────────────────────────────
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// ── Authenticated routes ──────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // Reset periode switcher ke aktif
    Route::get('/periode/reset', function () {
        app(PeriodeService::class)->resetToActive();
        return redirect()->back();
    })->name('periode.reset');

    // ── Admin routes ─────────────────────────────────────────────────────────
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {

        // Master Data
        Route::prefix('master-data')->name('master.')->group(function () {
            Route::get('/periode-ajaran', PeriodeAjaranManager::class)->name('periode');
            Route::get('/wilayah',        WilayahManager::class)->name('wilayah');
            Route::get('/paket',          PaketManager::class)->name('paket');
            Route::get('/tingkat',        TingkatManager::class)->name('tingkat');
            Route::get('/rombel',           RombelManager::class)->name('rombel');
            Route::get('/rombel/tambah',    FormRombel::class)->name('rombel.create');
            Route::get('/rombel/{id}/edit', FormRombel::class)->name('rombel.edit');
            Route::get('/rombel/{id}',      DetailRombel::class)->name('rombel.show');
            Route::get('/mapel',          MapelManager::class)->name('mapel');
            Route::get('/pemetaan',       PemetaanGuruMapelRombel::class)->name('pemetaan');
        });

        // Pengguna
        Route::prefix('pengguna')->name('pengguna.')->group(function () {
            Route::get('/guru',               GuruManager::class)->name('guru');
            Route::get('/peserta-didik',      PesertaDidikManager::class)->name('peserta-didik');
            Route::get('/peserta-didik/{id}', DetailPesertaDidik::class)->name('peserta-didik.show');
        });

        // Akademik
        Route::prefix('akademik')->name('akademik.')->group(function () {
            Route::get('/jadwal', JadwalManager::class)->name('jadwal');
        });

        // Kenaikan Kelas — Assign Rombel Baru (Langkah 17, Ruang Tunggu)
        Route::get('/kenaikan', AssignRombelBaru::class)->name('kenaikan');
        // Import Excel
        Route::prefix('import')->name('import.')->group(function () {
            Route::get('/peserta-didik', ImportPesertaDidik::class)->name('peserta-didik');
        });

        // Absensi rekap
        Route::prefix('absensi')->name('absensi.')->group(function () {
            Route::get('/rekap', RekapAbsensi::class)->name('rekap');
        });

        // Pengaturan (Langkah 19)
        Route::get('/pengaturan', SettingManager::class)->name('pengaturan');
        Route::get('/pengaturan/grade', GradeManager::class)->name('grade');

        // Error Log & Bug Report — Langkah 20 (Admin)
        Route::get('/log-error', DaftarErrorLog::class)->name('error-log');
        Route::get('/laporan-bug', DaftarBugReport::class)->name('bug-report');
    });

    // ── Guru routes ──────────────────────────────────────────────────────────
    Route::middleware('role:guru')->prefix('guru')->name('guru.')->group(function () {
        Route::get('/jadwal',  JadwalGuru::class)->name('jadwal');
        // ── Modul dengan toggle (CLAUDE.md #11) ──
        Route::middleware('module:absensi')->group(function () {
            Route::get('/absensi', SesiAbsensi::class)->name('absensi');
        });
        Route::middleware('module:materi')->group(function () {
            Route::get('/materi',           DaftarMateri::class)->name('materi');
            Route::get('/materi/tambah',    FormMateri::class)->name('materi.create');
            Route::get('/materi/{id}',      DetailMateri::class)->name('materi.show');
            Route::get('/materi/{id}/edit', FormMateri::class)->name('materi.edit');
            Route::post('/materi/trix-upload', [TrixUploadController::class, 'store'])->name('materi.trix-upload');
        });
        Route::middleware('module:tugas')->group(function () {
            Route::get('/tugas',                  DaftarTugas::class)->name('tugas');
            Route::get('/tugas/tambah',           FormTugas::class)->name('tugas.create');
            Route::get('/tugas/{id}/edit',        FormTugas::class)->name('tugas.edit');
            Route::get('/tugas/{tugasId}/submisi', DaftarSubmisi::class)->name('tugas.submisi');
            Route::get('/tugas/{tugasId}/submisi/{pdId}', DetailSubmisi::class)->name('tugas.submisi.detail');
        });
        // ── CBT (Langkah 16) ──
        Route::middleware('module:cbt')->group(function () {
            Route::get('/cbt', DaftarCbt::class)->name('cbt');
            Route::get('/cbt/{cbtId}/soal', FormSoalCbt::class)->name('cbt.soal');
            Route::get('/cbt/{cbtId}/hasil', DaftarHasilCbt::class)->name('cbt.hasil');
            Route::get('/cbt/{cbtId}/koreksi', FormKoreksiUraian::class)->name('cbt.koreksi');
        });
        // ── Kenaikan Kelas (Langkah 17, Wali Kelas) ──
        Route::get('/kenaikan', PenentuanStatus::class)->name('kenaikan');
        // ── Penilaian Akhir / Rapor (Langkah 18) ──
        Route::get('/penilaian',      FormNilaiKomponen::class)->name('penilaian');   // Guru Mapel: input nilai
        Route::get('/rapor',          ProgresRapor::class)->name('rapor');            // Wali Kelas: progres & terbit
        Route::get('/rapor/preview',  PreviewRapor::class)->name('rapor.preview');    // Wali Kelas: pratinjau & cetak
    });

    // ── Peserta Didik routes ─────────────────────────────────────────────────
    Route::middleware('role:peserta_didik')->prefix('peserta-didik')->name('peserta-didik.')->group(function () {
        Route::get('/jadwal',  JadwalPesertaDidik::class)->name('jadwal');
        // ── Modul dengan toggle (CLAUDE.md #11) ──
        Route::middleware('module:absensi')->group(function () {
            Route::get('/absensi', TombolHadir::class)->name('absensi');
        });
        Route::middleware('module:materi')->group(function () {
            Route::get('/materi',          DaftarMateriPD::class)->name('materi');
            Route::get('/materi/{id}',     DetailMateriPD::class)->name('materi.detail');
        });
        Route::middleware('module:tugas')->group(function () {
            Route::get('/tugas',           DaftarTugasPD::class)->name('tugas');
            Route::get('/tugas/{tugasId}', SubmisiTugasPD::class)->name('tugas.detail');
        });
        // ── CBT (Langkah 16, sisi PD) ──
        Route::middleware('module:cbt')->group(function () {
            Route::get('/cbt', DaftarCbtTersedia::class)->name('cbt');
            Route::get('/cbt/{cbtId}/kerjakan', PengerjaanCbt::class)->name('cbt.kerjakan');
        });
        // ── Rapor (Langkah 18, sisi PD) ──
        Route::get('/rapor', PreviewRapor::class)->name('rapor');
    });

    // ── Lapor Bug (Langkah 20) — semua role yang login ──
    Route::get('/lapor-bug', FormLaporBug::class)->name('bug-report.index');

});
