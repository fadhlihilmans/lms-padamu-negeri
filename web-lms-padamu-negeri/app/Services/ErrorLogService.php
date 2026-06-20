<?php

namespace App\Services;

use App\Models\ErrorLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ErrorLogService
{
    /**
     * Catat exception ke tabel error_log sekaligus ke Laravel log.
     * Dipanggil dari setiap blok catch(\Throwable) — WAJIB per CLAUDE.md rule 14.
     */
    public function catat(string $aksi, \Throwable $throwable, array $context = []): void
    {
        // Tulis ke database agar Admin bisa lihat dari UI (Langkah 20)
        try {
            ErrorLog::create([
                'user_id'     => Auth::id(),
                'aksi'        => $aksi,
                'pesan_error' => $throwable->getMessage(),
                'context'     => empty($context) ? null : $context,
                'url'         => request()->fullUrl(),
            ]);
        } catch (\Throwable) {
            // Jangan re-throw — pencatatan error tidak boleh mengganggu flow utama
        }

        // Juga catat ke Laravel log untuk keperluan debugging server
        Log::error("[{$aksi}] {$throwable->getMessage()}", [
            'exception' => $throwable,
            'context'   => $context,
        ]);
    }
}
