<?php

namespace Database\Seeders;

use App\Models\KenaikanKelas;
use App\Models\PeriodeAjaran;
use App\Models\PesertaDidik;
use App\Models\Rombel;
use App\Models\Wilayah;
use Illuminate\Database\Seeder;

/**
 * Mengisi `kenaikan_kelas` (keputusan akademik wali kelas untuk peserta didik).
 * `diputuskan_oleh` = user wali kelas rombel asal.
 */
class KenaikanKelasSeeder extends Seeder
{
    public function run(): void
    {
        $periode = PeriodeAjaran::where('is_aktif', true)->firstOrFail();

        // [wilayah rombel] => [nipd => status_keputusan]
        $keputusan = [
            'Botolambat' => [
                '1718' => 'naik',
                '1719' => 'naik',
                '1720' => 'tinggal',
            ],
            'Pondok 1' => [
                '1721' => 'naik',
                '1722' => 'pindah_wilayah',
            ],
        ];

        foreach ($keputusan as $wilayahNama => $rows) {
            $wilayah = Wilayah::where('nama', $wilayahNama)->first();
            if (! $wilayah) {
                continue;
            }

            $rombel = Rombel::with('waliKelas')
                ->where('periode_ajaran_id', $periode->id)
                ->where('wilayah_id', $wilayah->id)
                ->first();

            $diputuskanOleh = $rombel?->waliKelas?->user_id;
            if (! $rombel || ! $diputuskanOleh) {
                continue;
            }

            foreach ($rows as $nipd => $status) {
                $pd = PesertaDidik::where('nipd', $nipd)->first();
                if (! $pd) {
                    continue;
                }

                KenaikanKelas::firstOrCreate(
                    ['peserta_didik_id' => $pd->id, 'rombel_asal_id' => $rombel->id],
                    [
                        'status_keputusan' => $status,
                        'rombel_tujuan_id' => null,
                        'diputuskan_oleh'  => $diputuskanOleh,
                    ],
                );
            }
        }
    }
}
