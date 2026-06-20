# build-steps.md — Urutan Pengerjaan (Satu Langkah = Satu Sesi)

> Cara pakai: di Claude Code, kerjakan **satu langkah bernomor saja** lalu review.
> Contoh perintah: *"Kerjakan Langkah 3 dari docs/build-steps.md. Patuhi
> docs/database.md dan CLAUDE.md. Berhenti setelah selesai."*
>
> Pola wajib tiap modul fitur: **Migration → Model → Seeder → Livewire (class + view) → Route → Uji manual.**

---

## FASE 0 — Fondasi Proyek

### Langkah 1 — Inisialisasi & Instalasi Paket
- `composer create-project laravel/laravel lms-padamu` (Laravel 13).
- Install: `composer require livewire/livewire spatie/laravel-permission`.
- Install dev: `maatwebsite/excel` (import Excel) & `barryvdh/laravel-dompdf` (cetak rapor PDF).
- `php artisan vendor:publish` untuk Spatie & Excel config.
- Setup `.env` (DB MySQL), `php artisan storage:link`.
- Pasang Tailwind + aset TailAdmin (lihat `docs/design-guide.md`).
- **Output:** proyek jalan di `php artisan serve`, halaman welcome tampil.

### Langkah 2 — Layout Dasar ($slot) + TailAdmin Shell
- Buat `resources/views/components/layouts/app.blade.php` berisi sidebar + topbar
  TailAdmin dengan `{{ $slot }}` di area konten utama.
- Buat `layouts/guest.blade.php` untuk halaman login.
- Buat satu komponen Livewire dummy untuk menguji layout merender benar.
- **Output:** kerangka dashboard tampil (menu masih statis).

---

## FASE 1 — Database & Auth

### Langkah 3 — Semua Migration
- Buat **satu file migration per tabel**, urut sesuai daftar di `docs/database.md`.
- Jalankan `php artisan migrate`. Jangan buat model/seeder dulu di langkah ini.
- Jalankan migration Spatie (`permission:table`) lebih dulu.
- **Output:** `php artisan migrate:fresh` sukses tanpa error FK.

### Langkah 4 — Semua Model + Relasi
- Buat **satu file model per tabel**, definisikan relasi Eloquent
  (`hasMany`, `belongsTo`, `belongsToMany`), `$fillable`, dan `$casts`.
- Tambahkan trait Spatie `HasRoles` di model `User`.
- **Output:** relasi bisa diakses (uji via `php artisan tinker`).

### Langkah 5 — Seeder Inti (satu file per kelompok)
- `RoleSeeder` — buat 3 role + permission dasar (Spatie).
- `AdminSeeder` — 1 akun Admin awal.
- `PeriodeAjaranSeeder`, `WilayahSeeder`, `PaketSeeder`, `TingkatSeeder`,
  `MapelSeeder` — data master contoh.
- Daftarkan semua di `DatabaseSeeder`. Jalankan `php artisan db:seed`.
- **Output:** bisa login sebagai Admin setelah Langkah 6.

### Langkah 6 — Autentikasi Custom (PRD 6.1)
- LoginController + Livewire `Auth/LoginForm` (login pakai `username`, bukan email).
- Logic: cek `is_change_password`; jika 0 tampilkan Livewire `Auth/ModalGantiPassword`
  (dismissable, tidak memblokir).
- Middleware role (Spatie) untuk gate dashboard per role.
- **Output:** login 3 role berfungsi, modal ganti password muncul & bisa di-skip.

---

## FASE 2 — Master Data (PRD 6.2 & 6.3)

### Langkah 7 — Periode Ajaran + Switcher
- `Admin/MasterData/PeriodeAjaranManager` (CRUD + set `is_aktif`, auto-nonaktifkan lainnya).
- Switcher periode global (read-only mode untuk periode non-aktif).

### Langkah 8 — Wilayah, Paket, Tingkat
- Tiga Manager CRUD sederhana (`WilayahManager`, `PaketManager`, `TingkatManager`).

