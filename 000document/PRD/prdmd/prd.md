# PRD — LMS Kejar Paket Padamu Negeri

**Product Requirements Document — Sistem LMS Internal**
**Kejar Paket PKBM Padamu Negeri**

Versi 1.0 · 16 Juni 2026

**Stack:** Laravel 13 · Livewire 3 (Multi-File Component) · MySQL · TailAdmin
**Arsitektur:** Monolith, Tanpa API

---

## Daftar Isi

1. Pendahuluan
2. Tech Stack & Arsitektur
3. Definisi & Istilah
4. Role & Hak Akses (Permission Matrix)
5. Struktur Data Inti (ERD Kasar)
6. Modul Fitur (Flow Detail)
7. Template Excel Import Peserta Didik
8. Kebutuhan Non-Fungsional
9. Di Luar Lingkup (Out of Scope) Versi 1
10. Langkah Selanjutnya

---

# 1. Pendahuluan

## 1.1 Latar Belakang

PKBM Padamu Negeri merupakan lembaga pendidikan kesetaraan (Kejar Paket A/B/C) yang saat ini menjalankan proses akademik — absensi, distribusi materi, pengumpulan tugas, dan ujian — secara manual atau tersebar di berbagai alat (WhatsApp, Excel, kertas). Hal ini menyulitkan pelacakan data peserta didik, rekap kehadiran, dan penilaian secara terpusat.

Dokumen ini merangkum hasil diskusi dan koreksi rancangan awal (Rancangan 1 dan Rancangan 2) menjadi spesifikasi kebutuhan produk yang final dan dapat langsung dijadikan acuan pengembangan.

## 1.2 Tujuan Produk

Membangun sebuah sistem LMS (Learning Management System) internal yang menjadi satu pusat data dan aktivitas akademik bagi PKBM Padamu Negeri, mencakup:

- Pencatatan dan rekap kehadiran (absensi) peserta didik secara digital.
- Distribusi materi pembelajaran oleh guru ke peserta didik per mata pelajaran.
- Pengelolaan tugas (pengumpulan dan penilaian) menyerupai Google Classroom dalam skala sederhana.
- Pelaksanaan ujian/latihan berbasis komputer (CBT) dengan penilaian otomatis untuk soal pilihan ganda.
- Pengelolaan struktur akademik: tahun ajaran, semester, rombongan belajar (rombel), dan kenaikan kelas.
- Penilaian akhir berupa rapor sederhana per semester, dengan komponen nilai, grade, dan catatan per mata pelajaran, yang dapat dicetak dalam format PDF.
- Import data peserta didik secara massal melalui template Excel yang telah distandarkan.

## 1.3 Lingkup & Batasan Sistem

Sistem ini dirancang khusus untuk kebutuhan internal lembaga dengan skala kecil-menengah, bukan sistem akademik nasional/Dapodik. Beberapa batasan yang disepakati selama proses perancangan:

- Arsitektur monolith, tanpa lapisan API — tidak ada kebutuhan integrasi aplikasi mobile terpisah atau pihak ketiga pada versi ini.
- Tidak menggunakan istilah "siswa", melainkan "Peserta Didik" sesuai konteks pendidikan non-formal (Kejar Paket).
- Sistem tidak mereplikasi seluruh kompleksitas Dapodik — hanya data yang relevan secara operasional untuk lembaga yang disimpan.
- Rencana hosting menggunakan shared hosting, sehingga beberapa keputusan teknis (lihat Bab 8) disesuaikan dengan batasan tersebut.

## 1.4 Pihak Terkait (Stakeholders)

| Peran | Deskripsi Keterlibatan |
|---|---|
| Pemilik Produk / Pengelola Lembaga | Menentukan kebutuhan fungsional, menyetujui rancangan akhir. |
| Admin (Tata Usaha) | Pengguna utama untuk pengelolaan master data dan operasional harian. |
| Guru | Pengguna yang mengelola materi, tugas, CBT, dan absensi di mata pelajaran yang diampu. |
| Wali Kelas | Guru dengan tanggung jawab tambahan atas satu rombel tertentu. |
| Peserta Didik | Pengguna akhir yang mengikuti kegiatan belajar, mengerjakan tugas/CBT, dan melakukan absensi. |

---

# 2. Tech Stack & Arsitektur

## 2.1 Ringkasan Stack

| Komponen | Pilihan | Catatan |
|---|---|---|
| Framework Backend | Laravel 13 | Monolith, tanpa lapisan REST/GraphQL API. |
| Frontend Interaktif | Livewire 3.x (Multi-File Component) | Tidak menggunakan single-file component; PHP class dan Blade view dipisah. |
| Database | MySQL | Sesuai keputusan awal, kompatibel luas dengan shared hosting. |
| UI Template | TailAdmin | Dipertahankan sesuai rancangan awal, disesuaikan ke struktur Blade/Livewire. |
| Autentikasi | Laravel Auth dasar (manual, custom Controller + Livewire form) | Bukan Breeze penuh — mekanisme login non-standar (username = NIP/NIPD). |
| Otorisasi (Role & Permission) | Spatie Laravel-Permission | Role dasar (admin/guru/peserta_didik) dan permission granular per-mapel/per-rombel. |
| Interaktivitas Ringan (Timer CBT) | Alpine.js (bawaan Livewire 3) | Countdown timer di sisi browser tanpa polling server tiap detik. |
| Penyimpanan File | Local filesystem Laravel (`storage:link`) | Lihat Bab 8 untuk batasan terkait shared hosting. |
| Rencana Hosting | Shared Hosting | Tidak memerlukan dukungan cron job/queue worker (lihat Bab 9). |

## 2.2 Keputusan Arsitektur Penting

- **Tanpa API:** Tidak diperlukan endpoint REST/GraphQL terpisah. Seluruh interaktivitas (klik hadir, submit tugas, submit CBT, rekap real-time) ditangani oleh mekanisme AJAX internal Livewire 3.
- **Autentikasi Custom:** Karena identifier login bukan email melainkan NIP (guru) / NIPD (peserta didik), serta terdapat mekanisme pengingat ganti password di login pertama, proses login dibangun manual menggunakan Controller dan Livewire form — tidak memakai starter kit Breeze yang mengasumsikan email + verifikasi.
- **Spatie Laravel-Permission untuk Otorisasi:** Dipilih karena matriks hak akses lembaga ini cukup kompleks (akses berbeda per-mapel dan per-rombel, serta atribut tambahan Wali Kelas pada akun Guru). Spatie memberi struktur role-permission yang teruji, mengurangi risiko bug dibandingkan membangun sistem otorisasi dari nol.
- **Wali Kelas Bukan Role Tersendiri:** Wali Kelas adalah atribut pada akun Guru (kolom `wali_kelas_id` pada tabel Rombel yang merujuk ke Guru), bukan role keempat. Permission tambahan diberikan secara kondisional saat `user.id` cocok dengan `wali_kelas_id` rombel terkait.

## 2.3 Prinsip Desain Database

