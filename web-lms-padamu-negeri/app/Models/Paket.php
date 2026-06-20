<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Paket extends Model
{
    protected $table = 'paket';

    protected $fillable = ['nama'];

    public function tingkat(): HasMany
    {
        return $this->hasMany(Tingkat::class);
    }

    public function rombel(): HasMany
    {
        return $this->hasMany(Rombel::class);
    }
}
