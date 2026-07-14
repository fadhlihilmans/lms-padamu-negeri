<?php

namespace App\Providers;

use App\Exceptions\ModeArsipException;
use App\Services\PeriodeService;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Tabel akademik yang TERIKAT periode/TA — tidak boleh diubah saat Mode Arsip.
     *
     * Sengaja daftar-blokir (bukan blokir-semua) agar hal-hal berikut TETAP jalan
     * meski Admin sedang melihat periode lampau:
     *  - `ErrorLog` & `BugReport`  → pencatatan/pelaporan harus selalu bisa
     *  - `PeriodeAjaran`           → agar bisa KELUAR dari Mode Arsip
     *  - `Setting`, `KonfigurasiGrade` → konfigurasi sistem, bukan data periode
     *  - `User`, `Guru`, `PesertaDidik`, master data (Wilayah/Paket/Tingkat/Mapel)
     *    → identitas & master, tidak terikat periode
     */
    private const MODEL_TERKUNCI_SAAT_ARSIP = [
        \App\Models\Rombel::class,
        \App\Models\GuruMapelRombel::class,
        \App\Models\PesertaDidikRombel::class,
        \App\Models\JadwalPelajaran::class,

        \App\Models\Materi::class,
        \App\Models\MateriLampiran::class,
        \App\Models\Tugas::class,
        \App\Models\TugasSubmisi::class,

        \App\Models\Cbt::class,
        \App\Models\CbtSoal::class,
        \App\Models\HasilCbt::class,
        \App\Models\CbtJawabanPeserta::class,

        \App\Models\SesiAbsensi::class,
        \App\Models\AbsensiDetail::class,

        \App\Models\KenaikanKelas::class,
        \App\Models\Rapor::class,
        \App\Models\RaporNilaiMapel::class,
        \App\Models\RaporNilaiKomponen::class,
    ];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Carbon::setLocale('id');
        setlocale(LC_TIME, 'id_ID.UTF-8', 'id_ID', 'Indonesian');

        $this->kunciPenulisanSaatModeArsip();
    }

    /**
     * LAPIS KEAMANAN Mode Arsip (bukan kosmetik).
     *
     * Sebelumnya `isReadOnlyMode()` hanya dipakai untuk banner & warna — data
     * periode lampau MASIH BISA DIEDIT. Penjagaan dipasang di event Eloquent
     * supaya berlaku untuk SEMUA jalur penulisan (Livewire, Service, controller)
     * dan tidak bisa terlewat karena lupa menambah pengecekan di komponen baru.
     *
     * Hanya aktif bila pengguna EKSPLISIT memilih periode non-aktif di session
     * (lihat PeriodeService::isReadOnlyMode). Seeder/artisan tidak menyetel session
     * itu, jadi migrasi & seeding tidak terganggu.
     */
    private function kunciPenulisanSaatModeArsip(): void
    {
        $tolak = function (): void {
            if (! app(PeriodeService::class)->isReadOnlyMode()) {
                return;
            }

            throw new ModeArsipException();
        };

        foreach (self::MODEL_TERKUNCI_SAAT_ARSIP as $model) {
            $model::saving($tolak);    // create + update
            $model::deleting($tolak);  // delete + soft delete

            // `restoring` hanya ada pada model ber-SoftDeletes.
            if (in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses_recursive($model), true)) {
                $model::restoring($tolak);
            }
        }
    }
}
