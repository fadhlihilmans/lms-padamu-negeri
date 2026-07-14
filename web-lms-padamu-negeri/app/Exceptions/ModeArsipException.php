<?php

namespace App\Exceptions;

/**
 * Dilempar saat pengguna mencoba MENGUBAH data akademik sementara ia sedang
 * melihat periode lampau (Mode Arsip).
 *
 * Bukan error sistem — ini penolakan yang disengaja. Karena itu:
 *  - TIDAK dicatat ke `error_log` (lihat daftar abaikan di bootstrap/app.php)
 *  - Dirender jadi toast peringatan + kembali ke halaman sebelumnya.
 */
class ModeArsipException extends \RuntimeException
{
    public function __construct(string $message = '')
    {
        parent::__construct($message !== '' ? $message : self::pesanBaku());
    }

    public static function pesanBaku(): string
    {
        return 'Mode Arsip: data periode lampau bersifat baca-saja. '
             . 'Kembali ke Periode Aktif terlebih dahulu untuk mengubah data.';
    }
}
