<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles & permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Permissions ──────────────────────────────────────────────────────
        $permissions = [
            // Pengguna & Akun
            'manage-pengguna',
            'reset-password',
            'import-peserta-didik',

            // Master Data
            'manage-master-data',
            'manage-rombel',

            // Jadwal
            'manage-jadwal',
            'view-jadwal',

            // Absensi
            'manage-absensi',    // buka sesi, absen manual
            'access-absensi',    // klik hadir mandiri (peserta didik)
            'view-absensi',      // lihat rekap

            // Kegiatan Belajar
            'manage-materi',
            'access-materi',
            'manage-tugas',
            'access-tugas',
            'manage-cbt',
            'access-cbt',

            // Penilaian & Rapor
            'input-nilai-rapor',
            'view-rapor',

            // Kenaikan Kelas
            'manage-kenaikan-kelas',

            // Pengaturan Sistem
            'manage-settings',
            'view-error-log',
            'manage-bug-report',

            // Laporan Bug (semua role)
            'submit-bug-report',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // ── Roles & Assignment ───────────────────────────────────────────────
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions([
            'manage-pengguna',
            'reset-password',
            'import-peserta-didik',
            'manage-master-data',
            'manage-rombel',
            'manage-jadwal',
            'view-jadwal',
            'view-absensi',
            'manage-kenaikan-kelas',
            'manage-settings',
            'view-error-log',
            'manage-bug-report',
            'submit-bug-report',
            'view-rapor',
        ]);

        $guru = Role::firstOrCreate(['name' => 'guru']);
        $guru->syncPermissions([
            'view-jadwal',
            'manage-absensi',
            'view-absensi',
            'manage-materi',
            'manage-tugas',
            'manage-cbt',
            'input-nilai-rapor',
            'view-rapor',
            'manage-kenaikan-kelas',  // hanya efektif saat sebagai Wali Kelas (cek Policy)
            'submit-bug-report',
        ]);

        $pesertaDidik = Role::firstOrCreate(['name' => 'peserta_didik']);
        $pesertaDidik->syncPermissions([
            'view-jadwal',
            'access-absensi',
            'access-materi',
            'access-tugas',
            'access-cbt',
            'view-rapor',
            'submit-bug-report',
        ]);
    }
}
