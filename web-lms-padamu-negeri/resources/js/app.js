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
