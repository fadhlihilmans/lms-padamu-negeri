<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RaporNilaiMapel extends Model
{
    protected $table = 'rapor_nilai_mapel';

    protected $fillable = [
        'rapor_id',
        'mapel_id',
        'diinput_oleh',
        'catatan_mapel',
    ];

    public function rapor(): BelongsTo
    {
        return $this->belongsTo(Rapor::class);
    }

    public function mapel(): BelongsTo
    {
        return $this->belongsTo(Mapel::class);
    }

    // Guru Mapel yang menginput
    public function diinputOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diinput_oleh');
    }

    public function komponen(): HasMany
    {
        return $this->hasMany(RaporNilaiKomponen::class);
    }
}
