<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbsensiDetail extends Model
{
    protected $table = 'absensi_detail';

    protected $fillable = [
        'sesi_absensi_id',
        'peserta_didik_id',
        'status',
        'waktu_klik',
        'diubah_manual_oleh',
    ];

    protected function casts(): array
    {
        return [
            'waktu_klik' => 'datetime',
        ];
    }

    public function sesiAbsensi(): BelongsTo
    {
        return $this->belongsTo(SesiAbsensi::class);
    }

    public function pesertaDidik(): BelongsTo
    {
        return $this->belongsTo(PesertaDidik::class);
    }

    // Guru yang mengubah status secara manual
    public function diubahManualOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diubah_manual_oleh');
    }
}
