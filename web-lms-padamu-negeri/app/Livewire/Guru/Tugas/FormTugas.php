<?php

namespace App\Livewire\Guru\Tugas;

use App\Models\GuruMapelRombel;
use App\Models\Tugas;
use App\Services\ErrorLogService;
use App\Services\ImageService;
use App\Services\PeriodeService;
use App\Services\SettingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app', ['pageTitle' => 'Form Tugas'])]
#[Title('Form Tugas')]
class FormTugas extends Component
{
    use WithFileUploads;

    public ?int $editId = null;
    public string $gmrId = '';

    public string  $judul            = '';
    public string  $deskripsi        = '';
    public string  $deadline         = '';
    public ?string $lampiranExisting = null;
    public $lampiranBaru             = null;
    public bool    $hapusLampiran    = false;

    public function mount(?int $id = null): void
    {
        $guru = Auth::user()?->guru;

        if ($id) {
            $tugas = Tugas::find($id);
            if (! $tugas || ! $guru || $tugas->guruMapelRombel->guru_id !== $guru->id) {
                abort(403, 'Anda tidak memiliki akses ke tugas ini.');
            }

            $this->editId           = $id;
            $this->gmrId            = (string) $tugas->guru_mapel_rombel_id;
            $this->judul            = $tugas->judul;
            $this->deskripsi        = $tugas->deskripsi ?? '';
            $this->deadline         = $tugas->deadline->format('Y-m-d\TH:i');
            $this->lampiranExisting = $tugas->lampiran_path;
        } else {
            $gmrFromQuery = (int) request()->query('gmr');
            if ($guru && $gmrFromQuery) {
                $valid = GuruMapelRombel::where('id', $gmrFromQuery)->where('guru_id', $guru->id)->exists();
                $this->gmrId = $valid ? (string) $gmrFromQuery : '';
            }
        }
    }

    public function setDeskripsi(string $value): void
    {
        $this->deskripsi = $value;
    }

    public function save(): void
    {
        $maxMb = app(SettingService::class)->get('max_upload_tugas_mb', 10);
        $maxKb = $maxMb * 1024;

        $this->validate([
            'gmrId'        => ['required', 'exists:guru_mapel_rombel,id'],
            'judul'        => ['required', 'string', 'max:200'],
            'deskripsi'    => ['nullable', 'string'],
            'deadline'     => ['required', 'date'],
            'lampiranBaru' => ['nullable', 'file', "max:{$maxKb}"],
        ], [
            'gmrId.required'    => 'Mata pelajaran / rombel wajib dipilih.',
            'gmrId.exists'      => 'Mata pelajaran / rombel tidak valid.',
            'judul.required'    => 'Judul tugas wajib diisi.',
            'judul.max'         => 'Judul maksimal 200 karakter.',
            'deadline.required' => 'Batas waktu pengumpulan wajib diisi.',
            'deadline.date'     => 'Batas waktu tidak valid.',
            'lampiranBaru.file' => 'Lampiran harus berupa file.',
            'lampiranBaru.max'  => "Ukuran lampiran maksimal {$maxMb}MB.",
        ]);

        $guru = Auth::user()?->guru;
        $gmr  = GuruMapelRombel::findOrFail($this->gmrId);

        if (! $guru || $gmr->guru_id !== $guru->id) {
            $this->addError('gmrId', 'Mata pelajaran tidak valid.');
            return;
        }

        try {
            $lampiranPath = null;

            if ($this->editId) {
                $tugas = Tugas::findOrFail($this->editId);
                abort_unless($tugas->guruMapelRombel->guru_id === $guru->id, 403);
                $lampiranPath = $tugas->lampiran_path;

                if ($this->lampiranBaru) {
                    if ($lampiranPath) Storage::disk('public')->delete($lampiranPath);
                    $lampiranPath = app(ImageService::class)->store($this->lampiranBaru, 'tugas/lampiran', 'public');
                } elseif ($this->hapusLampiran) {
                    if ($lampiranPath) Storage::disk('public')->delete($lampiranPath);
                    $lampiranPath = null;
                }

                $tugas->update([
                    'guru_mapel_rombel_id' => $this->gmrId,
                    'judul'                => $this->judul,
                    'deskripsi'            => $this->deskripsi ?: null,
                    'lampiran_path'        => $lampiranPath,
                    'deadline'             => $this->deadline,
                ]);
            } else {
                if ($this->lampiranBaru) {
                    $lampiranPath = app(ImageService::class)->store($this->lampiranBaru, 'tugas/lampiran', 'public');
                }

                Tugas::create([
                    'guru_mapel_rombel_id' => $this->gmrId,
                    'periode_ajaran_id'    => app(\App\Services\PeriodeService::class)->getSelected()?->id,  // TRANSAKSI → semester
                    'judul'                => $this->judul,
                    'deskripsi'            => $this->deskripsi ?: null,
                    'lampiran_path'        => $lampiranPath,
                    'deadline'             => $this->deadline,
                ]);
            }

            $this->dispatch('notify', type: 'success', message: 'Tugas berhasil disimpan.');
            $this->redirectRoute('guru.tugas', ['gmr' => $this->gmrId], navigate: true);
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Simpan Tugas', $th);
            $this->dispatch('notify', type: 'error', message: 'Terjadi kesalahan, silakan coba lagi.');
        }
    }

    public function render(): View
    {
        $guru    = Auth::user()?->guru;
        $periode = app(PeriodeService::class)->getSelected();

        $gmrList = collect();
        if ($guru && $periode) {
            $gmrList = GuruMapelRombel::with(['mapel', 'rombel'])
                ->where('guru_id', $guru->id)
                ->where('tahun_ajaran', $periode->tahun_ajaran)
                ->get();
        }

        $gmrSelected = $this->gmrId ? $gmrList->firstWhere('id', (int) $this->gmrId) : null;
        $maxMb       = app(SettingService::class)->get('max_upload_tugas_mb', 10);

        return view('livewire.guru.tugas.form-tugas', compact('gmrList', 'gmrSelected', 'maxMb'));
    }
}
