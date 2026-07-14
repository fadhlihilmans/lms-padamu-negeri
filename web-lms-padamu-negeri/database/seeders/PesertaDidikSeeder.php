<?php

namespace Database\Seeders;

use App\Models\PesertaDidik;
use App\Models\User;
use Illuminate\Database\Seeder;

class PesertaDidikSeeder extends Seeder
{
    public function run(): void
    {
        $pesertaDidiks = [
            [
                'nipd'          => '9918',
                'nisn'          => '1234567890',
                'nik'           => '3201010101010001',
                'nama_lengkap'  => 'Ahmad Fauzi',
                'jenis_kelamin' => 'L',
                'tempat_lahir'  => 'Bogor',
                'tanggal_lahir' => '2000-01-15',
                'agama'         => 'Islam',
                'no_hp'         => '08567890001',
            ],
            [
                'nipd'          => '9919',
                'nisn'          => '1234567891',
                'nik'           => '3201010101010002',
                'nama_lengkap'  => 'Siti Aminah',
                'jenis_kelamin' => 'P',
                'tempat_lahir'  => 'Bogor',
                'tanggal_lahir' => '2000-03-22',
                'agama'         => 'Islam',
                'no_hp'         => null,
            ],
            [
                'nipd'          => '9920',
                'nisn'          => null,
                'nik'           => null,
                'nama_lengkap'  => 'Budi Santoso',
                'jenis_kelamin' => 'L',
                'tempat_lahir'  => 'Jakarta',
                'tanggal_lahir' => '1999-07-08',
                'agama'         => 'Islam',
                'no_hp'         => '08567890003',
            ],
            [
                'nipd'          => '9921',
                'nisn'          => '9876543210',
                'nik'           => '3201010101010004',
                'nama_lengkap'  => 'Dewi Lestari',
                'jenis_kelamin' => 'P',
                'tempat_lahir'  => 'Depok',
                'tanggal_lahir' => '2001-11-30',
                'agama'         => 'Islam',
                'no_hp'         => null,
            ],
            [
                'nipd'          => '9922',
                'nisn'          => null,
                'nik'           => '3201010101010005',
                'nama_lengkap'  => 'Eko Prasetyo',
                'jenis_kelamin' => 'L',
                'tempat_lahir'  => 'Bekasi',
                'tanggal_lahir' => '1998-05-14',
                'agama'         => 'Kristen',
                'no_hp'         => '08567890005',
            ],
        ];

        foreach ($pesertaDidiks as $data) {
            $user = User::create([
                'username'           => $data['nipd'],
                'password'           => $data['nipd'],
                'is_change_password' => false,
                'is_active'          => true,
            ]);
            $user->assignRole('peserta_didik');

            PesertaDidik::create([
                'user_id'        => $user->id,
                'nipd'           => $data['nipd'],
                'nisn'           => $data['nisn'],
                'nik'            => $data['nik'],
                'nama_lengkap'   => $data['nama_lengkap'],
                'jenis_kelamin'  => $data['jenis_kelamin'],
                'tempat_lahir'   => $data['tempat_lahir'],
                'tanggal_lahir'  => $data['tanggal_lahir'],
                'agama'          => $data['agama'],
                'no_hp'          => $data['no_hp'],
                'status_akademik' => 'aktif',
            ]);
        }
    }
}
