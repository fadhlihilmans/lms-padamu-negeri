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

    public ?int $periodeAjaranId = null;
    public ?int $wilayahId       = null;
    public ?int $paketId         = null;
    public ?int $tingkatId       = null;
    public ?int $waliKelasId     = null;

    public function mount(?int $id = null): void
    {
        if ($id) {
            $rombel = Rombel::findOrFail($id);
            $this->editId          = $rombel->id;
            $this->periodeAjaranId = $rombel->periode_ajaran_id;
            $this->wilayahId       = $rombel->wilayah_id;
            $this->paketId         = $rombel->paket_id;
            $this->tingkatId       = $rombel->tingkat_id;
            $this->waliKelasId     = $rombel->wali_kelas_id;
        } else {
            $aktif = PeriodeAjaran::where('is_aktif', true)->first();
            $this->periodeAjaranId = $aktif?->id;
        }
    }

    public function updatedPaketId(): void
    {
        $this->tingkatId = null;
    }

    public function save(): void
    {
        $this->validate([
            'periodeAjaranId' => 'required|exists:periode_ajaran,id',
            'wilayahId'       => 'required|exists:wilayah,id',
            'paketId'         => 'required|exists:paket,id',
            'tingkatId'       => 'required|exists:tingkat,id',
            'waliKelasId'     => 'nullable|exists:guru,id',
        ], [
            'periodeAjaranId.required' => 'Periode Ajaran wajib dipilih.',
            'periodeAjaranId.exists'   => 'Periode Ajaran tidak valid.',
            'wilayahId.required'       => 'Wilayah wajib dipilih.',
            'wilayahId.exists'         => 'Wilayah tidak valid.',
            'paketId.required'         => 'Paket wajib dipilih.',
            'paketId.exists'           => 'Paket tidak valid.',
            'tingkatId.required'       => 'Tingkat wajib dipilih.',
            'tingkatId.exists'         => 'Tingkat tidak valid.',
            'waliKelasId.exists'       => 'Guru wali kelas tidak valid.',
        ]);

        $duplicate = Rombel::where('periode_ajaran_id', $this->periodeAjaranId)
            ->where('wilayah_id', $this->wilayahId)
            ->where('paket_id', $this->paketId)
            ->where('tingkat_id', $this->tingkatId)
            ->when($this->editId, fn ($q) => $q->where('id', '!=', $this->editId))
            ->exists();

        if ($duplicate) {
            $this->addError('tingkatId', 'Rombel dengan kombinasi Periode, Wilayah, Paket, dan Tingkat ini sudah ada.');
            return;
        }

        $tingkat       = Tingkat::find($this->tingkatId);
        $wilayah       = Wilayah::find($this->wilayahId);
        $paket         = Paket::find($this->paketId);
        $periodeAjaran = PeriodeAjaran::find($this->periodeAjaranId);
        $nama          = "{$tingkat->nama} {$wilayah->nama} {$paket->nama} – TA {$periodeAjaran->tahun_ajaran}";

        $isEdit = (bool) $this->editId;

        try {
            Rombel::updateOrCreate(
                ['id' => $this->editId],
                [
                    'periode_ajaran_id' => $this->periodeAjaranId,
                    'wilayah_id'        => $this->wilayahId,
                    'paket_id'          => $this->paketId,
                    'tingkat_id'        => $this->tingkatId,
                    'wali_kelas_id'     => $this->waliKelasId ?: null,
                    'nama'              => $nama,
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
        $periodes = PeriodeAjaran::orderByDesc('id')->get();
        $wilayahs = Wilayah::orderBy('nama')->get();
        $pakets   = Paket::orderBy('nama')->get();
        $tingkats = $this->paketId
            ? Tingkat::where('paket_id', $this->paketId)->orderBy('nama')->get()
            : collect();
        $gurus = Guru::orderBy('nama_lengkap')->get();

        return view('livewire.admin.master-data.form-rombel', compact(
            'periodes', 'wilayahs', 'pakets', 'tingkats', 'gurus'
        ));
    }
}
