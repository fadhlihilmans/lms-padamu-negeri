<?php

namespace Database\Seeders;

use App\Models\KonfigurasiGrade;
use Illuminate\Database\Seeder;

class GradeSeeder extends Seeder
{
    public function run(): void
    {
        // Default rentang grade (docs/database.md Bagian 8.1).
        $default = [
            ['grade' => 'A', 'nilai_min' => 85, 'nilai_max' => 100],
            ['grade' => 'B', 'nilai_min' => 75, 'nilai_max' => 84],
            ['grade' => 'C', 'nilai_min' => 65, 'nilai_max' => 74],
            ['grade' => 'D', 'nilai_min' => 0,  'nilai_max' => 64],
        ];

        foreach ($default as $row) {
            KonfigurasiGrade::updateOrCreate(['grade' => $row['grade']], $row);
        }
    }
}
