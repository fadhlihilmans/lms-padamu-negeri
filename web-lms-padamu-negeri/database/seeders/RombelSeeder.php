<?php

namespace Database\Seeders;

use App\Models\PeriodeAjaran;
use App\Models\Paket;
use App\Models\Rombel;
use App\Models\Tingkat;
use App\Models\Wilayah;
use Illuminate\Database\Seeder;

class RombelSeeder extends Seeder
{
    public function run(): void
    {
        $periode = PeriodeAjaran::where('is_aktif', true)->firstOrFail();

        $paketC  = Paket::where('nama', 'Paket C')->firstOrFail();
        $paketB  = Paket::where('nama', 'Paket B')->firstOrFail();

        $kelas10 = Tingkat::where('nama', 'Kelas 10')->where('paket_id', $paketC->id)->firstOrFail();
        $kelas7  = Tingkat::where('nama', 'Kelas 7')->where('paket_id', $paketB->id)->firstOrFail();

        $botolambat = Wilayah::where('nama', 'Botolambat')->firstOrFail();
        $pondok1    = Wilayah::where('nama', 'Pondok 1')->firstOrFail();

        $rombels = [
            // Rombel 1: Botolambat – Paket C – Kelas 10
            [
                'periode_ajaran_id' => $periode->id,
                'wilayah_id'        => $botolambat->id,
                'paket_id'          => $paketC->id,
                'tingkat_id'        => $kelas10->id,
                'wali_kelas_id'     => null,
                'nama'              => "Kelas 10 Botolambat Paket C – TA {$periode->tahun_ajaran}",
            ],
            // Rombel 2: Pondok 1 – Paket B – Kelas 7
            [
                'periode_ajaran_id' => $periode->id,
                'wilayah_id'        => $pondok1->id,
                'paket_id'          => $paketB->id,
                'tingkat_id'        => $kelas7->id,
                'wali_kelas_id'     => null,
                'nama'              => "Kelas 7 Pondok 1 Paket B – TA {$periode->tahun_ajaran}",
            ],
        ];

        foreach ($rombels as $data) {
            Rombel::firstOrCreate(
                [
                    'periode_ajaran_id' => $data['periode_ajaran_id'],
                    'wilayah_id'        => $data['wilayah_id'],
                    'paket_id'          => $data['paket_id'],
                    'tingkat_id'        => $data['tingkat_id'],
                ],
                $data
            );
        }
    }
}
