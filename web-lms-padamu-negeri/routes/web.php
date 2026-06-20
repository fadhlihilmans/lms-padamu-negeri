<?php

use App\Http\Controllers\AuthController;
use App\Livewire\Auth\LoginForm;
use App\Livewire\Dashboard;
use App\Livewire\Admin\MasterData\MapelManager;
use App\Livewire\Admin\MasterData\PemetaanGuruMapelRombel;
use App\Livewire\Admin\MasterData\PeriodeAjaranManager;
use App\Livewire\Admin\MasterData\RombelManager;
use App\Livewire\Admin\MasterData\WilayahManager;
use App\Livewire\Admin\MasterData\PaketManager;
use App\Livewire\Admin\MasterData\TingkatManager;
use App\Livewire\Admin\Akademik\JadwalManager;
use App\Livewire\Admin\Pengguna\GuruManager;
use App\Livewire\Admin\Pengguna\PesertaDidikManager;
use App\Livewire\Guru\JadwalGuru;
use App\Livewire\PesertaDidik\JadwalPesertaDidik;
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
            Route::get('/mapel',          MapelManager::class)->name('mapel');
            Route::get('/pemetaan',       PemetaanGuruMapelRombel::class)->name('pemetaan');
        });

        // Pengguna
        Route::prefix('pengguna')->name('pengguna.')->group(function () {
            Route::get('/guru',          GuruManager::class)->name('guru');
            Route::get('/peserta-didik', PesertaDidikManager::class)->name('peserta-didik');
        });

        // Akademik
        Route::prefix('akademik')->name('akademik.')->group(function () {
            Route::get('/jadwal', JadwalManager::class)->name('jadwal');
        });
        // Import Excel — Langkah 12
        // Settings, Error Log, Bug Report — Langkah 19 & 20
    });

    // ── Guru routes ──────────────────────────────────────────────────────────
    Route::middleware('role:guru')->prefix('guru')->name('guru.')->group(function () {
        Route::get('/jadwal', JadwalGuru::class)->name('jadwal');
    });

    // ── Peserta Didik routes ─────────────────────────────────────────────────
    Route::middleware('role:peserta_didik')->prefix('peserta-didik')->name('peserta-didik.')->group(function () {
        Route::get('/jadwal', JadwalPesertaDidik::class)->name('jadwal');
    });

});
