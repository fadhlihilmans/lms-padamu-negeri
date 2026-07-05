<?php

namespace App\Livewire\Admin\Pengaturan;

use App\Models\Setting;
use App\Services\ErrorLogService;
use App\Services\ImageService;
use App\Services\SettingService;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app', ['pageTitle' => 'Pengaturan'])]
#[Title('Pengaturan')]
class SettingManager extends Component
{
    use WithFileUploads;

    /** values[key] => nilai (typed) */
    public array $values = [];

    /** logoFiles[key] => TemporaryUploadedFile */
    public array $logoFiles = [];

    public const GROUP_LABEL = [
        'general'   => 'Umum',
        'kop_rapor' => 'Kop Rapor',
        'upload'    => 'Upload',
        'modul'     => 'Modul',
    ];

    public const GROUP_ORDER = ['general', 'kop_rapor', 'upload', 'modul'];

    public function mount(): void
    {
        foreach (Setting::all() as $s) {
            $this->values[$s->key] = match ($s->type) {
                'boolean' => filter_var($s->value, FILTER_VALIDATE_BOOLEAN),
                'integer' => (int) $s->value,
                default   => (string) ($s->value ?? ''),
            };
        }
    }

    private function isLogo(string $key): bool
    {
        return str_ends_with($key, '_path');
    }

    public function saveGroup(string $group): void
    {
        $settings = Setting::where('group', $group)->get();
        $svc      = app(SettingService::class);

        // Validasi ringan per tipe.
        $rules = [];
        $messages = [];
        foreach ($settings as $s) {
            if ($this->isLogo($s->key)) {
                $rules["logoFiles.{$s->key}"] = ['nullable', 'image', 'max:2048'];
                $messages["logoFiles.{$s->key}.image"] = 'File logo harus berupa gambar.';
                $messages["logoFiles.{$s->key}.max"]   = 'Ukuran logo maksimal 2 MB.';
            } elseif ($s->type === 'integer') {
                $rules["values.{$s->key}"] = ['required', 'integer', 'min:1', 'max:1024'];
            } elseif ($s->key === 'nama_pkbm') {
                $rules["values.{$s->key}"] = ['required', 'string', 'max:150'];
            } elseif ($s->key === 'email_pkbm') {
                $rules["values.{$s->key}"] = ['nullable', 'email', 'max:150'];
            }
        }
        if (! empty($rules)) {
            $this->validate($rules, $messages);
        }

        try {
            foreach ($settings as $s) {
                if ($this->isLogo($s->key)) {
                    if (! empty($this->logoFiles[$s->key])) {
                        // hapus lama
                        $old = $this->values[$s->key] ?? '';
                        if ($old) {
                            Storage::disk('public')->delete($old);
                        }
                        // Logo dikompres ke WebP (raster) via ImageService; SVG/non-raster tetap apa adanya.
                        $path = app(ImageService::class)->store($this->logoFiles[$s->key], 'logo', 'public');
                        $this->values[$s->key] = $path;
                        $svc->set($s->key, $path);
                    }
                    continue;
                }

                $val = $this->values[$s->key] ?? '';
                $typed = match ($s->type) {
                    'boolean' => (bool) $val,
                    'integer' => (int) $val,
                    default   => (string) $val,
                };
                $svc->set($s->key, $typed);
            }

            $this->logoFiles = [];
            $svc->invalidate();
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Pengaturan ' . (self::GROUP_LABEL[$group] ?? $group) . ' tersimpan.']);
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Simpan Pengaturan', $th);
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Terjadi kesalahan, silakan coba lagi.']);
        }
    }

    public function removeLogo(string $key): void
    {
        try {
            $old = $this->values[$key] ?? '';
            if ($old) {
                Storage::disk('public')->delete($old);
            }
            app(SettingService::class)->set($key, '');
            $this->values[$key] = '';
            unset($this->logoFiles[$key]);
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Logo dihapus.']);
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Hapus Logo', $th);
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Gagal menghapus logo.']);
        }
    }

    public function render(): View
    {
        $grouped = Setting::all()
            ->groupBy('group')
            ->sortBy(fn ($rows, $group) => array_search($group, self::GROUP_ORDER, true));

        return view('livewire.admin.pengaturan.setting-manager', [
            'grouped'    => $grouped,
            'groupLabel' => self::GROUP_LABEL,
        ]);
    }
}
