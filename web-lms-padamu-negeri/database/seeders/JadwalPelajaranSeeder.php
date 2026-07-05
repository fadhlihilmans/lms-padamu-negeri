<?php

namespace Database\Seeders;

use App\Models\GuruMapelRombel;
use App\Models\JadwalPelajaran;
use App\Models\PeriodeAjaran;
use Illuminate\Database\Seeder;

class JadwalPelajaranSeeder extends Seeder
{
    public function run(): void
    {
        $periode = PeriodeAjaran::where('is_aktif', true)->firstOrFail();

        // [wilayah rombel][mapel nama] => [hari, jam_mulai, jam_selesai]
        $jadwal = [
            'Botolambat' => [
                'Matematika'       => ['senin', '07:30', '09:00'],
                'Bahasa Indonesia' => ['selasa', '09:15', '10:45'],
                'Bahasa Inggris'   => ['rabu', '07:30', '09:00'],
            ],
            'Pondok 1' => [
                'Matematika'                  => ['kamis', '07:30', '09:00'],
                'Ilmu Pengetahuan Alam (IPA)' => ['jumat', '09:15', '10:45'],
            ],
        ];

        $gmrs = GuruMapelRombel::with(['mapel', 'rombel.wilayah'])
            ->where('periode_ajaran_id', $periode->id)
            ->get();

        foreach ($gmrs as $gmr) {
            $wilayah = $gmr->rombel?->wilayah?->nama;
            $mapel   = $gmr->mapel?->nama;
            $slot    = $jadwal[$wilayah][$mapel] ?? null;
            if (! $slot) {
                continue;
            }

            JadwalPelajaran::firstOrCreate(
                [
                    'guru_mapel_rombel_id' => $gmr->id,
                    'hari'                 => $slot[0],
                ],
                [
                    'jam_mulai'   => $slot[1],
                    'jam_selesai' => $slot[2],
                ],
            );
        }
    }
}
