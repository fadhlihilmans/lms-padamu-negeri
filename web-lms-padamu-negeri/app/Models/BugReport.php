<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BugReport extends Model
{
    protected $table = 'bug_report';

    protected $fillable = [
        'user_id',
        'judul',
        'deskripsi',
        'screenshot_path',
        'halaman_url',
        'status',
        'catatan_admin',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
