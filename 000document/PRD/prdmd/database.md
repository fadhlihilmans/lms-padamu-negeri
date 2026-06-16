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
2. `users`
3. `guru`, `peserta_didik`
4. `peserta_didik_alamat`, `peserta_didik_ortu`
5. `periode_ajaran`, `wilayah`, `paket`
6. `tingkat` (FK → paket)
7. `rombel` (FK → wilayah, paket, tingkat, guru)
8. `peserta_didik_rombel`, `mapel`
9. `guru_mapel_rombel` (FK → guru, mapel, rombel, periode_ajaran)
10. `jadwal_pelajaran`
11. `materi`, `tugas`, `tugas_submisi`
12. `cbt`, `cbt_soal`, `hasil_cbt`, `cbt_jawaban_peserta`
13. `sesi_absensi`, `absensi_detail`
14. `kenaikan_kelas`
15. `rapor`, `rapor_nilai_mapel`, `rapor_nilai_komponen`
16. `konfigurasi_grade` (opsional)

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
| grade | ENUM('A','B','C','D') NOT NULL | konversi otomatis dari nilai_akhir |
| catatan | TEXT NULL | |

### konfigurasi_grade (opsional, jika Admin perlu atur rentang grade)
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK | |
| grade | ENUM('A','B','C','D') NOT NULL | |
| nilai_min | TINYINT UNSIGNED NOT NULL | |
| nilai_max | TINYINT UNSIGNED NOT NULL | |

> Catatan: `nilai_referensi` di `rapor_nilai_komponen` SENGAJA dihitung ulang saat
> form dibuka (Service Class), bukan disimpan sebagai cache permanen — supaya tidak
> usang kalau ada Tugas/CBT yang dinilai ulang. Lihat PRD 5.7.

---

## Keputusan yang masih perlu Anda kunci sebelum migration
- Apakah `konfigurasi_grade` masuk v1, atau rentang grade di-hardcode di Service dulu?
- Panjang VARCHAR final (angka di atas sudah aman, sesuaikan jika perlu).
- Perlu `softDeletes` (kolom `deleted_at`) di tabel mana saja? (Default: tidak ada,
  kecuali Anda mau arsip data peserta didik lulus.)
