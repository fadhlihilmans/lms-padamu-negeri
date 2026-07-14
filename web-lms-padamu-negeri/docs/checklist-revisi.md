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
- [x] 9.1 Form Soal CBT
- [x] 9.2 Pengerjaan CBT
- [x] 9.3 Form/Daftar CBT Baru
- [x] 9.4 Koreksi CBT
- [x] 9.5 Hasil CBT

### 10 — Kenaikan Kelas
- [x] 10.1 Assign Rombel Baru
- [x] 10.2 Kenaikan Kelas

### 11 — Rapor
- [x] 11.1 Progres Rapor
- [x] 11.2 Input Nilai Akhir Rapor
- [x] 11.3 Preview Cetak PDF

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
- [x] Lampiran Tugas — `Guru/Tugas/FormTugas` (kompres hanya bila file bertipe gambar)
- [x] Submisi Tugas — `PesertaDidik/Tugas/SubmisiTugasPD` (kompres hanya bila file bertipe gambar)

### Catatan
- [x] Verifikasi driver WebP tersedia di server produksi (Imagick/GD); Intervention otomatis pakai driver yang ada
- [x] **Output:** gambar yang diunggah tersimpan sebagai WebP ±`kompres_target_kb`, upload non-gambar tetap apa adanya, kegagalan kompres tidak pernah menggagalkan upload

---

## Fase Revisi — Logic Sistem

> Sumber: `Revisi_Logic_Sistem.md`. Diurutkan menurut **ketergantungan & kebutuhan**,
> bukan urutan asli dokumen. Tahap 3 adalah **akar** dari mayoritas bug di bagian 1–3
> dokumen asli, jadi sengaja ditaruh setelah bug kritis & keputusan.

### Tahap 0 — Keputusan (BLOCKING, harus diputuskan sebelum Tahap 3 & 4)
> ✍️ **Keputusan final keempat item di bawah sudah ditulis di [`docs/keputusan-revisi.md`](keputusan-revisi.md).**
> Baca dokumen itu sebelum mengerjakan Tahap 3 & 4. Bila tidak setuju, ubah dokumennya dulu.

- [x] **Boleh clear database?** (ada data asli NIPD 1721 tercampur dummy) → menentukan Jalur A (`migrate:fresh`) vs Jalur B (migrasi bertahap + backfill)
- [x] Plotting semester: **clone otomatis** ganjil→genap, atau tetap **manual** per semester
- [x] CBT nilai uraian: **kunci permanen** setelah dikoreksi, atau **izinkan edit terkontrol**
- [x] Strategi final "1 siswa banyak rombel" & mekanisme kenaikan kelas (pindah vs clone)

### Tahap 1 — Bug kritis (fitur tak terpakai; tidak menyentuh skema)
- [-] **Penilaian Akhir error `Undefined array key 17`** untuk semua guru kecuali `g001` — akar: `$rapors[$pid]` (Collection `offsetGet`) di `FormNilaiKomponen.php:118`, ganti ke `$rapors->get($pid)`
- [-] **Ganti dropdown rombel → pindah → balik ke rombel semula → error** (pola sama dengan di atas)
- [-] **Preview file peserta didik di MOBILE tidak berfungsi** (tidak muncul, tidak ada tombol close/X) — *prioritas tinggi*
- [-] Semua file (materi, lampiran tugas, hasil kerja) yang tampil ke siswa harus **mode preview**
- [-] Siswa di rombel baru yang **belum diisi plotting tidak bisa dihapus**