- Database hanya berperan sebagai penyimpan data terstruktur (tabel, kolom, relasi) — **tidak ada logic apapun yang ditanam di level database**, termasuk namun tidak terbatas pada trigger, stored procedure, stored function, view dengan logic kalkulasi, maupun event scheduler MySQL.
- Seluruh logic bisnis (validasi, kalkulasi nilai, penentuan status, konversi grade, dsb.) ditulis dan dieksekusi di level aplikasi (Model, Service Class, atau Action Class pada Laravel), bukan di level database. Tujuannya agar seluruh logic dapat ditelusuri, diuji (unit test), dan diubah dari satu tempat (kode), tanpa risiko ada aturan tersembunyi di database yang tidak terlihat saat membaca kode aplikasi.
- Setiap entitas data yang disebutkan pada Bab 5 — termasuk entitas master sederhana yang hanya memiliki satu atau dua kolom (misal `wilayah`, `paket`, `tingkat`) — **tetap dibuat sebagai tabel tersendiri** di database, bukan digabung menjadi satu tabel generik atau disimpan sebagai konstanta/enum di kode aplikasi. Hal ini menjaga konsistensi relasi (foreign key) dan mempermudah pengelolaan data master oleh Admin melalui antarmuka, sekecil apapun jumlah kolomnya.

---

# 3. Definisi & Istilah

| Istilah | Definisi |
|---|---|
| Peserta Didik | Istilah resmi untuk pengguna pelajar di sistem ini (bukan "siswa"). |
| Rombel | Rombongan Belajar — unit kelas hasil gabungan Wilayah + Paket + Tingkat + Tahun Ajaran. |
| Wilayah | Lokasi/cabang penyelenggaraan belajar, contoh: Botolambat, Pondok 1, Bakalan. |
| Paket | Jenjang kesetaraan: Paket A (setara SD), Paket B (setara SMP), Paket C (setara SMA). |
| Tingkat | Tingkatan kelas di dalam Paket, contoh: Kelas 10, 11, 12 untuk Paket C. |
| Tahun Ajaran | Periode akademik tahunan, contoh: 2023/2024. |
| Semester | Pembagian dalam satu Tahun Ajaran: Ganjil atau Genap. |
| Periode Ajaran | Gabungan Tahun Ajaran + Semester yang sedang berlaku, contoh: "2023/2024 Ganjil". |
| Wali Kelas | Atribut tambahan pada akun Guru yang memberi tanggung jawab/akses lebih atas satu Rombel tertentu. |
| CBT | Computer Based Test — ujian/latihan berbasis komputer dengan soal Pilihan Ganda dan/atau Uraian. |
| KKM | Kriteria Ketuntasan Minimal — nilai ambang batas lulus yang ditetapkan guru per CBT. |
| NIPD | Nomor Induk Peserta Didik — identifier internal lembaga (pengganti istilah NIS). |
| NIP | Nomor Induk Pegawai (Guru) — identifier internal lembaga untuk akun guru. |
| Ruang Tunggu | Status sementara bagi peserta didik yang telah naik tingkat namun belum di-assign ke Rombel baru oleh Admin. |

---

# 4. Role & Hak Akses (Permission Matrix)

## 4.1 Daftar Role

Sistem ini memiliki 3 role inti. Wali Kelas bukan role keempat, melainkan atribut tambahan pada akun Guru (lihat 4.3).

- **Admin (Tata Usaha)** — akses penuh ke seluruh master data dan operasional administratif.
- **Guru** — mengelola materi, tugas, dan CBT pada mata pelajaran yang diampu; dapat membuka sesi absensi.
- **Peserta Didik** — mengikuti kegiatan belajar, mengerjakan tugas/CBT, melakukan absensi mandiri.

## 4.2 Matriks Hak Akses Final

Wali Kelas direpresentasikan sebagai kondisi tambahan pada baris Guru (ditandai `*`), bukan kolom tersendiri.

| Fitur / Modul | Admin | Guru Mapel | Guru* (sebagai Wali Kelas) | Peserta Didik |
|---|---|---|---|---|
| Master Data (Tahun Ajaran, Wilayah, Paket, Rombel, Mapel) | Akses Penuh | Tidak Ada Akses | Tidak Ada Akses | Tidak Ada Akses |
| Manajemen Akun (Guru & Peserta Didik) | Akses Penuh | Tidak Ada Akses | Hanya Lihat Rombelnya | Hanya Edit Profil Sendiri |
| Materi & Tugas | Tidak Ada Akses | Akses Penuh (di Mapelnya) | Hanya Lihat (Rombelnya) | Lihat & Kerjakan Tugas |
| CBT (Ujian) | Tidak Ada Akses | Akses Penuh (di Mapelnya) | Hanya Lihat (Rombelnya) | Mengerjakan CBT |
| Penilaian Tugas & CBT | Tidak Ada Akses | Menilai (di Mapelnya) | Hanya Lihat Rekap (Rombelnya) | Lihat Nilai Sendiri |
| Absensi | Lihat Semua Rekap | Buka Sesi & Absen Manual (Mapelnya) | Lihat & Edit Rekap (Rombelnya) | Klik Hadir/Izin/Sakit |
| Jadwal Pelajaran | Akses Penuh (semua Rombel) | Tidak Ada Akses | Input/Edit (Rombelnya) | Hanya Lihat |
| Kenaikan / Pindah Kelas | Akses Penuh (assign akhir) | Tidak Ada Akses | Eksekusi Penentuan Status (Rombelnya) | Tidak Ada Akses |
| Reset Password Pengguna | Akses Penuh | Tidak Ada Akses | Tidak Ada Akses | Tidak Ada Akses |
| Import Data Peserta Didik (Excel) | Akses Penuh | Tidak Ada Akses | Tidak Ada Akses | Tidak Ada Akses |
| Penilaian Akhir (Rapor) | Lihat Semua, Tidak Input | Input Nilai Komponen (Mapelnya) | Input Catatan Umum & Terbitkan (Rombelnya) | Lihat Rapor Sendiri (setelah Terbit) |

## 4.3 Mekanisme Wali Kelas

Wali Kelas diimplementasikan sebagai kolom `wali_kelas_id` pada tabel `rombel` yang merujuk ke `users` (Guru). Saat sistem mengecek otorisasi, jika `Auth::id() == rombel.wali_kelas_id`, akun Guru tersebut otomatis mendapat permission tambahan untuk rombel itu saja (bukan untuk semua rombel), di atas permission dasarnya sebagai Guru Mapel.

Implikasinya, satu akun Guru bisa memiliki dua "konteks" akses sekaligus: sebagai pengajar mata pelajaran tertentu (di rombel manapun ia mengajar) dan sebagai wali kelas (di satu rombel spesifik yang ia diamanahkan).

## 4.4 Implementasi Teknis Otorisasi

