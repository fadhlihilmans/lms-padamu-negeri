<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rombel extends Model
{
    use SoftDeletes;

    protected $table = 'rombel';

    protected $fillable = [
        'wilayah_id',
        'paket_id',
        'tingkat_id',
        'tahun_ajaran',
        'wali_kelas_id',
        'nama',
    ];

    // ─── Relasi ────────────────────────────────────────────────────────────────

    public function wilayah(): BelongsTo
    {
        return $this->belongsTo(Wilayah::class);
    }

    public function paket(): BelongsTo
    {
        return $this->belongsTo(Paket::class);
    }

    public function tingkat(): BelongsTo
    {
        return $this->belongsTo(Tingkat::class);
    }

    // wali_kelas_id → guru.id
    public function waliKelas(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'wali_kelas_id');
    }

    public function pesertaDidikRombel(): HasMany
    {
        return $this->hasMany(PesertaDidikRombel::class);
    }

    public function guruMapelRombel(): HasMany
    {
        return $this->hasMany(GuruMapelRombel::class);
    }

    public function jadwalPelajaran(): HasMany
    {
        return $this->hasMany(JadwalPelajaran::class);
    }

    public function kenaikanKelasAsal(): HasMany
    {
        return $this->hasMany(KenaikanKelas::class, 'rombel_asal_id');
    }

    public function kenaikanKelasTujuan(): HasMany
    {
        return $this->hasMany(KenaikanKelas::class, 'rombel_tujuan_id');
    }

    public function rapor(): HasMany
    {
        return $this->hasMany(Rapor::class);
    }
}
