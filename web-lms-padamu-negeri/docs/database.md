# database.md — Skema Database FINAL (TERKUNCI)

> **Status:** Draft awal dari PRD Bab 5. Tinjau & setujui SELURUH tabel sebelum
> menulis migration pertama (PRD Bab 10.1). Setelah disetujui, **file ini adalah
> satu-satunya sumber kebenaran** untuk semua migration & model. Jika butuh ubah
> kolom, ubah DI SINI dulu, baru ubah migration.
>
> Konvensi: setiap tabel punya `id` BIGINT UNSIGNED auto-increment + `created_at`
> & `updated_at` (timestamps Laravel), kecuali ditulis lain. FK = BIGINT UNSIGNED.

## Urutan Migration (ikuti dependency)

1. (Spatie) `php artisan permission:table` → roles, permissions, dst.
2. `users` (dengan kolom `deleted_at`)
3. `guru`, `peserta_didik` (keduanya dengan `deleted_at`)
4. `peserta_didik_alamat`, `peserta_didik_ortu`
5. `periode_ajaran`, `wilayah`, `paket`
6. `tingkat` (FK → paket)
7. `rombel` (FK → wilayah, paket, tingkat, guru; dengan `deleted_at`)
8. `peserta_didik_rombel` (dengan `deleted_at`), `mapel`
9. `guru_mapel_rombel` (FK → guru, mapel, rombel, periode_ajaran)
10. `jadwal_pelajaran`
11. `materi`, `tugas`, `tugas_submisi` (`materi`/`tugas` dengan `deleted_at`, `tugas_submisi` dengan `deleted_at`)
12. `cbt`, `cbt_soal`, `hasil_cbt`, `cbt_jawaban_peserta` (`cbt` & `hasil_cbt` dengan `deleted_at`)
13. `sesi_absensi`, `absensi_detail`
14. `kenaikan_kelas` (dengan `deleted_at`)
15. `rapor` (dengan `deleted_at`), `rapor_nilai_mapel`, `rapor_nilai_komponen`
16. `settings`, `konfigurasi_grade`
17. `error_log`, `bug_report`

> Lihat bagian "Soft Delete — Tabel yang Menerapkan `deleted_at`" di bawah untuk
> daftar lengkap & alasannya sebelum menulis tiap migration.

---

## 1. Pengguna

### users
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK | |
| username | VARCHAR(50) UNIQUE NOT NULL | dari NIP (guru) / NIPD (peserta didik) |
| password | VARCHAR(255) NOT NULL | hash; nilai awal = username |
| is_change_password | BOOLEAN NOT NULL DEFAULT 0 | 0 = modal ganti password muncul tiap login |
| is_active | BOOLEAN NOT NULL DEFAULT 1 | tidak diubah otomatis saat lulus |

Role tidak disimpan sebagai kolom — dikelola Spatie (`model_has_roles`).

### guru
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK | |
| user_id | FK → users, UNIQUE, onDelete cascade | |
| nip | VARCHAR(30) UNIQUE NOT NULL | |
| nama_lengkap | VARCHAR(150) NOT NULL | |
| no_hp | VARCHAR(20) NULL | |

### peserta_didik
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK | |
| user_id | FK → users, UNIQUE, onDelete cascade | |
| nipd | VARCHAR(30) UNIQUE NOT NULL | |
| nisn | VARCHAR(20) UNIQUE NULL | Nomor Induk Siswa Nasional, opsional |
| nik | VARCHAR(20) NULL | |
| nama_lengkap | VARCHAR(150) NOT NULL | |
| jenis_kelamin | ENUM('L','P') NOT NULL | |
| tempat_lahir | VARCHAR(100) NULL | |
| tanggal_lahir | DATE NULL | |
| agama | VARCHAR(30) NULL | |
| no_hp | VARCHAR(20) NULL | |
| status_akademik | ENUM('aktif','lulus','pindah','keluar') NOT NULL DEFAULT 'aktif' | |

Index: `nipd` (unique), `status_akademik`.

