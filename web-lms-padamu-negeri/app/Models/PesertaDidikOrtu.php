<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PesertaDidikOrtu extends Model
{
    protected $table = 'peserta_didik_ortu';

    protected $fillable = [
        'peserta_didik_id',
        'jenis',
        'nama',
        'no_hp',
        'hubungan_wali',
    ];

    public function pesertaDidik(): BelongsTo
    {
        return $this->belongsTo(PesertaDidik::class);
    }
}
