<?php

namespace App\Livewire\Admin\MasterData;

use App\Models\Guru;
use App\Models\GuruMapelRombel;
use App\Models\Mapel;
use App\Models\PeriodeAjaran;
use App\Models\Rombel;
use App\Services\ErrorLogService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app', ['pageTitle' => 'Pemetaan Guru Mapel per Rombel'])]
#[Title('Pemetaan Guru Mapel Rombel')]
class PemetaanGuruMapelRombel extends Component
{
    use WithPagination;

    // ── Search, filter & pagination ──────────────────────────────────────────
    #[Url] public string $search         = '';
    public string        $filterRombelId = '';
    public int           $perPage        = 10;

    // ── Form (tambah/edit) ────────────────────────────────────────────────────
    public bool $showForm = false;
    public ?int $editId   = null;
    public ?int $rombelId = null;
    public ?int $guruId   = null;
    public ?int $mapelId  = null;

    // ── Confirm delete ────────────────────────────────────────────────────────
    public ?int $confirmDeleteId = null;

    public function updatedSearch(): void         { $this->resetPage(); }
    public function updatedPerPage(): void        { $this->resetPage(); }
    public function updatedFilterRombelId(): void { $this->resetPage(); }

    // ── Form actions ─────────────────────────────────────────────────────────

    public function openCreateForm(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function openEditForm(int $id): void
    {
        $pemetaan = GuruMapelRombel::findOrFail($id);
        $this->editId   = $id;
        $this->rombelId = $pemetaan->rombel_id;
        $this->guruId   = $pemetaan->guru_id;
        $this->mapelId  = $pemetaan->mapel_id;
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
            'rombelId' => 'required|exists:rombel,id',
            'guruId'   => 'required|exists:guru,id',
            'mapelId'  => 'required|exists:mapel,id',
        ], [
            'rombelId.required' => 'Rombel wajib dipilih.',
            'rombelId.exists'   => 'Rombel tidak valid.',
            'guruId.required'   => 'Guru wajib dipilih.',
            'guruId.exists'     => 'Guru tidak valid.',
            'mapelId.required'  => 'Mata Pelajaran wajib dipilih.',
            'mapelId.exists'    => 'Mata Pelajaran tidak valid.',
        ]);

        // Plotting terikat TAHUN AJARAN → cukup 1x per TA, genap otomatis ikut.
        $tahunAjaran = app(\App\Services\PeriodeService::class)->getTahunAjaran();

        if (! $tahunAjaran) {
            $this->dispatch('notify', type: 'error', message: 'Tidak ada Periode Ajaran aktif. Aktifkan periode terlebih dahulu.');
            return;
        }

        $duplicate = GuruMapelRombel::where('rombel_id', $this->rombelId)
            ->where('mapel_id', $this->mapelId)
            ->where('tahun_ajaran', $tahunAjaran)
            ->when($this->editId, fn ($q) => $q->where('id', '!=', $this->editId))
            ->exists();

        if ($duplicate) {
            $this->addError('mapelId', 'Mata Pelajaran ini sudah dipetakan untuk Rombel tersebut pada Tahun Ajaran ini.');
            return;
        }

        $isEdit = (bool) $this->editId;

        try {
            GuruMapelRombel::updateOrCreate(
                ['id' => $this->editId],
                [
                    'rombel_id'    => $this->rombelId,
                    'guru_id'      => $this->guruId,
                    'mapel_id'     => $this->mapelId,
                    'tahun_ajaran' => $tahunAjaran,
                ]
            );
            $this->closeForm();
            $this->dispatch('notify', type: 'success', message: 'Pemetaan berhasil ' . ($isEdit ? 'diperbarui' : 'ditambahkan') . '.');
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Simpan Pemetaan Guru Mapel Rombel', $th);
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
            $pemetaan = GuruMapelRombel::findOrFail($id);

            if ($pemetaan->materi()->exists() || $pemetaan->tugas()->exists() || $pemetaan->cbt()->exists()) {
                $this->dispatch('notify', type: 'error', message: 'Pemetaan tidak dapat dihapus karena sudah memiliki Materi, Tugas, atau CBT terkait.');
                return;
            }

            $pemetaan->delete();
            $this->dispatch('notify', type: 'success', message: 'Pemetaan berhasil dihapus.');
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Hapus Pemetaan Guru Mapel Rombel', $th);
            $this->dispatch('notify', type: 'error', message: 'Maaf, terjadi kesalahan saat menghapus.');
        }
    }

    // ── Render ───────────────────────────────────────────────────────────────

    public function render(): View
    {
        $periodeAktif = PeriodeAjaran::where('is_aktif', true)->first();
        $tahunAjaran  = app(\App\Services\PeriodeService::class)->getTahunAjaran();

        $pemetaan = GuruMapelRombel::query()
            ->with(['guru', 'mapel', 'rombel'])
            ->when($tahunAjaran, fn (Builder $q) => $q->where('tahun_ajaran', $tahunAjaran), fn (Builder $q) => $q->whereRaw('1 = 0'))
            ->when($this->filterRombelId, fn (Builder $q) => $q->where('rombel_id', $this->filterRombelId))
            ->when($this->search, fn (Builder $q) => $q->where(function (Builder $qq) {
                $qq->whereHas('guru', fn (Builder $qqq) => $qqq->where('nama_lengkap', 'like', "%{$this->search}%"))
                   ->orWhereHas('mapel', fn (Builder $qqq) => $qqq->where('nama', 'like', "%{$this->search}%"));
            }))
            ->orderBy('rombel_id')
            ->orderBy('mapel_id')
            ->paginate($this->perPage);

        $rombels = $tahunAjaran
            ? Rombel::where('tahun_ajaran', $tahunAjaran)->orderBy('nama')->get()
            : collect();
        // Hanya guru AKTIF yang bisa dipetakan ke penugasan baru. Guru nonaktif
        // (sudah keluar) tetap punya histori plotting di TA lampau — tidak dihapus.
        $gurus = Guru::whereHas('user', fn ($q) => $q->where('is_active', true))
            ->orderBy('nama_lengkap')
            ->get();
        $mapels = Mapel::orderBy('nama')->get();

        return view('livewire.admin.master-data.pemetaan-guru-mapel-rombel', compact(
            'pemetaan', 'rombels', 'gurus', 'mapels', 'periodeAktif'
        ));
    }

    private function resetForm(): void
    {
        $this->editId   = null;
        $this->rombelId = null;
        $this->guruId   = null;
        $this->mapelId  = null;
        $this->resetValidation();
    }
}