### peserta_didik_alamat
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK | |
| peserta_didik_id | FK → peserta_didik, onDelete cascade | |
| alamat | TEXT NULL | |
| rt | VARCHAR(5) NULL | |
| rw | VARCHAR(5) NULL | |
| dusun | VARCHAR(100) NULL | |
| kelurahan | VARCHAR(100) NULL | |
| kecamatan | VARCHAR(100) NULL | |
| kode_pos | VARCHAR(10) NULL | |

### peserta_didik_ortu
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK | |
| peserta_didik_id | FK → peserta_didik, onDelete cascade | |
| jenis | ENUM('ayah','ibu','wali') NOT NULL | |
| nama | VARCHAR(150) NULL | |
| no_hp | VARCHAR(20) NULL | |
| hubungan_wali | VARCHAR(50) NULL | hanya relevan jika jenis='wali' |

---

## 2. Struktur Akademik

### periode_ajaran
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK | |
| tahun_ajaran | VARCHAR(9) NOT NULL | mis. "2023/2024" |
| semester | ENUM('ganjil','genap') NOT NULL | |
| is_aktif | BOOLEAN NOT NULL DEFAULT 0 | hanya 1 baris boleh true (dijaga di level aplikasi) |

Unique: (`tahun_ajaran`,`semester`).

### wilayah
| id | BIGINT PK | |
|---|---|---|
| nama | VARCHAR(100) NOT NULL | Botolambat, Pondok 1, dst. |

### paket
| id | BIGINT PK | |
|---|---|---|
| nama | VARCHAR(50) NOT NULL | Paket A / B / C |

### tingkat
| id | BIGINT PK | |
|---|---|---|
| paket_id | FK → paket, onDelete cascade | |
| nama | VARCHAR(50) NOT NULL | Kelas 10, 11, 12 |

### rombel
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK | |
| wilayah_id | FK → wilayah, onDelete restrict | |
| paket_id | FK → paket, onDelete restrict | |
| tingkat_id | FK → tingkat, onDelete restrict | |
| tahun_ajaran | VARCHAR(9) NOT NULL | terikat ke TA, bukan semester |
| wali_kelas_id | FK → guru, NULL, onDelete set null | wajib diisi sebelum TA berjalan (validasi aplikasi) |
| nama | VARCHAR(150) NOT NULL | hasil generate, mis. "Kelas 10 Botolambat Paket C – TA 2023/2024" |

### peserta_didik_rombel (pivot)
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK | |
| peserta_didik_id | FK → peserta_didik, onDelete cascade | |
| rombel_id | FK → rombel, onDelete cascade | |

Unique: (`peserta_didik_id`,`rombel_id`).

### mapel
| id | BIGINT PK | |
|---|---|---|
| nama | VARCHAR(100) NOT NULL | hanya Admin yang kelola |

### guru_mapel_rombel (pivot otorisasi)
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK | |
| guru_id | FK → guru, onDelete cascade | |
| mapel_id | FK → mapel, onDelete cascade | |
| rombel_id | FK → rombel, onDelete cascade | |
| periode_ajaran_id | FK → periode_ajaran, onDelete cascade | |

Unique: (`guru_id`,`mapel_id`,`rombel_id`,`periode_ajaran_id`).
**Tabel ini jadi dasar otorisasi Materi/Tugas/CBT/Absensi.**

### jadwal_pelajaran
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK | |
| rombel_id | FK → rombel, onDelete cascade | |
| mapel_id | FK → mapel, onDelete restrict | |
| guru_id | FK → guru, onDelete restrict | |
| hari | ENUM('senin','selasa','rabu','kamis','jumat','sabtu','minggu') | |
| jam_mulai | TIME NOT NULL | |
| jam_selesai | TIME NOT NULL | |

---

## 3. Kegiatan Belajar

