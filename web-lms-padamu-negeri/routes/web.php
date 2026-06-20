<?php

use App\Http\Controllers\AuthController;
use App\Livewire\Auth\LoginForm;
use App\Livewire\Dashboard;
use App\Livewire\Admin\MasterData\PeriodeAjaranManager;
use App\Livewire\Admin\MasterData\WilayahManager;
use App\Livewire\Admin\MasterData\PaketManager;
use App\Livewire\Admin\MasterData\TingkatManager;
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
        app(PeriodeService::class)->resetToAktif();
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

            // Rombel, Mapel, Pemetaan — Langkah 9
        });

        // Pengguna — Langkah 10
        // Jadwal — Langkah 11
        // Import Excel — Langkah 12
        // Settings, Error Log, Bug Report — Langkah 19 & 20
    });

    // ── Guru routes ──────────────────────────────────────────────────────────
    Route::middleware('role:guru')->prefix('guru')->name('guru.')->group(function () {
        // Modul Guru dibangun Langkah 13–16
    });

    // ── Peserta Didik routes ─────────────────────────────────────────────────
    Route::middleware('role:peserta_didik')->prefix('peserta-didik')->name('peserta-didik.')->group(function () {
        // Modul Peserta Didik dibangun Langkah 13–16
    });

});
