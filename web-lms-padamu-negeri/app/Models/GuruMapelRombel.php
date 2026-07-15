<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GuruMapelRombel extends Model
{
    protected $table = 'guru_mapel_rombel';

    protected $fillable = [
        'guru_id',
        'mapel_id',
        'rombel_id',
        // STRUKTUR → plotting cukup 1x per Tahun Ajaran (bukan per semester).
        'tahun_ajaran',
    ];

    // ─── Scope ─────────────────────────────────────────────────────────────────

    /** Plotting pada satu Tahun Ajaran (mis. "2024/2025"). */
    public function scopeTahunAjaran($query, ?string $tahunAjaran)
    {
        return $query->when($tahunAjaran, fn ($q) => $q->where('tahun_ajaran', $tahunAjaran));
    }

    // ─── Relasi ────────────────────────────────────────────────────────────────
    // Catatan: TIDAK lagi belongsTo periode_ajaran — plotting terikat Tahun Ajaran.

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }

    public function mapel(): BelongsTo
    {
        return $this->belongsTo(Mapel::class);
    }

    public function rombel(): BelongsTo
    {
        return $this->belongsTo(Rombel::class);
    }

    public function materi(): HasMany
    {
        return $this->hasMany(Materi::class);
    }

    public function tugas(): HasMany
    {
        return $this->hasMany(Tugas::class);
    }

    public function cbt(): HasMany
    {
        return $this->hasMany(Cbt::class);
    }

    public function sesiAbsensi(): HasMany
    {
        return $this->hasMany(SesiAbsensi::class);
    }

    public function jadwalPelajaran(): HasMany
    {
        return $this->hasMany(JadwalPelajaran::class);
    }
}
