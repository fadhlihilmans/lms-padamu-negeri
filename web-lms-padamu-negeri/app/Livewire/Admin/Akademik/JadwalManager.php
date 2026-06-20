<?php

namespace App\Livewire\Admin\Akademik;

use App\Models\GuruMapelRombel;
use App\Models\JadwalPelajaran;
use App\Models\Rombel;
use App\Services\ErrorLogService;
use App\Services\PeriodeService;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.app', ['pageTitle' => 'Jadwal Pelajaran'])]
#[Title('Jadwal Pelajaran')]
class JadwalManager extends Component
{
    const HARI_ORDER = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];
    const HARI_LABEL = [
        'senin'  => 'Senin',  'selasa' => 'Selasa', 'rabu'   => 'Rabu',
        'kamis'  => 'Kamis',  'jumat'  => 'Jumat',  'sabtu'  => 'Sabtu',
        'minggu' => 'Minggu',
    ];

    // ── Filter ────────────────────────────────────────────────────────────────
    #[Url] public string $filterRombelId = '';

    // ── Form ─────────────────────────────────────────────────────────────────
    public bool   $showForm        = false;
    public ?int   $editId          = null;
    public ?int   $gmrId           = null;   // guru_mapel_rombel_id
    public string $hari            = '';
    public string $jamMulai        = '';
    public string $jamSelesai      = '';

    // ── Confirm delete ────────────────────────────────────────────────────────
    public ?int $confirmDeleteId = null;

    // ── Lifecycle ─────────────────────────────────────────────────────────────

    public function updatedFilterRombelId(): void
    {
        $this->closeForm();
    }

    // ── Form actions ─────────────────────────────────────────────────────────

    public function openCreateForm(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function openEditForm(int $id): void
    {
        $jadwal           = JadwalPelajaran::findOrFail($id);
        $this->editId     = $id;
        $this->gmrId      = $jadwal->guru_mapel_rombel_id;
        $this->hari       = $jadwal->hari;
        $this->jamMulai   = substr($jadwal->jam_mulai, 0, 5);
        $this->jamSelesai = substr($jadwal->jam_selesai, 0, 5);
        $this->showForm   = true;
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->resetForm();
    }

    public function save(): void
    {
        if (! $this->filterRombelId) return;

        $this->validate([
            'gmrId'      => 'required|exists:guru_mapel_rombel,id',
            'hari'       => 'required|in:senin,selasa,rabu,kamis,jumat,sabtu,minggu',
            'jamMulai'   => 'required|date_format:H:i',
            'jamSelesai' => 'required|date_format:H:i|after:jamMulai',
        ], [
            'gmrId.required'       => 'Pemetaan guru-mapel wajib dipilih.',
            'hari.required'        => 'Hari wajib dipilih.',
            'jamMulai.required'    => 'Jam mulai wajib diisi.',
            'jamMulai.date_format' => 'Format jam mulai tidak valid (HH:MM).',
            'jamSelesai.required'  => 'Jam selesai wajib diisi.',
            'jamSelesai.after'     => 'Jam selesai harus lebih dari jam mulai.',
        ]);

        // Pastikan GMR memang milik rombel yang dipilih
        $gmr = GuruMapelRombel::where('id', $this->gmrId)
            ->where('rombel_id', $this->filterRombelId)
            ->first();

        if (! $gmr) {
            $this->addError('gmrId', 'Pemetaan tidak valid untuk rombel ini.');
            return;
        }

        // Cek duplikat: rombel + hari + jam_mulai
        $duplicate = JadwalPelajaran::whereHas('guruMapelRombel', fn($q) => $q->where('rombel_id', $this->filterRombelId))
            ->where('hari', $this->hari)
            ->where('jam_mulai', $this->jamMulai . ':00')
            ->when($this->editId, fn($q) => $q->where('id', '!=', $this->editId))
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
        $rombels = Rombel::orderByDesc('periode_ajaran_id')->orderBy('nama')->get();

        $jadwalByHari   = collect();
        $rombelSelected = null;
        $pemetaan       = collect();

        if ($this->filterRombelId) {
            $rombelSelected = Rombel::with('periodeAjaran')->find($this->filterRombelId);

            // Pemetaan guru-mapel untuk rombel ini (scope ke periode aktif/selected)
            $pemetaanQuery = GuruMapelRombel::with(['guru', 'mapel'])
                ->where('rombel_id', $this->filterRombelId);

            if ($periode) {
                $pemetaanQuery->where('periode_ajaran_id', $periode->id);
            }

            $pemetaan = $pemetaanQuery->orderBy('mapel_id')->get();

            // Jadwal rombel terpilih
            $jadwalRaw = JadwalPelajaran::with(['guruMapelRombel.mapel', 'guruMapelRombel.guru'])
                ->whereHas('guruMapelRombel', fn($q) => $q->where('rombel_id', $this->filterRombelId))
                ->orderByRaw("FIELD(hari, 'senin','selasa','rabu','kamis','jumat','sabtu','minggu')")
                ->orderBy('jam_mulai')
                ->get();

            $jadwalByHari = $jadwalRaw->groupBy('hari');
        }

        $hariOrder = self::HARI_ORDER;
        $hariLabel = self::HARI_LABEL;

        return view('livewire.admin.akademik.jadwal-manager', compact(
            'rombels', 'pemetaan', 'jadwalByHari', 'rombelSelected', 'hariOrder', 'hariLabel',
        ));
    }

    private function resetForm(): void
    {
        $this->editId     = null;
        $this->gmrId      = null;
        $this->hari       = '';
        $this->jamMulai   = '';
        $this->jamSelesai = '';
        $this->resetValidation();
    }
}
