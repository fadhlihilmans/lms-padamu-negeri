<?php

namespace Database\Seeders;

use App\Models\Wilayah;
use Illuminate\Database\Seeder;

class WilayahSeeder extends Seeder
{
    public function run(): void
    {
        $wilayah = ['Botolambat', 'Pondok 1', 'Bakalan'];

        foreach ($wilayah as $nama) {
            Wilayah::firstOrCreate(['nama' => $nama]);
        }
    }
}
