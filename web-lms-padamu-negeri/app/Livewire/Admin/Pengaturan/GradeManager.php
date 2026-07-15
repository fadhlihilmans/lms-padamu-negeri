<?php

namespace App\Livewire\Admin\Pengaturan;

use App\Models\KonfigurasiGrade;
use App\Models\KonfigurasiNilai;
use App\Services\ErrorLogService;
use App\Services\GradeService;
use App\Services\NilaiConfigService;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Konfigurasi Nilai (Revisi Tahap 4) — dulu bernama "Konfigurasi Grade".
 *
 * Satu halaman, 4 card:
 *   1. Rentang Grade (tabel `konfigurasi_grade`)
 *   2. Bobot Nilai CBT          (PG / Uraian)          ┐
 *   3. Bobot Komponen TUGAS     (Tugas / CBT)          ├ tabel `konfigurasi_nilai`
 *   4. Bobot Nilai Rapor        (TUGAS / SAS/SAT)      ┘
 *
 * Aturan: tiap pasangan bobot WAJIB total 100 (CLAUDE.md #9a).
 */
#[Layout('components.layouts.app', ['pageTitle' => 'Konfigurasi Nilai'])]
#[Title('Konfigurasi Nilai')]
class GradeManager extends Component
{
    /** ranges[konfigurasi_grade_id] => ['grade'=>, 'min'=>, 'max'=>] */
    public array $ranges = [];

    /** bobot[key] => nilai persen (string) */
    public array $bobot = [];

    public function mount(): void
    {
        $this->loadRanges();
        $this->loadBobot();
    }

    private function loadBobot(): void
    {
        $this->bobot = KonfigurasiNilai::pluck('value', 'key')
            ->map(fn ($v) => (string) $v)
            ->all();
    }

    private function loadRanges(): void
    {
        $this->ranges = [];
        foreach (KonfigurasiGrade::orderByDesc('nilai_min')->get() as $g) {
            $this->ranges[$g->id] = [
                'grade' => $g->grade,
                'min'   => (string) $g->nilai_min,
                'max'   => (string) $g->nilai_max,
            ];
        }
    }

    public function save(): void
    {
        // Validasi dasar tiap baris.
        $rules = [];
        foreach ($this->ranges as $id => $r) {
            $rules["ranges.$id.min"] = ['required', 'integer', 'min:0', 'max:100'];
            $rules["ranges.$id.max"] = ['required', 'integer', 'min:0', 'max:100'];
        }
        $this->validate($rules, [], $this->attributeNames());

        // min <= max per baris.
        foreach ($this->ranges as $id => $r) {
            if ((int) $r['min'] > (int) $r['max']) {
                $this->addError("ranges.$id.min", "Nilai minimum grade {$r['grade']} tidak boleh melebihi maksimum.");
                return;
            }
        }

        // Anti-tumpang-tindih antar grade.
        $items = collect($this->ranges)->map(fn ($r, $id) => [
            'id' => $id, 'grade' => $r['grade'], 'min' => (int) $r['min'], 'max' => (int) $r['max'],
        ])->values();

        foreach ($items as $i => $a) {
            foreach ($items as $j => $b) {
                if ($j <= $i) {
                    continue;
                }
                if ($a['min'] <= $b['max'] && $b['min'] <= $a['max']) {
                    $this->addError("ranges.{$a['id']}.min", "Rentang grade {$a['grade']} tumpang tindih dengan grade {$b['grade']}.");
                    return;
                }
            }
        }

        try {
            foreach ($this->ranges as $id => $r) {
                KonfigurasiGrade::where('id', $id)->update([
                    'nilai_min' => (int) $r['min'],
                    'nilai_max' => (int) $r['max'],
                ]);
            }

            app(GradeService::class)->invalidate();
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Konfigurasi grade tersimpan.']);
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Simpan Konfigurasi Grade', $th);
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Terjadi kesalahan, silakan coba lagi.']);
        }
    }

    private function attributeNames(): array
    {
        $names = [];
        foreach ($this->ranges as $id => $r) {
            $names["ranges.$id.min"] = "minimum grade {$r['grade']}";
            $names["ranges.$id.max"] = "maksimum grade {$r['grade']}";
        }
        return $names;
    }

    /**
     * Simpan satu grup bobot (cbt | komponen_tugas | rapor).
     * Tiap pasangan WAJIB total 100 — kalau tidak, nilai jadi tidak bermakna.
     */
    public function saveBobot(string $grup): void
    {
        $keys = KonfigurasiNilai::PASANGAN[$grup] ?? null;
        if (! $keys) {
            return;
        }

        $rules = [];
        foreach ($keys as $k) {
            $rules["bobot.$k"] = ['required', 'integer', 'min:0', 'max:100'];
        }
        $this->validate($rules, [
            'bobot.*.required' => 'Bobot wajib diisi.',
            'bobot.*.integer'  => 'Bobot harus bilangan bulat 0–100.',
            'bobot.*.min'      => 'Bobot minimal 0.',
            'bobot.*.max'      => 'Bobot maksimal 100.',
        ]);

        $total = array_sum(array_map(fn ($k) => (int) ($this->bobot[$k] ?? 0), $keys));
        if ($total !== 100) {
            $this->addError("bobot.{$keys[0]}", "Total bobot harus tepat 100% (sekarang {$total}%).");
            return;
        }

        try {
            foreach ($keys as $k) {
                KonfigurasiNilai::where('key', $k)->update(['value' => (string) (int) $this->bobot[$k]]);
            }

            app(NilaiConfigService::class)->invalidate();
            $this->dispatch('notify', ['type' => 'success', 'message' => KonfigurasiNilai::LABEL_GRUP[$grup] . ' tersimpan.']);
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Simpan Bobot Nilai', $th);
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Terjadi kesalahan, silakan coba lagi.']);
        }
    }

    public function render(): View
    {
        $grupBobot = KonfigurasiNilai::orderBy('id')->get()->groupBy('grup');

        return view('livewire.admin.pengaturan.grade-manager', [
            'grupBobot'  => $grupBobot,
            'labelGrup'  => KonfigurasiNilai::LABEL_GRUP,
        ]);
    }
}
