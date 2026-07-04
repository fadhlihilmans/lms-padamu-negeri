<?php

namespace App\Livewire\Guru\Cbt;

use App\Models\Cbt;
use App\Models\CbtSoal;
use App\Models\GuruMapelRombel;
use App\Services\ErrorLogService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app', ['pageTitle' => 'Kelola Soal CBT'])]
#[Title('Kelola Soal CBT')]
class FormSoalCbt extends Component
{
    public Cbt $cbt;

    // Modal form
    public bool   $showForm    = false;
    public ?int   $editSoalId  = null;
    public string $tipeSoal    = 'pilihan_ganda';
    public string $pertanyaan  = '';
    public string $opsiA       = '';
    public string $opsiB       = '';
    public string $opsiC       = '';
    public string $opsiD       = '';
    public string $kunciJawaban = '';

    // Filter tampilan
    public string $filterTipe = '';

    // Delete confirm
    public ?int $confirmDeleteId = null;

    public function mount(int $cbtId): void
    {
        $cbt = Cbt::with(['guruMapelRombel.mapel', 'guruMapelRombel.rombel'])->findOrFail($cbtId);
        $this->authorizeGuru($cbt);
        $this->cbt = $cbt;
    }

    private function authorizeGuru(Cbt $cbt): void
    {
        $guru = Auth::user()?->guru;
        $gmr  = GuruMapelRombel::find($cbt->guru_mapel_rombel_id);
        abort_unless($guru && $gmr && $gmr->guru_id === $guru->id, 403);
    }

    public function openCreateForm(): void
    {
        $this->resetFormFields();
        $this->editSoalId = null;
        $this->tipeSoal   = 'pilihan_ganda';
        $this->showForm   = true;
    }

    public function openEditForm(int $soalId): void
    {
        $soal = $this->cbt->soal()->findOrFail($soalId);

        $this->resetFormFields();
        $this->editSoalId = $soal->id;
        $this->tipeSoal   = $soal->tipe_soal;
        $this->pertanyaan = $soal->pertanyaan;

        if ($soal->tipe_soal === 'pilihan_ganda') {
            $opsi = $soal->pilihan_jawaban ?? [];
            $this->opsiA        = $opsi['A'] ?? '';
            $this->opsiB        = $opsi['B'] ?? '';
            $this->opsiC        = $opsi['C'] ?? '';
            $this->opsiD        = $opsi['D'] ?? '';
            $this->kunciJawaban = $soal->kunci_jawaban ?? '';
        }

        $this->showForm = true;
    }

    public function setTipe(string $tipe): void
    {
        $this->tipeSoal = $tipe;
        $this->resetValidation();
    }

    public function closeForm(): void
    {
        $this->resetFormFields();
        $this->showForm = false;
    }

    public function saveSoal(): void
    {
        $rules = [
            'tipeSoal'   => ['required', 'in:pilihan_ganda,uraian'],
            'pertanyaan' => ['required', 'string'],
        ];

        if ($this->tipeSoal === 'pilihan_ganda') {
            $rules += [
                'opsiA'        => ['required', 'string', 'max:255'],
                'opsiB'        => ['required', 'string', 'max:255'],
                'opsiC'        => ['required', 'string', 'max:255'],
                'opsiD'        => ['required', 'string', 'max:255'],
                'kunciJawaban' => ['required', 'in:A,B,C,D'],
            ];
        }

        $this->validate($rules, [
            'pertanyaan.required'   => 'Teks soal wajib diisi.',
            'opsiA.required'        => 'Pilihan A wajib diisi.',
            'opsiB.required'        => 'Pilihan B wajib diisi.',
            'opsiC.required'        => 'Pilihan C wajib diisi.',
            'opsiD.required'        => 'Pilihan D wajib diisi.',
            'kunciJawaban.required' => 'Tandai kunci jawaban.',
        ]);

        try {
            if ($this->tipeSoal === 'pilihan_ganda') {
                $data = [
                    'tipe_soal'       => 'pilihan_ganda',
                    'pertanyaan'      => $this->pertanyaan,
                    'pilihan_jawaban' => [
                        'A' => $this->opsiA,
                        'B' => $this->opsiB,
                        'C' => $this->opsiC,
                        'D' => $this->opsiD,
                    ],
                    'kunci_jawaban'   => $this->kunciJawaban,
                ];
            } else {
                $data = [
                    'tipe_soal'       => 'uraian',
                    'pertanyaan'      => $this->pertanyaan,
                    'pilihan_jawaban' => null,
                    'kunci_jawaban'   => null,
                ];
            }

            if ($this->editSoalId) {
                $soal = $this->cbt->soal()->findOrFail($this->editSoalId);
                $soal->update($data);
            } else {
                $this->cbt->soal()->create($data);
            }

            $this->syncJenisCbt();
            $this->closeForm();
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Soal berhasil disimpan.']);
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Simpan Soal CBT', $th);
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Terjadi kesalahan, silakan coba lagi.']);
        }
    }

