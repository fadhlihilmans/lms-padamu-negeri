<?php

namespace App\Livewire\Admin\MasterData;

use App\Models\Paket;
use App\Services\ErrorLogService;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app', ['pageTitle' => 'Paket'])]
#[Title('Paket')]
class PaketManager extends Component
{
    public bool   $showForm         = false;
    public ?int   $editId           = null;
    public string $nama             = '';
    public ?int   $confirmDeleteId  = null;

    // ── Form ────────────────────────────────────────────────────────────────

    public function bukaFormTambah(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function bukaFormEdit(int $id): void
    {
        $p              = Paket::findOrFail($id);
        $this->editId   = $id;
        $this->nama     = $p->nama;
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
            'nama' => [
                'required', 'string', 'max:50',
                Rule::unique('paket', 'nama')->ignore($this->editId),
            ],
        ], [
            'nama.required' => 'Nama Paket wajib diisi.',
            'nama.max'      => 'Nama Paket maksimal 50 karakter.',
            'nama.unique'   => 'Nama Paket sudah ada.',
        ]);

        try {
            Paket::updateOrCreate(
                ['id' => $this->editId],
                ['nama' => trim($this->nama)]
            );
            $aksi = $this->editId ? 'diperbarui' : 'ditambahkan';
            $this->tutupForm();
            $this->dispatch('notify', type: 'success', message: "Paket berhasil {$aksi}.");
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->catat('Simpan Paket', $th);
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
            $paket = Paket::findOrFail($id);
            if ($paket->rombel()->exists()) {
                $this->dispatch('notify', type: 'error', message: 'Paket tidak dapat dihapus karena masih digunakan oleh Rombel.');
                return;
            }
            $paket->delete();
            $this->dispatch('notify', type: 'success', message: 'Paket berhasil dihapus.');
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->catat('Hapus Paket', $th);
            $this->dispatch('notify', type: 'error', message: 'Maaf, terjadi kesalahan saat menghapus.');
        }
    }

    // ── Render ──────────────────────────────────────────────────────────────

    public function render(): View
    {
        return view('livewire.admin.master-data.paket-manager', [
            'pakets' => Paket::withCount('tingkat')->orderBy('nama')->get(),
        ]);
    }

    private function resetForm(): void
    {
        $this->editId = null;
        $this->nama   = '';
        $this->resetValidation();
    }
}