- Role dasar (admin, guru, peserta_didik) dikelola lewat Spatie Laravel-Permission.
- Permission granular per-mapel diperiksa melalui tabel relasi `guru_mapel_rombel` (siapa mengajar apa, di rombel mana, pada periode ajaran mana).
- Permission kondisional Wali Kelas diperiksa melalui Policy Laravel yang membandingkan `Auth::id()` dengan `rombel.wali_kelas_id`, dikombinasikan dengan permission Spatie.

---

# 5. Struktur Data Inti (ERD Kasar)

> Bagian ini menjabarkan entitas utama dan relasinya secara naratif/tabular. Skema detail (tipe data lengkap, index, foreign key) disusun pada dokumen teknis terpisah `database.md` (lihat Bab 10).

## 5.1 Entitas Pengguna

### users
Menampung otentikasi dasar seluruh pengguna (Admin, Guru, Peserta Didik).

| Kolom | Tipe / Keterangan |
|---|---|
| id | Primary Key |
| username | String, unik. Diisi dari NIP (Guru) atau NIPD (Peserta Didik) saat akun dibuat. |
| password | Hashed. Nilai awal = sama dengan username. |
| role | Enum: admin, guru, peserta_didik (dikelola via Spatie). |
| is_change_password | Boolean, default 0. Saat masih 0, modal pengingat ganti password ditampilkan setiap login (dapat di-skip, tidak memblokir akses menu lain). Lihat 6.1. |
| is_active | Boolean, default 1. Tidak diubah otomatis saat Lulus (lihat 6.9/6.10). |

### guru

| Kolom | Tipe / Keterangan |
|---|---|
| id | Primary Key |
| user_id | Foreign Key ke users |
| nip | String, unik |
| nama_lengkap | String |
| no_hp | String |

### peserta_didik
Menampung data operasional akademik inti yang sering di-query/filter.

| Kolom | Tipe / Keterangan |
|---|---|
| id | Primary Key |
| user_id | Foreign Key ke users |
| nipd | String, unik |
| nik | String |
| nama_lengkap | String |
| jenis_kelamin | Enum: L, P |
| tempat_lahir | String |
| tanggal_lahir | Date |
| agama | String |
| no_hp | String, nullable |
| status_akademik | Enum: aktif, lulus, pindah, keluar. Default: aktif. |

### peserta_didik_alamat

| Kolom | Tipe / Keterangan |
|---|---|
| peserta_didik_id | Foreign Key ke peserta_didik |
| alamat | Text |
| rt / rw | String |
| dusun | String, nullable |
| kelurahan | String |
| kecamatan | String |
| kode_pos | String, nullable |

### peserta_didik_ortu
Menyimpan data Ayah, Ibu, dan Wali secara ringkas (nama dan kontak saja).

| Kolom | Tipe / Keterangan |
|---|---|
| peserta_didik_id | Foreign Key ke peserta_didik |
| jenis | Enum: ayah, ibu, wali |
| nama | String |
| no_hp | String, nullable |
| hubungan_wali | String, nullable. Hanya relevan jika jenis = wali. |

## 5.2 Entitas Struktur Akademik

### periode_ajaran
Gabungan Tahun Ajaran dan Semester. Hanya satu baris yang berstatus aktif dalam satu waktu.

| Kolom | Tipe / Keterangan |
|---|---|
| id | Primary Key |
| tahun_ajaran | String, contoh: 2023/2024 |
| semester | Enum: ganjil, genap |
| is_aktif | Boolean. Hanya 1 baris boleh bernilai true. |

### wilayah
Master lokasi/cabang penyelenggaraan belajar. Tetap dibuat sebagai tabel tersendiri meski kolom minim, sesuai prinsip 2.3.

| Kolom | Tipe / Keterangan |
|---|---|
| id | Primary Key |
| nama | String. Contoh: Botolambat, Pondok 1, Bakalan. |

### paket
Master jenjang kesetaraan.

| Kolom | Tipe / Keterangan |
|---|---|
| id | Primary Key |
| nama | String. Contoh: Paket A, Paket B, Paket C. |

### tingkat
Master tingkatan kelas di dalam suatu Paket.

| Kolom | Tipe / Keterangan |
|---|---|
| id | Primary Key |
| paket_id | Foreign Key ke paket. |
| nama | String. Contoh: Kelas 10, Kelas 11, Kelas 12. |

> Ketiga tabel di atas divalidasi ketat saat proses import Excel — nilai yang tidak ada di master akan ditolak, bukan dibuat otomatis.

### rombel
Terikat ke Tahun Ajaran (bukan ke Periode/Semester spesifik), karena keanggotaan peserta didik di suatu rombel berlaku untuk kedua semester dalam tahun ajaran yang sama.

| Kolom | Tipe / Keterangan |
|---|---|
| id | Primary Key |
| wilayah_id, paket_id, tingkat_id | Foreign Key ke tabel master masing-masing |
| tahun_ajaran | String. Contoh penamaan gabungan: "Kelas 10 Botolambat Paket C – TA 2023/2024" |
| wali_kelas_id | Foreign Key ke guru. Nullable saat rombel baru dibuat, wajib diisi sebelum tahun ajaran berjalan. |

### peserta_didik_rombel
Tabel pivot keanggotaan peserta didik pada rombel di suatu tahun ajaran.

### mapel
Master mata pelajaran. Hanya Admin yang dapat membuat/mengubah.

### guru_mapel_rombel
Tabel pivot pemetaan: Guru mengajar Mapel apa, di Rombel mana, pada Periode Ajaran apa. Menjadi dasar validasi otorisasi modul Materi, Tugas, dan CBT.

### jadwal_pelajaran

| Kolom | Tipe / Keterangan |
|---|---|
| id | Primary Key |
| rombel_id, mapel_id, guru_id | Foreign Key |
| hari | Enum: Senin–Minggu |
| jam_mulai, jam_selesai | Time |

## 5.3 Entitas Kegiatan Belajar

### materi

| Kolom | Tipe / Keterangan |
|---|---|
| id | Primary Key |
| guru_mapel_rombel_id | Foreign Key |
| judul, deskripsi | String / Text |
| tipe_konten | Enum: text, file, link_video, gambar |
| file_path / url | Nullable, sesuai tipe_konten |

### tugas

| Kolom | Tipe / Keterangan |
|---|---|
| id | Primary Key |
| guru_mapel_rombel_id | Foreign Key |
| judul, deskripsi | String / Text |
| lampiran_path | Nullable |
| deadline | Datetime |

### tugas_submisi

| Kolom | Tipe / Keterangan |
|---|---|
| id | Primary Key |
| tugas_id, peserta_didik_id | Foreign Key |
| file_path / isi_text | Sesuai jenis jawaban |
| waktu_submit | Datetime |
| nilai | Integer 0–100, nullable sebelum dinilai guru |

## 5.4 Entitas CBT

### cbt

| Kolom | Tipe / Keterangan |
|---|---|
| id | Primary Key |
| guru_mapel_rombel_id | Foreign Key |
| nama_ujian | String |
| kkm | Integer |
| tanggal_mulai, durasi_menit | Datetime / Integer |
| tampilkan_nilai_otomatis | Boolean. Hanya relevan untuk CBT Pilihan Ganda murni. |
| jenis_cbt | Enum: pilihan_ganda, uraian, campuran. Dideteksi otomatis dari komposisi soal. |

