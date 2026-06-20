<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tugas extends Model
{
    use SoftDeletes;

    protected $table = 'tugas';

    protected $fillable = [
        'guru_mapel_rombel_id',
        'judul',
        'deskripsi',
        'lampiran_path',
        'deadline',
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'datetime',
        ];
    }

    public function guruMapelRombel(): BelongsTo
    {
        return $this->belongsTo(GuruMapelRombel::class);
    }

    public function submisi(): HasMany
    {
        return $this->hasMany(TugasSubmisi::class);
    }
}
