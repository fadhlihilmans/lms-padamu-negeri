<?php

namespace Database\Seeders;

use App\Models\GuruMapelRombel;
use App\Models\PeriodeAjaran;
use App\Models\PesertaDidikRombel;
use App\Models\Rapor;
use App\Models\RaporNilaiKomponen;
use App\Models\RaporNilaiMapel;
use App\Models\KonfigurasiGrade;
use App\Models\Rombel;
use App\Models\Wilayah;
use App\Services\RaporService;
use Illuminate\Database\Seeder;

/**
 * Mengisi `rapor` + `rapor_nilai_mapel` + `rapor_nilai_komponen`.
 * Rombel Botolambat: nilai lengkap semua mapel & sudah TERBIT (contoh rapor final).
 * Grade dikonversi lewat GradeService (konfigurasi_grade), komponen mengikuti
 * RaporService::KOMPONEN (Pengetahuan, Keterampilan).
 */
class RaporSeeder extends Seeder
{
    public function run(): void
    {
        $periode = PeriodeAjaran::where('is_aktif', true)->firstOrFail();

        // Baca rentang grade langsung dari tabel (hindari cache GradeService saat seeding).
        $gradeRanges = KonfigurasiGrade::orderByDesc('nilai_min')->get();

        $wilayah = Wilayah::where('nama', 'Botolambat')->first();
        if (! $wilayah) {
            return;
        }

        $rombel = Rombel::with('waliKelas')
            ->where('tahun_ajaran', $periode->tahun_ajaran)
            ->where('wilayah_id', $wilayah->id)
            ->first();
        if (! $rombel) {
            return;
        }

        $waliUserId = $rombel->waliKelas?->user_id;

        // Mapel yang diajarkan di rombel ini + guru pengampunya.
        $gmrs = GuruMapelRombel::with(['mapel', 'guru'])
            ->where('rombel_id', $rombel->id)
            ->where('tahun_ajaran', $periode->tahun_ajaran)
            ->get();

        // PD anggota rombel (urut stabil).
        $pdIds = PesertaDidikRombel::where('rombel_id', $rombel->id)
            ->orderBy('peserta_didik_id')
            ->pluck('peserta_didik_id')
            ->values();

        // Nilai dasar per PD (index anggota) → dimodifikasi per mapel & komponen.
        $baseNilai = [0 => 85, 1 => 76, 2 => 66];

        foreach ($pdIds as $idx => $pdId) {
            $rapor = Rapor::firstOrCreate(
                ['peserta_didik_id' => $pdId, 'periode_ajaran_id' => $periode->id],
                [
                    'rombel_id'          => $rombel->id,
                    'catatan_wali_kelas' => 'Pertahankan semangat belajar dan tingkatkan kedisiplinan.',
                    'status'             => 'terbit',
                    'diterbitkan_oleh'   => $waliUserId,
                ],
            );

            foreach ($gmrs->values() as $m => $gmr) {
                $rnm = RaporNilaiMapel::firstOrCreate(
                    ['rapor_id' => $rapor->id, 'mapel_id' => $gmr->mapel_id],
                    [
                        'diinput_oleh'  => $gmr->guru?->user_id,
                        'catatan_mapel' => null,
                    ],
                );

                // Isi komponen hanya bila belum ada.
                if ($rnm->komponen()->count() > 0) {
                    continue;
                }

                foreach (RaporService::KOMPONEN as $k => $namaKomponen) {
                    $base  = $baseNilai[$idx] ?? 70;
                    $nilai = min(100, $base + ($m * 2) + ($k * 3));

                    RaporNilaiKomponen::create([
                        'rapor_nilai_mapel_id' => $rnm->id,
                        'nama_komponen'        => $namaKomponen,
                        // SAS/SAT diinput manual → tidak punya nilai referensi.
                        'nilai_referensi'      => $namaKomponen === \App\Services\RaporService::KOMPONEN_SAS ? null : $nilai,
                        'nilai_akhir'          => $nilai,
                        'grade'                => $this->konversiGrade($gradeRanges, $nilai),
                        'catatan'              => null,
                    ]);
                }
            }
        }
    }

    /** Konversi nilai → grade huruf berdasarkan rentang konfigurasi_grade. */
    private function konversiGrade($ranges, int $nilai): string
    {
        foreach ($ranges as $g) {
            if ($nilai >= $g->nilai_min && $nilai <= $g->nilai_max) {
                return $g->grade;
            }
        }

        return 'D';
    }
}
