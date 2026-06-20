# stitch-prompts.md — Kumpulan Prompt Google Stitch per Layar

> Cara pakai: buka stitch.withgoogle.com, mode **Experimental/Thinking**. Untuk
> hasil paling konsisten dengan TailAdmin, upload screenshot 1 halaman TailAdmin
> sebagai referensi visual SETIAP kali generate layar baru, lalu tempel prompt di
> bawah sebagai teks pendamping. Stitch membaca relasi spasial dari gambar, bukan
> kualitas gambarnya — jadi sketsa kasar pun cukup.
>
> Urutan layar di file ini mengikuti urutan modul di `build-steps.md`, supaya
> wireframe selesai tepat sebelum modul terkait mulai dikodekan.
>
> Setelah desain difinalkan: export HTML/Tailwind dari Stitch → simpan ke folder
> `design-reference/nama-layar.html` di proyek → minta Claude Code menirunya
> dengan struktur Livewire `$slot` (lihat `design-guide.md`).

---

## 0. Prompt Dasar (tempel di awal SETIAP sesi baru, sekali saja)

Gunakan ini sebagai "system context" informal di awal project Stitch Anda,
supaya Stitch konsisten di semua layar berikutnya:

```
I'm designing an internal LMS web admin system for an Indonesian equivalency
school (Kejar Paket / PKBM). Desktop-first web app (not mobile app). Visual
style: TailAdmin-like — clean SaaS admin dashboard, light theme, white cards
with soft shadows, rounded-lg corners, blue (#3C50E0-ish) as primary accent,
gray sidebar text, Inter or similar sans-serif font. Use Indonesian labels
exactly as I specify (do not translate to English, do not invent generic
placeholder text like "Lorem ipsum" — use the real labels I give you).
```

---

## 1. Autentikasi & Layout Dasar

### 1.1 Halaman Login

```
Web login page, desktop layout, centered card (max-width ~400px) on a soft
gray background. Card contains: small school logo placeholder at top center,
title "LMS Padamu Negeri", subtitle "Sistem Akademik PKBM", one text input
labeled "Username (NIP / NIPD)", one password input labeled "Password" with
show/hide icon, primary blue button full width labeled "Masuk". Small helper
text below the card: "Lupa password? Hubungi Admin". No email field, no
social login buttons, no "remember me" checkbox needed. Clean minimal admin
auth style, Tailwind, light theme.
```

### 1.2 Modal Ganti Password (overlay di atas dashboard)

```
Show a web admin dashboard background (sidebar + topbar + content area,
blurred/dimmed) with a centered modal popup on top. Modal title "Ganti
Password Anda". Modal body text: "Demi keamanan, segera ganti password
default Anda." Two password inputs: "Password Baru" and "Konfirmasi Password
Baru". Two buttons at the bottom: a secondary gray button "Nanti Saja" on the
left, a primary blue button "Simpan Password Baru" on the right. Modal has
rounded corners, soft shadow, close (X) icon top-right corner.
```

### 1.3 Layout Shell (Sidebar + Topbar) — dasar semua dashboard

```
Web admin dashboard shell, desktop layout, no content yet (just the frame).
Left sidebar (dark navy or white with border, ~250px wide): school logo +
name at top, then a vertical menu list with icons for: Dashboard, Master
Data, Pengguna, Jadwal Pelajaran, Import Excel, Absensi, Materi, Tugas, CBT,
Kenaikan Kelas, Penilaian Akhir, Pengaturan. Top bar (full width, white,
border-bottom): left side shows current page title, right side shows a
period switcher dropdown "TA 2023/2024 - Ganjil", a notification bell icon,
and a user avatar with name dropdown. Main content area is empty/placeholder
gray box. Clean TailAdmin-style SaaS dashboard, light theme.
```

---

## 2. Dashboard per Role

### 2.1 Dashboard Admin

```
Web admin dashboard, desktop layout, using the sidebar+topbar shell described
before. Main content: a row of 4 stat cards at the top — "Total Peserta
Didik" (with number and small icon), "Total Guru", "Total Rombel", "Periode
Aktif: 2023/2024 Ganjil". Below that, two columns: left column a table titled
"Rombel Terbaru" with columns Nama Rombel, Wilayah, Jumlah Peserta Didik,
Wali Kelas; right column a card titled "Ringkasan Absensi Hari Ini" showing a
simple horizontal bar or donut chart with Hadir/Izin/Sakit/Alpa percentages.
Clean admin UI, Tailwind, light theme, blue accent.
```

