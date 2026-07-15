<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KonfigurasiNilai extends Model
{
    protected $table = 'konfigurasi_nilai';

    protected $fillable = [
        'key',
        'value',
        'type',
        'grup',
        'label',
    ];

    /** Grup bobot — tiap grup WAJIB total 100. */
    public const GRUP_CBT            = 'cbt';
    public const GRUP_KOMPONEN_TUGAS = 'komponen_tugas';
    public const GRUP_RAPOR          = 'rapor';

    /** Pasangan key per grup (dipakai untuk validasi total 100). */
    public const PASANGAN = [
        self::GRUP_CBT            => ['bobot_cbt_pg', 'bobot_cbt_uraian'],
        self::GRUP_KOMPONEN_TUGAS => ['bobot_tugas_dari_tugas', 'bobot_tugas_dari_cbt'],
        self::GRUP_RAPOR          => ['bobot_rapor_tugas', 'bobot_rapor_sas'],
    ];

    public const LABEL_GRUP = [
        self::GRUP_CBT            => 'Bobot Nilai CBT',
        self::GRUP_KOMPONEN_TUGAS => 'Bobot Komponen TUGAS',
        self::GRUP_RAPOR          => 'Bobot Nilai Rapor',
    ];
}
