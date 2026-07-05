<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Alias middleware Spatie Laravel-Permission + kustom
        $middleware->alias([
            'role'               => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission'         => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'module'             => \App\Http\Middleware\EnsureModuleActive::class,
        ]);

        // Mode Maintenance dicek global di grup web (CLAUDE.md #10).
        $middleware->appendToGroup('web', \App\Http\Middleware\CheckMaintenanceMode::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        // Catat SEMUA exception tak-tertangani ke tabel error_log (CLAUDE.md #14),
        // supaya error yang tidak dibungkus try-catch (mis. query gagal saat login)
        // tetap muncul di menu Log Error. Exception "wajar" (validasi, auth, 404/403,
        // CSRF, model tidak ditemukan) diabaikan agar log tidak banjir.
        $exceptions->report(function (\Throwable $e): void {
            if (app()->runningInConsole()) {
                return; // abaikan error CLI/artisan/seeder
            }

            $abaikan = [
                \Illuminate\Validation\ValidationException::class,
                \Illuminate\Auth\AuthenticationException::class,
                \Illuminate\Auth\Access\AuthorizationException::class,
                \Illuminate\Session\TokenMismatchException::class,
                \Illuminate\Database\Eloquent\ModelNotFoundException::class,
                \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface::class,
            ];

            foreach ($abaikan as $tipe) {
                if ($e instanceof $tipe) {
                    return;
                }
            }

            // logToFile: false → Laravel sudah menulis exception ini ke file secara default.
            app(\App\Services\ErrorLogService::class)->record('Exception Tak Tertangani', $e, [], false);
        });
    })->create();
