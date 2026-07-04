<?php

namespace App\Livewire\PesertaDidik\Cbt;

use App\Models\Cbt;
use App\Models\GuruMapelRombel;
use App\Models\HasilCbt;
use App\Models\PesertaDidikRombel;
use App\Services\PeriodeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app', ['pageTitle' => 'CBT'])]
#[Title('CBT')]
class DaftarCbtTersedia extends Component
{
    use WithPagination;

    #[Url] public string $search       = '';
    #[Url] public string $filterMapel  = '';
    #[Url] public string $filterStatus = '';
    #[Url] public int    $perPage      = 12;

    public function updatingSearch(): void       { $this->resetPage(); }
    public function updatingFilterMapel(): void  { $this->resetPage(); }
    public function updatingFilterStatus(): void { $this->resetPage(); }
    public function updatingPerPage(): void      { $this->resetPage(); }

    /** Status pengerjaan CBT dari sudut pandang PD. */
    private function computeState(Cbt $cbt, ?HasilCbt $hasil): string
    {
        $now     = now();
        $mulai   = $cbt->tanggal_mulai;
        $selesai = $cbt->tanggal_mulai?->copy()->addMinutes($cbt->durasi_menit);

        if ($hasil && $hasil->waktu_submit) {
            return 'sudah';
        }
        if ($now->lt($mulai)) {
            return 'terjadwal';
        }
        if ($now->between($mulai, $selesai)) {
            return 'berlangsung';
        }
        return 'terlewat';
    }

    public function render(): View
    {
        $pd      = Auth::user()?->pesertaDidik;
        $periode = app(PeriodeService::class)->getSelected();

        $mapels     = collect();
        $cbt        = collect();
        $myHasil    = collect();

        if ($pd && $periode) {
            $rombelIds = PesertaDidikRombel::where('peserta_didik_id', $pd->id)
                ->whereHas('rombel', fn ($q) => $q->where('periode_ajaran_id', $periode->id))
                ->pluck('rombel_id')
                ->toArray();

            if ($rombelIds) {
                $mapels = GuruMapelRombel::with('mapel')
                    ->whereIn('rombel_id', $rombelIds)
                    ->where('periode_ajaran_id', $periode->id)
                    ->get()
                    ->pluck('mapel')
                    ->unique('id')
                    ->sortBy('nama')
                    ->values();

                $baseQuery = Cbt::with(['guruMapelRombel.mapel'])
                    ->withCount('soal')
                    ->whereHas('guruMapelRombel', fn ($q) => $q
                        ->whereIn('rombel_id', $rombelIds)
                        ->where('periode_ajaran_id', $periode->id))
                    ->when($this->search, fn ($q) => $q->where('nama_ujian', 'like', '%' . $this->search . '%'))
                    ->when($this->filterMapel, fn ($q) => $q->whereHas(
                        'guruMapelRombel', fn ($s) => $s->where('mapel_id', $this->filterMapel)))
                    ->orderByDesc('tanggal_mulai');

                $allCbt  = (clone $baseQuery)->get();
                $myHasil = HasilCbt::where('peserta_didik_id', $pd->id)
                    ->whereIn('cbt_id', $allCbt->pluck('id'))
                    ->get()
                    ->keyBy('cbt_id');

                if ($this->filterStatus) {
                    $ids = $allCbt
                        ->filter(fn ($c) => $this->computeState($c, $myHasil[$c->id] ?? null) === $this->filterStatus)
                        ->pluck('id')
                        ->toArray();
                    $baseQuery->whereIn('id', $ids);
                }

                $cbt = $baseQuery->paginate($this->perPage);
            }
        }

        return view('livewire.peserta-didik.cbt.daftar-cbt-tersedia', compact(
            'mapels', 'cbt', 'myHasil'
        ));
    }
}
