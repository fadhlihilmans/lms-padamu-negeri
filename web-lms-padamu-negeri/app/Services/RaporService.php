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
 * Interpretasi (PRD 6.11: "rata-rata Tugas + CBT"):
 *   - Pengetahuan  ← rata-rata nilai CBT (hasil_cbt.nilai_akhir)
 *   - Keterampilan ← rata-rata nilai Tugas (tugas_submisi.nilai)
 * Nilai referensi TIDAK di-cache permanen — selalu dihitung ulang saat form
 * dibuka (docs/database.md catatan rapor_nilai_komponen).
 */
class RaporService
{
    /** Komponen nilai baku v1 (lihat desain 11.2). */
    public const KOMPONEN = ['Pengetahuan', 'Keterampilan'];

    /**
     * @param  int[]  $pesertaDidikIds
     * @return array<int, array{Pengetahuan: ?int, Keterampilan: ?int}>
     */
    public function referensiForGmr(GuruMapelRombel $gmr, array $pesertaDidikIds): array
    {
        if (empty($pesertaDidikIds)) {
            return [];
        }

        $cbtIds   = Cbt::where('guru_mapel_rombel_id', $gmr->id)->pluck('id');
        $tugasIds = Tugas::where('guru_mapel_rombel_id', $gmr->id)->pluck('id');

        // Pengetahuan = rata-rata CBT.
        $pengetahuan = $cbtIds->isEmpty() ? collect() : HasilCbt::query()
            ->whereIn('cbt_id', $cbtIds)
            ->whereIn('peserta_didik_id', $pesertaDidikIds)
            ->whereNotNull('nilai_akhir')
            ->selectRaw('peserta_didik_id, AVG(nilai_akhir) as rata')
            ->groupBy('peserta_didik_id')
            ->pluck('rata', 'peserta_didik_id');

        // Keterampilan = rata-rata Tugas.
        $keterampilan = $tugasIds->isEmpty() ? collect() : TugasSubmisi::query()
            ->whereIn('tugas_id', $tugasIds)
            ->whereIn('peserta_didik_id', $pesertaDidikIds)
            ->whereNotNull('nilai')
            ->selectRaw('peserta_didik_id, AVG(nilai) as rata')
            ->groupBy('peserta_didik_id')
            ->pluck('rata', 'peserta_didik_id');

        $out = [];
        foreach ($pesertaDidikIds as $pid) {
            $out[$pid] = [
                'Pengetahuan'  => isset($pengetahuan[$pid])  ? (int) round($pengetahuan[$pid])  : null,
                'Keterampilan' => isset($keterampilan[$pid]) ? (int) round($keterampilan[$pid]) : null,
            ];
        }

        return $out;
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

        $rows = $rapor->nilaiMapel
            ->sortBy(fn ($m) => $m->mapel?->nama)
            ->values()
            ->map(function ($m) use ($grade) {
                $vals = $m->komponen->pluck('nilai_akhir')->filter(fn ($v) => $v !== null);
                $avg  = $vals->isNotEmpty() ? (int) round($vals->avg()) : null;
                return [
                    'mapel'     => $m->mapel?->nama ?? '—',
                    'nilai'     => $avg,
                    'grade'     => $avg !== null ? $grade->konversi($avg) : null,
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
