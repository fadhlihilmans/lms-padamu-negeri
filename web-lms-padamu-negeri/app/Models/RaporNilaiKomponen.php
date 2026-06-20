<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RaporNilaiKomponen extends Model
{
    protected $table = 'rapor_nilai_komponen';

    protected $fillable = [
        'rapor_nilai_mapel_id',
        'nama_komponen',
        'nilai_referensi',
        'nilai_akhir',
        'grade',
        'catatan',
    ];

    public function nilaiMapel(): BelongsTo
    {
        return $this->belongsTo(RaporNilaiMapel::class, 'rapor_nilai_mapel_id');
    }
}
