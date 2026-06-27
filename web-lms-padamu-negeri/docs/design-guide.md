# design-guide.md — Dari Google Stitch ke TailAdmin + Livewire $slot

## Bagian A — Bagaimana Claude Code "tahu" desain Stitch yang saya mau

Claude Code **tidak bisa membuka link Google Stitch sendiri**. Anda harus
membawa desainnya ke dalam folder proyek dalam salah satu bentuk berikut
(urut dari paling efektif):

1. **Export kode dari Stitch (paling akurat).** Stitch bisa mengekspor HTML/CSS
   (Tailwind). Simpan hasilnya ke folder referensi, mis. `design-reference/dashboard-admin.html`.
   Lalu perintahkan: *"Bangun komponen Livewire untuk dashboard admin yang meniru
   layout & styling di `design-reference/dashboard-admin.html`, tapi pakai struktur
   blade/Livewire dan komponen TailAdmin kita."*

2. **Screenshot.** Simpan PNG mockup ke `design-reference/` ATAU paste/tarik gambar
   langsung ke chat Claude Code di VS Code (ekstensi bisa membaca gambar). Lalu:
   *"Ikuti tata letak pada gambar ini untuk halaman X."*

3. **Deskripsi teks** (paling lemah, untuk penyesuaian kecil): "sidebar kiri gelap,
   card statistik 4 kolom di atas, tabel di bawah".

**Rekomendasi alur:** Stitch hanya untuk **wireframe cepat per halaman** (PRD Tahap
10.2). Begitu Anda suka, export ke `design-reference/`, lalu Claude Code
menerjemahkannya ke TailAdmin. Jangan harap Stitch menghasilkan kode produksi final
— anggap ia "papan sketsa". TailAdmin tetap sumber komponen UI sungguhan (tombol,
form, tabel, modal), karena keduanya sama-sama Tailwind sehingga konversinya mulus.

---

## Bagian B — Layout pakai Livewire `$slot`

Di Livewire 3, satu komponen full-page dibungkus oleh **layout component** yang
punya `{{ $slot }}`. Isi halaman dari tiap komponen Livewire "masuk" ke posisi
`$slot`. Anda menulis sidebar + topbar TailAdmin **sekali** di layout, dan semua
halaman otomatis ikut.

### 1. Layout (`resources/views/components/layouts/app.blade.php`)

Sudah ada di proyek — jangan ubah strukturnya. Pola kunci:
- Sidebar: `fixed inset-y-0 left-0 w-[280px]`, translate-x untuk open/close di mobile
- Overlay backdrop mobile: `fixed inset-0 z-40 bg-black/50 lg:hidden`
- Main content: `flex-1 overflow-y-auto`
- `<main class="flex-1 overflow-y-auto p-gutter">{{ $slot }}</main>`

### 2. Komponen halaman tinggal "menumpang" layout itu

```php
#[Layout('components.layouts.app')]
#[Title('Nama Halaman')]
class NamaKomponen extends Component { ... }
```

> **Multi-File Component:** class PHP dan file blade SELALU dipisah — jangan pakai single-file/Volt.

### 3. Slot bernama (opsional)
Kalau butuh area ekstra (mis. judul header berbeda per halaman):
di layout `{{ $header ?? '' }}`, di halaman `<x-slot:header>...</x-slot:header>`.

---

## Bagian C — Jika Hasil Export Stitch Hanya Berupa Gambar

Kadang hasil export/screenshot dari Stitch yang Anda taruh di
`design-reference/` **bukan kode HTML/Tailwind sungguhan**, melainkan gambar
statis. Cara menanganinya:

1. Jangan suruh Claude Code "menyalin" gambar itu jadi HTML 1:1 secara visual semata.
2. Perintahkan eksplisit: *"Gambar di `design-reference/X.png` ini hasil render Stitch.
   Bangun ulang sebagai komponen Livewire + TailAdmin yang MENERJEMAHKAN maksud
   layout-nya — bukan meniru piksel persis. Pastikan hasilnya responsive."*
3. Screenshot per-bagian jika gambar terlalu kompleks.
4. Selalu re-export dari Stitch sebagai **kode**, bukan gambar.

---

## Bagian D — Standar Wajib: Responsive & User-Friendly

> Standar ini berlaku **otomatis** di setiap komponen yang dibangun — tidak perlu
> diminta ulang setiap saat. Ini aturan dasar, bukan opsional.

### D.1 — Mobile-First Mindset

- Mulai dari tampilan mobile, lalu tambahkan breakpoint `sm:`, `md:`, `lg:` untuk
  layar lebih besar. Jangan desain desktop dulu lalu coba perkecil.
- **Sidebar:** di mobile (`<lg`) selalu tersembunyi (translate-x-full), dibuka via
  hamburger. Di desktop (`lg:`) selalu terlihat — pakai pola yang sudah ada di
  `app.blade.php`. Jangan buat sidebar-mobile terpisah.
- **Topbar:** hamburger button sudah ada di `topbar.blade.php` — JANGAN duplikasi.
  Pastikan `pageTitle` prop diisi dengan judul yang jelas dan singkat.
- **Padding halaman:** gunakan `p-gutter` (24px) yang sudah didefinisikan di Tailwind
  config — ini otomatis jadi `p-4` di mobile dan `p-6` di desktop via CSS variable.
  Kalau belum ada konfigurasi itu, gunakan `p-4 sm:p-6`.
- **Cursor pointer:** SELALU tambahkan `cursor-pointer` pada setiap elemen yang bisa
  diklik (button, a, select, label for checkbox, dll).

### D.2 — Tabel Data

- SELALU bungkus tabel dengan `<div class="overflow-x-auto -mx-4 sm:mx-0 px-4 sm:px-0">`.
  Ini mencegah halaman melebar di mobile.
- Kolom tabel di mobile: sembunyikan kolom kurang penting dengan `hidden sm:table-cell`
  atau `hidden md:table-cell` — prioritaskan kolom nama/identitas + aksi.
- Tinggi baris tabel minimal 48px untuk touch-friendly.
- Thead: selalu gunakan `sticky top-0` jika tabel panjang.

### D.3 — Form & Input

- Input **selalu full-width** (`w-full`) di semua ukuran layar.
- Tinggi input minimal `py-2.5` (≈44px total dengan padding + border) — touch target.
- Label di atas input, BUKAN di samping — konsisten di semua form.
- Error message: `text-[12px] text-[#ba1a1a] mt-1 flex items-center gap-1`.
- Grid form: `grid grid-cols-1 sm:grid-cols-2 gap-4` — stack di mobile, 2 kolom di `sm:`.

