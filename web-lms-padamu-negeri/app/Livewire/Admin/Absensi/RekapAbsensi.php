<?php

namespace App\Livewire\Admin\Absensi;

use App\Models\AbsensiDetail;
use App\Models\PesertaDidik;
use App\Models\Rombel;
use App\Models\SesiAbsensi;
use App\Services\PeriodeService;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app', ['pageTitle' => 'Rekap Absensi'])]
#[Title('Rekap Absensi')]
class RekapAbsensi extends Component
{
    use WithPagination;

    public string $filterRombelId = '';
    public string $filterTanggalMulai = '';
    public string $filterTanggalAkhir = '';
    public string $search = '';
    public int    $perPage = 15;

    public function mount(): void
    {
        $this->filterTanggalMulai = today()->startOfWeek()->format('Y-m-d');
        $this->filterTanggalAkhir = today()->format('Y-m-d');
    }

    public function updatingSearch(): void   { $this->resetPage(); }
    public function updatingFilterRombelId(): void { $this->resetPage(); }
    public function updatingFilterTanggalMulai(): void { $this->resetPage(); }
    public function updatingFilterTanggalAkhir(): void { $this->resetPage(); }

    public function resetFilter(): void
    {
        $this->filterRombelId     = '';
        $this->filterTanggalMulai = today()->startOfWeek()->format('Y-m-d');
        $this->filterTanggalAkhir = today()->format('Y-m-d');
        $this->search             = '';
        $this->resetPage();
    }

    public function render(): View
    {
        $periode = app(PeriodeService::class)->getSelected();
        $rombels = collect();

        if ($periode) {
            $rombels = Rombel::where('periode_ajaran_id', $periode->id)
                ->orderBy('nama')
                ->get();
        }

        // Sesi-sesi dengan filter
        $sesiQuery = SesiAbsensi::with([
                'guruMapelRombel.mapel',
                'guruMapelRombel.rombel',
                'guruMapelRombel.guru',
                'detail',
            ])
            ->when($periode, fn($q) =>
                $q->whereHas('guruMapelRombel', fn($sub) => $sub->where('periode_ajaran_id', $periode->id))
            )
            ->when($this->filterRombelId, fn($q) =>
                $q->whereHas('guruMapelRombel', fn($sub) => $sub->where('rombel_id', $this->filterRombelId))
            )
            ->when($this->filterTanggalMulai, fn($q) => $q->whereDate('tanggal', '>=', $this->filterTanggalMulai))
            ->when($this->filterTanggalAkhir, fn($q) => $q->whereDate('tanggal', '<=', $this->filterTanggalAkhir))
            ->when($this->search, function ($q) {
                $s = $this->search;
                $q->whereHas('guruMapelRombel', function ($sub) use ($s) {
                    $sub->whereHas('mapel',  fn($m) => $m->where('nama', 'like', "%$s%"))
                        ->orWhereHas('rombel', fn($r) => $r->where('nama', 'like', "%$s%"))
                        ->orWhereHas('guru',   fn($g) => $g->where('nama_lengkap', 'like', "%$s%"));
                });
            })
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc');

        $sesis = $sesiQuery->paginate($this->perPage);

        return view('livewire.admin.absensi.rekap-absensi', compact('rombels', 'sesis', 'periode'));
    }
}