### 2.2 Dashboard Guru

```
Web admin dashboard, desktop layout, sidebar shows simpler menu for teacher
role: Dashboard, Jadwal Saya, Materi, Tugas, CBT, Absensi, Penilaian Akhir.
Main content: welcome banner "Selamat datang, [Nama Guru]" with today's date.
Below: a card "Jadwal Hari Ini" listing 2-3 schedule rows (Mapel, Rombel, Jam).
Below that: a card "Tugas Perlu Dinilai" showing a small table (Tugas, Rombel,
Jumlah Belum Dinilai) with a "Lihat Semua" link. Light theme, clean cards
with soft shadows.
```

### 2.3 Dashboard Peserta Didik

```
Web dashboard, desktop layout, sidebar shows student menu: Dashboard, Jadwal,
Materi, Tugas, CBT, Absensi, Nilai/Rapor, Profil Saya. Main content: welcome
banner with student name and class "Kelas 10 Botolambat Paket C". Below: a
prominent "Absen Sekarang" card/button if a session is open today. Below
that: two cards side by side — "Tugas Mendatang" (list with deadline dates)
and "CBT Mendatang" (list with exam name and start time). Friendly but clean
light theme, blue accent buttons.
```

---

## 3. Master Data (Admin)

### 3.1 Manajemen Data Sederhana (Wilayah / Paket / Tingkat) — pola CRUD list

```
Web admin page, desktop layout, sidebar+topbar shell, page title "Master Data
- Wilayah". Top right: a primary blue button "+ Tambah Wilayah". Below: a
simple data table with columns "No", "Nama Wilayah", "Aksi" (with small edit
pencil icon and delete trash icon per row). Table has 5 sample rows
(Botolambat, Pondok 1, Bakalan, etc). Pagination control at the bottom right.
Clean minimal CRUD table layout, light theme.
```

```
Web admin modal/slide-over panel, "Tambah Wilayah Baru" title at top, single
text input labeled "Nama Wilayah" with placeholder "contoh: Botolambat",
Cancel button (gray, secondary) and "Simpan" button (blue, primary) at the
bottom right. Small and minimal, centered modal on dimmed background.
```

> Catatan: pola CRUD list + modal tambah ini bisa dipakai ulang untuk Paket,
> Tingkat, dan Mapel — tinggal ganti kata "Wilayah" jadi nama entitasnya.

### 3.2 Manajemen Rombel

```
Web admin page, desktop layout, page title "Master Data - Rombel". Top right
"+ Buat Rombel Baru" button. Data table columns: "Nama Rombel" (e.g. "Kelas 10
Botolambat Paket C - TA 2023/2024"), "Wilayah", "Paket", "Tingkat", "Wali
Kelas" (teacher name or "Belum Ditentukan" badge in orange), "Jumlah Peserta
Didik", "Aksi" (view/edit/delete icons). Light theme, clean table with
zebra-striped rows.
```

```
Web admin form page, title "Buat Rombel Baru". Form fields stacked vertically
in a card: dropdown "Wilayah", dropdown "Paket", dropdown "Tingkat" (these
three combine into an auto-generated read-only preview field below labeled
"Nama Rombel (otomatis)" showing live preview text), dropdown "Wali Kelas
(pilih Guru)", and below that a searchable multi-select / checklist box
titled "Pilih Peserta Didik" with a list of student names and checkboxes.
Primary blue "Simpan Rombel" button at the bottom. Light theme.
```

### 3.3 Pemetaan Guru-Mapel-Rombel

```
Web admin page, title "Pemetaan Pengajaran". Top right "+ Tambah Pemetaan"
button. A form/filter row at top with 3 dropdowns: "Guru", "Mata Pelajaran",
"Rombel", plus an "Simpan" button. Below, a table showing existing mappings
with columns: Guru, Mapel, Rombel, Periode Ajaran, Aksi (delete icon). Light
theme, simple admin table.
```

### 3.4 Manajemen Akun Guru / Peserta Didik

