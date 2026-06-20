# CLAUDE.md — Konteks Proyek LMS Padamu Negeri

> File ini dibaca **otomatis** oleh Claude Code setiap sesi. Isinya aturan main
> yang tidak boleh dilanggar. PRD lengkap & detail ada di file terpisah (lihat di
> bawah) — JANGAN salin ulang isinya ke sini, cukup rujuk.

## Dokumen Acuan (baca sebelum mengerjakan apa pun)

- `docs/prd.md` — Product Requirements Document (sumber kebenaran fitur & flow).
- `docs/database.md` — **Skema database FINAL & TERKUNCI.** Semua migration & model
  WAJIB mengikuti file ini persis. Jangan mengarang tipe kolom sendiri. Termasuk
  tabel `settings` (Bagian 8) — lihat aturan akses di bawah.
- `docs/build-steps.md` — Urutan pengerjaan, satu langkah satu commit.
- `docs/design-guide.md` — Cara menerjemahkan desain Google Stitch → TailAdmin → Livewire.

**Aturan:** Sebelum membuat migration/model/fitur, selalu buka `docs/database.md`
dan `docs/build-steps.md`. Jika ada konflik antara instruksiku saat chat dan
file-file ini, file ini yang menang — konfirmasikan dulu ke saya kalau ada bentrok.

## Tech Stack (kunci, jangan diganti)

- Laravel 13 — monolith, **tanpa API** (REST/GraphQL).
- Livewire 3 — **Multi-File Component** (class PHP & Blade view DIPISAH, bukan single-file/Volt).
- MySQL.
- TailAdmin sebagai basis UI (Tailwind CSS).
- Alpine.js (bawaan Livewire 3) — hanya untuk interaksi ringan, mis. timer CBT.
- Spatie Laravel-Permission — role & permission.
- Penyimpanan file: filesystem lokal (`storage:link`). Tidak ada queue/cron/scheduler.

## Aturan Coding Wajib

1. **Tanpa logic di database.** Tidak ada trigger, stored procedure, function, view
   berlogika, atau event scheduler MySQL. SEMUA logic bisnis (validasi, kalkulasi
   nilai, konversi grade, penentuan status) hidup di level aplikasi: Model, Service
   Class, atau Action Class. Lihat PRD Bab 2.3.
2. **Cascade pakai `onDelete()` di migration Laravel**, bukan trigger DB.
3. **Setiap entitas master tetap tabel tersendiri** (termasuk `wilayah`, `paket`,
   `tingkat` yang kolomnya minim) — JANGAN dijadikan enum/konstanta di kode.
4. **Istilah "Peserta Didik", bukan "siswa"** — di kode, UI, komentar, dan nama variabel.
5. **Login pakai `username` (NIP/NIPD), bukan email.** Auth dibangun manual
   (Controller + Livewire form), bukan Breeze/Jetstream.
6. **Wali Kelas BUKAN role.** Ia atribut `wali_kelas_id` di tabel `rombel`.
   Permission kondisional dicek lewat Policy (`Auth::id() === rombel.wali_kelas_id`).
7. Proses berat (import Excel, kompilasi rapor) dijalankan **synchronous** — jangan
   buat Job/Queue.
8. Layout halaman pakai pola **`{{ $slot }}`** dari Livewire (lihat `docs/design-guide.md`).
   **Setiap halaman/komponen WAJIB responsive (breakpoint Tailwind) dan
   user-friendly** (state kosong, loading, konfirmasi aksi merusak) — lihat
   standar baku di `docs/design-guide.md` Bagian D. Ini berlaku otomatis,
   tidak perlu diminta ulang tiap kali membangun komponen baru.
9. **Pengaturan global pakai tabel `settings` (key-value), bukan `.env` atau
   konstanta kode.** Akses lewat `SettingService::get('key')` /
   `SettingService::set('key', $value)` — Service ini yang melakukan cast sesuai
   kolom `type` (`boolean`/`string`/`integer`/`json`). Jangan query tabel
   `settings` langsung dari Livewire component; selalu lewat Service supaya
   caching & casting konsisten di satu tempat. Gunakan Laravel cache (`Cache::
   remember`) di Service ini agar tidak query DB di setiap request — invalidasi
   cache saat Admin menyimpan perubahan.
