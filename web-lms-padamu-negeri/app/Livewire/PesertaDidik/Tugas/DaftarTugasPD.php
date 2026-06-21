<?php

namespace App\Livewire\PesertaDidik\Tugas;

use App\Models\PesertaDidikRombel;
use App\Models\Tugas;
use App\Models\TugasSubmisi;
use App\Services\PeriodeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app', ['pageTitle' => 'Tugas'])]
#[Title('Tugas')]
class DaftarTugasPD extends Component
{
    use WithPagination;

    #[Url] public string $search      = '';
    #[Url] public int    $perPage     = 15;
    #[Url] public string $filterMapel = '';
    #[Url] public string $filterStatus = '';

    public function updatingSearch(): void       { $this->resetPage(); }
    public function updatingPerPage(): void      { $this->resetPage(); }
    public function updatingFilterMapel(): void  { $this->resetPage(); }
    public function updatingFilterStatus(): void { $this->resetPage(); }

    public function render(): View
    {
        $pd      = Auth::user()?->pesertaDidik;
        $periode = app(PeriodeService::class)->getSelected();

        $rombels   = collect();
        $rombelIds = [];
        $mapels    = collect();
        $tugas     = collect();
        $mySubmisiByTugasId = collect();

        if ($pd && $periode) {
            $rombels = PesertaDidikRombel::with('rombel')
                ->where('peserta_didik_id', $pd->id)
                ->whereHas('rombel', fn($q) => $q->where('periode_ajaran_id', $periode->id))
                ->get()
                ->pluck('rombel');

            $rombelIds = $rombels->pluck('id')->toArray();

            if ($rombelIds) {
                $mapels = \App\Models\GuruMapelRombel::with('mapel')
                    ->whereIn('rombel_id', $rombelIds)
                    ->where('periode_ajaran_id', $periode->id)
                    ->get()
                    ->pluck('mapel')
                    ->unique('id')
                    ->sortBy('nama')
                    ->values();

                $tugasQuery = Tugas::with(['guruMapelRombel.mapel', 'guruMapelRombel.rombel'])
                    ->whereHas('guruMapelRombel', fn($q) => $q
                        ->whereIn('rombel_id', $rombelIds)
                        ->where('periode_ajaran_id', $periode->id)
                    )
                    ->when($this->search, fn($q) => $q->where('judul', 'like', '%' . $this->search . '%'))
                    ->when($this->filterMapel, fn($q) => $q->whereHas(
                        'guruMapelRombel', fn($s) => $s->where('mapel_id', $this->filterMapel)
                    ))
                    ->orderByDesc('deadline');

                $allTugasIds = (clone $tugasQuery)->pluck('id')->toArray();

                $mySubmisiByTugasId = TugasSubmisi::where('peserta_didik_id', $pd->id)
                    ->whereIn('tugas_id', $allTugasIds)
                    ->get()
                    ->keyBy('tugas_id');

                // Status filter (applied in PHP after fetching submissions)
                if ($this->filterStatus) {
                    $filteredIds = collect($allTugasIds)->filter(function ($id) use ($mySubmisiByTugasId) {
                        $sub = $mySubmisiByTugasId[$id] ?? null;
                        return $this->matchesStatusFilter($sub, $id);
                    })->values()->toArray();

                    $tugasQuery->whereIn('id', $filteredIds);
                }

                $tugas = $tugasQuery->paginate($this->perPage);
            }
        }

        return view('livewire.peserta-didik.tugas.daftar-tugas-p-d', compact(
            'rombels', 'mapels', 'tugas', 'mySubmisiByTugasId'
        ));
    }

    private function matchesStatusFilter(?TugasSubmisi $sub, int $tugasId): bool
    {
        $tugas    = Tugas::find($tugasId);
        $deadline = $tugas?->deadline;

        return match($this->filterStatus) {
            'belum'     => ! $sub && $deadline && ! $deadline->isPast(),
            'terlewat'  => ! $sub && $deadline && $deadline->isPast(),
            'tepat'     => $sub && $deadline && $sub->waktu_submit->lte($deadline),
            'terlambat' => $sub && $deadline && $sub->waktu_submit->gt($deadline),
            default     => true,
        };
    }
}