```
Web admin page, title "Manajemen Akun Guru". Top right "+ Tambah Guru"
button and a search input. Data table columns: Foto/Avatar (circle
placeholder), NIP, Nama Lengkap, No HP, Status (Aktif badge green), Aksi
(edit, reset password key-icon, delete icons). Light theme, clean rows.
```

```
Web admin confirmation modal, title "Reset Password?", body text "Password
akan dikembalikan menjadi sama dengan Username (NIP). Pengguna akan diminta
mengganti password saat login berikutnya.", Cancel button and a red/orange
"Ya, Reset Password" button. Small centered modal.
```

---

## 4. Jadwal Pelajaran

```
Web admin page, title "Jadwal Pelajaran - Kelas 10 Botolambat Paket C". A
weekly timetable grid: columns are days (Senin–Sabtu), rows are time slots.
Each filled cell shows a small colored card with Mapel name and Guru name.
Top right: dropdown to select Rombel, and "+ Tambah Jadwal" button. Light
theme, calendar/timetable grid style with subtle grid lines.
```

```
Web admin form modal, title "Tambah Jadwal Pelajaran". Fields: dropdown
"Mata Pelajaran", dropdown "Guru Pengajar", dropdown "Hari", two time picker
inputs side by side "Jam Mulai" and "Jam Selesai". Cancel and "Simpan" button.
Compact modal, light theme.
```

---

## 5. Import Excel Peserta Didik

```
Web admin page, title "Import Data Peserta Didik". Top section: a card with
a dashed-border file drop zone labeled "Tarik file Excel di sini atau klik
untuk pilih file (.xlsx)", below it a link "Unduh Template Excel" with a
download icon, and a primary blue "Proses Import" button. Below that section,
a results card titled "Ringkasan Hasil Import" showing 3 stat boxes: "Total
Baris: 18" (gray), "Berhasil: 15" (green), "Gagal: 3" (red). Below the stats,
a small table listing failed rows: columns "Baris", "Kolom", "Alasan Gagal"
(e.g. "Baris 5 | Wilayah | Wilayah 'Sumberejo' tidak ditemukan di master
data"). Light theme, clean upload UI.
```

---

## 6. Absensi

```
Web teacher page, title "Sesi Absensi - Matematika - Kelas 10 Botolambat".
Top: a status badge "Sesi Terbuka" in green with a button "Tutup Sesi" in
red/outline next to it. Below: a real-time roster table with columns "No",
"Nama Peserta Didik", "Status" (showing colored pill badges: Hadir green,
Izin yellow, Sakit blue, Alpa red, or "Belum Absen" gray), "Waktu", and small
action buttons to manually override status per row. Light theme, live-feeling
table with subtle row highlight on recently-updated rows.
```

```
Web student page, title "Absensi - Hari Ini". A large centered card showing
today's date and class name, with 3 large button options stacked or in a
row: "Hadir" (big green button with checkmark icon), "Izin" (yellow button),
"Sakit" (blue button). Below, a small history list "Riwayat Absensi Minggu
Ini" with day-by-day status pills. Friendly, large touch-friendly buttons,
light theme.
```

---

## 7. Materi

```
Web teacher page, title "Materi - Matematika - Kelas 10 Botolambat". Top
right "+ Tambah Materi" button. Below, a list of material cards (not table),
each card showing: a small icon indicating type (document icon, video/play
icon, or image icon), title of the material, short description preview,
upload date, and edit/delete icons in the corner. 3-4 sample cards in a
vertical list. Light theme, clean card list.
```

```
Web admin form modal, title "Tambah Materi Baru". Fields: text input "Judul
Materi", textarea "Deskripsi", a radio/tab selector for "Tipe Konten" with
options: Teks, File, Link Video, Gambar — and the form area below changes
based on selection (show a file upload dropzone OR a URL text input OR a
textarea, illustrate the File Upload variant). Cancel and "Simpan Materi"
button. Light theme.
```

```
Web student page, title "Materi - Matematika". A list of material cards
similar to teacher view but read-only (no edit/delete icons), each card
clickable showing title, type icon, and date posted. Light theme.
```

---

## 8. Tugas

```
Web teacher page, title "Tugas - Matematika - Kelas 10 Botolambat". Top right
"+ Buat Tugas" button. List of assignment cards, each showing: title, deadline
date with a clock icon, a small progress indicator "12/15 Mengumpulkan", and
a "Lihat Submisi" button. Light theme, clean card list with deadline badges
(red if overdue, gray if upcoming).
```

