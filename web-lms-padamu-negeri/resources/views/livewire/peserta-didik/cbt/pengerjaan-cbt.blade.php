<div class="flex flex-col h-screen overflow-hidden"
     x-data="{
        deadline: {{ $deadlineTs }},
        now: Date.now(),
        submitted: false,
        soalSheet: false,
        timer: null,
        init() {
            this.timer = setInterval(() => {
                this.now = Date.now();
                if (this.remainingMs <= 0 && !this.submitted) {
                    this.submitted = true;
                    clearInterval(this.timer);
                    this.$wire.submitCbt();
                }
            }, 1000);
        },
        get remainingMs() { return Math.max(0, this.deadline - this.now); },
        get display() {
            let t = Math.floor(this.remainingMs / 1000);
            let h = String(Math.floor(t / 3600)).padStart(2, '0');
            let m = String(Math.floor((t % 3600) / 60)).padStart(2, '0');
            let s = String(t % 60).padStart(2, '0');
            return h + ':' + m + ':' + s;
        },
        get danger() { return this.remainingMs <= 300000; }
     }">

    @php
        $pdNama    = auth()->user()?->pesertaDidik?->nama_lengkap ?? '';
        $nomorSoal = $currentIndex + 1;
        $progress  = $totalSoal ? round($nomorSoal / $totalSoal * 100) : 0;
    @endphp

    {{-- ── Top bar: info + timer ───────────────────────────────────────────── --}}
    <header class="bg-white border-b border-[#c5c5d7] flex items-center justify-between px-4 py-3 flex-shrink-0">
        <div class="flex items-center gap-3 min-w-0">
            <div class="w-8 h-8 rounded bg-[#3c50e0] text-white flex items-center justify-center flex-shrink-0"><span class="material-symbols-outlined text-[18px]">quiz</span></div>
            <div class="min-w-0">
                <p class="text-sm font-bold leading-tight truncate">{{ $cbt->nama_ujian }}</p>
                <p class="text-xs text-[#757686] truncate">{{ $cbt->guruMapelRombel?->mapel?->nama }} · {{ $pdNama }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2 rounded-lg px-3 py-1.5 border flex-shrink-0"
             :class="danger ? 'bg-red-50 border-red-200' : 'bg-[#EEF2FF] border-[#3c50e0]/30'">
            <span class="material-symbols-outlined text-[18px]" :class="danger ? 'text-red-600' : 'text-[#3c50e0]'">timer</span>
            <span class="text-base font-bold tabular-nums" :class="danger ? 'text-red-600' : 'text-[#3c50e0]'" x-text="display">--:--:--</span>
        </div>
    </header>

    {{-- ── Content ─────────────────────────────────────────────────────────── --}}
    <div class="flex-1 flex overflow-hidden">

        {{-- Soal area --}}
        <main class="flex-1 overflow-y-auto p-4 pb-24 lg:pb-6 lg:p-6">
            <div class="max-w-2xl mx-auto space-y-5">

                {{-- Progress --}}
                <div>
                    <div class="flex items-center justify-between text-xs text-[#505f76] mb-1.5">
                        <span>Soal <strong class="text-on-surface">{{ $nomorSoal }}</strong> dari <strong class="text-on-surface">{{ $totalSoal }}</strong></span>
                        <span>{{ $answeredCount }} dijawab · {{ $totalSoal - $answeredCount }} belum</span>
                    </div>
                    <div class="w-full h-1.5 bg-[#e4e9ed] rounded-full overflow-hidden">
                        <div class="h-1.5 bg-[#3c50e0] rounded-full transition-all" style="width: {{ $progress }}%"></div>
                    </div>
                </div>

                {{-- Soal card --}}
                @if ($current)
                    <div class="bg-white border border-[#c5c5d7] rounded-xl overflow-hidden" wire:key="soal-{{ $current->id }}">
                        <div class="bg-white border-b border-[#c5c5d7] px-4 py-2.5 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-[#3c50e0] text-white text-xs font-bold flex items-center justify-center flex-shrink-0">{{ $nomorSoal }}</span>
                            <span class="text-xs font-medium text-[#505f76]">{{ $current->tipe_soal === 'pilihan_ganda' ? 'Pilihan Ganda' : 'Uraian' }}</span>
                        </div>
                        <div class="px-5 py-5">
                            <p class="text-sm text-on-surface leading-relaxed mb-5 whitespace-pre-line">{{ $current->pertanyaan }}</p>

                            @if ($current->tipe_soal === 'pilihan_ganda')
                                <div class="space-y-2.5">
                                    @foreach (($current->pilihan_jawaban ?? []) as $huruf => $teks)
                                        @php $selected = ($answers[$current->id] ?? '') === $huruf; @endphp
                                        <button type="button" wire:click="selectAnswer({{ $current->id }}, '{{ $huruf }}')"
                                                @class([
                                                    'w-full text-left flex items-start gap-3 px-4 py-3 rounded-[10px] transition-all cursor-pointer',
                                                    'border-2 border-[#3c50e0] bg-[#EEF2FF]' => $selected,
                                                    'border border-[#c5c5d7] bg-white hover:border-[#3c50e0] hover:bg-[#EEF2FF]' => ! $selected,
                                                ])>
                                            <span @class([
                                                    'w-[18px] h-[18px] rounded-full border-2 flex items-center justify-center flex-shrink-0 mt-0.5',
                                                    'border-[#3c50e0]' => $selected,
                                                    'border-[#c5c5d7]' => ! $selected,
                                                ])>
                                                @if ($selected)<span class="w-2 h-2 rounded-full bg-[#3c50e0]"></span>@endif
                                            </span>
                                            <span>
                                                <span class="text-xs font-semibold {{ $selected ? 'text-[#3c50e0]' : 'text-[#505f76]' }}">{{ $huruf }}.</span>
                                                <span class="text-sm text-on-surface ml-1 {{ $selected ? 'font-medium' : '' }}">{{ $teks }}</span>
                                            </span>
                                        </button>
                                    @endforeach
                                </div>
                            @else
                                <textarea wire:model.blur="answers.{{ $current->id }}" rows="6"
                                          placeholder="Tulis jawaban kamu di sini…"
                                          class="w-full px-3 py-3 border border-[#c5c5d7] rounded-lg text-sm resize-y focus:outline-none focus:border-[#3c50e0] focus:ring-1 focus:ring-[#3c50e0] leading-relaxed"></textarea>
                                <p class="text-xs text-[#757686] mt-1.5">Jawaban tersimpan otomatis saat kamu berpindah soal.</p>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Nav prev/next --}}
                <div class="flex items-center justify-between pt-1">
                    <button wire:click="prev" @disabled($currentIndex === 0)
                            class="flex items-center gap-1.5 px-4 py-2 text-sm rounded-lg border border-[#c5c5d7] text-[#505f76] hover:bg-[#f0f4f8] transition-colors cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed">
                        <span class="material-symbols-outlined text-[18px]">chevron_left</span> Sebelumnya
                    </button>
                    @if ($currentIndex < $totalSoal - 1)
                        <button wire:click="next"
                                class="flex items-center gap-1.5 px-4 py-2 text-sm rounded-lg bg-[#3c50e0] text-white hover:bg-[#2a3db0] transition-colors font-medium cursor-pointer">
                            Selanjutnya <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                        </button>
                    @else
                        <button wire:click="openSubmitModal"
                                class="flex items-center gap-1.5 px-4 py-2 text-sm rounded-lg bg-green-600 text-white hover:bg-green-700 transition-colors font-medium cursor-pointer">
                            <span class="material-symbols-outlined text-[18px]">send</span> Submit
                        </button>
                    @endif
                </div>

            </div>
        </main>

        {{-- Sidebar navigasi soal (desktop) --}}
        <aside class="hidden lg:flex flex-col w-64 flex-shrink-0 border-l border-[#c5c5d7] bg-white overflow-y-auto">
            <div class="px-4 py-3 border-b border-[#c5c5d7]">
                <p class="text-sm font-semibold">Navigasi Soal</p>
                <div class="flex flex-wrap gap-3 mt-2 text-xs text-[#505f76]">
                    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-[#3c50e0] inline-block"></span> Dijawab</span>
                    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-[#EEF2FF] border-2 border-[#3c50e0] inline-block"></span> Saat ini</span>
                    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-[#e4e9ed] border border-[#c5c5d7] inline-block"></span> Kosong</span>
                </div>
            </div>
            <div class="flex-1 p-3 grid grid-cols-5 gap-1.5 content-start">
                @foreach ($soalList as $idx => $s)
                    @php
                        $answered = in_array($s->id, $answeredIds, true);
                        $isCur    = $idx === $currentIndex;
                    @endphp
                    <button wire:click="goTo({{ $idx }})"
                            @class([
                                'w-9 h-9 flex items-center justify-center rounded-md text-[13px] font-semibold cursor-pointer transition-all',
                                'bg-[#3c50e0] text-white border border-[#3c50e0]' => $answered && ! $isCur,
                                'bg-[#EEF2FF] text-[#3c50e0] border-2 border-[#3c50e0]' => $isCur,
                                'bg-white text-[#505f76] border border-[#c5c5d7] hover:bg-[#f0f4f8]' => ! $answered && ! $isCur,
                            ])>{{ $idx + 1 }}</button>
                @endforeach
            </div>
            <div class="px-4 pb-4 pt-2 border-t border-[#c5c5d7]">
                <button wire:click="openSubmitModal"
                        class="w-full flex items-center justify-center gap-2 py-2.5 rounded-lg bg-green-600 text-white text-sm font-semibold hover:bg-green-700 transition-colors shadow-sm cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">send</span> Submit Jawaban
                </button>
            </div>
        </aside>
    </div>

    {{-- Mobile: bottom bar --}}
    <div class="lg:hidden fixed bottom-0 inset-x-0 z-30 bg-white border-t border-[#c5c5d7]">
        <div class="flex items-center gap-2 px-3 py-2.5">
            <button type="button" @click="soalSheet = true"
                    class="flex items-center gap-1.5 px-3 py-2 rounded-lg border border-[#c5c5d7] text-sm text-[#505f76] font-medium hover:bg-[#f0f4f8] flex-shrink-0 cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">grid_view</span> Soal
            </button>
            <button wire:click="openSubmitModal"
                    class="flex-1 flex items-center justify-center gap-2 py-2 rounded-lg bg-green-600 text-white text-sm font-semibold hover:bg-green-700 cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">send</span> Submit Jawaban
            </button>
        </div>
    </div>

    {{-- Mobile: soal sheet --}}
    <div class="lg:hidden fixed inset-0 z-40 bg-black/40" x-show="soalSheet" x-cloak @click="soalSheet = false" style="display:none">
        <div class="absolute inset-x-0 bottom-0 bg-white rounded-t-2xl shadow-xl max-h-[60vh] flex flex-col" @click.stop>
            <div class="px-4 py-3 border-b border-[#c5c5d7] flex items-center justify-between flex-shrink-0">
                <p class="text-sm font-semibold">Navigasi Soal</p>
                <button type="button" @click="soalSheet = false" class="text-[#757686] cursor-pointer"><span class="material-symbols-outlined">close</span></button>
            </div>
            <div class="px-4 py-2.5 flex flex-wrap gap-3 border-b border-[#c5c5d7] text-xs text-[#505f76] flex-shrink-0">
                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-[#3c50e0] inline-block"></span> Dijawab</span>
                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-[#EEF2FF] border-2 border-[#3c50e0] inline-block"></span> Saat ini</span>
                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-[#e4e9ed] border border-[#c5c5d7] inline-block"></span> Kosong</span>
            </div>
            <div class="flex-1 overflow-y-auto p-4 grid grid-cols-6 gap-1.5 content-start">
                @foreach ($soalList as $idx => $s)
                    @php
                        $answered = in_array($s->id, $answeredIds, true);
                        $isCur    = $idx === $currentIndex;
                    @endphp
                    <button wire:click="goTo({{ $idx }})" @click="soalSheet = false"
                            @class([
                                'w-9 h-9 flex items-center justify-center rounded-md text-[13px] font-semibold cursor-pointer transition-all',
                                'bg-[#3c50e0] text-white border border-[#3c50e0]' => $answered && ! $isCur,
                                'bg-[#EEF2FF] text-[#3c50e0] border-2 border-[#3c50e0]' => $isCur,
                                'bg-white text-[#505f76] border border-[#c5c5d7] hover:bg-[#f0f4f8]' => ! $answered && ! $isCur,
                            ])>{{ $idx + 1 }}</button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Modal konfirmasi submit --}}
    @if ($showSubmitModal)
        <div class="fixed inset-0 z-50 bg-black/40 flex items-end sm:items-center justify-center p-0 sm:p-4">
            <div class="bg-white w-full max-w-4xl sm:max-w-sm rounded-t-2xl sm:rounded-xl overflow-hidden shadow-xl">
                <div class="px-5 py-4 border-b border-[#c5c5d7] flex items-center justify-between">
                    <h2 class="text-base font-semibold">Selesaikan Ujian?</h2>
                    <button wire:click="closeSubmitModal" class="text-[#757686] cursor-pointer"><span class="material-symbols-outlined">close</span></button>
                </div>
                <div class="px-5 py-4 space-y-3">
                    @if ($totalSoal - $answeredCount > 0)
                        <div class="bg-amber-50 border border-amber-200 rounded-lg px-3 py-2.5 text-xs text-amber-700">
                            <p class="font-semibold mb-1">Perhatian!</p>
                            <p>Masih ada <strong>{{ $totalSoal - $answeredCount }} soal belum dijawab</strong>. Setelah submit, jawaban tidak bisa diubah.</p>
                        </div>
                    @else
                        <div class="bg-green-50 border border-green-200 rounded-lg px-3 py-2.5 text-xs text-green-700">
                            Semua soal sudah dijawab. Setelah submit, jawaban tidak bisa diubah.
                        </div>
                    @endif
                    <div class="grid grid-cols-2 gap-3 text-center text-sm">
                        <div class="bg-[#f0f4f8] rounded-lg py-2">
                            <p class="text-xl font-bold text-[#3c50e0]">{{ $answeredCount }}</p>
                            <p class="text-xs text-[#757686]">Sudah dijawab</p>
                        </div>
                        <div class="bg-red-50 rounded-lg py-2">
                            <p class="text-xl font-bold text-red-500">{{ $totalSoal - $answeredCount }}</p>
                            <p class="text-xs text-[#757686]">Belum dijawab</p>
                        </div>
                    </div>
                </div>
                <div class="px-5 py-4 border-t border-[#c5c5d7] flex justify-end gap-2">
                    <button wire:click="closeSubmitModal" class="px-4 py-2 text-sm rounded-lg border border-[#c5c5d7] text-[#505f76] hover:bg-[#f0f4f8] cursor-pointer">Kembali</button>
                    <button wire:click="submitCbt" wire:loading.attr="disabled"
                            class="px-4 py-2 text-sm rounded-lg bg-green-600 text-white hover:bg-green-700 font-semibold cursor-pointer flex items-center gap-2">
                        <span wire:loading wire:target="submitCbt" class="material-symbols-outlined text-[16px] animate-spin">progress_activity</span>
                        Ya, Submit
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
