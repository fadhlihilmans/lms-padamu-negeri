<?php

namespace Database\Seeders;

use App\Models\GuruMapelRombel;
use App\Models\Materi;
use App\Models\MateriLampiran;
use App\Models\PeriodeAjaran;
use Illuminate\Database\Seeder;

class MateriSeeder extends Seeder
{
    public function run(): void
    {
        $periode = PeriodeAjaran::where('is_aktif', true)->firstOrFail();

        // [wilayah, mapel] => daftar materi (judul, isi, lampiran?)
        $materiList = [
            ['Botolambat', 'Matematika', [
                [
                    'judul' => 'Konsep Dasar Aljabar',
                    'isi'   => '<p>Aljabar adalah cabang matematika yang mempelajari operasi hitung menggunakan simbol/variabel. Pada pertemuan ini kita membahas suku, koefisien, dan variabel.</p>',
                    'lampiran' => [
                        ['tipe' => 'link_video', 'url' => 'https://www.youtube.com/watch?v=NybHckSEQBI', 'nama_asli' => 'Pengantar Aljabar'],
                    ],
                ],
                [
                    'judul' => 'Persamaan Linear Satu Variabel',
                    'isi'   => '<p>Materi ini membahas cara menyelesaikan persamaan linear satu variabel (PLSV) beserta contoh soal dan pembahasannya.</p>',
                    'lampiran' => [],
                ],
            ]],
            ['Botolambat', 'Bahasa Indonesia', [
                [
                    'judul' => 'Struktur Teks Eksposisi',
                    'isi'   => '<p>Teks eksposisi terdiri atas tesis, argumentasi, dan penegasan ulang. Pahami tiap bagian melalui contoh berikut.</p>',
                    'lampiran' => [],
                ],
            ]],
            ['Pondok 1', 'Ilmu Pengetahuan Alam (IPA)', [
                [
                    'judul' => 'Klasifikasi Makhluk Hidup',
                    'isi'   => '<p>Makhluk hidup diklasifikasikan berdasarkan ciri-ciri tertentu ke dalam kingdom. Materi ini mengenalkan dasar taksonomi.</p>',
                    'lampiran' => [],
                ],
            ]],
        ];

        foreach ($materiList as [$wilayah, $mapel, $items]) {
            $gmr = $this->resolveGmr($periode->id, $wilayah, $mapel);
            if (! $gmr) {
                continue;
            }

            foreach ($items as $item) {
                $materi = Materi::firstOrCreate(
                    ['guru_mapel_rombel_id' => $gmr->id, 'judul' => $item['judul']],
                    ['isi' => $item['isi']],
                );

                foreach ($item['lampiran'] as $i => $lamp) {
                    MateriLampiran::firstOrCreate(
                        ['materi_id' => $materi->id, 'url' => $lamp['url'] ?? null, 'file_path' => $lamp['file_path'] ?? null],
                        [
                            'tipe'      => $lamp['tipe'],
                            'nama_asli' => $lamp['nama_asli'] ?? null,
                            'urutan'    => $i,
                        ],
                    );
                }
            }
        }
    }

    private function resolveGmr(int $periodeId, string $wilayah, string $mapel): ?GuruMapelRombel
    {
        return GuruMapelRombel::where('periode_ajaran_id', $periodeId)
            ->whereHas('rombel.wilayah', fn ($q) => $q->where('nama', $wilayah))
            ->whereHas('mapel', fn ($q) => $q->where('nama', $mapel))
            ->first();
    }
}
