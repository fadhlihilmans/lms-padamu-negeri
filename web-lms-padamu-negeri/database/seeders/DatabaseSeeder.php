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
            KonfigurasiNilaiSeeder::class,   // bobot penilaian (Tahap 4)
            SettingSeeder::class,

            // ── Data dummy end-to-end (ikuti urutan dependency) ──
            PesertaDidikAlamatSeeder::class,
            PesertaDidikOrtuSeeder::class,
            PesertaDidikRombelSeeder::class,
            GuruMapelRombelSeeder::class,   // + set wali kelas rombel
            JadwalPelajaranSeeder::class,
            MateriSeeder::class,            // + materi_lampiran
            TugasSeeder::class,             // + tugas_submisi
            CbtSeeder::class,               // + cbt_soal
            HasilCbtSeeder::class,          // + cbt_jawaban_peserta
            SesiAbsensiSeeder::class,       // + absensi_detail
            KenaikanKelasSeeder::class,
            RaporSeeder::class,             // + rapor_nilai_mapel + rapor_nilai_komponen
        ]);
    }
}
