<?php

namespace App\Livewire\Guru\Materi;

use App\Models\GuruMapelRombel;
use App\Models\Materi;
use App\Models\MateriLampiran;
use App\Services\ErrorLogService;
use App\Services\PeriodeService;
use App\Services\SettingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('components.layouts.app', ['pageTitle' => 'Materi'])]
#[Title('Materi')]
class DaftarMateri extends Component
{
    use WithFileUploads, WithPagination;

    // ── Filter ────────────────────────────────────────────────────────────────
    #[Url] public string $search  = '';
    #[Url] public int    $perPage = 10;
    #[Url] public string $gmrId   = '';

    // ── Form ─────────────────────────────────────────────────────────────────
    public bool   $showForm = false;
    public ?int   $editId   = null;
    public string $judul    = '';
    public string $isi      = '';

    // Upload — satu zona untuk file & gambar
    public array $lampiranBaru = [];

    // Link video
    public array  $linkVideo = [];
    public string $inputUrl  = '';

    // Lampiran existing (edit mode)
    public array $lampiranExisting = [];
    public array $lampiranHapus   = [];

    // ── Delete ────────────────────────────────────────────────────────────────
    public ?int $confirmDeleteId = null;

    // ── Lifecycle ─────────────────────────────────────────────────────────────

    public function updatingSearch(): void  { $this->resetPage(); }
    public function updatingPerPage(): void { $this->resetPage(); }
    public function updatedGmrId(): void    { $this->resetPage(); $this->closeForm(); }

    // ── Form actions ─────────────────────────────────────────────────────────

    public function openCreateForm(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function openEditForm(int $id): void
    {
        $materi = $this->authorizeMateri($id);
        if (! $materi) return;

        $this->resetForm();
        $this->editId = $id;
        $this->judul  = $materi->judul;
        $this->isi    = $materi->isi ?? '';

        $this->lampiranExisting = $materi->lampiran->map(fn($l) => [
            'id'        => $l->id,
            'tipe'      => $l->tipe,
            'file_path' => $l->file_path,
            'url'       => $l->url,
            'nama_asli' => $l->nama_asli,
        ])->toArray();

        $this->showForm = true;
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->resetForm();
    }

    public function addLinkVideo(): void
    {
        $url = trim($this->inputUrl);
        if (! $url) {
            $this->addError('inputUrl', 'URL tidak boleh kosong.');
            return;
        }
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            $this->addError('inputUrl', 'Format URL tidak valid.');
            return;
        }
        $this->linkVideo[] = $url;
        $this->inputUrl    = '';
        $this->resetErrorBag('inputUrl');
    }

    public function removeLinkVideo(int $index): void
    {
        array_splice($this->linkVideo, $index, 1);
        $this->linkVideo = array_values($this->linkVideo);
    }

    public function removeLampiranBaru(int $index): void
    {
        array_splice($this->lampiranBaru, $index, 1);
        $this->lampiranBaru = array_values($this->lampiranBaru);
    }

    public function toggleHapusLampiran(int $id): void
    {
        if (in_array($id, $this->lampiranHapus)) {
            $this->lampiranHapus = array_values(array_diff($this->lampiranHapus, [$id]));
        } else {
            $this->lampiranHapus[] = $id;
        }
    }

