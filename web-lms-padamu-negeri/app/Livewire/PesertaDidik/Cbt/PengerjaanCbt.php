<?php

namespace App\Livewire\PesertaDidik\Cbt;

use App\Models\Cbt;
use App\Models\CbtJawabanPeserta;
use App\Models\HasilCbt;
use App\Models\PesertaDidik;
use App\Models\PesertaDidikRombel;
use App\Services\CbtGradingService;
use App\Services\ErrorLogService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.cbt')]
#[Title('Pengerjaan CBT')]
class PengerjaanCbt extends Component
{
    public Cbt $cbt;
    public HasilCbt $hasil;

    /** answers[cbt_soal_id] => 'A'|'B'|... (PG) atau teks (uraian) */
    public array $answers = [];

    public int $currentIndex = 0;
    public int $deadlineTs   = 0;   // ms epoch, untuk timer Alpine
    public bool $showSubmitModal = false;

    public function mount(int $cbtId): void
    {
        $pd = Auth::user()?->pesertaDidik;
        abort_unless($pd, 403);

        $cbt = Cbt::with('guruMapelRombel')->findOrFail($cbtId);

        // Otorisasi: PD anggota rombel CBT ini.
        $isMember = PesertaDidikRombel::where('peserta_didik_id', $pd->id)
            ->where('rombel_id', $cbt->guruMapelRombel?->rombel_id)
            ->exists();
        abort_unless($isMember, 403);

        // Harus ada soal.
        if ($cbt->soal()->count() === 0) {
            session()->flash('toast', ['type' => 'warning', 'message' => 'Ujian belum memiliki soal.']);
            $this->redirectRoute('peserta-didik.cbt', navigate: true);
            return;
        }

        $now      = now();
        $mulai    = $cbt->tanggal_mulai;
        $windowEnd = $cbt->tanggal_mulai->copy()->addMinutes($cbt->durasi_menit);

        // Belum dibuka.
        if ($now->lt($mulai)) {
            session()->flash('toast', ['type' => 'warning', 'message' => 'Ujian belum dibuka.']);
            $this->redirectRoute('peserta-didik.cbt', navigate: true);
            return;
        }

        $existing = HasilCbt::where('cbt_id', $cbt->id)->where('peserta_didik_id', $pd->id)->first();

        // Sudah submit.
        if ($existing && $existing->waktu_submit) {
            session()->flash('toast', ['type' => 'success', 'message' => 'Kamu sudah menyelesaikan ujian ini.']);
            $this->redirectRoute('peserta-didik.cbt', navigate: true);
            return;
        }

        // Belum pernah mulai & jendela sudah lewat.
        if (! $existing && $now->gt($windowEnd)) {
            session()->flash('toast', ['type' => 'warning', 'message' => 'Waktu ujian telah berakhir.']);
            $this->redirectRoute('peserta-didik.cbt', navigate: true);
            return;
        }

        $this->cbt   = $cbt;
        $this->hasil = app(CbtGradingService::class)->startAttempt($cbt, $pd);

        $deadline = $this->hasil->waktu_mulai->copy()->addMinutes($cbt->durasi_menit);

        // Waktu sudah habis → auto-submit langsung.
        if ($now->gte($deadline)) {
            $this->finalize();
            session()->flash('toast', ['type' => 'warning', 'message' => 'Waktu ujian telah habis. Jawaban otomatis dikumpulkan.']);
            $this->redirectRoute('peserta-didik.cbt', navigate: true);
            return;
        }

        $this->deadlineTs = $deadline->getTimestamp() * 1000;

        // Muat jawaban tersimpan (resume).
        $tersimpan = CbtJawabanPeserta::where('hasil_cbt_id', $this->hasil->id)
            ->pluck('jawaban', 'cbt_soal_id');
        foreach ($this->soalList() as $soal) {
            $this->answers[$soal->id] = $tersimpan[$soal->id] ?? '';
        }
    }

    /** Semua soal urut. */
    private function soalList()
    {
        return $this->cbt->soal()->orderBy('id')->get();
    }

    private function isExpired(): bool
    {
        return $this->hasil->waktu_mulai->copy()->addMinutes($this->cbt->durasi_menit)->isPast();
    }

    public function selectAnswer(int $soalId, string $huruf): void
    {
        if ($this->isExpired()) {
            $this->submitCbt();
            return;
        }
        $this->answers[$soalId] = $huruf;
        $this->persistAnswer($soalId);
    }

    /** Uraian: dipanggil saat blur (wire:model.blur). */
    public function updatedAnswers($value, $key): void
    {
        if ($this->isExpired()) {
            return;
        }
        $this->persistAnswer((int) $key);
    }

    private function persistAnswer(int $soalId): void
    {
        try {
            app(CbtGradingService::class)->saveAnswer(
                $this->hasil,
                $soalId,
                (string) ($this->answers[$soalId] ?? ''),
            );
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Simpan Jawaban CBT', $th);
        }
    }

    public function goTo(int $index): void
    {
        $count = $this->soalList()->count();
        $this->currentIndex = max(0, min($index, $count - 1));
    }

    public function next(): void { $this->goTo($this->currentIndex + 1); }
    public function prev(): void { $this->goTo($this->currentIndex - 1); }

    public function openSubmitModal(): void  { $this->showSubmitModal = true; }
    public function closeSubmitModal(): void { $this->showSubmitModal = false; }

    /** Submit oleh PD (tombol) atau auto (timer habis). */
    public function submitCbt()
    {
        $this->finalize();
        session()->flash('toast', ['type' => 'success', 'message' => 'Ujian berhasil dikumpulkan.']);
        return $this->redirectRoute('peserta-didik.cbt', navigate: true);
    }

    private function finalize(): void
    {
        try {
            $grading = app(CbtGradingService::class);

            // Persist semua jawaban lalu grade.
            foreach ($this->soalList() as $soal) {
                $grading->saveAnswer($this->hasil, $soal->id, (string) ($this->answers[$soal->id] ?? ''));
            }
            $grading->submit($this->hasil);
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Submit CBT', $th);
        }
    }

    public function render(): View
    {
        // mount() mungkin sudah men-set redirect (belum dibuka / sudah submit / dsb).
        if (! isset($this->cbt)) {
            return view('livewire.peserta-didik.cbt.redirecting');
        }

        $soalList = $this->soalList();
        $current  = $soalList[$this->currentIndex] ?? $soalList->first();

        $answeredIds = collect($this->answers)
            ->filter(fn ($v) => $v !== '' && $v !== null)
            ->keys()
            ->map(fn ($k) => (int) $k)
            ->all();

        $answeredCount = count($answeredIds);
        $totalSoal     = $soalList->count();

        return view('livewire.peserta-didik.cbt.pengerjaan-cbt', compact(
            'soalList', 'current', 'answeredIds', 'answeredCount', 'totalSoal'
        ));
    }
}
