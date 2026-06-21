<?php

namespace App\Livewire\Guru\Cbt;

use App\Models\Cbt;
use App\Models\Guru;
use App\Models\GuruMapelRombel;
use App\Services\ErrorLogService;
use App\Services\PeriodeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app', ['pageTitle' => 'Form CBT'])]
#[Title('Form CBT')]
class FormCbt extends Component
{
    public ?int $editId = null;

    // Form fields
    public string $formGmrId              = '';
    public string $namaUjian              = '';
    public string $kkm                    = '';
    public string $tanggalMulai           = '';
    public string $durasiMenit            = '';
    public bool   $tampilkanNilaiOtomatis = false;

    public function mount(?int $cbtId = null): void
    {
        if ($cbtId) {
            $cbt = Cbt::findOrFail($cbtId);
            $this->authorizeGuru($cbt);

            $this->editId                 = $cbt->id;
            $this->formGmrId              = (string) $cbt->guru_mapel_rombel_id;
            $this->namaUjian              = $cbt->nama_ujian;
            $this->kkm                    = (string) $cbt->kkm;
            $this->tanggalMulai           = $cbt->tanggal_mulai->format('Y-m-d\TH:i');
            $this->durasiMenit            = (string) $cbt->durasi_menit;
            $this->tampilkanNilaiOtomatis = (bool) $cbt->tampilkan_nilai_otomatis;
        }
    }

    private function getGuru(): ?Guru
    {
        return Auth::user()?->guru;
    }

    private function authorizeGuru(Cbt $cbt): void
    {
        $guru = $this->getGuru();
        $gmr  = GuruMapelRombel::find($cbt->guru_mapel_rombel_id);
        abort_unless($guru && $gmr && $gmr->guru_id === $guru->id, 403);
    }

    public function save()
    {
        $this->validate([
            'formGmrId'    => ['required', 'exists:guru_mapel_rombel,id'],
            'namaUjian'    => ['required', 'string', 'max:200'],
            'kkm'          => ['required', 'integer', 'min:0', 'max:100'],
            'tanggalMulai' => ['required', 'date'],
            'durasiMenit'  => ['required', 'integer', 'min:1', 'max:1440'],
        ], [
            'formGmrId.required'    => 'Mata pelajaran / rombel wajib dipilih.',
            'namaUjian.required'    => 'Nama ujian wajib diisi.',
            'kkm.required'          => 'KKM wajib diisi.',
            'kkm.min'               => 'KKM minimal 0.',
            'kkm.max'               => 'KKM maksimal 100.',
            'tanggalMulai.required' => 'Tanggal & waktu mulai wajib diisi.',
            'durasiMenit.required'  => 'Durasi wajib diisi.',
            'durasiMenit.min'       => 'Durasi minimal 1 menit.',
            'durasiMenit.max'       => 'Durasi maksimal 1440 menit (24 jam).',
        ]);

        $guru = $this->getGuru();
        $gmr  = GuruMapelRombel::find($this->formGmrId);

        if (! $guru || ! $gmr || $gmr->guru_id !== $guru->id) {
            $this->addError('formGmrId', 'Mata pelajaran tidak valid.');
            return;
        }

        try {
            $data = [
                'guru_mapel_rombel_id'      => $this->formGmrId,
                'nama_ujian'                => $this->namaUjian,
                'kkm'                       => $this->kkm,
                'tanggal_mulai'            => $this->tanggalMulai,
                'durasi_menit'              => $this->durasiMenit,
                'tampilkan_nilai_otomatis' => $this->tampilkanNilaiOtomatis,
            ];

            if ($this->editId) {
                $cbt = Cbt::findOrFail($this->editId);
                $this->authorizeGuru($cbt);
                $cbt->update($data);

                session()->flash('toast', ['type' => 'success', 'message' => 'CBT berhasil diperbarui.']);
                return $this->redirect(route('guru.cbt'), navigate: true);
            }

            // Baru: jenis_cbt dideteksi dari komposisi soal nanti; default sementara.
            $data['jenis_cbt'] = 'pilihan_ganda';
            $cbt = Cbt::create($data);

            session()->flash('toast', ['type' => 'success', 'message' => 'CBT dibuat. Lanjutkan dengan menambahkan soal.']);

            if (\Illuminate\Support\Facades\Route::has('guru.cbt.soal')) {
                return $this->redirect(route('guru.cbt.soal', $cbt->id), navigate: true);
            }

            return $this->redirect(route('guru.cbt'), navigate: true);
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Simpan CBT', $th);
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Terjadi kesalahan, silakan coba lagi.']);
        }
    }

    public function render(): View
    {
        $guru    = $this->getGuru();
        $periode = app(PeriodeService::class)->getSelected();

        $gmrList = collect();
        if ($guru && $periode) {
            $gmrList = GuruMapelRombel::with(['mapel', 'rombel'])
                ->where('guru_id', $guru->id)
                ->where('periode_ajaran_id', $periode->id)
                ->get();
        }

        return view('livewire.guru.cbt.form-cbt', compact('gmrList'));
    }
}
