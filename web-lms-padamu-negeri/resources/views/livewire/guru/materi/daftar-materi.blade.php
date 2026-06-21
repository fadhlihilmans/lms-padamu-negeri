<div>

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-[24px] font-bold tracking-tight text-on-surface">Materi</h2>
            <p class="text-[14px] text-[#505f76] mt-0.5">
                {{ $gmrSelected ? $gmrSelected->mapel->nama . ' — ' . $gmrSelected->rombel->nama : 'Pilih pemetaan untuk melihat materi' }}
            </p>
        </div>
        @if ($gmrId)
            <button wire:click="openCreateForm"
                    class="inline-flex items-center gap-2 bg-[#3c50e0] text-white text-[14px] font-medium px-4 py-2.5 rounded-lg hover:bg-[#1c33c8] transition-colors shadow-sm cursor-pointer flex-shrink-0">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Tambah Materi
            </button>
        @endif
    </div>

    {{-- ── Filter Bar ───────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm p-4 mb-6 flex flex-col sm:flex-row gap-3">
        <div class="flex-1">
            <select wire:model.live="gmrId"
                    class="w-full border border-[#c5c5d7] rounded-lg px-4 py-2.5 text-[14px] text-on-surface focus:outline-none focus:ring-1 focus:ring-[#3c50e0] cursor-pointer">
                <option value="">— Pilih Mapel & Rombel —</option>
                @foreach ($pemetaan as $p)
                    <option value="{{ $p->id }}">{{ $p->mapel->nama }} — {{ $p->rombel->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1 relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#505f76] text-[18px] pointer-events-none">search</span>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari judul materi..."
                   class="w-full pl-10 pr-4 py-2.5 border border-[#c5c5d7] rounded-lg text-[14px] focus:outline-none focus:ring-1 focus:ring-[#3c50e0]">
        </div>
        <div class="flex-shrink-0">
            <select wire:model.live="perPage"
                    class="border border-[#c5c5d7] rounded-lg px-4 py-2.5 text-[14px] focus:outline-none focus:ring-1 focus:ring-[#3c50e0] cursor-pointer">
                <option value="10">10 / halaman</option>
                <option value="25">25 / halaman</option>
                <option value="50">50 / halaman</option>
            </select>
        </div>
    </div>

    {{-- ── Modal Form ───────────────────────────────────────────────────────── --}}
    @if ($showForm)
        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/50 backdrop-blur-sm p-0 sm:p-4">
            
            {{-- Modal Container (Posisinya relative agar tombol close bisa absolute di dalamnya) --}}
            <div class="relative bg-white rounded-t-2xl sm:rounded-xl shadow-xl shadow-black/10 w-full max-w-4xl sm:max-w-3xl border border-[#c5c5d7]">

                {{-- Tombol Close (Absolute di pojok kanan atas, tidak ikut ter-scroll) --}}
                <button wire:click="closeForm" type="button"
                        class="absolute top-4 right-4 text-[#505f76] hover:text-[#ba1a1a] transition-colors p-1 cursor-pointer z-10 bg-white rounded-full">
                    <span class="material-symbols-outlined">close</span>
                </button>

                {{-- Area Scrollable --}}
                <div class="p-6 sm:p-8 max-h-[90vh] overflow-y-auto">
                    
                    {{-- Header --}}
                    <div class="mb-6 border-b border-[#c5c5d7] pb-4 pr-8">
                        <h3 class="text-[20px] font-semibold text-on-surface">{{ $editId ? 'Edit Materi' : 'Tambah Materi Baru' }}</h3>
                    </div>

                    {{-- Form --}}
                    <form wire:submit="save" class="space-y-6">

                        {{-- Judul --}}
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[14px] font-medium text-on-surface">
                                Judul Materi <span class="text-[#ba1a1a]">*</span>
                            </label>
                            <input wire:model="judul" type="text" placeholder="Masukkan judul materi"
                                   class="w-full border rounded-lg px-4 py-2.5 text-[14px] focus:outline-none focus:ring-1 transition-shadow
                                          {{ $errors->has('judul') ? 'border-[#ba1a1a] focus:ring-[#ba1a1a]' : 'border-[#c5c5d7] focus:ring-[#3c50e0]' }}">
                            @error('judul') <p class="text-[12px] text-[#ba1a1a]">{{ $message }}</p> @enderror
                        </div>

                        {{-- Trix Rich Text Editor --}}
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[14px] font-medium text-on-surface">Isi Materi <span class="text-[12px] font-normal text-[#505f76]">(opsional)</span></label>
                            <div wire:ignore class="border border-[#c5c5d7] rounded-lg overflow-hidden trix-wrapper">
                                <input id="trix-content-{{ $editId ?? 'new' }}"
                                       type="hidden"
                                       value="{{ $isi }}">
                                <trix-editor
                                    input="trix-content-{{ $editId ?? 'new' }}"
                                    placeholder="Tulis penjelasan, ringkasan, atau instruksi materi di sini..."
                                    class="trix-content min-h-[160px]"
                                    x-on:trix-change="$wire.set('isi', $event.target.value)"
                                    x-on:trix-file-accept.prevent>
                                </trix-editor>
                            </div>
                        </div>

                        {{-- Lampiran existing (edit mode) --}}
                        @if ($editId && $lampiranExisting)
                            <div class="flex flex-col gap-2">
                                <label class="text-[14px] font-medium text-on-surface">Lampiran Saat Ini</label>
                                <div class="flex flex-col gap-2">
                                    @foreach ($lampiranExisting as $l)
                                        @php $ditandaHapus = in_array($l['id'], $lampiranHapus); @endphp
                                        <div class="flex items-center gap-3 p-3 rounded-lg border transition-colors
                                                    {{ $ditandaHapus ? 'border-[#ba1a1a] bg-[#ffdad6]/30' : 'border-[#c5c5d7] bg-[#f6fafe]' }}">
                                            @if ($l['tipe'] === 'link_video')
                                                <span class="material-symbols-outlined text-[#3c50e0] text-[18px]">play_circle</span>
                                                <span class="text-[13px] flex-1 truncate {{ $ditandaHapus ? 'line-through text-[#505f76]' : 'text-on-surface' }}">{{ $l['url'] }}</span>
                                            @elseif ($l['tipe'] === 'gambar')
                                                <span class="material-symbols-outlined text-[#505f76] text-[18px]">image</span>
                                                <span class="text-[13px] flex-1 truncate {{ $ditandaHapus ? 'line-through text-[#505f76]' : 'text-on-surface' }}">{{ $l['nama_asli'] }}</span>
                                            @else
                                                <span class="material-symbols-outlined text-[#ba1a1a] text-[18px]">description</span>
                                                <span class="text-[13px] flex-1 truncate {{ $ditandaHapus ? 'line-through text-[#505f76]' : 'text-on-surface' }}">{{ $l['nama_asli'] }}</span>
                                            @endif
                                            <button type="button" wire:click="toggleHapusLampiran({{ $l['id'] }})"
                                                    class="text-[12px] font-medium cursor-pointer flex-shrink-0
                                                           {{ $ditandaHapus ? 'text-[#3c50e0] hover:text-[#1c33c8]' : 'text-[#ba1a1a] hover:text-[#93000a]' }}">
                                                {{ $ditandaHapus ? 'Batalkan' : 'Hapus' }}
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Upload File & Gambar --}}
                        <div class="flex flex-col gap-2">
                            <label class="text-[14px] font-medium text-on-surface">
                                File & Gambar
                                <span class="text-[12px] font-normal text-[#505f76]">(PDF, DOCX, PPT, XLS, gambar — maks {{ $maxMb }}MB/file)</span>
                            </label>
                            <div class="relative border-2 border-dashed border-[#c5c5d7] rounded-xl bg-[#f6fafe] p-6 flex flex-col items-center text-center hover:border-[#3c50e0]/50 hover:bg-[#EEF2FF]/40 transition-colors cursor-pointer"
                                 x-data @click="$refs.fileZone.click()">
                                <span class="material-symbols-outlined text-[32px] text-[#505f76] mb-1">cloud_upload</span>
                                <p class="text-[13px] font-medium text-on-surface">Klik atau seret file ke sini</p>
                                <input x-ref="fileZone" wire:model="lampiranBaru" type="file" multiple
                                       accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,image/*" class="hidden">
                            </div>
                            @error('lampiranBaru.*') <p class="text-[12px] text-[#ba1a1a]">{{ $message }}</p> @enderror

                            {{-- Preview antrian upload --}}
                            @if ($lampiranBaru)
                                <div class="flex flex-col gap-1.5 mt-1">
                                    @foreach ($lampiranBaru as $i => $f)
                                        @php $isImg = str_starts_with($f->getMimeType() ?? '', 'image/'); @endphp
                                        <div class="flex items-center gap-2 text-[13px] text-on-surface rounded-lg px-3 py-2
                                                    {{ $isImg ? 'bg-[#d3e4fe]/40' : 'bg-[#EEF2FF]' }}">
                                            <span class="material-symbols-outlined text-[16px] {{ $isImg ? 'text-[#505f76]' : 'text-[#3c50e0]' }}">
                                                {{ $isImg ? 'image' : 'description' }}
                                            </span>
                                            <span class="flex-1 truncate">{{ $f->getClientOriginalName() }}</span>
                                            <span class="text-[11px] text-[#505f76]">{{ number_format($f->getSize() / 1024, 0) }} KB</span>
                                            <button type="button" wire:click="removeLampiranBaru({{ $i }})" class="text-[#ba1a1a] hover:text-[#93000a] cursor-pointer ml-1">
                                                <span class="material-symbols-outlined text-[16px]">close</span>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- Link Video --}}
                        <div class="flex flex-col gap-2">
                            <label class="text-[14px] font-medium text-on-surface">Link Video <span class="text-[12px] font-normal text-[#505f76]">(YouTube, dll)</span></label>
                            <div class="flex gap-2">
                                <input wire:model="inputUrl" type="text" placeholder="https://youtu.be/..."
                                       class="flex-1 border border-[#c5c5d7] rounded-lg px-4 py-2.5 text-[14px] focus:outline-none focus:ring-1 focus:ring-[#3c50e0]
                                              {{ $errors->has('inputUrl') ? 'border-[#ba1a1a]' : '' }}"
                                       wire:keydown.enter.prevent="addLinkVideo">
                                <button type="button" wire:click="addLinkVideo"
                                        class="px-4 py-2.5 rounded-lg border border-[#c5c5d7] bg-[#f0f4f8] text-[14px] font-medium text-on-surface hover:bg-[#e4e9ed] transition-colors cursor-pointer flex-shrink-0">
                                    + Tambah
                                </button>
                            </div>
                            @error('inputUrl') <p class="text-[12px] text-[#ba1a1a]">{{ $message }}</p> @enderror

                            @if ($linkVideo)
                                <div class="flex flex-col gap-1.5">
                                    @foreach ($linkVideo as $i => $url)
                                        <div class="flex items-center gap-2 text-[13px] text-on-surface bg-[#EEF2FF] rounded-lg px-3 py-2">
                                            <span class="material-symbols-outlined text-[16px] text-[#3c50e0]">play_circle</span>
                                            <span class="flex-1 truncate text-[#1c33c8]">{{ $url }}</span>
                                            <button type="button" wire:click="removeLinkVideo({{ $i }})" class="text-[#ba1a1a] hover:text-[#93000a] cursor-pointer">
                                                <span class="material-symbols-outlined text-[16px]">close</span>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- Footer Buttons --}}
                        <div class="flex justify-end gap-3 pt-6 mt-2 border-t border-[#c5c5d7]">
                            <button type="button" wire:click="closeForm"
                                    class="px-5 py-2.5 rounded-lg border border-[#c5c5d7] text-[#505f76] text-[14px] font-medium hover:bg-[#f0f4f8] transition-colors cursor-pointer">
                                Batal
                            </button>
                            <button type="submit"
                                    class="px-5 py-2.5 rounded-lg bg-[#3c50e0] text-white text-[14px] font-medium hover:bg-[#1c33c8] transition-colors flex items-center gap-2 cursor-pointer"
                                    wire:loading.attr="disabled" wire:loading.class="opacity-70 cursor-not-allowed">
                                <span wire:loading wire:target="save" class="material-symbols-outlined text-[16px] animate-spin">progress_activity</span>
                                Simpan Materi
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    @endif

    {{-- ── Modal Konfirmasi Hapus ───────────────────────────────────────────── --}}
    @if ($confirmDeleteId)
        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/50 backdrop-blur-sm p-0 sm:p-4">
            <div class="bg-white rounded-t-2xl sm:rounded-xl shadow-xl shadow-black/10 w-full max-w-4xl sm:max-w-sm border border-[#c5c5d7] p-6 flex flex-col gap-4">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full bg-[#ffdad6] flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-[#ba1a1a]">delete</span>
                    </div>
                    <div>
                        <h4 class="text-[16px] font-semibold text-on-surface">Hapus materi ini?</h4>
                        <p class="text-[14px] text-[#505f76] mt-1">Materi dan semua lampirannya akan dihapus permanen.</p>
                    </div>
                </div>
                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                    <button wire:click="$set('confirmDeleteId', null)"
                            class="w-full sm:w-auto px-4 py-2.5 rounded-lg border border-[#c5c5d7] text-[14px] text-[#505f76] hover:bg-[#f0f4f8] transition-colors cursor-pointer text-center">
                        Batal
                    </button>
                    <button wire:click="delete"
                            class="w-full sm:w-auto px-4 py-2.5 rounded-lg bg-[#ba1a1a] text-white text-[14px] font-medium hover:bg-[#93000a] transition-colors cursor-pointer text-center">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ── Content ──────────────────────────────────────────────────────────── --}}
    @if ($pemetaan->isEmpty())
        <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm px-6 py-16 text-center">
            <span class="material-symbols-outlined text-[48px] text-[#c5c5d7] mb-3 block">hub</span>
            <p class="text-[15px] font-medium text-on-surface mb-1">Belum ada pemetaan di periode ini</p>
            <p class="text-[13px] text-[#505f76]">Hubungi Admin untuk menambahkan pemetaan Anda.</p>
        </div>

    @elseif (! $gmrId)
        <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm px-6 py-16 text-center">
            <span class="material-symbols-outlined text-[48px] text-[#c5c5d7] mb-3 block">menu_book</span>
            <p class="text-[14px] text-[#505f76]">Pilih mata pelajaran dan rombel di atas.</p>
        </div>

    @elseif ($materi->isEmpty())
        <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm px-6 py-16 text-center">
            <span class="material-symbols-outlined text-[48px] text-[#c5c5d7] mb-3 block">menu_book</span>
            <p class="text-[15px] font-medium text-on-surface mb-1">{{ $search ? 'Tidak ada hasil' : 'Belum ada materi' }}</p>
            @if (! $search)
                <button wire:click="openCreateForm"
                        class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[#3c50e0] text-white text-[14px] font-medium hover:bg-[#1c33c8] transition-colors cursor-pointer">
                    <span class="material-symbols-outlined text-[16px]">add</span>
                    Tambah Materi Pertama
                </button>
            @endif
        </div>

    @else
        <div class="flex flex-col gap-3">
            @foreach ($materi as $m)
                <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm overflow-hidden">
                    <div class="flex items-start gap-4 p-5">
                        <div class="w-10 h-10 rounded-lg bg-[#EEF2FF] text-[#3c50e0] flex-shrink-0 flex items-center justify-center mt-0.5">
                            <span class="material-symbols-outlined text-[20px]">article</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-[15px] font-semibold text-on-surface">{{ $m->judul }}</h3>
                            <div class="flex items-center gap-3 mt-1 text-[12px] text-[#505f76] flex-wrap">
                                <span class="flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[13px]">calendar_month</span>
                                    {{ $m->created_at->translatedFormat('d M Y') }}
                                </span>
                                @if ($m->isi)
                                    <span class="px-2 py-0.5 rounded-full bg-[#f0f4f8] text-[11px] font-medium">Teks</span>
                                @endif
                                @foreach ($m->lampiran->groupBy('tipe') as $tipe => $items)
                                    <span class="px-2 py-0.5 rounded-full bg-[#EEF2FF] text-[#3c50e0] text-[11px] font-medium">
                                        {{ $items->count() }}
                                        {{ match($tipe) { 'file' => 'File', 'gambar' => 'Gambar', 'link_video' => 'Video', default => $tipe } }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        <div class="flex items-center gap-1 flex-shrink-0">
                            <a href="{{ route('guru.materi.show', $m->id) }}"
                               class="p-1.5 text-[#505f76] hover:text-[#3c50e0] hover:bg-[#EEF2FF] rounded-lg transition-colors cursor-pointer"
                               title="Lihat detail">
                                <span class="material-symbols-outlined text-[18px]">visibility</span>
                            </a>
                            <button wire:click="openEditForm({{ $m->id }})"
                                    class="p-1.5 text-[#505f76] hover:text-[#1c33c8] hover:bg-[#EEF2FF] rounded-lg transition-colors cursor-pointer">
                                <span class="material-symbols-outlined text-[18px]">edit</span>
                            </button>
                            <button wire:click="confirmDelete({{ $m->id }})"
                                    class="p-1.5 text-[#505f76] hover:text-[#ba1a1a] hover:bg-[#ffdad6] rounded-lg transition-colors cursor-pointer">
                                <span class="material-symbols-outlined text-[18px]">delete</span>
                            </button>
                        </div>
                    </div>

                    {{-- Lampiran chips --}}
                    @if ($m->lampiran->isNotEmpty())
                        <div class="px-5 pb-4 flex flex-wrap gap-2 border-t border-[#f0f4f8] pt-3">
                            @foreach ($m->lampiran as $l)
                                @if ($l->tipe === 'link_video')
                                    <a href="{{ $l->url }}" target="_blank" rel="noopener noreferrer"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-[#EEF2FF] text-[#3c50e0] text-[12px] font-medium hover:bg-[#d9dbff] transition-colors cursor-pointer">
                                        <span class="material-symbols-outlined text-[14px]">play_circle</span>
                                        {{ $l->youtubeId() ? 'YouTube' : 'Video' }}
                                    </a>
                                @elseif ($l->tipe === 'gambar')
                                    <a href="{{ Storage::url($l->file_path) }}" target="_blank"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-[#d3e4fe]/60 text-[#505f76] text-[12px] font-medium hover:bg-[#c0d4f0] transition-colors cursor-pointer">
                                        <span class="material-symbols-outlined text-[14px]">image</span>
                                        {{ Str::limit($l->nama_asli ?? 'Gambar', 20) }}
                                    </a>
                                @else
                                    <a href="{{ Storage::url($l->file_path) }}" target="_blank"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-[#ffdad6]/60 text-[#ba1a1a] text-[12px] font-medium hover:bg-[#ffc4c0] transition-colors cursor-pointer">
                                        <span class="material-symbols-outlined text-[14px]">description</span>
                                        {{ Str::limit($l->nama_asli ?? 'File', 20) }}
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        @if ($materi->hasPages())
            <div class="mt-6">{{ $materi->links() }}</div>
        @endif
    @endif

</div>

@push('scripts')
<link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
<script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>
<style>
    trix-editor { min-height: 160px; padding: 0.75rem 1rem; font-size: 14px; outline: none; }
    trix-toolbar .trix-button-group { border: 1px solid #c5c5d7; }
    .trix-wrapper trix-toolbar { background: #f0f4f8; border-bottom: 1px solid #c5c5d7; padding: 0.5rem; }
    .trix-wrapper trix-editor { border: none; }
</style>
@endpush