### cbt_soal

| Kolom | Tipe / Keterangan |
|---|---|
| id | Primary Key |
| cbt_id | Foreign Key |
| tipe_soal | Enum: pilihan_ganda, uraian |
| pertanyaan | Text |
| pilihan_jawaban | JSON, hanya untuk pilihan_ganda |
| kunci_jawaban | String, hanya untuk pilihan_ganda |

### hasil_cbt
Status penilaian disimpan di level hasil per peserta didik, karena status koreksi berbeda-beda antar peserta didik.

| Kolom | Tipe / Keterangan |
|---|---|
| id | Primary Key |
| cbt_id, peserta_didik_id | Foreign Key |
| waktu_mulai, waktu_submit | Datetime. Auto-submit jika durasi habis. |
| nilai_pg | Integer, terhitung otomatis saat submit (jika ada soal PG). |
| nilai_uraian | Integer, nullable, diisi guru manual. |
| nilai_akhir | Integer, gabungan nilai_pg + nilai_uraian. |
| status_penilaian | Enum: otomatis, menunggu_koreksi, selesai_dinilai (lihat 6.9). |

### cbt_jawaban_peserta
Menyimpan jawaban tiap soal per peserta didik per percobaan CBT, untuk keperluan koreksi manual Uraian dan audit.

## 5.5 Entitas Absensi

### sesi_absensi

| Kolom | Tipe / Keterangan |
|---|---|
| id | Primary Key |
| guru_mapel_rombel_id | Foreign Key |
| tanggal | Date |
| status_sesi | Enum: terbuka, ditutup |

### absensi_detail

| Kolom | Tipe / Keterangan |
|---|---|
| id | Primary Key |
| sesi_absensi_id, peserta_didik_id | Foreign Key |
| status | Enum: hadir, izin, sakit, alpa |
| waktu_klik | Datetime, nullable jika diisi manual oleh guru |
| diubah_manual_oleh | Foreign Key ke users, nullable |

## 5.6 Entitas Kenaikan Kelas

### kenaikan_kelas

| Kolom | Tipe / Keterangan |
|---|---|
| id | Primary Key |
| peserta_didik_id | Foreign Key |
| rombel_asal_id | Foreign Key ke rombel (tahun ajaran berjalan) |
| status_keputusan | Enum: naik, tinggal, lulus, pindah_paket, pindah_wilayah |
| rombel_tujuan_id | Foreign Key ke rombel, nullable hingga di-assign Admin pada tahun ajaran baru |
| diputuskan_oleh | Foreign Key ke users (Wali Kelas) |

## 5.7 Entitas Penilaian Akhir (Rapor)

Modul ini mengompilasi nilai per Peserta Didik per Mata Pelajaran pada satu Semester, dengan sumber referensi dari Tugas dan CBT yang dapat ditimpa (override) secara manual oleh Guru.

### rapor
Header rapor per Peserta Didik per Semester. Satu baris mewakili satu rapor.

| Kolom | Tipe / Keterangan |
|---|---|
| id | Primary Key |
| peserta_didik_id | Foreign Key |
| periode_ajaran_id | Foreign Key. Menentukan Semester (Ganjil/Genap) rapor ini berlaku. |
| rombel_id | Foreign Key. Rombel Peserta Didik saat rapor ini dibuat. |
| catatan_wali_kelas | Text, nullable. Diisi Wali Kelas, bersifat umum (bukan per mapel). |
| status | Enum: draft, terbit. Rapor berstatus draft selama komponen nilai belum lengkap dari seluruh Guru Mapel. |
| diterbitkan_oleh | Foreign Key ke users (Wali Kelas), nullable hingga status berubah ke terbit. |

### rapor_nilai_mapel
Detail nilai per Mata Pelajaran dalam satu rapor. Satu baris mewakili satu Mapel.

| Kolom | Tipe / Keterangan |
|---|---|
| id | Primary Key |
| rapor_id, mapel_id | Foreign Key |
| diinput_oleh | Foreign Key ke users (Guru Mapel terkait) |
| catatan_mapel | Text, nullable. Deskripsi/catatan dari Guru Mapel untuk mapel ini. |

### rapor_nilai_komponen
Detail nilai per komponen (misal: Pengetahuan, Keterampilan, Sikap) di dalam satu rapor_nilai_mapel.

| Kolom | Tipe / Keterangan |
|---|---|
| id | Primary Key |
| rapor_nilai_mapel_id | Foreign Key |
| nama_komponen | String. Contoh: Pengetahuan, Keterampilan, Sikap. |
| nilai_referensi | Integer, nullable. Auto-terisi dari rata-rata Tugas + CBT pada mapel & semester terkait, sebagai usulan awal. |
| nilai_akhir | Integer 0–100. Nilai final yang tersimpan — default sama dengan nilai_referensi, dapat di-override manual oleh Guru. |
| grade | Enum: A, B, C, D. Dikonversi otomatis dari rentang nilai_akhir (dapat dikonfigurasi Admin). |
| catatan | Text, nullable. Catatan/deskripsi spesifik untuk komponen ini. |

> **Catatan implementasi:** `nilai_referensi` dihitung sistem dari rata-rata seluruh `tugas_submisi.nilai` dan `hasil_cbt.nilai_akhir` pada kombinasi peserta_didik + mapel + periode_ajaran yang sesuai, dihitung saat Guru membuka form input nilai komponen — bukan disimpan permanen sebagai cache, untuk menghindari data usang jika ada tugas/CBT yang dinilai ulang setelah rapor dibuat.

---

# 6. Modul Fitur (Flow Detail)

> Urutan modul disusun mengikuti prinsip pengembangan bertahap: dimulai dari Autentikasi (fondasi seluruh role), dilanjutkan Master Data dan entitas dengan relasi paling minim, baru kemudian modul transaksional yang relasinya semakin kompleks, dan diakhiri modul yang menarik data dari hampir seluruh modul lain (Penilaian Akhir).

## 6.1 Autentikasi & Manajemen Password

**Deskripsi.** Mekanisme login diseragamkan untuk Guru dan Peserta Didik: username berbasis identifier internal lembaga, dengan password awal yang sama. Pengguna diberi pilihan untuk langsung mengganti password atau menundanya (skip), namun akan terus diingatkan melalui modal setiap login selama belum diganti.

**Alur:**

