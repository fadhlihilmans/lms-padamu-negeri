<?php

namespace App\Livewire\Admin\ImportExcel;

use App\Exports\PesertaDidikTemplateExport;
use App\Services\ImportPesertaDidikService;
use App\Services\PeriodeService;
use App\Services\ErrorLogService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('components.layouts.app', ['pageTitle' => 'Import Peserta Didik'])]
#[Title('Import Peserta Didik')]
class ImportPesertaDidik extends Component
{
    use WithFileUploads;

    public $file = null;

    public bool $showPreview = false;
    public array $previewResults = [];
    public int $previewValid = 0;
    public int $previewInvalid = 0;

    public bool $processed = false;
    public array $results = [];
    public int $totalBerhasil = 0;
    public int $totalGagal = 0;

    protected function rules(): array
    {
        return [
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:5120'],
        ];
    }

    protected function messages(): array
    {
        return [
            'file.required' => 'Pilih file Excel terlebih dahulu.',
            'file.mimes'    => 'File harus berformat .xlsx atau .xls.',
            'file.max'      => 'Ukuran file maksimal 5 MB.',
        ];
    }

    public function downloadTemplate()
    {
        return Excel::download(new PesertaDidikTemplateExport, 'template-import-peserta-didik.xlsx');
    }

    public function updatedFile(): void
    {
        $this->showPreview     = false;
        $this->previewResults  = [];
        $this->previewValid    = 0;
        $this->previewInvalid  = 0;

        $this->validateOnly('file');

        $periode = app(PeriodeService::class)->getSelected();
        if (!$periode) {
            $this->addError('file', 'Tidak ada Periode Ajaran aktif. Set periode aktif terlebih dahulu.');
            $this->file = null;
            return;
        }

        try {
            $this->previewResults = app(ImportPesertaDidikService::class)->preview($this->file, $periode->id);
            $this->previewValid   = collect($this->previewResults)->where('status', 'valid')->count();
            $this->previewInvalid = collect($this->previewResults)->where('status', 'gagal')->count();
            $this->showPreview    = true;
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Pratinjau Import Peserta Didik', $th);
            $this->dispatch('notify', type: 'error', message: 'Gagal membaca file. Pastikan format Excel sesuai template.');
            $this->file = null;
        }
    }

    public function cancelPreview(): void
    {
        $this->file            = null;
        $this->showPreview     = false;
        $this->previewResults  = [];
        $this->previewValid    = 0;
        $this->previewInvalid  = 0;
        $this->resetErrorBag();
    }

    public function import(): void
    {
        $this->validate();

        $periode = app(PeriodeService::class)->getSelected();
        if (!$periode) {
            $this->addError('file', 'Tidak ada Periode Ajaran aktif. Set periode aktif terlebih dahulu.');
            return;
        }

        try {
            $this->results = app(ImportPesertaDidikService::class)
                ->import($this->file, $periode->id);

            $this->totalBerhasil = collect($this->results)->where('status', 'berhasil')->count();
            $this->totalGagal    = collect($this->results)->where('status', 'gagal')->count();
            $this->processed     = true;
            $this->file          = null;
            $this->showPreview   = false;

            if ($this->totalBerhasil > 0) {
                $this->dispatch('notify', type: 'success',
                    message: "$this->totalBerhasil peserta didik berhasil diimport.");
            }
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Import Excel Peserta Didik', $th);
            $this->dispatch('notify', type: 'error', message: 'Gagal memproses file. Pastikan format Excel sesuai template.');
        }
    }

    public function resetForm(): void
    {
        $this->file            = null;
        $this->showPreview     = false;
        $this->previewResults  = [];
        $this->previewValid    = 0;
        $this->previewInvalid  = 0;
        $this->results         = [];
        $this->processed       = false;
        $this->totalBerhasil   = 0;
        $this->totalGagal      = 0;
        $this->resetErrorBag();
    }

    public function render(): \Illuminate\View\View
    {
        $periode = app(PeriodeService::class)->getSelected();

        return view('livewire.admin.import-excel.import-peserta-didik', [
            'periode' => $periode,
        ]);
    }
}
