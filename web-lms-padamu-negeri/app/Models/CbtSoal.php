<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CbtSoal extends Model
{
    protected $table = 'cbt_soal';

    protected $fillable = [
        'cbt_id',
        'tipe_soal',
        'pertanyaan',
        'pilihan_jawaban',
        'kunci_jawaban',
    ];

    protected function casts(): array
    {
        return [
            'pilihan_jawaban' => 'array',
        ];
    }

    public function cbt(): BelongsTo
    {
        return $this->belongsTo(Cbt::class);
    }

    public function jawabanPeserta(): HasMany
    {
        return $this->hasMany(CbtJawabanPeserta::class);
    }
}
