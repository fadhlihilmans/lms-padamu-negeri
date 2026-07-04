<div class="w-full space-y-4">

    {{-- ── Header + tombol kembali ─────────────────────────────────────────── --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('guru.cbt') }}" wire:navigate
           class="w-9 h-9 flex items-center justify-center rounded-lg border border-[#c5c5d7] bg-white text-[#505f76] hover:bg-[#f0f4f8] transition-colors flex-shrink-0 cursor-pointer">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        </a>
        <div>
            <h1 class="text-[18px] font-bold text-on-surface">Koreksi Uraian</h1>
            <p class="text-[13px] text-[#757686] mt-0.5 truncate max-w-[70vw]">{{ $cbt->nama_ujian }}</p>
        </div>
    </div>

    @if ($uraianSoal->isEmpty())
        <div class="bg-white border border-[#c5c5d7] rounded-xl px-6 py-16 text-center">
            <span class="material-symbols-outlined text-[48px] text-[#c5c5d7] mb-3 block">rate_review</span>
            <p class="text-[15px] font-medium text-on-surface mb-1">Tidak ada soal uraian</p>
            <p class="text-[13px] text-[#757686]">CBT ini tidak memiliki soal uraian untuk dikoreksi.</p>
        </div>
    @elseif ($totalSubmit === 0)
        <div class="bg-white border border-[#c5c5d7] rounded-xl px-6 py-16 text-center">
            <span class="material-symbols-outlined text-[48px] text-[#c5c5d7] mb-3 block">groups</span>
            <p class="text-[15px] font-medium text-on-surface mb-1">Belum ada yang mengerjakan</p>
            <p class="text-[13px] text-[#757686]">Koreksi tersedia setelah peserta didik mengerjakan CBT.</p>
        </div>
    @else
        <div class="flex flex-col lg:flex-row gap-4 items-start">

            {{-- ══ Panel kiri: daftar PD ══════════════════════════════════════ --}}
            <div class="w-full lg:w-72 flex-shrink-0 bg-white border border-[#c5c5d7] rounded-xl overflow-hidden lg:sticky lg:top-0 flex flex-col">
                <div class="px-4 py-3 border-b border-[#c5c5d7]">
                    <p class="text-xs font-semibold text-[#505f76] uppercase tracking-wide truncate">{{ $cbt->nama_ujian }}</p>
                    <p class="text-xs text-[#757686] mt-0.5">{{ $uraianSoal->count() }} soal uraian</p>
                    <div class="flex items-center gap-1.5 mt-2">
                        <div class="flex-1 h-1.5 bg-[#e4e9ed] rounded-full overflow-hidden">
                            <div class="h-1.5 bg-[#3c50e0] rounded-full" style="width: {{ $totalSubmit ? round($sudah / $totalSubmit * 100) : 0 }}%"></div>
                        </div>
                        <span class="text-xs text-[#505f76] whitespace-nowrap">{{ $sudah }} / {{ $totalSubmit }} dikoreksi</span>
                    </div>
                </div>
                <div class="px-3 py-2 border-b border-[#c5c5d7]">
                    <select wire:model.live="filterStatus"
                            class="w-full px-2 py-1.5 border border-[#c5c5d7] rounded-lg text-xs bg-white text-[#505f76] cursor-pointer focus:outline-none focus:border-[#3c50e0]">
                        <option value="">Semua Status</option>
                        <option value="menunggu">Menunggu Koreksi</option>
                        <option value="selesai">Sudah Dikoreksi</option>
                    </select>
                </div>
                <div class="max-h-72 lg:max-h-[60vh] overflow-y-auto py-1">
                    @forelse ($hasilList as $h)
                        @php
                            $nama    = $h->pesertaDidik?->nama_lengkap ?? '—';
                            $inisial = collect(explode(' ', trim($nama)))->take(2)->map(fn ($w) => mb_substr($w, 0, 1))->join('');
                            $aktif   = $selectedHasilId === $h->id;
                            $selesai = $h->status_penilaian === 'selesai_dinilai';
                        @endphp
                        <button wire:click="selectHasil({{ $h->id }})"
                                @class([
                                    'w-full text-left px-3 py-3 flex items-center justify-between transition-colors cursor-pointer',
                                    'bg-[#EEF2FF] border-l-4 border-[#3c50e0]' => $aktif,
                                    'hover:bg-[#f0f4f8] border-l-4 border-transparent' => ! $aktif,
                                    'opacity-70' => $selesai && ! $aktif,
                                ])>
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0 {{ $aktif ? 'bg-[#3c50e0] text-white' : 'bg-[#e4e9ed] text-[#757686]' }}">{{ strtoupper($inisial) }}</div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-on-surface truncate">{{ $nama }}</p>
                                    <p class="text-xs text-[#757686]">NIPD: {{ $h->pesertaDidik?->nipd ?? '—' }}</p>
                                </div>
                            </div>
                            @if ($selesai)
                                <span class="text-xs px-1.5 py-0.5 rounded-full bg-green-50 text-green-700 border border-green-200 font-medium whitespace-nowrap flex-shrink-0">Selesai</span>
                            @else
                                <span class="text-xs px-1.5 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200 font-medium whitespace-nowrap flex-shrink-0">Menunggu</span>
                            @endif
                        </button>
                    @empty
                        <p class="px-3 py-6 text-center text-xs text-[#757686]">Tidak ada peserta didik untuk filter ini.</p>
                    @endforelse
                </div>
            </div>

            {{-- ══ Panel kanan: soal + jawaban + nilai ════════════════════════ --}}
            <div class="flex-1 w-full min-w-0 space-y-4">
                @if (! $current)
                    <div class="bg-white border border-[#c5c5d7] rounded-xl px-6 py-16 text-center">
                        <span class="material-symbols-outlined text-[48px] text-[#c5c5d7] mb-3 block">touch_app</span>
                        <p class="text-[14px] text-[#757686]">Pilih peserta didik di panel kiri untuk mulai mengoreksi.</p>
                    </div>
                @else
                    @php
                        $namaCur    = $current->pesertaDidik?->nama_lengkap ?? '—';
                        $inisialCur = collect(explode(' ', trim($namaCur)))->take(2)->map(fn ($w) => mb_substr($w, 0, 1))->join('');
                    @endphp

                    {{-- PD info bar --}}
                    <div class="bg-white border border-[#c5c5d7] rounded-xl px-4 py-3 flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-[#3c50e0] text-white flex items-center justify-center text-sm font-bold flex-shrink-0">{{ strtoupper($inisialCur) }}</div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-on-surface truncate">{{ $namaCur }}</p>
                            <p class="text-xs text-[#757686] truncate">NIPD: {{ $current->pesertaDidik?->nipd ?? '—' }} · {{ $cbt->guruMapelRombel?->rombel?->nama }}</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-xs text-[#757686]">Skor PG (auto)</p>
                            <p class="text-lg font-bold text-[#3c50e0]">{{ $nilaiPg }}</p>
                        </div>
                    </div>

                    {{-- Soal uraian + jawaban + input nilai --}}
                    @foreach ($uraianSoal as $idx => $soal)
                        @php $jawaban = $jawabanMap[$soal->id]?->jawaban ?? null; @endphp
                        <div class="bg-white border border-[#c5c5d7] rounded-xl overflow-hidden">
                            <div class="border-b border-[#c5c5d7] px-4 py-2.5 flex items-center gap-2">
                                <span class="text-xs font-semibold text-[#505f76]">Soal Uraian #{{ $idx + 1 }}</span>
                                <span class="text-[#c5c5d7]">·</span>
                                <span class="text-xs text-[#757686]">Skala nilai 0–100</span>
                            </div>
                            <div class="px-4 py-4">
                                <p class="text-sm text-on-surface leading-relaxed mb-3 whitespace-pre-line">{{ $soal->pertanyaan }}</p>

                                {{-- Jawaban PD --}}
                                <div class="bg-white border border-[#c5c5d7] rounded-lg p-3 mb-3">
                                    <p class="text-xs font-medium text-[#505f76] mb-1.5">Jawaban {{ $namaCur }}:</p>
                                    @if ($jawaban)
                                        <p class="text-sm text-on-surface leading-relaxed whitespace-pre-line">{{ $jawaban }}</p>
                                    @else
                                        <p class="text-sm text-[#c5c5d7] italic">(tidak dijawab)</p>
                                    @endif
                                </div>

                                {{-- Input nilai --}}
                                <div class="flex items-center gap-2">
                                    <label class="text-sm font-medium text-[#505f76] whitespace-nowrap">Nilai (0–100):</label>
                                    <input type="number" min="0" max="100" wire:model="skor.{{ $soal->id }}"
                                           class="w-20 px-3 py-1.5 border rounded-lg text-sm font-bold text-[#3c50e0] text-center focus:outline-none focus:border-[#3c50e0] focus:ring-1 focus:ring-[#3c50e0] @error('skor.'.$soal->id) border-[#ba1a1a] @else border-[#c5c5d7] @enderror">
                                    <span class="text-xs text-[#757686]">/ 100</span>
                                    @error('skor.'.$soal->id) <span class="text-xs text-[#ba1a1a]">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    @endforeach

                    {{-- Bottom bar: total + navigasi --}}
                    <div class="bg-white border border-[#c5c5d7] rounded-xl px-4 py-3 flex flex-col sm:flex-row items-center gap-3 justify-between">
                        <div class="text-sm text-[#505f76] text-center sm:text-left">
                            Total: <strong class="text-on-surface">PG {{ $nilaiPg }}</strong>
                            + <strong class="text-on-surface">Uraian {{ $uraianAvg ?? '–' }}</strong>
                            = <strong class="text-[#3c50e0] text-lg">{{ $previewAkhir ?? '–' }}</strong> / 100
                        </div>
                        <div class="flex gap-2 w-full sm:w-auto">
                            <button type="button" wire:click="goToPrev"
                                    class="flex-1 sm:flex-none px-4 py-2 text-sm rounded-lg border border-[#c5c5d7] text-[#505f76] hover:bg-[#f0f4f8] flex items-center justify-center gap-1.5 cursor-pointer">
                                <span class="material-symbols-outlined text-[16px]">chevron_left</span> Sebelumnya
                            </button>
                            <button type="button" wire:click="saveAndNext" wire:loading.attr="disabled"
                                    class="flex-1 sm:flex-none px-4 py-2 text-sm rounded-lg bg-[#3c50e0] text-white hover:bg-[#2a3db0] font-medium flex items-center justify-center gap-1.5 cursor-pointer">
                                <span wire:loading wire:target="saveAndNext" class="material-symbols-outlined text-[16px] animate-spin">progress_activity</span>
                                Simpan &amp; Lanjut <span wire:loading.remove wire:target="saveAndNext" class="material-symbols-outlined text-[16px]">chevron_right</span>
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

</div>