### materi
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK | |
| guru_mapel_rombel_id | FK → guru_mapel_rombel, onDelete cascade | |
| judul | VARCHAR(200) NOT NULL | |
| deskripsi | TEXT NULL | |
| tipe_konten | ENUM('text','file','link_video','gambar') NOT NULL | |
| file_path | VARCHAR(255) NULL | jika tipe file/gambar |
| url | VARCHAR(500) NULL | jika tipe link_video |

### tugas
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK | |
| guru_mapel_rombel_id | FK → guru_mapel_rombel, onDelete cascade | |
| judul | VARCHAR(200) NOT NULL | |
| deskripsi | TEXT NULL | |
| lampiran_path | VARCHAR(255) NULL | |
| deadline | DATETIME NOT NULL | |

### tugas_submisi
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK | |
| tugas_id | FK → tugas, onDelete cascade | |
| peserta_didik_id | FK → peserta_didik, onDelete cascade | |
| file_path | VARCHAR(255) NULL | |
| isi_text | TEXT NULL | |
| waktu_submit | DATETIME NOT NULL | |
| nilai | TINYINT UNSIGNED NULL | 0–100, NULL sebelum dinilai |

Unique: (`tugas_id`,`peserta_didik_id`).

---

## 4. CBT

### cbt
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK | |
| guru_mapel_rombel_id | FK → guru_mapel_rombel, onDelete cascade | |
| nama_ujian | VARCHAR(200) NOT NULL | |
| kkm | TINYINT UNSIGNED NOT NULL | |
| tanggal_mulai | DATETIME NOT NULL | |
| durasi_menit | SMALLINT UNSIGNED NOT NULL | |
| tampilkan_nilai_otomatis | BOOLEAN NOT NULL DEFAULT 0 | hanya relevan utk PG |
| jenis_cbt | ENUM('pilihan_ganda','uraian','campuran') NOT NULL | dideteksi dari komposisi soal |

### cbt_soal
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK | |
| cbt_id | FK → cbt, onDelete cascade | |
| tipe_soal | ENUM('pilihan_ganda','uraian') NOT NULL | |
| pertanyaan | TEXT NOT NULL | |
| pilihan_jawaban | JSON NULL | hanya untuk PG, mis. {"A":"...","B":"..."} |
| kunci_jawaban | VARCHAR(10) NULL | hanya untuk PG |

### hasil_cbt
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK | |
| cbt_id | FK → cbt, onDelete cascade | |
| peserta_didik_id | FK → peserta_didik, onDelete cascade | |
| waktu_mulai | DATETIME NOT NULL | |
| waktu_submit | DATETIME NULL | auto-submit jika durasi habis |
| nilai_pg | TINYINT UNSIGNED NULL | otomatis saat submit |
| nilai_uraian | TINYINT UNSIGNED NULL | diisi guru manual |
| nilai_akhir | TINYINT UNSIGNED NULL | gabungan PG + uraian |
| status_penilaian | ENUM('otomatis','menunggu_koreksi','selesai_dinilai') NOT NULL | per peserta didik |
| nilai_ditampilkan | BOOLEAN NOT NULL DEFAULT 0 | override guru: tampilkan nilai ke PD walau `cbt.tampilkan_nilai_otomatis` = false (Langkah 16 revisi) |

Unique: (`cbt_id`,`peserta_didik_id`).

### cbt_jawaban_peserta
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK | |
| hasil_cbt_id | FK → hasil_cbt, onDelete cascade | |
| cbt_soal_id | FK → cbt_soal, onDelete cascade | |
| jawaban | TEXT NULL | huruf (PG) atau teks (uraian) |
| skor_uraian | TINYINT UNSIGNED NULL | diisi guru saat koreksi uraian |

---

## 5. Absensi

### sesi_absensi
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK | |
| guru_mapel_rombel_id | FK → guru_mapel_rombel, onDelete cascade | |
| tanggal | DATE NOT NULL | |
| status_sesi | ENUM('terbuka','ditutup') NOT NULL DEFAULT 'terbuka' | |

