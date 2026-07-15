<?php

namespace App\Livewire\Admin\MasterData;

use App\Models\Guru;
use App\Models\Paket;
use App\Models\PeriodeAjaran;
use App\Models\Rombel;
use App\Models\Tingkat;
use App\Models\Wilayah;
use App\Services\ErrorLogService;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app', ['pageTitle' => 'Rombel'])]
class FormRombel extends Component
{
    public ?int $editId = null;

    // Rombel terikat TAHUN AJARAN (bukan periode/semester) — Revisi Tahap 3.
    public ?string $tahunAjaran = null;
    public ?int $wilayahId       = null;
    public ?int $paketId         = null;
    public ?int $tingkatId       = null;
    public ?int $waliKelasId     = null;

    public function mount(?int $id = null): void
    {
        if ($id) {
            $rombel = Rombel::findOrFail($id);
            $this->editId      = $rombel->id;
            $this->tahunAjaran = $rombel->tahun_ajaran;
            $this->wilayahId   = $rombel->wilayah_id;
            $this->paketId     = $rombel->paket_id;
            $this->tingkatId   = $rombel->tingkat_id;
            $this->waliKelasId = $rombel->wali_kelas_id;
        } else {
            $this->tahunAjaran = PeriodeAjaran::where('is_aktif', true)->value('tahun_ajaran');
        }
    }

    /** Daftar Tahun Ajaran unik dari periode_ajaran. */
    public function tahunAjaranOptions()
    {
        return PeriodeAjaran::query()
            ->select('tahun_ajaran')
            ->distinct()
            ->orderByDesc('tahun_ajaran')
            ->pluck('tahun_ajaran');
    }

    public function updatedPaketId(): void
    {
        $this->tingkatId = null;
    }

    public function save(): void
    {
        $this->validate([
            'tahunAjaran' => 'required|string|exists:periode_ajaran,tahun_ajaran',
            'wilayahId'   => 'required|exists:wilayah,id',
            'paketId'     => 'required|exists:paket,id',
            'tingkatId'   => 'required|exists:tingkat,id',
            'waliKelasId' => 'nullable|exists:guru,id',
        ], [
            'tahunAjaran.required' => 'Tahun Ajaran wajib dipilih.',
            'tahunAjaran.exists'   => 'Tahun Ajaran tidak valid.',
            'wilayahId.required'   => 'Wilayah wajib dipilih.',
            'wilayahId.exists'     => 'Wilayah tidak valid.',
            'paketId.required'     => 'Paket wajib dipilih.',
            'paketId.exists'       => 'Paket tidak valid.',
            'tingkatId.required'   => 'Tingkat wajib dipilih.',
            'tingkatId.exists'     => 'Tingkat tidak valid.',
            'waliKelasId.exists'   => 'Guru wali kelas tidak valid.',
        ]);

        $duplicate = Rombel::where('tahun_ajaran', $this->tahunAjaran)
            ->where('wilayah_id', $this->wilayahId)
            ->where('paket_id', $this->paketId)
            ->where('tingkat_id', $this->tingkatId)
            ->when($this->editId, fn ($q) => $q->where('id', '!=', $this->editId))
            ->exists();

        if ($duplicate) {
            $this->addError('tingkatId', 'Rombel dengan kombinasi Tahun Ajaran, Wilayah, Paket, dan Tingkat ini sudah ada.');
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
                    'tahun_ajaran'  => $this->tahunAjaran,
                    'wilayah_id'    => $this->wilayahId,
                    'paket_id'      => $this->paketId,
                    'tingkat_id'    => $this->tingkatId,
                    'wali_kelas_id' => $this->waliKelasId ?: null,
                    'nama'          => $nama,
                ]
            );

            session()->flash('toast', [
                'type'    => 'success',
                'message' => 'Rombel berhasil ' . ($isEdit ? 'diperbarui' : 'ditambahkan') . '.',
            ]);

            $this->redirect(route('admin.master.rombel'), navigate: false);
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Simpan Rombel', $th);
            $this->dispatch('notify', type: 'error', message: 'Maaf, terjadi kesalahan saat menyimpan.');
        }
    }

    public function render(): View
    {
        $tahunAjarans = $this->tahunAjaranOptions();
        $wilayahs = Wilayah::orderBy('nama')->get();
        $pakets   = Paket::orderBy('nama')->get();
        $tingkats = $this->paketId
            ? Tingkat::where('paket_id', $this->paketId)->orderBy('nama')->get()
            : collect();
        // Hanya guru AKTIF yang bisa dipilih jadi wali kelas. Guru nonaktif tetap
        // tercatat sebagai wali kelas di rombel TA lampau (histori tidak diubah).
        // Pengecualian: wali kelas yang SUDAH terpasang tetap ditampilkan saat edit,
        // supaya nilainya tidak ikut terhapus hanya karena gurunya dinonaktifkan.
        $gurus = Guru::where(function ($q) {
                $q->whereHas('user', fn ($u) => $u->where('is_active', true));
                if ($this->waliKelasId) {
                    $q->orWhere('id', $this->waliKelasId);
                }
            })
            ->orderBy('nama_lengkap')
            ->get();

        return view('livewire.admin.master-data.form-rombel', compact(
            'tahunAjarans', 'wilayahs', 'pakets', 'tingkats', 'gurus'
        ));
    }
}
