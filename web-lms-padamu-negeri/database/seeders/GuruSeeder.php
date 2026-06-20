<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\User;
use Illuminate\Database\Seeder;

class GuruSeeder extends Seeder
{
    public function run(): void
    {
        $gurus = [
            ['nip' => 'G001', 'nama_lengkap' => 'Ahmad Supriyadi, S.Pd.',   'no_hp' => '081234567001'],
            ['nip' => 'G002', 'nama_lengkap' => 'Siti Rahayu, S.Pd.',       'no_hp' => '081234567002'],
            ['nip' => 'G003', 'nama_lengkap' => 'Budi Santoso, S.Pd.I.',    'no_hp' => '081234567003'],
            ['nip' => 'G004', 'nama_lengkap' => 'Dewi Anggraini, S.Pd.',    'no_hp' => '081234567004'],
            ['nip' => 'G005', 'nama_lengkap' => 'Rudi Hartono, S.Kom.',     'no_hp' => null],
        ];

        foreach ($gurus as $data) {
            $user = User::create([
                'username'           => $data['nip'],
                'password'           => $data['nip'],
                'is_change_password' => false,
                'is_active'          => true,
            ]);
            $user->assignRole('guru');

            Guru::create([
                'user_id'      => $user->id,
                'nip'          => $data['nip'],
                'nama_lengkap' => $data['nama_lengkap'],
                'no_hp'        => $data['no_hp'],
            ]);
        }
    }
}
