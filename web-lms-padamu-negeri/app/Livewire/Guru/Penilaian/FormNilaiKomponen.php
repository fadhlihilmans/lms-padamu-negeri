<?php

namespace App\Livewire\Guru\Penilaian;

use App\Models\Guru;
use App\Models\GuruMapelRombel;
use App\Models\PesertaDidikRombel;
use App\Models\Rapor;
use App\Models\RaporNilaiKomponen;
use App\Models\RaporNilaiMapel;
use App\Services\ErrorLogService;
use App\Services\GradeService;
use App\Services\PeriodeService;
use App\Services\RaporService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app', ['pageTitle' => 'Penilaian Akhir'])]
#[Title('Penilaian Akhir')]
class FormNilaiKomponen extends Component
{
    public string $gmrId  = '';
    public string $search = '';

    /** nilaiAkhir[peserta_didik_id][nama_komponen] => string */
    public array $nilaiAkhir = [];
    /** catatan[peserta_didik_id] => string (per mapel) */
    public array $catatan = [];

    // Modal catatan
    public ?int   $notePdId  = null;
    public string $noteDraft = '';

    public function mount(): void
    {
        $first = $this->guruGmrList()->first();
        if ($first) {
            $this->gmrId = (string) $first->id;
            $this->loadExisting();
        }
    }

    public function updatedGmrId(): void
    {
        $this->loadExisting();
    }

    private function getGuru(): ?Guru
    {
        return Auth::user()?->guru;
    }

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

    private function currentGmr(): ?GuruMapelRombel
    {
        return $this->gmrId ? GuruMapelRombel::with(['mapel', 'rombel'])->find($this->gmrId) : null;
    }

    private function pesertaList()
    {
        $gmr = $this->currentGmr();
        if (! $gmr) {
            return collect();
        }

        return PesertaDidikRombel::with('pesertaDidik')
            ->where('rombel_id', $gmr->rombel_id)
            ->get()
            ->pluck('pesertaDidik')
            ->filter()
            ->sortBy('nama_lengkap')
            ->values();
    }

    /** Muat nilai akhir & catatan tersimpan untuk gmr aktif. */
    private function loadExisting(): void
    {
        $this->nilaiAkhir = [];
        $this->catatan    = [];

        $gmr = $this->currentGmr();
        if (! $gmr) {
            return;
        }

        $periode = app(PeriodeService::class)->getSelected();
        $pdIds   = $this->pesertaList()->pluck('id')->all();

        $rapors = Rapor::where('periode_ajaran_id', $periode?->id)
            ->where('rombel_id', $gmr->rombel_id)
            ->whereIn('peserta_didik_id', $pdIds)
            ->with(['nilaiMapel' => fn ($q) => $q->where('mapel_id', $gmr->mapel_id)->with('komponen')])
            ->get()
            ->keyBy('peserta_didik_id');

        foreach ($pdIds as $pid) {
            $this->nilaiAkhir[$pid] = array_fill_keys(RaporService::KOMPONEN, '');
            $this->catatan[$pid]    = '';

            // Pakai get(): akses $rapors[$pid] pada Collection memanggil offsetGet()
            // yang melempar "Undefined array key" bila PD belum punya baris rapor.
            $rnm = $rapors->get($pid)?->nilaiMapel->first();
            if ($rnm) {
                $this->catatan[$pid] = $rnm->catatan_mapel ?? '';
                foreach ($rnm->komponen as $k) {
                    if (in_array($k->nama_komponen, RaporService::KOMPONEN, true)) {
                        $this->nilaiAkhir[$pid][$k->nama_komponen] = (string) $k->nilai_akhir;
                    }
                }
            }
        }
    }

    /** Salin nilai referensi ke kolom Akhir yang masih kosong. */
    public function copyFromRef(): void
    {
        $gmr = $this->currentGmr();
        if (! $gmr) {
            return;
        }

        $refs = app(RaporService::class)->referensiForGmr($gmr, array_keys($this->nilaiAkhir));
        foreach ($this->nilaiAkhir as $pid => $komp) {
            foreach (RaporService::KOMPONEN as $k) {
                if (($this->nilaiAkhir[$pid][$k] ?? '') === '' && $refs[$pid][$k] !== null) {
                    $this->nilaiAkhir[$pid][$k] = (string) $refs[$pid][$k];
                }
            }
        }
    }

    // ── Catatan modal ────────────────────────────────────────────────────────
    public function openNote(int $pdId): void
    {
        $this->notePdId  = $pdId;
        $this->noteDraft = $this->catatan[$pdId] ?? '';
    }

    public function saveNote(): void
    {
        if ($this->notePdId !== null) {
            $this->catatan[$this->notePdId] = trim($this->noteDraft);
        }
        $this->closeNote();
    }

    public function closeNote(): void
    {
        $this->notePdId  = null;
        $this->noteDraft = '';
    }

