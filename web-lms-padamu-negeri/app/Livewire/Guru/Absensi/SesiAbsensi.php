<?php

namespace App\Livewire\Guru\Absensi;

use App\Models\AbsensiDetail;
use App\Models\GuruMapelRombel;
use App\Models\PesertaDidik;
use App\Models\SesiAbsensi as SesiAbsensiModel;
use App\Services\ErrorLogService;
use App\Services\PeriodeService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app', ['pageTitle' => 'Absensi'])]
#[Title('Absensi')]
class SesiAbsensi extends Component
{
    public string $selectedGmrId       = '';
    public ?int   $sesiId              = null;
    public string $filterStatus        = '';
    public bool   $confirmCloseSession = false;

    // Form buka sesi
    public string $formTanggalBuka  = '';
    public string $formJamBuka      = '';
    public string $formTanggalTutup = '';
    public string $formJamTutup     = '';

    public function mount(): void
    {
        $this->formTanggalBuka  = today()->format('Y-m-d');
        $this->formJamBuka      = now()->format('H:i');
        $this->formTanggalTutup = today()->format('Y-m-d');

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
        if (! $this->selectedGmrId) return;

        // Tampilkan sesi hari ini atau yang terjadwal ke depan (belum ditutup)
        $sesi = SesiAbsensiModel::where('guru_mapel_rombel_id', $this->selectedGmrId)
            ->where(function ($q) {
                $q->whereDate('tanggal', today())
                  ->orWhere('tanggal', '>', today());
            })
            ->where('status_sesi', 'terbuka')
            ->latest('tanggal_buka')
            ->first();

        // Fallback: sesi hari ini yang sudah ditutup
        if (! $sesi) {
            $sesi = SesiAbsensiModel::where('guru_mapel_rombel_id', $this->selectedGmrId)
                ->whereDate('tanggal', today())
                ->latest()
                ->first();
        }

        $this->sesiId = $sesi?->id;
    }

    public function openSession(): void
    {
        if (! $this->selectedGmrId) return;

        $this->validate([
            'formTanggalBuka'  => ['required', 'date'],
            'formJamBuka'      => ['required', 'date_format:H:i'],
            'formTanggalTutup' => ['required', 'date'],
            'formJamTutup'     => ['required', 'date_format:H:i'],
        ], [
            'formTanggalBuka.required'  => 'Tanggal buka wajib diisi.',
            'formTanggalBuka.date'      => 'Tanggal buka tidak valid.',
            'formJamBuka.required'      => 'Jam buka wajib diisi.',
            'formTanggalTutup.required' => 'Tanggal tutup wajib diisi.',
            'formTanggalTutup.date'     => 'Tanggal tutup tidak valid.',
            'formJamTutup.required'     => 'Jam tutup wajib diisi.',
            'formJamBuka.date_format'   => 'Format jam tidak valid.',
            'formJamTutup.date_format'  => 'Format jam tidak valid.',
        ]);

        $bukaPada  = Carbon::parse("{$this->formTanggalBuka} {$this->formJamBuka}:00");
        $tutupPada = Carbon::parse("{$this->formTanggalTutup} {$this->formJamTutup}:00");

        if ($tutupPada->lte($bukaPada)) {
            $this->addError('formJamTutup', 'Waktu tutup harus lebih dari waktu buka.');
            return;
        }

        // Cegah duplikat sesi pada tanggal yang sama untuk GMR yang sama
        $existing = SesiAbsensiModel::where('guru_mapel_rombel_id', $this->selectedGmrId)
            ->whereDate('tanggal', $bukaPada->toDateString())
            ->first();

        if ($existing) {
            $this->sesiId = $existing->id;
            $this->dispatch('notify', type: 'warning', message: 'Sesi absensi pada tanggal tersebut sudah ada.');
            return;
        }

        try {
            $sesi = SesiAbsensiModel::create([
                'guru_mapel_rombel_id' => $this->selectedGmrId,
                'tanggal'              => $bukaPada->toDateString(),
                'tanggal_buka'         => $bukaPada,
                'tutup_pada'           => $tutupPada,
                'status_sesi'          => 'terbuka',
            ]);
            $this->sesiId = $sesi->id;

            $isScheduled = $bukaPada->isFuture();
            $msg = $isScheduled
                ? 'Sesi dijadwalkan pada ' . $bukaPada->translatedFormat('d M Y, H:i') . '.'
                : 'Sesi absensi berhasil dibuka.';

            $this->dispatch('notify', type: 'success', message: $msg);
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Buka Sesi Absensi', $th);
            $this->dispatch('notify', type: 'error', message: 'Gagal membuka sesi absensi.');
        }
    }

