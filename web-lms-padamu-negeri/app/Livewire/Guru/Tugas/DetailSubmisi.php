<?php

namespace App\Livewire\Guru\Tugas;

use App\Models\PesertaDidik;
use App\Models\PesertaDidikRombel;
use App\Models\Tugas;
use App\Models\TugasSubmisi;
use App\Services\ErrorLogService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app', ['pageTitle' => 'Detail Submisi'])]
#[Title('Detail Submisi')]
class DetailSubmisi extends Component
{
    public Tugas         $tugas;
    public PesertaDidik  $pd;
    public ?TugasSubmisi $submisi = null;

    public string $nilaiInput = '';

    public function mount(int $tugasId, int $pdId): void
    {
        $tugas = Tugas::with([
            'guruMapelRombel.mapel',
            'guruMapelRombel.rombel',
        ])->findOrFail($tugasId);

        $guru = Auth::user()?->guru;
        $gmr  = $tugas->guruMapelRombel;
        abort_unless($guru && $gmr && $gmr->guru_id === $guru->id, 403);

        $pd = PesertaDidik::findOrFail($pdId);
        abort_unless(
            PesertaDidikRombel::where('peserta_didik_id', $pd->id)
                ->where('rombel_id', $gmr->rombel_id)
                ->exists(),
            403
        );

        $this->tugas   = $tugas;
        $this->pd      = $pd;
        $this->submisi = TugasSubmisi::where('tugas_id', $tugas->id)
            ->where('peserta_didik_id', $pd->id)
            ->first();

        $this->nilaiInput = (string) ($this->submisi?->nilai ?? '');
    }

    public function saveNilai(): void
    {
        if (! $this->submisi) return;

        $this->validate([
            'nilaiInput' => ['required', 'integer', 'min:0', 'max:100'],
        ], [
            'nilaiInput.required' => 'Nilai wajib diisi.',
            'nilaiInput.integer'  => 'Nilai harus bilangan bulat.',
            'nilaiInput.min'      => 'Nilai minimal 0.',
            'nilaiInput.max'      => 'Nilai maksimal 100.',
        ]);

        try {
            $this->submisi->update(['nilai' => (int) $this->nilaiInput]);
            $this->submisi->refresh();
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Nilai berhasil disimpan.']);
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Simpan Nilai Detail Submisi', $th);
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Gagal menyimpan nilai.']);
        }
    }

    public function render(): View
    {
        return view('livewire.guru.tugas.detail-submisi');
    }
}
