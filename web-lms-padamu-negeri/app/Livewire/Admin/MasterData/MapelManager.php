<?php

namespace App\Livewire\Admin\MasterData;

use App\Models\Mapel;
use App\Services\ErrorLogService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app', ['pageTitle' => 'Mata Pelajaran'])]
#[Title('Mata Pelajaran')]
class MapelManager extends Component
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
        $m              = Mapel::findOrFail($id);
        $this->editId   = $id;
        $this->nama     = $m->nama;
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
                Rule::unique('mapel', 'nama')->ignore($this->editId),
            ],
        ], [
            'nama.required' => 'Nama Mata Pelajaran wajib diisi.',
            'nama.max'      => 'Nama Mata Pelajaran maksimal 100 karakter.',
            'nama.unique'   => 'Nama Mata Pelajaran sudah ada.',
        ]);

        $isEdit = (bool) $this->editId;

        try {
            Mapel::updateOrCreate(
                ['id' => $this->editId],
                ['nama' => trim($this->nama)]
            );
            $this->closeForm();
            $this->dispatch('notify', type: 'success', message: 'Mata Pelajaran berhasil ' . ($isEdit ? 'diperbarui' : 'ditambahkan') . '.');
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Simpan Mapel', $th);
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
            $mapel = Mapel::findOrFail($id);
            if ($mapel->guruMapelRombel()->exists()) {
                $this->dispatch('notify', type: 'error', message: 'Mata Pelajaran tidak dapat dihapus karena sudah digunakan dalam Pemetaan Guru.');
                return;
            }
            if ($mapel->jadwalPelajaran()->exists()) {
                $this->dispatch('notify', type: 'error', message: 'Mata Pelajaran tidak dapat dihapus karena sudah digunakan dalam Jadwal Pelajaran.');
                return;
            }
            $mapel->delete();
            $this->dispatch('notify', type: 'success', message: 'Mata Pelajaran berhasil dihapus.');
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Hapus Mapel', $th);
            $this->dispatch('notify', type: 'error', message: 'Maaf, terjadi kesalahan saat menghapus.');
        }
    }

    // ── Render ───────────────────────────────────────────────────────────────

    public function render(): View
    {
        $mapels = Mapel::query()
            ->when($this->search, fn(Builder $q) => $q->where('nama', 'like', "%{$this->search}%"))
            ->withCount('guruMapelRombel')
            ->orderBy('nama')
            ->paginate($this->perPage);

        return view('livewire.admin.master-data.mapel-manager', compact('mapels'));
    }

    private function resetForm(): void
    {
        $this->editId = null;
        $this->nama   = '';
        $this->resetValidation();
    }
}
