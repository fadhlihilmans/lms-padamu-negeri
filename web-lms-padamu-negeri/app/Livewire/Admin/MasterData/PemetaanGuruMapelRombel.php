<?php

namespace App\Livewire\Admin\MasterData;

use App\Models\Guru;
use App\Models\GuruMapelRombel;
use App\Models\Mapel;
use App\Models\PeriodeAjaran;
use App\Models\Rombel;
use App\Services\ErrorLogService;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.app', ['pageTitle' => 'Pemetaan Guru – Mapel – Rombel'])]
#[Title('Pemetaan Guru Mapel Rombel')]
class PemetaanGuruMapelRombel extends Component
{
    // ── Context filter (required to show data) ────────────────────────────────
    #[Url] public string $filterRombelId  = '';
    #[Url] public string $filterPeriodeId = '';

    // ── Form (add new entry) ─────────────────────────────────────────────────
    public bool $showForm       = false;
    public ?int $guruId         = null;
    public ?int $mapelId        = null;

    // ── Confirm delete ────────────────────────────────────────────────────────
    public ?int $confirmDeleteId = null;

    // ── Form actions ─────────────────────────────────────────────────────────

    public function openAddForm(): void
    {
        $this->guruId  = null;
        $this->mapelId = null;
        $this->resetValidation();
        $this->showForm = true;
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->guruId   = null;
        $this->mapelId  = null;
        $this->resetValidation();
    }

    public function save(): void
    {
        $this->validate([
            'guruId'  => 'required|exists:guru,id',
            'mapelId' => 'required|exists:mapel,id',
        ], [
            'guruId.required'  => 'Guru wajib dipilih.',
            'mapelId.required' => 'Mata Pelajaran wajib dipilih.',
        ]);

        $exists = GuruMapelRombel::where('guru_id', $this->guruId)
            ->where('mapel_id', $this->mapelId)
            ->where('rombel_id', $this->filterRombelId)
            ->where('periode_ajaran_id', $this->filterPeriodeId)
            ->exists();

        if ($exists) {
            $this->addError('mapelId', 'Guru ini sudah dipetakan ke Mata Pelajaran tersebut di Rombel dan Periode ini.');
            return;
        }

        try {
            GuruMapelRombel::create([
                'guru_id'           => $this->guruId,
                'mapel_id'          => $this->mapelId,
                'rombel_id'         => $this->filterRombelId,
                'periode_ajaran_id' => $this->filterPeriodeId,
            ]);
            $this->closeForm();
            $this->dispatch('notify', type: 'success', message: 'Pemetaan berhasil ditambahkan.');
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
        $rombels  = Rombel::orderByDesc('periode_ajaran_id')->orderBy('nama')->get();
        $periodes = PeriodeAjaran::orderByDesc('tahun_ajaran')
            ->orderByRaw("FIELD(semester, 'genap', 'ganjil')")
            ->get();

        $pemetaan = null;
        $gurus    = collect();
        $mapels   = collect();

        if ($this->filterRombelId && $this->filterPeriodeId) {
            $pemetaan = GuruMapelRombel::with(['guru', 'mapel'])
                ->where('rombel_id', $this->filterRombelId)
                ->where('periode_ajaran_id', $this->filterPeriodeId)
                ->orderBy('created_at')
                ->get();

            $gurus  = Guru::orderBy('nama_lengkap')->get();
            $mapels = Mapel::orderBy('nama')->get();
        }

        return view('livewire.admin.master-data.pemetaan-guru-mapel-rombel', compact(
            'rombels', 'periodes', 'pemetaan', 'gurus', 'mapels'
        ));
    }
}
