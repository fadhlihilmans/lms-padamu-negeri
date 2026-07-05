# Bagian 2 — Panduan Admin / Tata Usaha

> Panduan lengkap untuk Admin, diurutkan sesuai alur kerja: dari setup awal
> aplikasi hingga operasional harian.
> [← Kembali ke Daftar Isi](00-indeks.md)

> **Pola tiap sub-bab:** _Apa & untuk apa_ → _Langkah demi langkah_ → _Screenshot_ → _Catatan/Peringatan_.

---

## 2.1 Pengaturan Aplikasi & Logo

**Untuk apa:** mengatur identitas lembaga, logo kop rapor, batas upload, mode
maintenance, dan mengaktifkan/menonaktifkan modul.

**Langkah:**
1. Buka menu **Pengaturan**.
2. Isi **Identitas PKBM** (nama, alamat, telepon/WA, email).
3. Unggah **Logo Kabupaten** dan **Logo PKBM** (untuk kop rapor).
4. Atur **Batas Upload** file materi & tugas (MB).
5. Aktif/nonaktifkan **Modul** (CBT, Tugas, Absensi).
6. **Mode Maintenance** — aktifkan saat aplikasi sedang diperbaiki (hanya Admin yang bisa masuk).
7. Klik **Simpan**.

> 📷 _Screenshot: halaman Pengaturan_

> ⚠️ Menonaktifkan modul menyembunyikan menunya dari Guru & Peserta Didik dan memblokir aksesnya.

---

## 2.2 Tahun Ajaran, Semester & Switching Periode

**Untuk apa:** menentukan periode ajaran aktif sebagai konteks data seluruh aplikasi.

**Langkah:**
1. Buka **Periode Ajaran**.
2. Tambah Tahun Ajaran & Semester baru.
3. Tetapkan satu periode sebagai **Aktif** (periode aktif sebelumnya otomatis nonaktif).
4. Gunakan **switcher periode** untuk melihat data periode lain (mode arsip/read-only).

> 📷 _Screenshot: daftar periode + switcher_

> ⚠️ Perubahan data hanya bisa dilakukan pada periode **Aktif**.

---

## 2.3 Master Data (Wilayah, Paket, Tingkat, Mapel)

**Untuk apa:** menyiapkan data induk sebelum membuat rombel.

**Langkah:**
1. **Wilayah** — tambah daftar cabang/lokasi.
2. **Paket** — tambah Paket A/B/C.
3. **Tingkat** — tambah tingkat per paket (Kelas 10, 11, 12).
4. **Mata Pelajaran** — tambah daftar mapel.

> 📷 _Screenshot: salah satu halaman master data (mis. Wilayah)_

---

## 2.4 Rombel & Penetapan Wali Kelas

**Langkah:**
1. Pastikan periode aktif benar.
2. Buka **Rombel** → **Tambah**.
3. Pilih kombinasi **Wilayah + Paket + Tingkat** (nama rombel otomatis terbentuk).
4. Tetapkan **Wali Kelas** (pilih dari daftar Guru).
5. Masukkan Peserta Didik (manual atau hasil import Excel).

> 📷 _Screenshot: form rombel_

---

## 2.5 Import Peserta Didik via Excel

**Langkah:**
1. Buka **Import Peserta Didik** → **Unduh Template**.
2. Isi data sesuai kolom (header baris 1, data mulai baris 2). Kolom Wilayah/Paket/Tingkat menentukan rombel tujuan.
3. Unggah berkas Excel.
4. Baca **ringkasan hasil**: jumlah berhasil & gagal (beserta alasan per baris).

> 📷 _Screenshot: halaman import + ringkasan hasil_

> ⚠️ Kombinasi Wilayah/Paket/Tingkat harus sudah ada di master. Baris yang tidak cocok akan gagal.

---

## 2.6 Jadwal Pelajaran & Pemetaan Pengajaran

**Langkah — Pemetaan Guru–Mapel–Rombel:**
1. Buka **Pemetaan Pengajaran**.
2. Pilih Guru, Mata Pelajaran, dan Rombel untuk periode berjalan. Simpan.

**Langkah — Jadwal Pelajaran:**
1. Buka **Jadwal Pelajaran**, pilih rombel.
2. Input Mapel, Guru, Hari, Jam Mulai–Selesai.

> 📷 _Screenshot: pemetaan & jadwal_

---

## 2.7 Kenaikan Kelas & Ruang Tunggu

**Untuk apa:** menempatkan Peserta Didik dari Ruang Tunggu ke rombel baru di Tahun Ajaran berikutnya.

**Langkah:**
1. Pada Tahun Ajaran baru, buat Rombel baru (lihat 2.4).
2. Buka **Ruang Tunggu**.
3. Assign Peserta Didik (termasuk kasus Pindah Paket/Wilayah) ke rombel baru.

> 📷 _Screenshot: ruang tunggu_

> ℹ️ Status kenaikan (Naik/Tinggal/Lulus/Pindah) ditetapkan Wali Kelas — lihat [3.6](03-panduan-guru.md).

---

## 2.8 Log Error Sistem

**Untuk apa:** menelusuri error teknis yang tercatat otomatis.

**Langkah:**
1. Buka **Log Error**.
2. Lihat daftar & detail error.
3. Hapus satu per satu atau **Hapus Semua** bila sudah tidak diperlukan.

> 📷 _Screenshot: daftar log error_

---

## 2.9 Menindaklanjuti Laporan Bug

**Langkah:**
1. Buka **Laporan Bug**.
2. Filter berdasarkan status (Baru / Diproses / Selesai / Ditolak).
3. Ubah status & tambahkan catatan tindak lanjut.
4. Hapus laporan yang sudah selesai bila perlu.

> 📷 _Screenshot: daftar laporan bug_