    public function requestCloseSession(): void
    {
        $this->confirmCloseSession = true;
    }

    public function cancelCloseSession(): void
    {
        $this->confirmCloseSession = false;
    }

    public function closeSession(): void
    {
        if (! $this->sesiId) return;

        try {
            SesiAbsensiModel::findOrFail($this->sesiId)->update(['status_sesi' => 'ditutup']);
            $this->confirmCloseSession = false;
            $this->dispatch('notify', type: 'success', message: 'Sesi absensi ditutup.');
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Tutup Sesi Absensi', $th);
            $this->dispatch('notify', type: 'error', message: 'Gagal menutup sesi.');
        }
    }

    public function setStatus(int $pdId, string $status): void
    {
        if (! $this->sesiId) return;

        $allowed = ['hadir', 'izin', 'sakit', 'alpa'];
        if (! in_array($status, $allowed, true)) return;

        try {
            AbsensiDetail::updateOrCreate(
                ['sesi_absensi_id' => $this->sesiId, 'peserta_didik_id' => $pdId],
                [
                    'status'             => $status,
                    'waktu_klik'         => null,
                    'diubah_manual_oleh' => Auth::id(),
                ]
            );
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Set Status Absensi (Guru)', $th);
            $this->dispatch('notify', type: 'error', message: 'Gagal menyimpan status absensi.');
        }
    }

    private function autoCloseIfExpired(SesiAbsensiModel $sesi): void
    {
        if (
            $sesi->status_sesi === 'terbuka'
            && $sesi->tutup_pada
            && $sesi->tutup_pada->isPast()
        ) {
            $sesi->update(['status_sesi' => 'ditutup']);
            $sesi->refresh();
        }
    }

    private function getGuruMapelRombels()
    {
        $guru    = Auth::user()->guru;
        $periode = app(PeriodeService::class)->getSelected();

        if (! $guru || ! $periode) return collect();

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
            $this->autoCloseIfExpired($sesi);

            // Hanya load roster jika sesi sudah aktif (buka <= sekarang)
            $isActive = ! $sesi->tanggal_buka || $sesi->tanggal_buka->lte(now());

            if ($isActive && $sesi->status_sesi === 'terbuka') {
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

                if ($this->filterStatus === 'belum') {
                    $roster = $roster->filter(fn($pd) => ! isset($details[$pd->id]));
                } elseif ($this->filterStatus !== '') {
                    $roster = $roster->filter(fn($pd) => ($details[$pd->id]->status ?? null) === $this->filterStatus);
                }
            } elseif ($sesi->status_sesi === 'ditutup') {
                // Sesi ditutup — tetap load untuk ditampilkan rekap
                $rombelId = $sesi->guruMapelRombel->rombel_id;
                $roster   = PesertaDidik::whereHas('pesertaDidikRombel', fn($q) => $q->where('rombel_id', $rombelId))
                    ->orderBy('nama_lengkap')->get();
                $details  = AbsensiDetail::where('sesi_absensi_id', $sesi->id)->get()->keyBy('peserta_didik_id');

                $stats['total'] = $roster->count();
                $stats['hadir'] = $details->where('status', 'hadir')->count();
                $stats['izin']  = $details->where('status', 'izin')->count();
                $stats['sakit'] = $details->where('status', 'sakit')->count();
                $stats['alpa']  = $details->where('status', 'alpa')->count();
                $stats['belum'] = $stats['total'] - $details->count();

                if ($this->filterStatus === 'belum') {
                    $roster = $roster->filter(fn($pd) => ! isset($details[$pd->id]));
                } elseif ($this->filterStatus !== '') {
                    $roster = $roster->filter(fn($pd) => ($details[$pd->id]->status ?? null) === $this->filterStatus);
                }
            }
        }

        return view('livewire.guru.absensi.sesi-absensi', compact('gmrs', 'sesi', 'roster', 'details', 'stats'));
    }
}
