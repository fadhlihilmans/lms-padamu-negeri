{{--
    Modal pratinjau file GLOBAL — dipasang sekali di layout, dipakai semua modul.

    Cara memicu dari Blade mana pun:
        $dispatch('open-file-preview', { url: '{{ Storage::url($path) }}', name: 'nama.pdf' })

    - Gambar → tampil langsung (<img>).
    - PDF   → <iframe> (ringan, tanpa library tambahan).
      Di mobile yang tidak support iframe PDF, otomatis fallback ke tombol Unduh.
    - Lainnya (docx/xlsx/pptx) → kartu "pratinjau tidak tersedia" + tombol Unduh.

    Mobile: layar penuh, tombol tutup (X) selalu terlihat di header yang sticky.
--}}
<div x-data="{
        open: false,
        url: '',
        name: '',
        kind: 'other',

        imageExts: ['jpg','jpeg','png','webp','gif','bmp','svg'],

        show(detail) {
            const payload = Array.isArray(detail) ? detail[0] : detail;
            if (!payload || !payload.url) return;

            this.url  = payload.url;
            this.name = payload.name || 'File';

            const ext = (payload.url.split('?')[0].split('.').pop() || '').toLowerCase();
            this.kind = ext === 'pdf' ? 'pdf' : (this.imageExts.includes(ext) ? 'image' : 'other');

            this.open = true;
            document.body.style.overflow = 'hidden';
        },

        close() {
            this.open = false;
            this.url  = '';
            document.body.style.overflow = '';
        },
     }"
     @open-file-preview.window="show($event.detail)">

    <template x-teleport="body">
        <div x-show="open" x-cloak
             @keydown.escape.window="close()"
             class="fixed inset-0 z-[300] flex items-center justify-center sm:p-4"
             style="background: rgba(0,0,0,0.75); display: none;">

            <div class="bg-white w-full h-full sm:h-[88vh] sm:max-w-4xl sm:rounded-xl overflow-hidden shadow-2xl flex flex-col"
                 @click.stop>

                {{-- Header: nama file + Unduh + tombol X --}}
                <div class="px-4 py-3 border-b border-[#c5c5d7] flex items-center gap-2 flex-shrink-0 bg-white">
                    <span class="material-symbols-outlined text-[20px] text-[#505f76] flex-shrink-0">description</span>
                    <span class="text-[14px] font-medium text-on-surface truncate flex-1 min-w-0" x-text="name"></span>

                    <a :href="url" target="_blank" rel="noopener"
                       class="flex-shrink-0 inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-[#c5c5d7] text-[12.5px] font-medium text-[#505f76] hover:bg-[#f0f4f8] transition-colors">
                        <span class="material-symbols-outlined text-[16px]">download</span>
                        <span class="hidden sm:inline">Unduh</span>
                    </a>

                    <button type="button" @click="close()" aria-label="Tutup"
                            class="flex-shrink-0 w-9 h-9 inline-flex items-center justify-center rounded-lg text-[#505f76] hover:bg-[#f0f4f8] hover:text-[#ba1a1a] transition-colors cursor-pointer">
                        <span class="material-symbols-outlined text-[22px]">close</span>
                    </button>
                </div>

                {{-- Body --}}
                <div class="flex-1 overflow-hidden bg-[#f0f4f8]">

                    {{-- Gambar --}}
                    <template x-if="kind === 'image'">
                        <div class="w-full h-full flex items-center justify-center p-4 overflow-auto">
                            <img :src="url" :alt="name" class="max-w-full max-h-full object-contain rounded-lg shadow">
                        </div>
                    </template>

                    {{-- PDF (iframe) --}}
                    <template x-if="kind === 'pdf'">
                        <div class="w-full h-full">
                            <iframe :src="url + '#zoom=page-width'" class="w-full h-full border-0" title="Pratinjau PDF"></iframe>
                        </div>
                    </template>

                    {{-- Tipe lain: tidak bisa dipratinjau di browser --}}
                    <template x-if="kind === 'other'">
                        <div class="flex flex-col items-center justify-center gap-3 py-16 px-6 text-center h-full">
                            <div class="w-16 h-16 rounded-2xl bg-white border border-[#c5c5d7] flex items-center justify-center">
                                <span class="material-symbols-outlined text-[32px] text-[#757686]">draft</span>
                            </div>
                            <p class="text-[15px] font-semibold text-on-surface">Pratinjau tidak tersedia</p>
                            <p class="text-[13px] text-[#757686] max-w-sm">
                                Berkas Word/Excel/PowerPoint tidak dapat ditampilkan langsung di browser.
                                Silakan unduh untuk membukanya.
                            </p>
                            <a :href="url" target="_blank" rel="noopener"
                               class="mt-1 inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-[#3c50e0] text-white text-[13.5px] font-semibold hover:bg-[#2a3db0] transition-colors">
                                <span class="material-symbols-outlined text-[17px]">download</span> Unduh Berkas
                            </a>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </template>
</div>