### D.4 — Button & Touch Target

- Tinggi minimum tombol: `py-2.5` sehingga total ≥44px — wajib untuk touch-friendly.
- Di mobile, tombol aksi utama SELALU `w-full sm:w-auto` agar mudah disentuh.
- Kelompok tombol: di mobile stack vertikal (`flex flex-col sm:flex-row`), di desktop inline.
- Jangan gunakan tombol ukuran kecil (`px-2 py-1`) untuk aksi utama — hanya untuk
  aksi sekunder dalam tabel.

### D.5 — State Kosong & Loading

- **State kosong:** WAJIB ada. Pola minimum:
  ```html
  <div class="text-center py-12 px-4">
      <span class="material-symbols-outlined text-[48px] text-[#c5c5d7] mb-3 block">inbox</span>
      <p class="text-[15px] font-medium text-on-surface">Belum ada data</p>
      <p class="text-[13px] text-[#505f76] mt-1">Keterangan singkat.</p>
  </div>
  ```
- **Loading:** gunakan `wire:loading` bawaan Livewire. Minimal tambahkan
  `wire:loading.attr="disabled"` dan `wire:loading.class="opacity-60"` pada tombol submit.
  Untuk loading tabel, pakai `wire:loading.class="opacity-50"` pada wrapper tabel.

### D.6 — Konfirmasi Aksi Merusak

- Hapus, reset password, nonaktifkan modul: SELALU pakai modal konfirmasi, jangan
  langsung eksekusi dari satu klik.
- Pola modal konfirmasi: lihat **Bagian E.3** di bawah.

### D.7 — Aksesibilitas & Keterbacaan

- Teks body minimal `text-[14px]` (14px), jangan lebih kecil untuk konten penting.
- Label input, judul section: minimal `text-[13px] font-medium`.
- Gunakan `truncate` atau `line-clamp-*` untuk teks yang bisa overflow, jangan biarkan
  layout pecah karena nama panjang.
- Semua gambar/avatar/icon yang merupakan dekorasi harus `aria-hidden="true"`.

---

## Bagian E — Design Token Wajib (Konsistensi Visual)

> Gunakan token-token ini **persis**, tidak boleh improvisasi sendiri. Ini memastikan
> semua komponen terlihat konsisten — modal, card, button, input semuanya satu bahasa visual.

### E.1 — Border Radius

| Konteks | Class Tailwind | Nilai |
|---------|---------------|-------|
| Card utama, Modal panel | `rounded-xl` | 12px |
| Card dalam card, Section dalam modal | `rounded-lg` | 8px |
| Button, Input, Select, Badge status | `rounded-lg` | 8px |
| Avatar, Dot indicator | `rounded-full` | 9999px |
| Pill badge (tag kecil) | `rounded-full` | 9999px |
| Icon container kecil (24–40px) | `rounded-lg` | 8px |
| Icon container besar (48px+) | `rounded-xl` | 12px |

**Aturan:** Modal SELALU `rounded-xl`. Card SELALU `rounded-xl`. Jangan pakai `rounded-2xl`
atau `rounded-3xl` kecuali untuk bottom-sheet mobile (lihat E.3).

### E.2 — Shadow

| Konteks | Class |
|---------|-------|
| Card biasa | `shadow-sm` |
| Card hover / card aktif | `shadow-md` |
| Modal, Dropdown, Floating panel | `shadow-xl shadow-black/10` |
| Tombol primary (opsional) | `shadow-sm` |
| Sidebar (mobile, saat terbuka) | `shadow-xl` |

### E.3 — Modal: Struktur, Ukuran & Backdrop

**WAJIB: Setiap modal menggunakan pola ini persis:**

```html
{{-- Backdrop + Container --}}
<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4">
    {{-- Backdrop blur --}}
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="tutupModal()"></div>

    {{-- Panel Modal --}}
    {{-- w-full = full-width bottom-sheet di mobile; max-w-4xl = safety cap (mencegah full-screen di layar besar); sm:max-w-* = ukuran desktop --}}
    <div class="relative z-10 w-full max-w-4xl sm:max-w-md bg-white rounded-t-2xl sm:rounded-xl shadow-xl shadow-black/10
                flex flex-col max-h-[90dvh] sm:max-h-[85vh]">

        {{-- Header Modal --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-[#c5c5d7] flex-shrink-0">
            <h3 class="text-[16px] font-semibold text-on-surface">Judul Modal</h3>
            <button type="button" @click="tutupModal()"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-[#757686]
                           hover:bg-[#f0f4f8] hover:text-on-surface transition-colors cursor-pointer">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        {{-- Body Modal (scrollable) — min-h-0 WAJIB di flex column agar overflow-y-auto bekerja --}}
        <div class="flex-1 min-h-0 overflow-y-auto p-5 space-y-4">
            {{-- Konten form/info --}}
        </div>

        {{-- Footer Modal --}}
        <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 px-5 py-4
                    border-t border-[#c5c5d7] flex-shrink-0">
            <button type="button" @click="tutupModal()"
                    class="w-full sm:w-auto px-5 py-2.5 rounded-lg border border-[#c5c5d7]
                           text-[14px] text-[#505f76] hover:bg-[#f0f4f8] transition-colors cursor-pointer">
                Batal
            </button>
            <button type="submit"
                    class="w-full sm:w-auto px-5 py-2.5 rounded-lg bg-[#3c50e0] text-white
                           text-[14px] font-semibold hover:bg-[#2e3eb0] transition-colors cursor-pointer
                           disabled:opacity-60 shadow-sm">
                Simpan
            </button>
        </div>

    </div>
</div>
```

**Ukuran modal berdasarkan konten — WAJIB disesuaikan, bukan selalu `max-w-md`:**

| Ukuran | Class Desktop | Penggunaan | Contoh nyata |
|--------|--------------|-----------|-------------|
| XS — Konfirmasi | `sm:max-w-sm` (384px) | Hanya teks konfirmasi + 2 tombol, tanpa form | Modal hapus, modal logout |
| S — Form ringkas | `sm:max-w-md` (448px) | 1–3 field input | Modal ganti password (2 field), modal nama singkat |
| M — Form standar | `sm:max-w-lg` (512px) | 4–6 field input | Modal tambah wilayah, modal tambah paket, modal periode ajaran |
| L — Form kompleks | `sm:max-w-xl` (576px) | 7–10 field, atau field + preview | Modal tambah guru, modal tambah peserta didik |
| XL — Detail / tabel dalam modal | `sm:max-w-2xl` (672px) | Tabel dalam modal, form dengan banyak section, preview jadwal | Modal pemetaan guru-mapel, modal import preview |
| XXL — Editor / full-detail | `sm:max-w-4xl` (896px) | Form soal CBT, form materi (dengan rich editor), preview PDF kecil | Form soal CBT, form tugas panjang |

