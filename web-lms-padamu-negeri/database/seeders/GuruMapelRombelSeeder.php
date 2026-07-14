<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\GuruMapelRombel;
use App\Models\Mapel;
use App\Models\PeriodeAjaran;
use App\Models\Rombel;
use App\Models\Wilayah;
use Illuminate\Database\Seeder;

/**
 * Mengisi pivot otorisasi `guru_mapel_rombel` (dasar Materi/Tugas/CBT/Absensi)
 * dan sekaligus menetapkan Wali Kelas tiap rombel (atribut `rombel.wali_kelas_id`).
 */
class GuruMapelRombelSeeder extends Seeder
{
    public function run(): void
    {
        $periode = PeriodeAjaran::where('is_aktif', true)->firstOrFail();

        // [wilayah rombel] => [wali kelas nip, [mapel nama => guru nip]]
        $config = [
            'Botolambat' => [
                'wali'  => 'g001',
                'mapel' => [
                    'Matematika'        => 'g001',
                    'Bahasa Indonesia'  => 'g002',
                    'Bahasa Inggris'    => 'g004',
                ],
            ],
            'Pondok 1' => [
                'wali'  => 'g002',
                'mapel' => [
                    'Matematika'                   => 'g001',
                    'Ilmu Pengetahuan Alam (IPA)'  => 'g005',
                ],
            ],
        ];

        foreach ($config as $wilayahNama => $cfg) {
            $wilayah = Wilayah::where('nama', $wilayahNama)->first();
            if (! $wilayah) {
                continue;
            }

            $rombel = Rombel::where('tahun_ajaran', $periode->tahun_ajaran)
                ->where('wilayah_id', $wilayah->id)
                ->first();
            if (! $rombel) {
                continue;
            }

            // Tetapkan wali kelas bila belum ada.
            $wali = Guru::where('nip', $cfg['wali'])->first();
            if ($wali && $rombel->wali_kelas_id === null) {
                $rombel->update(['wali_kelas_id' => $wali->id]);
            }

            foreach ($cfg['mapel'] as $mapelNama => $guruNip) {
                $mapel = Mapel::where('nama', $mapelNama)->first();
                $guru  = Guru::where('nip', $guruNip)->first();
                if (! $mapel || ! $guru) {
                    continue;
                }

                GuruMapelRombel::firstOrCreate([
                    'guru_id'      => $guru->id,
                    'mapel_id'     => $mapel->id,
                    'rombel_id'    => $rombel->id,
                    'tahun_ajaran' => $periode->tahun_ajaran,
                ]);
            }
        }
    }
}
