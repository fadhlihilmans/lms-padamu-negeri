<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class PesertaDidik extends Model
{
    use SoftDeletes;

    protected $table = 'peserta_didik';

    protected $fillable = [
        'user_id',
        'nipd',
        'nisn',
        'nik',
        'nama_lengkap',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'no_hp',
        'status_akademik',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    // ─── Relasi ────────────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function alamat(): HasOne
    {
        return $this->hasOne(PesertaDidikAlamat::class);
    }

    public function ortu(): HasMany
    {
        return $this->hasMany(PesertaDidikOrtu::class);
    }

    public function pesertaDidikRombel(): HasMany
    {
        return $this->hasMany(PesertaDidikRombel::class);
    }

    public function tugasSubmisi(): HasMany
    {
        return $this->hasMany(TugasSubmisi::class);
    }

    public function hasilCbt(): HasMany
    {
        return $this->hasMany(HasilCbt::class);
    }

    public function absensiDetail(): HasMany
    {
        return $this->hasMany(AbsensiDetail::class);
    }

    public function kenaikanKelas(): HasMany
    {
        return $this->hasMany(KenaikanKelas::class);
    }

    public function rapor(): HasMany
    {
        return $this->hasMany(Rapor::class);
    }
}