    public function save(): void
    {
        $guru = Auth::user()->guru;
        if (! $guru || ! $this->gmrId) return;

        $gmr = GuruMapelRombel::where('id', $this->gmrId)
            ->where('guru_id', $guru->id)
            ->first();

        if (! $gmr) {
            $this->dispatch('notify', type: 'error', message: 'Pemetaan tidak valid.');
            return;
        }

        $maxMb = app(SettingService::class)->get('max_upload_materi_mb', 10);
        $maxKb = $maxMb * 1024;

        $this->validate([
            'judul'             => 'required|string|max:200',
            'lampiranBaru.*'    => "nullable|file|max:{$maxKb}",
        ], [
            'judul.required'        => 'Judul materi wajib diisi.',
            'judul.max'             => 'Judul maksimal 200 karakter.',
            'lampiranBaru.*.max'    => "Ukuran file maksimal {$maxMb}MB.",
        ]);

        try {
            $isEdit = (bool) $this->editId;

            $materi = Materi::updateOrCreate(
                ['id' => $this->editId],
                [
                    'guru_mapel_rombel_id' => $gmr->id,
                    'judul'                => $this->judul,
                    'isi'                  => $this->isi ?: null,
                ]
            );

            // Hapus lampiran yang di-mark hapus
            if ($this->lampiranHapus) {
                $toDelete = MateriLampiran::whereIn('id', $this->lampiranHapus)
                    ->where('materi_id', $materi->id)
                    ->get();
                foreach ($toDelete as $l) {
                    if ($l->file_path) Storage::disk('public')->delete($l->file_path);
                    $l->delete();
                }
            }

            $urutan = MateriLampiran::where('materi_id', $materi->id)->max('urutan') ?? 0;

            // Upload file/gambar baru — deteksi tipe dari MIME
            foreach ($this->lampiranBaru as $file) {
                $mime = $file->getMimeType() ?? '';
                $tipe = str_starts_with($mime, 'image/') ? 'gambar' : 'file';
                $path = $file->store('materi', 'public');
                MateriLampiran::create([
                    'materi_id' => $materi->id,
                    'tipe'      => $tipe,
                    'file_path' => $path,
                    'nama_asli' => $file->getClientOriginalName(),
                    'urutan'    => ++$urutan,
                ]);
            }

            // Simpan link video baru
            foreach ($this->linkVideo as $url) {
                MateriLampiran::create([
                    'materi_id' => $materi->id,
                    'tipe'      => 'link_video',
                    'url'       => $url,
                    'urutan'    => ++$urutan,
                ]);
            }

            $this->closeForm();
            $this->dispatch('notify', type: 'success', message: 'Materi berhasil ' . ($isEdit ? 'diperbarui' : 'ditambahkan') . '.');
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Simpan Materi', $th);
            $this->dispatch('notify', type: 'error', message: 'Maaf, terjadi kesalahan saat menyimpan.');
        }
    }

    public function confirmDelete(int $id): void { $this->confirmDeleteId = $id; }

    public function delete(): void
    {
        $id = $this->confirmDeleteId;
        $this->confirmDeleteId = null;
        if (! $id) return;

        $materi = $this->authorizeMateri($id);
        if (! $materi) return;

        try {
            foreach ($materi->lampiran as $l) {
                if ($l->file_path) Storage::disk('public')->delete($l->file_path);
            }
            $materi->delete();
            $this->dispatch('notify', type: 'success', message: 'Materi berhasil dihapus.');
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Hapus Materi', $th);
            $this->dispatch('notify', type: 'error', message: 'Maaf, terjadi kesalahan saat menghapus.');
        }
    }

    // ── Render ───────────────────────────────────────────────────────────────

    public function render(): View
    {
        $guru    = Auth::user()->guru;
        $periode = app(PeriodeService::class)->getSelected();

        $pemetaan    = collect();
        $gmrSelected = null;
        $materi      = collect();

        if ($guru) {
            $pemetaanQuery = GuruMapelRombel::with(['mapel', 'rombel'])
                ->where('guru_id', $guru->id);
            if ($periode) {
                $pemetaanQuery->where('periode_ajaran_id', $periode->id);
            }
            $pemetaan = $pemetaanQuery->orderBy('mapel_id')->get();

            if (! $this->gmrId && $pemetaan->isNotEmpty()) {
                $this->gmrId = (string) $pemetaan->first()->id;
            }

            if ($this->gmrId) {
                $gmrSelected = $pemetaan->firstWhere('id', $this->gmrId);

                $materi = Materi::with('lampiran')
                    ->where('guru_mapel_rombel_id', $this->gmrId)
                    ->when($this->search, fn($q) => $q->where('judul', 'like', '%' . $this->search . '%'))
                    ->latest()
                    ->paginate($this->perPage);
            }
        }

        $maxMb = app(SettingService::class)->get('max_upload_materi_mb', 10);

        return view('livewire.guru.materi.daftar-materi', compact('pemetaan', 'gmrSelected', 'materi', 'maxMb'));
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function authorizeMateri(int $id): ?Materi
    {
        $guru = Auth::user()->guru;
        if (! $guru) return null;

        $materi = Materi::with(['guruMapelRombel', 'lampiran'])->find($id);
        if (! $materi || $materi->guruMapelRombel->guru_id !== $guru->id) {
            $this->dispatch('notify', type: 'error', message: 'Anda tidak memiliki akses ke materi ini.');
            return null;
        }
        return $materi;
    }

    private function resetForm(): void
    {
        $this->editId           = null;
        $this->judul            = '';
        $this->isi              = '';
        $this->lampiranBaru     = [];
        $this->linkVideo        = [];
        $this->inputUrl         = '';
        $this->lampiranExisting = [];
        $this->lampiranHapus    = [];
        $this->resetValidation();
    }
}
