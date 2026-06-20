<?php

namespace Database\Seeders;

use App\Models\Paket;
use App\Models\Tingkat;
use Illuminate\Database\Seeder;

class TingkatSeeder extends Seeder
{
    public function run(): void
    {
        $map = [
            'Paket A' => ['Kelas 1', 'Kelas 2', 'Kelas 3', 'Kelas 4', 'Kelas 5', 'Kelas 6'],
            'Paket B' => ['Kelas 7', 'Kelas 8', 'Kelas 9'],
            'Paket C' => ['Kelas 10', 'Kelas 11', 'Kelas 12'],
        ];

        foreach ($map as $namaPaket => $tingkatan) {
            $paket = Paket::where('nama', $namaPaket)->first();
            if (! $paket) {
                continue;
            }
            foreach ($tingkatan as $nama) {
                Tingkat::firstOrCreate(
                    ['paket_id' => $paket->id, 'nama' => $nama]
                );
            }
        }
    }
}