> **DILARANG:** Modal `max-w-5xl`, `max-w-6xl`, `max-w-7xl`, atau lebih besar dari itu di desktop —
> ini membuat modal hampir full-screen dan terasa seperti halaman baru, bukan dialog.
> Jika konten benar-benar terlalu besar untuk modal, buat halaman terpisah dengan route sendiri.

**Aturan penentuan ukuran modal (checklist sebelum coding):**
1. Hitung jumlah field form: 1–3 field → `max-w-md`, 4–6 → `max-w-lg`, 7–10 → `max-w-xl`.
2. Apakah ada tabel, list, atau preview di dalam modal? → minimal `max-w-2xl`.
3. Apakah modal hanya teks konfirmasi? → `max-w-sm`, jangan lebih besar.
4. Cek: apakah pada layar 1366px lebar, modal menyisakan ruang di kiri-kanan minimal 10%? 
   Jika tidak, turunkan satu ukuran atau buat halaman terpisah.
5. **Modal ganti password** (hanya 2 field: password baru + konfirmasi): `sm:max-w-md` — tidak boleh lebih besar.
6. **Modal periode ajaran** (nama TA + semester + status): `sm:max-w-lg` — cukup untuk 3–4 field.
7. **Modal pemetaan guru-mapel** (dropdown guru + dropdown mapel + dropdown rombel): `sm:max-w-lg`.

**Aturan Backdrop:**
- SELALU `bg-black/50 backdrop-blur-sm` — tidak boleh `bg-black/40` atau tanpa `backdrop-blur-sm`.
- SELALU bisa ditutup dengan klik backdrop (`@click="tutupModal()"`) KECUALI modal konfirmasi kritis.

**Aturan Ukuran: DILARANG FULL SCREEN — berlaku untuk semua modal tanpa terkecuali:**
- DILARANG menggunakan `w-screen`, `h-screen`, `min-h-screen`, `max-w-full`, `inset-0` pada panel modal itu sendiri
  (berbeda dengan backdrop yang memang `inset-0` — itu benar).
- DILARANG `max-w-5xl` ke atas — lihat tabel ukuran di atas untuk batas maksimum yang diperbolehkan.
- DILARANG `h-full` atau `min-h-full` pada panel modal — tinggi harus mengikuti konten dengan batas `max-h`.
- Di desktop, panel modal WAJIB menyisakan ruang di kiri, kanan, atas, dan bawah layar.
  Gunakan `sm:p-4` atau `sm:p-6` pada wrapper container backdrop agar ada breathing room.
- Di mobile, bottom-sheet boleh memenuhi lebar layar (`w-full`) tapi TIDAK boleh memenuhi tinggi layar —
  gunakan `max-h-[90dvh]` sehingga bagian atas layar tetap terlihat (backdrop terlihat, user tahu ada modal).
- Jika konten modal terasa "butuh lebih banyak ruang", itu sinyal bahwa konten tersebut seharusnya
  menjadi **halaman tersendiri** (dengan route baru), bukan modal yang diperbesar.

**Contoh BENAR vs SALAH:**
```html
{{-- SALAH: panel modal full-screen --}}
<div class="fixed inset-0 z-50 bg-white">...</div>

{{-- SALAH: modal terlalu besar di desktop --}}
<div class="w-full sm:max-w-6xl bg-white rounded-xl ...">...</div>

{{-- SALAH: tinggi modal memenuhi layar --}}
<div class="w-full h-screen sm:max-w-lg bg-white ...">...</div>

{{-- BENAR: modal dengan ruang di sekeliling, tinggi mengikuti konten --}}
<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-6">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
    {{-- max-w-4xl = safety cap untuk semua ukuran layar; sm:max-w-lg = ukuran desktop final --}}
    <div class="relative z-10 w-full max-w-4xl sm:max-w-lg bg-white rounded-t-2xl sm:rounded-xl
                shadow-xl flex flex-col max-h-[90dvh] sm:max-h-[85vh]">
        ...
    </div>
</div>
```

**Aturan Mobile (bottom-sheet):**
- Di mobile (`<sm`): modal muncul dari bawah (`items-end`), `rounded-t-2xl`, lebar `w-full` (boleh).
- Di desktop (`sm:`): modal muncul di tengah (`sm:items-center`), `sm:rounded-xl`, lebar dibatasi `sm:max-w-*`.
- Panel modal WAJIB punya `max-w-4xl` (tanpa prefix) sebagai safety cap — ini mencegah modal full-screen pada layar besar (≥896px) jika breakpoint `sm:` tidak bekerja, dan konsisten di semua ukuran layar. Pola lengkap: `w-full max-w-4xl sm:max-w-*`.
- Body modal SELALU scrollable (`overflow-y-auto`) dengan `max-h-[90dvh] sm:max-h-[85vh]` — konten yang panjang scroll di dalam modal, bukan modal yang membesar tak terbatas.

### E.4 — Button Variants

Gunakan class-class ini secara konsisten di seluruh proyek:

```html
{{-- Primary (aksi utama: simpan, tambah) --}}
<button class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-[#3c50e0] text-white
               text-[14px] font-semibold hover:bg-[#2e3eb0] transition-colors cursor-pointer
               disabled:opacity-60 shadow-sm">

{{-- Secondary / Outline (aksi kedua: batal, kembali) --}}
<button class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg border border-[#c5c5d7]
               text-[14px] text-[#505f76] hover:bg-[#f0f4f8] transition-colors cursor-pointer">

{{-- Danger (hapus, nonaktifkan) --}}
<button class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-[#ba1a1a]
               text-[14px] text-[#ba1a1a] hover:bg-[#ffdad6] transition-colors cursor-pointer">

{{-- Ghost / Icon-only --}}
<button class="w-9 h-9 flex items-center justify-center rounded-lg text-[#505f76]
               hover:bg-[#f0f4f8] hover:text-on-surface transition-colors cursor-pointer">

{{-- Tabel action (kecil, untuk kolom aksi) --}}
<button class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[13px]
               hover:bg-[#f0f4f8] text-[#505f76] transition-colors cursor-pointer">
```

### E.5 — Card / Panel

