<div class="max-w-2xl mx-auto">

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <div class="flex items-center gap-3 mb-5">
        <a href="{{ $gmrId ? route('guru.materi', ['gmr' => $gmrId]) : route('guru.materi') }}"
           class="w-9 h-9 flex items-center justify-center rounded-lg border cursor-pointer transition-colors flex-shrink-0"
           style="border-color: #c5c5d7; background: white; color: #505f76"
           onmouseover="this.style.background='#f0f4f8'" onmouseout="this.style.background='white'">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        </a>
        <div>
            <h1 class="text-[18px] font-bold" style="color: #171c1f">{{ $editId ? 'Edit Materi' : 'Tambah Materi Baru' }}</h1>
            <p class="text-[13px] mt-0.5" style="color: #757686">{{ $editId ? 'Perbarui isi & lampiran materi.' : 'Bagikan materi pembelajaran ke peserta didik.' }}</p>
        </div>
    </div>

    {{-- ── Konteks Mapel & Rombel ──────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm p-4 sm:p-5 mb-6">
        @if ($gmrSelected)
            <div class="flex items-center gap-2.5 text-[13.5px]" style="color: #3c50e0">
                <span class="material-symbols-outlined text-[18px]">info</span>
                <span class="font-semibold">{{ $gmrSelected->mapel->nama }}</span>
                <span style="color: #505f76">— {{ $gmrSelected->rombel->nama }}</span>
            </div>
        @else
            <label class="block text-[13px] font-medium mb-1.5" style="color: #171c1f">Mata Pelajaran & Rombel <span style="color: #ba1a1a">*</span></label>
            <select wire:model="gmrId"
                    class="w-full border rounded-lg px-3 py-2.5 text-[14px] bg-white outline-none focus:border-[#3c50e0] focus:ring-1 focus:ring-[#3c50e0]"
                    style="border-color: {{ $errors->has('gmrId') ? '#ba1a1a' : '#c5c5d7' }}">
                <option value="">— Pilih Mapel & Rombel —</option>
                @foreach ($pemetaan as $p)
                    <option value="{{ $p->id }}">{{ $p->mapel->nama }} — {{ $p->rombel->nama }}</option>
                @endforeach
            </select>
            @error('gmrId') <p class="text-[12px] mt-1.5" style="color: #ba1a1a">{{ $message }}</p> @enderror
        @endif
    </div>

    {{-- ── Form ─────────────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm p-4 sm:p-6">
        <form wire:submit="save" class="space-y-5" x-data="{ uploading: 0 }">

            {{-- Judul --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-[13px] font-medium" style="color: #171c1f">
                    Judul Materi <span style="color: #ba1a1a">*</span>
                </label>
                <input wire:model="judul" type="text" placeholder="Masukkan judul materi"
                       class="w-full border rounded-lg px-3 py-2 text-[14px] focus:outline-none focus:ring-1 transition-shadow"
                       style="border-color: {{ $errors->has('judul') ? '#ba1a1a' : '#c5c5d7' }}">
                @error('judul') <p class="text-[12px]" style="color: #ba1a1a">{{ $message }}</p> @enderror
            </div>

            {{-- Trix Rich Text Editor --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-[13px] font-medium" style="color: #171c1f">Isi Materi <span class="text-[12px] font-normal" style="color: #505f76">(opsional)</span></label>
                <div wire:ignore
                     class="border rounded-lg overflow-hidden trix-wrapper"
                     style="border-color: #c5c5d7"
                     x-data
                     x-on:trix-file-accept="if (! $event.file.type.startsWith('image/')) { $event.preventDefault(); }"
                     x-on:trix-attachment-add="
                        if (! $event.attachment.file) return;
                        uploading++;
                        const form = new FormData();
                        form.append('file', $event.attachment.file);
                        fetch('{{ route('guru.materi.trix-upload') }}', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                            body: form,
                        })
                        .then(r => r.json())
                        .then(data => $event.attachment.setAttributes({ url: data.url, href: data.url }))
                        .catch(() => $event.attachment.remove())
                        .finally(() => uploading--);
                     ">
                    <input id="trix-content-{{ $editId ?? 'new' }}"
                           type="hidden"
                           value="{{ $isi }}">
                    <trix-editor
                        input="trix-content-{{ $editId ?? 'new' }}"
                        placeholder="Tulis penjelasan, ringkasan, atau instruksi materi di sini. Seret & lepas gambar untuk menyisipkannya."
                        class="trix-content min-h-[160px]"
                        x-on:trix-change="$wire.set('isi', $event.target.value, false)">
                    </trix-editor>
                </div>
                <p class="text-[11px]" style="color: #9da4b0">Bisa seret & lepas atau paste gambar langsung ke dalam teks. Untuk file PDF/dokumen, gunakan area upload di bawah.</p>
            </div>

            {{-- Lampiran existing (edit mode) --}}
            @if ($editId && $lampiranExisting)
                <div class="flex flex-col gap-2">
                    <label class="text-[13px] font-medium" style="color: #171c1f">Lampiran Saat Ini</label>
                    <div class="flex flex-col gap-2">
                        @foreach ($lampiranExisting as $l)
                            @php $ditandaHapus = in_array($l['id'], $lampiranHapus); @endphp
                            <div class="flex items-center gap-3 p-3 rounded-lg border transition-colors"
                                 style="{{ $ditandaHapus ? 'border-color:#ba1a1a; background:#ffdad64d' : 'border-color:#c5c5d7; background:#f6fafe' }}">
                                @if ($l['tipe'] === 'link_video')
                                    <span class="material-symbols-outlined text-[18px]" style="color: #3c50e0">play_circle</span>
                                    <span class="text-[13px] flex-1 truncate {{ $ditandaHapus ? 'line-through' : '' }}" style="color: {{ $ditandaHapus ? '#505f76' : '#171c1f' }}">{{ $l['url'] }}</span>
                                @elseif ($l['tipe'] === 'gambar')
                                    <span class="material-symbols-outlined text-[18px]" style="color: #505f76">image</span>
                                    <span class="text-[13px] flex-1 truncate {{ $ditandaHapus ? 'line-through' : '' }}" style="color: {{ $ditandaHapus ? '#505f76' : '#171c1f' }}">{{ $l['nama_asli'] }}</span>
                                @else
                                    <span class="material-symbols-outlined text-[18px]" style="color: #ba1a1a">description</span>
                                    <span class="text-[13px] flex-1 truncate {{ $ditandaHapus ? 'line-through' : '' }}" style="color: {{ $ditandaHapus ? '#505f76' : '#171c1f' }}">{{ $l['nama_asli'] }}</span>
                                @endif
                                <button type="button" wire:click="toggleHapusLampiran({{ $l['id'] }})"
                                        class="text-[12px] font-medium cursor-pointer flex-shrink-0"
                                        style="color: {{ $ditandaHapus ? '#3c50e0' : '#ba1a1a' }}">
                                    {{ $ditandaHapus ? 'Batalkan' : 'Hapus' }}
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Upload File & Gambar --}}
            <div class="flex flex-col gap-2">
                <label class="text-[13px] font-medium" style="color: #171c1f">
                    File & Gambar
                    <span class="text-[12px] font-normal" style="color: #505f76">(PDF, DOCX, PPT, XLS, gambar — maks {{ $maxMb }}MB/file)</span>
                </label>
                <div class="border-2 border-dashed rounded-xl p-6 flex flex-col items-center text-center transition-all cursor-pointer"
                     style="border-color: #c5c5d7; background: #f6fafe"
                     onmouseover="this.style.borderColor='#3c50e0'; this.style.background='#EEF2FF'"
                     onmouseout="this.style.borderColor='#c5c5d7'; this.style.background='#f6fafe'"
                     x-data @click="$refs.fileZone.click()">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center mb-3" style="background: #d0e1fb">
                        <span class="material-symbols-outlined text-[24px]" style="color: #3c50e0">cloud_upload</span>
                    </div>
                    <p class="text-[14px] font-medium" style="color: #171c1f">Seret & lepas file di sini</p>
                    <p class="text-[12px] mt-0.5 mb-3" style="color: #757686">atau klik untuk memilih file dari perangkat</p>
                    <span class="px-4 py-1.5 rounded-lg border text-[12px] font-medium transition-colors" style="border-color: #c5c5d7; color: #505f76">
                        Pilih File
                    </span>
                    <input x-ref="fileZone" wire:model="lampiranBaru" type="file" multiple
                           accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,image/*" class="hidden">
                </div>
                @error('lampiranBaru.*') <p class="text-[12px]" style="color: #ba1a1a">{{ $message }}</p> @enderror

                {{-- Preview antrian upload --}}
                @if ($lampiranBaru)
                    <div class="flex flex-col gap-1.5 mt-1">
                        @foreach ($lampiranBaru as $i => $f)
                            @php $isImg = str_starts_with($f->getMimeType() ?? '', 'image/'); @endphp
                            <div class="flex items-center gap-2 text-[13px] rounded-lg px-3 py-2" style="background: {{ $isImg ? '#d3e4fe66' : '#EEF2FF' }}; color: #171c1f">
                                <span class="material-symbols-outlined text-[16px]" style="color: {{ $isImg ? '#505f76' : '#3c50e0' }}">
                                    {{ $isImg ? 'image' : 'description' }}
                                </span>
                                <span class="flex-1 truncate">{{ $f->getClientOriginalName() }}</span>
                                <span class="text-[11px]" style="color: #505f76">{{ number_format($f->getSize() / 1024, 0) }} KB</span>
                                <button type="button" wire:click="removeLampiranBaru({{ $i }})" class="cursor-pointer ml-1" style="color: #ba1a1a">
                                    <span class="material-symbols-outlined text-[16px]">close</span>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Link Video --}}
            <div class="flex flex-col gap-2">
                <label class="text-[13px] font-medium" style="color: #171c1f">Link Video <span class="text-[12px] font-normal" style="color: #505f76">(YouTube, dll)</span></label>
                <div class="flex gap-2">
                    <input wire:model="inputUrl" type="text" placeholder="https://youtu.be/..."
                           class="flex-1 border rounded-lg px-3 py-2 text-[14px] focus:outline-none focus:ring-1 focus:ring-[#3c50e0]"
                           style="border-color: {{ $errors->has('inputUrl') ? '#ba1a1a' : '#c5c5d7' }}"
                           wire:keydown.enter.prevent="addLinkVideo">
                    <button type="button" wire:click="addLinkVideo"
                            class="px-4 py-2 rounded-lg border text-[14px] font-medium transition-colors cursor-pointer flex-shrink-0"
                            style="border-color: #c5c5d7; background: #f0f4f8; color: #171c1f">
                        + Tambah
                    </button>
                </div>
                @error('inputUrl') <p class="text-[12px]" style="color: #ba1a1a">{{ $message }}</p> @enderror

                @if ($linkVideo)
                    <div class="flex flex-col gap-1.5">
                        @foreach ($linkVideo as $i => $url)
                            <div class="flex items-center gap-2 text-[13px] rounded-lg px-3 py-2" style="background: #EEF2FF; color: #171c1f">
                                <span class="material-symbols-outlined text-[16px]" style="color: #3c50e0">play_circle</span>
                                <span class="flex-1 truncate" style="color: #1c33c8">{{ $url }}</span>
                                <button type="button" wire:click="removeLinkVideo({{ $i }})" class="cursor-pointer" style="color: #ba1a1a">
                                    <span class="material-symbols-outlined text-[16px]">close</span>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Footer Buttons --}}
            <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-4 border-t" style="border-color: #c5c5d7">
                <a href="{{ $gmrId ? route('guru.materi', ['gmr' => $gmrId]) : route('guru.materi') }}"
                   class="px-5 py-2.5 rounded-lg border text-[14px] font-medium transition-colors cursor-pointer text-center"
                   style="border-color: #c5c5d7; color: #505f76; background: white">
                    Batal
                </a>
                <button type="submit"
                        x-on:click="$wire.set('isi', document.getElementById('trix-content-{{ $editId ?? 'new' }}').value, false)"
                        x-bind:disabled="uploading > 0"
                        x-bind:class="uploading > 0 ? 'opacity-70 cursor-not-allowed' : 'cursor-pointer'"
                        class="px-5 py-2.5 rounded-lg text-white text-[14px] font-medium transition-colors flex items-center justify-center gap-2"
                        style="background: #3c50e0"
                        wire:loading.attr="disabled" wire:loading.class="opacity-70 cursor-not-allowed">
                    <span wire:loading wire:target="save" class="material-symbols-outlined text-[16px] animate-spin">progress_activity</span>
                    <span x-show="uploading > 0" class="material-symbols-outlined text-[16px] animate-spin">progress_activity</span>
                    <span x-text="uploading > 0 ? 'Mengunggah gambar…' : 'Simpan Materi'">Simpan Materi</span>
                </button>
            </div>
        </form>
    </div>

</div>

@push('scripts')
<link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
<script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>
<style>
    trix-editor { min-height: 160px; padding: 0.75rem 1rem; font-size: 14px; outline: none; }
    trix-toolbar .trix-button-group { border: 1px solid #c5c5d7; }
    .trix-wrapper trix-toolbar { background: #f0f4f8; border-bottom: 1px solid #c5c5d7; padding: 0.5rem; }
    .trix-wrapper trix-editor { border: none; }
    .trix-wrapper trix-editor .attachment { max-width: 100%; border-radius: 0.5rem; overflow: hidden; }
    .trix-wrapper trix-editor .attachment img { border-radius: 0.5rem; }
    .trix-wrapper trix-editor .attachment__caption { font-size: 12px; color: #757686; }
</style>
@endpush
