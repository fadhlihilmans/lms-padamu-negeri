<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JadwalPelajaran extends Model
{
    protected $table = 'jadwal_pelajaran';

    protected $fillable = [
        'guru_mapel_rombel_id',
        // TRANSAKSI → semester tempat data ini dibuat (Revisi Tahap 3).
        'periode_ajaran_id',
        'hari',
        'jam_mulai',
        'jam_selesai',
    ];

    public function guruMapelRombel(): BelongsTo
    {
        return $this->belongsTo(GuruMapelRombel::class);
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