### absensi_detail
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK | |
| sesi_absensi_id | FK → sesi_absensi, onDelete cascade | |
| peserta_didik_id | FK → peserta_didik, onDelete cascade | |
| status | ENUM('hadir','izin','sakit','alpa') NOT NULL | |
| waktu_klik | DATETIME NULL | NULL jika diisi manual guru |
| diubah_manual_oleh | FK → users, NULL, onDelete set null | |

Unique: (`sesi_absensi_id`,`peserta_didik_id`).

---

## 6. Kenaikan Kelas

### kenaikan_kelas
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK | |
| peserta_didik_id | FK → peserta_didik, onDelete cascade | |
| rombel_asal_id | FK → rombel, onDelete restrict | |
| status_keputusan | ENUM('naik','tinggal','lulus','pindah_paket','pindah_wilayah') NOT NULL | |
| rombel_tujuan_id | FK → rombel, NULL, onDelete set null | diisi Admin di TA baru |
| diputuskan_oleh | FK → users, onDelete restrict | wali kelas |

---

## 7. Rapor

### rapor
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK | |
| peserta_didik_id | FK → peserta_didik, onDelete cascade | |
| periode_ajaran_id | FK → periode_ajaran, onDelete restrict | |
| rombel_id | FK → rombel, onDelete restrict | |
| catatan_wali_kelas | TEXT NULL | umum, bukan per mapel |
| status | ENUM('draft','terbit') NOT NULL DEFAULT 'draft' | |
| diterbitkan_oleh | FK → users, NULL, onDelete set null | |

Unique: (`peserta_didik_id`,`periode_ajaran_id`).

### rapor_nilai_mapel
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK | |
| rapor_id | FK → rapor, onDelete cascade | |
| mapel_id | FK → mapel, onDelete restrict | |
| diinput_oleh | FK → users, onDelete restrict | guru mapel |
| catatan_mapel | TEXT NULL | |

Unique: (`rapor_id`,`mapel_id`).

### rapor_nilai_komponen
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK | |
| rapor_nilai_mapel_id | FK → rapor_nilai_mapel, onDelete cascade | |
| nama_komponen | VARCHAR(50) NOT NULL | Pengetahuan, Keterampilan, Sikap |
| nilai_referensi | TINYINT UNSIGNED NULL | usulan dari rata-rata Tugas+CBT (dihitung saat form dibuka, tidak di-cache permanen) |
| nilai_akhir | TINYINT UNSIGNED NOT NULL | final; default = nilai_referensi, bisa override |
| grade | ENUM('A','B','C','D') NOT NULL | konversi otomatis dari nilai_akhir, lihat tabel `konfigurasi_grade` di Bagian 8.1 |
| catatan | TEXT NULL | |

> Catatan: `nilai_referensi` SENGAJA dihitung ulang saat form dibuka (Service
> Class), bukan disimpan sebagai cache permanen — supaya tidak usang kalau ada
> Tugas/CBT yang dinilai ulang. Lihat PRD 5.7.

---

## 8. Pengaturan Aplikasi (Settings)

### settings
Tabel key-value generik untuk konfigurasi global aplikasi. Dikelola **hanya
oleh Admin**, lewat satu halaman pengaturan (lihat `build-steps.md` Langkah 19
dan `stitch-prompts.md` Bagian 12).

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK | |
| key | VARCHAR(100) UNIQUE NOT NULL | identifier tetap, snake_case, mis. `maintenance_mode` |
| value | TEXT NULL | nilai disimpan sebagai string; cast ke tipe asli di level aplikasi (lihat catatan) |
| type | ENUM('boolean','string','integer','json') NOT NULL DEFAULT 'string' | menentukan cara cast `value` |
| group | VARCHAR(50) NOT NULL DEFAULT 'general' | pengelompokan tampilan, mis. `general`, `kop_rapor`, `upload`, `modul` |
| label | VARCHAR(150) NOT NULL | teks ramah-pengguna untuk form, mis. "Mode Maintenance" |
| description | VARCHAR(255) NULL | bantuan kecil di bawah field |

