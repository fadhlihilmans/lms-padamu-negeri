<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CbtJawabanPeserta extends Model
{
    protected $table = 'cbt_jawaban_peserta';

    protected $fillable = [
        'hasil_cbt_id',
        'cbt_soal_id',
        'jawaban',
        'skor_uraian',
    ];

    public function hasilCbt(): BelongsTo
    {
        return $this->belongsTo(HasilCbt::class);
    }

    public function soal(): BelongsTo
    {
        return $this->belongsTo(CbtSoal::class, 'cbt_soal_id');
    }
}
