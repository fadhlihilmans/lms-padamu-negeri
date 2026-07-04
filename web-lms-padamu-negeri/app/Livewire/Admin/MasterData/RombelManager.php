<?php

namespace App\Livewire\Admin\MasterData;

use App\Models\Paket;
use App\Models\PeriodeAjaran;
use App\Models\PesertaDidik;
use App\Models\PesertaDidikRombel;
use App\Models\Rombel;
use App\Models\Wilayah;
use App\Services\ErrorLogService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app', ['pageTitle' => 'Rombel'])]
#[Title('Rombel')]
class RombelManager extends Component
{
    use WithPagination;

    // ── Search & pagination ──────────────────────────────────────────────────
    #[Url] public string $search              = '';
    public string        $filterWilayahId     = '';
    public string        $filterPaketId       = '';
    public string        $filterPeriodeId     = '';
    public int           $perPage             = 10;

    // ── Kelola Anggota ────────────────────────────────────────────────────────
    public ?int   $kelolaRombelId = null;
    public string $searchAnggota  = '';

    // ── Confirm delete ────────────────────────────────────────────────────────
    public ?int $confirmDeleteId = null;

    // ── Lifecycle ────────────────────────────────────────────────────────────

    public function updatedSearch(): void           { $this->resetPage(); }
    public function updatedPerPage(): void          { $this->resetPage(); }
    public function updatedFilterWilayahId(): void  { $this->resetPage(); }
    public function updatedFilterPaketId(): void    { $this->resetPage(); }
    public function updatedFilterPeriodeId(): void  { $this->resetPage(); }

    // ── Delete ───────────────────────────────────────────────────────────────

    public function confirmDelete(int $id): void { $this->confirmDeleteId = $id; }

    public function delete(): void
    {
        $id = $this->confirmDeleteId;
        $this->confirmDeleteId = null;
        if (! $id) return;

        try {
            $rombel = Rombel::findOrFail($id);

            if ($rombel->guruMapelRombel()->exists()) {
                $this->dispatch('notify', type: 'error', message: 'Rombel tidak dapat dihapus karena sudah memiliki pemetaan guru.');
                return;
            }
            if ($rombel->jadwalPelajaran()->exists()) {
                $this->dispatch('notify', type: 'error', message: 'Rombel tidak dapat dihapus karena sudah memiliki jadwal pelajaran.');
                return;
            }
            if ($rombel->pesertaDidikRombel()->exists()) {
                $this->dispatch('notify', type: 'error', message: 'Rombel tidak dapat dihapus karena masih memiliki anggota peserta didik.');
                return;
            }

            $rombel->delete();
            $this->dispatch('notify', type: 'success', message: 'Rombel berhasil dihapus.');
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Hapus Rombel', $th);
            $this->dispatch('notify', type: 'error', message: 'Maaf, terjadi kesalahan saat menghapus.');
        }
    }

    // ── Kelola Anggota ────────────────────────────────────────────────────────

    public function openKelolaMember(int $rombelId): void
    {
        $this->kelolaRombelId = $rombelId;
        $this->searchAnggota  = '';
    }

    public function closeKelolaMember(): void
    {
        $this->kelolaRombelId = null;
        $this->searchAnggota  = '';
    }

    public function addMember(int $pesertaDidikId): void
    {
        if (! $this->kelolaRombelId) return;

        try {
            $existing = PesertaDidikRombel::withTrashed()
                ->where('rombel_id', $this->kelolaRombelId)
                ->where('peserta_didik_id', $pesertaDidikId)
                ->first();

            if ($existing) {
                if ($existing->trashed()) {
                    $existing->restore();
                } else {
                    $this->dispatch('notify', type: 'error', message: 'Peserta Didik sudah terdaftar di Rombel ini.');
                    return;
                }
            } else {
                PesertaDidikRombel::create([
                    'rombel_id'        => $this->kelolaRombelId,
                    'peserta_didik_id' => $pesertaDidikId,
                ]);
            }

            $this->searchAnggota = '';
            $this->dispatch('notify', type: 'success', message: 'Peserta Didik berhasil ditambahkan ke Rombel.');
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Tambah Anggota Rombel', $th);
            $this->dispatch('notify', type: 'error', message: 'Maaf, terjadi kesalahan.');
        }
    }

    public function removeMember(int $pesertaDidikRombelId): void
    {
        try {
            $pdr = PesertaDidikRombel::findOrFail($pesertaDidikRombelId);
            $pdr->delete();
            $this->dispatch('notify', type: 'success', message: 'Peserta Didik berhasil dihapus dari Rombel.');
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Hapus Anggota Rombel', $th);
            $this->dispatch('notify', type: 'error', message: 'Maaf, terjadi kesalahan.');
        }
    }

    // ── Render ───────────────────────────────────────────────────────────────

    public function render(): View
    {
        $rombels = Rombel::query()
            ->with(['periodeAjaran', 'wilayah', 'paket', 'tingkat', 'waliKelas'])
            ->withCount('pesertaDidikRombel')
            ->when($this->search, fn(Builder $q) => $q->where('nama', 'like', "%{$this->search}%"))
            ->when($this->filterWilayahId, fn(Builder $q) => $q->where('wilayah_id', $this->filterWilayahId))
            ->when($this->filterPaketId, fn(Builder $q) => $q->where('paket_id', $this->filterPaketId))
            ->when($this->filterPeriodeId, fn(Builder $q) => $q->where('periode_ajaran_id', $this->filterPeriodeId))
            ->orderByDesc('periode_ajaran_id')
            ->orderBy('nama')
            ->paginate($this->perPage);

        $wilayahs = Wilayah::orderBy('nama')->get();
        $pakets   = Paket::orderBy('nama')->get();
        $periodes = PeriodeAjaran::orderByDesc('id')->get();

        $rombelKelola = null;
        $calonAnggota = collect();

        if ($this->kelolaRombelId) {
            $rombelKelola = Rombel::with(['pesertaDidikRombel.pesertaDidik'])
                ->find($this->kelolaRombelId);

            if ($rombelKelola && strlen(trim($this->searchAnggota)) >= 2) {
                $anggotaIds   = $rombelKelola->pesertaDidikRombel->pluck('peserta_didik_id');
                $calonAnggota = PesertaDidik::whereNotIn('id', $anggotaIds)
                    ->where('status_akademik', 'aktif')
                    ->where(function (Builder $q) {
                        $q->where('nama_lengkap', 'like', "%{$this->searchAnggota}%")
                          ->orWhere('nipd', 'like', "%{$this->searchAnggota}%");
                    })
                    ->limit(10)
                    ->get();
            }
        }

        return view('livewire.admin.master-data.rombel-manager', compact(
            'rombels', 'wilayahs', 'pakets',
            'periodes', 'rombelKelola', 'calonAnggota'
        ));
    }
}
