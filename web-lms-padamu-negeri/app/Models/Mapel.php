<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mapel extends Model
{
    protected $table = 'mapel';

    protected $fillable = ['nama'];

    public function guruMapelRombel(): HasMany
    {
        return $this->hasMany(GuruMapelRombel::class);
    }

    public function jadwalPelajaran(): HasMany
    {
        return $this->hasMany(JadwalPelajaran::class);
    }

    public function raporNilaiMapel(): HasMany
    {
        return $this->hasMany(RaporNilaiMapel::class);
    }
}
