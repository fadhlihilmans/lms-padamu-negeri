<?php

namespace App\Livewire\Admin\Pengguna;

use App\Models\Guru;
use App\Models\User;
use App\Services\ErrorLogService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app', ['pageTitle' => 'Manajemen Guru'])]
#[Title('Manajemen Guru')]
class GuruManager extends Component
{
    use WithPagination;

    // ── Search & pagination ──────────────────────────────────────────────────
    #[Url] public string $search  = '';
    public int           $perPage = 10;

    // ── Form ─────────────────────────────────────────────────────────────────
    public bool   $showForm    = false;
    public ?int   $editId      = null;
    public string $nip         = '';
    public string $namaLengkap = '';
    public string $noHp        = '';

    // ── Reset Password ────────────────────────────────────────────────────────
    public ?int $confirmResetId = null;

    // ── Toggle Aktif ─────────────────────────────────────────────────────────
    public ?int $confirmToggleId = null;

    // ── Confirm Delete ────────────────────────────────────────────────────────
    public ?int $confirmDeleteId = null;

    // ── Lifecycle ────────────────────────────────────────────────────────────

    public function updatedSearch(): void  { $this->resetPage(); }
    public function updatedPerPage(): void { $this->resetPage(); }

    // ── Form actions ─────────────────────────────────────────────────────────

