<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rapor extends Model
{
    use SoftDeletes;

    protected $table = 'rapor';

    protected $fillable = [
        'peserta_didik_id',
        'periode_ajaran_id',
        'rombel_id',
        'catatan_wali_kelas',
        'status',
        'diterbitkan_oleh',
    ];

    public function pesertaDidik(): BelongsTo
    {
        return $this->belongsTo(PesertaDidik::class);
    }

    public function periodeAjaran(): BelongsTo
    {
        return $this->belongsTo(PeriodeAjaran::class);
    }

    public function rombel(): BelongsTo
    {
        return $this->belongsTo(Rombel::class);
    }

    // Wali Kelas yang menerbitkan
    public function diterbitkanOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diterbitkan_oleh');
    }

    public function nilaiMapel(): HasMany
    {
        return $this->hasMany(RaporNilaiMapel::class);
    }
}
