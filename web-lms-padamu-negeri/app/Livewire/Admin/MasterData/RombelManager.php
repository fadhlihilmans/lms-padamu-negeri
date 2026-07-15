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
    /** Kosong = ikut TA periode aktif (default). Rombel terikat TA, bukan semester. */
    public string        $filterTahunAjaran   = '';
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
    public function updatedFilterTahunAjaran(): void { $this->resetPage(); }

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

        // LAPIS 1 (UX): cegah lebih dulu dengan pesan jelas, sebelum model menolak.
        // 1 peserta didik hanya boleh punya 1 rombel per Tahun Ajaran.
        $bentrok = PesertaDidikRombel::rombelLainDiTaSama($pesertaDidikId, $this->kelolaRombelId);
        if ($bentrok) {
            $this->dispatch('notify', type: 'error', message:
                "Peserta didik sudah terdaftar di \"{$bentrok->nama}\" pada TA {$bentrok->tahun_ajaran}. "
                . 'Keluarkan dulu dari rombel itu sebelum memindahkannya.');
            return;
        }

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
        } catch (\App\Exceptions\SatuRombelPerTaException $th) {
            // LAPIS 2 (jaring pengaman model) — seharusnya tak tercapai, tapi jangan
            // sampai jadi halaman error bila ada jalur yang terlewat.
            $this->dispatch('notify', type: 'error', message: $th->getMessage());
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
        // Default: hanya TA yang sedang dipilih (revisi — jangan tampilkan semua histori).
        $taAktif = app(\App\Services\PeriodeService::class)->getTahunAjaran();
        $taFilter = $this->filterTahunAjaran !== '' ? $this->filterTahunAjaran : $taAktif;

        $rombels = Rombel::query()
            ->with(['wilayah', 'paket', 'tingkat', 'waliKelas'])
            ->withCount('pesertaDidikRombel')
            ->tahunAjaran($taFilter)
            ->when($this->search, fn(Builder $q) => $q->where('nama', 'like', "%{$this->search}%"))
            ->when($this->filterWilayahId, fn(Builder $q) => $q->where('wilayah_id', $this->filterWilayahId))
            ->when($this->filterPaketId, fn(Builder $q) => $q->where('paket_id', $this->filterPaketId))
            ->orderByDesc('tahun_ajaran')
            ->orderBy('nama')
            ->paginate($this->perPage);

        $wilayahs     = Wilayah::orderBy('nama')->get();
        $pakets       = Paket::orderBy('nama')->get();
        $tahunAjarans = PeriodeAjaran::select('tahun_ajaran')->distinct()
            ->orderByDesc('tahun_ajaran')->pluck('tahun_ajaran');

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
            'tahunAjarans', 'rombelKelola', 'calonAnggota'
        ));
    }
}