- Akun Guru/Peserta Didik dibuat oleh Admin (manual atau via import Excel untuk Peserta Didik). Username diisi otomatis dari NIP (Guru) atau NIPD (Peserta Didik).
- Password awal di-set sama dengan username, di-hash sebelum disimpan. Kolom `is_change_password` di-set 0.
- Pengguna login menggunakan username dan password awal tersebut.
- Sistem memeriksa `is_change_password`. Jika bernilai 0, sistem menampilkan modal Ganti Password tepat setelah login berhasil — modal ini muncul di atas dashboard, bukan menggantikan/mem-blokir halaman dashboard.
- Pengguna dapat memilih: (a) mengisi password baru lalu menyimpan, atau (b) menekan tombol "Nanti Saja"/"Skip" untuk menutup modal dan melanjutkan menggunakan password lama.
- Jika pengguna mengisi password baru: ketentuan password tidak ketat — cukup minimal panjang karakter (misal 6 karakter), tanpa wajib kombinasi huruf besar/angka/simbol. Setelah berhasil, `is_change_password` di-set 1 dan modal tidak muncul lagi.
- Jika pengguna memilih Skip: `is_change_password` tetap 0, sehingga modal muncul kembali pada setiap login berikutnya sampai password benar-benar diganti.
- Jika pengguna lupa password, Admin dapat menggunakan menu "Reset Password" pada profil pengguna terkait — password dikembalikan ke nilai username, dan `is_change_password` di-set kembali ke 0.

**Catatan Teknis:**

- Login dibangun manual (Controller + Livewire form component), tidak menggunakan starter kit Breeze, karena identifier login bukan email.
- Spatie Laravel-Permission digunakan untuk mengelola role pasca-login.
- Modal Ganti Password bersifat dismissable — pengingat tetap muncul konsisten setiap login agar password lemah/default tidak dibiarkan selamanya.

## 6.2 Manajemen Tahun Ajaran, Semester & Switching

**Deskripsi.** Sistem mengenal satu Periode Ajaran aktif secara global (gabungan Tahun Ajaran + Semester), namun pengguna dapat beralih melihat data periode lain melalui switcher di dalam sistem (bukan saat proses login).

**Alur:**

- Admin membuat data Periode Ajaran baru melalui menu Master Data.
- Admin menetapkan satu Periode Ajaran sebagai aktif (`is_aktif = true`). Hanya satu baris yang dapat aktif — sistem otomatis menonaktifkan periode aktif sebelumnya.
- Saat pengguna login, sistem otomatis menggunakan Periode Ajaran Aktif sebagai konteks data default.
- Di dalam sistem tersedia switcher periode (umumnya untuk Admin dan Wali Kelas) untuk melihat data historis.
- Ketika switcher diarahkan ke periode selain yang aktif, tampilan berubah ke mode arsip/read-only — aksi yang mengubah data dikunci dan hanya dapat dilakukan pada Periode Ajaran Aktif.

**Catatan Penting:**

- Rombel terikat ke Tahun Ajaran (bukan ke Periode/Semester spesifik).
- Kenaikan Kelas hanya terjadi pada transisi antar Tahun Ajaran (dari Semester Genap ke Ganjil tahun berikutnya), bukan antar semester dalam tahun ajaran yang sama.

## 6.3 Master Data (Wilayah, Paket, Tingkat, Rombel, Mapel)

**Deskripsi.** Seluruh master data dikelola sepenuhnya oleh Admin. Entitas Wilayah, Paket, dan Tingkat adalah data paling sederhana sehingga dikerjakan lebih dahulu sebagai fondasi sebelum Rombel dan Mapel.

**Alur Pengelolaan Data Sederhana (Wilayah, Paket, Tingkat):**

- Admin membuka Master Data > Wilayah, menambahkan daftar wilayah/cabang (Botolambat, Pondok 1, Bakalan).
- Admin membuka Master Data > Paket, menambahkan paket kesetaraan (Paket A, B, C).
- Admin membuka Master Data > Tingkat, menambahkan tingkat per paket (Kelas 10, 11, 12).

**Alur Pembuatan Rombel:**

- Admin memastikan Periode Ajaran aktif sudah benar.
- Admin memilih kombinasi Wilayah, Paket, dan Tingkat yang sudah terdaftar di master.
- Sistem membentuk Rombel dengan format penamaan otomatis ("Kelas 10 Botolambat Paket C – TA 2023/2024").
- Admin menetapkan Wali Kelas (memilih dari daftar akun Guru).
- Admin memasukkan daftar Peserta Didik ke dalam Rombel (manual atau hasil import Excel).

**Alur Pengelolaan Mapel & Pemetaan Guru-Mapel-Rombel:**

- Admin membuka Master Data > Mata Pelajaran, menambahkan daftar mapel.
- Admin membuka Pemetaan Pengajaran, memilih Guru, Mata Pelajaran, dan Rombel untuk Periode Ajaran yang berlaku.
- Sistem menyimpan pemetaan ini sebagai dasar otorisasi modul Materi, Tugas, dan CBT.

## 6.4 Jadwal Pelajaran

**Deskripsi.** Dikerjakan setelah Master Data karena relasinya sederhana dan tidak bergantung modul transaksional lain.

**Alur:**

- Admin atau Wali Kelas membuka menu Jadwal Pelajaran.
- Admin dapat menginput/mengubah jadwal seluruh Rombel; Wali Kelas hanya untuk Rombel tanggung jawabnya.
- Jadwal diinput per Rombel: Mata Pelajaran, Guru pengajar, Hari, Jam Mulai, Jam Selesai.
- Guru dan Peserta Didik dapat melihat (read-only) jadwal yang relevan pada dashboard masing-masing.

## 6.5 Import Data Peserta Didik (Excel)

**Deskripsi.** Sistem menyediakan template Excel baku buatan lembaga. Detail kolom dijabarkan pada Bab 7.

**Alur:**

- Admin mengunduh template Excel kosong dari menu Import Peserta Didik.
- Admin mengisi data sesuai kolom A–Y (header di baris 1, data mulai baris 2), termasuk kolom Wilayah, Paket, Tingkat sebagai penentu Rombel Tujuan (tiga kolom terpisah).
- Admin mengunggah berkas Excel.
- Sistem memproses **synchronous** dalam satu request, tanpa background job/queue (skala data tiap rombel kecil, di bawah 20 peserta didik).
- Sistem melakukan validasi per baris: kombinasi Wilayah/Paket/Tingkat harus ada di master — jika tidak, baris ditandai gagal (tidak ada pembuatan master otomatis).
- Sistem menampilkan ringkasan: jumlah berhasil, jumlah gagal beserta alasan (baris ke berapa, kolom apa, kenapa).
- Untuk baris berhasil, sistem otomatis membuat akun (users + peserta_didik + alamat + ortu) dan memasukkan ke Rombel Tujuan.

## 6.6 Absensi

**Deskripsi.** Modul transaksional paling sederhana (tanpa lampiran file maupun penilaian bertingkat), dikerjakan lebih dahulu untuk memvalidasi pola interaksi Livewire real-time.

**Alur:**

- Guru menekan "Buka Sesi Absensi" untuk Mapel-Rombel yang diampu.
- Status sesi absensi hari itu menjadi "Terbuka".
- Peserta Didik membuka menu Absensi dan menekan Hadir (atau Izin/Sakit).
- Guru melihat secara real-time siapa yang sudah/belum absen, dan dapat mengubah status secara manual jika ada kendala teknis.
- Guru menutup sesi setelah kelas selesai.
- Rekap kehadiran tersedia bagi Guru (mapelnya), Wali Kelas (rombelnya), dan Admin (seluruh rekap).

