<?php

use App\Http\Controllers\AuthController;
use App\Livewire\Auth\LoginForm;
use App\Livewire\Dashboard;
use App\Livewire\Admin\MasterData\MapelManager;
use App\Livewire\Admin\MasterData\PemetaanGuruMapelRombel;
use App\Livewire\Admin\MasterData\PeriodeAjaranManager;
use App\Livewire\Admin\MasterData\RombelManager;
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
use App\Livewire\Guru\Tugas\DaftarTugas;
use App\Livewire\Guru\Tugas\DaftarSubmisi;
use App\Livewire\Guru\Tugas\DetailSubmisi;
use App\Livewire\Guru\Cbt\DaftarCbt;
use App\Livewire\Guru\Cbt\FormCbt;
use App\Livewire\PesertaDidik\Tugas\DaftarTugasPD;
use App\Livewire\PesertaDidik\Tugas\SubmisiTugasPD;
use App\Livewire\PesertaDidik\Absensi\TombolHadir;
use App\Livewire\PesertaDidik\JadwalPesertaDidik;
use App\Livewire\PesertaDidik\Materi\DaftarMateriPD;
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
            Route::get('/rombel',         RombelManager::class)->name('rombel');
            Route::get('/rombel/{id}',    DetailRombel::class)->name('rombel.show');
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
        // Import Excel
        Route::prefix('import')->name('import.')->group(function () {
            Route::get('/peserta-didik', ImportPesertaDidik::class)->name('peserta-didik');
        });

        // Absensi rekap
        Route::prefix('absensi')->name('absensi.')->group(function () {
            Route::get('/rekap', RekapAbsensi::class)->name('rekap');
        });

        // Settings, Error Log, Bug Report — Langkah 19 & 20
    });

    // ── Guru routes ──────────────────────────────────────────────────────────
    Route::middleware('role:guru')->prefix('guru')->name('guru.')->group(function () {
        Route::get('/jadwal',  JadwalGuru::class)->name('jadwal');
        Route::get('/absensi', SesiAbsensi::class)->name('absensi');
        Route::get('/materi',      DaftarMateri::class)->name('materi');
        Route::get('/materi/{id}', DetailMateri::class)->name('materi.show');
        Route::get('/tugas',             DaftarTugas::class)->name('tugas');
        Route::get('/tugas/{tugasId}/submisi', DaftarSubmisi::class)->name('tugas.submisi');
        Route::get('/tugas/{tugasId}/submisi/{pdId}', DetailSubmisi::class)->name('tugas.submisi.detail');
        // ── CBT (Langkah 16) ──
        Route::get('/cbt', DaftarCbt::class)->name('cbt');
        Route::get('/cbt/create', FormCbt::class)->name('cbt.create');
        Route::get('/cbt/{cbtId}/edit', FormCbt::class)->name('cbt.edit');
    });

    // ── Peserta Didik routes ─────────────────────────────────────────────────
    Route::middleware('role:peserta_didik')->prefix('peserta-didik')->name('peserta-didik.')->group(function () {
        Route::get('/jadwal',  JadwalPesertaDidik::class)->name('jadwal');
        Route::get('/absensi', TombolHadir::class)->name('absensi');
        Route::get('/materi',          DaftarMateriPD::class)->name('materi');
        Route::get('/materi/{id}',     DetailMateriPD::class)->name('materi.detail');
        Route::get('/tugas',           DaftarTugasPD::class)->name('tugas');
        Route::get('/tugas/{tugasId}', SubmisiTugasPD::class)->name('tugas.detail');
    });

});
