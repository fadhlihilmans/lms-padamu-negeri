<?php

namespace App\Livewire\Admin\Penilaian;

use App\Models\GuruMapelRombel;
use App\Models\PesertaDidikRombel;
use App\Models\Rapor;
use App\Models\RaporNilaiMapel;
use App\Models\Rombel;
use App\Services\PeriodeService;
use App\Services\RaporService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Rekap Penilaian (read-only, Admin).
 * Memantau progres input nilai & status rapor tiap rombel pada periode aktif.
 * Tidak mengubah data apa pun — input nilai tetap milik Guru/Wali Kelas.
 */
#[Layout('components.layouts.app', ['pageTitle' => 'Rekap Penilaian'])]
#[Title('Rekap Penilaian')]
class RekapPenilaian extends Component
{
    use WithPagination;

    #[Url(as: 'q', keep: false)]
    public string $search = '';

    #[Url]
    public string $status = ''; // '', 'belum', 'draft', 'terbit'

    #[Url]
    public int $perPage = 10;

    public function updating($name): void
    {
        if (in_array($name, ['search', 'status', 'perPage'], true)) {
            $this->resetPage();
        }
    }

    public function resetFilter(): void
    {
        $this->reset(['search', 'status']);
        $this->resetPage();
    }

    public function render(): View
    {
        $periode = app(PeriodeService::class)->getSelected();

        if (! $periode) {
            return view('livewire.admin.penilaian.rekap-penilaian', [
                'periode'   => null,
                'paginator' => null,
            ]);
        }

        $rombels = Rombel::query()
            ->where('periode_ajaran_id', $periode->id)
            ->when($this->search !== '', fn ($q) => $q->where('nama', 'like', '%' . $this->search . '%'))
            ->with(['paket', 'waliKelas'])
            ->orderBy('nama')
            ->get();

        // Progres dihitung di aplikasi (bukan DB), lalu difilter & dipaginasi manual
        // agar jumlah halaman konsisten dengan filter status.
        $rows = $this->buildRows($rombels, $periode->id);

        if ($this->status !== '') {
            $rows = $rows->where('status', $this->status)->values();
        }

        $page      = Paginator::resolveCurrentPage();
        $paginator = new LengthAwarePaginator(
            $rows->forPage($page, $this->perPage)->values(),
            $rows->count(),
            $this->perPage,
            $page,
            ['path' => Paginator::resolveCurrentPath(), 'pageName' => 'page'],
        );

        return view('livewire.admin.penilaian.rekap-penilaian', [
            'periode'   => $periode,
            'paginator' => $paginator,
        ]);
    }

    /**
     * Hitung progres penilaian untuk sekumpulan rombel (batch, hindari N+1).
     */
    private function buildRows($rombels, int $periodeId)
    {
        $ids = $rombels->pluck('id');
        if ($ids->isEmpty()) {
            return collect();
        }

        $komCount = count(RaporService::KOMPONEN);

        $pdCount = PesertaDidikRombel::whereIn('rombel_id', $ids)
            ->selectRaw('rombel_id, COUNT(*) as total')
            ->groupBy('rombel_id')
            ->pluck('total', 'rombel_id');

        $gmrByRombel = GuruMapelRombel::whereIn('rombel_id', $ids)
            ->where('periode_ajaran_id', $periodeId)
            ->with('mapel')
            ->get()
            ->groupBy('rombel_id');

        $rapors = Rapor::whereIn('rombel_id', $ids)
            ->where('periode_ajaran_id', $periodeId)
            ->get(['id', 'rombel_id', 'status']);

        $raporByRombel   = $rapors->groupBy('rombel_id');
        $raporIdToRombel = $rapors->pluck('rombel_id', 'id');

        $rnmByRombelMapel = $rapors->isEmpty()
            ? collect()
            : RaporNilaiMapel::whereIn('rapor_id', $rapors->pluck('id'))
                ->withCount('komponen')
                ->get(['id', 'rapor_id', 'mapel_id'])
                ->groupBy(fn ($r) => $raporIdToRombel[$r->rapor_id] . '-' . $r->mapel_id);

        return $rombels->map(function ($rombel) use ($pdCount, $gmrByRombel, $raporByRombel, $rnmByRombelMapel, $komCount) {
            $totalPd    = (int) ($pdCount[$rombel->id] ?? 0);
            $gmrs       = $gmrByRombel[$rombel->id] ?? collect();
            $totalMapel = $gmrs->count();

            $lengkapMapel = 0;
            foreach ($gmrs as $g) {
                $rnms     = $rnmByRombelMapel[$rombel->id . '-' . $g->mapel_id] ?? collect();
                $complete = $rnms->filter(fn ($r) => $r->komponen_count >= $komCount)->count();
                if ($totalPd > 0 && $complete >= $totalPd) {
                    $lengkapMapel++;
                }
            }

            $rombelRapors = $raporByRombel[$rombel->id] ?? collect();
            if ($rombelRapors->isNotEmpty() && $rombelRapors->every(fn ($r) => $r->status === 'terbit')) {
                $status = 'terbit';
            } elseif ($rombelRapors->isEmpty()) {
                $status = 'belum';
            } else {
                $status = 'draft';
            }

            $pct = $totalMapel > 0 ? (int) round($lengkapMapel / $totalMapel * 100) : 0;

            return (object) [
                'rombel'       => $rombel,
                'totalPd'      => $totalPd,
                'totalMapel'   => $totalMapel,
                'lengkapMapel' => $lengkapMapel,
                'pct'          => $pct,
                'status'       => $status,
            ];
        });
    }
}
