<?php

namespace App\Models;

use App\Exceptions\SatuRombelPerTaException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PesertaDidikRombel extends Model
{
    use SoftDeletes;

    protected $table = 'peserta_didik_rombel';

    protected $fillable = [
        'peserta_didik_id',
        'rombel_id',
    ];

    /**
     * LAPIS KEAMANAN: 1 Peserta Didik = 1 rombel per Tahun Ajaran.
     *
     * Dipasang di event model (bukan sekadar dicek di Livewire) supaya berlaku ke
     * SEMUA jalur — Livewire, Import Excel, Kenaikan Kelas, seeder — dan tidak
     * bisa terlewat karena lupa menambah pengecekan di kode baru.
     *
     * Catatan: rombel kini terikat Tahun Ajaran, jadi ganjil↔genap TIDAK melahirkan
     * rombel baru. Naik ke TA berikutnya tetap boleh (TA-nya berbeda) — itu histori,
     * bukan duplikat.
     */
    protected static function booted(): void
    {
        static::creating(fn (self $pdr) => $pdr->pastikanSatuRombelPerTa());
        static::restoring(fn (self $pdr) => $pdr->pastikanSatuRombelPerTa());
    }

    private function pastikanSatuRombelPerTa(): void
    {
        $bentrok = self::rombelLainDiTaSama((int) $this->peserta_didik_id, (int) $this->rombel_id);

        if ($bentrok) {
            throw new SatuRombelPerTaException(
                PesertaDidik::find($this->peserta_didik_id)?->nama_lengkap ?? 'Peserta didik',
                $bentrok->nama,
                $bentrok->tahun_ajaran,
            );
        }
    }

    /**
     * Rombel LAIN yang sudah ditempati PD pada Tahun Ajaran yang sama.
     * Null bila aman. Dipakai komponen untuk memberi pesan ramah SEBELUM menyimpan.
     */
    public static function rombelLainDiTaSama(int $pesertaDidikId, int $rombelId): ?Rombel
    {
        $tujuan = Rombel::find($rombelId);
        if (! $tujuan) {
            return null;
        }

        return Rombel::query()
            ->where('tahun_ajaran', $tujuan->tahun_ajaran)
            ->where('id', '!=', $tujuan->id)
            ->whereHas('pesertaDidikRombel', fn ($q) => $q->where('peserta_didik_id', $pesertaDidikId))
            ->first();
    }

    public function pesertaDidik(): BelongsTo
    {
        return $this->belongsTo(PesertaDidik::class);
    }

    public function rombel(): BelongsTo
    {
        return $this->belongsTo(Rombel::class);
    }
}
