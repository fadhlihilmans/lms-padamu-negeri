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
 * Pemakaian: ->middleware('module:cbt') / 'module:tugas' / 'module:absensi' / 'module:materi'
 */
class EnsureModuleActive
{
    public function handle(Request $request, Closure $next, string $module): Response
    {
        $key = "modul_{$module}_aktif";

        if (! app(SettingService::class)->get($key, true)) {
            return response(view('modul-nonaktif', ['module' => $module]), 403);
        }

        return $next($request);
    }
}
