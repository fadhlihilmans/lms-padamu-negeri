<?php

namespace App\Livewire\Guru\Tugas;

use App\Models\Guru;
use App\Models\GuruMapelRombel;
use App\Models\PesertaDidikRombel;
use App\Models\Tugas;
use App\Services\ErrorLogService;
use App\Services\PeriodeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app', ['pageTitle' => 'Tugas'])]
#[Title('Tugas')]
class DaftarTugas extends Component
{
    use WithPagination;

    // Filter
    #[Url(as: 'gmr')] public string $gmrId = '';
    public string $search       = '';
    public string $filterStatus = '';
    public int    $perPage      = 10;

    // Delete confirm
    public ?int $confirmDeleteId = null;

    public function updatingSearch(): void       { $this->resetPage(); }
    public function updatingPerPage(): void      { $this->resetPage(); }
    public function updatingGmrId(): void        { $this->resetPage(); }
    public function updatingFilterStatus(): void { $this->resetPage(); }

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
        if (! $this->confirmDeleteId) return;

        try {
            $tugas = Tugas::findOrFail($this->confirmDeleteId);
            $this->authorizeGuru($tugas);

            if ($tugas->lampiran_path) {
                Storage::disk('public')->delete($tugas->lampiran_path);
            }

            $tugas->delete();
            $this->confirmDeleteId = null;
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Tugas dihapus.']);
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Hapus Tugas', $th);
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Gagal menghapus tugas.']);
        }
    }

    private function authorizeGuru(Tugas $tugas): void
    {
        $guru = $this->getGuru();
        $gmr  = GuruMapelRombel::find($tugas->guru_mapel_rombel_id);
        abort_unless($guru && $gmr && $gmr->guru_id === $guru->id, 403);
    }

    public function render(): View
    {
        $guru    = $this->getGuru();
        $periode = app(PeriodeService::class)->getSelected();

        $gmrList        = collect();
        $tugas          = collect();
        $pdCountByGmrId = [];

        if ($guru && $periode) {
            $gmrList = GuruMapelRombel::with(['mapel', 'rombel'])
                ->where('guru_id', $guru->id)
                ->where('tahun_ajaran', $periode->tahun_ajaran)
                ->get();

            $gmrIds = $gmrList->pluck('id')->toArray();

            foreach ($gmrList as $gmr) {
                $pdCountByGmrId[$gmr->id] = PesertaDidikRombel::where('rombel_id', $gmr->rombel_id)->count();
            }

            if ($gmrIds) {
                $tugas = Tugas::with(['guruMapelRombel.mapel', 'guruMapelRombel.rombel'])
                    ->withCount('submisi')
                    ->whereIn('guru_mapel_rombel_id', $gmrIds)
                    ->where('periode_ajaran_id', $periode->id)
                    ->when($this->gmrId, fn($q) => $q->where('guru_mapel_rombel_id', $this->gmrId))
                    ->when($this->search, fn($q) => $q->where('judul', 'like', '%' . $this->search . '%'))
                    ->when($this->filterStatus === 'aktif', fn($q) => $q->where('deadline', '>=', now()))
                    ->when($this->filterStatus === 'lewat', fn($q) => $q->where('deadline', '<', now()))
                    ->orderByDesc('deadline')
                    ->paginate($this->perPage);
            }
        }

        return view('livewire.guru.tugas.daftar-tugas', compact(
            'gmrList', 'tugas', 'pdCountByGmrId'
        ));
    }
}