```html
{{-- Card standar --}}
<div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm">
    {{-- Header card (opsional) --}}
    <div class="px-5 py-4 border-b border-[#c5c5d7] flex items-center justify-between">
        <h3 class="text-[15px] font-semibold text-on-surface">Judul</h3>
    </div>
    {{-- Body card --}}
    <div class="p-5">
        {{-- konten --}}
    </div>
</div>
```

- Border: SELALU `border border-[#c5c5d7]`
- Background: SELALU `bg-white`
- Radius: SELALU `rounded-xl`
- Shadow: SELALU `shadow-sm`
- Padding body: `p-5` (20px) atau `p-6` (24px) — konsisten dalam satu halaman.
- Padding header card: `px-5 py-4`

### E.6 — Input / Select / Textarea

```html
{{-- Input standar --}}
<input class="w-full px-4 py-2.5 border border-[#c5c5d7] rounded-lg text-[14px] text-on-surface
              bg-white focus:outline-none focus:ring-2 focus:ring-[#3c50e0]/30 focus:border-[#3c50e0]
              placeholder:text-[#b0b0c0] transition-colors">

{{-- Input dengan icon kiri --}}
<div class="relative">
    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2
                 text-[#505f76] text-[18px] pointer-events-none">search</span>
    <input class="w-full pl-10 pr-4 py-2.5 border border-[#c5c5d7] rounded-lg text-[14px] ...">
</div>

{{-- Select standar --}}
<select class="w-full px-4 py-2.5 border border-[#c5c5d7] rounded-lg text-[14px] text-on-surface
               bg-white focus:outline-none focus:ring-2 focus:ring-[#3c50e0]/30 focus:border-[#3c50e0]
               cursor-pointer appearance-none">
```

- Focus ring: SELALU `focus:ring-2 focus:ring-[#3c50e0]/30 focus:border-[#3c50e0]`
- Error state: ganti `border-[#c5c5d7]` dengan `border-[#ba1a1a]`
- Disabled: tambahkan `disabled:bg-[#f0f4f8] disabled:text-[#b0b0c0] disabled:cursor-not-allowed`

### E.7 — Badge / Chip Status

```html
{{-- Aktif / Terbuka / Sukses --}}
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[12px] font-semibold
             bg-green-100 text-green-800 border border-green-200">
    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Aktif
</span>

{{-- Menunggu / Draft / Warning --}}
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[12px] font-semibold
             bg-amber-100 text-amber-800 border border-amber-200">

{{-- Nonaktif / Ditolak / Error --}}
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[12px] font-semibold
             bg-[#ffdad6] text-[#93000a] border border-[#ba1a1a]/20">

{{-- Netral / Info --}}
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[12px] font-semibold
             bg-[#f0f4f8] text-[#505f76] border border-[#c5c5d7]">
```

### E.8 — Spacing: Jarak Antar Komponen & Button

> Spacing yang buruk adalah penyebab UI terasa "padat" dan tidak nyaman — terutama di mobile.
> Ikuti aturan ini pada SETIAP komponen, bukan hanya yang baru.

**Jarak dalam form (di dalam body modal atau form halaman):**

```html
{{-- Gunakan space-y-4 untuk jarak antar field standar --}}
<div class="p-5 space-y-4">
    <div>
        <label class="block text-[13px] font-medium text-on-surface mb-1.5">Label</label>
        <input class="w-full ...">
    </div>
    <div>
        <label class="block text-[13px] font-medium text-on-surface mb-1.5">Label 2</label>
        <input class="w-full ...">
    </div>
</div>
```

- Jarak antar field dalam form: `space-y-4` (16px) — standar.
- Jarak label ke input di bawahnya: `mb-1.5` (6px) — jangan `mb-1` (terlalu rapat) atau `mb-3` (terlalu jauh).
- Jarak antar section dalam form (misal: "Informasi Pribadi" ke "Informasi Kontak"): `space-y-6` atau `pt-4 border-t border-[#c5c5d7]`.
- Jarak antar field dalam satu baris (grid): `gap-4` (16px).

**Jarak button dengan elemen di atasnya:**

```html
{{-- SALAH: button langsung menempel pada input terakhir --}}
<div>
    <input class="w-full ...">
</div>
<button>Simpan</button>  {{-- tidak ada jarak --}}

{{-- BENAR: gunakan mt-6 untuk memisahkan button dari form --}}
<div>
    <input class="w-full ...">
</div>
<div class="mt-6 flex gap-3">
    <button>Simpan</button>
</div>
```

- Button submit setelah form terakhir: minimal `mt-6` (24px) — ini penting agar button tidak "menempel" pada input.
- Jika button ada di dalam `space-y-4`, tambahkan `pt-2` pada wrapper button untuk sedikit extra jarak.
- Pada halaman (bukan modal), button aksi yang berdiri sendiri (misal "Perbarui Jawaban", "Kirim Tugas") harus
  diberi `mt-6` atau `mt-8` dari konten di atasnya — jangan biarkan button terasa menyatu dengan form.

**Jarak antar tombol dalam satu kelompok:**

```html
{{-- Kelompok button: gunakan gap-3 (12px) --}}
<div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
    <button>Batal</button>
    <button>Simpan</button>
</div>
```

- Antar tombol dalam satu baris: `gap-3` (12px).
- Jangan `gap-1` atau `gap-2` — terlalu rapat untuk touch target di mobile.
- Jangan `gap-6` atau lebih — terasa terpisah.

**Jarak antar card / section di halaman:**

- Antar card utama di halaman: `mb-5` atau `space-y-5` (20px).
- Antar card di halaman yang lebih padat: `mb-4` atau `gap-4`.
- Jangan `mb-2` antar card — terlalu rapat.
- Header halaman ke konten pertama: `mb-5 sm:mb-6`.

**Padding dalam card:**

| Jenis card | Padding |
|-----------|---------|
| Card dengan header + body | Header: `px-5 py-4`, Body: `p-5` |
| Card sederhana (tanpa header) | `p-5` atau `p-6` |
| Card ringkas (misal stat card) | `p-4` atau `p-5` |
| Tabel dalam card | Header tabel: `px-5 py-3`, Cell: `px-5 py-3.5` |

> **Aturan konsistensi:** dalam satu halaman, pakai **satu ukuran padding** yang konsisten
> untuk semua card (misal semua `p-5`, atau semua `p-6`). Jangan campur `p-4`, `p-5`, dan
> `p-6` pada card yang sejajar tanpa alasan yang jelas.

