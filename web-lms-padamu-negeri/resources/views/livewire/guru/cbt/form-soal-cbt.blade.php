<div class="w-full space-y-4">

    @php
        $jenisMap = [
            'pilihan_ganda' => ['PG', 'bg-green-50 text-green-700'],
            'uraian'        => ['Uraian', 'bg-purple-50 text-purple-700'],
            'campuran'      => ['Campuran', 'bg-[#d0e1fb] text-[#3c50e0]'],
        ];
        [$jenisLabel, $jenisCls] = $jenisMap[$cbt->jenis_cbt] ?? ['-', 'bg-[#f0f4f8] text-[#505f76]'];
    @endphp

    {{-- ── Header + tombol kembali ─────────────────────────────────────────── --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('guru.cbt') }}" wire:navigate
           class="w-9 h-9 flex items-center justify-center rounded-lg border border-[#c5c5d7] bg-white text-[#505f76] hover:bg-[#f0f4f8] transition-colors flex-shrink-0 cursor-pointer">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        </a>
        <div>
            <h1 class="text-[18px] font-bold text-on-surface">Kelola Soal</h1>
            <p class="text-[13px] text-[#757686] mt-0.5">Susun, urutkan &amp; atur soal ujian.</p>
        </div>
    </div>

    {{-- ── CBT info bar ────────────────────────────────────────────────────── --}}
    <div class="bg-white border border-[#c5c5d7] rounded-xl p-4 flex flex-wrap items-center gap-4 justify-between">
        <div>
            <h1 class="text-lg font-bold text-on-surface">{{ $cbt->nama_ujian }}</h1>
            <div class="flex flex-wrap gap-3 mt-1 text-xs text-[#757686]">
                <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">event</span> {{ $cbt->tanggal_mulai->translatedFormat('d M Y, H:i') }}</span>
                <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">timer</span> {{ $cbt->durasi_menit }} menit</span>
                <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">grade</span> KKM {{ $cbt->kkm }}</span>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
            <span class="text-xs px-2 py-1 rounded-full font-medium {{ $jenisCls }}">{{ $jenisLabel }}</span>
            <span class="text-sm font-bold text-[#3c50e0]">{{ $totalSoal }} soal</span>
            <button wire:click="openCreateForm"
                    class="w-full sm:w-auto flex items-center justify-center gap-1.5 px-3 py-2 bg-[#3c50e0] text-white text-sm font-medium rounded-lg hover:bg-[#2a3db0] shadow-sm whitespace-nowrap cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">add</span> Tambah Soal
            </button>
        </div>
    </div>

    {{-- ── Empty penuh: belum ada soal sama sekali ────────────────────────── --}}
    @if ($totalSoal === 0)
        <div class="bg-white border border-[#c5c5d7] rounded-xl px-6 py-16 text-center">
            <span class="material-symbols-outlined text-[48px] text-[#c5c5d7] mb-3 block">help</span>
            <p class="text-[15px] font-medium text-on-surface mb-1">Belum ada soal</p>
            <p class="text-[13px] text-[#757686] mb-4">Klik "Tambah Soal" untuk mulai menyusun ujian.</p>
            <button wire:click="openCreateForm"
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#3c50e0] text-white text-sm font-medium rounded-lg hover:bg-[#2a3db0] cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">add</span> Tambah Soal
            </button>
        </div>
    @else
        {{-- ── Filter tipe soal ───────────────────────────────────────────── --}}
        <div class="bg-white border border-[#c5c5d7] rounded-xl px-4 py-3 flex flex-col sm:flex-row sm:items-center gap-2 justify-between">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px] text-[#757686]">filter_list</span>
                <select wire:model.live="filterTipe"
                        class="px-3 py-2 border border-[#c5c5d7] rounded-lg text-sm bg-white text-[#505f76] cursor-pointer focus:outline-none focus:border-[#3c50e0]">
                    <option value="">Semua Tipe</option>
                    <option value="pilihan_ganda">Pilihan Ganda</option>
                    <option value="uraian">Uraian</option>
                </select>
            </div>
            @if (! $canReorder)
                <p class="text-xs text-[#757686] flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">info</span>
                    {{ $filterTipe ? 'Geser urutan tersedia saat filter "Semua Tipe".' : 'Urutan terkunci karena CBT sudah dikerjakan.' }}
                </p>
            @endif
        </div>

        {{-- ── Daftar soal ────────────────────────────────────────────────── --}}
        <div class="space-y-3">
            @forelse ($soalList as $i => $soal)
                <div class="bg-white border border-[#c5c5d7] rounded-xl overflow-hidden">
                    <div class="flex items-center justify-between px-4 py-3 bg-white border-b border-[#c5c5d7]">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-[#3c50e0] text-white text-xs font-bold flex items-center justify-center">{{ $i + 1 }}</span>
                            @if ($soal->tipe_soal === 'pilihan_ganda')
                                <span class="text-xs px-2 py-0.5 rounded-full bg-green-50 text-green-700 font-medium">Pilihan Ganda</span>
                            @else
                                <span class="text-xs px-2 py-0.5 rounded-full bg-purple-50 text-purple-700 font-medium">Uraian</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-1">
                            @if ($canReorder)
                                <button wire:click="moveUp({{ $soal->id }})" @disabled($loop->first)
                                        class="w-8 h-8 rounded flex items-center justify-center text-[#505f76] hover:text-[#3c50e0] hover:bg-[#EEF2FF] cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed" title="Naikkan">
                                    <span class="material-symbols-outlined text-[18px]">arrow_upward</span>
                                </button>
                                <button wire:click="moveDown({{ $soal->id }})" @disabled($loop->last)
                                        class="w-8 h-8 rounded flex items-center justify-center text-[#505f76] hover:text-[#3c50e0] hover:bg-[#EEF2FF] cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed" title="Turunkan">
                                    <span class="material-symbols-outlined text-[18px]">arrow_downward</span>
                                </button>
                                <span class="w-px h-5 bg-[#c5c5d7] mx-1"></span>
                            @endif
                            <button wire:click="openEditForm({{ $soal->id }})"
                                    class="w-8 h-8 rounded flex items-center justify-center text-[#505f76] hover:text-[#3c50e0] hover:bg-[#EEF2FF] cursor-pointer" title="Edit">
                                <span class="material-symbols-outlined text-[18px]">edit</span>
                            </button>
                            <button wire:click="confirmDelete({{ $soal->id }})"
                                    class="w-8 h-8 rounded flex items-center justify-center text-[#505f76] hover:text-red-600 hover:bg-red-50 cursor-pointer" title="Hapus">
                                <span class="material-symbols-outlined text-[18px]">delete</span>
                            </button>
                        </div>
                    </div>
                    <div class="px-4 py-4">
                        <p class="text-sm text-on-surface leading-relaxed mb-3 whitespace-pre-line">{{ $soal->pertanyaan }}</p>

                        @if ($soal->tipe_soal === 'pilihan_ganda')
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach (($soal->pilihan_jawaban ?? []) as $huruf => $teks)
                                    @if ($huruf === $soal->kunci_jawaban)
                                        <div class="flex items-center gap-2 text-xs px-3 py-2 rounded-lg bg-green-50 border border-green-200 text-green-700 font-medium">
                                            <span class="material-symbols-outlined text-[14px] filled">check_circle</span>
                                            {{ $huruf }}. {{ $teks }}
                                            <span class="ml-auto text-green-600 text-[10px]">Kunci</span>
                                        </div>
                                    @else
                                        <div class="flex items-center gap-2 text-xs px-3 py-2 rounded-lg border border-[#c5c5d7] text-[#505f76]">{{ $huruf }}. {{ $teks }}</div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white border border-[#c5c5d7] rounded-xl px-6 py-12 text-center">
                    <span class="material-symbols-outlined text-[40px] text-[#c5c5d7] mb-2 block">filter_list_off</span>
                    <p class="text-[14px] text-[#757686]">Tidak ada soal bertipe {{ $filterTipe === 'uraian' ? 'Uraian' : 'Pilihan Ganda' }}.</p>
                </div>
            @endforelse
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- Modal Tambah / Edit Soal (9.1)                                         --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    @if ($showForm)
        <div class="fixed inset-0 z-50 bg-black/40 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4">
            <div class="bg-white w-full max-w-4xl sm:max-w-lg rounded-t-2xl sm:rounded-xl overflow-hidden shadow-xl flex flex-col max-h-[92vh]">
                <div class="px-5 py-4 border-b border-[#c5c5d7] flex items-center justify-between flex-shrink-0">
                    <h2 class="text-base font-semibold text-on-surface">{{ $editSoalId ? 'Edit Soal' : 'Tambah Soal' }}</h2>
                    <button wire:click="closeForm" class="text-[#757686] hover:text-[#171c1f] cursor-pointer">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form wire:submit="saveSoal" class="flex flex-col flex-1 min-h-0">
                    <div class="p-5 overflow-y-auto min-h-0 space-y-4 flex-1">

                        {{-- Tipe soal --}}
                        <div>
                            <label class="text-xs font-medium text-[#505f76] block mb-2">Tipe Soal <span class="text-red-500">*</span></label>
                            <div class="flex gap-2">
                                <button type="button" wire:click="setTipe('pilihan_ganda')"
                                        @class([
                                            'flex items-center gap-1.5 px-3.5 py-1.5 rounded-md text-[13px] font-medium cursor-pointer transition-all',
                                            'border-2 border-[#3c50e0] bg-[#EEF2FF] text-[#3c50e0]' => $tipeSoal === 'pilihan_ganda',
                                            'border border-[#c5c5d7] bg-white text-[#505f76]' => $tipeSoal !== 'pilihan_ganda',
                                        ])>
                                    <span class="material-symbols-outlined text-[18px]">radio_button_checked</span> Pilihan Ganda
                                </button>
                                <button type="button" wire:click="setTipe('uraian')"
                                        @class([
                                            'flex items-center gap-1.5 px-3.5 py-1.5 rounded-md text-[13px] font-medium cursor-pointer transition-all',
                                            'border-2 border-[#3c50e0] bg-[#EEF2FF] text-[#3c50e0]' => $tipeSoal === 'uraian',
                                            'border border-[#c5c5d7] bg-white text-[#505f76]' => $tipeSoal !== 'uraian',
                                        ])>
                                    <span class="material-symbols-outlined text-[18px]">subject</span> Uraian
                                </button>
                            </div>
                        </div>

                        {{-- Pertanyaan --}}
                        <div>
                            <label class="text-xs font-medium text-[#505f76] block mb-1">Teks Soal <span class="text-red-500">*</span></label>
                            <textarea wire:model="pertanyaan" rows="4" placeholder="Tulis pertanyaan di sini…"
                                      class="w-full px-3 py-2 border rounded-lg text-sm resize-y focus:outline-none focus:border-[#3c50e0] focus:ring-1 focus:ring-[#3c50e0] @error('pertanyaan') border-[#ba1a1a] @else border-[#c5c5d7] @enderror"></textarea>
                            @error('pertanyaan') <p class="text-xs text-[#ba1a1a] mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Opsi PG --}}
                        @if ($tipeSoal === 'pilihan_ganda')
                            <div>
                                <label class="text-xs font-medium text-[#505f76] block mb-2">
                                    Pilihan Jawaban <span class="text-red-500">*</span>
                                    <span class="font-normal">(tandai kunci jawaban)</span>
                                </label>
                                <div class="space-y-2">
                                    @foreach (['A', 'B', 'C', 'D'] as $huruf)
                                        <div class="flex items-center gap-2">
                                            <input type="radio" wire:model="kunciJawaban" value="{{ $huruf }}" name="kunci" class="accent-[#3c50e0] flex-shrink-0 cursor-pointer"/>
                                            <span class="text-xs font-semibold text-[#505f76] w-4">{{ $huruf }}.</span>
                                            <input type="text" wire:model="opsi{{ $huruf }}" placeholder="Pilihan {{ $huruf }}"
                                                   class="flex-1 px-3 py-2 border rounded-lg text-sm focus:outline-none focus:border-[#3c50e0] focus:ring-1 focus:ring-[#3c50e0] @error('opsi'.$huruf) border-[#ba1a1a] @else border-[#c5c5d7] @enderror"/>
                                        </div>
                                    @endforeach
                                </div>
                                @error('kunciJawaban') <p class="text-xs text-[#ba1a1a] mt-1">{{ $message }}</p> @enderror
                                @foreach (['opsiA', 'opsiB', 'opsiC', 'opsiD'] as $opsiField)
                                    @error($opsiField) <p class="text-xs text-[#ba1a1a] mt-1">{{ $message }}</p> @enderror
                                @endforeach
                                <p class="text-xs text-[#757686] mt-1">Klik lingkaran di kiri untuk memilih kunci jawaban.</p>
                            </div>
                        @else
                            <div class="bg-[#f6fafe] border border-dashed border-[#c5c5d7] rounded-lg px-3 py-3 text-xs text-[#757686] flex items-start gap-2">
                                <span class="material-symbols-outlined text-[16px] text-[#505f76]">info</span>
                                Soal uraian dinilai manual oleh guru pada halaman Koreksi setelah ujian selesai.
                            </div>
                        @endif

                    </div>

                    <div class="px-5 py-4 border-t border-[#c5c5d7] flex flex-col-reverse sm:flex-row sm:justify-end gap-2 flex-shrink-0 bg-white">
                        <button type="button" wire:click="closeForm"
                                class="w-full sm:w-auto px-4 py-2 text-sm rounded-lg border border-[#c5c5d7] text-[#505f76] hover:bg-[#f0f4f8] cursor-pointer text-center">
                            Batal
                        </button>
                        <button type="submit" wire:loading.attr="disabled"
                                class="w-full sm:w-auto px-4 py-2 text-sm rounded-lg bg-[#3c50e0] text-white hover:bg-[#2a3db0] font-medium cursor-pointer flex items-center justify-center gap-2">
                            <span wire:loading wire:target="saveSoal" class="material-symbols-outlined text-[16px] animate-spin">progress_activity</span>
                            Simpan Soal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ── Modal Konfirmasi Hapus ─────────────────────────────────────────── --}}
    @if ($confirmDeleteId)
        <div class="fixed inset-0 z-[60] flex items-end sm:items-center justify-center bg-black/40 backdrop-blur-sm p-0 sm:p-4">
            <div class="bg-white rounded-t-2xl sm:rounded-xl shadow-xl w-full max-w-4xl sm:max-w-sm p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-red-600 text-[24px]">delete_forever</span>
                    </div>
                    <div>
                        <h4 class="text-[16px] font-semibold text-on-surface">Hapus Soal?</h4>
                        <p class="text-[13px] text-[#757686] mt-0.5">Soal ini akan dihapus permanen dari ujian.</p>
                    </div>
                </div>
                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                    <button wire:click="cancelDelete"
                            class="w-full sm:w-auto px-5 py-2.5 text-[14px] font-medium text-[#505f76] border border-[#c5c5d7] rounded-xl hover:bg-[#f0f4f8] cursor-pointer text-center">
                        Batal
                    </button>
                    <button wire:click="deleteSoal"
                            class="w-full sm:w-auto px-5 py-2.5 text-[14px] font-semibold text-white bg-[#ba1a1a] rounded-xl hover:bg-[#93000a] cursor-pointer text-center">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
