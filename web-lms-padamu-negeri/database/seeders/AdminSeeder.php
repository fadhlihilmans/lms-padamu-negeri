<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'password'            => bcrypt('admin'),
                'is_change_password'  => false,  // akan muncul modal ganti password saat login pertama
                'is_active'           => true,
            ]
        );

        $admin->assignRole('admin');
    }
}
