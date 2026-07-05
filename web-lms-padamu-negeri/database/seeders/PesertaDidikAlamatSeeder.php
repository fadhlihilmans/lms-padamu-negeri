<?php

namespace Database\Seeders;

use App\Models\PesertaDidik;
use App\Models\PesertaDidikAlamat;
use Illuminate\Database\Seeder;

class PesertaDidikAlamatSeeder extends Seeder
{
    public function run(): void
    {
        $alamat = [
            '1718' => ['alamat' => 'Jl. Merdeka No. 12', 'rt' => '001', 'rw' => '002', 'dusun' => 'Krajan',   'kelurahan' => 'Botolambat', 'kecamatan' => 'Gringsing', 'kode_pos' => '51281'],
            '1719' => ['alamat' => 'Jl. Diponegoro No. 5', 'rt' => '003', 'rw' => '001', 'dusun' => 'Sawah',    'kelurahan' => 'Botolambat', 'kecamatan' => 'Gringsing', 'kode_pos' => '51281'],
            '1720' => ['alamat' => 'Jl. Kartini No. 8',    'rt' => '002', 'rw' => '004', 'dusun' => 'Tengah',   'kelurahan' => 'Sidorejo',   'kecamatan' => 'Gringsing', 'kode_pos' => '51281'],
            '1721' => ['alamat' => 'Jl. Sudirman No. 21',  'rt' => '004', 'rw' => '002', 'dusun' => 'Pondok',   'kelurahan' => 'Ketanggan',  'kecamatan' => 'Gringsing', 'kode_pos' => '51281'],
            '1722' => ['alamat' => 'Jl. Ahmad Yani No. 3', 'rt' => '001', 'rw' => '005', 'dusun' => 'Ngasinan', 'kelurahan' => 'Ketanggan',  'kecamatan' => 'Gringsing', 'kode_pos' => '51281'],
        ];

        foreach ($alamat as $nipd => $data) {
            $pd = PesertaDidik::where('nipd', $nipd)->first();
            if (! $pd) {
                continue;
            }

            PesertaDidikAlamat::firstOrCreate(
                ['peserta_didik_id' => $pd->id],
                $data,
            );
        }
    }
}
