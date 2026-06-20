<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TugasSubmisi extends Model
{
    use SoftDeletes;

    protected $table = 'tugas_submisi';

    protected $fillable = [
        'tugas_id',
        'peserta_didik_id',
        'file_path',
        'isi_text',
        'waktu_submit',
        'nilai',
    ];

    protected function casts(): array
    {
        return [
            'waktu_submit' => 'datetime',
        ];
    }

    public function tugas(): BelongsTo
    {
        return $this->belongsTo(Tugas::class);
    }

    public function pesertaDidik(): BelongsTo
    {
        return $this->belongsTo(PesertaDidik::class);
    }
}
