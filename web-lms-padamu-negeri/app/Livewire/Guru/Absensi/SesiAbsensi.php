<?php

namespace App\Livewire\Guru\Absensi;

use App\Models\AbsensiDetail;
use App\Models\GuruMapelRombel;
use App\Models\PesertaDidik;
use App\Models\SesiAbsensi as SesiAbsensiModel;
use App\Services\ErrorLogService;
use App\Services\PeriodeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app', ['pageTitle' => 'Absensi'])]
#[Title('Absensi')]
class SesiAbsensi extends Component
{
    public string $selectedGmrId = '';
    public ?int   $sesiId        = null;
    public string $filterStatus  = '';

    public function mount(): void
    {
        // Auto-pilih gmr pertama jika hanya satu
        $gmrs = $this->getGuruMapelRombels();
        if ($gmrs->count() === 1) {
            $this->selectedGmrId = (string) $gmrs->first()->id;
            $this->loadExistingSession();
        }
    }

    public function updatedSelectedGmrId(): void
    {
        $this->sesiId       = null;
        $this->filterStatus = '';
        $this->loadExistingSession();
    }

    private function loadExistingSession(): void
    {
        if (!$this->selectedGmrId) return;

        $sesi = SesiAbsensiModel::where('guru_mapel_rombel_id', $this->selectedGmrId)
            ->whereDate('tanggal', today())
            ->latest()
            ->first();

        $this->sesiId = $sesi?->id;
    }

    public function openSession(): void
    {
        if (!$this->selectedGmrId) return;

        // Cegah duplikat sesi hari ini untuk GMR yang sama
        $existing = SesiAbsensiModel::where('guru_mapel_rombel_id', $this->selectedGmrId)
            ->whereDate('tanggal', today())
            ->first();

        if ($existing) {
            $this->sesiId = $existing->id;
            $this->dispatch('notify', type: 'warning', message: 'Sesi absensi hari ini sudah ada.');
            return;
        }

        try {
            $sesi = SesiAbsensiModel::create([
                'guru_mapel_rombel_id' => $this->selectedGmrId,
                'tanggal'              => today(),
                'status_sesi'          => 'terbuka',
            ]);
            $this->sesiId = $sesi->id;
            $this->dispatch('notify', type: 'success', message: 'Sesi absensi berhasil dibuka.');
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Buka Sesi Absensi', $th);
            $this->dispatch('notify', type: 'error', message: 'Gagal membuka sesi absensi.');
        }
    }

    public function closeSession(): void
    {
        if (!$this->sesiId) return;

        try {
            SesiAbsensiModel::findOrFail($this->sesiId)->update(['status_sesi' => 'ditutup']);
            $this->dispatch('notify', type: 'success', message: 'Sesi absensi ditutup.');
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Tutup Sesi Absensi', $th);
            $this->dispatch('notify', type: 'error', message: 'Gagal menutup sesi.');
        }
    }

    public function setStatus(int $pdId, string $status): void
    {
        if (!$this->sesiId) return;

        $allowed = ['hadir', 'izin', 'sakit', 'alpa'];
        if (!in_array($status, $allowed, true)) return;

        try {
            AbsensiDetail::updateOrCreate(
                ['sesi_absensi_id' => $this->sesiId, 'peserta_didik_id' => $pdId],
                [
                    'status'              => $status,
                    'waktu_klik'          => null,        // manual override by guru
                    'diubah_manual_oleh'  => Auth::id(),
                ]
            );
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Set Status Absensi (Guru)', $th);
            $this->dispatch('notify', type: 'error', message: 'Gagal menyimpan status absensi.');
        }
    }

    private function getGuruMapelRombels()
    {
        $guru    = Auth::user()->guru;
        $periode = app(PeriodeService::class)->getSelected();

        if (!$guru || !$periode) return collect();

        return GuruMapelRombel::where('guru_id', $guru->id)
            ->where('periode_ajaran_id', $periode->id)
            ->with(['mapel', 'rombel'])
            ->get();
    }

    public function render(): View
    {
        $gmrs    = $this->getGuruMapelRombels();
        $sesi    = $this->sesiId ? SesiAbsensiModel::find($this->sesiId) : null;
        $roster  = collect();
        $details = collect();
        $stats   = ['total' => 0, 'hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0, 'belum' => 0];

        if ($sesi) {
            $rombelId = $sesi->guruMapelRombel->rombel_id;

            $query = PesertaDidik::whereHas('pesertaDidikRombel', fn($q) => $q->where('rombel_id', $rombelId))
                ->orderBy('nama_lengkap');

            $roster  = $query->get();
            $details = AbsensiDetail::where('sesi_absensi_id', $sesi->id)
                ->get()
                ->keyBy('peserta_didik_id');

            $stats['total'] = $roster->count();
            $stats['hadir'] = $details->where('status', 'hadir')->count();
            $stats['izin']  = $details->where('status', 'izin')->count();
            $stats['sakit'] = $details->where('status', 'sakit')->count();
            $stats['alpa']  = $details->where('status', 'alpa')->count();
            $stats['belum'] = $stats['total'] - $details->count();

            // Filter roster by status
            if ($this->filterStatus === 'belum') {
                $roster = $roster->filter(fn($pd) => !isset($details[$pd->id]));
            } elseif ($this->filterStatus !== '') {
                $roster = $roster->filter(fn($pd) => ($details[$pd->id]->status ?? null) === $this->filterStatus);
            }
        }

        return view('livewire.guru.absensi.sesi-absensi', compact('gmrs', 'sesi', 'roster', 'details', 'stats'));
    }
}
