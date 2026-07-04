<?php

namespace App\Livewire\PesertaDidik\Tugas;

use App\Models\PesertaDidikRombel;
use App\Models\Tugas;
use App\Models\TugasSubmisi;
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

#[Layout('components.layouts.app', ['pageTitle' => 'Detail Tugas'])]
#[Title('Detail Tugas')]
class SubmisiTugasPD extends Component
{
    use WithFileUploads;

    public Tugas          $tugas;
    public ?TugasSubmisi  $submisi = null;

    // Form fields
    public string $isiText  = '';
    public $fileBaru        = null;

    // Dipanggil dari JS Trix saat isi berubah
    public function setIsiText(string $value): void
    {
        $this->isiText = $value;
    }

    // Confirm re-submit (if already submitted)
    public bool $confirmResubmit = false;

    // Modal kumpulkan/kirim ulang jawaban
    public bool $showSubmitModal = false;

    public function openSubmitModal(): void
    {
        $this->showSubmitModal = true;
    }

    public function closeSubmitModal(): void
    {
        $this->showSubmitModal = false;
        $this->resetValidation();
    }

    public function mount(int $tugasId): void
    {
        $pd      = Auth::user()?->pesertaDidik;
        $periode = app(PeriodeService::class)->getSelected();

        $tugas = Tugas::with([
            'guruMapelRombel.mapel',
            'guruMapelRombel.rombel',
            'guruMapelRombel.guru',
        ])->findOrFail($tugasId);

        $rombelId = $tugas->guruMapelRombel?->rombel_id;
        $boleh    = $pd && $rombelId && PesertaDidikRombel::where('peserta_didik_id', $pd->id)
            ->where('rombel_id', $rombelId)
            ->exists();

        abort_unless($boleh, 403, 'Anda tidak memiliki akses ke tugas ini.');

        $this->tugas   = $tugas;
        $this->submisi = TugasSubmisi::where('tugas_id', $tugas->id)
            ->where('peserta_didik_id', $pd->id)
            ->first();

        // Pre-fill form with existing submission
        if ($this->submisi) {
            $this->isiText = $this->submisi->isi_text ?? '';
        }
    }

    public function submit(): void
    {
        $maxMb = app(SettingService::class)->get('max_upload_tugas_mb', 10);
        $maxKb = $maxMb * 1024;

        $this->validate([
            'fileBaru' => ['nullable', 'file', "max:{$maxKb}"],
            'isiText'  => ['nullable', 'string', 'max:10000'],
        ], [
            'fileBaru.file' => 'Jawaban harus berupa file.',
            'fileBaru.max'  => "Ukuran file maksimal {$maxMb}MB.",
            'isiText.max'   => 'Jawaban teks maksimal 10.000 karakter.',
        ]);

        if (! $this->fileBaru && ! trim($this->isiText)) {
            $this->addError('isiText', 'Isi teks jawaban atau unggah file jawaban terlebih dahulu.');
            return;
        }

        $pd = Auth::user()?->pesertaDidik;

        try {
            $isLate      = $this->tugas->deadline->isPast();
            $filePath    = null;

            if ($this->fileBaru) {
                // Hapus file lama jika ada
                if ($this->submisi?->file_path) {
                    Storage::disk('public')->delete($this->submisi->file_path);
                }
                $filePath = $this->fileBaru->store('tugas/submisi', 'public');
            } else {
                $filePath = $this->submisi?->file_path;
            }

            $data = [
                'file_path'    => $filePath,
                'isi_text'     => trim($this->isiText) ?: null,
                'waktu_submit' => now(),
            ];

            if ($this->submisi) {
                // Re-submit: reset nilai jika guru belum menilai
                if ($this->submisi->nilai === null) {
                    $this->submisi->update($data);
                } else {
                    // Sudah dinilai — reset nilai agar guru review ulang
                    $this->submisi->update(array_merge($data, ['nilai' => null]));
                }
                $this->submisi->refresh();
            } else {
                $this->submisi = TugasSubmisi::create(array_merge($data, [
                    'tugas_id'        => $this->tugas->id,
                    'peserta_didik_id' => $pd->id,
                ]));
            }

            $this->fileBaru        = null;
            $this->isiText         = '';
            $this->confirmResubmit = false;
            $this->showSubmitModal = false;
            $this->dispatch('clear-trix-submisi');

            $msg = $isLate
                ? 'Tugas dikumpulkan (ditandai terlambat karena melewati deadline).'
                : 'Tugas berhasil dikumpulkan!';

            $this->dispatch('notify', ['type' => 'success', 'message' => $msg]);
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Submisi Tugas PD', $th);
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Gagal mengumpulkan tugas. Coba lagi.']);
        }
    }

    public function render(): View
    {
        return view('livewire.peserta-didik.tugas.submisi-tugas-p-d');
    }
}