### Langkah 9 — Rombel + Mapel + Pemetaan
- `RombelManager` (generate nama otomatis, set wali kelas, assign peserta didik).
- `MapelManager`.
- `PemetaanGuruMapelRombel` (isi tabel `guru_mapel_rombel`).

### Langkah 10 — Manajemen Akun Guru & Reset Password
- `Admin/Pengguna/GuruManager` (buat akun guru → otomatis buat `users`).
- `ResetPasswordModal` (reset ke username, set `is_change_password=0`, catat audit).

---

## FASE 3 — Jadwal & Import

### Langkah 11 — Jadwal Pelajaran (PRD 6.4)
- `JadwalManager` (Admin = semua rombel; Wali Kelas = rombelnya, via Policy).
- `JadwalViewer` (read-only untuk Guru & Peserta Didik).

### Langkah 12 — Import Excel Peserta Didik (PRD 6.5 & Bab 7)
- Template `.xlsx` kolom A–Y (lihat Langkah 18 / Tahap 4 PRD).
- `Admin/ImportExcel/UploadForm` — proses **synchronous**, validasi per baris.
- `RingkasanHasilImport` — tampilkan berhasil/gagal + alasan per baris.
- Output: buat `users + peserta_didik + alamat + ortu` + masuk Rombel otomatis.

---

## FASE 4 — Modul Transaksional (urut dari sederhana)

### Langkah 13 — Absensi (PRD 6.6)
- `Absensi/BukaSesiAbsensi` (Guru), `TombolHadir` (Peserta Didik),
  `RekapAbsensiRealtime` (polling Livewire beberapa detik, bukan websocket).

### Langkah 14 — Materi (PRD 6.7)
- `Guru/Materi/DaftarMateri` + `FormMateri` (tipe: text/file/link_video/gambar).

### Langkah 15 — Tugas (PRD 6.8)
- `DaftarTugas`, `FormTugas`, `DaftarSubmisi`, `FormPenilaianTugas`.
- Submisi setelah deadline ditandai "Terlambat".

### Langkah 16 — CBT (PRD 6.9) — modul paling kompleks
- Guru: `DaftarCbt`, `FormCbt`, `FormSoalCbt`, `DaftarHasilCbt`, `FormKoreksiUraian`.
- Peserta Didik: `DaftarCbtTersedia`, `PengerjaanCbt` (timer Alpine.js + auto-submit).
- Auto-grading PG di Service; validasi waktu di server saat submit.
- Status penilaian per peserta didik di `hasil_cbt`.

---

## FASE 5 — Penutup Akademik

### Langkah 17 — Kenaikan Kelas (PRD 6.10)
- `KenaikanKelas/PenentuanStatus` (Wali Kelas), `AssignRombelBaru` (Admin, Ruang Tunggu).

### Langkah 18 — Penilaian Akhir / Rapor (PRD 6.11) — terakhir
- `Penilaian/FormNilaiKomponen` (hitung `nilai_referensi` via Service saat form dibuka).
- Konversi grade (Service / `konfigurasi_grade`).
- `PreviewRapor` + `CetakRaporPdf` (dompdf, kop 3 kolom dari `public/images/`, lihat PRD 6.11.1).
- **Prasyarat:** aset logo sudah dikumpulkan (PRD Tahap 10.5).
- Catatan: kop rapor sebaiknya menarik `nama_pkbm`, `alamat_pkbm`,
  `telepon_pkbm`, `email_pkbm`, `logo_pkbm_path`, `logo_kabupaten_path` dari
  `SettingService`, bukan hardcode — supaya langkah 19 tidak perlu mengubah
  kode rapor lagi nanti.

---

## FASE 6 — Pengaturan Aplikasi & Log

### Langkah 19 — Modul Settings (Maintenance, Identitas PKBM, Toggle Modul, Grade)
- Migration `settings`, `konfigurasi_grade` (lihat `docs/database.md` Bagian 8).
- `SettingSeeder` — isi baris awal sesuai tabel default di `docs/database.md`,
  termasuk `modul_materi_aktif`.
