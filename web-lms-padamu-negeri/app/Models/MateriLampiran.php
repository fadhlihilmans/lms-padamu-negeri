<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MateriLampiran extends Model
{
    protected $table = 'materi_lampiran';

    protected $fillable = [
        'materi_id',
        'tipe',
        'file_path',
        'url',
        'nama_asli',
        'urutan',
    ];

    public function materi(): BelongsTo
    {
        return $this->belongsTo(Materi::class);
    }

    public function youtubeId(): ?string
    {
        if ($this->tipe !== 'link_video' || ! $this->url) return null;

        preg_match(
            '/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|v\/|shorts\/))([\w\-]{11})/',
            $this->url,
            $matches
        );

        return $matches[1] ?? null;
    }
}