> Tidak ada logic kalkulasi di tabel ini (sesuai prinsip 2.3) — `type` hanya
> metadata untuk cast, casting sungguhan terjadi di `SettingService` (lihat
> `CLAUDE.md`).

### Baris Awal (diisi via Seeder, bukan migration)

| key | type | group | value default | Kegunaan |
|---|---|---|---|---|
| `maintenance_mode` | boolean | general | `false` | Jika `true`, seluruh akses non-Admin diarahkan ke halaman maintenance. |
| `nama_pkbm` | string | general | `PKBM Padamu Negeri` | Ditampilkan di sidebar, topbar, login, kop rapor. |
| `alamat_pkbm` | string | kop_rapor | (diisi manual) | Blok tengah kop rapor. |
| `telepon_pkbm` | string | kop_rapor | (diisi manual) | Blok tengah kop rapor. |
| `email_pkbm` | string | kop_rapor | (diisi manual) | Blok tengah kop rapor. |
| `logo_pkbm_path` | string | kop_rapor | (path file upload) | Logo kanan kop rapor & sidebar. Disimpan via `storage:link`, BUKAN base64. |
| `logo_kabupaten_path` | string | kop_rapor | (path file upload) | Logo kiri kop rapor. |
| `max_upload_materi_mb` | integer | upload | `10` | Batas ukuran file materi, divalidasi di form Livewire. |
| `max_upload_tugas_mb` | integer | upload | `10` | Batas ukuran file submisi tugas. |
| `modul_cbt_guru_aktif` / `modul_cbt_pd_aktif` | boolean | modul | `true` | Toggle **terpisah per role** (Guru & Peserta Didik). Lihat aturan dua-lapis di `CLAUDE.md`. |
| `modul_tugas_guru_aktif` / `modul_tugas_pd_aktif` | boolean | modul | `true` | idem. |
| `modul_absensi_guru_aktif` / `modul_absensi_pd_aktif` | boolean | modul | `true` | idem. |
| `modul_materi_guru_aktif` / `modul_materi_pd_aktif` | boolean | modul | `true` | idem. |

> Tambah baris baru kapan saja tanpa migration baru — cukup `INSERT` lewat
> Seeder atau lewat UI Pengaturan itu sendiri (form generate otomatis dari
> baris yang ada, dikelompokkan per `group`).

---

### konfigurasi_grade
Rentang nilai untuk konversi otomatis ke huruf grade (A/B/C/D), dapat diubah
Admin lewat modul Pengaturan tanpa perlu deploy ulang kode. **Masuk v1**
(keputusan final — lihat penjelasan di bawah).

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK | |
| grade | ENUM('A','B','C','D') NOT NULL UNIQUE | |
| nilai_min | TINYINT UNSIGNED NOT NULL | |
| nilai_max | TINYINT UNSIGNED NOT NULL | |

> **Kenapa masuk v1, bukan hardcode:** kebijakan rentang nilai bisa berubah
> dari waktu ke waktu sesuai aturan Dinas/lembaga. Tanpa tabel ini, perubahan
> rentang nilai berarti developer harus ubah kode `GradeService` dan deploy
> ulang setiap kali aturan berubah. Dengan tabel ini, Admin mengubahnya sendiri
> lewat UI. Biaya implementasi rendah (1 tabel kecil, CRUD sederhana mirip
> Wilayah/Paket), manfaatnya signifikan untuk lembaga ini.
> Validasi penting di Service: rentang antar grade tidak boleh tumpang tindih
> (dicek di level aplikasi saat Admin menyimpan, bukan constraint database).

Default seed: A = 85–100, B = 75–84, C = 65–74, D = 0–64 (sesuaikan dengan
aturan resmi lembaga sebelum go-live).

---

## 9. Log & Pelaporan Masalah

Dua tabel berbeda tujuan — jangan digabung:
- **`error_log`** — dicatat **otomatis oleh sistem** saat terjadi Exception PHP
  (`catch (\Throwable $th)`). Untuk developer/Admin men-debug error teknis.
