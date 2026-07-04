<?php

namespace App\Livewire\Guru\Cbt;

use App\Models\Cbt;
use App\Models\GuruMapelRombel;
use App\Models\HasilCbt;
use App\Models\PesertaDidik;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app', ['pageTitle' => 'Hasil CBT'])]
#[Title('Hasil CBT')]
class DaftarHasilCbt extends Component
{
    use WithPagination;

    public Cbt $cbt;

    public string $search       = '';
    public string $statusFilter = '';
    public int    $perPage      = 10;

    public function updatingSearch(): void       { $this->resetPage(); }
    public function updatingPerPage(): void      { $this->resetPage(); }
    public function updatingStatusFilter(): void { $this->resetPage(); }

    public function mount(int $cbtId): void
    {
        $cbt = Cbt::with(['guruMapelRombel.mapel', 'guruMapelRombel.rombel'])->findOrFail($cbtId);
        $this->authorizeGuru($cbt);
        $this->cbt = $cbt;
    }

    private function authorizeGuru(Cbt $cbt): void
    {
        $guru = Auth::user()?->guru;
        $gmr  = GuruMapelRombel::find($cbt->guru_mapel_rombel_id);
        abort_unless($guru && $gmr && $gmr->guru_id === $guru->id, 403);
    }

    /** Toggle tampilkan nilai satu PD ke halaman PD. */
    public function toggleNilai(int $hasilId): void
    {
        $hasil = HasilCbt::where('cbt_id', $this->cbt->id)->find($hasilId);
        if (! $hasil || $hasil->nilai_akhir === null) {
            return; // belum ada nilai final
        }
        $hasil->update(['nilai_ditampilkan' => ! $hasil->nilai_ditampilkan]);
        $this->dispatch('notify', [
            'type'    => 'success',
            'message' => $hasil->nilai_ditampilkan ? 'Nilai ditampilkan ke peserta didik.' : 'Nilai disembunyikan dari peserta didik.',
        ]);
    }

    /** Tampilkan semua nilai (yang sudah final) ke PD. */
    public function showAllNilai(): void
    {
        HasilCbt::where('cbt_id', $this->cbt->id)
            ->whereNotNull('nilai_akhir')
            ->update(['nilai_ditampilkan' => true]);
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Semua nilai final ditampilkan ke peserta didik.']);
    }

    public function render(): View
    {
        $rombelId = $this->cbt->guruMapelRombel?->rombel_id;
        $kkm      = $this->cbt->kkm;

        // Daftar peserta didik rombel + hasil (jika sudah mengerjakan).
        $peserta = PesertaDidik::query()
            ->join('peserta_didik_rombel', 'peserta_didik_rombel.peserta_didik_id', '=', 'peserta_didik.id')
            ->where('peserta_didik_rombel.rombel_id', $rombelId)
            ->leftJoin('hasil_cbt', function ($j) {
                $j->on('hasil_cbt.peserta_didik_id', '=', 'peserta_didik.id')
                  ->where('hasil_cbt.cbt_id', '=', $this->cbt->id)
                  ->whereNull('hasil_cbt.deleted_at')
                  ->whereNotNull('hasil_cbt.waktu_submit');
            })
            ->when($this->search, fn ($q) => $q->where(function ($sub) {
                $sub->where('peserta_didik.nama_lengkap', 'like', '%' . $this->search . '%')
                    ->orWhere('peserta_didik.nipd', 'like', '%' . $this->search . '%');
            }))
            ->when($this->statusFilter === 'lulus', fn ($q) => $q->where('hasil_cbt.nilai_akhir', '>=', $kkm))
            ->when($this->statusFilter === 'tidak_lulus', fn ($q) => $q
                ->whereNotNull('hasil_cbt.nilai_akhir')
                ->where('hasil_cbt.nilai_akhir', '<', $kkm))
            ->when($this->statusFilter === 'menunggu', fn ($q) => $q->where('hasil_cbt.status_penilaian', 'menunggu_koreksi'))
            ->when($this->statusFilter === 'belum', fn ($q) => $q->whereNull('hasil_cbt.id'))
            ->orderBy('peserta_didik.nama_lengkap')
            ->select(
                'peserta_didik.*',
                'hasil_cbt.id as hasil_id',
                'hasil_cbt.nilai_pg',
                'hasil_cbt.nilai_uraian',
                'hasil_cbt.nilai_akhir',
                'hasil_cbt.status_penilaian',
                'hasil_cbt.nilai_ditampilkan',
            )
            ->paginate($this->perPage);

        // Statistik (hanya hasil yang sudah punya nilai akhir).
        $finalized = HasilCbt::where('cbt_id', $this->cbt->id)->whereNotNull('nilai_akhir');
        $finalizedCount = (clone $finalized)->count();

        $stats = [
            'rata'      => $finalizedCount ? round((clone $finalized)->avg('nilai_akhir'), 1) : null,
            'tertinggi' => $finalizedCount ? (clone $finalized)->max('nilai_akhir') : null,
            'terendah'  => $finalizedCount ? (clone $finalized)->min('nilai_akhir') : null,
            'lulus'     => (clone $finalized)->where('nilai_akhir', '>=', $kkm)->count(),
            'finalized' => $finalizedCount,
        ];

        $menungguCount = HasilCbt::where('cbt_id', $this->cbt->id)
            ->whereNotNull('waktu_submit')
            ->where('status_penilaian', 'menunggu_koreksi')
            ->count();

        return view('livewire.guru.cbt.daftar-hasil-cbt', compact(
            'peserta', 'kkm', 'stats', 'menungguCount'
        ));
    }
}
