<?php

namespace Database\Seeders;

use App\Models\Cbt;
use App\Models\CbtJawabanPeserta;
use App\Models\GuruMapelRombel;
use App\Models\HasilCbt;
use App\Models\PeriodeAjaran;
use App\Models\PesertaDidik;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Mengisi `hasil_cbt` + `cbt_jawaban_peserta` untuk CBT pilihan ganda yang sudah
 * lewat (kemarin). Penilaian PG otomatis (meniru CbtGradingService::submit):
 * nilai_pg = % benar, nilai_akhir = nilai_pg, status 'otomatis', nilai tampil.
 */
class HasilCbtSeeder extends Seeder
{
    public function run(): void
    {
        // NIPD diambil DINAMIS dari PesertaDidikSeeder (urut id) — jangan hardcode,
        // supaya seeder ini tidak rusak bila daftar NIPD diubah.
        $daftarNipd = \App\Models\PesertaDidik::orderBy('id')->pluck('nipd')->all();

        $periode = PeriodeAjaran::where('is_aktif', true)->firstOrFail();

        $gmr = GuruMapelRombel::where('tahun_ajaran', $periode->tahun_ajaran)
            ->whereHas('rombel.wilayah', fn ($q) => $q->where('nama', 'Botolambat'))
            ->whereHas('mapel', fn ($q) => $q->where('nama', 'Matematika'))
            ->first();
        if (! $gmr) {
            return;
        }

        $cbt = Cbt::where('guru_mapel_rombel_id', $gmr->id)
            ->where('nama_ujian', 'Ujian Pilihan Ganda Matematika Dasar')
            ->first();
        if (! $cbt) {
            return;
        }

        $soal = $cbt->soal()->orderBy('id')->get(); // 5 PG, urutan sesuai bank

        // Pola jawaban tiap PD (huruf per soal, urut sesuai $soal).
        $patterns = [
            $daftarNipd[0] => ['C', 'B', 'A', 'B', 'C'], // semua benar  → 100
            $daftarNipd[1] => ['C', 'B', 'A', 'B', 'A'], // 4 benar      → 80
            $daftarNipd[2] => ['C', 'B', 'C', 'A', 'C'], // 3 benar      → 60
        ];

        $mulai  = Carbon::yesterday()->setTime(9, 0);
        $submit = Carbon::yesterday()->setTime(9, 45);

        foreach ($patterns as $nipd => $answers) {
            $pd = PesertaDidik::where('nipd', $nipd)->first();
            if (! $pd) {
                continue;
            }

            $benar = 0;
            foreach ($soal as $i => $s) {
                if (($answers[$i] ?? null) === $s->kunci_jawaban) {
                    $benar++;
                }
            }
            $nilaiPg = $soal->count() > 0 ? (int) round($benar / $soal->count() * 100) : 0;

            $hasil = HasilCbt::firstOrCreate(
                ['cbt_id' => $cbt->id, 'peserta_didik_id' => $pd->id],
                [
                    'waktu_mulai'       => $mulai,
                    'waktu_submit'      => $submit,
                    'nilai_pg'          => $nilaiPg,
                    'nilai_uraian'      => null,
                    'nilai_akhir'       => $nilaiPg,
                    'status_penilaian'  => 'otomatis',
                    'nilai_ditampilkan' => true,
                ],
            );

            foreach ($soal as $i => $s) {
                CbtJawabanPeserta::firstOrCreate(
                    ['hasil_cbt_id' => $hasil->id, 'cbt_soal_id' => $s->id],
                    ['jawaban' => $answers[$i] ?? null],
                );
            }
        }
    }
}