## 6.7 Materi

**Alur:**

- Guru memilih Mapel-Rombel yang diampu pada Periode Ajaran aktif.
- Guru membuat Materi baru dengan tipe konten: teks, file (dokumen/gambar), atau link video (disarankan YouTube, lihat Bab 8).
- Materi otomatis tampil ke seluruh Peserta Didik di Rombel tersebut.
- Wali Kelas (rombelnya) dan Peserta Didik dapat melihat materi; hanya Guru pengampu yang dapat mengubah/menghapus.

## 6.8 Tugas

**Alur:**

- Guru membuat Tugas pada Mapel-Rombel yang diampu: judul, deskripsi, lampiran (opsional), deadline.
- Peserta Didik menerima notifikasi tugas baru di dashboard.
- Peserta Didik mengunggah jawaban (file atau teks) sebelum deadline. Submisi setelah deadline tetap dicatat namun ditandai "Terlambat".
- Guru melihat daftar Peserta Didik yang sudah/belum mengumpulkan, dan memberikan nilai (0–100) per submisi.
- Peserta Didik melihat nilai setelah dinilai guru.

## 6.9 CBT (Computer Based Test)

**Deskripsi.** Modul terpisah dari Tugas, dengan soal terstruktur, timer, auto-submit, dan auto-grading untuk sebagian jenis soal. Dikerjakan setelah Materi dan Tugas karena paling kompleks.

**Alur Pembuatan CBT (oleh Guru):**

- Guru membuat jadwal CBT baru: nama ujian, KKM, tanggal & waktu mulai, durasi.
- Guru memasukkan soal — Pilihan Ganda (dengan kunci) dan/atau Uraian (dinilai manual).
- Sistem otomatis mendeteksi `jenis_cbt` dari komposisi soal.
- Untuk CBT mengandung PG, Guru mengatur "Tampilkan Nilai Otomatis: Ya/Tidak".

**Alur Pengerjaan CBT (oleh Peserta Didik):**

- Peserta Didik login pada waktu yang ditentukan. Saat masuk halaman pengerjaan, timer mundur mulai (Alpine.js di sisi browser).
- Peserta Didik mengerjakan soal dalam batas waktu.
- Jika waktu habis sebelum submit, sistem auto-submit jawaban yang sudah terisi.

**Alur Penilaian — Berdasarkan Jenis CBT:**

| Jenis CBT | Mekanisme Penilaian | status_penilaian Awal |
|---|---|---|
| Pilihan Ganda murni | Nilai dihitung otomatis segera setelah submit. | otomatis — tampil/tersembunyi sesuai setting Tampilkan Nilai Otomatis. |
| Uraian murni | Tidak ada penghitungan otomatis. Guru memberi skor manual per soal. | menunggu_koreksi → selesai_dinilai setelah koreksi selesai. |
| Campuran (PG + Uraian) | Nilai PG otomatis, status keseluruhan menunggu hingga Uraian dikoreksi, lalu digabung. | menunggu_koreksi → selesai_dinilai setelah seluruh Uraian dikoreksi. |

**Catatan Penting:**

- `status_penilaian` disimpan di level hasil per Peserta Didik (`hasil_cbt`), bukan di level CBT.
- Tugas dan CBT dikunci sebagai dua modul terpisah.

## 6.10 Kenaikan Kelas

**Alur:**

- Menjelang akhir Tahun Ajaran (Semester Genap), Wali Kelas masuk menu Kenaikan Kelas untuk Rombelnya.
- Sistem menampilkan daftar Peserta Didik di Rombel tersebut.
- Wali Kelas menetapkan status tiap Peserta Didik: Naik Tingkat, Tinggal, Lulus, Pindah Paket, atau Pindah Wilayah.
- Setelah disimpan, Peserta Didik dengan status Naik/Tinggal/Pindah Paket/Pindah Wilayah masuk "Ruang Tunggu", menunggu di-assign Admin ke Rombel baru pada Tahun Ajaran berikutnya.
- Peserta Didik status Lulus tidak masuk Ruang Tunggu. Akunnya tetap aktif (`is_active = true`), namun `status_akademik` menjadi "lulus" dan tidak lagi punya keanggotaan Rombel aktif.
- Pada Tahun Ajaran baru, Admin membuat Rombel baru dan mengassign Peserta Didik dari Ruang Tunggu — termasuk kasus Pindah Paket/Wilayah yang tidak linear.

**Catatan Penting:**

- Kenaikan Kelas hanya pada transisi antar Tahun Ajaran.
- Status "Lulus" tidak menonaktifkan akun, agar data riwayat tetap dapat diakses.

## 6.11 Penilaian Akhir (Rapor)

**Deskripsi.** Menghasilkan rapor sederhana per Peserta Didik tiap akhir Semester (2 kali per Tahun Ajaran). Setiap komponen nilai memiliki Angka, Grade, dan Catatan per Mata Pelajaran. Dikerjakan paling akhir karena menarik data dari hampir seluruh modul sebelumnya.

**Alur Input Nilai (oleh Guru Mapel):**

- Guru Mapel membuka menu Penilaian Akhir untuk Mapel-Rombel yang diampu pada Periode Ajaran berjalan.
- Sistem menampilkan daftar Peserta Didik dengan komponen nilai yang berlaku (Pengetahuan, Keterampilan, dst.).
- Untuk tiap komponen, sistem menghitung `nilai_referensi` dari rata-rata Tugas + CBT sebagai usulan awal.
- Guru menerima usulan atau meng-override menjadi `nilai_akhir` yang berbeda.
- Sistem mengonversi `nilai_akhir` menjadi Grade huruf (A/B/C/D).
- Guru menambah Catatan per mapel (opsional namun disarankan).
- Guru menyimpan — data masuk ke `rapor_nilai_mapel` dan `rapor_nilai_komponen`.

**Alur Pelengkapan oleh Wali Kelas:**

- Wali Kelas memantau progres pengisian; rapor tetap draft hingga seluruh Mapel diisi.
- Wali Kelas menambah `catatan_wali_kelas` yang sifatnya umum.
- Setelah seluruh komponen lengkap, Wali Kelas menerbitkan rapor (`status` draft → terbit).

**Alur Cetak PDF:**

- Setelah terbit, Wali Kelas, Admin, dan Peserta Didik dapat melihat pratinjau rapor.
- Pratinjau menampilkan kop surat tiga zona (lihat 6.11.1).
- Di bawah kop: judul "LAPORAN HASIL BELAJAR PESERTA DIDIK", keterangan Semester & Tahun Ajaran, lalu identitas Peserta Didik.
- Tabel nilai per Mapel (Angka, Grade, Catatan), diikuti catatan umum Wali Kelas.
- Tombol Cetak PDF menghasilkan dokumen siap unduh/cetak.