- **`bug_report`** — dikirim **manual oleh pengguna** (Admin/Guru/Peserta Didik)
  saat menemukan sesuatu yang janggal walau sistem tidak crash (mis. tombol
  tidak berfungsi, tampilan rusak). Untuk Admin menindaklanjuti laporan UX/fungsional.

### error_log

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK | |
| aksi | VARCHAR(255) NOT NULL | label aksi yang sedang dilakukan saat error terjadi, mis. "Update Jabatan" (kolom `log` pada draft Anda). |
| pesan_error | TEXT NOT NULL | hasil `$th->getMessage()` (kolom `desc` pada draft Anda). |
| context | JSON NULL | opsional: trace singkat, request data, atau info tambahan untuk reproduce. |
| user_id | FK → users, NULL, onDelete set null | siapa yang sedang login saat error terjadi (jika ada). |
| url | VARCHAR(500) NULL | URL/route saat error terjadi. |
| created_at | TIMESTAMP | **Tidak ada `updated_at`** (log tidak pernah diubah, hanya dibuat & dihapus). |

**Tidak pakai `deleted_at` (no soft delete).** Sesuai permintaan: Admin boleh
hapus satu per satu maupun **Hapus Semua (`deleteAll`)** langsung permanen —
log error bersifat operasional jangka pendek, bukan arsip historis.

> Catatan penamaan: saya sarankan `aksi` & `pesan_error` (lebih jelas dibaca
> tim lain dibanding `log`/`desc` yang ambigu), tapi fungsinya identik dengan
> draft Anda — tinggal mapping nama kolom di `ErrorLogService` (lihat `CLAUDE.md`).

### bug_report

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK | |
| user_id | FK → users, onDelete cascade | siapa pun yang lapor — Admin, Guru, atau Peserta Didik (tidak nullable, harus login). |
| judul | VARCHAR(150) NOT NULL | judul singkat laporan. |
| deskripsi | TEXT NOT NULL | penjelasan masalah dari pengguna. |
| screenshot_path | VARCHAR(255) NULL | path gambar terlampir, via `storage:link`. |
| halaman_url | VARCHAR(500) NULL | URL saat laporan dikirim (membantu Admin reproduce). |
| status | ENUM('baru','diproses','selesai','ditolak') NOT NULL DEFAULT 'baru' | ditindaklanjuti Admin. |
| catatan_admin | TEXT NULL | catatan tindak lanjut dari Admin. |

**Tidak pakai soft delete** — sama seperti `error_log`, bersifat operasional;
Admin boleh hapus laporan yang sudah `selesai`/`ditolak` secara permanen.

---

## Soft Delete — Tabel yang Menerapkan `deleted_at`

Prinsip yang dipakai: soft delete untuk data yang **punya jejak historis,
akademik, atau legal** — kehilangan datanya secara permanen merugikan meski
"dihapus" dari penggunaan sehari-hari. Hard delete untuk data **operasional/
sampah** yang aman hilang permanen.

