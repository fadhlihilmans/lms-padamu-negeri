<?php

namespace Database\Seeders;

use App\Models\PeriodeAjaran;
use Illuminate\Database\Seeder;

class PeriodeAjaranSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['tahun_ajaran' => '2023/2024', 'semester' => 'ganjil',  'is_aktif' => false],
            ['tahun_ajaran' => '2023/2024', 'semester' => 'genap',   'is_aktif' => false],
            ['tahun_ajaran' => '2024/2025', 'semester' => 'ganjil',  'is_aktif' => false],
            ['tahun_ajaran' => '2024/2025', 'semester' => 'genap',   'is_aktif' => true],  // aktif
        ];

        foreach ($data as $row) {
            PeriodeAjaran::firstOrCreate(
                ['tahun_ajaran' => $row['tahun_ajaran'], 'semester' => $row['semester']],
                ['is_aktif' => $row['is_aktif']]
            );
        }
    }
}
