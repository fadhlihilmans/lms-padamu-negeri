# checklist.md — Progres Pembangunan LMS Padamu Negeri

> **Sumber:** diturunkan dari `docs/build-steps.md` (Fase 0–6, Langkah 1–20).
>
> **ATURAN PENTING (berlaku untuk Claude Code):**
> - ✅ **Kotak centang `[ ]` / `[x]` di file ini HANYA boleh diubah oleh pemilik proyek (saya), BUKAN oleh Claude.**
>   Claude **dilarang** mencentang, meng-uncheck, atau menandai task apa pun sebagai selesai.
> - Claude **boleh** menambah baris task baru (mis. jika `build-steps.md` bertambah) atau memperbaiki
>   redaksi task, tetapi tetap dalam keadaan **belum dicentang** (`[ ]`).
> - Untuk mengetahui progres, Claude **membaca** status centang di sini (read-only) — jangan menebak dari kode.
> - Saat mulai satu Langkah, kerjakan **satu Langkah bernomor saja**, lalu berhenti & minta review
>   (sesuai `docs/build-steps.md` dan `CLAUDE.md`). Pemilik yang menandai `[x]` bila sudah di-review & diterima.
>
> Legenda: `[ ]` belum · `[x]` selesai (diisi pemilik).

---

## FASE 0 — Fondasi Proyek

### Langkah 1 — Inisialisasi & Instalasi Paket
- [x] `composer create-project laravel/laravel lms-padamu` (Laravel 13)
- [x] Install: `livewire/livewire`, `spatie/laravel-permission`
- [x] Install: `maatwebsite/excel` (import Excel) & `barryvdh/laravel-dompdf` (rapor PDF)
- [x] `php artisan vendor:publish` untuk Spatie & Excel
- [x] Setup `.env` (DB MySQL) + `php artisan storage:link`
- [x] Pasang Tailwind + aset TailAdmin (lihat `docs/design-guide.md`)
- [x] **Output:** proyek jalan di `php artisan serve`, halaman welcome tampil

### Langkah 2 — Layout Dasar ($slot) + TailAdmin Shell
- [x] `resources/views/components/layouts/app.blade.php` (sidebar + topbar TailAdmin, `{{ $slot }}`)
- [x] `layouts/guest.blade.php` untuk halaman login
- [x] Komponen Livewire dummy untuk uji render layout
- [x] **Output:** kerangka dashboard tampil (menu masih statis)

---

## FASE 1 — Database & Auth

### Langkah 3 — Semua Migration
- [x] Satu file migration per tabel, urut sesuai `docs/database.md`
- [x] Jalankan migration Spatie (`permission:table`) lebih dulu
- [x] `php artisan migrate` (belum buat model/seeder)
- [x] **Output:** `php artisan migrate:fresh` sukses tanpa error FK

### Langkah 4 — Semua Model + Relasi
- [x] Satu file model per tabel: relasi Eloquent (`hasMany`/`belongsTo`/`belongsToMany`), `$fillable`, `$casts`
- [x] Trait Spatie `HasRoles` di model `User`
- [x] **Output:** relasi bisa diakses (uji via `php artisan tinker`)

### Langkah 5 — Seeder Inti (satu file per kelompok)
- [x] `RoleSeeder` — 3 role + permission dasar (Spatie)
- [x] `AdminSeeder` — 1 akun Admin awal
- [x] `PeriodeAjaranSeeder`, `WilayahSeeder`, `PaketSeeder`, `TingkatSeeder`, `MapelSeeder`
- [x] Daftarkan di `DatabaseSeeder` + `php artisan db:seed`
- [x] **Output:** bisa login sebagai Admin setelah Langkah 6

### Langkah 6 — Autentikasi Custom (PRD 6.1)
- [x] LoginController + Livewire `Auth/LoginForm` (login pakai `username`, bukan email)
- [x] Cek `is_change_password`; jika 0 tampilkan `Auth/ModalGantiPassword` (dismissable)
- [x] Middleware role (Spatie) untuk gate dashboard per role
- [x] **Output:** login 3 role berfungsi, modal ganti password muncul & bisa di-skip

---

## FASE 2 — Master Data (PRD 6.2 & 6.3)

### Langkah 7 — Periode Ajaran + Switcher
- [x] `Admin/MasterData/PeriodeAjaranManager` (CRUD + set `is_aktif`, auto-nonaktifkan lain)
- [x] Switcher periode global (read-only mode untuk periode non-aktif)

### Langkah 8 — Wilayah, Paket, Tingkat
- [x] `WilayahManager` (CRUD)
- [x] `PaketManager` (CRUD)
- [x] `TingkatManager` (CRUD)

### Langkah 9 — Rombel + Mapel + Pemetaan
- [x] `RombelManager` (generate nama otomatis, set wali kelas, assign peserta didik)
- [x] `MapelManager`
- [x] `PemetaanGuruMapelRombel` (isi tabel `guru_mapel_rombel`)

### Langkah 10 — Manajemen Akun Guru & Reset Password
- [x] `Admin/Pengguna/GuruManager` (buat akun guru → otomatis buat `users`)
- [x] `ResetPasswordModal` (reset ke username, `is_change_password=0`, catat audit)

