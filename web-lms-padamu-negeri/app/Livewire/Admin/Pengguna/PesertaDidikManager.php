<?php

namespace App\Livewire\Admin\Pengguna;

use App\Models\PesertaDidik;
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

#[Layout('components.layouts.app', ['pageTitle' => 'Manajemen Peserta Didik'])]
#[Title('Manajemen Peserta Didik')]
class PesertaDidikManager extends Component
{
    use WithPagination;

    // ── Search & pagination ──────────────────────────────────────────────────
    #[Url] public string $search              = '';
    public string        $filterStatusAkademik = '';
    public int           $perPage             = 10;

    // ── Form ─────────────────────────────────────────────────────────────────
    public bool   $showForm       = false;
    public ?int   $editId         = null;
    public string $nipd           = '';
    public string $nisn           = '';
    public string $nik            = '';
    public string $namaLengkap    = '';
    public string $jenisKelamin   = '';
    public string $tempatLahir    = '';
    public string $tanggalLahir   = '';
    public string $agama          = '';
    public string $noHp           = '';
    public string $statusAkademik = 'aktif';

    // ── Reset Password ────────────────────────────────────────────────────────
    public ?int $confirmResetId = null;

    // ── Confirm Delete ────────────────────────────────────────────────────────
    public ?int $confirmDeleteId = null;

    // ── Lifecycle ────────────────────────────────────────────────────────────

    public function updatedSearch(): void               { $this->resetPage(); }
    public function updatedPerPage(): void              { $this->resetPage(); }
    public function updatedFilterStatusAkademik(): void { $this->resetPage(); }

    // ── Form actions ─────────────────────────────────────────────────────────