### E.9 — Warna Referensi Cepat

| Token | Hex | Penggunaan |
|-------|-----|-----------|
| Primary | `#3c50e0` | CTA button, active nav, link, icon aksi |
| Primary Dark | `#2e3eb0` | Hover primary |
| On-Surface | `#171c1f` | Teks utama |
| Secondary text | `#505f76` | Teks sekunder, label |
| Tertiary text | `#757686` | Placeholder, hint, caption |
| Border | `#c5c5d7` | Border semua elemen |
| Background | `#F1F5F9` | Page background |
| Surface | `#f6fafe` | Card header, strip alternatif |
| Error | `#ba1a1a` | Tombol danger, border error, icon error |
| Error bg | `#ffdad6` | Background error ringan |

---

## Bagian F — Pola Mobile-First per Komponen

> Ini adalah **pola kode siap pakai** yang wajib diikuti saat membangun
> komponen baru. Setiap pola sudah dioptimalkan untuk mobile.

### F.1 — Page Header (Judul + Aksi)

```html
{{-- Header halaman: stack di mobile, inline di sm: --}}
<div class="mb-5 sm:mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-[20px] sm:text-[24px] font-bold tracking-tight text-on-surface">
                Judul Halaman
            </h2>
            <p class="text-[13px] sm:text-[14px] text-[#505f76] mt-0.5">
                Deskripsi singkat halaman ini.
            </p>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
            <button class="w-full sm:w-auto inline-flex items-center justify-center gap-2
                           px-5 py-2.5 rounded-lg bg-[#3c50e0] text-white text-[14px]
                           font-semibold hover:bg-[#2e3eb0] transition-colors cursor-pointer shadow-sm">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Tambah
            </button>
        </div>
    </div>
</div>
```

### F.2 — Search + Filter Bar

```html
<div class="flex flex-col sm:flex-row gap-3 mb-4">
    {{-- Search --}}
    <div class="relative flex-1">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2
                     text-[#757686] text-[18px] pointer-events-none">search</span>
        <input wire:model.live.debounce.300ms="search" type="text"
               placeholder="Cari..."
               class="w-full pl-10 pr-4 py-2.5 border border-[#c5c5d7] rounded-lg
                      text-[14px] bg-white focus:outline-none focus:ring-2
                      focus:ring-[#3c50e0]/30 focus:border-[#3c50e0] transition-colors">
    </div>

    {{-- Filter Dropdown (jika ada) --}}
    <select wire:model.live="filterStatus"
            class="sm:w-48 px-4 py-2.5 border border-[#c5c5d7] rounded-lg text-[14px]
                   bg-white focus:outline-none focus:ring-2 focus:ring-[#3c50e0]/30
                   focus:border-[#3c50e0] cursor-pointer">
        <option value="">Semua Status</option>
        <option value="aktif">Aktif</option>
        <option value="nonaktif">Nonaktif</option>
    </select>

    {{-- Per-page Selector --}}
    <select wire:model.live="perPage"
            class="sm:w-32 px-4 py-2.5 border border-[#c5c5d7] rounded-lg text-[14px]
                   bg-white focus:outline-none focus:ring-2 focus:ring-[#3c50e0]/30
                   focus:border-[#3c50e0] cursor-pointer">
        <option value="10">10 / hal</option>
        <option value="25">25 / hal</option>
        <option value="50">50 / hal</option>
    </select>
</div>
```

### F.3 — Tabel Data dengan Pagination

```html
<div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm overflow-hidden">

    {{-- Tabel: selalu overflow-x-auto --}}
    <div class="overflow-x-auto" wire:loading.class="opacity-50">
        <table class="w-full min-w-[600px]">
            <thead class="bg-[#f6fafe] border-b border-[#c5c5d7]">
                <tr>
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-[#505f76]
                               uppercase tracking-wider whitespace-nowrap">Nama</th>
                    {{-- Kolom yang disembunyikan di mobile: --}}
                    <th class="hidden sm:table-cell text-left px-5 py-3 text-[11px] font-semibold
                               text-[#505f76] uppercase tracking-wider">Detail</th>
                    <th class="text-right px-5 py-3 text-[11px] font-semibold text-[#505f76]
                               uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#f0f4f8]">
                @forelse ($items as $item)
                    <tr class="hover:bg-[#f6fafe] transition-colors">
                        <td class="px-5 py-3.5 text-[14px] text-on-surface">{{ $item->nama }}</td>
                        <td class="hidden sm:table-cell px-5 py-3.5 text-[14px] text-[#505f76]">
                            {{ $item->detail }}
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <button wire:click="edit({{ $item->id }})"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg
                                               text-[#505f76] hover:bg-[#f0f4f8] transition-colors cursor-pointer"
                                        title="Edit">
                                    <span class="material-symbols-outlined text-[17px]">edit</span>
                                </button>
                                <button wire:click="confirmDelete({{ $item->id }})"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg
                                               text-[#ba1a1a] hover:bg-[#ffdad6] transition-colors cursor-pointer"
                                        title="Hapus">
                                    <span class="material-symbols-outlined text-[17px]">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="99" class="px-5 py-14 text-center">
                            <span class="material-symbols-outlined text-[44px] text-[#c5c5d7] block mb-2">
                                inbox
                            </span>
                            <p class="text-[14px] font-medium text-on-surface">Belum ada data</p>
                            <p class="text-[13px] text-[#505f76] mt-1">Mulai dengan menambah data baru.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if ($items->hasPages())
        <div class="px-5 py-3.5 border-t border-[#c5c5d7] flex flex-col sm:flex-row
                    sm:items-center sm:justify-between gap-3">
            <p class="text-[13px] text-[#505f76]">
                Menampilkan {{ $items->firstItem() }}–{{ $items->lastItem() }}
                dari {{ $items->total() }} data
            </p>
            <div>{{ $items->links() }}</div>
        </div>
    @endif

</div>
```

### F.4 — Stat Card Grid (Dashboard)

```html
{{-- 1 kolom di mobile, 2 di sm, 4 di lg --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-[13px] font-medium text-[#505f76]">Label</p>
            <div class="w-9 h-9 rounded-lg bg-[#EEF2FF] flex items-center justify-center">
                <span class="material-symbols-outlined text-[#3c50e0] text-[18px]">group</span>
            </div>
        </div>
        <p class="text-[28px] font-bold text-on-surface leading-none">42</p>
        <p class="text-[12px] text-[#505f76] mt-1.5">Sub-label</p>
    </div>
</div>
```

### F.5 — Modal Konfirmasi Hapus (Pola Minimal)

