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
        // NIPD diambil DINAMIS dari PesertaDidikSeeder (urut id) — jangan hardcode,
        // supaya seeder ini tidak rusak bila daftar NIPD diubah.
        $daftarNipd = \App\Models\PesertaDidik::orderBy('id')->pluck('nipd')->all();

        $periode = PeriodeAjaran::where('is_aktif', true)->firstOrFail();

        // Keanggotaan per rombel (berdasarkan wilayah rombel di periode aktif).
        $mapping = [
            'Botolambat' => [$daftarNipd[0], $daftarNipd[1], $daftarNipd[2]],
            'Pondok 1'   => [$daftarNipd[3], $daftarNipd[4]],
        ];

        foreach ($mapping as $wilayahNama => $nipds) {
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
