<?php

namespace App\Livewire\Guru\Tugas;

use App\Models\GuruMapelRombel;
use App\Models\PesertaDidikRombel;
use App\Models\Tugas;
use App\Models\TugasSubmisi;
use App\Services\ErrorLogService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app', ['pageTitle' => 'Submisi Tugas'])]
#[Title('Submisi Tugas')]
class DaftarSubmisi extends Component
{
    public Tugas $tugas;

    // Filter
    public string $search       = '';
    public string $filterStatus = '';

    // Grade modal
    public ?int    $gradeTargetId = null;
    public string  $nilaiInput    = '';

    public function mount(int $tugasId): void
    {
        $tugas = Tugas::with([
            'guruMapelRombel.mapel',
            'guruMapelRombel.rombel',
        ])->findOrFail($tugasId);

        $guru = Auth::user()?->guru;
        $gmr  = $tugas->guruMapelRombel;
        abort_unless($guru && $gmr && $gmr->guru_id === $guru->id, 403);

        $this->tugas = $tugas;
    }

    public function openGradeModal(int $submisiId): void
    {
        $submisi = TugasSubmisi::findOrFail($submisiId);
        $this->gradeTargetId = $submisiId;
        $this->nilaiInput    = (string) ($submisi->nilai ?? '');
        $this->resetValidation();
    }

    public function closeGradeModal(): void
    {
        $this->gradeTargetId = null;
        $this->nilaiInput    = '';
        $this->resetValidation();
    }

    public function saveNilai(): void
    {
        $this->validate([
            'nilaiInput' => ['required', 'integer', 'min:0', 'max:100'],
        ], [
            'nilaiInput.required' => 'Nilai wajib diisi.',
            'nilaiInput.integer'  => 'Nilai harus bilangan bulat.',
            'nilaiInput.min'      => 'Nilai minimal 0.',
            'nilaiInput.max'      => 'Nilai maksimal 100.',
        ]);

        try {
            $submisi = TugasSubmisi::findOrFail($this->gradeTargetId);
            $submisi->update(['nilai' => (int) $this->nilaiInput]);
            $this->closeGradeModal();
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Nilai berhasil disimpan.']);
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Simpan Nilai Tugas', $th);
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Gagal menyimpan nilai.']);
        }
    }

    public function render(): View
    {
        $rombelId = $this->tugas->guruMapelRombel?->rombel_id;

        $pdList = PesertaDidikRombel::with(['pesertaDidik'])
            ->where('rombel_id', $rombelId)
            ->when($this->search, fn($q) => $q->whereHas(
                'pesertaDidik',
                fn($s) => $s->where('nama_lengkap', 'like', '%' . $this->search . '%')
            ))
            ->get()
            ->sortBy(fn($pdr) => $pdr->pesertaDidik?->nama_lengkap)
            ->values();

        $submisiByPdId = TugasSubmisi::where('tugas_id', $this->tugas->id)
            ->get()
            ->keyBy('peserta_didik_id');

        // Apply status filter after loading
        if ($this->filterStatus) {
            $pdList = $pdList->filter(function ($pdr) use ($submisiByPdId) {
                $sub = $submisiByPdId[$pdr->peserta_didik_id] ?? null;
                $status = $this->resolveStatus($sub);
                return $status === $this->filterStatus;
            })->values();
        }

        $stats = [
            'total'    => PesertaDidikRombel::where('rombel_id', $rombelId)->count(),
            'sudah'    => $submisiByPdId->whereIn('peserta_didik_id', PesertaDidikRombel::where('rombel_id', $rombelId)->pluck('peserta_didik_id'))->count(),
            'dinilai'  => $submisiByPdId->whereNotNull('nilai')->whereIn('peserta_didik_id', PesertaDidikRombel::where('rombel_id', $rombelId)->pluck('peserta_didik_id'))->count(),
        ];

        return view('livewire.guru.tugas.daftar-submisi', compact('pdList', 'submisiByPdId', 'stats'));
    }

    public function resolveStatus(?TugasSubmisi $submisi): string
    {
        if (! $submisi) return 'belum';
        return $submisi->waktu_submit->gt($this->tugas->deadline) ? 'terlambat' : 'tepat';
    }
}
