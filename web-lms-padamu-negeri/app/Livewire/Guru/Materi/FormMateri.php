<?php

namespace App\Livewire\Guru\Materi;

use App\Models\GuruMapelRombel;
use App\Models\Materi;
use App\Models\MateriLampiran;
use App\Services\ErrorLogService;
use App\Services\ImageService;
use App\Services\SettingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app', ['pageTitle' => 'Form Materi'])]
#[Title('Form Materi')]
class FormMateri extends Component
{
    use WithFileUploads;

    public ?int $editId = null;
    public ?int $gmrId  = null;

    public string $judul = '';
    public string $isi   = '';

    // Upload — satu zona untuk file & gambar
    public array $lampiranBaru = [];

    // Link video
    public array  $linkVideo = [];
    public string $inputUrl  = '';

    // Lampiran existing (edit mode)
    public array $lampiranExisting = [];
    public array $lampiranHapus    = [];

    public function mount(?int $id = null): void
    {
        $guru = Auth::user()->guru;

        if ($id) {
            $materi = Materi::with('lampiran')->find($id);
            if (! $materi || ! $guru || $materi->guruMapelRombel->guru_id !== $guru->id) {
                abort(403, 'Anda tidak memiliki akses ke materi ini.');
            }

            $this->editId = $id;
            $this->gmrId  = $materi->guru_mapel_rombel_id;
            $this->judul  = $materi->judul;
            $this->isi    = $materi->isi ?? '';

            $this->lampiranExisting = $materi->lampiran->map(fn($l) => [
                'id'        => $l->id,
                'tipe'      => $l->tipe,
                'file_path' => $l->file_path,
                'url'       => $l->url,
                'nama_asli' => $l->nama_asli,
            ])->toArray();
        } else {
            $gmrFromQuery = (int) request()->query('gmr');
            if ($guru && $gmrFromQuery) {
                $valid = GuruMapelRombel::where('id', $gmrFromQuery)->where('guru_id', $guru->id)->exists();
                $this->gmrId = $valid ? $gmrFromQuery : null;
            }
        }
    }

    protected function rules(): array
    {
        $maxMb = app(SettingService::class)->get('max_upload_materi_mb', 10);

        return [
            'judul'          => ['required', 'string', 'max:200'],
            'lampiranBaru.*' => ['nullable', 'file', 'max:' . ($maxMb * 1024)],
        ];
    }

    protected function messages(): array
    {
        $maxMb = app(SettingService::class)->get('max_upload_materi_mb', 10);

        return [
            'judul.required'     => 'Judul materi wajib diisi.',
            'judul.max'          => 'Judul maksimal 200 karakter.',
            'lampiranBaru.*.max' => "Ukuran file maksimal {$maxMb}MB.",
        ];
    }

    public function addLinkVideo(): void
    {
        $url = trim($this->inputUrl);
        if (! $url) {
            $this->addError('inputUrl', 'URL tidak boleh kosong.');
            return;
        }
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            $this->addError('inputUrl', 'Format URL tidak valid.');
            return;
        }
        $this->linkVideo[] = $url;
        $this->inputUrl    = '';
        $this->resetErrorBag('inputUrl');
    }

    public function removeLinkVideo(int $index): void
    {
        array_splice($this->linkVideo, $index, 1);
        $this->linkVideo = array_values($this->linkVideo);
    }

    public function removeLampiranBaru(int $index): void
    {
        array_splice($this->lampiranBaru, $index, 1);
        $this->lampiranBaru = array_values($this->lampiranBaru);
    }

    public function toggleHapusLampiran(int $id): void
    {
        if (in_array($id, $this->lampiranHapus)) {
            $this->lampiranHapus = array_values(array_diff($this->lampiranHapus, [$id]));
        } else {
            $this->lampiranHapus[] = $id;
        }
    }

    public function save(): void
    {
        $guru = Auth::user()->guru;
        if (! $guru || ! $this->gmrId) return;

        $gmr = GuruMapelRombel::where('id', $this->gmrId)
            ->where('guru_id', $guru->id)
            ->first();

        if (! $gmr) {
            $this->dispatch('notify', type: 'error', message: 'Pemetaan tidak valid.');
            return;
        }

        $this->validate();

        try {
            $isEdit = (bool) $this->editId;

            $materi = Materi::updateOrCreate(
                ['id' => $this->editId],
                [
                    'guru_mapel_rombel_id' => $gmr->id,
                    'judul'                => $this->judul,
                    'isi'                  => $this->isi ?: null,
                ]
            );

            // Hapus lampiran yang di-mark hapus
            if ($this->lampiranHapus) {
                $toDelete = MateriLampiran::whereIn('id', $this->lampiranHapus)
                    ->where('materi_id', $materi->id)
                    ->get();
                foreach ($toDelete as $l) {
                    if ($l->file_path) Storage::disk('public')->delete($l->file_path);
                    $l->delete();
                }
            }

            $urutan = MateriLampiran::where('materi_id', $materi->id)->max('urutan') ?? 0;

            // Upload file/gambar baru — deteksi tipe dari MIME
            foreach ($this->lampiranBaru as $file) {
                $mime = $file->getMimeType() ?? '';
                $tipe = str_starts_with($mime, 'image/') ? 'gambar' : 'file';
                // Gambar raster dikompres ke WebP; dokumen (pdf/docx/dll) disimpan apa adanya.
                $path = app(ImageService::class)->store($file, 'materi', 'public');
                MateriLampiran::create([
                    'materi_id' => $materi->id,
                    'tipe'      => $tipe,
                    'file_path' => $path,
                    'nama_asli' => $file->getClientOriginalName(),
                    'urutan'    => ++$urutan,
                ]);
            }

            // Simpan link video baru
            foreach ($this->linkVideo as $url) {
                MateriLampiran::create([
                    'materi_id' => $materi->id,
                    'tipe'      => 'link_video',
                    'url'       => $url,
                    'urutan'    => ++$urutan,
                ]);
            }

            $this->dispatch('notify', type: 'success', message: 'Materi berhasil ' . ($isEdit ? 'diperbarui' : 'ditambahkan') . '.');
            $this->redirectRoute('guru.materi', ['gmr' => $gmr->id], navigate: true);
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Simpan Materi', $th);
            $this->dispatch('notify', type: 'error', message: 'Maaf, terjadi kesalahan saat menyimpan.');
        }
    }

    public function render(): View
    {
        $guru = Auth::user()->guru;

        $pemetaan = $guru
            ? GuruMapelRombel::with(['mapel', 'rombel'])->where('guru_id', $guru->id)->orderBy('mapel_id')->get()
            : collect();

        $gmrSelected = $this->gmrId ? $pemetaan->firstWhere('id', $this->gmrId) : null;

        $maxMb = app(SettingService::class)->get('max_upload_materi_mb', 10);

        return view('livewire.guru.materi.form-materi', compact('pemetaan', 'gmrSelected', 'maxMb'));
    }
}