### 6.11.1 Spesifikasi Desain Kop Rapor

Kop surat disusun dalam tiga kolom sejajar di bagian paling atas dokumen:

- **Kolom kiri:** Logo Pemerintah Kabupaten Batang, tinggi ±60–70px pada render PDF, rata kiri.
- **Kolom tengah:** blok teks rata tengah berisi (atas ke bawah) nama Pemerintah Kabupaten/Dinas Pendidikan dan Kebudayaan, nama lembaga "PKBM Padamu Negeri" (lebih besar/tebal), alamat lengkap, kontak (telepon/WhatsApp dan email).
- **Kolom kanan:** Logo PKBM Padamu Negeri, sejajar ukuran logo Kabupaten, rata kanan.
- Di bawah ketiga kolom: garis pembatas horizontal tebal (border-bottom) sebagai pemisah kop dari isi.
- Aset logo disimpan sebagai berkas statis di `public/images/`, dirujuk langsung oleh template PDF — bukan diunggah ulang via UI.
- Tim pengembang perlu meminta berkas logo resmi (PNG/SVG transparan) dari lembaga sebelum implementasi cetak PDF (lihat Bab 10.5).

---

# 7. Template Excel Import Peserta Didik

Template dirancang khusus untuk lembaga (bukan turunan Dapodik), berisi 25 kolom data yang relevan operasional. Field tidak relevan (NISN, SKHUN, No KK, data Bank, KIP/KPS/PIP, koordinat, Berat/Tinggi Badan, dsb.) tidak disertakan.

## 7.1 Struktur Berkas

- **Baris 1 (Header):** nama kolom sesuai 7.2, ditulis tepat satu kali di baris paling atas. Header tidak boleh diubah urutan/namanya — import membaca berdasarkan posisi kolom (A, B, C, …), bukan teks header.
- **Baris 2 dan seterusnya (Data):** satu baris = satu Peserta Didik. Tidak boleh ada baris kosong di tengah — sistem berhenti membaca saat menemukan baris yang seluruh kolomnya kosong.
- Sheet yang dibaca adalah sheet pertama (Sheet1). Sheet tambahan diabaikan.

## 7.2 Pemetaan Kolom (A–Y)

| Kolom | Nama Header (Baris 1) | Keterangan |
|---|---|---|
| A | No | Nomor urut baris, tidak disimpan ke database. |
| B | NIPD | Nomor Induk Peserta Didik, wajib unik. |
| C | NIK | Nomor Induk Kependudukan Peserta Didik. |
| D | Nama Lengkap | Wajib diisi. |
| E | Jenis Kelamin | Isi: L atau P. |
| F | Tempat Lahir | |
| G | Tanggal Lahir | Format: YYYY-MM-DD. |
| H | Agama | |
| I | No HP Peserta Didik | Opsional. |
| J | Email | Opsional, info kontak tambahan (bukan untuk login). |
| K | Wilayah | Wajib cocok dengan master data Wilayah. |
| L | Paket | Wajib cocok dengan master data Paket (A/B/C). |
| M | Tingkat | Wajib cocok dengan master data Tingkat. |
| N | Alamat | |
| O | RT | |
| P | RW | |
| Q | Dusun | Opsional. |
| R | Kelurahan | |
| S | Kecamatan | |
| T | Kode Pos | Opsional. |
| U | Nama Ayah | |
| V | No HP Ayah | Opsional. |
| W | Nama Ibu | |
| X | No HP Ibu | Opsional. |
| Y | Nama Wali | Opsional, diisi jika wali berbeda dari Ayah/Ibu. |

> Catatan: kolom Wali dapat diperluas menjadi 3 kolom (Y: Nama Wali, Z: No HP Wali, AA: Hubungan Wali) jika perlu. Untuk versi awal cukup kolom Y.

## 7.3 Contoh Baris Data

| Baris | Contoh Isi (kolom B, D, E, K, L, M) |
|---|---|
| 2 | 2024001 │ Ahmad Fauzi │ L │ Botolambat │ C │ 10 |
| 3 | 2024002 │ Siti Aminah │ P │ Botolambat │ C │ 10 |
| 4 | 2024003 │ Budi Santoso │ L │ Pondok 1 │ B │ 8 |

## 7.4 Aturan Validasi Saat Import

- Kombinasi Wilayah + Paket + Tingkat (kolom K–M) wajib sudah terdaftar di Master Data. Jika tidak ditemukan, baris gagal dan ditandai — sistem tidak membuat master baru otomatis.
- NIPD wajib unik. Baris dengan NIPD yang sudah terdaftar ditolak dan ditandai duplikat.
- Nilai kosong berupa karakter spasi diperlakukan sebagai NULL, bukan string kosong.
- Proses import synchronous (langsung saat upload, tanpa background job/queue).

## 7.5 Output Proses Import

- Akun login (`users`) dibuat otomatis: username = NIPD, password awal = NIPD, `is_change_password = 0`.
- Data inti (`peserta_didik`), alamat (`peserta_didik_alamat`), dan ortu/wali (`peserta_didik_ortu`) tersimpan sesuai kolom yang diisi.
- Peserta Didik otomatis dimasukkan ke Rombel sesuai kombinasi Wilayah/Paket/Tingkat pada Tahun Ajaran aktif.
- Sistem menampilkan ringkasan: total baris diproses, jumlah berhasil, jumlah gagal beserta alasan per baris.

---

# 8. Kebutuhan Non-Fungsional

## 8.1 Keamanan & Autentikasi

- Password disimpan sebagai hash (bcrypt/Argon2 bawaan Laravel), tidak pernah teks biasa.
- Ketentuan kompleksitas password longgar (minimal panjang karakter saja).
- Setiap akun baru diingatkan mengganti password pada login pertama (`is_change_password`).
- Reset password hanya oleh Admin, tercatat dengan jejak audit (siapa, kapan).

## 8.2 Batasan Terkait Shared Hosting

- Limit ukuran upload umumnya 32–64MB (bergantung `php.ini` provider). Lampiran tugas/materi disarankan dibatasi wajar (mis. maks 10–20MB per file).
- Materi video disarankan via tautan eksternal (YouTube/Google Drive), tidak diunggah langsung sebagai file.
- Penyimpanan file pakai filesystem lokal Laravel (`storage:link`) — perlu pemantauan kapasitas disk berkala.
- Sistem tidak memerlukan cron job, queue worker, atau scheduler pada versi ini (lihat Bab 9).

## 8.3 Performa

- Rekap absensi real-time memanfaatkan polling/refresh Livewire (interval beberapa detik), bukan websocket.
- Timer CBT dihitung di sisi browser (Alpine.js); validasi waktu tetap di server saat submit/auto-submit.
- Import Excel dan kompilasi rapor dijalankan synchronous (volume data per rombel kecil, di bawah 20 peserta didik).

## 8.4 Kompatibilitas & Pemeliharaan

