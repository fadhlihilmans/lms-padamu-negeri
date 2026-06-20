<?php

namespace App\Livewire\Shared;

use App\Models\PeriodeAjaran;
use App\Services\PeriodeService;
use Illuminate\View\View;
use Livewire\Component;

class PeriodeSwitcher extends Component
{
    public function switchPeriod(int $id): void
    {
        app(PeriodeService::class)->setPeriod($id);
        $this->redirect(request()->fullUrl(), navigate: false);
    }

    public function render(): View
    {
        $service  = app(PeriodeService::class);
        $selected = $service->getSelected();

        $daftar = PeriodeAjaran::orderByDesc('tahun_ajaran')
            ->orderByRaw("FIELD(semester, 'genap', 'ganjil')")
            ->get();

        return view('livewire.shared.periode-switcher', [
            'selected'       => $selected,
            'daftar'         => $daftar,
            'isReadOnlyMode' => $service->isReadOnlyMode(),
        ]);
    }
}