```
Web teacher page, title "Submisi Tugas - Tugas Pertemuan 5". A table with
columns: "No", "Nama Peserta Didik", "Status" (badge: "Sudah Mengumpulkan"
green, "Terlambat" orange, "Belum Mengumpulkan" gray), "Waktu Submit", "File/
Jawaban" (download or preview link), "Nilai" (editable number input 0-100
inline in the table), and a small "Simpan" button per row or a global save
button at bottom. Light theme, dense data table.
```

```
Web student page, title "Tugas Pertemuan 5 - Matematika". Top: assignment
title, description text, deadline countdown badge "Deadline: 2 hari lagi"
in orange. Middle: a file upload dropzone OR a text answer textarea (show
both options as tabs: "Upload File" / "Tulis Jawaban"). Bottom: primary blue
"Kumpulkan Tugas" button. If already submitted, show a green confirmation
banner instead with submitted file/text and submission time. Light theme.
```

---

## 9. CBT (Computer Based Test)

### 9.1 Pembuatan CBT (Guru)

```
Web teacher page, title "Buat CBT Baru". Form in a card: text input "Nama
Ujian", number input "KKM", datetime picker "Tanggal & Waktu Mulai", number
input "Durasi (menit)", a toggle switch "Tampilkan Nilai Otomatis ke Peserta
Didik". Below the form, a section "Daftar Soal" with a "+ Tambah Soal"
button and an empty state illustration saying "Belum ada soal ditambahkan".
Light theme, clean form layout.
```

```
Web teacher modal, title "Tambah Soal". A tab/radio selector "Tipe Soal":
Pilihan Ganda or Uraian. For Pilihan Ganda variant: textarea "Pertanyaan",
then 4 option rows labeled A, B, C, D each with a text input and a radio
button to mark as "Kunci Jawaban". Cancel and "Simpan Soal" button. Light
theme, clean form modal.
```

### 9.2 Pengerjaan CBT (Peserta Didik)

```
Web full-screen exam page, desktop layout, minimal distraction-free design.
Top bar: exam title "Ujian Matematika - Bab Aljabar" on the left, a large
countdown timer "00:45:12" in a red/orange badge on the right. Main area
(left, ~70% width): one question displayed large with question number "Soal
5 dari 20", question text, and 4 selectable answer option cards (A-D) with
radio-style selection highlight. Right sidebar (~30% width): a question
navigator grid showing numbers 1-20 as small squares, answered ones filled
blue, current one outlined, unanswered ones gray. Bottom of main area:
"Sebelumnya" (gray outline) and "Selanjutnya" (blue) buttons, plus a
separate green "Submit Jawaban" button visible near the navigator. Calm,
focused, light theme, no sidebar menu (exam mode hides normal navigation).
```

### 9.3 Koreksi Uraian (Guru)

```
Web teacher page, title "Koreksi Jawaban Uraian - Ujian Matematika". Left
side: list of students needing correction with status badges ("Menunggu
Koreksi" yellow / "Selesai" green). Right side (main panel): selected
student's essay answer text displayed in a card, with a number input below
labeled "Skor (0-100)" and a "Simpan Skor & Lanjut ke Berikutnya" button.
Light theme, split-panel grading interface.
```

### 9.4 Hasil CBT (rekap nilai)

```
Web teacher page, title "Hasil CBT - Ujian Matematika". A table with columns:
"No", "Nama Peserta Didik", "Nilai PG", "Nilai Uraian", "Nilai Akhir",
"Status Penilaian" (badge: Otomatis blue, Menunggu Koreksi yellow, Selesai
Dinilai green), sorted by score descending. Top right: export/print icon
button. Light theme, clean results table with a summary stat row above
(Rata-rata, Tertinggi, Terendah, Jumlah Lulus KKM).
```

---

## 10. Kenaikan Kelas

```
Web teacher (Wali Kelas) page, title "Kenaikan Kelas - Kelas 10 Botolambat
Paket C". A table with columns: "No", "Nama Peserta Didik", "Status Saat
Ini" (Aktif badge), "Keputusan" (a dropdown per row with options: Naik
Tingkat, Tinggal, Lulus, Pindah Paket, Pindah Wilayah). Bottom: a primary
blue "Simpan Keputusan Kenaikan Kelas" button. Light theme, clean decision
table.
```

