# checklist-revisi.md — Tracking Revisi Tampilan (papan pribadi)

> Catatan: file ini murni untuk **tracking pribadi saya**. Tidak ada aturan wajib
> apa pun yang mengikat cara kerja Claude — Claude tidak perlu mengecek file ini
> sebelum bekerja. Centang `[ ]` / `[x]` saya isi sendiri.

---

## Fase Revisi Rombak Tampilan

Menerapkan desain final `docs/design-references/*.html` ke tampilan Blade aplikasi.

### 1 — Auth & Shell
- [x] 1.1 Halaman Login
- [x] 1.2 Modal Ganti Password
- [x] 1.3 Layout Shell (sidebar + topbar)

### 2 — Dashboard
- [x] 2.1 Dashboard Admin
- [x] 2.2 Dashboard Guru
- [x] 2.3 Dashboard Peserta Didik

### 3 — Master Data
- [x] 3.1.1 Master Data (Wilayah/Paket/Tingkat/Mata Pelajaran/Periode Ajaran)
- [x] 3.1.2 Modal Master Data
- [x] 3.2.1 Master Data Rombel
- [x] 3.2.2 Form Rombel
- [x] 3.3.1 Pemetaan Guru–Mapel–Rombel
- [x] 3.4.1 Manajemen Akun
- [x] 3.4.2 Modal Reset Password

### 4 — Jadwal
- [x] 4.1 Jadwal Pelajaran
- [x] 4.2 Modal Tambah Jadwal Pelajaran

### 5 — Import
- [x] 5 Import Data Peserta Didik

### 6 — Absensi
- [x] 6.1 Absensi (Peserta Didik)
- [x] 6.2 Sesi Absensi Guru
- [x] 6.3 Rekap Absensi

### 7 — Materi
- [x] 7.1 Materi Guru
- [x] 7.2 Materi Peserta Didik
- [x] 7.3 Form Tambah Materi
- [x] 7.4.1 Detail Materi Guru
- [x] 7.4.2 Detail Materi Peserta Didik

### 8 — Tugas
- [x] 8.1 Tugas
- [x] 8.2 Submisi Tugas
- [x] 8.3 Form Tugas

### 9 — CBT
- [ ] 9.1 Form Soal CBT
- [ ] 9.2 Pengerjaan CBT
- [ ] 9.3 Form/Daftar CBT Baru
- [ ] 9.4 Koreksi CBT
- [ ] 9.5 Hasil CBT

### 10 — Kenaikan Kelas
- [ ] 10.1 Assign Rombel Baru
- [ ] 10.2 Kenaikan Kelas

### 11 — Rapor
- [ ] 11.1 Progres Rapor
- [ ] 11.2 Input Nilai Akhir Rapor
- [ ] 11.3 Preview Cetak PDF

## Fase Revisi Tambah Alert pada form

Menambahkan **pop-up (toast) otomatis** saat validasi gagal — pelengkap pesan
inline `@error(...)` yang tetap dipertahankan.

**Mekanisme sudah GLOBAL** (hook di `resources/js/app.js` → event `notify`), jadi
berlaku otomatis ke semua form Livewire tanpa mengubah komponennya. Centang di
bawah = sudah **diverifikasi** toast-nya muncul saat submit dengan input tidak valid.

### A — Auth
- [x] A.1 Login (`auth/login-form`)
- [x] A.2 Modal Ganti Password (`auth/modal-ganti-password`)

### B — Master Data
- [x] B.1 Wilayah (`admin/master-data/wilayah-manager`)
- [x] B.2 Paket (`admin/master-data/paket-manager`)
- [x] B.3 Tingkat (`admin/master-data/tingkat-manager`)
- [x] B.4 Mapel (`admin/master-data/mapel-manager`)
- [x] B.5 Periode Ajaran (`admin/master-data/periode-ajaran-manager`)
- [x] B.6 Rombel (`admin/master-data/form-rombel`)
- [x] B.7 Pemetaan Guru-Mapel-Rombel (`admin/master-data/pemetaan-guru-mapel-rombel`)

### C — Manajemen Pengguna
- [x] C.1 Manajemen Guru (`admin/pengguna/guru-manager`)
- [x] C.2 Manajemen Peserta Didik (`admin/pengguna/peserta-didik-manager`)
- [x] C.3 Import Peserta Didik (`admin/import-excel/import-peserta-didik`)

### D — Jadwal
- [x] D.1 Jadwal Pelajaran — Admin (`admin/akademik/jadwal-manager`)
- [x] D.2 Jadwal — Guru/Wali Kelas (`guru/jadwal-guru`)

### E — Absensi
- [x] E.1 Sesi Absensi — Guru (`guru/absensi/sesi-absensi`)

### F — Materi
- [x] F.1 Form Tambah/Edit Materi (`guru/materi/form-materi`)

### G — Tugas
- [x] G.1 Form Tugas (`guru/tugas/form-tugas`)
- [x] G.2 Beri Nilai Submisi (`guru/tugas/daftar-submisi`)
- [x] G.3 Detail Submisi — nilai (`guru/tugas/detail-submisi`)
- [x] G.4 Submisi Tugas — Peserta Didik (`peserta-didik/tugas/submisi-tugas-p-d`)

### H — CBT
- [x] H.1 Form Buat/Edit CBT (`guru/cbt/daftar-cbt` — modal)
- [x] H.2 Form Soal CBT (`guru/cbt/form-soal-cbt`)
- [x] H.3 Koreksi Uraian (`guru/cbt/form-koreksi-uraian`)

## Fase Revisi — Compress Image

Kompresi **hanya untuk gambar**; file lain (PDF, doc/docx, ppt/pptx, xls/xlsx) **tidak** dikompres.
Ukuran target kompresi **dinamis** dari tabel `settings` (grup Upload).

### Prasyarat
- [x] Setting `kompres_target_kb` (grup `upload`, integer, default **300 KB**) — target ukuran hasil kompres
- [x] `ImageService` reusable (Intervention Image v3, driver Imagick → fallback GD): resize dimensi maks → strip metadata → encode **WebP** hingga ± `kompres_target_kb` → **fallback simpan original bila gagal** (+ catat ke `error_log`)

### Titik upload gambar yang dikompres
- [x] Logo PKBM & Logo Kabupaten — `Admin/Pengaturan/SettingManager` (khusus gambar)
- [x] Screenshot Lapor Bug — `BugReport/FormLaporBug` (khusus gambar)
- [x] Lampiran gambar Materi — `Guru/Materi/FormMateri` (kompres hanya bila file bertipe gambar)
- [x] Gambar inline Materi via Trix — `TrixUploadController` (khusus gambar, wajib pola fallback)
- [ ] Lampiran Tugas — `Guru/Tugas/FormTugas` (kompres hanya bila file bertipe gambar)
- [ ] Submisi Tugas — `PesertaDidik/Tugas/SubmisiTugasPD` (kompres hanya bila file bertipe gambar)

### Catatan
- [ ] Verifikasi driver WebP tersedia di server produksi (Imagick/GD); Intervention otomatis pakai driver yang ada
- [ ] **Output:** gambar yang diunggah tersimpan sebagai WebP ±`kompres_target_kb`, upload non-gambar tetap apa adanya, kegagalan kompres tidak pernah menggagalkan upload
