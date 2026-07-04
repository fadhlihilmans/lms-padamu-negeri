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
    private const CACHE_KEY = 'konfigurasi_grade_all';

    /** Semua rentang grade (dicache; invalidasi saat Admin mengubah). */
    public function all()
    {
        return Cache::remember(self::CACHE_KEY, now()->addHour(), function () {
            return KonfigurasiGrade::orderByDesc('nilai_min')->get();
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
            if ($nilai >= $g->nilai_min && $nilai <= $g->nilai_max) {
                return $g->grade;
            }
        }

        return 'D';
    }
}
