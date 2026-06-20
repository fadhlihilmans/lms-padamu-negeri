<?php

namespace Database\Seeders;

use App\Models\Mapel;
use Illuminate\Database\Seeder;

class MapelSeeder extends Seeder
{
    public function run(): void
    {
        $mapel = [
            'Pendidikan Agama dan Budi Pekerti',
            'Pendidikan Kewarganegaraan',
            'Bahasa Indonesia',
            'Matematika',
            'Ilmu Pengetahuan Alam (IPA)',
            'Ilmu Pengetahuan Sosial (IPS)',
            'Bahasa Inggris',
            'Seni Budaya',
            'Pendidikan Jasmani dan Kesehatan (PJOK)',
        ];

        foreach ($mapel as $nama) {
            Mapel::firstOrCreate(['nama' => $nama]);
        }
    }
}
