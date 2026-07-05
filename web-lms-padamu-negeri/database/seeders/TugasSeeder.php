<?php

namespace Database\Seeders;

use App\Models\GuruMapelRombel;
use App\Models\PeriodeAjaran;
use App\Models\PesertaDidikRombel;
use App\Models\Tugas;
use App\Models\TugasSubmisi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Mengisi `tugas` beserta `tugas_submisi` (sebagian sudah dinilai, sebagian belum).
 */
class TugasSeeder extends Seeder
{
    public function run(): void
    {
        $periode = PeriodeAjaran::where('is_aktif', true)->firstOrFail();

        // [wilayah, mapel] => daftar tugas
        // submisi: [urutan PD anggota (0-based) => nilai|null]
        $tugasList = [
            ['Botolambat', 'Matematika', [
                [
                    'judul'    => 'Latihan Soal Aljabar',
                    'deskripsi' => 'Kerjakan soal nomor 1–10 pada buku paket halaman 24.',
                    'deadline' => Carbon::now()->subDays(3),
                    'submisi'  => [0 => 85, 1 => 78, 2 => null], // PD ke-3 belum dinilai
                ],
                [
                    'judul'    => 'PR Persamaan Linear',
                    'deskripsi' => 'Selesaikan 5 persamaan linear satu variabel beserta langkahnya.',
                    'deadline' => Carbon::now()->addDays(5),
                    'submisi'  => [0 => null], // baru 1 PD mengumpulkan, belum dinilai
                ],
            ]],
            ['Botolambat', 'Bahasa Indonesia', [
                [
                    'judul'    => 'Menulis Teks Eksposisi',
                    'deskripsi' => 'Tulis teks eksposisi minimal 3 paragraf bertema pendidikan.',
                    'deadline' => Carbon::now()->subDays(1),
                    'submisi'  => [0 => 80, 1 => null],
                ],
            ]],
            ['Pondok 1', 'Ilmu Pengetahuan Alam (IPA)', [
                [
                    'judul'    => 'Rangkuman Klasifikasi Makhluk Hidup',
                    'deskripsi' => 'Buat rangkuman satu halaman tentang lima kingdom.',
                    'deadline' => Carbon::now()->subDays(2),
                    'submisi'  => [0 => 90, 1 => null],
                ],
            ]],
        ];

        foreach ($tugasList as [$wilayah, $mapel, $items]) {
            $gmr = GuruMapelRombel::where('periode_ajaran_id', $periode->id)
                ->whereHas('rombel.wilayah', fn ($q) => $q->where('nama', $wilayah))
                ->whereHas('mapel', fn ($q) => $q->where('nama', $mapel))
                ->first();
            if (! $gmr) {
                continue;
            }

            // PD anggota rombel (urut stabil by id).
            $pdIds = PesertaDidikRombel::where('rombel_id', $gmr->rombel_id)
                ->orderBy('peserta_didik_id')
                ->pluck('peserta_didik_id')
                ->values();

            foreach ($items as $item) {
                $tugas = Tugas::firstOrCreate(
                    ['guru_mapel_rombel_id' => $gmr->id, 'judul' => $item['judul']],
                    ['deskripsi' => $item['deskripsi'], 'deadline' => $item['deadline']],
                );

                foreach ($item['submisi'] as $idx => $nilai) {
                    $pdId = $pdIds[$idx] ?? null;
                    if (! $pdId) {
                        continue;
                    }

                    // waktu submit sedikit sebelum deadline (atau kemarin untuk tugas future).
                    $waktuSubmit = $item['deadline']->isFuture()
                        ? Carbon::now()->subDay()
                        : (clone $item['deadline'])->subHours(6);

                    TugasSubmisi::firstOrCreate(
                        ['tugas_id' => $tugas->id, 'peserta_didik_id' => $pdId],
                        [
                            'isi_text'     => 'Jawaban tugas oleh peserta didik (data dummy).',
                            'waktu_submit' => $waktuSubmit,
                            'nilai'        => $nilai,
                        ],
                    );
                }
            }
        }
    }
}