```
Web admin page, title "Assign Rombel Baru - Ruang Tunggu". Left panel: list
of students in "Ruang Tunggu" status with checkboxes, showing name and their
decision badge (Naik/Pindah Paket/etc) and origin class. Right panel: a
dropdown to select target Rombel for the checked students, and an "Assign ke
Rombel Ini" button. Light theme, two-panel assignment interface.
```

---

## 11. Penilaian Akhir (Rapor)

### 11.1 Input Nilai (Guru Mapel)

```
Web teacher page, title "Input Nilai Akhir - Matematika - Kelas 10
Botolambat - Semester Ganjil 2023/2024". A table with columns: "No", "Nama
Peserta Didik", then grouped sub-columns per component e.g. "Pengetahuan"
and "Keterampilan" each showing a "Nilai Referensi" (gray, read-only,
showing auto-calculated number) next to an editable "Nilai Akhir" input box,
followed by "Grade" (auto badge A/B/C/D), and a "Catatan" text icon that
opens a small note field. Bottom: primary blue "Simpan Semua Nilai" button.
Light theme, dense grading spreadsheet-like table.
```

### 11.2 Progres & Penerbitan (Wali Kelas)

```
Web teacher (Wali Kelas) page, title "Progres Rapor - Kelas 10 Botolambat -
Semester Ganjil". A progress checklist card showing each Mata Pelajaran with
a status icon (checkmark green = lengkap, clock yellow = belum lengkap),
e.g. "Matematika ✓", "Bahasa Indonesia ✓", "IPA (belum diisi)". Below, a
textarea "Catatan Umum Wali Kelas". Bottom: a primary blue "Terbitkan Rapor"
button, disabled/grayed out with a tooltip if not all subjects are complete
yet. Light theme.
```

### 11.3 Preview & Cetak Rapor PDF

```
Web document preview page, A4 paper proportions, white background with
shadow (like a print preview). Top: a letterhead with 3 columns — left
column small government logo placeholder, center column centered text block
"PEMERINTAH KABUPATEN BATANG", "DINAS PENDIDIKAN DAN KEBUDAYAAN", "PKBM
PADAMU NEGERI" in larger bold text, address and contact lines below in
smaller text, right column small school logo placeholder — followed by a
thick horizontal divider line. Below: centered title "LAPORAN HASIL BELAJAR
PESERTA DIDIK", then student identity info (Nama, NIPD, Rombel, Wali Kelas,
Semester, Tahun Ajaran) in a clean key-value layout. Below that: a table
with columns "No", "Mata Pelajaran", "Nilai", "Grade", "Catatan" listing
several subjects. At the bottom: "Catatan Wali Kelas" text block. A floating
"Cetak PDF" button visible above the document (not part of the printed
page). Formal, document-style, light theme, serif or formal sans font for
the letterhead text.
```

---

## 12. Pengaturan Aplikasi (Settings)

```
Web admin page, title "Pengaturan Aplikasi". Layout uses left-side vertical
tabs (not top tabs) for groups: "Umum", "Kop Rapor", "Upload", "Modul". Active
tab "Umum" is selected, showing a form card with: text input "Nama PKBM"
(value "PKBM Padamu Negeri"), a toggle switch "Mode Maintenance" with a red
warning helper text below it "Jika aktif, hanya Admin yang bisa mengakses
sistem", primary blue "Simpan Perubahan" button bottom right. Light theme,
settings page pattern with sidebar tabs on the left.
```

```
Web admin page, same Pengaturan Aplikasi layout with left tabs, but "Kop
Rapor" tab active. Form fields: two file upload boxes side by side labeled
"Logo Kabupaten" and "Logo PKBM" each showing a small image preview
placeholder and a "Ganti Gambar" button, below them a textarea "Alamat
PKBM", text input "Telepon/WhatsApp", text input "Email". "Simpan
Perubahan" button bottom right. Light theme.
```

```
Web admin page, same Pengaturan Aplikasi layout, "Modul" tab active. A list
of toggle rows, each row showing a module name on the left ("Modul CBT",
"Modul Tugas", "Modul Absensi", "Modul Materi") with a short description
below the name ("Nonaktifkan untuk menyembunyikan dan mengunci akses fitur
ini bagi Guru dan Peserta Didik"), and a toggle switch on the right side of
each row (all four ON/blue). Light theme, clean settings list with toggle
switches.
```