- Arsitektur monolith tanpa API — pengembangan fitur baru harus mempertimbangkan dampak ke Livewire component yang ada.
- Spatie Laravel-Permission memudahkan penambahan permission baru tanpa mengubah struktur otorisasi inti.
- Logo Kabupaten Batang dan logo PKBM untuk kop rapor disimpan sebagai aset statis (`public/images/`) — perubahan logo dilakukan dengan mengganti berkas pada deployment, bukan via UI.

---

# 9. Di Luar Lingkup (Out of Scope) Versi 1

- Lapisan API (REST/GraphQL) untuk integrasi mobile atau pihak ketiga.
- Sinkronisasi otomatis dua arah dengan Dapodik (import tetap manual via template baku).
- Notifikasi push/SMS/WhatsApp otomatis (notifikasi tugas baru terbatas pada dashboard internal).
- Modul keuangan/pembayaran SPP atau sejenisnya.
- Aplikasi mobile native (Android/iOS) — sistem diakses via browser/web responsif.
- Multi-tenant (lebih dari satu lembaga dalam satu instalasi).
- Validasi otomatis jadwal pelajaran terhadap sesi absensi.
- Cron job, queue worker (`queue:work`), dan Laravel Scheduler (`schedule:run`). Mengingat skala sangat kecil (di bawah 20 peserta didik per rombel), proses yang sebelumnya diasumsikan butuh background job (import Excel, kompilasi rapor) cukup dijalankan synchronous tanpa risiko timeout berarti. Kebutuhan ini dapat dipertimbangkan kembali di versi mendatang jika skala bertambah signifikan.

---

# 10. Langkah Selanjutnya

> Tahapan disusun berurutan — setiap tahap sebaiknya diselesaikan sebelum memulai tahap berikutnya.

## 10.1 Tahap 1 — ERD Detail (Wajib Selesai Sebelum Migration)

ERD detail harus mencakup seluruh fitur pada Bab 5 dan Bab 6 (termasuk Penilaian Akhir/Rapor) sebelum satu pun file migration ditulis. Urutan yang disarankan:

- Finalisasi seluruh entitas dan kolom (tipe data lengkap), termasuk master sekecil `wilayah`, `paket`, `tingkat` yang tetap tabel tersendiri (prinsip 2.3).
- Penentuan foreign key dan relasi (one-to-many, many-to-many), termasuk pivot (`peserta_didik_rombel`, `guru_mapel_rombel`).
- Penentuan index (kolom sering di-query: `username`, `nipd`, `nip`, kombinasi `periode_ajaran_id + rombel_id`, dsb.).
- Penentuan constraint (unique, not null, default), termasuk cascade behavior (`onDelete` di migration, bukan trigger).
- Setelah ERD detail disetujui menyeluruh, baru migration ditulis satu per satu mengikuti urutan dependency (tabel master & `users` dahulu).

> Hasil tahap ini dituangkan ke `database.md`.

## 10.2 Tahap 2 — Wireframe / Mockup UI

Wireframe/mockup kasar untuk dashboard tiap role (Admin, Guru, Peserta Didik) menggunakan struktur komponen TailAdmin.

- Disarankan memakai Google Stitch untuk mempercepat pembuatan wireframe berbasis prompt sebelum dituangkan ke markup TailAdmin.
- Urutan pembuatan mockup mengikuti urutan modul pada 10.3.

> Lihat `design-guide.md` untuk alur Stitch → TailAdmin → Livewire.

## 10.3 Tahap 3 — Struktur Folder & Urutan Pengerjaan Livewire

Struktur direktori Livewire multi-file component disarankan:

- `app/Livewire/Auth/` — LoginForm.php, ModalGantiPassword.php
- `app/Livewire/Admin/MasterData/` — PeriodeAjaranManager.php, WilayahManager.php, PaketManager.php, TingkatManager.php, RombelManager.php, MapelManager.php, PemetaanGuruMapelRombel.php
- `app/Livewire/Admin/Pengguna/` — GuruManager.php, PesertaDidikManager.php, ResetPasswordModal.php
- `app/Livewire/JadwalPelajaran/` — JadwalManager.php, JadwalViewer.php
- `app/Livewire/Admin/ImportExcel/` — UploadForm.php, RingkasanHasilImport.php
- `app/Livewire/Absensi/` — BukaSesiAbsensi.php, RekapAbsensiRealtime.php, TombolHadir.php
- `app/Livewire/Guru/Materi/` — DaftarMateri.php, FormMateri.php
- `app/Livewire/Guru/Tugas/` — DaftarTugas.php, FormTugas.php, DaftarSubmisi.php, FormPenilaianTugas.php
- `app/Livewire/Guru/Cbt/` — DaftarCbt.php, FormCbt.php, FormSoalCbt.php, DaftarHasilCbt.php, FormKoreksiUraian.php
- `app/Livewire/PesertaDidik/Cbt/` — DaftarCbtTersedia.php, PengerjaanCbt.php (memuat Alpine.js untuk timer)
- `app/Livewire/KenaikanKelas/` — PenentuanStatus.php, AssignRombelBaru.php
- `app/Livewire/Penilaian/` — FormNilaiKomponen.php, PreviewRapor.php, CetakRaporPdf.php

Urutan pengerjaan (dari fondasi/relasi minim ke fitur turunan/kompleks):

1. Auth & Manajemen Password
2. Master Data & Pemetaan (Periode Ajaran, Wilayah, Paket, Tingkat, Rombel, Mapel, Guru-Mapel-Rombel)
3. Jadwal Pelajaran
4. Import Excel Peserta Didik
5. Absensi
6. Materi
7. Tugas
8. CBT
9. Kenaikan Kelas
10. Penilaian Akhir / Rapor

> Detail langkah operasional ada di `build-steps.md`.

## 10.4 Tahap 4 — Template Excel Final

Pembuatan berkas `.xlsx` final sesuai struktur kolom A–Y pada Bab 7 (header baris 1, data mulai baris 2), diuji langsung kepada Admin sebelum development — memastikan format mudah diisi dengan Excel/LibreOffice/Google Sheets.

## 10.5 Tahap 5 — Pengumpulan Aset Logo untuk Rapor

Sebelum modul Penilaian Akhir (6.11) dikodekan, tim perlu memperoleh dari lembaga:

- Logo Pemerintah Kabupaten Batang (PNG/SVG transparan, ≥300dpi pada ukuran cetak).
- Logo PKBM Padamu Negeri (format & resolusi sama).
- Teks resmi kop surat (nama dinas, alamat lengkap, telepon/WhatsApp, email) agar blok tengah kop akurat tanpa placeholder saat live.

## 10.6 Tahap 6 — Penentuan Hosting

Karena sistem tidak memerlukan cron job/queue worker (Bab 9), kriteria pemilihan hosting cukup berfokus pada:

- Versi PHP sesuai kebutuhan Laravel 13.
- Limit ukuran upload memadai untuk lampiran tugas/materi (Bab 8.2).
- Kapasitas penyimpanan disk wajar untuk skala data internal lembaga.
