# Setup Claude Code: GitHub Issue + Termius (VPS)

Panduan lengkap agar Claude bisa dijalankan dari HP tanpa laptop —
lewat GitHub Issue atau terminal Termius.

---

## BAGIAN 1 — Claude via GitHub Issue

> Dikerjakan **sekali dari laptop** saja.

### Langkah 1.1 — Pastikan project sudah di GitHub

Kalau belum di-push:

```bash
cd web-lms-padamu-negeri
git init
git add .
git commit -m "feat: initial commit"
git branch -M main
git remote add origin https://github.com/username/web-lms-padamu-negeri.git
git push -u origin main
```

Kalau sudah ada di GitHub, skip ke 1.2.

---

### Langkah 1.2 — Install GitHub App Claude Code

Buka terminal di folder project, jalankan Claude Code:

```bash
claude
```

Di dalam sesi Claude Code, ketik:

```
/install-github-app
```

Ikuti semua langkah yang muncul — Claude Code akan memandu Anda untuk:
- Login ke GitHub
- Pilih repo `web-lms-padamu-negeri`
- Membuat file `.github/workflows/claude.yml` otomatis
- Menyimpan `ANTHROPIC_API_KEY` sebagai secret di repo

Setelah selesai, keluar dari sesi:

```
/exit
```

---

### Langkah 1.3 — Commit file workflow yang dibuat

```bash
git add .github/
git commit -m "feat: setup claude github action"
git push origin main
```

---

### Langkah 1.4 — Verifikasi workflow aktif

Buka GitHub di browser → repo Anda → tab **Actions**.
Harus terlihat workflow baru bernama "Claude" atau "Claude Code".
Kalau sudah muncul, setup selesai.

---

### Langkah 1.5 — Tes dari HP

Buka app GitHub di HP → repo → tab **Issues** → **New issue**:

```
Judul: Test Claude

Isi:
@claude Cek apakah kamu bisa membaca CLAUDE.md dan docs/build-steps.md.
Ringkas isinya dalam 3 poin saja. Jangan tulis kode, jangan buat PR.
```

Submit. Tunggu 1-2 menit — Claude harusnya membalas langsung
di kolom komentar issue tersebut.

---

### Cara Pakai Harian — GitHub Issue

Buka GitHub app di HP → Issues → New Issue, contoh format:

```
Judul: Langkah 3 — Migration

Isi:
@claude Kerjakan Langkah 3 dari docs/build-steps.md (Semua Migration).
Buat satu file migration per tabel sesuai urutan dependency di docs/database.md.
Jalankan php artisan migrate dan laporkan hasilnya di komentar ini.
Setelah selesai buat Pull Request. Patuhi CLAUDE.md.
```

Claude kerja di background → buat PR → Anda review dan merge dari HP.

---

## BAGIAN 2 — Claude via Termius (SSH ke VPS)

> Bisa dikerjakan langsung dari Termius di HP setelah connect ke VPS.

### Langkah 2.1 — Connect ke VPS via Termius

Di HP, buka Termius → **New Host**:

- **Hostname:** IP VPS Anda
- **Username:** root
- **Password:** password VPS dari provider

Save → Connect.

---

### Langkah 2.2 — Install Node.js versi terbaru

Versi Node.js dari `apt` bawaan Ubuntu biasanya terlalu lama untuk Claude Code.
Install versi terbaru:

```bash
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt install -y nodejs
node --version
npm --version
```

Harus muncul Node 20.x atau lebih baru.

---

### Langkah 2.3 — Install Claude Code

```bash
npm install -g @anthropic-ai/claude-code
claude --version
```

Harus muncul versi Claude Code, bukan error.

---

### Langkah 2.4 — Install Git dan clone project

```bash
apt install -y git
cd /root
git clone https://github.com/username/web-lms-padamu-negeri.git
cd web-lms-padamu-negeri
```

Verifikasi file `.md` ada:

```bash
ls docs/
cat CLAUDE.md
```

Harus tampil isi `CLAUDE.md` yang sudah dibuat sebelumnya.

---

### Langkah 2.5 — Login Claude Code di VPS

```bash
claude
```

Akan muncul prompt login:

```
To login, open this URL in your browser:
https://claude.ai/oauth/...
```

Copy URL → buka di browser HP → login dengan akun Anthropic → izinkan akses.
Kembali ke Termius, sesi Claude Code akan otomatis masuk.

Tes singkat:

```
Baca CLAUDE.md dan sebutkan 3 aturan pertama yang ada di dalamnya.
```

Kalau Claude menjawab benar sesuai isi `CLAUDE.md`, login berhasil.
Keluar dari sesi interaktif:

```
/exit
```

---

### Langkah 2.6 — Buat shortcut script `lms`

```bash
nano /usr/local/bin/lms
```

Isi:

