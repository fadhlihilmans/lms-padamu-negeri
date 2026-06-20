<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class KenaikanKelas extends Model
{
    use SoftDeletes;

    protected $table = 'kenaikan_kelas';

    protected $fillable = [
        'peserta_didik_id',
        'rombel_asal_id',
        'status_keputusan',
        'rombel_tujuan_id',
        'diputuskan_oleh',
    ];

    public function pesertaDidik(): BelongsTo
    {
        return $this->belongsTo(PesertaDidik::class);
    }

    public function rombelAsal(): BelongsTo
    {
        return $this->belongsTo(Rombel::class, 'rombel_asal_id');
    }

    public function rombelTujuan(): BelongsTo
    {
        return $this->belongsTo(Rombel::class, 'rombel_tujuan_id');
    }

    // Wali Kelas yang memutuskan
    public function diputuskanOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diputuskan_oleh');
    }
}
