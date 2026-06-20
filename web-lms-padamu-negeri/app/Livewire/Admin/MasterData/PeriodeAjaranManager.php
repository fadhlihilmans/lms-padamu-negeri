<?php

namespace App\Livewire\Admin\MasterData;

use App\Models\PeriodeAjaran;
use App\Services\ErrorLogService;
use App\Services\PeriodeService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app', ['pageTitle' => 'Periode Ajaran'])]
#[Title('Periode Ajaran')]
class PeriodeAjaranManager extends Component
{
    use WithPagination;

    // ── Search & pagination ──────────────────────────────────────────────────
    #[Url] public string $search         = '';
    public string        $filterSemester = '';
    public int           $perPage        = 10;

    // ── Form ─────────────────────────────────────────────────────────────────
    public bool   $showForm    = false;
    public ?int   $editId      = null;
    public string $tahunAjaran = '';
    public string $semester    = '';

    // ── Confirmations ────────────────────────────────────────────────────────
    public ?int $confirmDeleteId   = null;
    public ?int $confirmActivateId = null;

    // ── Search / filter lifecycle ─────────────────────────────────────────────
    public function updatedSearch(): void         { $this->resetPage(); }
    public function updatedPerPage(): void        { $this->resetPage(); }
    public function updatedFilterSemester(): void { $this->resetPage(); }

    // ── Form actions ─────────────────────────────────────────────────────────

    public function openCreateForm(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function openEditForm(int $id): void
    {
        $periode           = PeriodeAjaran::findOrFail($id);
        $this->editId      = $id;
        $this->tahunAjaran = $periode->tahun_ajaran;
        $this->semester    = $periode->semester;
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
            'tahunAjaran' => [
                'required',
                'string',
                'regex:/^\d{4}\/\d{4}$/',
                Rule::unique('periode_ajaran', 'tahun_ajaran')
                    ->where('semester', $this->semester)
                    ->ignore($this->editId),
            ],
            'semester' => 'required|in:ganjil,genap',
        ], [
            'tahunAjaran.required' => 'Tahun Ajaran wajib diisi.',
            'tahunAjaran.regex'    => 'Format harus YYYY/YYYY, mis. 2024/2025.',
            'tahunAjaran.unique'   => 'Kombinasi Tahun Ajaran dan Semester sudah ada.',
            'semester.required'    => 'Semester wajib dipilih.',
            'semester.in'          => 'Semester tidak valid.',
        ]);

        $isEdit = (bool) $this->editId;

        try {
            PeriodeAjaran::updateOrCreate(
                ['id' => $this->editId],
                [
                    'tahun_ajaran' => $this->tahunAjaran,
                    'semester'     => $this->semester,
                ]
            );

            $this->closeForm();
            $this->dispatch('notify', type: 'success', message: 'Periode Ajaran berhasil ' . ($isEdit ? 'diperbarui' : 'ditambahkan') . '.');
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Simpan Periode Ajaran', $th);
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
            $periode = PeriodeAjaran::findOrFail($id);

            if ($periode->is_aktif) {
                $this->dispatch('notify', type: 'error', message: 'Periode Aktif tidak dapat dihapus. Aktifkan periode lain terlebih dahulu.');
                return;
            }

            $periode->delete();
            $this->dispatch('notify', type: 'success', message: 'Periode Ajaran berhasil dihapus.');
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Hapus Periode Ajaran', $th);
            $this->dispatch('notify', type: 'error', message: 'Maaf, terjadi kesalahan saat menghapus.');
        }
    }

    // ── Activate ─────────────────────────────────────────────────────────────

    public function confirmActivate(int $id): void { $this->confirmActivateId = $id; }

    public function activate(): void
    {
        $id = $this->confirmActivateId;
        $this->confirmActivateId = null;

        if (! $id) return;

        try {
            DB::transaction(function () use ($id) {
                PeriodeAjaran::query()->update(['is_aktif' => false]);
                PeriodeAjaran::findOrFail($id)->update(['is_aktif' => true]);
            });

            app(PeriodeService::class)->resetToActive();
            $this->dispatch('notify', type: 'success', message: 'Periode Ajaran berhasil diaktifkan.');
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Aktifkan Periode Ajaran', $th);
            $this->dispatch('notify', type: 'error', message: 'Maaf, terjadi kesalahan.');
        }
    }

    // ── Render ───────────────────────────────────────────────────────────────

    public function render(): View
    {
        $periodes = PeriodeAjaran::query()
            ->when($this->search, fn(Builder $q) => $q->where('tahun_ajaran', 'like', "%{$this->search}%"))
            ->when($this->filterSemester, fn(Builder $q) => $q->where('semester', $this->filterSemester))
            ->orderByDesc('tahun_ajaran')
            ->orderByRaw("FIELD(semester, 'genap', 'ganjil')")
            ->paginate($this->perPage);

        return view('livewire.admin.master-data.periode-ajaran-manager', compact('periodes'));
    }

    private function resetForm(): void
    {
        $this->editId      = null;
        $this->tahunAjaran = '';
        $this->semester    = '';
        $this->resetValidation();
    }
}
