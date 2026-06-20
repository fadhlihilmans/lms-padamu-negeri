<?php

namespace App\Services;

use App\Models\ErrorLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ErrorLogService
{
    public function record(string $action, \Throwable $throwable, array $context = []): void
    {
        try {
            ErrorLog::create([
                'user_id'     => Auth::id(),
                'aksi'        => $action,
                'pesan_error' => $throwable->getMessage(),
                'context'     => empty($context) ? null : $context,
                'url'         => request()->fullUrl(),
            ]);
        } catch (\Throwable) {
            // Do not re-throw — error logging must never disrupt the main flow
        }

        Log::error("[{$action}] {$throwable->getMessage()}", [
            'exception' => $throwable,
            'context'   => $context,
        ]);
    }
}
