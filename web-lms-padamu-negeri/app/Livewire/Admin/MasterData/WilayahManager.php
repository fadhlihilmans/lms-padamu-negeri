<?php

namespace App\Livewire\Admin\MasterData;

use App\Models\Wilayah;
use App\Services\ErrorLogService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app', ['pageTitle' => 'Wilayah'])]
#[Title('Wilayah')]
class WilayahManager extends Component
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
        $w              = Wilayah::findOrFail($id);
        $this->editId   = $id;
        $this->nama     = $w->nama;
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
                'required', 'string', 'max:100',
                Rule::unique('wilayah', 'nama')->ignore($this->editId),
            ],
        ], [
            'nama.required' => 'Nama Wilayah wajib diisi.',
            'nama.max'      => 'Nama Wilayah maksimal 100 karakter.',
            'nama.unique'   => 'Nama Wilayah sudah ada.',
        ]);

        $isEdit = (bool) $this->editId;

        try {
            Wilayah::updateOrCreate(
                ['id' => $this->editId],
                ['nama' => trim($this->nama)]
            );
            $this->closeForm();
            $this->dispatch('notify', type: 'success', message: 'Wilayah berhasil ' . ($isEdit ? 'diperbarui' : 'ditambahkan') . '.');
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Simpan Wilayah', $th);
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
            $wilayah = Wilayah::findOrFail($id);
            if ($wilayah->rombel()->exists()) {
                $this->dispatch('notify', type: 'error', message: 'Wilayah tidak dapat dihapus karena masih digunakan oleh Rombel.');
                return;
            }
            $wilayah->delete();
            $this->dispatch('notify', type: 'success', message: 'Wilayah berhasil dihapus.');
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Hapus Wilayah', $th);
            $this->dispatch('notify', type: 'error', message: 'Maaf, terjadi kesalahan saat menghapus.');
        }
    }

    // ── Render ───────────────────────────────────────────────────────────────

    public function render(): View
    {
        $wilayahs = Wilayah::query()
            ->when($this->search, fn(Builder $q) => $q->where('nama', 'like', "%{$this->search}%"))
            ->orderBy('nama')
            ->paginate($this->perPage);

            // dd(session('periode_id_selected'));

        return view('livewire.admin.master-data.wilayah-manager', compact('wilayahs'));
    }

    private function resetForm(): void
    {
        $this->editId = null;
        $this->nama   = '';
        $this->resetValidation();
    }
}
