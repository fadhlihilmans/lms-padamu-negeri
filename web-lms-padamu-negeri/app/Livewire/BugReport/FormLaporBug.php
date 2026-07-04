<?php

namespace App\Livewire\BugReport;

use App\Models\BugReport;
use App\Services\ErrorLogService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('components.layouts.app', ['pageTitle' => 'Lapor Bug'])]
#[Title('Lapor Bug')]
class FormLaporBug extends Component
{
    use WithFileUploads, WithPagination;

    public string $judul      = '';
    public string $deskripsi  = '';
    public $screenshot        = null;
    public string $halamanUrl = '';

    public const STATUS = [
        'baru'     => ['Baru', 'bg-blue-50 text-blue-700 border-blue-200'],
        'diproses' => ['Diproses', 'bg-amber-50 text-amber-700 border-amber-200'],
        'selesai'  => ['Selesai', 'bg-green-50 text-green-700 border-green-200'],
        'ditolak'  => ['Ditolak', 'bg-[#f0f4f8] text-[#757686] border-[#c5c5d7]'],
    ];

    public function mount(): void
    {
        // Halaman asal (dari mana pengguna menekan Lapor Bug).
        $this->halamanUrl = request('from') ?: (url()->previous() ?: '');
    }

    public function submit(): void
    {
        $this->validate([
            'judul'      => ['required', 'string', 'max:150'],
            'deskripsi'  => ['required', 'string', 'max:5000'],
            'screenshot' => ['nullable', 'image', 'max:4096'],
        ], [
            'judul.required'     => 'Judul laporan wajib diisi.',
            'deskripsi.required' => 'Deskripsi masalah wajib diisi.',
            'screenshot.image'   => 'Lampiran harus berupa gambar.',
            'screenshot.max'     => 'Ukuran gambar maksimal 4 MB.',
        ]);

        try {
            $path = $this->screenshot ? $this->screenshot->store('bug-report', 'public') : null;

            BugReport::create([
                'user_id'         => Auth::id(),
                'judul'           => $this->judul,
                'deskripsi'       => $this->deskripsi,
                'screenshot_path' => $path,
                'halaman_url'     => $this->halamanUrl ?: null,
                'status'          => 'baru',
            ]);

            $this->reset('judul', 'deskripsi', 'screenshot');
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Laporan terkirim. Terima kasih!']);
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Kirim Bug Report', $th);
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Gagal mengirim laporan, silakan coba lagi.']);
        }
    }

    public function render(): View
    {
        $laporan = BugReport::where('user_id', Auth::id())
            ->latest()
            ->paginate(5);

        return view('livewire.bug-report.form-lapor-bug', [
            'laporan' => $laporan,
            'statusMeta' => self::STATUS,
        ]);
    }
}
