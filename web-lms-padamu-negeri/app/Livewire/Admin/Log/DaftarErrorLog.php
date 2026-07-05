<?php

namespace App\Livewire\Admin\Log;

use App\Models\ErrorLog;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app', ['pageTitle' => 'Log Error'])]
#[Title('Log Error')]
class DaftarErrorLog extends Component
{
    use WithPagination;

    public string $search  = '';
    public string $tanggal = '';
    public int    $perPage = 15;

    public ?int $confirmDeleteId = null;
    public bool $showDeleteAll   = false;
    public ?int $detailId        = null;

    public function showDetail(int $id): void
    {
        $this->detailId = $id;
    }

    public function closeDetail(): void
    {
        $this->detailId = null;
    }

    public function updatingSearch(): void  { $this->resetPage(); }
    public function updatingTanggal(): void { $this->resetPage(); }
    public function updatingPerPage(): void { $this->resetPage(); }

    public function delete(): void
    {
        if ($this->confirmDeleteId) {
            ErrorLog::where('id', $this->confirmDeleteId)->delete();
            $this->confirmDeleteId = null;
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Log dihapus.']);
        }
    }

    public function deleteAll(): void
    {
        ErrorLog::query()->delete();
        $this->showDeleteAll = false;
        $this->resetPage();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Semua log error dihapus.']);
    }

    public function render(): View
    {
        $logs = ErrorLog::with('user')
            ->when($this->search, fn ($q) => $q->where(fn ($s) => $s
                ->where('aksi', 'like', '%' . $this->search . '%')
                ->orWhere('pesan_error', 'like', '%' . $this->search . '%')))
            ->when($this->tanggal, fn ($q) => $q->whereDate('created_at', $this->tanggal))
            ->latest()
            ->paginate($this->perPage);

        $detail = $this->detailId
            ? ErrorLog::with('user')->find($this->detailId)
            : null;

        return view('livewire.admin.log.daftar-error-log', compact('logs', 'detail'));
    }
}