```html
@if ($confirmDeleteId)
<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
         wire:click="$set('confirmDeleteId', null)"></div>
    <div class="relative z-10 w-full sm:max-w-sm bg-white rounded-t-2xl sm:rounded-xl
                shadow-xl shadow-black/10 p-6">
        <div class="flex items-start gap-4 mb-5">
            <div class="w-11 h-11 rounded-full bg-[#ffdad6] flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-[#ba1a1a] text-[22px]">delete</span>
            </div>
            <div>
                <h4 class="text-[16px] font-semibold text-on-surface">Hapus data ini?</h4>
                <p class="text-[13px] text-[#505f76] mt-1">Tindakan ini tidak dapat dibatalkan.</p>
            </div>
        </div>
        <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
            <button type="button" wire:click="$set('confirmDeleteId', null)"
                    class="w-full sm:w-auto px-5 py-2.5 rounded-lg border border-[#c5c5d7]
                           text-[14px] text-[#505f76] hover:bg-[#f0f4f8] transition-colors cursor-pointer">
                Batal
            </button>
            <button type="button" wire:click="hapus"
                    wire:loading.attr="disabled"
                    class="w-full sm:w-auto px-5 py-2.5 rounded-lg bg-[#ba1a1a] text-white
                           text-[14px] font-semibold hover:bg-[#93000a] transition-colors cursor-pointer
                           disabled:opacity-60">
                Ya, Hapus
            </button>
        </div>
    </div>
</div>
@endif
```

### F.6 — Pola Halaman dengan Form Edit (Inline, Bukan Modal)

Untuk form yang terlalu panjang untuk modal:

```html
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    {{-- Panel utama (2/3 lebar di desktop) --}}
    <div class="lg:col-span-2 space-y-5">
        <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm">
            <div class="px-5 py-4 border-b border-[#c5c5d7]">
                <h3 class="text-[15px] font-semibold text-on-surface">Informasi Utama</h3>
            </div>
            <div class="p-5 space-y-4">
                {{-- field-field form --}}
            </div>
        </div>
    </div>

    {{-- Sidebar panel (1/3 di desktop, di bawah di mobile) --}}
    <div class="space-y-4">
        <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm">
            <div class="px-5 py-4 border-b border-[#c5c5d7]">
                <h3 class="text-[15px] font-semibold text-on-surface">Status & Aksi</h3>
            </div>
            <div class="p-5 space-y-3">
                <button class="w-full px-5 py-2.5 rounded-lg bg-[#3c50e0] text-white text-[14px]
                               font-semibold hover:bg-[#2e3eb0] transition-colors cursor-pointer">
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>
```

### F.7 — Info Banner / Alert

```html
{{-- Info (biru) --}}
<div class="flex items-start gap-3 p-4 rounded-lg bg-[#EEF2FF] border border-[#c5d0ff]">
    <span class="material-symbols-outlined text-[#3c50e0] text-[20px] flex-shrink-0 mt-0.5">info</span>
    <p class="text-[13px] text-[#3c50e0]">Pesan info.</p>
</div>

{{-- Warning (kuning) --}}
<div class="flex items-start gap-3 p-4 rounded-lg bg-amber-50 border border-amber-200">
    <span class="material-symbols-outlined text-amber-600 text-[20px] flex-shrink-0 mt-0.5">warning</span>
    <p class="text-[13px] text-amber-800">Pesan peringatan.</p>
</div>

{{-- Error (merah) --}}
<div class="flex items-start gap-3 p-4 rounded-lg bg-[#ffdad6] border border-[#ba1a1a]/20">
    <span class="material-symbols-outlined text-[#ba1a1a] text-[20px] flex-shrink-0 mt-0.5">error</span>
    <p class="text-[13px] text-[#93000a]">Pesan error.</p>
</div>
```

---

## Bagian G — Ringkasan Aturan Tidak Boleh Dilanggar

### Visual & Konsistensi
1. **Modal: WAJIB** `backdrop-blur-sm`, `rounded-t-2xl sm:rounded-xl`, `items-end sm:items-center` (bottom-sheet mobile).
2. **Modal DILARANG full-screen** — tidak boleh ada `w-screen`, `h-screen`, `min-h-screen`, `h-full`, `max-w-full`
   pada panel modal. Di mobile, lebar `w-full` boleh untuk bottom-sheet, tapi WAJIB ditambah `max-w-4xl` sebagai
   safety cap. Pola wajib panel: `w-full max-w-4xl sm:max-w-*`. Tinggi SELALU dibatasi `max-h-[90dvh] sm:max-h-[85vh]`.
   Di desktop, lebar dan tinggi KEDUANYA harus dibatasi. Jika konten terlalu besar untuk modal → buat halaman baru.
3. **Modal ukuran di desktop: JANGAN pernah** `max-w-5xl` ke atas. Maksimum `sm:max-w-4xl`, hanya untuk editor. Lihat tabel di E.3.
4. **Modal dengan sedikit field: WAJIB kecil** — modal ganti password (2 field) → `sm:max-w-md`, modal konfirmasi → `sm:max-w-sm`.
5. **Desktop: modal SELALU menyisakan ruang** di sekeliling — gunakan `sm:p-4` atau `sm:p-6` pada container backdrop.
5. **Button: SELALU** `cursor-pointer` dan `py-2.5` (touch target ≥44px).
6. **Card: SELALU** `bg-white rounded-xl border border-[#c5c5d7] shadow-sm`.
7. **Border-radius konsisten:** modal `rounded-xl`, card `rounded-xl`, button `rounded-lg`, input `rounded-lg` —
   jangan campur `rounded-md` dan `rounded-xl` dalam satu halaman.
8. **Focus ring: WAJIB** `focus:ring-2 focus:ring-[#3c50e0]/30 focus:border-[#3c50e0]`.
9. **Cursor pointer:** SELALU pada button, a, select, label checkbox, dan elemen interaktif apapun.

### Spacing & Jarak
10. **Jarak button dengan form:** button submit WAJIB diberi `mt-6` dari field terakhir — jangan biarkan button menempel langsung.
11. **Jarak antar field dalam form:** gunakan `space-y-4` — jangan kurang dari itu.
12. **Label ke input:** `mb-1.5` — tidak boleh `mb-0` atau `mb-3`.
13. **Antar tombol dalam satu baris:** `gap-3` — tidak boleh `gap-1` (terlalu rapat) atau `gap-6` (terlalu jauh).
14. **Antar card di halaman:** minimal `mb-5` atau `space-y-5`.
15. **Padding card konsisten dalam satu halaman:** pilih satu (`p-5` atau `p-6`) dan pakai seragam.

