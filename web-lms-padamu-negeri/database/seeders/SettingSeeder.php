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
            ['whatsapp_admin', 'string', 'general', '6281234567891', 'WhatsApp Admin', 'Nomor WhatsApp admin untuk bantuan/reset password (format 62xxx). Ditampilkan di halaman login.'],
            ['logo_aplikasi_path', 'string', 'general', '', 'Logo Aplikasi', 'Logo yang tampil di halaman login dan sidebar. Bila kosong, memakai ikon bawaan.'],

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
            ['kompres_target_kb', 'integer', 'upload', '300', 'Target Kompres Gambar (KB)', 'Gambar yang diunggah dikompres ke WebP hingga sekitar ukuran ini (logo, gambar materi, screenshot bug).'],

            // ── Modul ──
            // Toggle per role — dikelompokkan per blok role (CLAUDE.md #11).
            ['modul_materi_guru_aktif', 'boolean', 'modul_guru', '1', 'Materi', 'Akses modul Materi untuk Guru.'],
            ['modul_tugas_guru_aktif', 'boolean', 'modul_guru', '1', 'Tugas', 'Akses modul Tugas untuk Guru.'],
            ['modul_absensi_guru_aktif', 'boolean', 'modul_guru', '1', 'Absensi', 'Akses modul Absensi untuk Guru.'],
            ['modul_cbt_guru_aktif', 'boolean', 'modul_guru', '1', 'CBT', 'Akses modul CBT untuk Guru.'],
            ['modul_materi_pd_aktif', 'boolean', 'modul_pd', '1', 'Materi', 'Akses modul Materi untuk Peserta Didik.'],
            ['modul_tugas_pd_aktif', 'boolean', 'modul_pd', '1', 'Tugas', 'Akses modul Tugas untuk Peserta Didik.'],
            ['modul_absensi_pd_aktif', 'boolean', 'modul_pd', '1', 'Absensi', 'Akses modul Absensi untuk Peserta Didik.'],
            ['modul_cbt_pd_aktif', 'boolean', 'modul_pd', '1', 'CBT', 'Akses modul CBT untuk Peserta Didik.'],
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
