<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cbt extends Model
{
    use SoftDeletes;

    protected $table = 'cbt';

    protected $fillable = [
        'guru_mapel_rombel_id',
        // TRANSAKSI → semester tempat data ini dibuat (Revisi Tahap 3).
        'periode_ajaran_id',
        'nama_ujian',
        'kkm',
        'tanggal_mulai',
        'durasi_menit',
        'tampilkan_nilai_otomatis',
        'jenis_cbt',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai'            => 'datetime',
            'tampilkan_nilai_otomatis' => 'boolean',
        ];
    }

    public function guruMapelRombel(): BelongsTo
    {
        return $this->belongsTo(GuruMapelRombel::class);
    }

    public function soal(): HasMany
    {
        return $this->hasMany(CbtSoal::class);
    }

    public function hasilCbt(): HasMany
    {
        return $this->hasMany(HasilCbt::class);
    }

    /** TRANSAKSI → terikat periode (TA + semester). */
    public function periodeAjaran(): BelongsTo
    {
        return $this->belongsTo(PeriodeAjaran::class);
    }

    /** Batasi ke satu periode (TA + semester). */
    public function scopePeriode($query, $periodeId)
    {
        return $query->when($periodeId, fn ($q) => $q->where('periode_ajaran_id', $periodeId));
    }
}
