<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wilayah extends Model
{
    protected $table = 'wilayah';

    protected $fillable = ['nama'];

    public function rombel(): HasMany
    {
        return $this->hasMany(Rombel::class);
    }
}
