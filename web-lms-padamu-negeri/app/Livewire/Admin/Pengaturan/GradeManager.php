<?php

namespace App\Livewire\Admin\Pengaturan;

use App\Models\KonfigurasiGrade;
use App\Services\ErrorLogService;
use App\Services\GradeService;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app', ['pageTitle' => 'Konfigurasi Grade'])]
#[Title('Konfigurasi Grade')]
class GradeManager extends Component
{
    /** ranges[konfigurasi_grade_id] => ['grade'=>, 'min'=>, 'max'=>] */
    public array $ranges = [];

    public function mount(): void
    {
        $this->loadRanges();
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

    public function render(): View
    {
        return view('livewire.admin.pengaturan.grade-manager');
    }
}
