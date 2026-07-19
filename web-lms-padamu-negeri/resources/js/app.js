// ─────────────────────────────────────────────────────────────────────────────
// Pratinjau file global (gambar + PDF), dipakai semua peran & semua modul.
//
// Kenapa PDF.js dan bukan <iframe src="*.pdf">:
//   Browser mobile (Chrome Android / Safari iOS) TIDAK merender PDF di dalam
//   iframe — hasilnya kosong atau malah memicu unduhan, sehingga pratinjau
//   "tidak muncul". PDF.js merender PDF ke <canvas>, jadi jalan di semua
//   perangkat. Self-hosted → berkas sekolah tidak dikirim ke pihak ketiga.
//
// Dokumen Office (docx/xlsx/pptx) tidak bisa dirender browser tanpa layanan
// pihak ketiga → sengaja ditampilkan sebagai kartu "pratinjau tidak tersedia"
// beserta tombol Unduh (jujur, bukan pura-pura bisa).
//
// Pemakaian dari Blade mana pun:
//   $dispatch('open-file-preview', { url: '...', name: 'x.pdf' })
// ─────────────────────────────────────────────────────────────────────────────
import * as pdfjsLib from 'pdfjs-dist';
import pdfWorkerUrl from 'pdfjs-dist/build/pdf.worker.min.mjs?url';

// Vite plugin renameMjsToJs (di vite.config.js) me-rename .mjs → .js saat
// build, tapi URL di sini masih menunjuk .mjs. Kita ganti ekstensi di runtime
// agar browser request file .js yang benar (diterima semua web server).
const workerSrc = typeof pdfWorkerUrl === 'string'
    ? pdfWorkerUrl.replace(/\.mjs(\b|$)/, '.js')
    : pdfWorkerUrl;
pdfjsLib.GlobalWorkerOptions.workerSrc = workerSrc;

const IMAGE_EXT = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp', 'svg'];

document.addEventListener('alpine:init', () => {
    window.Alpine.data('filePreview', () => ({
        open: false,
        url: '',
        name: '',
        kind: 'other',   // 'image' | 'pdf' | 'other'
        loading: false,
        error: '',

        show(detail) {
            const payload = Array.isArray(detail) ? detail[0] : detail;
            if (!payload || !payload.url) return;

            this.url  = payload.url;
            this.name = payload.name || 'File';
            this.error = '';

            const ext = (payload.url.split('?')[0].split('.').pop() || '').toLowerCase();
            this.kind = ext === 'pdf' ? 'pdf' : (IMAGE_EXT.includes(ext) ? 'image' : 'other');

            this.open = true;
            document.body.style.overflow = 'hidden'; // kunci scroll latar (mobile)

            if (this.kind === 'pdf') {
                this.$nextTick(() => this.renderPdf());
            }
        },

        close() {
            this.open = false;
            this.url = '';
            this.error = '';
            document.body.style.overflow = '';
            const host = this.$refs.pdfHost;
            if (host) host.innerHTML = '';
        },

        async renderPdf() {
            const host = this.$refs.pdfHost;
            if (!host) return;

            host.innerHTML = '';
            this.loading = true;

            try {
                const pdf = await pdfjsLib.getDocument(this.url).promise;
                // Lebar render mengikuti lebar kontainer → tajam di mobile & desktop.
                const targetWidth = Math.min(host.clientWidth || 800, 1400);

                for (let n = 1; n <= pdf.numPages; n++) {
                    const page = await pdf.getPage(n);
                    const base = page.getViewport({ scale: 1 });
                    const scale = targetWidth / base.width;
                    const viewport = page.getViewport({ scale });

                    const canvas = document.createElement('canvas');
                    const ratio = window.devicePixelRatio || 1;
                    canvas.width  = Math.floor(viewport.width * ratio);
                    canvas.height = Math.floor(viewport.height * ratio);
                    canvas.style.width = '100%';
                    canvas.style.height = 'auto';
                    canvas.className = 'block mx-auto mb-3 rounded shadow-sm bg-white';

                    const ctx = canvas.getContext('2d');
                    ctx.scale(ratio, ratio);
                    host.appendChild(canvas);

                    await page.render({ canvasContext: ctx, viewport }).promise;
                }
            } catch (e) {
                this.error = 'Gagal memuat pratinjau PDF. Silakan unduh berkasnya.';
            } finally {
                this.loading = false;
            }
        },
    }));
});

// ─────────────────────────────────────────────────────────────────────────────
// Global form-validation alert (pop-up/toast).
//
// Menampilkan toast error global (event `notify`, didengar layout) setiap kali
// sebuah request Livewire selesai dengan error validasi. Berlaku untuk SEMUA form
// Livewire tanpa mengubah tiap komponen. Inline `@error(...)` per-field TETAP
// dipertahankan (ini pelengkap, bukan pengganti).
//
// Aturan tampil (agar tidak spam TAPI selalu muncul saat relevan):
//   toast bila error TIDAK kosong DAN (error-set BERUBAH  ATAU  commit berisi aksi).
//   - error berubah  → validasi baru/berbeda muncul (submit pertama, validasi upload).
//   - commit aksi    → submit/klik ulang meski error sama → HARUS muncul lagi.
//   - sinkronisasi `wire:model.live` yang membawa error basi (bukan aksi, error
//     tak berubah) → dilewati supaya tidak spam saat mengetik/memfilter.
// Dedupe 600ms untuk mencegah dobel-fire sesaat dari satu interaksi.
// ─────────────────────────────────────────────────────────────────────────────
document.addEventListener('livewire:init', () => {
    if (!window.Livewire) return;

    const lastSig = new Map(); // per component.id → signature error terakhir
    let lastMessage = null;
    let lastAt = 0;

    Livewire.hook('commit', ({ component, commit, succeed }) => {
        // Apakah commit ini membawa pemanggilan aksi (submit/klik method)?
        // Bila shape tak dikenal, anggap true agar alert tidak malah hilang.
        const hasCalls = commit && Array.isArray(commit.calls)
            ? commit.calls.length > 0
            : true;

        succeed(({ snapshot }) => {
            try {
                const parsed = typeof snapshot === 'string' ? JSON.parse(snapshot) : snapshot;
                const errors = (parsed && parsed.memo && parsed.memo.errors) || {};
                const keys = Object.keys(errors);

                const id  = component.id;
                const sig = JSON.stringify(errors);
                const prev = lastSig.get(id);
                lastSig.set(id, keys.length ? sig : undefined);

                if (keys.length === 0) return;

                const changed = sig !== prev;
                if (!changed && !hasCalls) return; // error basi pada sync non-aksi → lewati

                const first = errors[keys[0]];
                const firstMsg = Array.isArray(first) ? first[0] : String(first);
                const message = keys.length > 1
                    ? `Periksa ${keys.length} isian yang belum valid.`
                    : firstMsg;

                const now = Date.now();
                if (message === lastMessage && (now - lastAt) < 600) return;
                lastMessage = message;
                lastAt = now;

                window.dispatchEvent(new CustomEvent('notify', {
                    detail: { type: 'error', message },
                }));
            } catch (e) {
                // abaikan — jangan sampai mengganggu siklus Livewire
            }
        });
    });
});
