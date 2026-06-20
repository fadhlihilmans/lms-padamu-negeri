<?php

namespace App\Livewire\Admin\MasterData;

use App\Models\Guru;
use App\Models\Paket;
use App\Models\PesertaDidik;
use App\Models\PesertaDidikRombel;
use App\Models\PeriodeAjaran;
use App\Models\Rombel;
use App\Models\Tingkat;
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
    #[Url] public string $search             = '';
    public string        $filterWilayahId    = '';
    public string        $filterPaketId      = '';
    public string        $filterTahunAjaran  = '';
    public int           $perPage            = 10;

    // ── Form ─────────────────────────────────────────────────────────────────
    public bool   $showForm     = false;
    public ?int   $editId       = null;
    public ?int   $wilayahId    = null;
    public ?int   $paketId      = null;
    public ?int   $tingkatId    = null;
    public string $tahunAjaran  = '';
    public ?int   $waliKelasId  = null;

    // ── Kelola Anggota ────────────────────────────────────────────────────────
    public ?int  $kelolaRombelId  = null;
    public string $searchAnggota  = '';

    // ── Confirm delete ────────────────────────────────────────────────────────
    public ?int $confirmDeleteId = null;

    // ── Lifecycle ────────────────────────────────────────────────────────────

    public function updatedSearch(): void            { $this->resetPage(); }
    public function updatedPerPage(): void           { $this->resetPage(); }
    public function updatedFilterWilayahId(): void   { $this->resetPage(); }
    public function updatedFilterPaketId(): void     { $this->resetPage(); }
    public function updatedFilterTahunAjaran(): void { $this->resetPage(); }

    /** Reset tingkat when paket changes in the form */
    public function updatedPaketId(): void { $this->tingkatId = null; }

    // ── Form actions ─────────────────────────────────────────────────────────

    public function openCreateForm(): void
    {
        $this->resetForm();
        // Default tahun ajaran to the active periode
        $aktif = PeriodeAjaran::where('is_aktif', true)->first();
        if ($aktif) {
            $this->tahunAjaran = $aktif->tahun_ajaran;
        }
        $this->showForm = true;
    }

    public function openEditForm(int $id): void
    {
        $rombel             = Rombel::findOrFail($id);
        $this->editId       = $id;
        $this->wilayahId    = $rombel->wilayah_id;
        $this->paketId      = $rombel->paket_id;
        $this->tingkatId    = $rombel->tingkat_id;
        $this->tahunAjaran  = $rombel->tahun_ajaran;
        $this->waliKelasId  = $rombel->wali_kelas_id;
        $this->showForm     = true;
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->resetForm();
    }

    public function save(): void
    {
        $this->validate([
            'wilayahId'   => 'required|exists:wilayah,id',
            'paketId'     => 'required|exists:paket,id',
            'tingkatId'   => 'required|exists:tingkat,id',
            'tahunAjaran' => 'required|string|regex:/^\d{4}\/\d{4}$/',
            'waliKelasId' => 'nullable|exists:guru,id',
        ], [
            'wilayahId.required'   => 'Wilayah wajib dipilih.',
            'paketId.required'     => 'Paket wajib dipilih.',
            'tingkatId.required'   => 'Tingkat wajib dipilih.',
            'tahunAjaran.required' => 'Tahun Ajaran wajib diisi.',
            'tahunAjaran.regex'    => 'Format Tahun Ajaran harus YYYY/YYYY, mis. 2024/2025.',
        ]);

        $duplicate = Rombel::where('wilayah_id', $this->wilayahId)
            ->where('paket_id', $this->paketId)
            ->where('tingkat_id', $this->tingkatId)
            ->where('tahun_ajaran', $this->tahunAjaran)
            ->when($this->editId, fn($q) => $q->where('id', '!=', $this->editId))
            ->exists();

        if ($duplicate) {
            $this->addError('tahunAjaran', 'Rombel dengan kombinasi Wilayah, Paket, Tingkat, dan Tahun Ajaran ini sudah ada.');
            return;
        }

        $tingkat = Tingkat::find($this->tingkatId);
        $wilayah = Wilayah::find($this->wilayahId);
        $paket   = Paket::find($this->paketId);
        $nama    = "{$tingkat->nama} {$wilayah->nama} {$paket->nama} – TA {$this->tahunAjaran}";

        $isEdit = (bool) $this->editId;

        try {
            Rombel::updateOrCreate(
                ['id' => $this->editId],
                [
                    'wilayah_id'    => $this->wilayahId,
                    'paket_id'      => $this->paketId,
                    'tingkat_id'    => $this->tingkatId,
                    'tahun_ajaran'  => $this->tahunAjaran,
                    'wali_kelas_id' => $this->waliKelasId ?: null,
                    'nama'          => $nama,
                ]
            );
            $this->closeForm();
            $this->dispatch('notify', type: 'success', message: 'Rombel berhasil ' . ($isEdit ? 'diperbarui' : 'ditambahkan') . '.');
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Simpan Rombel', $th);
            $this->dispatch('notify', type: 'error', message: 'Maaf, terjadi kesalahan saat menyimpan.');
        }
    }

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
            ->with(['wilayah', 'paket', 'tingkat', 'waliKelas'])
            ->withCount('pesertaDidikRombel')
            ->when($this->search, fn(Builder $q) => $q->where('nama', 'like', "%{$this->search}%"))
            ->when($this->filterWilayahId, fn(Builder $q) => $q->where('wilayah_id', $this->filterWilayahId))
            ->when($this->filterPaketId, fn(Builder $q) => $q->where('paket_id', $this->filterPaketId))
            ->when($this->filterTahunAjaran, fn(Builder $q) => $q->where('tahun_ajaran', $this->filterTahunAjaran))
            ->orderByDesc('tahun_ajaran')
            ->orderBy('nama')
            ->paginate($this->perPage);

        $wilayahs    = Wilayah::orderBy('nama')->get();
        $pakets      = Paket::orderBy('nama')->get();
        $tingkats    = $this->paketId
            ? Tingkat::where('paket_id', $this->paketId)->orderBy('nama')->get()
            : collect();
        $gurus       = Guru::orderBy('nama_lengkap')->get();

        $tahunAjaranOptions = Rombel::orderByDesc('tahun_ajaran')
            ->distinct()
            ->pluck('tahun_ajaran');

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
            'rombels', 'wilayahs', 'pakets', 'tingkats', 'gurus',
            'tahunAjaranOptions', 'rombelKelola', 'calonAnggota'
        ));
    }

    private function resetForm(): void
    {
        $this->editId      = null;
        $this->wilayahId   = null;
        $this->paketId     = null;
        $this->tingkatId   = null;
        $this->tahunAjaran = '';
        $this->waliKelasId = null;
        $this->resetValidation();
    }
}
