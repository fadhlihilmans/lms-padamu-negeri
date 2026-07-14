<?php

namespace App\Livewire\Admin\MasterData;

use App\Models\Rombel;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app', ['pageTitle' => 'Detail Rombel'])]
#[Title('Detail Rombel')]
class DetailRombel extends Component
{
    public Rombel $rombel;

    public function mount(int $id): void
    {
        $this->rombel = Rombel::with([
            'wilayah',
            'paket',
            'tingkat',
            'waliKelas',
            'pesertaDidikRombel.pesertaDidik',
            'guruMapelRombel.guru',
            'guruMapelRombel.mapel',
        ])->findOrFail($id);
    }

    public function render(): View
    {
        return view('livewire.admin.master-data.detail-rombel');
    }
}
