<?php

namespace App\Livewire\Guru\Penilaian;

use App\Models\Guru;
use App\Models\GuruMapelRombel;
use App\Models\PesertaDidikRombel;
use App\Models\Rapor;
use App\Models\RaporNilaiMapel;
use App\Services\ErrorLogService;
use App\Services\PeriodeService;
use App\Services\RaporService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app', ['pageTitle' => 'Progres Rapor'])]
#[Title('Progres Rapor')]
class ProgresRapor extends Component
{
    public string $rombelId    = '';
    public string $catatanWali = '';
    public bool   $showPublish = false;

    private function getGuru(): ?Guru
    {
        return Auth::user()?->guru;
    }

    private function waliRombels()
    {
        $guru    = $this->getGuru();
        $periode = app(PeriodeService::class)->getSelected();

        if (! $guru || ! $periode) {
            return collect();
        }

        return \App\Models\Rombel::query()
            ->where('wali_kelas_id', $guru->id)
            ->where('tahun_ajaran', $periode->tahun_ajaran)
            ->orderBy('nama')
            ->get();
    }

    public function mount(): void
    {
        $first = $this->waliRombels()->first();
        if ($first) {
            $this->rombelId = (string) $first->id;
            $this->loadCatatan();
        }
    }

    public function updatedRombelId(): void
    {
        $this->loadCatatan();
    }

    private function pdIds(): array
    {
        if (! $this->rombelId) {
            return [];
        }
        return PesertaDidikRombel::where('rombel_id', $this->rombelId)
            ->pluck('peserta_didik_id')->all();
    }

    private function loadCatatan(): void
    {
        $this->catatanWali = '';
        $periode = app(PeriodeService::class)->getSelected();

        $rapor = Rapor::where('periode_ajaran_id', $periode?->id)
            ->where('rombel_id', $this->rombelId)
            ->whereNotNull('catatan_wali_kelas')
            ->first();

        $this->catatanWali = $rapor?->catatan_wali_kelas ?? '';
    }

    public function saveCatatan(): void
    {
        $periode = app(PeriodeService::class)->getSelected();
        $pdIds   = $this->pdIds();

        if (empty($pdIds) || ! $periode) {
            return;
        }

        try {
            DB::transaction(function () use ($periode, $pdIds) {
                foreach ($pdIds as $pid) {
                    $rapor = Rapor::firstOrCreate(
                        ['peserta_didik_id' => $pid, 'periode_ajaran_id' => $periode->id],
                        ['rombel_id' => $this->rombelId, 'status' => 'draft'],
                    );
                    $rapor->update(['catatan_wali_kelas' => trim($this->catatanWali) ?: null]);
                }
            });
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Catatan wali kelas tersimpan.']);
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Simpan Catatan Wali', $th);
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Terjadi kesalahan, silakan coba lagi.']);
        }
    }

    public function publish(): void
    {
        $this->showPublish = false;
        $periode = app(PeriodeService::class)->getSelected();

        // Otorisasi + kelengkapan dihitung ulang.
        [$allComplete] = $this->progress();
        if (! $allComplete) {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Rapor belum lengkap, tidak dapat diterbitkan.']);
            return;
        }

        try {
            Rapor::where('periode_ajaran_id', $periode->id)
                ->where('rombel_id', $this->rombelId)
                ->update(['status' => 'terbit', 'diterbitkan_oleh' => Auth::id()]);

            $this->dispatch('notify', ['type' => 'success', 'message' => 'Rapor berhasil diterbitkan.']);
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Terbitkan Rapor', $th);
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Terjadi kesalahan, silakan coba lagi.']);
        }
    }

    /** @return array{0: bool, 1: array, 2: int, 3: int, 4: string} [allComplete, mapelRows, lengkapCount, totalMapel, status] */
    private function progress(): array
    {
        $periode = app(PeriodeService::class)->getSelected();
        $pdIds   = $this->pdIds();
        $totalPd = count($pdIds);

        $gmrs = GuruMapelRombel::with(['mapel', 'guru'])
            ->where('rombel_id', $this->rombelId)
            ->where('tahun_ajaran', $periode?->tahun_ajaran)
            ->get();

        $rapors   = Rapor::where('periode_ajaran_id', $periode?->id)
            ->where('rombel_id', $this->rombelId)
            ->whereIn('peserta_didik_id', $pdIds)
            ->get();
        $raporIds = $rapors->pluck('id');

        $komCount = count(RaporService::KOMPONEN);

        $rnmByMapel = $raporIds->isEmpty() ? collect() : RaporNilaiMapel::whereIn('rapor_id', $raporIds)
            ->whereIn('mapel_id', $gmrs->pluck('mapel_id'))
            ->withCount('komponen')
            ->get()
            ->groupBy('mapel_id');

        $mapelRows = $gmrs->map(function ($g) use ($rnmByMapel, $komCount, $totalPd) {
            $rnms     = $rnmByMapel[$g->mapel_id] ?? collect();
            $complete = $rnms->filter(fn ($r) => $r->komponen_count >= $komCount)->count();
            return [
                'mapel'   => $g->mapel,
                'guru'    => $g->guru,
                'lengkap' => $totalPd > 0 && $complete >= $totalPd,
            ];
        });

        $totalMapel   = $gmrs->count();
        $lengkapCount = $mapelRows->where('lengkap', true)->count();
        $allComplete  = $totalMapel > 0 && $totalPd > 0 && $lengkapCount === $totalMapel;

        $status = $rapors->isNotEmpty() && $rapors->every(fn ($r) => $r->status === 'terbit') ? 'terbit' : 'draft';

        return [$allComplete, $mapelRows->values()->all(), $lengkapCount, $totalMapel, $status];
    }

    public function render(): View
    {
        $rombels = $this->waliRombels();
        $rombel  = $this->rombelId ? $rombels->firstWhere('id', (int) $this->rombelId) : null;
        $periode = app(PeriodeService::class)->getSelected();

        [$allComplete, $mapelRows, $lengkapCount, $totalMapel, $status] = $rombel
            ? $this->progress()
            : [false, [], 0, 0, 'draft'];

        return view('livewire.guru.penilaian.progres-rapor', compact(
            'rombels', 'rombel', 'periode', 'allComplete', 'mapelRows', 'lengkapCount', 'totalMapel', 'status'
        ));
    }
}
