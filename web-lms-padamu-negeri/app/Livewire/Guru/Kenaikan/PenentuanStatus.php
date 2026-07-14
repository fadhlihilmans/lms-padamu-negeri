<?php

namespace App\Livewire\Guru\Kenaikan;

use App\Models\Guru;
use App\Models\KenaikanKelas;
use App\Models\PesertaDidik;
use App\Models\PesertaDidikRombel;
use App\Models\Rombel;
use App\Services\ErrorLogService;
use App\Services\PeriodeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app', ['pageTitle' => 'Kenaikan Kelas'])]
#[Title('Kenaikan Kelas')]
class PenentuanStatus extends Component
{
    public string $rombelId = '';
    public string $search   = '';

    /** decisions[peserta_didik_id] => '' | naik | tinggal | lulus | pindah_paket | pindah_wilayah */
    public array $decisions = [];

    public bool $showConfirm = false;

    public const STATUS = [
        'naik'           => ['label' => 'Naik Tingkat',   'icon' => 'arrow_upward',      'cls' => 'border-[#3c50e0] bg-[#EEF2FF] text-[#3c50e0]'],
        'tinggal'        => ['label' => 'Tinggal',        'icon' => 'refresh',           'cls' => 'border-amber-400 bg-amber-50 text-amber-700'],
        'lulus'          => ['label' => 'Lulus',          'icon' => 'workspace_premium', 'cls' => 'border-green-400 bg-green-50 text-green-700'],
        'pindah_paket'   => ['label' => 'Pindah Paket',   'icon' => 'swap_horiz',        'cls' => 'border-purple-300 bg-purple-50 text-purple-700'],
        'pindah_wilayah' => ['label' => 'Pindah Wilayah', 'icon' => 'swap_horiz',        'cls' => 'border-purple-300 bg-purple-50 text-purple-700'],
    ];

    private function getGuru(): ?Guru
    {
        return Auth::user()?->guru;
    }

    /** Rombel yang di-wali guru pada periode terpilih. */
    private function waliRombels()
    {
        $guru    = $this->getGuru();
        $periode = app(PeriodeService::class)->getSelected();

        if (! $guru || ! $periode) {
            return collect();
        }

        return Rombel::with(['paket'])
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
            $this->loadDecisions();
        }
    }

    public function updatedRombelId(): void
    {
        $this->loadDecisions();
    }

    /** Muat keputusan tersimpan untuk rombel aktif. */
    private function loadDecisions(): void
    {
        $this->decisions = [];
        if (! $this->rombelId) {
            return;
        }

        $existing = KenaikanKelas::where('rombel_asal_id', $this->rombelId)
            ->pluck('status_keputusan', 'peserta_didik_id');

        foreach ($this->pesertaList() as $pd) {
            $this->decisions[$pd->id] = $existing[$pd->id] ?? '';
        }
    }

    /** Peserta didik anggota rombel aktif. */
    private function pesertaList()
    {
        if (! $this->rombelId) {
            return collect();
        }

        return PesertaDidikRombel::with('pesertaDidik')
            ->where('rombel_id', $this->rombelId)
            ->get()
            ->pluck('pesertaDidik')
            ->filter()
            ->sortBy('nama_lengkap')
            ->values();
    }

    /** Tandai semua yang belum diputuskan sebagai Naik Tingkat. */
    public function markAllNaik(): void
    {
        foreach ($this->decisions as $pdId => $status) {
            if ($status === '') {
                $this->decisions[$pdId] = 'naik';
            }
        }
    }

    public function requestSave(): void
    {
        $adaKosong = collect($this->decisions)->contains(fn ($v) => $v === '' || $v === null);
        if ($adaKosong) {
            $this->showConfirm = true;
            return;
        }
        $this->save();
    }

    public function save(): void
    {
        $this->showConfirm = false;

        $rombel = Rombel::find($this->rombelId);
        $guru   = $this->getGuru();

        if (! $rombel || ! $guru || $rombel->wali_kelas_id !== $guru->id) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Anda bukan wali kelas rombel ini.']);
            return;
        }

        try {
            $tersimpan = 0;
            foreach ($this->decisions as $pdId => $status) {
                if ($status === '' || $status === null) {
                    continue; // biarkan yang belum diputuskan
                }

                KenaikanKelas::updateOrCreate(
                    ['peserta_didik_id' => $pdId, 'rombel_asal_id' => $rombel->id],
                    ['status_keputusan' => $status, 'diputuskan_oleh' => Auth::id()],
                );

                // Hanya Lulus yang mengubah status akademik (PRD 6.10).
                PesertaDidik::where('id', $pdId)->update([
                    'status_akademik' => $status === 'lulus' ? 'lulus' : 'aktif',
                ]);

                $tersimpan++;
            }

            $this->dispatch('notify', ['type' => 'success', 'message' => "Keputusan kenaikan tersimpan ({$tersimpan} peserta didik)."]);
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Simpan Kenaikan Kelas', $th);
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Terjadi kesalahan, silakan coba lagi.']);
        }
    }

    public function render(): View
    {
        $rombels = $this->waliRombels();
        $rombel  = $this->rombelId ? $rombels->firstWhere('id', (int) $this->rombelId) : null;

        $peserta = $this->pesertaList()
            ->when($this->search, fn ($c) => $c->filter(fn ($pd) => str_contains(
                strtolower($pd->nama_lengkap . ' ' . $pd->nipd), strtolower($this->search)
            )))
            ->values();

        $total       = count($this->decisions);
        $belum       = collect($this->decisions)->filter(fn ($v) => $v === '' || $v === null)->count();
        $naikCount   = collect($this->decisions)->filter(fn ($v) => $v === 'naik')->count();
        $diputuskan  = $total - $belum;

        return view('livewire.guru.kenaikan.penentuan-status', compact(
            'rombels', 'rombel', 'peserta', 'total', 'belum', 'naikCount', 'diputuskan'
        ));
    }
}