---

## FASE 3 — Jadwal & Import

### Langkah 11 — Jadwal Pelajaran (PRD 6.4)
- [x] `JadwalManager` (Admin = semua rombel; Wali Kelas = rombelnya, via Policy)
- [x] `JadwalViewer` (read-only untuk Guru & Peserta Didik)

### Langkah 12 — Import Excel Peserta Didik (PRD 6.5 & Bab 7)
- [x] Template `.xlsx` kolom A–Z
- [x] `Admin/ImportExcel/UploadForm` — proses synchronous, validasi per baris
- [x] `RingkasanHasilImport` — berhasil/gagal + alasan per baris
- [x] Output: buat `users + peserta_didik + alamat + ortu` + masuk Rombel otomatis

---

## FASE 4 — Modul Transaksional

### Langkah 13 — Absensi (PRD 6.6)
- [x] `Absensi/BukaSesiAbsensi` (Guru)
- [x] `TombolHadir` (Peserta Didik)
- [x] `RekapAbsensiRealtime` (polling Livewire, bukan websocket)

### Langkah 14 — Materi (PRD 6.7)
- [x] `Guru/Materi/DaftarMateri`
- [x] `FormMateri` (tipe: text/file/link_video/gambar)

### Langkah 15 — Tugas (PRD 6.8)
- [x] `DaftarTugas`, `FormTugas`
- [x] `DaftarSubmisi`, `FormPenilaianTugas`
- [x] Submisi setelah deadline ditandai "Terlambat"

### Langkah 16 — CBT (PRD 6.9) — modul paling kompleks
- [x] Guru: `DaftarCbt`, `FormCbt`, `FormSoalCbt`, `DaftarHasilCbt`, `FormKoreksiUraian`
- [x] Peserta Didik: `DaftarCbtTersedia`, `PengerjaanCbt` (timer Alpine + auto-submit)
- [x] Auto-grading PG di Service; validasi waktu di server saat submit
- [x] Status penilaian per peserta didik di `hasil_cbt`

---

## FASE 5 — Penutup Akademik

### Langkah 17 — Kenaikan Kelas (PRD 6.10)
- [x] `KenaikanKelas/PenentuanStatus` (Wali Kelas)
- [x] `AssignRombelBaru` (Admin, Ruang Tunggu)

### Langkah 18 — Penilaian Akhir / Rapor (PRD 6.11)
- [x] `Penilaian/FormNilaiKomponen` (hitung `nilai_referensi` via Service saat form dibuka)
- [x] Konversi grade (Service / `konfigurasi_grade`)
- [x] `PreviewRapor` + `CetakRaporPdf` (dompdf, kop 3 kolom dari `public/images/`)
- [x] Prasyarat: aset logo sudah dikumpulkan (PRD Tahap 10.5)
- [x] Kop rapor menarik data dari `SettingService` (bukan hardcode)

---

## FASE 6 — Pengaturan Aplikasi & Log

### Langkah 19 — Modul Settings (Maintenance, Identitas PKBM, Toggle Modul, Grade)
- [x] Migration `settings`, `konfigurasi_grade` (database.md Bagian 8)
- [x] `SettingSeeder` (isi default termasuk `modul_materi_aktif`)
- [x] `GradeSeeder` (default A/B/C/D)
- [x] `SettingService` — `get`/`set`/`getGroup` + cache & cast tipe
- [x] `GradeService` — `konversi($nilai): string` + validasi anti-tumpang-tindih
- [x] `CheckMaintenanceMode` middleware (grup `web`, Admin bypass → `maintenance.blade.php`)
- [x] Middleware/Policy toggle modul per route group (dua lapis — wajib)
- [x] `Admin/Pengaturan/SettingManager` (form per group + upload logo)
- [x] `Admin/Pengaturan/GradeManager` (CRUD rentang grade)
- [x] Sidebar baca `modul_*_aktif` dari `SettingService`
- [x] Layout & login tampilkan `nama_pkbm` dari Settings
- [x] **Output:** Maintenance memblokir non-Admin; modul nonaktif disembunyikan & URL diblokir

### Langkah 20 — Error Log & Bug Report
- [x] Migration `error_log`, `bug_report` (database.md Bagian 9)
- [x] Model `ErrorLog`, `BugReport` (tanpa `SoftDeletes`)
- [x] `ErrorLogService::catat($aksi, $throwable, $context)` (auto `user_id` + `url`)
- [x] Audit try-catch Fase 1–5 memanggil `ErrorLogService::catat()`
- [x] `Admin/ErrorLog/DaftarErrorLog` (filter tanggal, hapus per baris + hapus semua)
- [x] `BugReport/FormLaporBug` (semua role, tombol di topbar, auto `halaman_url`)
- [x] `Admin/BugReport/DaftarBugReport` (filter status, preview screenshot, ubah status)
- [x] **Output:** error PHP otomatis tercatat & terlihat Admin; semua role bisa lapor bug bergambar

---

> Catatan revisi desain HTML (`docs/design-references/*.html`) dilacak terpisah dan **bukan** bagian
> dari build-steps ini.

---
