<?php

namespace App\Http\Middleware;

use App\Services\SettingService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Mode Maintenance (CLAUDE.md #10): saat aktif, Admin tetap akses penuh;
 * role lain diarahkan ke halaman maintenance. Didaftarkan di grup `web`.
 */
class CheckMaintenanceMode
{
    public function handle(Request $request, Closure $next): Response
    {
        if (app(SettingService::class)->get('maintenance_mode', false)) {
            $user = $request->user();

            // Admin bypass; tamu dibiarkan (agar bisa login).
            if ($user && ! $user->hasRole('admin')) {
                // Biarkan logout supaya pengguna non-admin bisa keluar.
                if ($request->routeIs('logout')) {
                    return $next($request);
                }
                return response(view('maintenance'), 503);
            }
        }

        return $next($request);
    }
}
