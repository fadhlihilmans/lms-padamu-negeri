<?php

namespace App\Livewire\Admin\MasterData;

use App\Models\Wilayah;
use App\Services\ErrorLogService;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app', ['pageTitle' => 'Wilayah'])]
#[Title('Wilayah')]
class WilayahManager extends Component
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
        $w             = Wilayah::findOrFail($id);
        $this->editId  = $id;
        $this->nama    = $w->nama;
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
                'required', 'string', 'max:100',
                Rule::unique('wilayah', 'nama')->ignore($this->editId),
            ],
        ], [
            'nama.required' => 'Nama Wilayah wajib diisi.',
            'nama.max'      => 'Nama Wilayah maksimal 100 karakter.',
            'nama.unique'   => 'Nama Wilayah sudah ada.',
        ]);

        try {
            Wilayah::updateOrCreate(
                ['id' => $this->editId],
                ['nama' => trim($this->nama)]
            );
            $aksi = $this->editId ? 'diperbarui' : 'ditambahkan';
            $this->tutupForm();
            $this->dispatch('notify', type: 'success', message: "Wilayah berhasil {$aksi}.");
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->catat('Simpan Wilayah', $th);
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
            $wilayah = Wilayah::findOrFail($id);
            if ($wilayah->rombel()->exists()) {
                $this->dispatch('notify', type: 'error', message: 'Wilayah tidak dapat dihapus karena masih digunakan oleh Rombel.');
                return;
            }
            $wilayah->delete();
            $this->dispatch('notify', type: 'success', message: 'Wilayah berhasil dihapus.');
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->catat('Hapus Wilayah', $th);
            $this->dispatch('notify', type: 'error', message: 'Maaf, terjadi kesalahan saat menghapus.');
        }
    }

    // ── Render ──────────────────────────────────────────────────────────────

    public function render(): View
    {
        return view('livewire.admin.master-data.wilayah-manager', [
            'wilayahs' => Wilayah::orderBy('nama')->get(),
        ]);
    }

    private function resetForm(): void
    {
        $this->editId = null;
        $this->nama   = '';
        $this->resetValidation();
    }
}
