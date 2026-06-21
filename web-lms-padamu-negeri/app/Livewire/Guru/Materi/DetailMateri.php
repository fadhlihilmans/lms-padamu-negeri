<?php

namespace App\Livewire\Guru\Materi;

use App\Models\Materi;
use App\Models\GuruMapelRombel;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app', ['pageTitle' => 'Detail Materi'])]
#[Title('Detail Materi')]
class DetailMateri extends Component
{
    public Materi $materi;

    public function mount(int $id): void
    {
        $guru   = Auth::user()->guru;
        $materi = Materi::with(['guruMapelRombel.mapel', 'guruMapelRombel.rombel', 'guruMapelRombel.guru', 'lampiran'])->findOrFail($id);

        // Pastikan materi milik guru ini
        $boleh = $guru && $materi->guruMapelRombel?->guru_id === $guru->id;
        abort_unless($boleh, 403, 'Anda tidak memiliki akses ke materi ini.');

        $this->materi = $materi;
    }

    public function render(): View
    {
        return view('livewire.guru.materi.detail-materi');
    }
}
