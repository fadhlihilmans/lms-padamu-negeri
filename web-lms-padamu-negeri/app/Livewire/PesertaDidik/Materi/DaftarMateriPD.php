<?php

namespace App\Livewire\PesertaDidik\Materi;

use App\Models\Materi;
use App\Models\PesertaDidikRombel;
use App\Services\PeriodeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app', ['pageTitle' => 'Materi'])]
#[Title('Materi')]
class DaftarMateriPD extends Component
{
    use WithPagination;

    #[Url] public string $search      = '';
    #[Url] public int    $perPage     = 15;
    #[Url] public string $filterMapel = '';

    public function updatingSearch(): void      { $this->resetPage(); }
    public function updatingPerPage(): void     { $this->resetPage(); }
    public function updatingFilterMapel(): void { $this->resetPage(); }

    public function render(): View
    {
        $pd      = Auth::user()->pesertaDidik;
        $periode = app(PeriodeService::class)->getSelected();

        $rombels   = collect();
        $rombelIds = [];
        $mapels    = collect();
        $materi    = collect();

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

                $materi = Materi::with(['guruMapelRombel.mapel', 'lampiran'])
                    ->whereHas('guruMapelRombel', function ($q) use ($rombelIds, $periode) {
                        $q->whereIn('rombel_id', $rombelIds)
                          ->where('periode_ajaran_id', $periode->id);
                    })
                    ->when($this->search, fn($q) => $q->where('judul', 'like', '%' . $this->search . '%'))
                    ->when($this->filterMapel, fn($q) => $q->whereHas(
                        'guruMapelRombel', fn($s) => $s->where('mapel_id', $this->filterMapel)
                    ))
                    ->latest()
                    ->paginate($this->perPage);
            }
        }

        return view('livewire.peserta-didik.materi.daftar-materi-p-d', compact(
            'pd', 'rombels', 'mapels', 'materi'
        ));
    }
}
