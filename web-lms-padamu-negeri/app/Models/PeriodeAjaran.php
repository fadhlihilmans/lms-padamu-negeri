<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PeriodeAjaran extends Model
{
    protected $table = 'periode_ajaran';

    protected $fillable = [
        'tahun_ajaran',
        'semester',
        'is_aktif',
    ];

    protected function casts(): array
    {
        return [
            'is_aktif' => 'boolean',
        ];
    }

    public function guruMapelRombel(): HasMany
    {
        return $this->hasMany(GuruMapelRombel::class);
    }

    public function rapor(): HasMany
    {
        return $this->hasMany(Rapor::class);
    }
}
