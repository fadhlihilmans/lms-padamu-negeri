# Buku Panduan Pengguna — LMS Padamu Negeri

**Manual Book / Tutorial Penggunaan Aplikasi**
PKBM Padamu Negeri

> Dokumen ini adalah panduan penggunaan aplikasi untuk pengguna akhir (client),
> bukan dokumentasi teknis pengembangan. Disusun **per peran (role)** agar setiap
> pengguna cukup membaca bagian yang relevan dengannya.

---

## Cara Membaca Buku Ini

- **Admin / Tata Usaha** → baca [Bagian 1](#bagian-1--pendahuluan) dan [Bagian 2](#bagian-2--panduan-admin--tata-usaha).
- **Guru** → baca [Bagian 1](#bagian-1--pendahuluan) dan [Bagian 3](#bagian-3--panduan-guru--wali-kelas).
- **Wali Kelas** → baca [Bagian 3](#bagian-3--panduan-guru--wali-kelas) (Guru + tugas tambahan Wali Kelas).
- **Peserta Didik** → baca [Bagian 1](#bagian-1--pendahuluan) dan [Bagian 4](#bagian-4--panduan-peserta-didik).

---

## Daftar Isi

**Bagian 1 — Pendahuluan (semua pengguna)**
- 1.1 Tentang Aplikasi
- 1.2 Istilah Penting
- 1.3 Cara Mengakses & Login
- 1.4 Ganti Password & Lupa Password
- 1.5 Mengenal Tampilan

**Bagian 2 — Panduan Admin / Tata Usaha**
- 2.1 Pengaturan Aplikasi & Logo
- 2.2 Tahun Ajaran, Semester & Switching Periode
- 2.3 Master Data (Wilayah, Paket, Tingkat, Mapel)
- 2.4 Rombel & Penetapan Wali Kelas
- 2.5 Import Peserta Didik via Excel
- 2.6 Jadwal Pelajaran & Pemetaan Pengajaran
- 2.7 Kenaikan Kelas & Ruang Tunggu
- 2.8 Log Error Sistem
- 2.9 Menindaklanjuti Laporan Bug

**Bagian 3 — Panduan Guru & Wali Kelas**
- 3.1 Absensi
- 3.2 Materi
- 3.3 Tugas
- 3.4 CBT (Ujian Berbasis Komputer)
- 3.5 Penilaian Akhir (Input Nilai Rapor)
- 3.6 Tugas Tambahan Wali Kelas

**Bagian 4 — Panduan Peserta Didik**
- 4.1 Melihat Jadwal & Materi
- 4.2 Melakukan Absensi
- 4.3 Mengumpulkan Tugas
- 4.4 Mengerjakan CBT
- 4.5 Melihat Nilai & Rapor

**Bagian 5 — Lampiran**
- 5.1 FAQ / Pertanyaan Umum
- 5.2 Troubleshooting
- 5.3 Cara Melapor Bug
- 5.4 Kontak Bantuan

> **Pola tiap sub-bab fitur:** _Apa & untuk apa_ → _Langkah demi langkah_ → _Catatan/Peringatan_.

---
---

# Bagian 1 — Pendahuluan

> Wajib dibaca semua pengguna sebelum bagian sesuai perannya.

## 1.1 Tentang Aplikasi

_(Ringkas: apa itu LMS Padamu Negeri dan untuk apa. 1–2 paragraf, bahasa awam.)_

LMS Padamu Negeri adalah aplikasi pusat kegiatan akademik PKBM Padamu Negeri —
absensi, materi, tugas, ujian (CBT), dan rapor — dalam satu tempat, menggantikan
proses manual (WhatsApp, Excel, kertas).

Peran pengguna dalam aplikasi:

| Peran | Keterangan singkat |
|---|---|
| Admin / Tata Usaha | Mengelola data induk & operasional harian. |
| Guru | Mengajar: materi, tugas, CBT, absensi. |
| Wali Kelas | Guru dengan tanggung jawab tambahan atas satu rombel. |
| Peserta Didik | Mengikuti belajar: absen, tugas, ujian, lihat nilai. |

### Teknologi yang Digunakan

Aplikasi ini dibangun sebagai web monolith (tanpa API terpisah) dengan teknologi berikut:

| Komponen | Teknologi | Versi |
|---|---|---|
| Bahasa Pemrograman | PHP | 8.3+ |
| Framework Backend | Laravel | 13.x |
| Frontend Interaktif | Livewire | 3.x |
| Interaktivitas Ringan | Alpine.js | bawaan Livewire 3 |
| UI / Styling | Tailwind CSS (basis TailAdmin) | 4.x |
| Build Tool | Vite | 8.x |
| Database | MySQL | — |
| Role & Permission | Spatie Laravel-Permission | 8.x |
| Cetak PDF (Rapor) | barryvdh/laravel-dompdf | 3.x |
| Import Excel | Maatwebsite Excel | 3.x |
| Pemrosesan Gambar | Intervention Image + ekstensi Imagick | 4.x / Imagick 3.7 |

## 1.2 Istilah Penting

_(Tabel istilah versi awam — turunan PRD Bab 3.)_

| Istilah | Arti |
|---|---|
| Peserta Didik | Sebutan resmi untuk pelajar (bukan "siswa"). |
| Rombel | Rombongan Belajar (kelas). |
| Wilayah | Lokasi/cabang belajar. |
| Paket | Jenjang: Paket A (SD), B (SMP), C (SMA). |
| Tingkat | Kelas dalam paket (mis. Kelas 10, 11, 12). |
| Periode Ajaran | Tahun Ajaran + Semester yang berlaku. |
| Wali Kelas | Guru penanggung jawab satu rombel. |
| CBT | Ujian berbasis komputer. |
| KKM | Nilai minimal ketuntasan. |
| NIP / NIPD | Nomor induk Guru / Peserta Didik (dipakai untuk login). |

## 1.3 Cara Mengakses & Login

1. Buka alamat aplikasi di browser: `_(isi URL)_`.
2. Masukkan **Username** — yaitu **NIP** (Guru) atau **NIPD** (Peserta Didik). Bukan email.
3. Masukkan **Password**. Password awal Anda **sama dengan username**.
4. Klik **Masuk**.

> ⚠️ Jika salah 3× / lupa password, hubungi Admin untuk reset.

## 1.4 Ganti Password & Lupa Password

**Ganti password (login pertama):**
1. Setelah login pertama, muncul jendela **Ganti Password** di atas dashboard.
2. Isi password baru (minimal 6 karakter), lalu **Simpan**.
3. Bisa juga menekan **Nanti Saja** — namun pengingat akan terus muncul tiap login sampai diganti.

**Lupa password:**
- Hubungi Admin. Admin akan mereset password Anda kembali ke username.

## 1.5 Mengenal Tampilan

- **Sidebar (kiri):** menu sesuai peran Anda.
- **Header (atas):** identitas lembaga, tombol **Lapor Bug**, menu profil & **Keluar (Logout)**.
- **Logout:** klik nama/foto profil di kanan atas → **Keluar**.

---
---

# Bagian 2 — Panduan Admin / Tata Usaha

> Panduan lengkap untuk Admin, diurutkan sesuai alur kerja: dari setup awal
> aplikasi hingga operasional harian.

## 2.1 Pengaturan Aplikasi & Logo

**Untuk apa:** mengatur identitas lembaga, logo kop rapor, batas upload, dan mode maintenance.

**Langkah:**
1. Buka menu **Pengaturan**.
2. Isi **Identitas PKBM** (nama, alamat, telepon/WA, email).
3. Unggah **Logo Kabupaten** dan **Logo PKBM** (untuk kop rapor).
4. Atur **Batas Upload** file materi & tugas (MB).
5. **Mode Maintenance** — aktifkan saat aplikasi sedang diperbaiki (hanya Admin yang bisa masuk).
6. Klik **Simpan**.

## 2.2 Tahun Ajaran, Semester & Switching Periode

**Untuk apa:** menentukan periode ajaran aktif sebagai konteks data seluruh aplikasi.

**Langkah:**
1. Buka **Periode Ajaran**.
2. Tambah Tahun Ajaran & Semester baru.
3. Tetapkan satu periode sebagai **Aktif** (periode aktif sebelumnya otomatis nonaktif).
4. Gunakan **switcher periode** untuk melihat data periode lain (mode arsip/read-only).

> ⚠️ Perubahan data hanya bisa dilakukan pada periode **Aktif**.

## 2.3 Master Data (Wilayah, Paket, Tingkat, Mapel)

**Untuk apa:** menyiapkan data induk sebelum membuat rombel.

**Langkah:**
1. **Wilayah** — tambah daftar cabang/lokasi.
2. **Paket** — tambah Paket A/B/C.
3. **Tingkat** — tambah tingkat per paket (Kelas 10, 11, 12).
4. **Mata Pelajaran** — tambah daftar mapel.

> ℹ️ **Catatan — data yang wajib disiapkan agar aplikasi bisa dipakai (mis. untuk demo).**
> Isi secara berurutan agar tidak ada relasi yang kosong:
> 1. **Periode Ajaran** (lihat 2.2) — tetapkan satu sebagai **Aktif**. Tanpa ini, hampir semua modul tidak punya konteks.
> 2. **Wilayah**, **Paket**, **Tingkat** — minimal masing-masing 1 data. Ketiganya syarat pembuatan Rombel.
> 3. **Mata Pelajaran** — minimal beberapa mapel.
> 4. **Akun Guru** — minimal 1 (dipakai sebagai Wali Kelas & pengajar).
> 5. **Rombel** (lihat 2.4) — butuh Wilayah + Paket + Tingkat + Wali Kelas.
> 6. **Peserta Didik** (lihat 2.5) — dimasukkan ke Rombel.
> 7. **Pemetaan Pengajaran** (lihat 2.6) — hubungkan Guru–Mapel–Rombel; ini syarat modul Materi, Tugas, dan CBT dapat digunakan.
>
> Setelah 7 langkah di atas terisi, seluruh alur (absensi → materi → tugas → CBT → rapor) sudah dapat didemokan.

## 2.4 Rombel & Penetapan Wali Kelas

**Langkah:**
1. Pastikan periode aktif benar.
2. Buka **Rombel** → **Tambah**.
3. Pilih kombinasi **Wilayah + Paket + Tingkat** (nama rombel otomatis terbentuk).
4. Tetapkan **Wali Kelas** (pilih dari daftar Guru).
5. Masukkan Peserta Didik (manual atau hasil import Excel).


## 2.5 Import Peserta Didik via Excel

**Langkah:**
1. Buka **Import Peserta Didik** → **Unduh Template**.
2. Isi data sesuai kolom (header baris 1, data mulai baris 2). Kolom Wilayah/Paket/Tingkat menentukan rombel tujuan.
3. Unggah berkas Excel.
4. Baca **ringkasan hasil**: jumlah berhasil & gagal (beserta alasan per baris).


> ⚠️ Kombinasi Wilayah/Paket/Tingkat harus sudah ada di master. Baris yang tidak cocok akan gagal.

## 2.6 Jadwal Pelajaran & Pemetaan Pengajaran

**Langkah — Pemetaan Guru–Mapel–Rombel:**
1. Buka **Pemetaan Pengajaran**.
2. Pilih Guru, Mata Pelajaran, dan Rombel untuk periode berjalan. Simpan.

**Langkah — Jadwal Pelajaran:**
1. Buka **Jadwal Pelajaran**, pilih rombel.
2. Input Mapel, Guru, Hari, Jam Mulai–Selesai.


## 2.7 Kenaikan Kelas & Ruang Tunggu

**Untuk apa:** menempatkan Peserta Didik dari Ruang Tunggu ke rombel baru di Tahun Ajaran berikutnya.

**Langkah:**
1. Pada Tahun Ajaran baru, buat Rombel baru (lihat 2.4).
2. Buka **Ruang Tunggu**.
3. Assign Peserta Didik (termasuk kasus Pindah Paket/Wilayah) ke rombel baru.


> ℹ️ Status kenaikan (Naik/Tinggal/Lulus/Pindah) ditetapkan Wali Kelas — lihat 3.6.

## 2.8 Log Error Sistem

**Untuk apa:** menelusuri error teknis yang tercatat otomatis.

**Langkah:**
1. Buka **Log Error**.
2. Lihat daftar & detail error.
3. Hapus satu per satu atau **Hapus Semua** bila sudah tidak diperlukan.


## 2.9 Menindaklanjuti Laporan Bug

**Langkah:**
1. Buka **Laporan Bug**.
2. Filter berdasarkan status (Baru / Diproses / Selesai / Ditolak).
3. Ubah status & tambahkan catatan tindak lanjut.
4. Hapus laporan yang sudah selesai bila perlu.


---
---

# Bagian 3 — Panduan Guru & Wali Kelas

> Untuk Guru. Sub-bab 3.6 khusus bagi Guru yang juga menjadi **Wali Kelas**.

## 3.1 Absensi

**Langkah:**
1. Buka **Absensi**, pilih Mapel-Rombel yang Anda ampu.
2. Klik **Buka Sesi Absensi**.
3. Pantau kehadiran secara langsung; ubah status manual bila perlu (Hadir/Izin/Sakit).
4. Klik **Tutup Sesi** setelah kelas selesai.
5. Lihat **Rekap** kehadiran kapan saja.


## 3.2 Materi

**Langkah:**
1. Buka **Materi**, pilih Mapel-Rombel.
2. Klik **Tambah Materi**: isi judul + konten (teks / file / link video).
3. Simpan — materi otomatis tampil ke Peserta Didik rombel tersebut.


> ⚠️ Ukuran file mengikuti batas upload yang diatur Admin.

## 3.3 Tugas

**Langkah:**
1. Buka **Tugas**, pilih Mapel-Rombel → **Tambah Tugas**.
2. Isi judul, deskripsi, lampiran (opsional), **deadline**. Simpan.
3. Lihat daftar Peserta Didik yang sudah/belum mengumpulkan.
4. Beri **nilai (0–100)** per submisi.


> ℹ️ Submisi setelah deadline tetap masuk, ditandai **Terlambat**.

## 3.4 CBT (Ujian Berbasis Komputer)

**Membuat CBT:**
1. Buka **CBT** → **Buat Baru**: nama ujian, KKM, tanggal/waktu mulai, durasi.
2. Tambah soal: **Pilihan Ganda** (dengan kunci) dan/atau **Uraian**.
3. Untuk soal PG, atur **Tampilkan Nilai Otomatis: Ya/Tidak**.

**Menilai:**
- **PG** → nilai otomatis setelah submit.
- **Uraian** → beri skor manual per soal.
- **Campuran** → nilai PG otomatis, gabung setelah Uraian dikoreksi.


## 3.5 Penilaian Akhir (Input Nilai Rapor)

**Langkah (Guru Mapel):**
1. Buka **Penilaian Akhir**, pilih Mapel-Rombel.
2. Sistem menampilkan **nilai referensi** (rata-rata Tugas + CBT) sebagai usulan.
3. Terima usulan atau **override** ke nilai akhir. Grade huruf terisi otomatis.
4. Tambahkan **catatan per mapel** (disarankan). Simpan.


## 3.6 Tugas Tambahan Wali Kelas

> Hanya untuk Guru yang ditetapkan sebagai Wali Kelas suatu rombel.

**Kenaikan Kelas:**
1. Menjelang akhir Tahun Ajaran, buka **Kenaikan Kelas** untuk rombel Anda.
2. Tetapkan status tiap Peserta Didik: Naik / Tinggal / Lulus / Pindah Paket / Pindah Wilayah. Simpan.

**Rapor:**
1. Buka **Rapor** rombel Anda; pantau progres pengisian nilai per mapel.
2. Tambahkan **catatan wali kelas** (umum).
3. Setelah semua mapel lengkap, klik **Terbitkan Rapor**.
4. Pratinjau & **Cetak PDF**.


> ⚠️ Rapor tetap draft sampai seluruh mapel terisi.

---
---

# Bagian 4 — Panduan Peserta Didik

## 4.1 Melihat Jadwal & Materi

**Langkah:**
1. Lihat **jadwal pelajaran** Anda di dashboard.
2. Buka **Materi** untuk membaca/mengunduh materi dari guru per mata pelajaran.


## 4.2 Melakukan Absensi

**Langkah:**
1. Saat guru membuka sesi, buka menu **Absensi**.
2. Klik **Hadir** (atau Izin/Sakit sesuai kondisi).


> ⚠️ Absen hanya bisa saat sesi dibuka guru.

## 4.3 Mengumpulkan Tugas

**Langkah:**
1. Buka **Tugas**, pilih tugas yang aktif.
2. Unggah jawaban (file atau teks) sebelum **deadline**.
3. Lihat **nilai** setelah dinilai guru.


> ℹ️ Mengumpulkan setelah deadline tetap tercatat, ditandai **Terlambat**.

## 4.4 Mengerjakan CBT

**Langkah:**
1. Masuk pada waktu ujian yang ditentukan → buka **CBT**.
2. Saat mulai, **timer** berjalan. Kerjakan soal.
3. Klik **Selesai/Submit** sebelum waktu habis.


> ⚠️ Jika waktu habis, jawaban yang sudah terisi otomatis dikirim.

## 4.5 Melihat Nilai & Rapor

**Langkah:**
1. Lihat nilai tugas & CBT di masing-masing menu.
2. Setelah rapor **diterbitkan** Wali Kelas, buka **Rapor** untuk pratinjau & unduh PDF.


---
---

# Bagian 5 — Lampiran

## 5.1 FAQ / Pertanyaan Umum

_(Isi bertahap dari pertanyaan client yang sering muncul.)_

- **Login pakai email?** Tidak. Gunakan **username** = NIP (Guru) / NIPD (Peserta Didik).
- **Password awal apa?** Sama dengan username Anda.

## 5.2 Troubleshooting

| Masalah | Kemungkinan penyebab | Solusi |
|---|---|---|
| Tidak bisa login | Salah username/password | Pastikan username = NIP/NIPD; minta reset ke Admin. |
| File gagal diunggah | Ukuran melebihi batas | Perkecil file / cek batas upload ke Admin. |
| Tidak bisa absen | Sesi belum dibuka guru | Tunggu guru membuka sesi. |
| Muncul "Maaf, terjadi kesalahan" | Error sistem | Laporkan via Lapor Bug (lihat 5.3). |

## 5.3 Cara Melapor Bug

1. Klik tombol **Lapor Bug** di header (tersedia di semua halaman).
2. Isi judul singkat & deskripsi masalah; lampirkan screenshot bila ada.
3. Kirim. Admin akan menindaklanjuti.


## 5.4 Kontak Bantuan

- Admin / Tata Usaha PKBM Padamu Negeri: _(isi nama & kontak)_
- Tim pengembang (bila diperlukan): _(isi kontak)_

---

## Riwayat Dokumen

| Versi | Tanggal | Perubahan |
|---|---|---|
| Draf 0.1 | 05 Juli 2026 | Kerangka awal disusun. |
