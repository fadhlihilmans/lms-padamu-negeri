# keputusan-revisi.md — Catatan Keputusan (Tahap 0)

> Keputusan final untuk **Tahap 0** pada `docs/checklist-revisi.md` → *Fase Revisi — Logic Sistem*.
> Diputuskan atas izin pemilik proyek. Dokumen ini **membuka blokir Tahap 3 & 4**.
> Bila ada yang ingin diubah, ubah DI SINI dulu sebelum kode disentuh.

---

## Ringkasan Akar Masalah (dasar semua keputusan di bawah)

Dua fakta dari skema yang ada sekarang:

1. **`rombel` & `guru_mapel_rombel` terikat `periode_ajaran_id`** (Tahun Ajaran **+ semester**),
   padahal `docs/database.md` menetapkan rombel terikat **Tahun Ajaran saja**
   (*"terikat ke TA, bukan semester"*). Implementasi menyimpang dari spesifikasi.

2. **`peserta_didik_rombel` hanya `unique(peserta_didik_id, rombel_id)`.**
   Ini cuma mencegah siswa masuk rombel **yang sama** dua kali — **tidak** mencegah siswa
   berada di **banyak rombel sekaligus**.

**Akibat gabungan keduanya:** setiap ganti semester lahir rombel baru → siswa disalin ke sana →
siswa menumpuk rombel → dashboard "nyantol", jadwal duplikat, materi TA lama muncul, wali kelas
lama masih aktif. Jadi bagian 1, 2, dan 3 dokumen revisi **bukan tiga bug terpisah — satu akar.**

---

## Keputusan 1 — Clear Database: **YA (Jalur A: clear & rebuild)**, bertingkat

**Lokal / development:** boleh di-clear bebas. Seluruh isinya 100% data seeder.

**Produksi:** clear **diizinkan**, tetapi **wajib** lewat protokol ini:
1. **Backup penuh** (`mysqldump`) dan simpan **di luar server**.
2. **Ekspor data ASLI** peserta didik ke Excel memakai format template Import yang sudah ada.
3. Skema TA baru **selesai & teruji di lokal** lebih dulu.
4. Baru `php artisan migrate:fresh --seed` di produksi.
5. **Import ulang** data asli lewat fitur Import Excel.

**Alasan:** data produksi sudah terlanjur rusak (rombel & keanggotaan siswa duplikat). Migrasi
bertahap (Jalur B) berarti **mewariskan kekacauan** ke skema baru, jauh lebih rumit dan berisiko,
demi menyelamatkan data yang jumlahnya sedikit dan mayoritas dummy.

**Pagar pengaman:** eksekusi `migrate:fresh` di **produksi** tetap dikonfirmasi ulang tepat sebelum
dijalankan (tindakan tidak bisa dibatalkan). Izin ini **tidak** berarti boleh menghapus produksi
tanpa backup.

---

## Keputusan 2 — Plotting ganjil→genap: **TIDAK di-clone. Dimensi semester dihapus.**

`guru_mapel_rombel` diubah dari `periode_ajaran_id` → **`tahun_ajaran`**.

**Konsekuensi:** plotting otomatis berlaku **1× per Tahun Ajaran**; semester genap memakai data
yang sama **tanpa menyalin apa pun**.

**Kenapa bukan clone otomatis:** clone = menggandakan baris = **persis penyakit duplikasi yang
sedang kita berantas**. Menghapus dimensi semester menyelesaikan masalah *by design*, bukan
menambalnya. Semester tetap relevan untuk data **transaksional** (absensi, tugas, CBT, rapor),
bukan untuk **struktur** (rombel & plotting).

---

## Keputusan 3 — CBT nilai uraian: **Dikunci setelah dikoreksi + aksi "Edit Nilai" eksplisit** (edit terkontrol)

- Setelah guru **menyimpan** koreksi seorang peserta didik, skor uraiannya menjadi **read-only**.
- Untuk mengubah, guru harus menekan aksi **eksplisit "Edit Nilai"** pada baris peserta didik itu.
- Terpisah dari itu: form koreksi **wajib di-scope per peserta didik** — state tidak boleh bocor
  antar submisi (ini bug teknis yang menyebabkan "ke-edit tidak sengaja", tetap diperbaiki).

**Alasan:** kunci permanen berbahaya (salah ketik jadi abadi); bebas-edit = keluhan aslinya.
Edit terkontrol mencegah kecelakaan tanpa memenjarakan guru.

---

## Keputusan 4 — **Satu peserta didik = satu rombel per Tahun Ajaran** (PINDAH, bukan clone)

- Rombel keyed per **Tahun Ajaran** (Keputusan 2) → ganjil↔genap **tidak lagi** melahirkan rombel
  baru → duplikasi antar semester **hilang dengan sendirinya**.
- Aturan baru: **1 peserta didik hanya boleh punya 1 rombel per TA.** Dijaga di level aplikasi,
  didukung unique index pada `(peserta_didik_id, tahun_ajaran)` bila memungkinkan.
- **Kenaikan kelas (antar TA)** = membuat keanggotaan baru di rombel TA berikutnya. Keanggotaan TA
  lama **tetap** sebagai histori — beda TA, jadi bukan duplikat.
- **UI Kenaikan Kelas:** tambah **filter per rombel** + **checklist-semua** untuk pemindahan massal.

---

## Dampak ke dokumen lain

- `docs/database.md` **wajib diperbarui** sebelum Tahap 3 & 4 dieksekusi:
  - `rombel.tahun_ajaran` & `guru_mapel_rombel.tahun_ajaran` (ganti `periode_ajaran_id`)
  - constraint baru `peserta_didik_rombel` (1 rombel per TA)
  - skema nilai baru (Tahap 4): komponen **TUGAS** & **SAS/SAT**, bobot dinamis di tabel `settings`
- Aturan CLAUDE.md tetap berlaku: **dokumen menang atas percakapan.**
