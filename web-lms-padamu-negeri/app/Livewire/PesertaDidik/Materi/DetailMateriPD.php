<?php

namespace App\Livewire\PesertaDidik\Materi;

use App\Models\Materi;
use App\Models\PesertaDidikRombel;
use App\Services\PeriodeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app', ['pageTitle' => 'Detail Materi'])]
#[Title('Detail Materi')]
class DetailMateriPD extends Component
{
    public Materi $materi;

    public function mount(int $id): void
    {
        $pd      = Auth::user()->pesertaDidik;
        $periode = app(PeriodeService::class)->getSelected();

        $materi = Materi::with(['guruMapelRombel.mapel', 'guruMapelRombel.guru', 'lampiran'])->findOrFail($id);

        // Pastikan PD terdaftar di rombel materi ini pada periode yang sama
        $rombel = $materi->guruMapelRombel?->rombel_id;
        $boleh  = $pd && $rombel && PesertaDidikRombel::where('peserta_didik_id', $pd->id)
            ->where('rombel_id', $rombel)
            ->exists();

        abort_unless($boleh, 403, 'Anda tidak memiliki akses ke materi ini.');

        $this->materi = $materi;
    }

    public function render(): View
    {
        return view('livewire.peserta-didik.materi.detail-materi-p-d');
    }
}
