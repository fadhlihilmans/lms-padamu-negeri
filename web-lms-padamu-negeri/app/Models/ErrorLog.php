<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ErrorLog extends Model
{
    // Tabel hanya punya created_at, tidak ada updated_at
    const UPDATED_AT = null;

    protected $table = 'error_log';

    protected $fillable = [
        'user_id',
        'aksi',
        'pesan_error',
        'context',
        'url',
    ];

    protected function casts(): array
    {
        return [
            'context' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
