<?php

namespace App\Exceptions;

/**
 * Dilempar bila satu Peserta Didik hendak dimasukkan ke LEBIH DARI SATU rombel
 * pada Tahun Ajaran yang sama.
 *
 * Ini akar bug "1 siswa banyak rombel" (Revisi Logic Sistem #12): unique
 * (peserta_didik_id, rombel_id) hanya mencegah PD masuk rombel YANG SAMA dua
 * kali — ia tidak mencegah PD berada di banyak rombel sekaligus.
 *
 * Aturan final (docs/keputusan-revisi.md): 1 PD = 1 rombel per Tahun Ajaran.
 */
class SatuRombelPerTaException extends \RuntimeException
{
    public function __construct(
        public readonly string $namaPesertaDidik,
        public readonly string $namaRombelLain,
        public readonly string $tahunAjaran,
    ) {
        parent::__construct(
            "{$namaPesertaDidik} sudah terdaftar di rombel \"{$namaRombelLain}\" "
            . "pada TA {$tahunAjaran}. Satu peserta didik hanya boleh punya satu rombel per Tahun Ajaran — "
            . 'keluarkan dulu dari rombel lama sebelum memindahkannya.'
        );
    }
}
