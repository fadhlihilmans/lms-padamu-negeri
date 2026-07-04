<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        // key, type, group, value, label, description
        $rows = [
            // ── Umum ──
            ['maintenance_mode', 'boolean', 'general', '0', 'Mode Maintenance', 'Jika aktif, seluruh akses non-Admin diarahkan ke halaman maintenance.'],
            ['nama_pkbm', 'string', 'general', 'PKBM Padamu Negeri', 'Nama PKBM', 'Ditampilkan di sidebar, topbar, login, dan kop rapor.'],

            // ── Kop Rapor ──
            ['nama_kabupaten', 'string', 'kop_rapor', 'Pemerintah Kabupaten Batang', 'Nama Pemerintah Kabupaten', 'Baris teratas kop rapor.'],
            ['nama_dinas', 'string', 'kop_rapor', 'Dinas Pendidikan dan Kebudayaan', 'Nama Dinas', 'Baris kedua kop rapor.'],
            ['alamat_pkbm', 'string', 'kop_rapor', '', 'Alamat PKBM', 'Blok tengah kop rapor.'],
            ['telepon_pkbm', 'string', 'kop_rapor', '', 'Telepon / WhatsApp', 'Kontak pada kop rapor.'],
            ['email_pkbm', 'string', 'kop_rapor', '', 'Email', 'Kontak pada kop rapor.'],
            ['kepala_pkbm_nama', 'string', 'kop_rapor', '', 'Nama Kepala PKBM', 'Untuk tanda tangan rapor.'],
            ['kepala_pkbm_nip', 'string', 'kop_rapor', '', 'NIP Kepala PKBM', 'Untuk tanda tangan rapor.'],
            ['logo_pkbm_path', 'string', 'kop_rapor', '', 'Logo PKBM', 'Logo kanan kop rapor & sidebar (PNG/JPG).'],
            ['logo_kabupaten_path', 'string', 'kop_rapor', '', 'Logo Kabupaten', 'Logo kiri kop rapor (PNG/JPG).'],

            // ── Upload ──
            ['max_upload_materi_mb', 'integer', 'upload', '10', 'Batas Upload Materi (MB)', 'Ukuran maksimal file materi.'],
            ['max_upload_tugas_mb', 'integer', 'upload', '10', 'Batas Upload Tugas (MB)', 'Ukuran maksimal file submisi tugas.'],

            // ── Modul ──
            ['modul_materi_aktif', 'boolean', 'modul', '1', 'Modul Materi', 'Aktifkan/nonaktifkan modul Materi.'],
            ['modul_tugas_aktif', 'boolean', 'modul', '1', 'Modul Tugas', 'Aktifkan/nonaktifkan modul Tugas.'],
            ['modul_absensi_aktif', 'boolean', 'modul', '1', 'Modul Absensi', 'Aktifkan/nonaktifkan modul Absensi.'],
            ['modul_cbt_aktif', 'boolean', 'modul', '1', 'Modul CBT', 'Aktifkan/nonaktifkan modul CBT.'],
        ];

        foreach ($rows as [$key, $type, $group, $value, $label, $desc]) {
            // firstOrCreate → jangan menimpa nilai yang sudah diubah Admin.
            Setting::firstOrCreate(
                ['key' => $key],
                ['type' => $type, 'group' => $group, 'value' => $value, 'label' => $label, 'description' => $desc],
            );
        }
    }
}
