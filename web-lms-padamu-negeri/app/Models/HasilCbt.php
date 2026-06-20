<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class HasilCbt extends Model
{
    use SoftDeletes;

    protected $table = 'hasil_cbt';

    protected $fillable = [
        'cbt_id',
        'peserta_didik_id',
        'waktu_mulai',
        'waktu_submit',
        'nilai_pg',
        'nilai_uraian',
        'nilai_akhir',
        'status_penilaian',
    ];

    protected function casts(): array
    {
        return [
            'waktu_mulai'   => 'datetime',
            'waktu_submit'  => 'datetime',
        ];
    }

    public function cbt(): BelongsTo
    {
        return $this->belongsTo(Cbt::class);
    }

    public function pesertaDidik(): BelongsTo
    {
        return $this->belongsTo(PesertaDidik::class);
    }

    public function jawabanPeserta(): HasMany
    {
        return $this->hasMany(CbtJawabanPeserta::class);
    }
}
