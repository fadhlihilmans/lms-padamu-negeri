<?php

namespace Database\Seeders;

use App\Models\Cbt;
use App\Models\CbtSoal;
use App\Models\GuruMapelRombel;
use App\Models\PeriodeAjaran;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Mengisi `cbt` + `cbt_soal`.
 *
 * Tiga CBT sesuai permintaan (masing-masing 5 soal), semua di rombel Botolambat – Matematika:
 *  1. tanggal_mulai = sekarang + 2 jam   (campuran, akan datang hari ini)
 *  2. tanggal_mulai = besok               (campuran, terjadwal)
 *  3. tanggal_mulai = kemarin             (pilihan ganda saja, durasi 90 mnt, nilai tampil otomatis)
 */
class CbtSeeder extends Seeder
{
    public function run(): void
    {
        $periode = PeriodeAjaran::where('is_aktif', true)->firstOrFail();

        $gmr = GuruMapelRombel::where('tahun_ajaran', $periode->tahun_ajaran)
            ->whereHas('rombel.wilayah', fn ($q) => $q->where('nama', 'Botolambat'))
            ->whereHas('mapel', fn ($q) => $q->where('nama', 'Matematika'))
            ->first();
        if (! $gmr) {
            return;
        }

        // PG soal generik dipakai ulang; kunci disertakan.
        $pgBank = [
            ['pertanyaan' => 'Hasil dari 2x + 3 = 11 adalah x = ...', 'pilihan_jawaban' => ['A' => '2', 'B' => '3', 'C' => '4', 'D' => '5'], 'kunci_jawaban' => 'C'],
            ['pertanyaan' => 'Bentuk sederhana dari 3(2a + 4) adalah ...', 'pilihan_jawaban' => ['A' => '6a + 4', 'B' => '6a + 12', 'C' => '5a + 12', 'D' => '6a + 7'], 'kunci_jawaban' => 'B'],
            ['pertanyaan' => 'Nilai dari 5² − 3² adalah ...', 'pilihan_jawaban' => ['A' => '16', 'B' => '8', 'C' => '4', 'D' => '34'], 'kunci_jawaban' => 'A'],
            ['pertanyaan' => 'Jika y = 2x dan x = 5, maka y = ...', 'pilihan_jawaban' => ['A' => '7', 'B' => '10', 'C' => '25', 'D' => '3'], 'kunci_jawaban' => 'B'],
            ['pertanyaan' => 'KPK dari 4 dan 6 adalah ...', 'pilihan_jawaban' => ['A' => '10', 'B' => '24', 'C' => '12', 'D' => '2'], 'kunci_jawaban' => 'C'],
        ];

        $uraianBank = [
            ['pertanyaan' => 'Jelaskan langkah menyelesaikan persamaan linear 3x − 6 = 9.'],
            ['pertanyaan' => 'Berikan satu contoh penerapan aljabar dalam kehidupan sehari-hari dan uraikan.'],
        ];

        // Definisi 3 CBT.
        $cbtDefs = [
            [
                'nama_ujian'               => 'Ulangan Harian Aljabar',
                'kkm'                      => 70,
                'tanggal_mulai'            => Carbon::now()->addHours(2),
                'durasi_menit'             => 90,
                'tampilkan_nilai_otomatis' => false,
                'jenis_cbt'                => 'campuran',
                'soal'                     => $this->campuran($pgBank, $uraianBank),
            ],
            [
                'nama_ujian'               => 'Ulangan Persamaan Linear',
                'kkm'                      => 70,
                'tanggal_mulai'            => Carbon::tomorrow()->setTime(8, 0),
                'durasi_menit'             => 90,
                'tampilkan_nilai_otomatis' => false,
                'jenis_cbt'                => 'campuran',
                'soal'                     => $this->campuran($pgBank, $uraianBank),
            ],
            [
                'nama_ujian'               => 'Ujian Pilihan Ganda Matematika Dasar',
                'kkm'                      => 70,
                'tanggal_mulai'            => Carbon::yesterday()->setTime(9, 0),
                'durasi_menit'             => 90,
                'tampilkan_nilai_otomatis' => true,
                'jenis_cbt'                => 'pilihan_ganda',
                'soal'                     => array_map(fn ($s) => $s + ['tipe_soal' => 'pilihan_ganda'], $pgBank),
            ],
        ];

        foreach ($cbtDefs as $def) {
            $soalDef = $def['soal'];
            unset($def['soal']);

            $def['periode_ajaran_id'] = $periode->id;
            $cbt = Cbt::firstOrCreate(
                ['guru_mapel_rombel_id' => $gmr->id, 'nama_ujian' => $def['nama_ujian']],
                $def,
            );

            // Isi soal hanya jika belum ada (idempoten).
            if ($cbt->soal()->count() === 0) {
                foreach ($soalDef as $s) {
                    CbtSoal::create([
                        'cbt_id'          => $cbt->id,
                        'tipe_soal'       => $s['tipe_soal'],
                        'pertanyaan'      => $s['pertanyaan'],
                        'pilihan_jawaban' => $s['pilihan_jawaban'] ?? null,
                        'kunci_jawaban'   => $s['kunci_jawaban'] ?? null,
                    ]);
                }
            }
        }
    }

    /** 5 soal campuran: 3 PG + 2 uraian. */
    private function campuran(array $pgBank, array $uraianBank): array
    {
        $pg = array_map(fn ($s) => $s + ['tipe_soal' => 'pilihan_ganda'], array_slice($pgBank, 0, 3));
        $ur = array_map(fn ($s) => $s + ['tipe_soal' => 'uraian'], $uraianBank);

        return array_merge($pg, $ur);
    }
}