### Tahap 2 — Perbaikan cepat (risiko rendah, bisa paralel)
- [-] **Password minimal 3 karakter** (turun dari 6, agar bisa sama dengan NIPD) — ubah di `ModalGantiPassword` & `ProfilSaya`
- [-] **Import Excel:** paksa kolom **NIPD/NISN/NIK jadi teks** agar `0` di depan tidak hilang (`007` ≠ `7`)
- [-] **Import Excel:** tanda **bintang (\*)** pada header kolom yang wajib diisi
- [-] **Editor materi:** list/poin bernomor tidak terlihat di editor (hasil akhir sudah benar → dugaan CSS Trix)
- [-] **CBT:** tanda/ikon "**sedang berlangsung**" — sekarang ikon "akan datang" hilang begitu CBT dimulai
- [-] **Styling:** elemen upload file & kirim tugas tidak center
- [-] **Styling:** padding dropdown mepet (berlaku global)
- [-] **Upload:** drag & drop belum berfungsi (baru bisa klik pilih file) — *prioritas rendah*

### Tahap 3 — AKAR: Skema Tahun Ajaran (butuh keputusan Tahap 0)
> Masalah: `rombel` & `guru_mapel_rombel` sekarang terikat `periode_ajaran_id` (**TA + semester**),
> padahal `docs/database.md` menetapkan rombel terikat **Tahun Ajaran** saja. Ini akar dari
> duplikat rombel, "1 siswa banyak rombel", dashboard nyantol, jadwal duplikat, dsb.

- [-] Migration: `rombel` & `guru_mapel_rombel` terikat **Tahun Ajaran**, bukan periode (TA+semester)
- [-] Selaraskan / update `docs/database.md` bila skema final berbeda dari spesifikasi
- [-] **Plotting cukup 1× per TA** (ganjil); semester genap otomatis memakai data yang sama
- [-] Data di Admin **difilter TA aktif** saja (bukan seluruh histori)
- [-] Menu **Rombel**: tampilkan hanya rombel TA aktif
- [-] **Mata Pelajaran**: jumlah "pemetaan" dihitung dari TA aktif saja
- [-] **Dashboard Guru & Peserta Didik** ikut TA aktif (tidak nyantol ke periode lama)
- [-] **Wali kelas lama** tidak lagi tercatat aktif di TA yang sudah lewat
- [-] **Materi** tidak lagi menampilkan data semester/TA sebelumnya
- [-] **Jadwal Pelajaran (siswa)**: dropdown rombel tidak duplikat & sinkron dengan TA aktif
- [-] **Mode arsip** (dropdown TA): benar-benar read-only + menampilkan data TA lama (bukan TA aktif)
- [-] Telusuri & perbaiki akar **"1 siswa bisa punya banyak rombel"** + cegah duplikasi
- [-] **Kenaikan kelas (ganjil→genap)**: siswa **PINDAH** rombel, bukan di-clone/double
- [-] **Kenaikan kelas**: tambah **filter per rombel** + **checklist-semua** untuk pemindahan massal
- [-] **Manajemen Guru**: solusi hapus guru yang masih punya histori pemetaan di TA lampau

### Tahap 4 — Skema Nilai (Rapor & CBT) — WAJIB update `docs/database.md` dulu
- [ ] **CBT:** skema penilaian **PG 70% + Uraian 30% = 100** (sekarang ditimbang per jumlah soal di `CbtGradingService`)
- [ ] **CBT:** nilai uraian tidak bisa ke-edit tak sengaja saat mengoreksi submisi lain (ikuti keputusan Tahap 0)
- [ ] **Rapor:** komponen **"Pengetahuan" → "TUGAS"** = total nilai Tugas **+** nilai CBT
- [ ] **Rapor:** komponen **"Keterampilan" → "SAS/SAT"** = **input manual**, tanpa nilai referensi (tidak pakai CBT)
- [ ] **Rapor:** bobot **Tugas 70% + SAS/SAT 30% = 100**, bersifat **dinamis** (simpan di tabel `settings`)
- [ ] **Progres Rapor:** status "Belum Lengkap" muncul padahal tugas sudah lengkap (diduga karena siswa belum mengerjakan CBT yang lewat tenggat)
- [ ] Sesuaikan `RaporService::KOMPONEN`, `referensiForGmr()`, dan `docs/database.md`
