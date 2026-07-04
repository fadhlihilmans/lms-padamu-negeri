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

    public function ekstensi(): string
    {
        return strtolower(pathinfo($this->nama_asli ?? '', PATHINFO_EXTENSION));
    }

    /**
     * Ikon & warna file dokumen berdasarkan ekstensi (untuk tampilan detail materi).
     */
    public function ikonWarna(): array
    {
        return match ($this->ekstensi()) {
            'pdf'          => ['picture_as_pdf', 'text-red-600', 'bg-red-50'],
            'doc', 'docx'  => ['description', 'text-blue-600', 'bg-blue-50'],
            'xls', 'xlsx'  => ['description', 'text-green-600', 'bg-green-50'],
            'ppt', 'pptx'  => ['description', 'text-orange-600', 'bg-orange-50'],
            default        => ['description', 'text-[#505f76]', 'bg-[#f0f4f8]'],
        };
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
