<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles, SoftDeletes;

    protected $fillable = [
        'username',
        'password',
        'is_change_password',
        'is_active',
    ];

    protected $hidden = ['password'];

    // Tabel tidak punya kolom remember_token
    public function getRememberTokenName(): ?string
    {
        return null;
    }

    protected function casts(): array
    {
        return [
            'password'           => 'hashed',
            'is_change_password' => 'boolean',
            'is_active'          => 'boolean',
        ];
    }

    // ─── Relasi ────────────────────────────────────────────────────────────────

    public function guru(): HasOne
    {
        return $this->hasOne(Guru::class);
    }

    public function pesertaDidik(): HasOne
    {
        return $this->hasOne(PesertaDidik::class);
    }

    public function errorLogs(): HasMany
    {
        return $this->hasMany(ErrorLog::class);
    }

    public function bugReports(): HasMany
    {
        return $this->hasMany(BugReport::class);
    }
}
