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
                'password'           => 'admin',
                'is_change_password' => true,
                'is_active'          => true,
            ]
        );
        $admin->assignRole('admin');

        $adminDev = User::firstOrCreate(
            ['username' => 'admindev'],
            [
                'password'           => 'admindev',
                'is_change_password' => true,
                'is_active'          => true,
            ]
        );
        $adminDev->assignRole('admin');
    }
}
