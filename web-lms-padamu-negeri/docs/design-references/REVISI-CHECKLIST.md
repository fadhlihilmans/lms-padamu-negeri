# Checklist Revisi Design References

> File sementara untuk tracking progress revisi HTML. Hapus setelah selesai.

---

## CATATAN GLOBAL (berlaku semua file)
- [ ] Background content → `#f9fafb`
- [ ] Border di atas bg content → `#e5e7eb` (border-gray-200)
- [ ] Border di atas bg putih → `#d1d5db` (border-gray-300)
- [ ] Header tabel → putih semua (bukan abu/biru)
- [ ] Semua dropdown: teks "data" → "Data" (capital), e.g. "Tampil 10 **Data**"
- [ ] Footer modal (bg `#f6fafe`) → ubah ke putih di semua modal
- [ ] Dropdown yang tertimpa → perbaiki z-index / overflow visible
- [ ] Modal desktop → sudut bawah `rounded` (rounded-b-xl)
- [ ] Modal mobile (bottom-sheet) → sudut bawah tidak rounded

---

## 1.1 — Halaman Login
- [ ] Mobile: cegah geser/zoom (periksa overflow, max-width card 100%)
- [ ] Border luar card → `#d1d5db`
- [ ] Icon mata (visibility toggle) → pastikan `top-1/2 -translate-y-1/2` presisi di dalam wrapper

## 1.2 — Modal Ganti Password
- [ ] Icon mata → vertikal center
- [ ] Footer modal (bg `#f6fafe`) → putih
- [ ] Desktop: sudut bawah rounded
- [ ] Mobile: sudut bawah tidak rounded

## 1.3 — Layout Shell
- [ ] Mobile: cegah geser/overflow horizontal
- [ ] Mobile sidebar: icon sejajar teks, besarkan icon, margin text proporsional

## 2.1 — Dashboard Admin
- [ ] Bg baris tabel "Rombel Terbaru" (nama rombel, paket, dst) → putih (hapus bg abu)

## 2.2 — Dashboard Guru
- [ ] ✅ Aman (verifikasi viewport saja)

## 2.3 — Dashboard Peserta Didik
- [ ] ✅ Aman

## 3.1.1 — Master Data Wilayah
- [ ] Background toolbar/content area → putih
- [ ] Icon sidebar "Rombel" → hapus shape lingkaran (div bulat di sekitar icon)

## 3.1.2 — Modal Wilayah
- [ ] Desktop: sudut bawah rounded
- [ ] Mobile: sudut bawah tidak rounded

## 3.2.1 — Master Data Rombel
- [ ] Background content area → putih
- [ ] Mobile: layout diproporsionalkan
- [ ] Dropdown: padding kanan tambahkan ruang agar icon panah tidak mepet (`pr-8` atau lebih)
- [ ] Label "rombel" → "Rombel" (kapital)

## 3.2.2 — Form Rombel
- [ ] Footer modal (bg `#f6fafe`) → putih (termasuk bagian paling bawah)

## 3.3.1 — Pemetaan Guru
- [ ] Background content area → putih

## 3.4.1 — Manajemen Akun
- [ ] Background content area → putih

## 3.4.2 — Modal Reset Password
- [ ] Background → putih
- [ ] Footer modal → putih
- [ ] Desktop: sudut bawah rounded
- [ ] Mobile: sudut bawah tidak rounded, teks tombol di-center

## 4.1 — Jadwal Pelajaran
- [ ] Teks nama hari → `#171c1f` hitam (hapus warna biru/primary)
- [ ] Mobile: rapikan layout kalau memungkinkan

## 4.2 — Modal Tambah Jadwal Pelajaran
- [ ] Placeholder dropdown → "Pilih Rombel", "Pilih Mapel" (capital each word)
- [ ] Footer modal → putih
- [ ] Desktop: sudut bawah rounded

## 5 — Import Data Peserta Didik
- [ ] Background content area → putih