- `GradeSeeder` — isi default A/B/C/D dari `docs/database.md`.
- `app/Services/SettingService.php` — method `get($key)`, `set($key, $value)`,
  `getGroup($group)`, dengan cache (`Cache::remember`, invalidasi saat `set()`
  dipanggil). Semua cast tipe (`boolean`/`integer`/`json`) terjadi di sini.
- `app/Services/GradeService.php` — method `konversi($nilai): string` untuk
  cari grade dari `konfigurasi_grade`, validasi anti-tumpang-tindih rentang
  saat Admin menyimpan.
- `app/Http/Middleware/CheckMaintenanceMode.php` — daftarkan di grup `web`.
  Admin bypass; role lain diarahkan ke `views/maintenance.blade.php`.
- Middleware/Policy per modul untuk toggle (`modul_cbt_aktif`,
  `modul_tugas_aktif`, `modul_absensi_aktif`, `modul_materi_aktif`) di route
  group masing-masing modul — **wajib**, lihat aturan dua-lapis di `CLAUDE.md`.
- `Admin/Pengaturan/SettingManager` (Livewire) — satu halaman, form
  dikelompokkan per `group` (Umum, Kop Rapor, Upload, Modul), termasuk
  upload logo dengan validasi ukuran/format gambar.
- `Admin/Pengaturan/GradeManager` (Livewire) — CRUD rentang grade.
- Update sidebar: baca `modul_*_aktif` dari `SettingService` untuk
  menyembunyikan/menampilkan menu modul terkait.
- Update layout & login: tampilkan `nama_pkbm` dari Settings, bukan hardcode.
- **Output:** mengubah Maintenance Mode ke `true` memblokir akses non-Admin;
  menonaktifkan modul CBT menyembunyikan menunya DAN memblokir akses
  langsung ke URL CBT untuk role yang tidak berhak.

### Langkah 20 — Error Log & Bug Report
- Migration `error_log`, `bug_report` (lihat `docs/database.md` Bagian 9).
- Model `ErrorLog`, `BugReport` — **tanpa** trait `SoftDeletes`.
- `app/Services/ErrorLogService.php` — method `catat(string $aksi, \Throwable
  $throwable, array $context = [])`, otomatis isi `user_id` dari `Auth::id()`
  dan `url` dari `request()->fullUrl()`.
- **Audit cepat**: cek try-catch yang sudah ada dari Fase 1–5, pastikan semua
  memanggil `ErrorLogService::catat()` sesuai pola di `CLAUDE.md`.
- `Admin/ErrorLog/DaftarErrorLog` (Livewire) — tabel dengan filter tanggal,
  tombol Hapus per baris, tombol "Hapus Semua" dengan modal konfirmasi.
- `BugReport/FormLaporBug` (Livewire, untuk SEMUA role) — judul, deskripsi,
  upload screenshot opsional, auto-isi `halaman_url` dari URL saat ini.
  Tombol akses ada di topbar (ikon kecil "Lapor Bug"), bukan hanya di menu
  Admin — supaya semua role bisa lapor dari halaman manapun.
- `Admin/BugReport/DaftarBugReport` (Livewire) — tabel dengan filter status,
  preview screenshot, ubah status (baru/diproses/selesai/ditolak), catatan
  tindak lanjut, tombol Hapus per baris.
- **Output:** error PHP otomatis tercatat & terlihat Admin; pengguna mana pun
  bisa kirim laporan bug manual lengkap dengan gambar dari halaman manapun.

---

## Aturan saat menjalankan tiap langkah
1. Buka `docs/database.md` & `CLAUDE.md` sebelum mulai.
2. Buat file kecil & terpisah (satu migration/model/seeder per tabel).
3. Jalankan migrasi/seed, laporkan hasil.
4. Berhenti & minta review sebelum lanjut ke langkah berikutnya.
5. Tulis logic bisnis di Model/Service/Action — **tidak pernah di database**.
6. Untuk apapun yang sifatnya "bisa berubah tanpa deploy ulang" (nama lembaga,
   logo, batas upload, on/off modul) — taruh di tabel `settings`, jangan hardcode
   maupun taruh di `.env`.