| Tabel | Soft Delete? | Alasan |
|---|---|---|
| `peserta_didik` | **Ya** | Riwayat akademik (nilai, rapor, kenaikan kelas) tetap harus bisa ditelusuri meski peserta didik "dihapus" dari sistem (pindah/keluar/kesalahan input). Hard delete akan merusak relasi FK ke rapor, hasil_cbt, dll. |
| `guru` | **Ya** | Riwayat pengajaran (materi, tugas, CBT yang pernah dibuat) tetap perlu tertaut ke guru meski sudah tidak aktif/keluar. |
| `users` | **Ya** | Mengikuti `peserta_didik`/`guru` — satu akun terhubung ke banyak riwayat (absensi, submisi, dsb). Hapus akun tidak boleh menghapus jejak siapa yang melakukan apa. |
| `rombel` | **Ya** | Rombel tahun lalu harus tetap bisa dilihat di laporan historis meski sudah "ditutup". |
| `rapor` | **Ya** | Dokumen resmi/legal — tidak boleh hilang permanen secara tidak sengaja. |
| `tugas`, `cbt`, `materi` | **Ya** | Jika dihapus guru, submisi/hasil/jawaban peserta didik yang sudah terkumpul tetap perlu basis riwayat (nilai yang sudah masuk rapor merujuk ke sini secara tidak langsung). |
| `tugas_submisi`, `hasil_cbt` | **Ya** | Bukti pengerjaan peserta didik — riwayat penilaian, jangan hilang permanen akibat klik salah. |
| `kenaikan_kelas` | **Ya** | Keputusan akademik formal, perlu jejak audit permanen. |
| `peserta_didik_rombel` (pivot keanggotaan) | **Ya** | Supaya riwayat "peserta didik X pernah di rombel Y tahun Z" tetap terlacak meski dipindah rombel. |
| `wilayah`, `paket`, `tingkat`, `mapel` | Tidak perlu | Master data dasar; biasanya tidak pernah benar-benar "dihapus", hanya bertambah. Jika perlu nonaktifkan, pakai kolom `is_aktif`, bukan soft delete. |
| `jadwal_pelajaran`, `sesi_absensi`, `absensi_detail` | Tidak perlu | Operasional harian; histori kehadiran tetap ada di laporan rekap, baris jadwal lama yang dihapus tidak merugikan. |
| `error_log`, `bug_report` | **Tidak** (lihat Bagian 9) | Sengaja hard delete sesuai kebutuhan operasional. |
| `settings`, `konfigurasi_grade` | Tidak perlu | Konfigurasi sistem, bukan data transaksional. |
| Tabel pivot otorisasi (`guru_mapel_rombel`) | Tidak perlu | Jika pemetaan dicabut, cukup hard delete — riwayat siapa pernah mengajar apa sudah cukup terwakili lewat `materi`/`tugas`/`cbt` yang mereka buat (yang soft delete). |

> **Catatan teknis:** tambahkan trait `SoftDeletes` di Model untuk tabel
> bertanda **Ya**, dan kolom `deleted_at TIMESTAMP NULL` di migration-nya.
> Saat query relasi FK ke tabel ber-soft-delete, ingat Eloquent otomatis
> mengecualikan baris yang sudah di-soft-delete kecuali eksplisit
> `withTrashed()` — penting untuk laporan rekap historis (Wali Kelas/Admin
> yang lihat data tahun lalu harus pakai `withTrashed()` di Service terkait).

---

## Toggle Modul — Keputusan Final

Toggle aktif/nonaktif v1: **CBT, Tugas, Absensi, Materi**. Import Excel TIDAK
memakai toggle (dipertimbangkan, tapi tidak disertakan di v1).

| Modul | Status | Setting Key |
|---|---|---|
| CBT | ✅ Toggle | `modul_cbt_aktif` |
| Tugas | ✅ Toggle | `modul_tugas_aktif` |
| Absensi | ✅ Toggle | `modul_absensi_aktif` |
| Materi | ✅ Toggle | `modul_materi_aktif` |
| Import Excel | Tidak ada toggle | — |
| Jadwal Pelajaran | Tidak ada toggle | — |
| Penilaian Akhir/Rapor | Tidak ada toggle (dikontrol via `periode_ajaran.is_aktif` & status `rapor`) | — |

---

## Keputusan yang Sudah Dikunci (FINAL)

- ✅ `konfigurasi_grade` masuk v1 (lihat Bagian 8 di atas).
- ✅ Panjang VARCHAR: gunakan default Laravel (`VARCHAR(255)`) di semua kolom
  string kecuali disebutkan eksplisit berbeda di dokumen ini (mis. `username`,
  `nipd`, `nip` tetap dipersempit untuk konsistensi index, tapi tidak wajib —
  boleh diseragamkan ke 255 juga jika ingin lebih sederhana).
- ✅ Soft delete diterapkan sesuai tabel di atas.
- ✅ Toggle modul v1: **CBT, Tugas, Absensi, Materi**.