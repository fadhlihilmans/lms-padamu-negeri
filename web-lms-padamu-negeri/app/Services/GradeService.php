<?php

namespace App\Services;

use App\Models\KonfigurasiGrade;
use Illuminate\Support\Facades\Cache;

/**
 * Konversi nilai angka (0–100) → grade huruf (A/B/C/D) berdasarkan tabel
 * konfigurasi_grade. Rentang dikelola Admin (Langkah 19); di sini hanya baca.
 */
class GradeService
{
    // v2: sengaja diganti dari 'konfigurasi_grade_all' agar entri cache lama
    // (berisi Eloquent Collection yang rawan rusak saat de-serialisasi) diabaikan.
    private const CACHE_KEY = 'konfigurasi_grade_all_v2';

    /**
     * Semua rentang grade sebagai array biasa (bukan model Eloquent), supaya
     * aman di-cache lintas request/driver — menghindari __PHP_Incomplete_Class.
     *
     * @return array<int, array{grade: string, nilai_min: int, nilai_max: int}>
     */
    public function all(): array
    {
        return Cache::remember(self::CACHE_KEY, now()->addHour(), function () {
            return KonfigurasiGrade::orderByDesc('nilai_min')
                ->get(['grade', 'nilai_min', 'nilai_max'])
                ->map(fn ($g) => [
                    'grade'     => $g->grade,
                    'nilai_min' => (int) $g->nilai_min,
                    'nilai_max' => (int) $g->nilai_max,
                ])
                ->all();
        });
    }

    public function invalidate(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /** Konversi nilai → grade. Null bila nilai null. Fallback 'D' bila tak match. */
    public function konversi(?int $nilai): ?string
    {
        if ($nilai === null) {
            return null;
        }

        foreach ($this->all() as $g) {
            if ($nilai >= $g['nilai_min'] && $nilai <= $g['nilai_max']) {
                return $g['grade'];
            }
        }

        return 'D';
    }
}
