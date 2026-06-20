<?php

namespace App\Livewire\Admin\MasterData;

use App\Models\Paket;
use App\Services\ErrorLogService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app', ['pageTitle' => 'Paket'])]
#[Title('Paket')]
class PaketManager extends Component
{
    use WithPagination;

    // ── Search & pagination ──────────────────────────────────────────────────
    #[Url] public string $search  = '';
    public int           $perPage = 10;

    // ── Form ─────────────────────────────────────────────────────────────────
    public bool   $showForm        = false;
    public ?int   $editId          = null;
    public string $nama            = '';
    public ?int   $confirmDeleteId = null;

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
        $p              = Paket::findOrFail($id);
        $this->editId   = $id;
        $this->nama     = $p->nama;
        $this->showForm = true;
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->resetForm();
    }

    public function save(): void
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

        $isEdit = (bool) $this->editId;

        try {
            Paket::updateOrCreate(
                ['id' => $this->editId],
                ['nama' => trim($this->nama)]
            );
            $this->closeForm();
            $this->dispatch('notify', type: 'success', message: 'Paket berhasil ' . ($isEdit ? 'diperbarui' : 'ditambahkan') . '.');
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Simpan Paket', $th);
            $this->dispatch('notify', type: 'error', message: 'Maaf, terjadi kesalahan saat menyimpan.');
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
            $paket = Paket::findOrFail($id);
            if ($paket->rombel()->exists()) {
                $this->dispatch('notify', type: 'error', message: 'Paket tidak dapat dihapus karena masih digunakan oleh Rombel.');
                return;
            }
            $paket->delete();
            $this->dispatch('notify', type: 'success', message: 'Paket berhasil dihapus.');
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Hapus Paket', $th);
            $this->dispatch('notify', type: 'error', message: 'Maaf, terjadi kesalahan saat menghapus.');
        }
    }

    // ── Render ───────────────────────────────────────────────────────────────

    public function render(): View
    {
        $pakets = Paket::query()
            ->withCount('tingkat')
            ->when($this->search, fn(Builder $q) => $q->where('nama', 'like', "%{$this->search}%"))
            ->orderBy('nama')
            ->paginate($this->perPage);

        return view('livewire.admin.master-data.paket-manager', compact('pakets'));
    }

    private function resetForm(): void
    {
        $this->editId = null;
        $this->nama   = '';
        $this->resetValidation();
    }
}
