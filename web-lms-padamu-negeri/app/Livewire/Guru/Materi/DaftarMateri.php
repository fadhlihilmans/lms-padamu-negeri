<?php

namespace App\Livewire\Guru\Materi;

use App\Models\GuruMapelRombel;
use App\Models\Materi;
use App\Services\ErrorLogService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app', ['pageTitle' => 'Materi'])]
#[Title('Materi')]
class DaftarMateri extends Component
{
    use WithPagination;

    // ── Filter ────────────────────────────────────────────────────────────────
    #[Url] public string $search  = '';
    #[Url] public int    $perPage = 10;
    #[Url(as: 'gmr')] public string $gmrId = '';

    // ── Delete ────────────────────────────────────────────────────────────────
    public ?int $confirmDeleteId = null;

    public function updatingSearch(): void  { $this->resetPage(); }
    public function updatingPerPage(): void { $this->resetPage(); }
    public function updatedGmrId(): void    { $this->resetPage(); }

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
        $pemetaan    = collect();
        $gmrSelected = null;
        $materi      = collect();

        if ($guru) {
            $pemetaanQuery = GuruMapelRombel::with(['mapel', 'rombel.paket'])
                ->where('guru_id', $guru->id);
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

        return view('livewire.guru.materi.daftar-materi', compact('pemetaan', 'gmrSelected', 'materi'));
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
}
