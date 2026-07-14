<?php

namespace App\Livewire\PesertaDidik;

use App\Models\JadwalPelajaran;
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
        $periode    = app(\App\Services\PeriodeService::class)->getSelected();

        // Rombel HANYA dari Tahun Ajaran yang sedang dilihat (bukan seluruh histori).
        // Karena 1 PD = 1 rombel per TA, daftar ini normalnya berisi tepat satu rombel.
        $rombels = collect();
        if ($pd && $periode) {
            $rombels = Rombel::whereHas('pesertaDidikRombel', fn($q) => $q->where('peserta_didik_id', $pd->id))
                ->where('tahun_ajaran', $periode->tahun_ajaran)
                ->orderBy('nama')
                ->get();

            // Pilihan lama bisa jadi milik TA lain (mis. setelah ganti periode) → reset.
            if (! $rombels->contains('id', (int) $this->filterRombelId)) {
                $this->filterRombelId = (string) ($rombels->first()->id ?? '');
            }
        }

        $jadwalByHari   = collect();
        $rombelSelected = null;

        if ($this->filterRombelId && $periode) {
            $rombelSelected = $rombels->firstWhere('id', (int) $this->filterRombelId);
            if ($rombelSelected) {
                $jadwalRaw = JadwalPelajaran::with(['guruMapelRombel.mapel', 'guruMapelRombel.guru'])
                    // Jadwal berlaku PER SEMESTER. Tanpa filter ini, jadwal ganjil &
                    // genap tampil bersamaan → itu penyebab "jadwal duplikat".
                    ->where('periode_ajaran_id', $periode->id)
                    ->whereHas('guruMapelRombel', fn($q) => $q->where('rombel_id', $rombelSelected->id))
                    ->orderByRaw("FIELD(hari, 'senin','selasa','rabu','kamis','jumat','sabtu','minggu')")
                    ->orderBy('jam_mulai')
                    ->get();
                $jadwalByHari = $jadwalRaw->groupBy('hari');
            }
        }

        return view('livewire.peserta-didik.jadwal-peserta-didik', compact(
            'pd', 'rombels', 'rombelSelected', 'jadwalByHari', 'hariOrder', 'hariLabel', 'periode'
        ));
    }
}