### Mobile-First
16. **Tabel: SELALU** dibungkus `overflow-x-auto`.
17. **Kolom tabel sekunder: WAJIB** `hidden sm:table-cell`.
18. **Form grid: WAJIB** `grid-cols-1 sm:grid-cols-2` (stack di mobile).
19. **Button aksi utama di mobile: WAJIB** `w-full sm:w-auto`.
20. **Kelompok button di footer: WAJIB** `flex-col-reverse sm:flex-row` (Batal di bawah di mobile).
21. **State kosong: WAJIB ada** di setiap list/tabel — jangan biarkan area kosong tanpa pesan.
22. **Layout sidebar:** jangan buat sidebar duplikat — pakai yang sudah ada di `app.blade.php`.
23. **Page title:** WAJIB isi prop `pageTitle` di setiap halaman.
24. **Mobile first:** improvisasi dari referensi sangat diperlukan — referensi desain tidak selalu mobile-ready, wajib disesuaikan.

### Badge & Status Chip (Mobile-safe)
25. **Badge dengan dot/icon: WAJIB** `whitespace-nowrap` pada wrapper `<span>` dan `flex-shrink-0` pada dot/icon-nya. Ini mencegah badge "pecah" di layar sempit. Contoh:
    ```html
    <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full bg-red-50 text-red-600 border border-red-200 whitespace-nowrap">
      <span class="w-1.5 h-1.5 rounded-full bg-red-500 flex-shrink-0 inline-block"></span> Lewat Tenggat
    </span>
    ```

### Topbar / Header Halaman (layout template)
26. **Header halaman MINIMAL** — hanya breadcrumb + hamburger. Jangan letakkan search global, notifikasi, atau avatar di header halaman individual karena itu akan diatur oleh layout template. Pengecualian: tombol aksi kontekstual singkat (`Ekspor CSV`, `Cetak`) boleh ada di header.

---

## Bagian H — Pola Komponen Khusus Peran (7.x–11.x)

### H.1 — GMR Selector (Guru: Pilih Mapel & Rombel)

Guru dapat mengampu lebih dari satu mapel/rombel. Sebelum menampilkan konten, tampilkan selector ini di bagian atas halaman:

```html
<div class="bg-white border border-[#c5c5d7] rounded-xl px-4 py-4 mb-4">
  <p class="text-xs font-semibold text-[#505f76] uppercase tracking-wide mb-3">Pilih Konteks</p>
  <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
    <div>
      <label class="block text-xs font-medium text-[#505f76] mb-1">Mata Pelajaran</label>
      <select class="w-full px-3 py-2 border border-[#c5c5d7] rounded-lg text-sm bg-white focus:outline-none focus:border-[#3c50e0] focus:ring-1 focus:ring-[#3c50e0]">
        <option>Matematika</option>
      </select>
    </div>
    <div>
      <label class="block text-xs font-medium text-[#505f76] mb-1">Rombel</label>
      <select class="w-full px-3 py-2 border border-[#c5c5d7] rounded-lg text-sm bg-white focus:outline-none focus:border-[#3c50e0] focus:ring-1 focus:ring-[#3c50e0]">
        <option>Kelas 10 Botolambat</option>
      </select>
    </div>
  </div>
</div>
```

Setelah GMR dipilih, tampilkan **context info banner** di bawahnya:
```html
<div class="bg-[#EEF2FF] border border-[#3c50e0]/20 rounded-xl px-4 py-3 flex items-center gap-2.5 text-sm text-[#505f76] mb-4">
  <span class="material-symbols-outlined text-[18px] text-[#3c50e0] flex-shrink-0">info</span>
  <p>Menampilkan data untuk <strong class="text-[#171c1f]">Matematika</strong> · <strong class="text-[#171c1f]">Kelas 10 Botolambat</strong></p>
</div>
```

### H.2 — Bottom Nav (Peserta Didik, Mobile)

Peserta Didik memiliki bottom navigation yang muncul di mobile (`lg:hidden`). Selalu 5 item:

```html
<nav class="fixed bottom-0 left-0 right-0 z-30 bg-white border-t border-[#c5c5d7] lg:hidden">
  <div class="grid grid-cols-5 h-14">
    <a href="#" class="flex flex-col items-center justify-center gap-0.5 text-[#3c50e0]">
      <span class="material-symbols-outlined text-[22px]" style="font-variation-settings:'FILL' 1">home</span>
      <span class="text-[10px] font-medium">Beranda</span>
    </a>
    <a href="#" class="flex flex-col items-center justify-center gap-0.5 text-[#757686]">
      <span class="material-symbols-outlined text-[22px]">menu_book</span>
      <span class="text-[10px]">Materi</span>
    </a>
    <a href="#" class="flex flex-col items-center justify-center gap-0.5 text-[#757686]">
      <span class="material-symbols-outlined text-[22px]">assignment</span>
      <span class="text-[10px]">Tugas</span>
    </a>
    <a href="#" class="flex flex-col items-center justify-center gap-0.5 text-[#757686]">
      <span class="material-symbols-outlined text-[22px]">quiz</span>
      <span class="text-[10px]">CBT</span>
    </a>
    <a href="#" class="flex flex-col items-center justify-center gap-0.5 text-[#757686]">
      <span class="material-symbols-outlined text-[22px]">person</span>
      <span class="text-[10px]">Profil</span>
    </a>
  </div>
</nav>
```

- Halaman yang punya bottom nav: WAJIB tambahkan `pb-14` pada `<main>` agar konten tidak tertutup.
- Item aktif: warna `#3c50e0`, icon `FILL 1`. Item tidak aktif: warna `#757686`, icon `FILL 0`.

### H.3 — Split Panel (Koreksi / Assign)

Digunakan di 9.4 (koreksi uraian) dan 10.1 (assign rombel). Pola:

```html
<div class="flex-1 flex overflow-hidden">
  <!-- Panel kiri: daftar/navigasi (fixed width) -->
  <div class="w-64 flex-shrink-0 border-r border-[#c5c5d7] bg-white flex flex-col overflow-hidden hidden lg:flex">
    <!-- header panel -->
    <div class="px-4 py-3 border-b border-[#c5c5d7] bg-[#f0f4f8]">
      <p class="text-xs font-semibold text-[#505f76] uppercase tracking-wide">Judul Panel</p>
    </div>
    <!-- list scrollable -->
    <div class="flex-1 overflow-y-auto">
      <!-- item aktif -->
      <button class="w-full text-left px-3 py-3 flex items-center justify-between bg-[#EEF2FF] border-l-4 border-[#3c50e0]">
        ...
      </button>
    </div>
  </div>

  <!-- Panel kanan: konten utama (scrollable) -->
  <div class="flex-1 overflow-y-auto p-4 lg:p-5 space-y-4">
    ...
  </div>
</div>
```

