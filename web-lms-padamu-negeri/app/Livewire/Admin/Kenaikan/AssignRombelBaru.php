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

    /** Filter daftar tunggu berdasarkan rombel ASAL (mempermudah pemindahan massal). */
    public string $filterRombelAsalId = '';

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

    /** Antrian setelah filter rombel asal + pencarian (yang benar-benar TAMPIL). */
    private function visibleQuery()
    {
        return $this->waitingQuery()
            ->when($this->filterRombelAsalId, fn ($q) => $q->where('rombel_asal_id', $this->filterRombelAsalId))
            ->when($this->search, fn ($q) => $q->whereHas('pesertaDidik', fn ($s) => $s
                ->where('nama_lengkap', 'like', '%' . $this->search . '%')
                ->orWhere('nipd', 'like', '%' . $this->search . '%')));
    }

    // Ganti filter/pencarian → bersihkan pilihan supaya tidak ada yang "terpilih
    // diam-diam" padahal sudah tidak tampil di layar.
    public function updatedFilterRombelAsalId(): void { $this->selected = []; }
    public function updatedSearch(): void            { $this->selected = []; }

    /**
     * Checklist-semua: centang/hapus SEMUA baris yang sedang tampil.
     * Sengaja hanya yang tampil (bukan seluruh antrian) agar Admin tidak
     * memindahkan peserta didik dari rombel lain tanpa sadar.
     */
    public function toggleSelectAll(): void
    {
        $visible = $this->visibleQuery()->pluck('id')->map(fn ($id) => (string) $id)->all();

        $semuaSudahTerpilih = ! empty($visible)
            && count(array_intersect($visible, $this->selected)) === count($visible);

        $this->selected = $semuaSudahTerpilih ? [] : $visible;
    }

    public function clearSelection(): void
    {
        $this->selected = [];
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
            $count   = 0;
            $dilewati = [];   // PD yang sudah punya rombel lain di TA tujuan

            DB::transaction(function () use ($rombel, &$count, &$dilewati) {
                $records = KenaikanKelas::with('pesertaDidik')
                    ->whereIn('id', $this->selected)
                    ->whereNull('rombel_tujuan_id')
                    ->get();

                foreach ($records as $rec) {
                    // 1 PD = 1 rombel per TA. Yang bentrok DILEWATI, bukan
                    // menggagalkan seluruh batch — supaya sisanya tetap terpindah.
                    $bentrok = PesertaDidikRombel::rombelLainDiTaSama($rec->peserta_didik_id, $rombel->id);
                    if ($bentrok) {
                        $dilewati[] = ($rec->pesertaDidik?->nama_lengkap ?? 'PD') . " (sudah di {$bentrok->nama})";
                        continue;
                    }

                    $rec->update(['rombel_tujuan_id' => $rombel->id]);

                    PesertaDidikRombel::updateOrCreate(
                        ['peserta_didik_id' => $rec->peserta_didik_id, 'rombel_id' => $rombel->id],
                        [],
                    );
                    $count++;
                }
            });

            $this->selected = [];

            if ($count > 0) {
                $this->dispatch('notify', ['type' => 'success', 'message' => "{$count} peserta didik di-assign ke {$rombel->nama}."]);
            }
            if ($dilewati) {
                $this->dispatch('notify', [
                    'type'    => 'warning',
                    'message' => count($dilewati) . ' peserta didik dilewati karena sudah punya rombel lain di TA ini: '
                               . implode(', ', array_slice($dilewati, 0, 3))
                               . (count($dilewati) > 3 ? ', …' : ''),
                ]);
            }
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Assign Rombel Baru', $th);
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Terjadi kesalahan, silakan coba lagi.']);
        }
    }

    public function render(): View
    {
        $waiting = $this->visibleQuery()
            ->get()
            ->sortBy(fn ($k) => $k->pesertaDidik?->nama_lengkap)
            ->values();

        $totalTunggu = $this->waitingQuery()->count();

        // Opsi filter: rombel asal yang benar-benar ada di antrian + jumlahnya.
        $rombelAsalOptions = $this->waitingQuery()
            ->get()
            ->groupBy('rombel_asal_id')
            ->map(fn ($rows) => [
                'id'    => $rows->first()->rombel_asal_id,
                'nama'  => $rows->first()->rombelAsal?->nama ?? '—',
                'total' => $rows->count(),
            ])
            ->sortBy('nama')
            ->values();

        // Status checklist-semua (berdasarkan baris yang tampil).
        $visibleIds  = $waiting->pluck('id')->map(fn ($id) => (string) $id)->all();
        $allSelected = ! empty($visibleIds)
            && count(array_intersect($visibleIds, $this->selected)) === count($visibleIds);

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
                ->where('tahun_ajaran', \App\Models\PeriodeAjaran::find($this->targetPeriodeId)?->tahun_ajaran)
                ->where('paket_id', $this->targetPaketId)
                ->orderBy('nama')
                ->get();
        }

        $targetRombel = $this->targetRombelId
            ? $rombelsTujuan->firstWhere('id', (int) $this->targetRombelId)
            : null;

        return view('livewire.admin.kenaikan.assign-rombel-baru', compact(
            'waiting', 'totalTunggu', 'selectedRecords', 'periodes', 'pakets', 'rombelsTujuan', 'targetRombel',
            'rombelAsalOptions', 'allSelected'
        ));
    }
}
