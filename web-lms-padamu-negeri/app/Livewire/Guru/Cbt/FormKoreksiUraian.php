<?php

namespace App\Livewire\Guru\Cbt;

use App\Models\Cbt;
use App\Models\CbtJawabanPeserta;
use App\Models\GuruMapelRombel;
use App\Models\HasilCbt;
use App\Services\ErrorLogService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app', ['pageTitle' => 'Koreksi Uraian'])]
#[Title('Koreksi Uraian')]
class FormKoreksiUraian extends Component
{
    public Cbt $cbt;

    public string $filterStatus  = '';
    public ?int   $selectedHasilId = null;

    /**
     * Mode ubah untuk PD terpilih.
     * - Menunggu koreksi → true (langsung boleh isi).
     * - Sudah dikoreksi  → false (terkunci; guru harus klik "Edit Nilai").
     * Ini mencegah skor yang sudah final ke-edit/tersimpan ulang tanpa sengaja
     * saat guru berpindah antar peserta didik (keputusan Tahap 0).
     */
    public bool $editMode = false;

    /** skor[cbt_soal_id] => nilai 0..100 */
    public array $skor = [];

    public function mount(int $cbtId): void
    {
        $cbt = Cbt::with(['guruMapelRombel.mapel', 'guruMapelRombel.rombel'])->findOrFail($cbtId);
        $this->authorizeGuru($cbt);
        $this->cbt = $cbt;

        // Pilih PD pertama yang masih menunggu koreksi (atau pertama yang submit).
        $first = $this->hasilQuery()->where('status_penilaian', 'menunggu_koreksi')->first()
            ?? $this->hasilQuery()->first();

        if ($first) {
            $this->selectHasil($first->id);
        }
    }

    private function authorizeGuru(Cbt $cbt): void
    {
        $guru = Auth::user()?->guru;
        $gmr  = GuruMapelRombel::find($cbt->guru_mapel_rombel_id);
        abort_unless($guru && $gmr && $gmr->guru_id === $guru->id, 403);
    }

    /** Hasil PD yang sudah submit CBT ini, urut nama PD. */
    private function hasilQuery()
    {
        return HasilCbt::query()
            ->where('cbt_id', $this->cbt->id)
            ->whereNotNull('waktu_submit')
            ->whereHas('pesertaDidik')
            ->with('pesertaDidik')
            ->join('peserta_didik', 'peserta_didik.id', '=', 'hasil_cbt.peserta_didik_id')
            ->orderBy('peserta_didik.nama_lengkap')
            ->select('hasil_cbt.*');
    }

    private function uraianSoal()
    {
        return $this->cbt->soal()->where('tipe_soal', 'uraian')->orderBy('id')->get();
    }

    public function selectHasil(int $hasilId): void
    {
        $hasil = HasilCbt::where('cbt_id', $this->cbt->id)->findOrFail($hasilId);

        $this->selectedHasilId = $hasil->id;

        // Muat skor tersimpan (jika sudah pernah dikoreksi).
        $jawaban = CbtJawabanPeserta::where('hasil_cbt_id', $hasil->id)
            ->pluck('skor_uraian', 'cbt_soal_id');

        $this->skor = [];
        foreach ($this->uraianSoal() as $soal) {
            $this->skor[$soal->id] = $jawaban[$soal->id] ?? '';
        }

        // Terkunci bila sudah dikoreksi; butuh koreksi → langsung bisa diisi.
        $this->editMode = $hasil->status_penilaian !== 'selesai_dinilai';
        $this->resetValidation();
    }

    /** Buka kunci untuk mengubah nilai yang sudah final (aksi eksplisit). */
    public function enableEdit(): void
    {
        $this->editMode = true;
    }

    public function goToPrev(): void
    {
        $ids = $this->hasilQuery()->pluck('hasil_cbt.id')->all();
        $idx = array_search($this->selectedHasilId, $ids, true);
        if ($idx !== false && $idx > 0) {
            $this->selectHasil($ids[$idx - 1]);
        }
    }

