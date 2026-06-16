# CLAUDE.md — Konteks Proyek LMS Padamu Negeri

> File ini dibaca **otomatis** oleh Claude Code setiap sesi. Isinya aturan main
> yang tidak boleh dilanggar. PRD lengkap & detail ada di file terpisah (lihat di
> bawah) — JANGAN salin ulang isinya ke sini, cukup rujuk.

## Dokumen Acuan (baca sebelum mengerjakan apa pun)

- `docs/prd.md` — Product Requirements Document (sumber kebenaran fitur & flow).
- `docs/database.md` — **Skema database FINAL & TERKUNCI.** Semua migration & model
  WAJIB mengikuti file ini persis. Jangan mengarang tipe kolom sendiri.
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
