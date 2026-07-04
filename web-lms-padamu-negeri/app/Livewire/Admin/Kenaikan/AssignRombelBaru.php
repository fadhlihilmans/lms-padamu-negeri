<?php

namespace App\Livewire\Admin\Kenaikan;

use App\Models\KenaikanKelas;
use App\Models\Paket;
use App\Models\PeriodeAjaran;
use App\Models\PesertaDidikRombel;
use App\Models\Rombel;
use App\Services\ErrorLogService;
use App\Services\PeriodeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app', ['pageTitle' => 'Assign Rombel Baru'])]
#[Title('Assign Rombel Baru')]
class AssignRombelBaru extends Component
{
    public string $search = '';

    /** kenaikan_kelas id yang dipilih */
    public array $selected = [];

    // Form rombel tujuan
    public string $targetPeriodeId = '';
    public string $targetPaketId   = '';
    public string $targetRombelId  = '';

    public const STATUS = [
        'naik'           => ['Naik Tingkat',   'bg-blue-50 text-blue-700 border-blue-200'],
        'tinggal'        => ['Tinggal',        'bg-amber-50 text-amber-700 border-amber-200'],
        'pindah_paket'   => ['Pindah Paket',   'bg-purple-50 text-purple-700 border-purple-200'],
        'pindah_wilayah' => ['Pindah Wilayah', 'bg-purple-50 text-purple-700 border-purple-200'],
    ];

    public function mount(): void
    {
        $periode = app(PeriodeService::class)->getSelected();
        if ($periode) {
            $this->targetPeriodeId = (string) $periode->id;
        }
    }

    public function updatedTargetPeriodeId(): void
    {
        $this->targetPaketId  = '';
        $this->targetRombelId = '';
    }

    public function updatedTargetPaketId(): void
    {
        $this->targetRombelId = '';
    }

    /** Antrian ruang tunggu: keputusan non-lulus yang belum di-assign. */
    private function waitingQuery()
    {
        return KenaikanKelas::query()
            ->with(['pesertaDidik', 'rombelAsal'])
            ->whereNull('rombel_tujuan_id')
            ->where('status_keputusan', '!=', 'lulus')
            ->whereHas('pesertaDidik');
    }

    public function assign(): void
    {
        if (empty($this->selected)) {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Pilih peserta didik dari daftar tunggu terlebih dahulu.']);
            return;
        }

        $rombel = Rombel::find($this->targetRombelId);
        if (! $rombel) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Rombel tujuan wajib dipilih.']);
            return;
        }

        try {
            $count = 0;
            DB::transaction(function () use ($rombel, &$count) {
                $records = KenaikanKelas::whereIn('id', $this->selected)
                    ->whereNull('rombel_tujuan_id')
                    ->get();

                foreach ($records as $rec) {
                    $rec->update(['rombel_tujuan_id' => $rombel->id]);

                    // Buat keanggotaan rombel baru (unique peserta_didik_id+rombel_id).
                    PesertaDidikRombel::updateOrCreate(
                        ['peserta_didik_id' => $rec->peserta_didik_id, 'rombel_id' => $rombel->id],
                        [],
                    );
                    $count++;
                }
            });

            $this->selected = [];
            $this->dispatch('notify', ['type' => 'success', 'message' => "{$count} peserta didik di-assign ke {$rombel->nama}."]);
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Assign Rombel Baru', $th);
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Terjadi kesalahan, silakan coba lagi.']);
        }
    }

    public function render(): View
    {
        $waiting = $this->waitingQuery()
            ->when($this->search, fn ($q) => $q->whereHas('pesertaDidik', fn ($s) => $s
                ->where('nama_lengkap', 'like', '%' . $this->search . '%')
                ->orWhere('nipd', 'like', '%' . $this->search . '%')))
            ->get()
            ->sortBy(fn ($k) => $k->pesertaDidik?->nama_lengkap)
            ->values();

        $totalTunggu = $this->waitingQuery()->count();

        // Ringkasan PD terpilih.
        $selectedRecords = collect();
        if (! empty($this->selected)) {
            $selectedRecords = KenaikanKelas::with('pesertaDidik')
                ->whereIn('id', $this->selected)->get();
        }

        // Opsi form tujuan.
        $periodes = PeriodeAjaran::orderByDesc('tahun_ajaran')->get();
        $pakets   = Paket::orderBy('nama')->get();

        $rombelsTujuan = collect();
        if ($this->targetPeriodeId && $this->targetPaketId) {
            $rombelsTujuan = Rombel::withCount('pesertaDidikRombel')
                ->where('periode_ajaran_id', $this->targetPeriodeId)
                ->where('paket_id', $this->targetPaketId)
                ->orderBy('nama')
                ->get();
        }

        $targetRombel = $this->targetRombelId
            ? $rombelsTujuan->firstWhere('id', (int) $this->targetRombelId)
            : null;

        return view('livewire.admin.kenaikan.assign-rombel-baru', compact(
            'waiting', 'totalTunggu', 'selectedRecords', 'periodes', 'pakets', 'rombelsTujuan', 'targetRombel'
        ));
    }
}
