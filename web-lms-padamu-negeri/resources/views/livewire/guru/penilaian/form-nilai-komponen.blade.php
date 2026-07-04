<div class="max-w-5xl mx-auto space-y-4">

    @php
        $gradeCls = [
            'A' => 'bg-green-100 text-green-700',
            'B' => 'bg-blue-100 text-blue-700',
            'C' => 'bg-amber-100 text-amber-700',
            'D' => 'bg-red-100 text-red-700',
        ];
    @endphp

    {{-- ── Heading ─────────────────────────────────────────────────────────── --}}
    <div>
        <h1 class="text-xl font-bold text-on-surface">Input Nilai Akhir</h1>
        <p class="text-sm text-[#505f76] mt-0.5 flex flex-wrap gap-1.5 items-center">
            <span class="material-symbols-outlined text-[15px]">calculate</span>
            @if ($gmr)
                {{ $gmr->mapel?->nama }} · {{ $gmr->rombel?->nama }}@if ($periode) · {{ ucfirst($periode->semester) }} {{ $periode->tahun_ajaran }}@endif
            @else
                Pilih mata pelajaran & rombel
            @endif
        </p>
    </div>

    @if ($gmrList->isEmpty())
        <div class="bg-white border border-[#c5c5d7] rounded-xl px-6 py-16 text-center">
            <span class="material-symbols-outlined text-[48px] text-[#c5c5d7] mb-3 block">grade</span>
            <p class="text-[15px] font-medium text-on-surface mb-1">Belum ada pemetaan mapel</p>
            <p class="text-[13px] text-[#757686]">Hubungi Admin untuk menambahkan pemetaan mengajar Anda.</p>
        </div>
    @else
        {{-- Selektor mapel-rombel --}}
        <div class="bg-white border border-[#c5c5d7] rounded-xl p-4 flex flex-col sm:flex-row gap-3 sm:items-end">
            <div class="flex-1">
                <label class="text-xs font-medium text-[#505f76] block mb-1">Mata Pelajaran / Rombel</label>
                <select wire:model.live="gmrId"
                        class="w-full px-3 py-2 border border-[#c5c5d7] rounded-lg text-sm bg-white cursor-pointer focus:outline-none focus:border-[#3c50e0] focus:ring-1 focus:ring-[#3c50e0]">
                    @foreach ($gmrList as $g)
                        <option value="{{ $g->id }}">{{ $g->mapel?->nama }} — {{ $g->rombel?->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Info legend --}}
        <div class="bg-[#EEF2FF] border border-[#3c50e0]/20 rounded-xl px-4 py-2.5 flex flex-wrap gap-4 items-center text-xs text-[#505f76]">
            <div class="flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[15px] text-[#3c50e0]">info</span>
                <strong>Ref</strong> = usulan dari rata-rata Tugas/CBT (hanya baca)
            </div>
            <div class="flex items-center gap-1.5">
                <span class="inline-block w-4 h-4 rounded border border-[#3c50e0] bg-white"></span>
                Kolom <strong>Akhir</strong> = nilai yang Anda input
            </div>
            <button wire:click="copyFromRef"
                    class="ml-auto flex items-center gap-1 text-[#3c50e0] font-medium hover:underline cursor-pointer">
                <span class="material-symbols-outlined text-[15px]">content_copy</span> Salin Ref → Akhir (kosong)
            </button>
        </div>

        {{-- Spreadsheet --}}
        <div class="bg-white border border-[#c5c5d7] rounded-xl overflow-hidden">
            <div class="px-4 py-3 border-b border-[#c5c5d7] flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <p class="text-sm font-semibold text-on-surface">{{ $peserta->count() }} peserta didik</p>
                <div class="relative w-full sm:w-auto">
                    <span class="material-symbols-outlined absolute left-2 top-1/2 -translate-y-1/2 text-[#757686] text-[18px] pointer-events-none">search</span>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama…"
                           class="w-full sm:w-52 pl-9 pr-3 py-2 border border-[#c5c5d7] rounded-lg text-sm bg-white focus:outline-none focus:border-[#3c50e0] focus:ring-1 focus:ring-[#3c50e0]">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse" style="min-width:640px">
                    <thead>
                        <tr class="bg-white border-b border-[#c5c5d7] text-xs font-semibold text-[#505f76] uppercase tracking-wide">
                            <th class="px-3 py-2.5 border-r border-[#c5c5d7] text-center w-10" rowspan="2">No</th>
                            <th class="px-3 py-2.5 border-r border-[#c5c5d7]" rowspan="2">Nama Peserta Didik</th>
                            @foreach ($komponen as $k)
                                <th class="px-3 py-2 border-r border-b border-[#c5c5d7] text-center" colspan="2">{{ $k }}</th>
                            @endforeach
                            <th class="px-3 py-2.5 border-r border-[#c5c5d7] text-center w-16" rowspan="2">Grade</th>
                            <th class="px-3 py-2.5 text-center w-16" rowspan="2">Catatan</th>
                        </tr>
                        <tr class="bg-white border-b-2 border-[#c5c5d7] text-xs text-[#757686]">
                            @foreach ($komponen as $k)
                                <th class="px-2 py-2 border-r border-[#c5c5d7] text-center font-normal">Ref</th>
                                <th class="px-2 py-2 border-r border-[#c5c5d7] text-center font-semibold text-[#505f76]">Akhir</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#c5c5d7]">
                        @forelse ($peserta as $i => $pd)
                            @php $g = $rowGrade[$pd->id] ?? null; @endphp
                            <tr class="hover:bg-[#f6fafe] transition-colors">
                                <td class="px-3 py-2.5 border-r border-[#c5c5d7] text-center text-[#757686]">{{ $i + 1 }}</td>
                                <td class="px-3 py-2.5 border-r border-[#c5c5d7] font-medium whitespace-nowrap">
                                    {{ $pd->nama_lengkap }}
                                    <span class="block text-xs text-[#757686] font-normal">NIPD: {{ $pd->nipd }}</span>
                                </td>
                                @foreach ($komponen as $k)
                                    @php $ref = $refs[$pd->id][$k] ?? null; @endphp
                                    <td class="px-2 py-2 border-r border-[#c5c5d7] text-center">
                                        <span class="inline-block px-2 py-0.5 bg-[#f0f4f8] rounded text-[#757686] text-xs">{{ $ref ?? '—' }}</span>
                                    </td>
                                    <td class="px-2 py-2 border-r border-[#c5c5d7] text-center">
                                        <input type="number" min="0" max="100" placeholder="—"
                                               wire:model.live.debounce.500ms="nilaiAkhir.{{ $pd->id }}.{{ $k }}"
                                               class="w-14 px-2 py-1 text-center border rounded text-sm focus:outline-none focus:border-[#3c50e0] focus:ring-1 focus:ring-[#3c50e0] placeholder-[#c5c5d7]
                                                      @error('nilaiAkhir.'.$pd->id.'.'.$k) border-[#ba1a1a] @else border-[#c5c5d7] @enderror">
                                    </td>
                                @endforeach
                                <td class="px-3 py-2.5 border-r border-[#c5c5d7] text-center">
                                    @if ($g)
                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full font-bold text-xs {{ $gradeCls[$g] ?? 'bg-[#f0f4f8] text-[#757686]' }}">{{ $g }}</span>
                                    @else
                                        <span class="text-[#c5c5d7] italic text-xs">—</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5 text-center">
                                    @php $hasNote = trim($catatan[$pd->id] ?? '') !== ''; @endphp
                                    <button wire:click="openNote({{ $pd->id }})"
                                            class="p-1 transition-colors cursor-pointer {{ $hasNote ? 'text-[#3c50e0]' : 'text-[#757686] hover:text-[#3c50e0]' }}"
                                            title="{{ $hasNote ? 'Ada catatan' : 'Tambah catatan' }}">
                                        <span class="material-symbols-outlined text-[18px] {{ $hasNote ? 'filled' : '' }}">{{ $hasNote ? 'comment' : 'add_comment' }}</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 4 + count($komponen) * 2 }}" class="px-4 py-16 text-center">
                                    <span class="material-symbols-outlined text-[48px] text-[#c5c5d7] mb-3 block">group_off</span>
                                    <p class="text-[15px] font-medium text-on-surface mb-1">
                                        {{ $search ? 'Tidak ada peserta didik yang cocok' : 'Belum ada peserta didik di rombel ini' }}
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-4 py-3 border-t border-[#c5c5d7] flex flex-col sm:flex-row items-center justify-between gap-3">
                <p class="text-xs text-[#757686]">
                    @if ($belum > 0){{ $belum }} peserta didik belum lengkap nilai akhirnya.@else Semua nilai lengkap.@endif
                </p>
                <button wire:click="save" wire:loading.attr="disabled"
                        class="px-5 py-2 bg-[#3c50e0] text-white rounded-lg text-sm font-semibold hover:bg-[#2a3db0] transition-colors flex items-center gap-2 cursor-pointer">
                    <span wire:loading wire:target="save" class="material-symbols-outlined text-[18px] animate-spin">progress_activity</span>
                    <span wire:loading.remove wire:target="save" class="material-symbols-outlined text-[18px]">save</span>
                    Simpan Nilai
                </button>
            </div>
        </div>
    @endif

    {{-- ── Modal catatan ───────────────────────────────────────────────────── --}}
    @if ($notePdId !== null)
        @php $notePd = $peserta->firstWhere('id', $notePdId) ?? \App\Models\PesertaDidik::find($notePdId); @endphp
        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/40 backdrop-blur-sm p-0 sm:p-4">
            <div class="bg-white w-full max-w-4xl sm:max-w-sm rounded-t-2xl sm:rounded-xl overflow-hidden shadow-xl p-5">
                <h3 class="text-base font-bold text-on-surface mb-1">Catatan untuk {{ $notePd?->nama_lengkap }}</h3>
                <p class="text-xs text-[#757686] mb-3">{{ $gmr?->mapel?->nama }} · {{ $gmr?->rombel?->nama }}</p>
                <textarea wire:model="noteDraft" rows="4"
                          placeholder="Tulis catatan untuk mapel ini (opsional)…"
                          class="w-full px-3 py-2 border border-[#c5c5d7] rounded-lg text-sm resize-none focus:outline-none focus:border-[#3c50e0] focus:ring-1 focus:ring-[#3c50e0]"></textarea>
                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2 mt-3">
                    <button wire:click="closeNote"
                            class="w-full sm:w-auto px-4 py-2 text-sm rounded-lg border border-[#c5c5d7] text-[#505f76] hover:bg-[#f0f4f8] cursor-pointer text-center">Batal</button>
                    <button wire:click="saveNote"
                            class="w-full sm:w-auto px-4 py-2 text-sm rounded-lg bg-[#3c50e0] text-white hover:bg-[#2a3db0] font-medium cursor-pointer text-center">Simpan Catatan</button>
                </div>
                <p class="text-[11px] text-[#757686] mt-2">Catatan tersimpan permanen saat Anda menekan <strong>Simpan Nilai</strong>.</p>
            </div>
        </div>
    @endif

</div>