    public function confirmDelete(int $soalId): void
    {
        $this->confirmDeleteId = $soalId;
    }

    public function cancelDelete(): void
    {
        $this->confirmDeleteId = null;
    }

    public function deleteSoal(): void
    {
        if (! $this->confirmDeleteId) {
            return;
        }

        try {
            $soal = $this->cbt->soal()->findOrFail($this->confirmDeleteId);
            $soal->delete();

            $this->syncJenisCbt();
            $this->confirmDeleteId = null;
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Soal dihapus.']);
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Hapus Soal CBT', $th);
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Gagal menghapus soal.']);
        }
    }

    public function moveUp(int $soalId): void
    {
        $this->reorder($soalId, -1);
    }

    public function moveDown(int $soalId): void
    {
        $this->reorder($soalId, 1);
    }

    /**
     * Geser urutan soal dengan menukar isi antar dua soal bersebelahan.
     * Skema cbt_soal tak punya kolom urutan; tabel diurutkan by id, jadi
     * "pindah nomor" = menukar konten (tipe, pertanyaan, opsi, kunci).
     * Dikunci bila CBT sudah dikerjakan agar jawaban peserta tetap valid.
     */
    private function reorder(int $soalId, int $direction): void
    {
        if ($this->cbt->hasilCbt()->exists()) {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Urutan terkunci: CBT sudah dikerjakan peserta didik.']);
            return;
        }

        $all = $this->cbt->soal()->orderBy('id')->get();
        $idx = $all->search(fn ($s) => $s->id === $soalId);

        if ($idx === false) {
            return;
        }

        $targetIdx = $idx + $direction;
        if ($targetIdx < 0 || $targetIdx >= $all->count()) {
            return;
        }

        try {
            $a = $all[$idx];
            $b = $all[$targetIdx];

            $fields = ['tipe_soal', 'pertanyaan', 'pilihan_jawaban', 'kunci_jawaban'];
            $tmp = $a->only($fields);

            foreach ($fields as $f) {
                $a->{$f} = $b->{$f};
                $b->{$f} = $tmp[$f];
            }
            $a->save();
            $b->save();
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Geser Urutan Soal CBT', $th);
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Gagal menggeser urutan soal.']);
        }
    }

    /** Deteksi jenis_cbt dari komposisi soal (PRD 6.9). */
    private function syncJenisCbt(): void
    {
        $tipes = $this->cbt->soal()->pluck('tipe_soal')->unique();

        if ($tipes->count() > 1) {
            $jenis = 'campuran';
        } elseif ($tipes->first() === 'uraian') {
            $jenis = 'uraian';
        } else {
            // kosong atau semua PG
            $jenis = 'pilihan_ganda';
        }

        if ($this->cbt->jenis_cbt !== $jenis) {
            $this->cbt->update(['jenis_cbt' => $jenis]);
            $this->cbt->refresh();
        }
    }

    private function resetFormFields(): void
    {
        $this->editSoalId   = null;
        $this->pertanyaan   = '';
        $this->opsiA        = '';
        $this->opsiB        = '';
        $this->opsiC        = '';
        $this->opsiD        = '';
        $this->kunciJawaban = '';
        $this->resetValidation();
    }

    public function render(): View
    {
        $allSoal   = $this->cbt->soal()->orderBy('id')->get();
        $totalSoal = $allSoal->count();

        $soalList = $this->filterTipe
            ? $allSoal->where('tipe_soal', $this->filterTipe)->values()
            : $allSoal;

        // Reorder hanya saat tampil "Semua" & belum ada yang mengerjakan.
        $canReorder = $this->filterTipe === '' && ! $this->cbt->hasilCbt()->exists();

        return view('livewire.guru.cbt.form-soal-cbt', compact(
            'soalList', 'totalSoal', 'canReorder'
        ));
    }
}
