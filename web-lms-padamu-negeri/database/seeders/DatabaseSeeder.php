<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            AdminSeeder::class,
            PeriodeAjaranSeeder::class,
            WilayahSeeder::class,
            PaketSeeder::class,
            TingkatSeeder::class,
            MapelSeeder::class,
            GuruSeeder::class,
            PesertaDidikSeeder::class,
            RombelSeeder::class,
            // Langkah 18–19
            GradeSeeder::class,
            SettingSeeder::class,
        ]);
    }
}