    public function save(): void
    {
        $gmr  = $this->currentGmr();
        $guru = $this->getGuru();

        if (! $gmr || ! $guru || $gmr->guru_id !== $guru->id) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Mata pelajaran tidak valid.']);
            return;
        }

        // Validasi tiap nilai yang diisi (0–100).
        $rules = [];
        $messages = [];
        foreach ($this->nilaiAkhir as $pid => $komp) {
            foreach (RaporService::KOMPONEN as $k) {
                if (($komp[$k] ?? '') !== '') {
                    $rules["nilaiAkhir.$pid.$k"] = ['integer', 'min:0', 'max:100'];
                    $messages["nilaiAkhir.$pid.$k.integer"] = 'Nilai harus bilangan bulat 0–100.';
                    $messages["nilaiAkhir.$pid.$k.min"]     = 'Nilai minimal 0.';
                    $messages["nilaiAkhir.$pid.$k.max"]     = 'Nilai maksimal 100.';
                }
            }
        }
        $this->validate($rules, $messages);

        $periode = app(PeriodeService::class)->getSelected();
        $grade   = app(GradeService::class);
        $refs    = app(RaporService::class)->referensiForGmr($gmr, array_keys($this->nilaiAkhir));

        try {
            $tersimpan = 0;
            DB::transaction(function () use ($gmr, $periode, $grade, $refs, &$tersimpan) {
                foreach ($this->nilaiAkhir as $pid => $komp) {
                    $adaNilai = collect($komp)->contains(fn ($v) => $v !== '' && $v !== null);
                    $adaCatatan = trim($this->catatan[$pid] ?? '') !== '';
                    if (! $adaNilai && ! $adaCatatan) {
                        continue;
                    }

                    $rapor = Rapor::firstOrCreate(
                        ['peserta_didik_id' => $pid, 'periode_ajaran_id' => $periode->id],
                        ['rombel_id' => $gmr->rombel_id, 'status' => 'draft'],
                    );

                    $rnm = RaporNilaiMapel::updateOrCreate(
                        ['rapor_id' => $rapor->id, 'mapel_id' => $gmr->mapel_id],
                        ['diinput_oleh' => Auth::id(), 'catatan_mapel' => trim($this->catatan[$pid] ?? '') ?: null],
                    );

                    foreach (RaporService::KOMPONEN as $k) {
                        $val = $komp[$k] ?? '';
                        if ($val === '' || $val === null) {
                            continue;
                        }
                        $nilai = (int) $val;
                        RaporNilaiKomponen::updateOrCreate(
                            ['rapor_nilai_mapel_id' => $rnm->id, 'nama_komponen' => $k],
                            [
                                'nilai_referensi' => $refs[$pid][$k],
                                'nilai_akhir'     => $nilai,
                                'grade'           => $grade->konversi($nilai),
                            ],
                        );
                    }
                    $tersimpan++;
                }
            });

            $this->dispatch('notify', ['type' => 'success', 'message' => "Nilai tersimpan ({$tersimpan} peserta didik)."]);
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Simpan Nilai Rapor', $th);
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Terjadi kesalahan, silakan coba lagi.']);
        }
    }

    public function render(): View
    {
        $gmrList = $this->guruGmrList();
        $gmr     = $this->currentGmr();
        $periode = app(PeriodeService::class)->getSelected();

        $peserta = $this->pesertaList()
            ->when($this->search, fn ($c) => $c->filter(fn ($pd) => str_contains(
                strtolower($pd->nama_lengkap . ' ' . $pd->nipd), strtolower($this->search)
            )))->values();

        $refs   = $gmr ? app(RaporService::class)->referensiForGmr($gmr, $this->pesertaList()->pluck('id')->all()) : [];
        $grade  = app(GradeService::class);

        // Grade ringkas per PD (dari rata-rata komponen Akhir yang terisi) + hitung belum lengkap.
        $rowGrade = [];
        $belum    = 0;
        foreach ($this->pesertaList() as $pd) {
            $vals = collect(RaporService::KOMPONEN)
                ->map(fn ($k) => $this->nilaiAkhir[$pd->id][$k] ?? '')
                ->filter(fn ($v) => $v !== '' && $v !== null);

            $rowGrade[$pd->id] = $vals->isNotEmpty() ? $grade->konversi((int) round($vals->avg())) : null;
            if ($vals->count() < count(RaporService::KOMPONEN)) {
                $belum++;
            }
        }

        return view('livewire.guru.penilaian.form-nilai-komponen', [
            'gmrList'  => $gmrList,
            'gmr'      => $gmr,
            'periode'  => $periode,
            'peserta'  => $peserta,
            'refs'     => $refs,
            'komponen' => RaporService::KOMPONEN,
            'rowGrade' => $rowGrade,
            'belum'    => $belum,
        ]);
    }
}
