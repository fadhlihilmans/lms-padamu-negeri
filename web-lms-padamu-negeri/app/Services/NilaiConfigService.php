<?php

namespace App\Services;

use App\Models\KonfigurasiNilai;
use Illuminate\Support\Facades\Cache;

/**
 * Akses tunggal ke BOBOT penilaian (tabel `konfigurasi_nilai`) — CLAUDE.md #9a.
 *
 * Meng-cache array biasa (bukan model Eloquent) supaya aman di-deserialisasi
 * lintas request/driver — pelajaran dari bug GradeService dulu.
 */
class NilaiConfigService
{
    private const CACHE_KEY = 'konfigurasi_nilai_all_v1';

    /** Dipakai bila tabel belum di-seed (mis. saat migrasi awal). */
    private const DEFAULTS = [
        'bobot_cbt_pg'           => 70,
        'bobot_cbt_uraian'       => 30,
        'bobot_tugas_dari_tugas' => 60,
        'bobot_tugas_dari_cbt'   => 40,
        'bobot_rapor_tugas'      => 70,
        'bobot_rapor_sas'        => 30,
    ];

    /** @return array<string,int> */
    public function all(): array
    {
        return Cache::remember(self::CACHE_KEY, now()->addHour(), function () {
            $rows = KonfigurasiNilai::pluck('value', 'key')
                ->map(fn ($v) => (int) $v)
                ->all();

            return $rows ?: self::DEFAULTS;
        });
    }

    public function get(string $key): int
    {
        return $this->all()[$key] ?? self::DEFAULTS[$key] ?? 0;
    }

    public function invalidate(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    // ── Perhitungan berbobot ──────────────────────────────────────────────────

    /**
     * Gabungkan dua nilai menurut bobotnya, DENGAN NORMALISASI.
     *
     * Inti aturan (database.md): bila salah satu sumber tidak ada (null), bobot
     * dinormalisasi ke 100% — sehingga sumber yang tersisa dipakai penuh.
     * Tanpa ini, CBT tanpa soal uraian nilainya mentok 70 dan peserta didik
     * TIDAK PERNAH bisa mendapat 100.
     *
     * @return int|null null bila kedua sumber tidak ada.
     */
    public function gabung(?float $nilaiA, ?float $nilaiB, int $bobotA, int $bobotB): ?int
    {
        $adaA = $nilaiA !== null;
        $adaB = $nilaiB !== null;

        if (! $adaA && ! $adaB) {
            return null;
        }

        // Hanya satu sumber → dipakai penuh (normalisasi 100%).
        if (! $adaB) {
            return (int) round($nilaiA);
        }
        if (! $adaA) {
            return (int) round($nilaiB);
        }

        $total = $bobotA + $bobotB;
        if ($total <= 0) {
            return (int) round(($nilaiA + $nilaiB) / 2); // pengaman bila bobot rusak
        }

        return (int) round((($nilaiA * $bobotA) + ($nilaiB * $bobotB)) / $total);
    }

    /** Nilai CBT = PG & Uraian berbobot (ternormalisasi bila hanya satu tipe). */
    public function nilaiCbt(?float $nilaiPg, ?float $nilaiUraian): ?int
    {
        return $this->gabung(
            $nilaiPg, $nilaiUraian,
            $this->get('bobot_cbt_pg'), $this->get('bobot_cbt_uraian'),
        );
    }

    /** Komponen TUGAS = rata Tugas & rata CBT berbobot. */
    public function komponenTugas(?float $rataTugas, ?float $rataCbt): ?int
    {
        return $this->gabung(
            $rataTugas, $rataCbt,
            $this->get('bobot_tugas_dari_tugas'), $this->get('bobot_tugas_dari_cbt'),
        );
    }

    /** Nilai satu mapel di rapor = TUGAS & SAS/SAT berbobot. */
    public function nilaiMapel(?float $tugas, ?float $sas): ?int
    {
        return $this->gabung(
            $tugas, $sas,
            $this->get('bobot_rapor_tugas'), $this->get('bobot_rapor_sas'),
        );
    }
}
