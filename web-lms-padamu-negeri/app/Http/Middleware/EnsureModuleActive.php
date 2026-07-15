<?php

namespace App\Http\Middleware;

use App\Services\SettingService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Lapis keamanan toggle modul (CLAUDE.md #11): blokir akses langsung ke URL
 * modul yang dinonaktifkan (bukan sekadar menyembunyikan menu di sidebar).
 * Tampilkan halaman "Modul belum tersedia", bukan 404 polos.
 *
 * Toggle terpisah per role: `modul_{modul}_guru_aktif` & `modul_{modul}_pd_aktif`.
 * Pemakaian: ->middleware('module:cbt') / 'module:tugas' / 'module:absensi' / 'module:materi'
 */
class EnsureModuleActive
{
    public function handle(Request $request, Closure $next, string $module): Response
    {
        $user = $request->user();

        // Tentukan sufiks role. Admin (di luar guru/pd) tidak diblokir modul.
        $role = match (true) {
            $user?->hasRole('guru')          => 'guru',
            $user?->hasRole('peserta_didik') => 'pd',
            default                          => null,
        };

        if ($role !== null) {
            $key = "modul_{$module}_{$role}_aktif";
            if (! app(SettingService::class)->get($key, true)) {
                return response(view('modul-nonaktif', ['module' => $module]), 403);
            }
        }

        return $next($request);
    }
}
