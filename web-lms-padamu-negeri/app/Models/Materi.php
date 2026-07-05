<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Materi extends Model
{
    use SoftDeletes;

    protected $table = 'materi';

    protected $fillable = [
        'guru_mapel_rombel_id',
        'judul',
        'isi',
    ];

    public function guruMapelRombel(): BelongsTo
    {
        return $this->belongsTo(GuruMapelRombel::class);
    }

    public function lampiran(): HasMany
    {
        return $this->hasMany(MateriLampiran::class)->orderBy('urutan');
    }

    /**
     * Ringkasan teks `isi` untuk daftar/preview: buang blok <figure> (lampiran
     * gambar Trix beserta caption nama file & ukuran) lalu strip tag sisanya.
     */
    public function ringkasan(int $limit = 110): ?string
    {
        if (! $this->isi) {
            return null;
        }

        // Hilangkan figure (attachment gambar) & script/style sebelum strip_tags.
        $bersih = preg_replace('/<(figure|script|style)\b[^>]*>.*?<\/\1>/is', ' ', $this->isi);
        $teks   = trim(preg_replace('/\s+/', ' ', strip_tags($bersih ?? '')));

        return $teks !== '' ? Str::limit($teks, $limit) : null;
    }
}
