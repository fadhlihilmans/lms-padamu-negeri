<?php

namespace Database\Seeders;

use App\Models\AbsensiDetail;
use App\Models\GuruMapelRombel;
use App\Models\PeriodeAjaran;
use App\Models\PesertaDidikRombel;
use App\Models\SesiAbsensi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Mengisi `sesi_absensi` + `absensi_detail`.
 * - 1 sesi HARI INI (terbuka): sebagian PD sudah absen, sisanya belum
 *   (agar dashboard PD menampilkan status "belum/sudah" dan rekap admin terisi).
 * - Beberapa sesi lampau (ditutup) dengan status kehadiran bervariasi.
 */
class SesiAbsensiSeeder extends Seeder
{
    public function run(): void
    {
        $periode = PeriodeAjaran::where('is_aktif', true)->firstOrFail();

        // [wilayah, mapel] => daftar sesi
        $config = [
            ['Botolambat', 'Matematika', [
                // hari ini, terbuka — hanya PD index 0 yang sudah absen
                ['offset' => 0,  'status_sesi' => 'terbuka', 'detail' => [0 => 'hadir']],
                // 2 hari lalu, ditutup — lengkap
                ['offset' => -2, 'status_sesi' => 'ditutup', 'detail' => [0 => 'hadir', 1 => 'izin', 2 => 'alpa']],
                // 5 hari lalu, ditutup — semua hadir
                ['offset' => -5, 'status_sesi' => 'ditutup', 'detail' => [0 => 'hadir', 1 => 'hadir', 2 => 'hadir']],
            ]],
            ['Pondok 1', 'Ilmu Pengetahuan Alam (IPA)', [
                ['offset' => -3, 'status_sesi' => 'ditutup', 'detail' => [0 => 'hadir', 1 => 'sakit']],
            ]],
        ];

        foreach ($config as [$wilayah, $mapel, $sesiList]) {
            $gmr = GuruMapelRombel::with('guru')
                ->where('tahun_ajaran', $periode->tahun_ajaran)
                ->whereHas('rombel.wilayah', fn ($q) => $q->where('nama', $wilayah))
                ->whereHas('mapel', fn ($q) => $q->where('nama', $mapel))
                ->first();
            if (! $gmr) {
                continue;
            }

            $guruUserId = $gmr->guru?->user_id;

            $pdIds = PesertaDidikRombel::where('rombel_id', $gmr->rombel_id)
                ->orderBy('peserta_didik_id')
                ->pluck('peserta_didik_id')
                ->values();

            foreach ($sesiList as $sesiDef) {
                $tanggal = Carbon::today()->addDays($sesiDef['offset']);

                if ($sesiDef['offset'] === 0) {
                    $bukaAt  = Carbon::now()->subHour();
                    $tutupAt = Carbon::now()->addHours(3);
                } else {
                    $bukaAt  = (clone $tanggal)->setTime(7, 30);
                    $tutupAt = (clone $tanggal)->setTime(9, 0);
                }

                $sesi = SesiAbsensi::firstOrCreate(
                    ['guru_mapel_rombel_id' => $gmr->id, 'tanggal' => $tanggal->toDateString()],
                    [
                        'periode_ajaran_id' => $periode->id,
                        'tanggal_buka' => $bukaAt,
                        'tutup_pada'   => $tutupAt,
                        'status_sesi'  => $sesiDef['status_sesi'],
                    ],
                );

                foreach ($sesiDef['detail'] as $idx => $status) {
                    $pdId = $pdIds[$idx] ?? null;
                    if (! $pdId) {
                        continue;
                    }

                    // 'alpa' dianggap ditandai manual oleh guru; lainnya klik mandiri PD.
                    $manual = $status === 'alpa';

                    AbsensiDetail::firstOrCreate(
                        ['sesi_absensi_id' => $sesi->id, 'peserta_didik_id' => $pdId],
                        [
                            'status'             => $status,
                            'waktu_klik'         => $manual ? null : (clone $bukaAt)->addMinutes(5),
                            'diubah_manual_oleh' => $manual ? $guruUserId : null,
                        ],
                    );
                }
            }
        }
    }
}