## 6.1 — Absensi (Peserta Didik)
- [ ] Teks "PILIH STATUS KEHADIRAN" → kontras (`#171c1f`), hapus warna abu
- [ ] Mobile: sesuaikan ukuran teks dan icon di kartu pilihan status
- [ ] Desktop: grid riwayat kehadiran → 7 kolom (7 hari)
- [ ] Mobile: cek apakah 4 kolom tidak terlalu kecil, sesuaikan

## 6.2 — Sesi Absensi Guru
- [ ] Mobile: tombol "Tutup Sesi" → full width (`w-full`)
- [ ] Background content area → putih

## 6.3 — Rekap Absensi
- [ ] Mobile: input tanggal (mulai & akhir) → full width seperti search bar
- [ ] Background content area → putih

## 7.1 — Materi Guru
- [ ] Dropdown filter tipe → perbaiki z-index agar tidak tertimpa elemen lain
- [ ] Tombol aksi (edit/hapus) → selalu tampil, hapus hover-only
- [ ] Icon PDF → vertikal center dalam card
- [ ] Tanggal → vertikal center dalam card
- [ ] Mobile: layout kartu → icon kiri | judul+deskripsi tengah | tanggal bawah | tombol aksi kanan (vertikal)

## 7.2 — Materi Peserta Didik
- [ ] Sama dengan revisi 7.1
- [ ] Info di bawah file PDF: hapus "oleh Budi Wahyono", sisakan tanggal saja
- [ ] Mobile: longgarkan padding (terutama area list mata pelajaran dengan tabel)

## 7.3 — Modal Tambah Materi
- [ ] Footer modal → putih
- [ ] Desktop: sudut bawah rounded

## 8.1 — Daftar Tugas Guru
- [ ] Background tabel → putih
- [ ] Dropdown filter status → perbaiki z-index agar tidak tertimpa
- [ ] Mobile: rapikan layout toolbar dan kartu/tabel

## 8.2 — Submisi Tugas
- [ ] Background tabel → putih

## 8.3 — Form Tugas Peserta Didik
- [ ] Background area judul tugas (mis. "Persamaan Kuadrat Lanjutan") → putih

## 9.1 — Form Soal CBT
- [ ] Mobile: longgarkan margin/padding (saat ini terlalu mepet)
- [ ] Background content → putih

## 9.2 — Pengerjaan CBT
- [ ] Background → putih
- [ ] Mobile: tombol Submit → perbaiki layout dan rounded (samakan dengan tombol lain)
- [ ] Mobile: tambahkan navigasi grid soal (misal: bottom sheet atau collapsible FAB nav)

## 9.3 — Daftar CBT Guru
- [ ] Dropdown filter → perbaiki z-index, drop ke bawah jika tidak muat ke atas
- [ ] Mobile context banner (biru): urutan → Paket C → Kelas 10 Botolambat → Periode 2024/25
- [ ] Mobile toolbar: search bar → full width, rapikan susunan filter

## 9.4 — Koreksi Uraian
- [ ] Background header tabel (panel kanan) → putih

## 9.5 — Hasil CBT
- [ ] Stat card "12/15": ukuran angka "15" disamakan dengan "12"
- [ ] Mobile: rapikan layout stat cards dan tabel
- [ ] Background header tabel → putih

## 10.1 — Assign Rombel Baru
- [ ] ✅ Aman

## 10.2 — Kenaikan Kelas
- [ ] Redesign: ganti dropdown per-baris → checkbox massal (pilih semua + tentukan keputusan sekaligus, seperti pola 10.1)

## 11.1 — Progres Rapor
- [ ] Background header tabel (list mapel) → putih

## 11.2 — Input Nilai Akhir
- [ ] Mobile: icon mapel (calculate) → letakkan inline di sebelah teks heading, bukan berdiri sendiri
- [ ] Background header tabel → putih

## 11.3 — Preview Cetak Rapor
- [ ] Titik-titik TTD: samakan lebar/indentasi antar kolom (atau hapus `(....)` saja)
- [ ] Tombol "Cetak PDF": pindah dari floating FAB → sticky di header halaman atau di atas dokumen A4 rata kanan (sejajar konten)

---

_Terakhir diperbarui: 2026-06-28_
