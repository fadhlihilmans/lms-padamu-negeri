<?php

namespace App\Livewire\PesertaDidik;

use App\Models\JadwalPelajaran;
use App\Models\PesertaDidikRombel;
use App\Models\Rombel;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app', ['pageTitle' => 'Jadwal Pelajaran'])]
#[Title('Jadwal Pelajaran')]
class JadwalPesertaDidik extends Component
{
    const HARI_ORDER = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];
    const HARI_LABEL = [
        'senin'  => 'Senin',  'selasa' => 'Selasa', 'rabu'   => 'Rabu',
        'kamis'  => 'Kamis',  'jumat'  => 'Jumat',  'sabtu'  => 'Sabtu',
        'minggu' => 'Minggu',
    ];

    public string $filterRombelId = '';

    public function render(): View
    {
        $hariOrder  = self::HARI_ORDER;
        $hariLabel  = self::HARI_LABEL;
        $pd         = Auth::user()->pesertaDidik;

        // Rombel yang diikuti (tidak soft-deleted)
        $rombels = collect();
        if ($pd) {
            $rombels = Rombel::whereHas('pesertaDidikRombel', fn($q) => $q->where('peserta_didik_id', $pd->id))
                ->orderByDesc('tahun_ajaran')
                ->orderBy('nama')
                ->get();

            // Default ke rombel pertama jika belum dipilih
            if (! $this->filterRombelId && $rombels->isNotEmpty()) {
                $this->filterRombelId = $rombels->first()->id;
            }
        }

        $jadwalByHari   = collect();
        $rombelSelected = null;

        if ($this->filterRombelId) {
            $rombelSelected = $rombels->firstWhere('id', $this->filterRombelId);
            if ($rombelSelected) {
                $jadwalRaw = JadwalPelajaran::with(['mapel', 'guru'])
                    ->where('rombel_id', $this->filterRombelId)
                    ->orderByRaw("FIELD(hari, 'senin','selasa','rabu','kamis','jumat','sabtu','minggu')")
                    ->orderBy('jam_mulai')
                    ->get();
                $jadwalByHari = $jadwalRaw->groupBy('hari');
            }
        }

        return view('livewire.peserta-didik.jadwal-peserta-didik', compact(
            'pd', 'rombels', 'rombelSelected', 'jadwalByHari', 'hariOrder', 'hariLabel'
        ));
    }
}
