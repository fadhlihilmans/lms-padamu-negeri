<?php

namespace App\Livewire\Guru;

use App\Models\GuruMapelRombel;
use App\Models\JadwalPelajaran;
use App\Models\Rombel;
use App\Services\ErrorLogService;
use App\Services\PeriodeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app', ['pageTitle' => 'Jadwal Pelajaran'])]
#[Title('Jadwal Pelajaran')]
class JadwalGuru extends Component
{
    const HARI_ORDER = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];
    const HARI_LABEL = [
        'senin'  => 'Senin',  'selasa' => 'Selasa', 'rabu'   => 'Rabu',
        'kamis'  => 'Kamis',  'jumat'  => 'Jumat',  'sabtu'  => 'Sabtu',
        'minggu' => 'Minggu',
    ];

    // ── Form (hanya wali kelas) ────────────────────────────────────────────────
    public bool   $showForm        = false;
    public ?int   $editId          = null;
    public ?int   $gmrId           = null;
    public string $hari            = '';
    public string $jamMulai        = '';
    public string $jamSelesai      = '';
    public ?int   $confirmDeleteId = null;

    // ── Form actions (hanya untuk wali kelas) ────────────────────────────────

    public function openCreateForm(): void
    {
        $this->resetWkForm();
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
        $this->resetWkForm();
    }

    public function save(): void
    {
        $guru            = Auth::user()->guru;
        $periode         = app(PeriodeService::class)->getSelected();
        $rombelWaliKelas = $guru ? Rombel::where('wali_kelas_id', $guru->id)->first() : null;

        if (! $rombelWaliKelas) {
            $this->dispatch('notify', type: 'error', message: 'Anda tidak memiliki akses untuk mengelola jadwal.');
            return;
        }

        $this->validate([
            'gmrId'      => 'required|exists:guru_mapel_rombel,id',
            'hari'       => 'required|in:senin,selasa,rabu,kamis,jumat,sabtu,minggu',
            'jamMulai'   => 'required|date_format:H:i',
            'jamSelesai' => 'required|date_format:H:i|after:jamMulai',
        ], [
            'gmrId.required'       => 'Pemetaan guru-mapel wajib dipilih.',
            'hari.required'        => 'Hari wajib dipilih.',
            'jamMulai.required'    => 'Jam mulai wajib diisi.',
            'jamSelesai.required'  => 'Jam selesai wajib diisi.',
            'jamSelesai.after'     => 'Jam selesai harus lebih dari jam mulai.',
        ]);

        // Pastikan GMR memang untuk rombel wali kelas ini
        $gmr = GuruMapelRombel::where('id', $this->gmrId)
            ->where('rombel_id', $rombelWaliKelas->id)
            ->first();

        if (! $gmr) {
            $this->addError('gmrId', 'Pemetaan tidak valid untuk rombel Anda.');
            return;
        }

        $duplicate = JadwalPelajaran::whereHas('guruMapelRombel', fn($q) => $q->where('rombel_id', $rombelWaliKelas->id))
            ->where('hari', $this->hari)
            ->where('jam_mulai', $this->jamMulai . ':00')
            ->when($this->editId, fn($q) => $q->where('id', '!=', $this->editId))
            ->exists();

        if ($duplicate) {
            $this->addError('jamMulai', 'Sudah ada jadwal dengan hari dan jam mulai yang sama.');
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
            app(ErrorLogService::class)->record('Simpan Jadwal (Wali Kelas)', $th);
            $this->dispatch('notify', type: 'error', message: 'Maaf, terjadi kesalahan saat menyimpan.');
        }
    }

    public function confirmDelete(int $id): void { $this->confirmDeleteId = $id; }

    public function delete(): void
    {
        $id = $this->confirmDeleteId;
        $this->confirmDeleteId = null;
        if (! $id) return;

        $guru            = Auth::user()->guru;
        $rombelWaliKelas = $guru ? Rombel::where('wali_kelas_id', $guru->id)->first() : null;

        if (! $rombelWaliKelas) {
            $this->dispatch('notify', type: 'error', message: 'Anda tidak memiliki akses menghapus jadwal ini.');
            return;
        }

        try {
            $jadwal = JadwalPelajaran::with('guruMapelRombel')->findOrFail($id);
            if ($jadwal->guruMapelRombel->rombel_id !== $rombelWaliKelas->id) {
                $this->dispatch('notify', type: 'error', message: 'Anda hanya bisa menghapus jadwal rombel Anda sendiri.');
                return;
            }
            $jadwal->delete();
            $this->dispatch('notify', type: 'success', message: 'Jadwal berhasil dihapus.');
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Hapus Jadwal (Wali Kelas)', $th);
            $this->dispatch('notify', type: 'error', message: 'Maaf, terjadi kesalahan saat menghapus.');
        }
    }

    // ── Render ───────────────────────────────────────────────────────────────

    public function render(): View
    {
        $guru      = Auth::user()->guru;
        $periode   = app(PeriodeService::class)->getSelected();
        $hariOrder = self::HARI_ORDER;
        $hariLabel = self::HARI_LABEL;

        $rombelWaliKelas = null;
        $jadwalWkByHari  = collect();
        $pemetaanWk      = collect();
        $jadwalPribadi   = collect();

        if ($guru) {
            // Jadwal pribadi (sebagai pengajar di semua rombel)
            $jadwalRaw = JadwalPelajaran::with(['guruMapelRombel.rombel', 'guruMapelRombel.mapel'])
                ->whereHas('guruMapelRombel', fn($q) => $q->where('guru_id', $guru->id)
                    ->when($periode, fn($s) => $s->where('periode_ajaran_id', $periode->id))
                )
                ->orderByRaw("FIELD(hari, 'senin','selasa','rabu','kamis','jumat','sabtu','minggu')")
                ->orderBy('jam_mulai')
                ->get();
            $jadwalPribadi = $jadwalRaw->groupBy('hari');

            // Cek wali kelas
            $rombelWaliKelas = Rombel::where('wali_kelas_id', $guru->id)->first();

            if ($rombelWaliKelas) {
                // Pemetaan untuk form tambah jadwal
                $pemetaanQuery = GuruMapelRombel::with(['guru', 'mapel'])
                    ->where('rombel_id', $rombelWaliKelas->id);
                if ($periode) {
                    $pemetaanQuery->where('periode_ajaran_id', $periode->id);
                }
                $pemetaanWk = $pemetaanQuery->orderBy('mapel_id')->get();

                // Jadwal rombel wali kelas
                $jadwalWkRaw = JadwalPelajaran::with(['guruMapelRombel.mapel', 'guruMapelRombel.guru'])
                    ->whereHas('guruMapelRombel', fn($q) => $q->where('rombel_id', $rombelWaliKelas->id))
                    ->orderByRaw("FIELD(hari, 'senin','selasa','rabu','kamis','jumat','sabtu','minggu')")
                    ->orderBy('jam_mulai')
                    ->get();
                $jadwalWkByHari = $jadwalWkRaw->groupBy('hari');
            }
        }

        return view('livewire.guru.jadwal-guru', compact(
            'guru', 'hariOrder', 'hariLabel',
            'jadwalPribadi', 'rombelWaliKelas', 'jadwalWkByHari', 'pemetaanWk'
        ));
    }

    private function resetWkForm(): void
    {
        $this->editId     = null;
        $this->gmrId      = null;
        $this->hari       = '';
        $this->jamMulai   = '';
        $this->jamSelesai = '';
        $this->resetValidation();
    }
}
