<?php

namespace App\Services;

use App\Models\PeriodeAjaran;

class PeriodeService
{
    /**
     * Periode yang sedang dilihat pengguna.
     * Jika tidak ada pilihan di session, fallback ke periode aktif di DB.
     */
    public function periodeSelected(): ?PeriodeAjaran
    {
        $id = session('periode_id_selected');

        if ($id) {
            $periode = PeriodeAjaran::find($id);
            if ($periode) {
                return $periode;
            }
            // ID tersimpan sudah tidak valid — bersihkan
            session()->forget('periode_id_selected');
        }

        return PeriodeAjaran::where('is_aktif', true)->first();
    }

    /**
     * Apakah pengguna sedang melihat periode historis (bukan yang aktif)?
     * Komponen/action yang bersifat write harus cek ini sebelum mengizinkan mutasi.
     */
    public function isReadOnlyMode(): bool
    {
        $periode = $this->periodeSelected();
        return $periode !== null && ! $periode->is_aktif;
    }

    public function setPeriode(int $id): void
    {
        session(['periode_id_selected' => $id]);
    }

    public function resetToAktif(): void
    {
        session()->forget('periode_id_selected');
    }
}