    public function openCreateForm(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function openEditForm(int $id): void
    {
        $pd                   = PesertaDidik::findOrFail($id);
        $this->editId         = $id;
        $this->nipd           = $pd->nipd;
        $this->nisn           = $pd->nisn ?? '';
        $this->nik            = $pd->nik ?? '';
        $this->namaLengkap    = $pd->nama_lengkap;
        $this->jenisKelamin   = $pd->jenis_kelamin;
        $this->tempatLahir    = $pd->tempat_lahir ?? '';
        $this->tanggalLahir   = $pd->tanggal_lahir ? $pd->tanggal_lahir->format('Y-m-d') : '';
        $this->agama          = $pd->agama ?? '';
        $this->noHp           = $pd->no_hp ?? '';
        $this->statusAkademik = $pd->status_akademik;
        $this->showForm       = true;
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->resetForm();
    }

    public function save(): void
    {
        $rules = [
            'nipd'           => ['required', 'string', 'max:30', Rule::unique('peserta_didik', 'nipd')->ignore($this->editId)],
            'namaLengkap'    => 'required|string|max:150',
            'jenisKelamin'   => 'required|in:L,P',
            'tempatLahir'    => 'nullable|string|max:100',
            'tanggalLahir'   => 'nullable|date',
            'agama'          => 'nullable|string|max:30',
            'noHp'           => 'nullable|string|max:20',
            'statusAkademik' => 'required|in:aktif,lulus,pindah,keluar',
            'nisn'           => ['nullable', 'string', 'max:20', Rule::unique('peserta_didik', 'nisn')->ignore($this->editId)],
            'nik'            => 'nullable|string|max:20',
        ];

        $messages = [
            'nipd.required'           => 'NIPD wajib diisi.',
            'nipd.max'                => 'NIPD maksimal 30 karakter.',
            'nipd.unique'             => 'NIPD sudah terdaftar.',
            'namaLengkap.required'    => 'Nama lengkap wajib diisi.',
            'namaLengkap.max'         => 'Nama lengkap maksimal 150 karakter.',
            'jenisKelamin.required'   => 'Jenis kelamin wajib dipilih.',
            'jenisKelamin.in'         => 'Jenis kelamin harus L atau P.',
            'tanggalLahir.date'       => 'Tanggal lahir tidak valid.',
            'statusAkademik.required' => 'Status akademik wajib dipilih.',
            'statusAkademik.in'       => 'Status akademik tidak valid.',
            'nisn.max'                => 'NISN maksimal 20 karakter.',
            'nisn.unique'             => 'NISN sudah terdaftar.',
        ];

        $this->validate($rules, $messages);

        try {
            DB::transaction(function () {
                if ($this->editId) {
                    $pd = PesertaDidik::with('user')->findOrFail($this->editId);
                    $pd->update([
                        'nipd'           => $this->nipd,
                        'nisn'           => $this->nisn ?: null,
                        'nik'            => $this->nik ?: null,
                        'nama_lengkap'   => $this->namaLengkap,
                        'jenis_kelamin'  => $this->jenisKelamin,
                        'tempat_lahir'   => $this->tempatLahir ?: null,
                        'tanggal_lahir'  => $this->tanggalLahir ?: null,
                        'agama'          => $this->agama ?: null,
                        'no_hp'          => $this->noHp ?: null,
                        'status_akademik' => $this->statusAkademik,
                    ]);
                    // Sync username jika NIPD berubah
                    if ($pd->user && $pd->user->username !== $this->nipd) {
                        $pd->user->update(['username' => $this->nipd]);
                    }
                } else {
                    $user = User::create([
                        'username'           => $this->nipd,
                        'password'           => $this->nipd,
                        'is_change_password' => false,
                        'is_active'          => true,
                    ]);
                    $user->assignRole('peserta_didik');

                    PesertaDidik::create([
                        'user_id'        => $user->id,
                        'nipd'           => $this->nipd,
                        'nisn'           => $this->nisn ?: null,
                        'nik'            => $this->nik ?: null,
                        'nama_lengkap'   => $this->namaLengkap,
                        'jenis_kelamin'  => $this->jenisKelamin,
                        'tempat_lahir'   => $this->tempatLahir ?: null,
                        'tanggal_lahir'  => $this->tanggalLahir ?: null,
                        'agama'          => $this->agama ?: null,
                        'no_hp'          => $this->noHp ?: null,
                        'status_akademik' => $this->statusAkademik,
                    ]);
                }
            });

            $this->closeForm();
            $this->dispatch('notify', type: 'success', message: 'Data peserta didik berhasil ' . ($this->editId ? 'diperbarui' : 'ditambahkan') . '.');
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Simpan Peserta Didik', $th);
            $this->dispatch('notify', type: 'error', message: 'Maaf, terjadi kesalahan saat menyimpan.');
        }
    }

    // ── Reset Password ────────────────────────────────────────────────────────

    public function confirmReset(int $id): void { $this->confirmResetId = $id; }

    public function resetPassword(): void
    {
        $id = $this->confirmResetId;
        $this->confirmResetId = null;
        if (! $id) return;

        try {
            $pd = PesertaDidik::with('user')->findOrFail($id);
            $pd->user->update([
                'password'           => $pd->nipd,
                'is_change_password' => false,
            ]);
            $this->dispatch('notify', type: 'success', message: "Password \"{$pd->nama_lengkap}\" berhasil direset ke NIPD.");
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Reset Password Peserta Didik', $th);
            $this->dispatch('notify', type: 'error', message: 'Maaf, terjadi kesalahan.');
        }
    }

    // ── Toggle Aktif ─────────────────────────────────────────────────────────

    public function toggleActive(int $id): void
    {
        try {
            $pd = PesertaDidik::with('user')->findOrFail($id);
            $pd->user->update(['is_active' => ! $pd->user->is_active]);
            $status = $pd->user->is_active ? 'diaktifkan' : 'dinonaktifkan';
            $this->dispatch('notify', type: 'success', message: "Akun \"{$pd->nama_lengkap}\" berhasil {$status}.");
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Toggle Aktif Peserta Didik', $th);
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
            $pd = PesertaDidik::findOrFail($id);

            if ($pd->pesertaDidikRombel()->exists()) {
                $this->dispatch('notify', type: 'error', message: 'Peserta Didik tidak dapat dihapus karena terdaftar di rombel.');
                return;
            }
            if ($pd->tugasSubmisi()->exists()) {
                $this->dispatch('notify', type: 'error', message: 'Peserta Didik tidak dapat dihapus karena memiliki submisi tugas.');
                return;
            }
            if ($pd->hasilCbt()->exists()) {
                $this->dispatch('notify', type: 'error', message: 'Peserta Didik tidak dapat dihapus karena memiliki hasil CBT.');
                return;
            }

            DB::transaction(function () use ($pd) {
                $pd->user->delete();
                $pd->delete();
            });

            $this->dispatch('notify', type: 'success', message: 'Data peserta didik berhasil dihapus.');
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Hapus Peserta Didik', $th);
            $this->dispatch('notify', type: 'error', message: 'Maaf, terjadi kesalahan saat menghapus.');
        }
    }

    // ── Render ───────────────────────────────────────────────────────────────

    public function render(): View
    {
        $pesertaDidiks = PesertaDidik::query()
            ->with('user')
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('nama_lengkap', 'like', "%{$this->search}%")
                  ->orWhere('nipd', 'like', "%{$this->search}%")
                  ->orWhere('nisn', 'like', "%{$this->search}%");
            }))
            ->when($this->filterStatusAkademik, fn($q) => $q->where('status_akademik', $this->filterStatusAkademik))
            ->orderBy('nama_lengkap')
            ->paginate($this->perPage);

        return view('livewire.admin.pengguna.peserta-didik-manager', compact('pesertaDidiks'));
    }

    private function resetForm(): void
    {
        $this->editId         = null;
        $this->nipd           = '';
        $this->nisn           = '';
        $this->nik            = '';
        $this->namaLengkap    = '';
        $this->jenisKelamin   = '';
        $this->tempatLahir    = '';
        $this->tanggalLahir   = '';
        $this->agama          = '';
        $this->noHp           = '';
        $this->statusAkademik = 'aktif';
        $this->resetValidation();
    }
}