```
Web full-page maintenance screen, centered content on a plain light
background (no sidebar, no topbar menu — this replaces the whole app for
non-admin users). A large icon (wrench or gear), heading "Sedang
Pemeliharaan", body text "Sistem sedang dalam pemeliharaan. Silakan coba
lagi beberapa saat lagi.", small footer text with PKBM name. Calm, minimal,
centered, light theme.
```

---



## 13. Error Log & Lapor Bug

```
Web admin page, title "Log Error Sistem". Top right: a red outline button
"Hapus Semua" and a date range filter input. A data table with columns:
"Waktu" (datetime), "Aksi" (e.g. "Update Jabatan"), "Pesan Error" (truncated
text with a small "Lihat Detail" link), "Pengguna" (user name or "Sistem" if
none), "URL", and a small trash icon per row for individual delete. Rows
have a subtle red-left-border accent to indicate error severity. Light
theme, dense technical log table.
```

```
Web modal, title "Detail Error". Shows full error message in a monospace
code-block style box (gray background, red text), below it metadata in a
key-value list: Waktu, Pengguna, URL, Aksi. A "Tutup" button at the bottom.
Light theme, technical detail modal.
```

```
Web confirmation modal, title "Hapus Semua Log Error?", body text "Tindakan
ini akan menghapus seluruh log error secara permanen dan tidak dapat
dibatalkan.", Cancel button (gray) and a red "Ya, Hapus Semua" button.
Small centered modal with a warning triangle icon.
```

```
Web small floating button or topbar icon button, bug/flag icon, tooltip
"Lapor Bug atau Masalah". When clicked, opens a slide-over panel from the
right titled "Lapor Bug / Masalah" with fields: text input "Judul Masalah",
textarea "Deskripsi Masalah" (placeholder "Jelaskan apa yang terjadi dan
yang Anda harapkan..."), a file/image upload dropzone labeled "Lampirkan
Screenshot (opsional)" with image preview after upload, and a primary blue
"Kirim Laporan" button at the bottom. Light theme, simple friendly bug
report form, not intimidating.
```

```
Web admin page, title "Laporan Bug dari Pengguna". Top: filter tabs/pills
for status: Semua, Baru, Diproses, Selesai, Ditolak. A list of report cards
(not table), each card showing: reporter name + role badge (Guru/Peserta
Didik/Admin), report title, short description preview, attached screenshot
thumbnail if any, submitted date, and a status dropdown badge (colored:
Baru=blue, Diproses=yellow, Selesai=green, Ditolak=gray) to update status
inline. Light theme, clean card list, empty state illustration if no
reports match filter.
```

---

## Tips Tambahan

- **Generate satu layar per prompt.** Jangan gabungkan beberapa layar dalam satu
  prompt panjang — hasilnya cenderung berantakan. Iterasi satu per satu.
- **Refine, bukan ulang dari nol.** Setelah hasil pertama keluar, lanjutkan dengan
  instruksi singkat seperti "buat sidebar lebih ramping" atau "ubah warna aksen
  jadi hijau" — Stitch akan regenerate berdasarkan versi sebelumnya.
- **Konsistensi warna/tema:** simpan satu kalimat tema (lihat Prompt Dasar di atas)
  dan tempel ulang di setiap prompt baru supaya semua layar terasa satu sistem.
- **Untuk variasi pola CRUD** (Paket, Tingkat, Mapel, dll.) yang mirip Wilayah:
  cukup ambil prompt 3.1 dan ganti nama entitasnya — tidak perlu generate prompt
  baru dari nol untuk tiap master data sederhana.
- **Setelah puas dengan satu layar:** ekspor HTML/Tailwind (BUKAN gambar/screenshot
  jika memungkinkan — lihat `design-guide.md` Bagian C soal kenapa ini penting),
  simpan ke `design-reference/<nama-layar>.html`, lalu lanjut ke layar berikutnya.
- **Selalu cek responsive sebelum lanjut:** minta Stitch tampilkan preview mobile
  juga ("show me a mobile/narrow viewport version of this") untuk layar yang akan
  sering diakses dari HP, terutama Absensi dan CBT Peserta Didik.