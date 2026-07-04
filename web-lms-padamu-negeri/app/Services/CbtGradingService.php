<?php

namespace App\Services;

use App\Models\Cbt;
use App\Models\CbtJawabanPeserta;
use App\Models\HasilCbt;
use App\Models\PesertaDidik;

/**
 * Auto-grading & lifecycle pengerjaan CBT.
 *
 * MODEL NILAI (keputusan produk): skala 0–100 per soal.
 * - nilai_pg     = persentase PG benar (0–100).
 * - nilai_uraian = rata-rata skor uraian (diisi guru saat koreksi).
 * - nilai_akhir  = rata-rata tertimbang PG & uraian menurut jumlah soal.
 */
class CbtGradingService
{
    /** Mulai / lanjutkan attempt: get-or-create hasil_cbt. */
    public function startAttempt(Cbt $cbt, PesertaDidik $pd): HasilCbt
    {
        $hasUraian = $cbt->soal()->where('tipe_soal', 'uraian')->exists();

        return HasilCbt::firstOrCreate(
            ['cbt_id' => $cbt->id, 'peserta_didik_id' => $pd->id],
            [
                'waktu_mulai'      => now(),
                'status_penilaian' => $hasUraian ? 'menunggu_koreksi' : 'otomatis',
            ],
        );
    }

    /** Simpan / patch satu jawaban PD (huruf PG atau teks uraian). */
    public function saveAnswer(HasilCbt $hasil, int $cbtSoalId, ?string $jawaban): void
    {
        CbtJawabanPeserta::updateOrCreate(
            ['hasil_cbt_id' => $hasil->id, 'cbt_soal_id' => $cbtSoalId],
            ['jawaban' => $jawaban !== '' ? $jawaban : null],
        );
    }

    /** Finalisasi ujian: auto-grade PG, tentukan status, set waktu_submit. */
    public function submit(HasilCbt $hasil): void
    {
        if ($hasil->waktu_submit) {
            return; // sudah pernah submit — idempoten
        }

        $soal       = $hasil->cbt->soal()->get();
        $pgSoal     = $soal->where('tipe_soal', 'pilihan_ganda');
        $uraianSoal = $soal->where('tipe_soal', 'uraian');

        $jawaban = CbtJawabanPeserta::where('hasil_cbt_id', $hasil->id)
            ->get()->keyBy('cbt_soal_id');

        // nilai_pg = % PG benar (0–100).
        $nilaiPg = 0;
        if ($pgSoal->count() > 0) {
            $benar = 0;
            foreach ($pgSoal as $s) {
                $jwb = $jawaban[$s->id]->jawaban ?? null;
                if ($jwb !== null && $jwb === $s->kunci_jawaban) {
                    $benar++;
                }
            }
            $nilaiPg = (int) round($benar / $pgSoal->count() * 100);
        }

        if ($uraianSoal->count() > 0) {
            // Ada uraian → menunggu koreksi guru; nilai_akhir belum final.
            $hasil->update([
                'nilai_pg'         => $nilaiPg,
                'nilai_uraian'     => null,
                'nilai_akhir'      => null,
                'status_penilaian' => 'menunggu_koreksi',
                'waktu_submit'     => now(),
            ]);
        } else {
            // PG murni → nilai final otomatis.
            $hasil->update([
                'nilai_pg'         => $nilaiPg,
                'nilai_uraian'     => null,
                'nilai_akhir'      => $nilaiPg,
                'status_penilaian' => 'otomatis',
                'waktu_submit'     => now(),
            ]);
        }
    }
}
