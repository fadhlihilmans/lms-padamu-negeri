<?php

namespace App\Livewire\Guru;

use App\Models\JadwalPelajaran;
use App\Models\Mapel;
use App\Models\Guru;
use App\Models\Rombel;
use App\Services\ErrorLogService;
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

    // ── Wali Kelas — Form jadwal rombel ──────────────────────────────────────
    public bool   $showForm   = false;
    public ?int   $editId     = null;
    public string $hari       = '';
    public ?int   $mapelId    = null;
    public ?int   $guruFormId = null;
    public string $jamMulai   = '';
    public string $jamSelesai = '';
    public ?int   $confirmDeleteId = null;

    // ── Form actions (hanya untuk wali kelas) ────────────────────────────────

    public function openCreateForm(): void
    {
        $this->resetWkForm();
        $this->showForm = true;
    }

    public function openEditForm(int $id): void
    {
        $jadwal              = JadwalPelajaran::findOrFail($id);
        $this->editId        = $id;
        $this->hari          = $jadwal->hari;
        $this->mapelId       = $jadwal->mapel_id;
        $this->guruFormId    = $jadwal->guru_id;
        $this->jamMulai      = substr($jadwal->jam_mulai, 0, 5);
        $this->jamSelesai    = substr($jadwal->jam_selesai, 0, 5);
        $this->showForm      = true;
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->resetWkForm();
    }

    public function save(): void
    {
        $guru = Auth::user()->guru;
        $rombelWaliKelas = $guru ? Rombel::where('wali_kelas_id', $guru->id)->first() : null;

        if (! $rombelWaliKelas) {
            $this->dispatch('notify', type: 'error', message: 'Anda tidak memiliki akses untuk mengelola jadwal.');
            return;
        }

        $this->validate([
            'hari'       => 'required|in:senin,selasa,rabu,kamis,jumat,sabtu,minggu',
            'mapelId'    => 'required|exists:mapel,id',
            'guruFormId' => 'required|exists:guru,id',
            'jamMulai'   => 'required|date_format:H:i',
            'jamSelesai' => 'required|date_format:H:i|after:jamMulai',
        ], [
            'hari.required'        => 'Hari wajib dipilih.',
            'mapelId.required'     => 'Mata pelajaran wajib dipilih.',
            'guruFormId.required'  => 'Guru wajib dipilih.',
            'jamMulai.required'    => 'Jam mulai wajib diisi.',
            'jamSelesai.required'  => 'Jam selesai wajib diisi.',
            'jamSelesai.after'     => 'Jam selesai harus lebih dari jam mulai.',
        ]);

        $duplicate = JadwalPelajaran::where('rombel_id', $rombelWaliKelas->id)
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
                    'rombel_id'   => $rombelWaliKelas->id,
                    'mapel_id'    => $this->mapelId,
                    'guru_id'     => $this->guruFormId,
                    'hari'        => $this->hari,
                    'jam_mulai'   => $this->jamMulai,
                    'jam_selesai' => $this->jamSelesai,
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

        $guru = Auth::user()->guru;
        $rombelWaliKelas = $guru ? Rombel::where('wali_kelas_id', $guru->id)->first() : null;

        if (! $rombelWaliKelas) {
            $this->dispatch('notify', type: 'error', message: 'Anda tidak memiliki akses menghapus jadwal ini.');
            return;
        }

        try {
            $jadwal = JadwalPelajaran::findOrFail($id);
            if ($jadwal->rombel_id !== $rombelWaliKelas->id) {
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
        $guru            = Auth::user()->guru;
        $hariOrder       = self::HARI_ORDER;
        $hariLabel       = self::HARI_LABEL;
        $rombelWaliKelas = null;
        $jadwalWkByHari  = collect();
        $mapels          = collect();
        $allGurus        = collect();

        // Jadwal pribadi (sebagai guru pengajar)
        $jadwalPribadi = collect();
        if ($guru) {
            $jadwalRaw = JadwalPelajaran::with(['rombel', 'mapel'])
                ->where('guru_id', $guru->id)
                ->orderByRaw("FIELD(hari, 'senin','selasa','rabu','kamis','jumat','sabtu','minggu')")
                ->orderBy('jam_mulai')
                ->get();
            $jadwalPribadi = $jadwalRaw->groupBy('hari');

            // Cek wali kelas
            $rombelWaliKelas = Rombel::where('wali_kelas_id', $guru->id)->first();

            if ($rombelWaliKelas) {
                $jadwalWkRaw = JadwalPelajaran::with(['mapel', 'guru'])
                    ->where('rombel_id', $rombelWaliKelas->id)
                    ->orderByRaw("FIELD(hari, 'senin','selasa','rabu','kamis','jumat','sabtu','minggu')")
                    ->orderBy('jam_mulai')
                    ->get();
                $jadwalWkByHari = $jadwalWkRaw->groupBy('hari');
                $mapels         = Mapel::orderBy('nama')->get();
                $allGurus       = Guru::orderBy('nama_lengkap')->get();
            }
        }

        return view('livewire.guru.jadwal-guru', compact(
            'guru', 'hariOrder', 'hariLabel',
            'jadwalPribadi', 'rombelWaliKelas', 'jadwalWkByHari',
            'mapels', 'allGurus'
        ));
    }

    private function resetWkForm(): void
    {
        $this->editId     = null;
        $this->hari       = '';
        $this->mapelId    = null;
        $this->guruFormId = null;
        $this->jamMulai   = '';
        $this->jamSelesai = '';
        $this->resetValidation();
    }
}
