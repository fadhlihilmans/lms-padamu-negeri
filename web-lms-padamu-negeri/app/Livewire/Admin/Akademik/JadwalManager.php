<?php

namespace App\Livewire\Admin\Akademik;

use App\Models\GuruMapelRombel;
use App\Models\JadwalPelajaran;
use App\Models\Rombel;
use App\Services\ErrorLogService;
use App\Services\PeriodeService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app', ['pageTitle' => 'Jadwal Pelajaran'])]
#[Title('Jadwal Pelajaran')]
class JadwalManager extends Component
{
    use WithPagination;

    const HARI_ORDER = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];
    const HARI_LABEL = [
        'senin'  => 'Senin',  'selasa' => 'Selasa', 'rabu'   => 'Rabu',
        'kamis'  => 'Kamis',  'jumat'  => 'Jumat',  'sabtu'  => 'Sabtu',
        'minggu' => 'Minggu',
    ];

    // ── Search, filter & pagination ──────────────────────────────────────────
    #[Url] public string $search         = '';
    public string        $filterRombelId = '';
    public string        $filterHari     = '';
    public int           $perPage        = 10;

    // ── Form ─────────────────────────────────────────────────────────────────
    public bool   $showForm    = false;
    public ?int   $editId      = null;
    public ?int   $rombelIdForm = null;
    public ?int   $gmrId       = null;   // guru_mapel_rombel_id (menyimpan pilihan Mapel)
    public string $hari        = '';
    public string $jamMulai    = '';
    public string $jamSelesai  = '';

    // ── Confirm delete ────────────────────────────────────────────────────────
    public ?int $confirmDeleteId = null;

    public function updatedSearch(): void          { $this->resetPage(); }
    public function updatedPerPage(): void         { $this->resetPage(); }
    public function updatedFilterRombelId(): void  { $this->resetPage(); }
    public function updatedFilterHari(): void      { $this->resetPage(); }

    public function updatedRombelIdForm(): void
    {
        $this->gmrId = null;
    }

    // ── Form actions ─────────────────────────────────────────────────────────

    public function openCreateForm(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function openEditForm(int $id): void
    {
        $jadwal             = JadwalPelajaran::with('guruMapelRombel')->findOrFail($id);
        $this->editId       = $id;
        $this->rombelIdForm = $jadwal->guruMapelRombel?->rombel_id;
        $this->gmrId        = $jadwal->guru_mapel_rombel_id;
        $this->hari         = $jadwal->hari;
        $this->jamMulai     = substr($jadwal->jam_mulai, 0, 5);
        $this->jamSelesai   = substr($jadwal->jam_selesai, 0, 5);
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
            'rombelIdForm' => 'required|exists:rombel,id',
            'gmrId'        => 'required|exists:guru_mapel_rombel,id',
            'hari'         => 'required|in:senin,selasa,rabu,kamis,jumat,sabtu,minggu',
            'jamMulai'     => 'required|date_format:H:i',
            'jamSelesai'   => 'required|date_format:H:i|after:jamMulai',
        ], [
            'rombelIdForm.required' => 'Rombel wajib dipilih.',
            'gmrId.required'        => 'Mata Pelajaran wajib dipilih.',
            'hari.required'         => 'Hari wajib dipilih.',
            'jamMulai.required'     => 'Jam mulai wajib diisi.',
            'jamMulai.date_format'  => 'Format jam mulai tidak valid (HH:MM).',
            'jamSelesai.required'    => 'Jam selesai wajib diisi.',
            'jamSelesai.date_format' => 'Format jam selesai tidak valid (HH:MM).',
            'jamSelesai.after'       => 'Jam selesai harus lebih dari jam mulai.',
            'rombelIdForm.exists'    => 'Rombel tidak valid.',
            'gmrId.exists'           => 'Mata Pelajaran tidak valid.',
            'hari.in'                => 'Hari tidak valid.',
        ]);

        $gmr = GuruMapelRombel::where('id', $this->gmrId)
            ->where('rombel_id', $this->rombelIdForm)
            ->first();

        if (! $gmr) {
            $this->addError('gmrId', 'Pemetaan tidak valid untuk rombel ini.');
            return;
        }

        $duplicate = JadwalPelajaran::whereHas('guruMapelRombel', fn ($q) => $q->where('rombel_id', $this->rombelIdForm))
            ->where('hari', $this->hari)
            ->where('jam_mulai', $this->jamMulai . ':00')
            ->when($this->editId, fn ($q) => $q->where('id', '!=', $this->editId))
            ->exists();

        if ($duplicate) {
            $this->addError('jamMulai', 'Sudah ada jadwal dengan hari dan jam mulai yang sama di rombel ini.');
            return;
        }

        $isEdit = (bool) $this->editId;

        try {
            JadwalPelajaran::updateOrCreate(
                ['id' => $this->editId],
                [
                    'guru_mapel_rombel_id' => $this->gmrId,
                    'hari'                 => $this->hari,
                    'jam_mulai'            => $this->jamMulai,
                    'jam_selesai'          => $this->jamSelesai,
                ]
            );
            $this->closeForm();
            $this->dispatch('notify', type: 'success', message: 'Jadwal berhasil ' . ($isEdit ? 'diperbarui' : 'ditambahkan') . '.');
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Simpan Jadwal Pelajaran', $th);
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
            JadwalPelajaran::findOrFail($id)->delete();
            $this->dispatch('notify', type: 'success', message: 'Jadwal berhasil dihapus.');
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Hapus Jadwal Pelajaran', $th);
            $this->dispatch('notify', type: 'error', message: 'Maaf, terjadi kesalahan saat menghapus.');
        }
    }

    // ── Render ───────────────────────────────────────────────────────────────

    public function render(): View
    {
        $periode = app(PeriodeService::class)->getSelected();

        $rombels = $periode
            ? Rombel::where('periode_ajaran_id', $periode->id)->orderBy('nama')->get()
            : collect();

        $hariOrderSql = "FIELD(hari, '" . implode("','", self::HARI_ORDER) . "')";

        $jadwals = JadwalPelajaran::query()
            ->with(['guruMapelRombel.mapel', 'guruMapelRombel.guru', 'guruMapelRombel.rombel'])
            ->when($periode, fn (Builder $q) => $q->whereHas('guruMapelRombel.rombel', fn (Builder $qq) => $qq->where('periode_ajaran_id', $periode->id)), fn (Builder $q) => $q->whereRaw('1 = 0'))
            ->when($this->filterRombelId, fn (Builder $q) => $q->whereHas('guruMapelRombel', fn (Builder $qq) => $qq->where('rombel_id', $this->filterRombelId)))
            ->when($this->filterHari, fn (Builder $q) => $q->where('hari', $this->filterHari))
            ->when($this->search, fn (Builder $q) => $q->where(function (Builder $qq) {
                $qq->whereHas('guruMapelRombel.mapel', fn (Builder $qqq) => $qqq->where('nama', 'like', "%{$this->search}%"))
                   ->orWhereHas('guruMapelRombel.guru', fn (Builder $qqq) => $qqq->where('nama_lengkap', 'like', "%{$this->search}%"));
            }))
            ->orderByRaw($hariOrderSql)
            ->orderBy('jam_mulai')
            ->paginate($this->perPage);

        $pemetaanForm = $this->rombelIdForm
            ? GuruMapelRombel::with(['guru', 'mapel'])->where('rombel_id', $this->rombelIdForm)->orderBy('mapel_id')->get()
            : collect();

        $hariOrder = self::HARI_ORDER;
        $hariLabel = self::HARI_LABEL;

        return view('livewire.admin.akademik.jadwal-manager', compact(
            'jadwals', 'rombels', 'pemetaanForm', 'periode', 'hariOrder', 'hariLabel'
        ));
    }

    private function resetForm(): void
    {
        $this->editId       = null;
        $this->rombelIdForm = null;
        $this->gmrId        = null;
        $this->hari         = '';
        $this->jamMulai     = '';
        $this->jamSelesai   = '';
        $this->resetValidation();
    }
}
