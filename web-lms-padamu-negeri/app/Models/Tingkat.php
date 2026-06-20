<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tingkat extends Model
{
    protected $table = 'tingkat';

    protected $fillable = ['paket_id', 'nama'];

    public function paket(): BelongsTo
    {
        return $this->belongsTo(Paket::class);
    }

    public function rombel(): HasMany
    {
        return $this->hasMany(Rombel::class);
    }
}
