<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Materi extends Model
{
    use SoftDeletes;

    protected $table = 'materi';

    protected $fillable = [
        'guru_mapel_rombel_id',
        'judul',
        'deskripsi',
        'tipe_konten',
        'file_path',
        'url',
    ];

    public function guruMapelRombel(): BelongsTo
    {
        return $this->belongsTo(GuruMapelRombel::class);
    }
}
