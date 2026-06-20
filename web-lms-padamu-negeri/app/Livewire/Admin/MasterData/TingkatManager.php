<?php

namespace App\Livewire\Admin\MasterData;

use App\Models\Paket;
use App\Models\Tingkat;
use App\Services\ErrorLogService;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app', ['pageTitle' => 'Tingkat'])]
#[Title('Tingkat')]
class TingkatManager extends Component
{
    public bool   $showForm         = false;
    public ?int   $editId           = null;
    public string $nama             = '';
    public ?int   $paketId          = null;
    public ?int   $confirmDeleteId  = null;

    // ── Form ────────────────────────────────────────────────────────────────

    public function bukaFormTambah(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function bukaFormEdit(int $id): void
    {
        $t              = Tingkat::findOrFail($id);
        $this->editId   = $id;
        $this->nama     = $t->nama;
        $this->paketId  = $t->paket_id;
        $this->showForm = true;
    }

    public function tutupForm(): void
    {
        $this->showForm = false;
        $this->resetForm();
    }

    public function simpan(): void
    {
        $this->validate([
            'paketId' => 'required|exists:paket,id',
            'nama'    => [
                'required', 'string', 'max:50',
                Rule::unique('tingkat', 'nama')->where('paket_id', $this->paketId)->ignore($this->editId),
            ],
        ], [
            'paketId.required' => 'Paket wajib dipilih.',
            'paketId.exists'   => 'Paket tidak valid.',
            'nama.required'    => 'Nama Tingkat wajib diisi.',
            'nama.max'         => 'Nama Tingkat maksimal 50 karakter.',
            'nama.unique'      => 'Nama Tingkat sudah ada di Paket yang sama.',
        ]);

        try {
            Tingkat::updateOrCreate(
                ['id' => $this->editId],
                [
                    'paket_id' => $this->paketId,
                    'nama'     => trim($this->nama),
                ]
            );
            $aksi = $this->editId ? 'diperbarui' : 'ditambahkan';
            $this->tutupForm();
            $this->dispatch('notify', type: 'success', message: "Tingkat berhasil {$aksi}.");
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->catat('Simpan Tingkat', $th);
            $this->dispatch('notify', type: 'error', message: 'Maaf, terjadi kesalahan saat menyimpan.');
        }
    }

    // ── Hapus ───────────────────────────────────────────────────────────────

    public function konfirmasiHapus(int $id): void
    {
        $this->confirmDeleteId = $id;
    }

    public function hapus(): void
    {
        $id                    = $this->confirmDeleteId;
        $this->confirmDeleteId = null;
        if (! $id) return;

        try {
            $tingkat = Tingkat::findOrFail($id);
            if ($tingkat->rombel()->exists()) {
                $this->dispatch('notify', type: 'error', message: 'Tingkat tidak dapat dihapus karena masih digunakan oleh Rombel.');
                return;
            }
            $tingkat->delete();
            $this->dispatch('notify', type: 'success', message: 'Tingkat berhasil dihapus.');
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->catat('Hapus Tingkat', $th);
            $this->dispatch('notify', type: 'error', message: 'Maaf, terjadi kesalahan saat menghapus.');
        }
    }

    // ── Render ──────────────────────────────────────────────────────────────

    public function render(): View
    {
        return view('livewire.admin.master-data.tingkat-manager', [
            'tingkats' => Tingkat::with('paket')->orderBy('paket_id')->orderBy('nama')->get(),
            'pakets'   => Paket::orderBy('nama')->get(),
        ]);
    }

    private function resetForm(): void
    {
        $this->editId  = null;
        $this->nama    = '';
        $this->paketId = null;
        $this->resetValidation();
    }
}