    public function saveAndNext(): void
    {
        // Tolak menyimpan bila terkunci (jaring pengaman selain tombol yang disembunyikan).
        if (! $this->selectedHasilId || ! $this->editMode) {
            return;
        }

        $uraianSoal = $this->uraianSoal();

        $rules = [];
        foreach ($uraianSoal as $soal) {
            $rules["skor.{$soal->id}"] = ['required', 'integer', 'min:0', 'max:100'];
        }
        $this->validate($rules, [], collect($rules)->keys()
            ->mapWithKeys(fn ($k) => [$k . '.required' => 'Nilai wajib diisi (0–100).'])
            ->all());

        try {
            $hasil = HasilCbt::where('cbt_id', $this->cbt->id)->findOrFail($this->selectedHasilId);

            foreach ($uraianSoal as $soal) {
                CbtJawabanPeserta::updateOrCreate(
                    ['hasil_cbt_id' => $hasil->id, 'cbt_soal_id' => $soal->id],
                    ['skor_uraian' => (int) $this->skor[$soal->id]],
                );
            }

            // nilai_uraian = rata-rata skor uraian (0–100).
            $nilaiUraian = (int) round(collect($this->skor)->map(fn ($v) => (int) $v)->avg());

            // nilai_akhir = PG & Uraian BERBOBOT (Konfigurasi Nilai, default 70:30).
            // Sebelumnya ditimbang menurut JUMLAH SOAL — diganti sesuai Revisi Tahap 4.
            // Bila CBT tak punya soal PG, bobot dinormalisasi → uraian dipakai penuh.
            $adaPg   = $this->cbt->soal()->where('tipe_soal', 'pilihan_ganda')->exists();
            $nilaiPg = $adaPg ? (float) ($hasil->nilai_pg ?? 0) : null;

            $nilaiAkhir = app(\App\Services\NilaiConfigService::class)
                ->nilaiCbt($nilaiPg, (float) $nilaiUraian) ?? 0;

            $hasil->update([
                'nilai_uraian'     => $nilaiUraian,
                'nilai_akhir'      => $nilaiAkhir,
                'status_penilaian' => 'selesai_dinilai',
            ]);

            $this->dispatch('notify', ['type' => 'success', 'message' => 'Koreksi disimpan.']);

            // Lanjut ke PD berikutnya yang masih menunggu.
            $next = $this->hasilQuery()
                ->where('status_penilaian', 'menunggu_koreksi')
                ->where('hasil_cbt.id', '!=', $hasil->id)
                ->first();

            if ($next) {
                $this->selectHasil($next->id);
            } else {
                // Tetap di PD ini, tapi KUNCI kembali (baru saja jadi final).
                $this->editMode = false;
                $this->dispatch('notify', ['type' => 'success', 'message' => 'Semua koreksi uraian selesai.']);
            }
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Simpan Koreksi Uraian', $th);
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Terjadi kesalahan, silakan coba lagi.']);
        }
    }

    public function render(): View
    {
        $uraianSoal = $this->uraianSoal();

        $hasilList = $this->hasilQuery()
            ->when($this->filterStatus === 'menunggu', fn ($q) => $q->where('status_penilaian', 'menunggu_koreksi'))
            ->when($this->filterStatus === 'selesai', fn ($q) => $q->where('status_penilaian', 'selesai_dinilai'))
            ->get();

        $allHasil    = $this->hasilQuery()->get();
        $totalSubmit = $allHasil->count();
        $sudah       = $allHasil->where('status_penilaian', 'selesai_dinilai')->count();

        $current = $this->selectedHasilId
            ? HasilCbt::with('pesertaDidik')->find($this->selectedHasilId)
            : null;

        // Jawaban PD untuk soal uraian (keyed by cbt_soal_id).
        $jawabanMap = collect();
        if ($current) {
            $jawabanMap = CbtJawabanPeserta::where('hasil_cbt_id', $current->id)
                ->get()->keyBy('cbt_soal_id');
        }

        // Preview total.
        $jumlahUraian = $uraianSoal->count();
        $jumlahPg     = $this->cbt->soal()->where('tipe_soal', 'pilihan_ganda')->count();
        $totalSoal    = $jumlahPg + $jumlahUraian;
        $nilaiPg      = (int) ($current?->nilai_pg ?? 0);

        $filled    = collect($this->skor)->filter(fn ($v) => $v !== '' && $v !== null);
        $uraianAvg = $filled->count() ? (int) round($filled->map(fn ($v) => (int) $v)->avg()) : null;

        // Preview nilai akhir = PG & Uraian BERBOBOT (konsisten dgn saat disimpan).
        $previewAkhir = $uraianAvg !== null
            ? app(\App\Services\NilaiConfigService::class)
                ->nilaiCbt($jumlahPg > 0 ? (float) $nilaiPg : null, (float) $uraianAvg)
            : null;

        return view('livewire.guru.cbt.form-koreksi-uraian', compact(
            'uraianSoal', 'hasilList', 'current', 'jawabanMap',
            'totalSubmit', 'sudah', 'nilaiPg', 'uraianAvg', 'previewAkhir'
        ));
    }
}
