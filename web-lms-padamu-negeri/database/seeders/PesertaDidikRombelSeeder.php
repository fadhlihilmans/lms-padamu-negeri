<?php

namespace Database\Seeders;

use App\Models\PesertaDidik;
use App\Models\PesertaDidikRombel;
use App\Models\PeriodeAjaran;
use App\Models\Rombel;
use App\Models\Wilayah;
use Illuminate\Database\Seeder;

class PesertaDidikRombelSeeder extends Seeder
{
    public function run(): void
    {
        $periode = PeriodeAjaran::where('is_aktif', true)->firstOrFail();

        // Keanggotaan per rombel (berdasarkan wilayah rombel di periode aktif).
        $mapping = [
            'Botolambat' => ['1718', '1719', '1720'],
            'Pondok 1'   => ['1721', '1722'],
        ];

        foreach ($mapping as $wilayahNama => $nipds) {
            $wilayah = Wilayah::where('nama', $wilayahNama)->first();
            if (! $wilayah) {
                continue;
            }

            $rombel = Rombel::where('periode_ajaran_id', $periode->id)
                ->where('wilayah_id', $wilayah->id)
                ->first();
            if (! $rombel) {
                continue;
            }

            foreach ($nipds as $nipd) {
                $pd = PesertaDidik::where('nipd', $nipd)->first();
                if (! $pd) {
                    continue;
                }

                PesertaDidikRombel::firstOrCreate([
                    'peserta_didik_id' => $pd->id,
                    'rombel_id'        => $rombel->id,
                ]);
            }
        }
    }
}
