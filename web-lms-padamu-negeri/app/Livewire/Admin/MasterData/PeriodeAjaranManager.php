<?php

namespace App\Livewire\Admin\MasterData;

use App\Models\PeriodeAjaran;
use App\Services\ErrorLogService;
use App\Services\PeriodeService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app', ['pageTitle' => 'Periode Ajaran'])]
#[Title('Periode Ajaran')]
class PeriodeAjaranManager extends Component
{
    public bool   $showForm    = false;
    public ?int   $editId      = null;
    public string $tahunAjaran = '';
    public string $semester    = '';

    public ?int   $confirmDeleteId   = null;
    public ?int   $confirmAktifId    = null;

    // ── Form ────────────────────────────────────────────────────────────────

    public function bukaFormTambah(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function bukaFormEdit(int $id): void
    {
        $periode = PeriodeAjaran::findOrFail($id);
        $this->editId      = $id;
        $this->tahunAjaran = $periode->tahun_ajaran;
        $this->semester    = $periode->semester;
        $this->showForm    = true;
    }

    public function tutupForm(): void
    {
        $this->showForm = false;
        $this->resetForm();
    }

    public function simpan(): void
    {
        $this->validate([
            'tahunAjaran' => [
                'required',
                'string',
                'regex:/^\d{4}\/\d{4}$/',
                Rule::unique('periode_ajaran', 'tahun_ajaran')
                    ->where('semester', $this->semester)
                    ->ignore($this->editId),
            ],
            'semester'    => 'required|in:ganjil,genap',
        ], [
            'tahunAjaran.required' => 'Tahun Ajaran wajib diisi.',
            'tahunAjaran.regex'    => 'Format Tahun Ajaran harus YYYY/YYYY, mis. 2024/2025.',
            'tahunAjaran.unique'   => 'Kombinasi Tahun Ajaran dan Semester sudah ada.',
            'semester.required'    => 'Semester wajib dipilih.',
            'semester.in'          => 'Semester tidak valid.',
        ]);

        try {
            PeriodeAjaran::updateOrCreate(
                ['id' => $this->editId],
                [
                    'tahun_ajaran' => $this->tahunAjaran,
                    'semester'     => $this->semester,
                ]
            );

            $this->tutupForm();
            $this->dispatch('notify', type: 'success', message: 'Periode Ajaran berhasil ' . ($this->editId ? 'diperbarui' : 'ditambahkan') . '.');
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->catat('Simpan Periode Ajaran', $th);
            $this->dispatch('notify', type: 'error', message: 'Maaf, terjadi kesalahan saat menyimpan.');
        }
    }

    // ── Hapus ───────────────────────────────────────────────────────────────

    public function konfirmasiHapus(int $id): void
    {
        $this->confirmDeleteId = $id;
    }

    public function hapus(): void
    {
        $id = $this->confirmDeleteId;
        $this->confirmDeleteId = null;

        if (! $id) {
            return;
        }

        try {
            $periode = PeriodeAjaran::findOrFail($id);

            if ($periode->is_aktif) {
                $this->dispatch('notify', type: 'error', message: 'Periode Aktif tidak dapat dihapus. Aktifkan periode lain terlebih dahulu.');
                return;
            }

            $periode->delete();
            $this->dispatch('notify', type: 'success', message: 'Periode Ajaran berhasil dihapus.');
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->catat('Hapus Periode Ajaran', $th);
            $this->dispatch('notify', type: 'error', message: 'Maaf, terjadi kesalahan saat menghapus.');
        }
    }

    // ── Set Aktif ───────────────────────────────────────────────────────────

    public function konfirmasiAktif(int $id): void
    {
        $this->confirmAktifId = $id;
    }

    public function setAktif(): void
    {
        $id = $this->confirmAktifId;
        $this->confirmAktifId = null;

        if (! $id) {
            return;
        }

        try {
            DB::transaction(function () use ($id) {
                PeriodeAjaran::query()->update(['is_aktif' => false]);
                PeriodeAjaran::findOrFail($id)->update(['is_aktif' => true]);
            });

            // Reset switcher session agar langsung pakai periode aktif baru
            app(PeriodeService::class)->resetToAktif();

            $this->dispatch('notify', type: 'success', message: 'Periode Ajaran berhasil diaktifkan.');
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->catat('Set Aktif Periode Ajaran', $th);
            $this->dispatch('notify', type: 'error', message: 'Maaf, terjadi kesalahan.');
        }
    }

    // ── Render ──────────────────────────────────────────────────────────────

    public function render(): View
    {
        return view('livewire.admin.master-data.periode-ajaran-manager', [
            'periodes' => PeriodeAjaran::orderByDesc('tahun_ajaran')
                ->orderByRaw("FIELD(semester, 'genap', 'ganjil')")
                ->get(),
        ]);
    }

    private function resetForm(): void
    {
        $this->editId      = null;
        $this->tahunAjaran = '';
        $this->semester    = '';
        $this->resetValidation();
    }
}
