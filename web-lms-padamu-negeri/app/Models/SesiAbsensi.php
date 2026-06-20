<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SesiAbsensi extends Model
{
    protected $table = 'sesi_absensi';

    protected $fillable = [
        'guru_mapel_rombel_id',
        'tanggal',
        'status_sesi',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    public function guruMapelRombel(): BelongsTo
    {
        return $this->belongsTo(GuruMapelRombel::class);
    }

    public function detail(): HasMany
    {
        return $this->hasMany(AbsensiDetail::class);
    }
}
