<?php

namespace App\Livewire\Guru\Cbt;

use App\Models\Cbt;
use App\Models\Guru;
use App\Models\GuruMapelRombel;
use App\Models\PesertaDidikRombel;
use App\Services\ErrorLogService;
use App\Services\PeriodeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app', ['pageTitle' => 'CBT'])]
#[Title('CBT')]
class DaftarCbt extends Component
{
    use WithPagination;

    // Filter
    public string $gmrId   = '';
    public string $search  = '';
    public int    $perPage = 12;

    // Delete confirm
    public ?int $confirmDeleteId = null;

    public function updatingSearch(): void  { $this->resetPage(); }
    public function updatingPerPage(): void { $this->resetPage(); }
    public function updatingGmrId(): void   { $this->resetPage(); }

    private function getGuru(): ?Guru
    {
        return Auth::user()?->guru;
    }

    public function confirmDelete(int $id): void
    {
        $this->confirmDeleteId = $id;
    }

    public function cancelDelete(): void
    {
        $this->confirmDeleteId = null;
    }

    public function delete(): void
    {
        if (! $this->confirmDeleteId) {
            return;
        }

        try {
            $cbt = Cbt::findOrFail($this->confirmDeleteId);
            $this->authorizeGuru($cbt);

            $cbt->delete();
            $this->confirmDeleteId = null;
            $this->dispatch('notify', ['type' => 'success', 'message' => 'CBT dihapus.']);
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Hapus CBT', $th);
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Gagal menghapus CBT.']);
        }
    }

    private function authorizeGuru(Cbt $cbt): void
    {
        $guru = $this->getGuru();
        $gmr  = GuruMapelRombel::find($cbt->guru_mapel_rombel_id);
        abort_unless($guru && $gmr && $gmr->guru_id === $guru->id, 403);
    }

    public function render(): View
    {
        $guru    = $this->getGuru();
        $periode = app(PeriodeService::class)->getSelected();

        $gmrList        = collect();
        $cbt            = collect();
        $pdCountByGmrId = [];

        if ($guru && $periode) {
            $gmrList = GuruMapelRombel::with(['mapel', 'rombel'])
                ->where('guru_id', $guru->id)
                ->where('periode_ajaran_id', $periode->id)
                ->get();

            $gmrIds = $gmrList->pluck('id')->toArray();

            foreach ($gmrList as $gmr) {
                $pdCountByGmrId[$gmr->id] = PesertaDidikRombel::where('rombel_id', $gmr->rombel_id)->count();
            }

            if ($gmrIds) {
                $cbt = Cbt::with(['guruMapelRombel.mapel', 'guruMapelRombel.rombel'])
                    ->withCount(['soal', 'hasilCbt'])
                    ->whereIn('guru_mapel_rombel_id', $gmrIds)
                    ->when($this->gmrId, fn ($q) => $q->where('guru_mapel_rombel_id', $this->gmrId))
                    ->when($this->search, fn ($q) => $q->where('nama_ujian', 'like', '%' . $this->search . '%'))
                    ->orderByDesc('tanggal_mulai')
                    ->paginate($this->perPage);
            }
        }

        return view('livewire.guru.cbt.daftar-cbt', compact(
            'gmrList', 'cbt', 'pdCountByGmrId'
        ));
    }
}
