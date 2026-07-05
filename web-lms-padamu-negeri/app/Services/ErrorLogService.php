<?php

namespace App\Services;

use App\Models\ErrorLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ErrorLogService
{
    /**
     * Catat error ke tabel error_log (+ opsional log file Laravel).
     *
     * @param  bool  $logToFile  set false bila pemanggil global handler
     *                           (Laravel sudah menulis exception ke file secara default).
     */
    public function record(string $action, \Throwable $throwable, array $context = [], bool $logToFile = true): void
    {
        // Metadata teknis untuk membantu debug dari UI (ditaruh di kolom context).
        $meta = [
            'exception' => $throwable::class,
            'lokasi'    => $throwable->getFile() . ':' . $throwable->getLine(),
            'trace'     => collect(explode("\n", $throwable->getTraceAsString()))->take(15)->implode("\n"),
        ];

        try {
            ErrorLog::create([
                'user_id'     => Auth::id(),
                'aksi'        => $action,
                'pesan_error' => $throwable->getMessage() !== '' ? $throwable->getMessage() : '(tanpa pesan)',
                'context'     => array_merge($meta, $context),
                'url'         => request()->fullUrl(),
            ]);
        } catch (\Throwable) {
            // Do not re-throw — error logging must never disrupt the main flow
        }

        if ($logToFile) {
            Log::error("[{$action}] {$throwable->getMessage()}", [
                'exception' => $throwable,
                'context'   => $context,
            ]);
        }
    }
}
