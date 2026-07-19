<?php

namespace App\Services;

use App\Models\Cbt;
use App\Models\GuruMapelRombel;
use App\Models\HasilCbt;
use App\Models\Rapor;
use App\Models\Tugas;
use App\Models\TugasSubmisi;

/**
 * Menghitung nilai_referensi (usulan) tiap komponen rapor dari data formatif.
 *
 * KOMPONEN v2 (Revisi Tahap 4) — MENGGANTI Pengetahuan/Keterampilan:
 *   - TUGAS   ← gabungan BERBOBOT rata-rata Tugas + rata-rata CBT
 *               (bobot dari `konfigurasi_nilai`, default 60:40)
 *   - SAS/SAT ← INPUT MANUAL guru, tidak memakai CBT → referensi selalu null
 *
 * Nilai referensi TIDAK di-cache permanen — selalu dihitung ulang saat form
 * dibuka (docs/database.md catatan rapor_nilai_komponen).
 */
class RaporService
{
    public const KOMPONEN_TUGAS = 'TUGAS';
    public const KOMPONEN_SAS   = 'SAS/SAT';

    /** Komponen nilai v2. Urutan menentukan urutan kolom di form penilaian. */
    public const KOMPONEN = [self::KOMPONEN_TUGAS, self::KOMPONEN_SAS];

    /** Komponen yang punya nilai referensi otomatis (SAS/SAT manual → tidak ada). */
    public const KOMPONEN_BERREFERENSI = [self::KOMPONEN_TUGAS];

    /**
     * @param  int[]  $pesertaDidikIds
     * @return array<int, array{'TUGAS': ?int, 'SAS/SAT': ?int}>
     */
    public function referensiForGmr(GuruMapelRombel $gmr, array $pesertaDidikIds): array
    {
        if (empty($pesertaDidikIds)) {
            return [];
        }

        $cbtIds   = Cbt::where('guru_mapel_rombel_id', $gmr->id)->pluck('id');
        $tugasIds = Tugas::where('guru_mapel_rombel_id', $gmr->id)->pluck('id');

        // Rata-rata nilai CBT peserta didik.
        $rataCbt = $cbtIds->isEmpty() ? collect() : HasilCbt::query()
            ->whereIn('cbt_id', $cbtIds)
            ->whereIn('peserta_didik_id', $pesertaDidikIds)
            ->whereNotNull('nilai_akhir')
            ->selectRaw('peserta_didik_id, AVG(nilai_akhir) as rata')
            ->groupBy('peserta_didik_id')
            ->pluck('rata', 'peserta_didik_id');

        // Rata-rata nilai Tugas peserta didik.
        $rataTugas = $tugasIds->isEmpty() ? collect() : TugasSubmisi::query()
            ->whereIn('tugas_id', $tugasIds)
            ->whereIn('peserta_didik_id', $pesertaDidikIds)
            ->whereNotNull('nilai')
            ->selectRaw('peserta_didik_id, AVG(nilai) as rata')
            ->groupBy('peserta_didik_id')
            ->pluck('rata', 'peserta_didik_id');

        $bobot = app(NilaiConfigService::class);

        $out = [];
        foreach ($pesertaDidikIds as $pid) {
            // Penting: bila PD belum mengerjakan CBT, TUGAS TIDAK jadi kosong —
            // bobot dinormalisasi sehingga nilai Tugas dipakai penuh. Inilah
            // perbaikan bug "Belum Lengkap padahal tugas sudah lengkap".
            $out[$pid] = [
                self::KOMPONEN_TUGAS => $bobot->komponenTugas(
                    isset($rataTugas[$pid]) ? (float) $rataTugas[$pid] : null,
                    isset($rataCbt[$pid])   ? (float) $rataCbt[$pid]   : null,
                ),
                self::KOMPONEN_SAS   => null,   // manual — tidak ada usulan otomatis
            ];
        }

        return $out;
    }

