<?php

namespace App\Livewire\Guru\Cbt;

use App\Models\Cbt;
use App\Models\Guru;
use App\Models\GuruMapelRombel;
use App\Services\ErrorLogService;
use App\Services\PeriodeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app', ['pageTitle' => 'CBT'])]
#[Title('CBT')]
class DaftarCbt extends Component
{
    use WithPagination;

    // GMR selector (konteks)
    public string $mapelId = '';
    public string $gmrId   = '';

    // Toolbar
    public string $search       = '';
    public string $statusFilter = '';
    public int    $perPage      = 10;

    // Modal form
    public bool   $showForm               = false;
    public ?int   $editId                 = null;
    public string $namaUjian              = '';
    public string $tanggalMulai           = '';
    public string $jamMulai               = '';
    public string $durasiMenit            = '';
    public string $kkm                    = '';
    public bool   $tampilkanNilaiOtomatis = false;
    /** true bila CBT sudah mulai (berlangsung/selesai) → jadwal dikunci. */
    public bool   $scheduleLocked         = false;

    // Delete confirm
    public ?int $confirmDeleteId = null;

    public function updatingSearch(): void       { $this->resetPage(); }
    public function updatingPerPage(): void      { $this->resetPage(); }
    public function updatingStatusFilter(): void { $this->resetPage(); }
    public function updatingGmrId(): void        { $this->resetPage(); }

    public function mount(): void
    {
        // Pilih gmr pertama sebagai konteks awal supaya tabel langsung tampil.
        $first = $this->guruGmrList()->first();
        if ($first) {
            $this->mapelId = (string) $first->mapel_id;
            $this->gmrId   = (string) $first->id;
        }
    }

    public function updatedMapelId(): void
    {
        // Saat mapel berganti, arahkan gmr ke rombel pertama pada mapel tsb.
        $firstOfMapel = $this->guruGmrList()
            ->firstWhere('mapel_id', (int) $this->mapelId);
        $this->gmrId = $firstOfMapel ? (string) $firstOfMapel->id : '';
        $this->resetPage();
    }

    private function getGuru(): ?Guru
    {
        return Auth::user()?->guru;
    }

    /** Semua GMR guru pada periode terpilih (dipakai untuk selector). */
    private function guruGmrList()
    {
        $guru    = $this->getGuru();
        $periode = app(PeriodeService::class)->getSelected();

        if (! $guru || ! $periode) {
            return collect();
        }

        return GuruMapelRombel::with(['mapel', 'rombel'])
            ->where('guru_id', $guru->id)
            ->where('tahun_ajaran', $periode->tahun_ajaran)
            ->get();
    }

    private function authorizeGuru(Cbt $cbt): void
    {
        $guru = $this->getGuru();
        $gmr  = GuruMapelRombel::find($cbt->guru_mapel_rombel_id);
        abort_unless($guru && $gmr && $gmr->guru_id === $guru->id, 403);
    }

    public function openCreateForm(): void
    {
        if (! $this->gmrId) {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Pilih mata pelajaran & rombel terlebih dahulu.']);
            return;
        }
        $this->resetFormFields();
        $this->editId   = null;
        $this->showForm = true;
    }

    public function openEditForm(int $id): void
    {
        $cbt = Cbt::findOrFail($id);
        $this->authorizeGuru($cbt);

        $this->resetFormFields();
        $this->editId                 = $cbt->id;
        $this->namaUjian              = $cbt->nama_ujian;
        $this->tanggalMulai           = $cbt->tanggal_mulai->format('Y-m-d');
        $this->jamMulai               = $cbt->tanggal_mulai->format('H:i');
        $this->durasiMenit            = (string) $cbt->durasi_menit;
        $this->kkm                    = (string) $cbt->kkm;
        $this->tampilkanNilaiOtomatis = (bool) $cbt->tampilkan_nilai_otomatis;
        // Jadwal dikunci jika ujian sudah mulai — hanya nama/KKM/tampilkan nilai yang boleh diubah.
        $this->scheduleLocked         = $cbt->tanggal_mulai->isPast();
        $this->showForm               = true;
    }

    public function closeForm(): void
    {
        $this->resetFormFields();
        $this->showForm = false;
    }

    public function save()
    {
        // Saat jadwal dikunci (ujian sudah mulai), tanggal/jam/durasi tak divalidasi/diubah.
        $editLocked = $this->editId && $this->scheduleLocked;

        $rules = [
            'namaUjian' => ['required', 'string', 'max:200'],
            'kkm'       => ['required', 'integer', 'min:0', 'max:100'],
        ];
        if (! $editLocked) {
            $rules += [
                'tanggalMulai' => ['required', 'date'],
                'jamMulai'     => ['required'],
                'durasiMenit'  => ['required', 'integer', 'min:1', 'max:1440'],
            ];
        }

        $this->validate($rules, [
            'namaUjian.required'    => 'Nama ujian wajib diisi.',
            'tanggalMulai.required' => 'Tanggal mulai wajib diisi.',
            'jamMulai.required'     => 'Jam mulai wajib diisi.',
            'durasiMenit.required'  => 'Durasi wajib diisi.',
            'durasiMenit.min'       => 'Durasi minimal 1 menit.',
            'durasiMenit.max'       => 'Durasi maksimal 1440 menit (24 jam).',
            'kkm.required'          => 'KKM wajib diisi.',
            'kkm.max'               => 'KKM maksimal 100.',
        ]);

        $guru = $this->getGuru();
        $gmr  = GuruMapelRombel::find($this->gmrId);

        if (! $guru || ! $gmr || $gmr->guru_id !== $guru->id) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Konteks mapel/rombel tidak valid.']);
            return;
        }

