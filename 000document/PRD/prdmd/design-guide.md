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

**Tips konsistensi:** simpan keputusan visual tetap (warna brand, font, radius,
spacing) di file ini, supaya tiap sesi Claude Code memakai token yang sama dan
hasil antar-halaman tidak "belang". Contoh:

```
Warna primer  : #... (ambil dari TailAdmin / brand lembaga)
Font          : Inter / sesuai TailAdmin
Sidebar       : layout TailAdmin "default", item menu per-role
Komponen tabel: gunakan style tabel TailAdmin (bukan tabel polosan)
```

---

## Bagian B — Layout pakai Livewire `$slot`

Di Livewire 3, satu komponen full-page dibungkus oleh **layout component** yang
punya `{{ $slot }}`. Isi halaman dari tiap komponen Livewire "masuk" ke posisi
`$slot`. Anda menulis sidebar + topbar TailAdmin **sekali** di layout, dan semua
halaman otomatis ikut.

### 1. Buat layout (sekali saja)
`resources/views/components/layouts/app.blade.php`:

```blade
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'LMS Padamu Negeri' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        {{-- Sidebar TailAdmin: menu menyesuaikan role --}}
        <x-sidebar />

        <div class="flex-1 flex flex-col overflow-hidden">
            <x-topbar />

            <main class="flex-1 overflow-y-auto p-6">
                {{ $slot }}   {{-- ← isi tiap halaman Livewire muncul di sini --}}
            </main>
        </div>
    </div>
    @livewireScripts
</body>
</html>
```

### 2. Komponen halaman tinggal "menumpang" layout itu

```php
// app/Livewire/Admin/MasterData/WilayahManager.php
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
#[Title('Master Data — Wilayah')]
class WilayahManager extends Component
{
    public function render()
    {
        return view('livewire.admin.master-data.wilayah-manager');
        // view ini HANYA berisi konten utama (tabel + form),
        // TANPA sidebar/topbar — itu sudah ditangani $slot di layout.
    }
}
```

> Karena PRD mengunci **Multi-File Component**: class PHP dan file blade dipisah
> (seperti contoh di atas) — jangan pakai single-file/Volt.

### 3. Slot bernama (opsional, untuk area ekstra)
Kalau butuh menyuntik konten ke beberapa area (mis. judul header berbeda per halaman),
pakai named slot: di layout `{{ $header ?? '' }}`, di halaman `<x-slot:header>...</x-slot:header>`.

---

## Bagian C — Jika Hasil Export Stitch Hanya Berupa Gambar (Bukan Kode Asli)

Kadang hasil export/screenshot dari Stitch yang Anda taruh di
`design-reference/` **bukan kode HTML/Tailwind sungguhan**, melainkan gambar
statis (PNG/JPG dari hasil render Stitch, atau HTML yang isinya cuma
`<img src="...">` satu file besar). Ini terjadi kalau Anda screenshot manual
alih-alih memakai fitur export kode di Stitch.

**Cara menanganinya di Claude Code:**

1. **Jangan pernah suruh Claude Code "menyalin" gambar itu jadi HTML 1:1
   secara visual semata.** Itu menghasilkan markup yang rapuh (posisi absolut,
   ukuran fixed-pixel) dan tidak responsif.
2. Perintahkan eksplisit, contoh:
   *"Gambar di `design-reference/dashboard-admin.png` ini hasil render Stitch,
   bukan kode asli. Bangun ulang sebagai komponen Livewire + TailAdmin yang
   MENERJEMAHKAN maksud layout-nya (posisi sidebar, urutan card, struktur
   tabel) — bukan meniru piksel persis. Pastikan hasilnya responsive dan
   pakai komponen TailAdmin yang sudah ada di proyek, jangan styling baru
   dari nol."*
3. Jika gambar terlalu rumit untuk dideskripsikan lewat teks, **screenshot
   ulang per-bagian** (mis. crop khusus bagian sidebar, crop khusus bagian
   tabel) supaya Claude Code bisa fokus menerjemahkan satu bagian dengan
   akurat, lalu digabung.
4. Selalu re-export dari Stitch sebagai **kode (HTML/Tailwind/JSX)**, bukan
   gambar, setiap kali memungkinkan — ini jauh lebih akurat untuk
   diterjemahkan Claude Code dibanding gambar. Gambar hanya untuk kasus
   darurat saat fitur export kode tidak tersedia/gagal.

## Bagian D — Standar Wajib: Responsive & User-Friendly

Setiap kali Claude Code membangun atau mengonversi tampilan dari referensi
desain (Stitch, screenshot, atau mockup apapun), terapkan aturan berikut
**tanpa perlu diminta ulang setiap saat** — ini standar baku proyek:

- **Mobile-first / breakpoint Tailwind wajib dipakai** (`sm:`, `md:`, `lg:`,
  `xl:`), bukan ukuran fixed-pixel. Sidebar yang di desktop tampil penuh harus
  collapse jadi hamburger menu di layar kecil (`<lg`).
- **Tabel data lebar** (mis. rekap nilai, hasil CBT) di layar kecil: bungkus
  dengan `overflow-x-auto`, jangan biarkan tabel memaksa lebar halaman melebar.
- **Form & modal** harus tetap nyaman dipakai di layar sempit — input full
  width di mobile, padding cukup, tombol mudah disentuh (minimal area klik
  ~44px tinggi sesuai prinsip touch-target).
- **Kontras warna & ukuran teks** harus terbaca jelas (teks badan minimal
  setara `text-sm`/14px, jangan lebih kecil untuk konten penting).
- **State kosong & loading wajib ada**, bukan tampilan kosong membingungkan:
  tampilkan pesan seperti "Belum ada data" dengan ilustrasi/ikon sederhana
  saat tabel/list kosong, dan indikator loading (spinner Livewire bawaan
  `wire:loading`) saat data sedang diproses.
- **Konfirmasi untuk aksi merusak** (hapus, reset password, nonaktifkan
  modul) selalu pakai modal konfirmasi — jangan langsung eksekusi dari satu
  klik tombol tanpa jeda.
- Jika ragu antara "mengikuti referensi desain persis" vs "membuat lebih
  rapi & mudah dipakai", **utamakan yang lebih mudah dipakai** — referensi
  desain (termasuk hasil Stitch) adalah panduan arah, bukan kontrak yang
  harus ditiru piksel demi piksel.

---


- Layout utama: 1 file `app.blade.php` + `$slot` + `<x-sidebar/>` & `<x-topbar/>`.
- Layout login terpisah: `guest.blade.php` (tanpa sidebar).
- Sidebar render menu sesuai role (Admin/Guru/Peserta Didik) — cek role via Spatie.
- Sumber komponen UI = TailAdmin. Stitch hanya untuk wireframe → export ke `design-reference/`.