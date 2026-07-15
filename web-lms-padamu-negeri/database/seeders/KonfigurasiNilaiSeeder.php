<?php

namespace Database\Seeders;

use App\Models\KonfigurasiNilai;
use Illuminate\Database\Seeder;

class KonfigurasiNilaiSeeder extends Seeder
{
    public function run(): void
    {
        // key, value, grup, label — tiap grup WAJIB total 100.
        $rows = [
            // Nilai CBT = PG 70% + Uraian 30%
            ['bobot_cbt_pg',           '70', KonfigurasiNilai::GRUP_CBT,            'Pilihan Ganda (%)'],
            ['bobot_cbt_uraian',       '30', KonfigurasiNilai::GRUP_CBT,            'Uraian (%)'],

            // Komponen TUGAS = gabungan nilai Tugas + nilai CBT
            ['bobot_tugas_dari_tugas', '60', KonfigurasiNilai::GRUP_KOMPONEN_TUGAS, 'Dari Nilai Tugas (%)'],
            ['bobot_tugas_dari_cbt',   '40', KonfigurasiNilai::GRUP_KOMPONEN_TUGAS, 'Dari Nilai CBT (%)'],

            // Nilai mapel di rapor = TUGAS 70% + SAS/SAT 30%
            ['bobot_rapor_tugas',      '70', KonfigurasiNilai::GRUP_RAPOR,          'Komponen TUGAS (%)'],
            ['bobot_rapor_sas',        '30', KonfigurasiNilai::GRUP_RAPOR,          'Komponen SAS/SAT (%)'],
        ];

        foreach ($rows as [$key, $value, $grup, $label]) {
            // firstOrCreate → jangan menimpa nilai yang sudah diubah Admin.
            KonfigurasiNilai::firstOrCreate(
                ['key' => $key],
                ['value' => $value, 'type' => 'integer', 'grup' => $grup, 'label' => $label],
            );
        }
    }
}