        try {
            if ($editLocked) {
                // Hanya field yang aman diubah setelah ujian berjalan.
                $cbt = Cbt::findOrFail($this->editId);
                $this->authorizeGuru($cbt);
                $cbt->update([
                    'nama_ujian'                => $this->namaUjian,
                    'kkm'                       => $this->kkm,
                    'tampilkan_nilai_otomatis' => $this->tampilkanNilaiOtomatis,
                ]);

                $this->closeForm();
                $this->dispatch('notify', ['type' => 'success', 'message' => 'CBT berhasil diperbarui.']);
                return;
            }

            $datetime = $this->tanggalMulai . ' ' . $this->jamMulai;

            $data = [
                'nama_ujian'                => $this->namaUjian,
                'tanggal_mulai'            => $datetime,
                'durasi_menit'              => $this->durasiMenit,
                'kkm'                       => $this->kkm,
                'tampilkan_nilai_otomatis' => $this->tampilkanNilaiOtomatis,
            ];

            if ($this->editId) {
                $cbt = Cbt::findOrFail($this->editId);
                $this->authorizeGuru($cbt);
                $cbt->update($data);

                $this->closeForm();
                $this->dispatch('notify', ['type' => 'success', 'message' => 'CBT berhasil diperbarui.']);
                return;
            }

            // jenis_cbt dideteksi ulang dari komposisi soal di FormSoalCbt; default sementara.
            $data['guru_mapel_rombel_id'] = $gmr->id;
            // TRANSAKSI → semester tempat CBT dibuat (Revisi Tahap 3).
            $data['periode_ajaran_id']    = app(\App\Services\PeriodeService::class)->getSelected()?->id;
            $data['jenis_cbt']            = 'pilihan_ganda';
            $cbt = Cbt::create($data);

            $this->closeForm();
            session()->flash('toast', ['type' => 'success', 'message' => 'CBT dibuat. Silakan tambahkan soal.']);

            if (Route::has('guru.cbt.soal')) {
                return $this->redirect(route('guru.cbt.soal', $cbt->id), navigate: true);
            }

            $this->dispatch('notify', ['type' => 'success', 'message' => 'CBT dibuat.']);
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Simpan CBT', $th);
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
        if (! $this->confirmDeleteId) {
            return;
        }

        try {
            $cbt = Cbt::findOrFail($this->confirmDeleteId);
            $this->authorizeGuru($cbt);
            $cbt->delete();

            $this->confirmDeleteId = null;
            $this->dispatch('notify', ['type' => 'success', 'message' => 'CBT dihapus.']);
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Hapus CBT', $th);
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Gagal menghapus CBT.']);
        }
    }

    private function resetFormFields(): void
    {
        $this->editId                 = null;
        $this->namaUjian              = '';
        $this->tanggalMulai           = '';
        $this->jamMulai               = '';
        $this->durasiMenit            = '';
        $this->kkm                    = '';
        $this->tampilkanNilaiOtomatis = false;
        $this->scheduleLocked         = false;
        $this->resetValidation();
    }

    public function render(): View
    {
        $gmrList = $this->guruGmrList();

        // Opsi selector
        $mapelOptions = $gmrList->unique('mapel_id')->values();
        $rombelOptions = $this->mapelId
            ? $gmrList->where('mapel_id', (int) $this->mapelId)->values()
            : collect();

        $gmrSelected = $this->gmrId ? $gmrList->firstWhere('id', (int) $this->gmrId) : null;
        $periode     = app(PeriodeService::class)->getSelected();

        $now = now();
        $cbt = collect();

        if ($gmrSelected) {
            $cbt = Cbt::withCount([
                    'soal',
                    'hasilCbt',
                    'soal as uraian_count' => fn ($q) => $q->where('tipe_soal', 'uraian'),
                ])
                ->where('guru_mapel_rombel_id', $gmrSelected->id)
                ->when($this->search, fn ($q) => $q->where('nama_ujian', 'like', '%' . $this->search . '%'))
                ->when($this->statusFilter === 'terjadwal', fn ($q) => $q->where('tanggal_mulai', '>', $now))
                ->when($this->statusFilter === 'berlangsung', fn ($q) => $q
                    ->where('tanggal_mulai', '<=', $now)
                    ->whereRaw('DATE_ADD(tanggal_mulai, INTERVAL durasi_menit MINUTE) >= ?', [$now]))
                ->when($this->statusFilter === 'selesai', fn ($q) => $q
                    ->whereRaw('DATE_ADD(tanggal_mulai, INTERVAL durasi_menit MINUTE) < ?', [$now]))
                ->orderByDesc('tanggal_mulai')
                ->paginate($this->perPage);
        }

        return view('livewire.guru.cbt.daftar-cbt', compact(
            'gmrList', 'mapelOptions', 'rombelOptions', 'gmrSelected', 'periode', 'cbt'
        ));
    }
}