    /** Ambil nilai_akhir satu komponen pada rapor_nilai_mapel (null bila belum diisi). */
    private function nilaiKomponen($raporNilaiMapel, string $namaKomponen): ?float
    {
        $k = $raporNilaiMapel->komponen->firstWhere('nama_komponen', $namaKomponen);

        return $k?->nilai_akhir !== null ? (float) $k->nilai_akhir : null;
    }

    /**
     * Bangun data lengkap untuk pratinjau / cetak rapor 1 peserta didik.
     * Return null bila rapor belum ada.
     */
    public function buildRapor(int $pesertaDidikId, int $periodeAjaranId): ?array
    {
        $rapor = Rapor::where('peserta_didik_id', $pesertaDidikId)
            ->where('periode_ajaran_id', $periodeAjaranId)
            ->with([
                'pesertaDidik', 'rombel.paket', 'rombel.waliKelas', 'periodeAjaran',
                'nilaiMapel.mapel', 'nilaiMapel.komponen',
            ])
            ->first();

        if (! $rapor) {
            return null;
        }

        $grade = app(GradeService::class);
        $bobot = app(NilaiConfigService::class);

        // Ambil semua mapel yang diajarkan di rombel ini agar mapel yang belum dinilai
        // tetap muncul di cetak rapor dengan nilai kosong (—).
        $semuaGmr = GuruMapelRombel::with('mapel')
            ->where('rombel_id', $rapor->rombel_id)
            ->where('tahun_ajaran', $rapor->periodeAjaran->tahun_ajaran)
            ->get();

        $rows = $semuaGmr
            ->sortBy(fn ($gmr) => $gmr->mapel?->nama)
            ->values()
            ->map(function ($gmr) use ($rapor, $grade, $bobot) {
                // Cari apakah sudah ada nilai yang diinput untuk mapel ini
                $m = $rapor->nilaiMapel->firstWhere('mapel_id', $gmr->mapel_id);

                if (! $m) {
                    return [
                        'mapel'     => $gmr->mapel?->nama ?? '—',
                        'nilai'     => null,
                        'grade'     => null,
                        'deskripsi' => null,
                    ];
                }

                // Jika sudah ada, hitung nilainya
                $nilai = $bobot->nilaiMapel(
                    $this->nilaiKomponen($m, self::KOMPONEN_TUGAS),
                    $this->nilaiKomponen($m, self::KOMPONEN_SAS),
                );

                return [
                    'mapel'     => $m->mapel?->nama ?? '—',
                    'nilai'     => $nilai,
                    'grade'     => $nilai !== null ? $grade->konversi($nilai) : null,
                    'deskripsi' => $m->catatan_mapel,
                ];
            });

        $s = app(SettingService::class);

        return [
            'rapor'       => $rapor,
            'pd'          => $rapor->pesertaDidik,
            'rombel'      => $rapor->rombel,
            'periode'     => $rapor->periodeAjaran,
            'waliKelas'   => $rapor->rombel?->waliKelas,
            'rows'        => $rows,
            'catatanWali' => $rapor->catatan_wali_kelas,
            'status'      => $rapor->status,
            'kop'         => [
                'kabupaten'    => $s->get('nama_kabupaten', 'Pemerintah Kabupaten Batang'),
                'dinas'        => $s->get('nama_dinas', 'Dinas Pendidikan dan Kebudayaan'),
                'nama_pkbm'    => $s->get('nama_pkbm', 'PKBM Padamu Negeri'),
                'alamat'       => $s->get('alamat_pkbm', 'Jl. Pendidikan No. 123, Kabupaten Batang, Jawa Tengah'),
                'telepon'      => $s->get('telepon_pkbm', ''),
                'email'        => $s->get('email_pkbm', ''),
                'logo_pkbm'    => $s->get('logo_pkbm_path', null),
                'logo_kab'     => $s->get('logo_kabupaten_path', null),
                'kepala_nama'  => $s->get('kepala_pkbm_nama', ''),
                'kepala_nip'   => $s->get('kepala_pkbm_nip', ''),
            ],
        ];
    }
}
