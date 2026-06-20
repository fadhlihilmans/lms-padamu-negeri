<?php

namespace Database\Seeders;

use App\Models\Paket;
use Illuminate\Database\Seeder;

class PaketSeeder extends Seeder
{
    public function run(): void
    {
        $paket = ['Paket A', 'Paket B', 'Paket C'];

        foreach ($paket as $nama) {
            Paket::firstOrCreate(['nama' => $nama]);
        }
    }
}