```bash
#!/bin/bash

# Cara pakai:
# lms "Kerjakan Langkah 3 dari docs/build-steps.md"

if [ -z "$1" ]; then
  echo "Usage: lms \"perintah untuk Claude\""
  exit 1
fi

cd /root/web-lms-padamu-negeri

echo ""
echo "========================================"
echo "Menjalankan Claude Code..."
echo "Perintah: $1"
echo "========================================"
echo ""

claude -p "$1. Patuhi semua aturan di CLAUDE.md dan docs/database.md. Berhenti setelah satu langkah selesai, jangan lanjut ke langkah berikutnya." \
  --dangerously-skip-permissions

echo ""
echo "========================================"
echo "Claude selesai. Commit & push hasil..."
echo "========================================"

git add -A
git diff --cached --name-only

if git diff --cached --quiet; then
  echo "Tidak ada perubahan untuk di-commit."
else
  git commit -m "feat: $(echo $1 | cut -c1-60)"
  git push origin main
  echo "Berhasil push ke GitHub."
fi

echo ""
echo "Selesai: $(date)"
```

Simpan (Ctrl+X → Y → Enter), lalu beri izin eksekusi:

```bash
chmod +x /usr/local/bin/lms
```

---

### Langkah 2.7 — Setup deploy key supaya bisa push tanpa password

Di VPS, generate SSH key:

```bash
ssh-keygen -t ed25519 -C "vps-lms" -f /root/.ssh/lms_key -N ""
cat /root/.ssh/lms_key.pub
```

Copy output (mulai dari `ssh-ed25519 ...`).

Buka GitHub di browser HP → repo → **Settings** → **Deploy keys** → **Add deploy key**:

- **Title:** VPS LMS
- **Key:** paste public key tadi
- **Allow write access:** centang **Ya**

Klik **Add key**. Kembali ke Termius:

```bash
nano /root/.ssh/config
```

Isi:

```
Host github.com
  HostName github.com
  User git
  IdentityFile /root/.ssh/lms_key
```

```bash
chmod 600 /root/.ssh/config
chmod 600 /root/.ssh/lms_key
```

Ubah remote URL project ke SSH:

```bash
cd /root/web-lms-padamu-negeri
git remote set-url origin git@github.com:username/web-lms-padamu-negeri.git
```

Tes pull (harus sukses tanpa diminta password):

```bash
git pull
```

---

### Langkah 2.8 — Tes dari Termius

```bash
lms "Baca docs/build-steps.md dan laporkan langkah mana saja yang sudah selesai berdasarkan file yang ada di project ini. Jangan buat atau ubah file apapun."
```

Claude akan membaca project, menganalisis, dan menjawab.
Karena tidak ada perubahan file, script otomatis skip bagian commit.

---

### Cara Pakai Harian — Termius

Dari Termius di HP, SSH ke VPS, lalu:

```bash
# Kerjakan satu langkah dari build-steps.md
lms "Kerjakan Langkah 4 dari docs/build-steps.md"

# Debug atau perbaiki error
lms "Ada error di migration users, cek dan perbaiki"

# Cek status project
lms "Laporkan langkah mana saja yang sudah selesai"
```

Setelah Claude selesai, kode otomatis di-push ke GitHub.

---

## Urutan Setup yang Disarankan

```
Hari ini (dari laptop):
1. Push project ke GitHub           → Bagian 1.1
2. /install-github-app di Claude    → Bagian 1.2 - 1.4
3. Tes issue pertama dari HP        → Bagian 1.5

Besok (dari Termius HP saja):
4. Install Node & Claude di VPS     → Bagian 2.2 - 2.3
5. Clone project, login Claude      → Bagian 2.4 - 2.5
6. Buat script lms                  → Bagian 2.6
7. Setup deploy key GitHub          → Bagian 2.7
8. Tes lms dari Termius             → Bagian 2.8
```

---

## Perbandingan Dua Jalur

| | GitHub Issue `@claude` | Termius `lms "..."` |
|---|---|---|
| Cocok untuk | Kerjakan langkah bertahap, review kode dulu sebelum live | Debug cepat, perintah singkat, hasil langsung |
| Review kode | Ya — via PR di GitHub app | Tidak — langsung push ke main |
| Butuh VPS? | Tidak | Ya |
| Lihat hasil web | Perlu setup VPS + webhook terpisah | Langsung di URL VPS |
| Trigger | Komentar issue GitHub | Command di Termius |

---

## Catatan Penting

- **`--dangerously-skip-permissions`** di script `lms` artinya Claude bisa edit file
  tanpa minta konfirmasi tiap aksi. Aman dipakai di VPS development, tapi jangan
  dipakai di server production dengan data nyata.
- **Deploy key** di GitHub hanya untuk repo ini saja — lebih aman dari personal
  access token yang berlaku untuk semua repo.
- Kalau Claude di VPS error karena sesi login expired, tinggal jalankan `claude`
  interaktif di Termius untuk login ulang, lalu keluar `/exit`, lanjut pakai `lms`
  seperti biasa.