10. **Mode Maintenance** (`maintenance_mode`): dicek di Middleware global
    (`CheckMaintenanceMode`), bukan dicek manual di tiap controller/Livewire.
    Saat `true`: Admin tetap bisa login & akses penuh; role lain diarahkan ke
    halaman maintenance statis. Middleware ini didaftarkan di grup `web`
    sebelum middleware auth lain.
11. **Toggle modul (`modul_*_aktif`) WAJIB dicek di dua lapis, bukan satu:**
    - **Lapis 1 (UX):** sidebar menyembunyikan menu modul yang nonaktif.
    - **Lapis 2 (keamanan, WAJIB, jangan dilewati):** Middleware atau Policy
      di route group modul tersebut tetap memblokir akses langsung ke URL-nya
      meski menu disembunyikan. Tampilkan halaman "Modul belum tersedia"
      (bukan 404 polos) jika diakses saat nonaktif.
    - Jangan pernah menganggap "menu disembunyikan" sudah cukup aman — itu
      hanya kosmetik, bukan kontrol akses.
12. **Logo (`logo_pkbm_path`, `logo_kabupaten_path`) disimpan via
    `storage:link`** seperti file upload lain, path-nya saja yang disimpan di
    tabel `settings`. Jangan simpan logo sebagai base64 di kolom `value`.
13. **Soft delete WAJIB** di Model untuk tabel bertanda "Ya" pada daftar di
    `docs/database.md` (Bagian "Soft Delete"). Tambahkan trait `SoftDeletes`
    dan kolom `deleted_at` di migration-nya. Untuk laporan/rekap yang
    menampilkan data historis (mis. Wali Kelas melihat rapor tahun lalu),
    gunakan `withTrashed()` secara eksplisit di Service — jangan asumsikan
    data lama otomatis muncul di query biasa.
14. **Setiap blok `try-catch` yang menangani `\Throwable` WAJIB mencatat ke
    `error_log`** lewat `ErrorLogService::catat()`, bukan `Log::error()`
    bawaan Laravel saja (boleh keduanya, tapi `error_log` di database wajib
    ada supaya Admin bisa lihat dari UI). Pola standar:
    ```php
    try {
        // ...
    } catch (\Throwable $th) {
        app(ErrorLogService::class)->catat(
            aksi: 'Update Jabatan',
            throwable: $th,
        );
        $this->dispatch('failed-message', 'Maaf, terjadi kesalahan.');
    }
    ```
    `ErrorLogService::catat()` otomatis mengisi `pesan_error` dari
    `$th->getMessage()`, `user_id` dari `Auth::id()`, dan `url` dari
    `request()->fullUrl()` — komponen tidak perlu mengisi itu manual.
15. **Bug Report (`bug_report`) adalah fitur terpisah dari `error_log`** —
    untuk laporan manual pengguna (semua role boleh lapor), bukan exception
    otomatis. Jangan satukan logic keduanya di satu Service/tabel yang sama.

## Konvensi Penamaan

- Tabel & kolom: `snake_case`, bahasa Indonesia sesuai `docs/database.md`.
- Livewire component: `PascalCase`, namespace per modul (lihat struktur folder di PRD Bab 10.3).
- Satu modul = satu PR/commit logis. Jangan menggabung banyak modul dalam satu langkah.

## Cara Kerja Yang Saya Harapkan Dari Claude Code

- Kerjakan **satu langkah dari `docs/build-steps.md` setiap kali**, lalu berhenti
  dan tunggu saya review sebelum lanjut.
- Untuk migration, model, dan seeder: **buat satu file per tabel/model**, jangan
  digabung dalam satu file besar.
- Setelah membuat migration, jalankan `php artisan migrate` dan laporkan hasilnya.
- Jika butuh keputusan desain yang belum ada di dokumen, **tanya dulu**, jangan asumsi.
- Setelah selesai generate, bisa tambahkan jika sekarang sudah selesai pada fase ke- berapa, langkah ke- berapa dan sampai di list task yang mana.
- tambahkan search, pagination, pilihan untuk menampilkan berapa data, filter (jika dibutuhkan dan gunakan filter yang interaktif jika dibutuhkan seperti search, checkbox, etc) untuk tiap menu atau crud.
- jika membuat fungsi gunakan english.