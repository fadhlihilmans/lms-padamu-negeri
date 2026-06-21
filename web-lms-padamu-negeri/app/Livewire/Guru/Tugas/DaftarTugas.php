<?php

namespace App\Livewire\Guru\Tugas;

use App\Models\Guru;
use App\Models\GuruMapelRombel;
use App\Models\PesertaDidikRombel;
use App\Models\Tugas;
use App\Services\ErrorLogService;
use App\Services\PeriodeService;
use App\Services\SettingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('components.layouts.app', ['pageTitle' => 'Tugas'])]
#[Title('Tugas')]
class DaftarTugas extends Component
{
    use WithFileUploads, WithPagination;

    // Filter
    public string $gmrId   = '';
    public string $search  = '';
    public int    $perPage = 12;

    // Form state
    public bool    $showForm         = false;
    public ?int    $editId           = null;
    public string  $formGmrId       = '';
    public string  $judul            = '';
    public string  $deskripsi        = '';
    public string  $deadline         = '';
    public ?string $lampiranExisting = null;
    public $lampiranBaru             = null;
    public bool    $hapusLampiran    = false;

    // Dipanggil dari JS Trix saat isi berubah
    public function setDeskripsi(string $value): void
    {
        $this->deskripsi = $value;
    }

    // Delete confirm
    public ?int $confirmDeleteId = null;

    public function updatingSearch(): void  { $this->resetPage(); }
    public function updatingPerPage(): void { $this->resetPage(); }
    public function updatingGmrId(): void   { $this->resetPage(); }

    private function getGuru(): ?Guru
    {
        return Auth::user()?->guru;
    }

    public function openCreateForm(): void
    {
        $this->resetFormFields();
        $this->formGmrId = $this->gmrId;
        $this->editId    = null;
        $this->showForm  = true;
    }

    public function openEditForm(int $id): void
    {
        $tugas = Tugas::findOrFail($id);
        $this->authorizeGuru($tugas);

        $this->resetFormFields();
        $this->editId           = $id;
        $this->formGmrId        = (string) $tugas->guru_mapel_rombel_id;
        $this->judul            = $tugas->judul;
        $this->deskripsi        = $tugas->deskripsi ?? '';
        $this->deadline         = $tugas->deadline->format('Y-m-d\TH:i');
        $this->lampiranExisting = $tugas->lampiran_path;
        $this->showForm         = true;
    }

    public function cancelForm(): void
    {
        $this->resetFormFields();
        $this->showForm = false;
    }

    public function save(): void
    {
        $maxMb = app(SettingService::class)->get('max_upload_tugas_mb', 10);
        $maxKb = $maxMb * 1024;

        $this->validate([
            'formGmrId'    => ['required', 'exists:guru_mapel_rombel,id'],
            'judul'        => ['required', 'string', 'max:200'],
            'deskripsi'    => ['nullable', 'string'],
            'deadline'     => ['required', 'date'],
            'lampiranBaru' => ['nullable', 'file', "max:{$maxKb}"],
        ]);

        $guru = $this->getGuru();
        $gmr  = GuruMapelRombel::findOrFail($this->formGmrId);

        if (! $guru || $gmr->guru_id !== $guru->id) {
            $this->addError('formGmrId', 'Mata pelajaran tidak valid.');
            return;
        }

        try {
            $lampiranPath = null;

            if ($this->editId) {
                $tugas        = Tugas::findOrFail($this->editId);
                $this->authorizeGuru($tugas);
                $lampiranPath = $tugas->lampiran_path;

                if ($this->lampiranBaru) {
                    if ($lampiranPath) Storage::disk('public')->delete($lampiranPath);
                    $lampiranPath = $this->lampiranBaru->store('tugas/lampiran', 'public');
                } elseif ($this->hapusLampiran) {
                    if ($lampiranPath) Storage::disk('public')->delete($lampiranPath);
                    $lampiranPath = null;
                }

                $tugas->update([
                    'guru_mapel_rombel_id' => $this->formGmrId,
                    'judul'                => $this->judul,
                    'deskripsi'            => $this->deskripsi ?: null,
                    'lampiran_path'        => $lampiranPath,
                    'deadline'             => $this->deadline,
                ]);
            } else {
                if ($this->lampiranBaru) {
                    $lampiranPath = $this->lampiranBaru->store('tugas/lampiran', 'public');
                }

                Tugas::create([
                    'guru_mapel_rombel_id' => $this->formGmrId,
                    'judul'                => $this->judul,
                    'deskripsi'            => $this->deskripsi ?: null,
                    'lampiran_path'        => $lampiranPath,
                    'deadline'             => $this->deadline,
                ]);
            }

            $this->resetFormFields();
            $this->showForm = false;
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Tugas berhasil disimpan.']);
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Simpan Tugas', $th);
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Terjadi kesalahan, silakan coba lagi.']);
        }
    }

    public function confirmDelete(int $id): void
    {
        $this->confirmDeleteId = $id;
    }

    public function cancelDelete(): void
    {
        $this->confirmDeleteId = null;
    }

    public function delete(): void
    {
        if (! $this->confirmDeleteId) return;

        try {
            $tugas = Tugas::findOrFail($this->confirmDeleteId);
            $this->authorizeGuru($tugas);

            if ($tugas->lampiran_path) {
                Storage::disk('public')->delete($tugas->lampiran_path);
            }

            $tugas->delete();
            $this->confirmDeleteId = null;
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Tugas dihapus.']);
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Hapus Tugas', $th);
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Gagal menghapus tugas.']);
        }
    }

    private function resetFormFields(): void
    {
        $this->editId           = null;
        $this->formGmrId        = '';
        $this->judul            = '';
        $this->deskripsi        = '';
        $this->deadline         = '';
        $this->lampiranExisting = null;
        $this->lampiranBaru     = null;
        $this->hapusLampiran    = false;
        $this->resetValidation();
    }

    private function authorizeGuru(Tugas $tugas): void
    {
        $guru = $this->getGuru();
        $gmr  = GuruMapelRombel::find($tugas->guru_mapel_rombel_id);
        abort_unless($guru && $gmr && $gmr->guru_id === $guru->id, 403);
    }

    public function render(): View
    {
        $guru    = $this->getGuru();
        $periode = app(PeriodeService::class)->getSelected();

        $gmrList         = collect();
        $tugas           = collect();
        $pdCountByGmrId  = [];

        if ($guru && $periode) {
            $gmrList = GuruMapelRombel::with(['mapel', 'rombel'])
                ->where('guru_id', $guru->id)
                ->where('periode_ajaran_id', $periode->id)
                ->get();

            $gmrIds = $gmrList->pluck('id')->toArray();

            foreach ($gmrList as $gmr) {
                $pdCountByGmrId[$gmr->id] = PesertaDidikRombel::where('rombel_id', $gmr->rombel_id)->count();
            }

            if ($gmrIds) {
                $tugas = Tugas::with(['guruMapelRombel.mapel', 'guruMapelRombel.rombel'])
                    ->withCount('submisi')
                    ->whereIn('guru_mapel_rombel_id', $gmrIds)
                    ->when($this->gmrId, fn($q) => $q->where('guru_mapel_rombel_id', $this->gmrId))
                    ->when($this->search, fn($q) => $q->where('judul', 'like', '%' . $this->search . '%'))
                    ->orderByDesc('deadline')
                    ->paginate($this->perPage);
            }
        }

        return view('livewire.guru.tugas.daftar-tugas', compact(
            'gmrList', 'tugas', 'pdCountByGmrId'
        ));
    }
}
