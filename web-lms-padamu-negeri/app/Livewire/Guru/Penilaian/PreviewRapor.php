<?php

namespace App\Livewire\Guru\Penilaian;

use App\Models\PesertaDidikRombel;
use App\Models\Rombel;
use App\Services\ErrorLogService;
use App\Services\PeriodeService;
use App\Services\RaporService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app', ['pageTitle' => 'Pratinjau Rapor'])]
#[Title('Pratinjau Rapor')]
class PreviewRapor extends Component
{
    public string $mode = 'wali';       // 'wali' | 'pd'
    public string $rombelId = '';
    public string $pesertaDidikId = '';

    public function mount(): void
    {
        $user = Auth::user();

        if ($user?->hasRole('peserta_didik')) {
            $this->mode           = 'pd';
            $this->pesertaDidikId = (string) ($user->pesertaDidik?->id ?? '');
            return;
        }

        // Wali kelas: pilih rombel yang di-wali + PD pertama.
        $this->mode = 'wali';
        $first = $this->waliRombels()->first();
        if ($first) {
            $this->rombelId = (string) $first->id;
            $pd = $this->pesertaList()->first();
            $this->pesertaDidikId = $pd ? (string) $pd->id : '';
        }
    }

    public function updatedRombelId(): void
    {
        $pd = $this->pesertaList()->first();
        $this->pesertaDidikId = $pd ? (string) $pd->id : '';
    }

    private function waliRombels()
    {
        $guru    = Auth::user()?->guru;
        $periode = app(PeriodeService::class)->getSelected();
        if (! $guru || ! $periode) {
            return collect();
        }
        return Rombel::where('wali_kelas_id', $guru->id)
            ->where('tahun_ajaran', $periode->tahun_ajaran)
            ->orderBy('nama')->get();
    }

    private function pesertaList()
    {
        if (! $this->rombelId) {
            return collect();
        }
        return PesertaDidikRombel::with('pesertaDidik')
            ->where('rombel_id', $this->rombelId)
            ->get()->pluck('pesertaDidik')->filter()
            ->sortBy('nama_lengkap')->values();
    }

    /** Otorisasi akses rapor PD tertentu. */
    private function canAccess(int $pdId): bool
    {
        $user = Auth::user();

        if ($user?->hasRole('peserta_didik')) {
            return $user->pesertaDidik?->id === $pdId;
        }

        // Wali kelas: PD harus anggota rombel yang di-wali.
        $rombelIds = $this->waliRombels()->pluck('id');
        return PesertaDidikRombel::where('peserta_didik_id', $pdId)
            ->whereIn('rombel_id', $rombelIds)->exists();
    }

    private function raporData(): ?array
    {
        $periode = app(PeriodeService::class)->getSelected();
        if (! $this->pesertaDidikId || ! $periode) {
            return null;
        }

        $pdId = (int) $this->pesertaDidikId;
        if (! $this->canAccess($pdId)) {
            return null;
        }

        return app(RaporService::class)->buildRapor($pdId, $periode->id);
    }

    public function downloadPdf()
    {
        $data = $this->raporData();
        if (! $data) {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Rapor belum tersedia.']);
            return;
        }

        // Peserta didik hanya boleh unduh bila sudah terbit.
        if ($this->mode === 'pd' && $data['status'] !== 'terbit') {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Rapor belum diterbitkan.']);
            return;
        }

        try {
            $pdf  = Pdf::loadView('pdf.rapor', $data)->setPaper('a4');
            $nama = Str::slug($data['pd']?->nama_lengkap ?? 'rapor');

            return response()->streamDownload(
                fn () => print($pdf->output()),
                "rapor-{$nama}.pdf",
                ['Content-Type' => 'application/pdf'],
            );
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Cetak Rapor PDF', $th);
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Gagal membuat PDF.']);
        }
    }

    public function render(): View
    {
        $data     = $this->raporData();
        $rombels  = $this->mode === 'wali' ? $this->waliRombels() : collect();
        $peserta  = $this->mode === 'wali' ? $this->pesertaList() : collect();

        // PD hanya lihat bila terbit.
        $lockedForPd = $this->mode === 'pd' && $data && $data['status'] !== 'terbit';

        return view('livewire.guru.penilaian.preview-rapor', compact(
            'data', 'rombels', 'peserta', 'lockedForPd'
        ));
    }
}