    public function openCreateForm(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function openEditForm(int $id): void
    {
        $guru              = Guru::findOrFail($id);
        $this->editId      = $id;
        $this->nip         = $guru->nip;
        $this->namaLengkap = $guru->nama_lengkap;
        $this->noHp        = $guru->no_hp ?? '';
        $this->showForm    = true;
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->resetForm();
    }

    public function save(): void
    {
        $this->validate([
            'nip'         => ['required', 'string', 'max:30', Rule::unique('guru', 'nip')->ignore($this->editId)],
            'namaLengkap' => 'required|string|max:150',
            'noHp'        => 'nullable|string|max:20',
        ], [
            'nip.required'         => 'NIP wajib diisi.',
            'nip.max'              => 'NIP maksimal 30 karakter.',
            'nip.unique'           => 'NIP sudah terdaftar.',
            'namaLengkap.required' => 'Nama lengkap wajib diisi.',
            'namaLengkap.max'      => 'Nama lengkap maksimal 150 karakter.',
            'noHp.max'             => 'No. HP maksimal 20 karakter.',
        ]);

        try {
            DB::transaction(function () {
                if ($this->editId) {
                    $guru = Guru::with('user')->findOrFail($this->editId);
                    $guru->update([
                        'nip'          => $this->nip,
                        'nama_lengkap' => $this->namaLengkap,
                        'no_hp'        => $this->noHp ?: null,
                    ]);
                    // Sync username jika NIP berubah
                    if ($guru->user && $guru->user->username !== $this->nip) {
                        $guru->user->update(['username' => $this->nip]);
                    }
                } else {
                    $user = User::create([
                        'username'           => $this->nip,
                        'password'           => $this->nip,
                        'is_change_password' => false,
                        'is_active'          => true,
                    ]);
                    $user->assignRole('guru');

                    Guru::create([
                        'user_id'      => $user->id,
                        'nip'          => $this->nip,
                        'nama_lengkap' => $this->namaLengkap,
                        'no_hp'        => $this->noHp ?: null,
                    ]);
                }
            });

            $this->closeForm();
            $this->dispatch('notify', type: 'success', message: 'Data guru berhasil ' . ($this->editId ? 'diperbarui' : 'ditambahkan') . '.');
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Simpan Guru', $th);
            $this->dispatch('notify', type: 'error', message: 'Maaf, terjadi kesalahan saat menyimpan.');
        }
    }

    // ── Reset Password ────────────────────────────────────────────────────────

    public function confirmReset(int $id): void  { $this->confirmResetId = $id; }

    public function resetPassword(): void
    {
        $id = $this->confirmResetId;
        $this->confirmResetId = null;
        if (! $id) return;

        try {
            $guru = Guru::with('user')->findOrFail($id);
            $guru->user->update([
                'password'           => $guru->nip,
                'is_change_password' => false,
            ]);
            $this->dispatch('notify', type: 'success', message: "Password guru \"{$guru->nama_lengkap}\" berhasil direset ke NIP.");
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Reset Password Guru', $th);
            $this->dispatch('notify', type: 'error', message: 'Maaf, terjadi kesalahan.');
        }
    }

    // ── Toggle Aktif ─────────────────────────────────────────────────────────

    /**
     * Tugas guru yang MASIH BERJALAN di TA aktif (wali kelas + plotting mapel).
     *
     * Dipakai sebagai penjagaan sebelum menonaktifkan: akun nonaktif TIDAK BISA
     * LOGIN, sehingga bila guru masih wali kelas / masih mengampu mapel di TA
     * aktif, tugas itu akan macet (rapor, kenaikan kelas, materi, tugas, CBT).
     *
     * @return array{rombelWali: array<string>, jumlahMapel: int, adaKonflik: bool}
     */
    private function tugasAktif(Guru $guru): array
    {
        $ta = app(\App\Services\PeriodeService::class)->getTahunAjaran();

        $rombelWali = \App\Models\Rombel::where('wali_kelas_id', $guru->id)
            ->tahunAjaran($ta)
            ->pluck('nama')
            ->all();

        $jumlahMapel = $guru->guruMapelRombel()
            ->when($ta, fn ($q) => $q->where('tahun_ajaran', $ta))
            ->count();

        return [
            'rombelWali'  => $rombelWali,
            'jumlahMapel' => $jumlahMapel,
            'adaKonflik'  => count($rombelWali) > 0 || $jumlahMapel > 0,
        ];
    }

    public function toggleActive(int $id): void
    {
        $guru = Guru::with('user')->findOrFail($id);

        // Mengaktifkan kembali tidak berisiko → langsung jalan.
        if (! $guru->user->is_active) {
            $this->setAktif($guru, true);
            return;
        }

        // Menonaktifkan: peringatkan bila guru masih punya tugas di TA aktif.
        if ($this->tugasAktif($guru)['adaKonflik']) {
            $this->confirmToggleId = $id;   // → tampilkan modal peringatan
            return;
        }

        $this->setAktif($guru, false);
    }

    /** Tetap nonaktifkan meski masih ada tugas aktif (Admin sudah diperingatkan). */
    public function confirmNonaktif(): void
    {
        $id = $this->confirmToggleId;
        $this->confirmToggleId = null;
        if (! $id) {
            return;
        }

        $this->setAktif(Guru::with('user')->findOrFail($id), false);
    }

    public function cancelToggle(): void
    {
        $this->confirmToggleId = null;
    }

    private function setAktif(Guru $guru, bool $aktif): void
    {
        try {
            $guru->user->update(['is_active' => $aktif]);
            $status = $aktif ? 'diaktifkan' : 'dinonaktifkan';
            $this->dispatch('notify', type: 'success', message: "Akun \"{$guru->nama_lengkap}\" berhasil {$status}.");
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Toggle Aktif Guru', $th);
            $this->dispatch('notify', type: 'error', message: 'Maaf, terjadi kesalahan.');
        }
    }

    // ── Delete ───────────────────────────────────────────────────────────────

    public function confirmDelete(int $id): void { $this->confirmDeleteId = $id; }

    public function delete(): void
    {
        $id = $this->confirmDeleteId;
        $this->confirmDeleteId = null;
        if (! $id) return;

        try {
            $guru = Guru::findOrFail($id);

            // Guru yang punya JEJAK MENGAJAR tidak boleh dihapus — menghapusnya akan
            // memutus histori materi/tugas/CBT/nilai di TA lampau. Sesuai database.md:
            // "Jika perlu nonaktifkan, pakai kolom is_aktif, bukan soft delete."
            // Solusinya: NONAKTIFKAN akunnya (users.is_active = 0), bukan dihapus.
            if ($guru->guruMapelRombel()->exists() || $guru->jadwalPelajaran()->exists()) {
                $this->dispatch(
                    'notify',
                    type: 'warning',
                    message: 'Guru ini punya histori mengajar (pemetaan/jadwal), jadi tidak bisa dihapus tanpa merusak arsip. Gunakan tombol Nonaktifkan — guru akan hilang dari penugasan baru, tetapi datanya tetap utuh.',
                );
                return;
            }

            DB::transaction(function () use ($guru) {
                $guru->user->delete();
                $guru->delete();
            });

            $this->dispatch('notify', type: 'success', message: 'Data guru berhasil dihapus.');
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Hapus Guru', $th);
            $this->dispatch('notify', type: 'error', message: 'Maaf, terjadi kesalahan saat menghapus.');
        }
    }

    // ── Render ───────────────────────────────────────────────────────────────

    public function render(): View
    {
        $gurus = Guru::query()
            ->with('user')
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('nama_lengkap', 'like', "%{$this->search}%")
                  ->orWhere('nip', 'like', "%{$this->search}%");
            }))
            ->orderBy('nama_lengkap')
            ->paginate($this->perPage);

        // Data untuk modal peringatan nonaktif (hanya saat dipicu).
        $toggleGuru = $this->confirmToggleId ? Guru::find($this->confirmToggleId) : null;
        $toggleInfo = $toggleGuru ? $this->tugasAktif($toggleGuru) : null;

        return view('livewire.admin.pengguna.guru-manager', compact('gurus', 'toggleGuru', 'toggleInfo'));
    }

    private function resetForm(): void
    {
        $this->editId      = null;
        $this->nip         = '';
        $this->namaLengkap = '';
        $this->noHp        = '';
        $this->resetValidation();
    }
}