- Di mobile (`<lg`): panel kiri disembunyikan (`hidden lg:flex`). Panel kanan mengisi seluruh lebar.
- Progress/konteks panel: letakkan di dalam panel kiri (di header panel), bukan di topbar.

### H.4 — Accordion Konten (Peserta Didik)

Untuk instruksi tugas atau keterangan panjang yang bisa dilipat:

```html
<div class="bg-white border border-[#c5c5d7] rounded-xl overflow-hidden">
  <button onclick="toggleAcc()" class="w-full flex items-center justify-between px-4 py-3 hover:bg-[#f6fafe] transition-colors">
    <span class="text-sm font-semibold">Instruksi Tugas</span>
    <span class="material-symbols-outlined text-[20px] text-[#505f76]" id="acc-icon">expand_less</span>
  </button>
  <div id="acc-body" class="px-4 pb-4 text-sm text-[#505f76] leading-relaxed">
    <!-- konten instruksi -->
  </div>
</div>
<script>
function toggleAcc() {
  const body = document.getElementById('acc-body');
  const icon = document.getElementById('acc-icon');
  const hidden = body.classList.toggle('hidden');
  icon.textContent = hidden ? 'expand_more' : 'expand_less';
}
</script>
```

- Default: **terbuka** (body tidak punya class `hidden` saat render pertama).
- Icon: `expand_less` saat terbuka, `expand_more` saat tertutup.

### H.5 — CBT: Timer, Option Cards, Nav Grid

**Timer (Alpine.js / vanilla JS):**
```js
let total = 90 * 60; // detik
function tick() {
  const m = String(Math.floor(total / 60)).padStart(2, '0');
  const s = String(total % 60).padStart(2, '0');
  document.getElementById('timer').textContent = m + ':' + s;
  if (total <= 300) document.getElementById('timer').classList.add('text-red-500');
  if (total > 0) total--;
}
tick(); setInterval(tick, 1000);
```

**Option cards (PG):**
```html
<div class="opt-card ..." onclick="selectOpt(this, 'A')">A. Pilihan A</div>
<style>
  .opt-card { ... border: 2px solid #c5c5d7; cursor: pointer; }
  .opt-card.selected { border-color: #3c50e0; background: #EEF2FF; }
</style>
<script>
function selectOpt(el, val) {
  document.querySelectorAll('.opt-card').forEach(c => c.classList.remove('selected'));
  el.classList.add('selected');
}
</script>
```

**Nav grid soal (desktop sidebar):**
```html
<div class="grid grid-cols-5 gap-1.5">
  <button class="nav-q answered">1</button>
  <button class="nav-q current">2</button>
  <button class="nav-q">3</button>
</div>
<style>
  .nav-q { width:32px; height:32px; border-radius:6px; font-size:12px; border:1.5px solid #c5c5d7; background:#fff; cursor:pointer; }
  .nav-q.answered { background:#3c50e0; color:#fff; border-color:#3c50e0; }
  .nav-q.current { border-color:#3c50e0; color:#3c50e0; font-weight:700; }
</style>
```

### H.6 — Preview Cetak (Rapor PDF)

- Dokumen A4 dibungkus `overflow-x-auto` + `min-width:560px` agar bisa scroll di mobile.
- Tambahkan banner mobile (`lg:hidden`) yang menyarankan Desktop Site.
- FAB "Cetak PDF" menggunakan `window.print()` dan `sticky top-0`.
- `@media print`: sembunyikan sidebar, header, FAB, overlay. Hilangkan shadow/border pada `.a4-doc`.
- Kop surat: logo placeholder dengan `<span class="material-symbols-outlined">` — akan diganti gambar nyata saat implementasi.

### H.7 — Kenaikan Kelas: Dropdown Warna Dinamis

Dropdown keputusan per PD mengubah warna border/bg mengikuti pilihan:
- Belum dipilih: `border-[#c5c5d7]`, `text-[#505f76]`
- Naik Tingkat: `border-[#3c50e0] bg-[#EEF2FF] text-[#3c50e0]`
- Lulus: `border-green-400 bg-green-50 text-green-700`
- Tinggal: `border-amber-400 bg-amber-50 text-amber-700`
- Pindah: `border-purple-300 bg-purple-50 text-purple-700`

Implementasikan via `wire:change` + PHP property yang memetakan keputusan → class Tailwind (gunakan `@class` blade directive).

### H.8 — Halaman Login

- Background: `bg-white` (putih bersih), bukan gradient.
- Card: shadow `box-shadow: 0 24px 64px -12px rgba(28,51,200,0.4)`, header card gradient biru tetap dipertahankan.
- Footer copyright: gunakan `date('Y')` di PHP (atau `new Date().getFullYear()` di JS) — jangan hardcode tahun.
- Decoration circles: subtle, opacity 4–5% warna primary, bukan putih.

---

## Bagian I — Akses per Role pada Fitur

| Halaman/Fitur | Admin | Guru | Wali Kelas | Peserta Didik |
|---------------|-------|------|------------|---------------|
| Rekap Absensi (6.3) | ✅ Semua rombel | ✅ Rombel/mapel yg diampu | ✅ Rombel walinya | ❌ |
| Materi (7.x) | ✅ | ✅ Kelola | ❌ | ✅ Lihat saja |
| Tugas (8.x) | ✅ | ✅ Kelola + nilai | ❌ | ✅ Submit |
| CBT (9.x) | ✅ | ✅ Buat + koreksi | ❌ | ✅ Kerjakan |
| Assign Rombel (10.1) | ✅ | ❌ | ❌ | ❌ |
| Kenaikan Kelas (10.2) | ✅ | ❌ | ✅ Rombel walinya | ❌ |
| Rapor (11.x) | ✅ | ✅ Input nilai mapelnya | ✅ Progres + terbitkan | ✅ Lihat rapor sendiri |

> Catatan: "Wali Kelas" bukan role tersendiri — ia adalah Guru dengan `wali_kelas_id` di tabel `rombel`. Permission kondisional dicek via Policy (`Auth::id() === rombel->wali_kelas_id`).
