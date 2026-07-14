<?php

namespace Database\Seeders;

use App\Models\PesertaDidik;
use App\Models\PesertaDidikOrtu;
use Illuminate\Database\Seeder;

class PesertaDidikOrtuSeeder extends Seeder
{
    public function run(): void
    {
        // NIPD diambil DINAMIS dari PesertaDidikSeeder (urut id) — jangan hardcode,
        // supaya seeder ini tidak rusak bila daftar NIPD diubah.
        $daftarNipd = \App\Models\PesertaDidik::orderBy('id')->pluck('nipd')->all();

        // Tiap peserta didik: ayah + ibu; sebagian ada wali.
        $ortu = [
            $daftarNipd[0] => [
                ['jenis' => 'ayah', 'nama' => 'Sukarno',      'no_hp' => '081200000018', 'hubungan_wali' => null],
                ['jenis' => 'ibu',  'nama' => 'Sumarni',      'no_hp' => null,           'hubungan_wali' => null],
            ],
            $daftarNipd[1] => [
                ['jenis' => 'ayah', 'nama' => 'Hasan Basri',  'no_hp' => '081200000019', 'hubungan_wali' => null],
                ['jenis' => 'ibu',  'nama' => 'Aisyah',       'no_hp' => '081200000119', 'hubungan_wali' => null],
            ],
            $daftarNipd[2] => [
                ['jenis' => 'ayah', 'nama' => 'Slamet Riyadi', 'no_hp' => null,          'hubungan_wali' => null],
                ['jenis' => 'ibu',  'nama' => 'Wagiyem',       'no_hp' => '081200000020', 'hubungan_wali' => null],
                ['jenis' => 'wali', 'nama' => 'Paijo',         'no_hp' => '081200000220', 'hubungan_wali' => 'Paman'],
            ],
            $daftarNipd[3] => [
                ['jenis' => 'ayah', 'nama' => 'Bambang Sutrisno', 'no_hp' => '081200000021', 'hubungan_wali' => null],
                ['jenis' => 'ibu',  'nama' => 'Lestari',          'no_hp' => null,           'hubungan_wali' => null],
            ],
            $daftarNipd[4] => [
                ['jenis' => 'wali', 'nama' => 'Yohanes Prasetyo', 'no_hp' => '081200000022', 'hubungan_wali' => 'Kakak Kandung'],
            ],
        ];

        foreach ($ortu as $nipd => $rows) {
            $pd = PesertaDidik::where('nipd', $nipd)->first();
            if (! $pd) {
                continue;
            }

            foreach ($rows as $row) {
                PesertaDidikOrtu::firstOrCreate(
                    ['peserta_didik_id' => $pd->id, 'jenis' => $row['jenis']],
                    $row,
                );
            }
        }
    }
}
