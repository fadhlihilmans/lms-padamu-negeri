<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SesiAbsensi extends Model
{
    protected $table = 'sesi_absensi';

    protected $fillable = [
        'guru_mapel_rombel_id',
        // TRANSAKSI → semester tempat data ini dibuat (Revisi Tahap 3).
        'periode_ajaran_id',
        'tanggal',
        'tanggal_buka',
        'tutup_pada',
        'status_sesi',
    ];

    protected function casts(): array
    {
        return [
            'tanggal'      => 'date',
            'tanggal_buka' => 'datetime',
            'tutup_pada'   => 'datetime',
        ];
    }

    public function guruMapelRombel(): BelongsTo
    {
        return $this->belongsTo(GuruMapelRombel::class);
    }

    public function detail(): HasMany
    {
        return $this->hasMany(AbsensiDetail::class);
    }

    /** TRANSAKSI → terikat periode (TA + semester). */
    public function periodeAjaran(): BelongsTo
    {
        return $this->belongsTo(PeriodeAjaran::class);
    }

    /** Batasi ke satu periode (TA + semester). */
    public function scopePeriode($query, $periodeId)
    {
        return $query->when($periodeId, fn ($q) => $q->where('periode_ajaran_id', $periodeId));
    }
}
