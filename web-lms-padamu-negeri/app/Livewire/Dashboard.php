<?php

namespace App\Livewire;

use App\Models\Cbt;
use App\Models\JadwalPelajaran;
use App\Models\PesertaDidik;
use App\Models\Rombel;
use App\Models\Tugas;
use App\Models\Guru;
use App\Services\PeriodeService;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    /** Peta nama hari (Carbon 'l') → enum kolom `hari` di jadwal_pelajaran. */
    private const HARI_ENUM = [
        'Sunday' => 'minggu', 'Monday' => 'senin', 'Tuesday' => 'selasa',
        'Wednesday' => 'rabu', 'Thursday' => 'kamis', 'Friday' => 'jumat', 'Saturday' => 'sabtu',
    ];

    public function render()
    {
        $user    = auth()->user();
        $periode = app(PeriodeService::class)->getSelected();
        $hariIni = self::HARI_ENUM[Carbon::now()->format('l')] ?? 'senin';

        $data = [
            'periode'     => $periode,
            'tanggalIndo' => $this->tanggalIndo(),
            'role'        => 'unknown',
        ];

        if ($user?->hasRole('admin')) {
            $data = array_merge($data, $this->dataAdmin($periode));
        } elseif ($user?->hasRole('guru')) {
            $data = array_merge($data, $this->dataGuru($user, $periode, $hariIni));
        } elseif ($user?->hasRole('peserta_didik')) {
            $data = array_merge($data, $this->dataPesertaDidik($user, $hariIni));
        }

        return view('livewire.dashboard', $data);
    }

    private function dataAdmin(?object $periode): array
    {
        $rombelQuery = Rombel::query()
            ->when($periode, fn ($q) => $q->where('periode_ajaran_id', $periode->id));

        return [
            'role'          => 'admin',
            'totalPeserta'  => PesertaDidik::where('status_akademik', 'aktif')->count(),
            'totalGuru'     => Guru::count(),
            'totalRombel'   => (clone $rombelQuery)->count(),
            'rombelTerbaru' => (clone $rombelQuery)
                ->with(['paket', 'waliKelas'])
                ->withCount('pesertaDidikRombel')
                ->orderByDesc('id')
                ->take(5)
                ->get(),
        ];
    }

    private function dataGuru(object $user, ?object $periode, string $hariIni): array
    {
        $guru = $user->guru;

        if (! $guru) {
            return ['role' => 'guru', 'guru' => null, 'jadwalHariIni' => collect(), 'perluDinilai' => collect()];
        }

        $jadwalHariIni = JadwalPelajaran::whereHas('guruMapelRombel', fn ($q) => $q
                ->where('guru_id', $guru->id)
                ->when($periode, fn ($qq) => $qq->where('periode_ajaran_id', $periode->id)))
            ->where('hari', $hariIni)
            ->with(['guruMapelRombel.mapel', 'guruMapelRombel.rombel'])
            ->orderBy('jam_mulai')
            ->get();

        $perluDinilai = Tugas::whereHas('guruMapelRombel', fn ($q) => $q->where('guru_id', $guru->id))
            ->withCount(['submisi as belum_dinilai_count' => fn ($q) => $q->whereNull('nilai')])
            ->with('guruMapelRombel.rombel')
            ->orderByDesc('id')
            ->get()
            ->filter(fn ($t) => $t->belum_dinilai_count > 0)
            ->take(5)
            ->values();

        return [
            'role'          => 'guru',
            'guru'          => $guru,
            'jadwalHariIni' => $jadwalHariIni,
            'perluDinilai'  => $perluDinilai,
        ];
    }

    private function dataPesertaDidik(object $user, string $hariIni): array
    {
        $pd       = $user->pesertaDidik;
        $rombelPd = $pd?->pesertaDidikRombel()->with('rombel.paket')->latest()->first()?->rombel;
        $rombelId = $rombelPd?->id;

        $jadwalHariIni = $rombelId
            ? JadwalPelajaran::whereHas('guruMapelRombel', fn ($q) => $q->where('rombel_id', $rombelId))
                ->where('hari', $hariIni)
                ->with('guruMapelRombel.mapel')
                ->orderBy('jam_mulai')
                ->get()
            : collect();

        $tugasMendatang = $rombelId
            ? Tugas::whereHas('guruMapelRombel', fn ($q) => $q->where('rombel_id', $rombelId))
                ->where('deadline', '>=', now())
                ->with('guruMapelRombel.mapel')
                ->orderBy('deadline')
                ->take(4)
                ->get()
            : collect();

        $cbtMendatang = $rombelId
            ? Cbt::whereHas('guruMapelRombel', fn ($q) => $q->where('rombel_id', $rombelId))
                ->where('tanggal_mulai', '>=', now())
                ->with('guruMapelRombel.mapel')
                ->orderBy('tanggal_mulai')
                ->take(2)
                ->get()
            : collect();

        return [
            'role'           => 'peserta_didik',
            'pd'             => $pd,
            'rombelPd'       => $rombelPd,
            'jadwalHariIni'  => $jadwalHariIni,
            'tugasMendatang' => $tugasMendatang,
            'cbtMendatang'   => $cbtMendatang,
        ];
    }

    private function tanggalIndo(): string
    {
        $hari = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
                 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
        $bulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                  'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $now = Carbon::now();

        return $hari[$now->format('l')] . ', ' . $now->day . ' ' . $bulan[(int) $now->month] . ' ' . $now->year;
    }
}
