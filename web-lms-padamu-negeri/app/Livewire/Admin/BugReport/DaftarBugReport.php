<?php

namespace App\Livewire\Admin\BugReport;

use App\Models\BugReport;
use App\Services\ErrorLogService;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app', ['pageTitle' => 'Laporan Bug'])]
#[Title('Laporan Bug')]
class DaftarBugReport extends Component
{
    use WithPagination;

    public string $search       = '';
    public string $filterStatus = '';
    public int    $perPage      = 10;

    // Detail / tindak lanjut
    public ?int   $detailId    = null;
    public string $editStatus  = '';
    public string $editCatatan = '';

    public ?int $confirmDeleteId = null;

    public const STATUS = [
        'baru'     => ['Baru', 'bg-blue-50 text-blue-700 border-blue-200'],
        'diproses' => ['Diproses', 'bg-amber-50 text-amber-700 border-amber-200'],
        'selesai'  => ['Selesai', 'bg-green-50 text-green-700 border-green-200'],
        'ditolak'  => ['Ditolak', 'bg-[#f0f4f8] text-[#757686] border-[#c5c5d7]'],
    ];

    public function updatingSearch(): void       { $this->resetPage(); }
    public function updatingFilterStatus(): void { $this->resetPage(); }
    public function updatingPerPage(): void      { $this->resetPage(); }

    public function openDetail(int $id): void
    {
        $b = BugReport::findOrFail($id);
        $this->detailId    = $b->id;
        $this->editStatus  = $b->status;
        $this->editCatatan = $b->catatan_admin ?? '';
    }

    public function closeDetail(): void
    {
        $this->detailId = null;
        $this->editStatus = '';
        $this->editCatatan = '';
    }

    public function saveStatus(): void
    {
        $this->validate([
            'editStatus'  => ['required', 'in:baru,diproses,selesai,ditolak'],
            'editCatatan' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            BugReport::where('id', $this->detailId)->update([
                'status'        => $this->editStatus,
                'catatan_admin' => trim($this->editCatatan) ?: null,
            ]);
            $this->closeDetail();
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Status laporan diperbarui.']);
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Update Bug Report', $th);
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Terjadi kesalahan, silakan coba lagi.']);
        }
    }

    public function delete(): void
    {
        if (! $this->confirmDeleteId) {
            return;
        }
        try {
            $b = BugReport::find($this->confirmDeleteId);
            if ($b?->screenshot_path) {
                Storage::disk('public')->delete($b->screenshot_path);
            }
            $b?->delete();
            $this->confirmDeleteId = null;
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Laporan dihapus.']);
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Hapus Bug Report', $th);
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Gagal menghapus laporan.']);
        }
    }

    public function render(): View
    {
        $reports = BugReport::with('user.guru', 'user.pesertaDidik')
            ->when($this->search, fn ($q) => $q->where('judul', 'like', '%' . $this->search . '%'))
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->latest()
            ->paginate($this->perPage);

        $detail = $this->detailId ? BugReport::with('user')->find($this->detailId) : null;

        return view('livewire.admin.bug-report.daftar-bug-report', [
            'reports'    => $reports,
            'detail'     => $detail,
            'statusMeta' => self::STATUS,
        ]);
    }
}
