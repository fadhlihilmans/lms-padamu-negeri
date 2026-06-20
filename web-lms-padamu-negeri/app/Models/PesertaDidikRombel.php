<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PesertaDidikRombel extends Model
{
    use SoftDeletes;

    protected $table = 'peserta_didik_rombel';

    protected $fillable = [
        'peserta_didik_id',
        'rombel_id',
    ];

    public function pesertaDidik(): BelongsTo
    {
        return $this->belongsTo(PesertaDidik::class);
    }

    public function rombel(): BelongsTo
    {
        return $this->belongsTo(Rombel::class);
    }
}
