<?php

namespace App\Livewire\Admin\Akademik;

use App\Models\Guru;
use App\Models\JadwalPelajaran;
use App\Models\Mapel;
use App\Models\Rombel;
use App\Services\ErrorLogService;
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
    public bool   $showForm   = false;
    public ?int   $editId     = null;
    public string $hari       = '';
    public ?int   $mapelId    = null;
    public ?int   $guruId     = null;
    public string $jamMulai   = '';
    public string $jamSelesai = '';

    // ── Confirm delete ────────────────────────────────────────────────────────
    public ?int $confirmDeleteId = null;

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
        $this->hari       = $jadwal->hari;
        $this->mapelId    = $jadwal->mapel_id;
        $this->guruId     = $jadwal->guru_id;
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
        $this->validate([
            'hari'       => 'required|in:senin,selasa,rabu,kamis,jumat,sabtu,minggu',
            'mapelId'    => 'required|exists:mapel,id',
            'guruId'     => 'required|exists:guru,id',
            'jamMulai'   => 'required|date_format:H:i',
            'jamSelesai' => 'required|date_format:H:i|after:jamMulai',
        ], [
            'hari.required'        => 'Hari wajib dipilih.',
            'hari.in'              => 'Hari tidak valid.',
            'mapelId.required'     => 'Mata pelajaran wajib dipilih.',
            'guruId.required'      => 'Guru wajib dipilih.',
            'jamMulai.required'    => 'Jam mulai wajib diisi.',
            'jamMulai.date_format' => 'Format jam mulai tidak valid (HH:MM).',
            'jamSelesai.required'  => 'Jam selesai wajib diisi.',
            'jamSelesai.after'     => 'Jam selesai harus lebih dari jam mulai.',
        ]);

        // Cek duplikat (rombel + hari + jam_mulai yang sama)
        $duplicate = JadwalPelajaran::where('rombel_id', $this->filterRombelId)
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
                    'rombel_id'   => $this->filterRombelId,
                    'mapel_id'    => $this->mapelId,
                    'guru_id'     => $this->guruId,
                    'hari'        => $this->hari,
                    'jam_mulai'   => $this->jamMulai,
                    'jam_selesai' => $this->jamSelesai,
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
        $rombels      = Rombel::orderByDesc('tahun_ajaran')->orderBy('nama')->get();
        $mapels       = Mapel::orderBy('nama')->get();
        $gurus        = Guru::orderBy('nama_lengkap')->get();
        $hariOrder    = self::HARI_ORDER;
        $hariLabel    = self::HARI_LABEL;

        $jadwalByHari = collect();
        $rombelSelected = null;

        if ($this->filterRombelId) {
            $rombelSelected = Rombel::find($this->filterRombelId);
            $jadwalRaw = JadwalPelajaran::with(['mapel', 'guru'])
                ->where('rombel_id', $this->filterRombelId)
                ->orderByRaw("FIELD(hari, 'senin','selasa','rabu','kamis','jumat','sabtu','minggu')")
                ->orderBy('jam_mulai')
                ->get();

            $jadwalByHari = $jadwalRaw->groupBy('hari');
        }

        return view('livewire.admin.akademik.jadwal-manager', compact(
            'rombels', 'mapels', 'gurus', 'jadwalByHari', 'rombelSelected', 'hariOrder', 'hariLabel'
        ));
    }

    private function resetForm(): void
    {
        $this->editId     = null;
        $this->hari       = '';
        $this->mapelId    = null;
        $this->guruId     = null;
        $this->jamMulai   = '';
        $this->jamSelesai = '';
        $this->resetValidation();
    }
}
