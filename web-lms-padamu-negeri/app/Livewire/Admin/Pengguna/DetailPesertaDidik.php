<?php

namespace App\Livewire\Admin\Pengguna;

use App\Models\PesertaDidik;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app', ['pageTitle' => 'Detail Peserta Didik'])]
#[Title('Detail Peserta Didik')]
class DetailPesertaDidik extends Component
{
    public PesertaDidik $pesertaDidik;

    public function mount(int $id): void
    {
        $this->pesertaDidik = PesertaDidik::with([
            'user',
            'alamat',
            'ortu',
            'pesertaDidikRombel.rombel.periodeAjaran',
            'pesertaDidikRombel.rombel.paket',
            'pesertaDidikRombel.rombel.tingkat',
        ])->findOrFail($id);
    }

    public function render(): View
    {
        return view('livewire.admin.pengguna.detail-peserta-didik');
    }
}
