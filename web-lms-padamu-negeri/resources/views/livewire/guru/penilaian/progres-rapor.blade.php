<div class="max-w-4xl mx-auto space-y-4">

    {{-- ── Heading ─────────────────────────────────────────────────────────── --}}
    <div>
        <h1 class="text-xl font-bold text-on-surface">Progres Rapor</h1>
        <p class="text-sm text-[#505f76] mt-0.5 flex items-center gap-1.5">
            <span class="material-symbols-outlined text-[15px]">groups</span>
            {{ $rombel?->nama ?? 'Wali Kelas' }}@if ($periode) · {{ ucfirst($periode->semester) }} {{ $periode->tahun_ajaran }}@endif
        </p>
    </div>

    @if (! $rombel)
        <div class="bg-white border border-[#c5c5d7] rounded-xl px-6 py-16 text-center">
            <span class="material-symbols-outlined text-[48px] text-[#c5c5d7] mb-3 block">grade</span>
            <p class="text-[15px] font-medium text-on-surface mb-1">Belum ada rombel</p>
            <p class="text-[13px] text-[#757686]">Anda bukan wali kelas di periode yang dipilih.</p>
        </div>
    @else
        {{-- Selektor rombel (jika wali >1) --}}
        @if ($rombels->count() > 1)
            <div class="bg-white border border-[#c5c5d7] rounded-xl p-4">
                <label class="text-xs font-medium text-[#505f76] block mb-1">Pilih Rombel</label>
                <select wire:model.live="rombelId"
                        class="w-full sm:w-80 px-3 py-2 border border-[#c5c5d7] rounded-lg text-sm bg-white cursor-pointer focus:outline-none focus:border-[#3c50e0] focus:ring-1 focus:ring-[#3c50e0]">
                    @foreach ($rombels as $r)
                        <option value="{{ $r->id }}">{{ $r->nama }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        @php $pct = $totalMapel > 0 ? round($lengkapCount / $totalMapel * 100) : 0; @endphp

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

            {{-- ══ Kiri: ringkasan + tindakan ═════════════════════════════════ --}}
            <div class="flex flex-col gap-4">
                {{-- Donut --}}
                <div class="bg-white border border-[#c5c5d7] rounded-xl p-5">
                    <p class="text-sm font-semibold mb-4 text-on-surface">Ringkasan Status</p>
                    <div class="relative w-28 h-28 mx-auto mb-5">
                        <svg class="w-full h-full -rotate-90" viewBox="0 0 36 36">
                            <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#e4e9ed" stroke-width="3"/>
                            <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#3c50e0" stroke-width="3" stroke-dasharray="{{ $pct }} 100" stroke-linecap="round"/>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-xl font-bold text-on-surface">{{ $lengkapCount }}/{{ $totalMapel }}</span>
                            <span class="text-xs text-[#757686]">Selesai</span>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-green-500 flex-shrink-0"></span><span class="text-[#505f76]">Lengkap</span></div>
                            <span class="font-semibold text-on-surface">{{ $lengkapCount }} Mapel</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-amber-400 flex-shrink-0"></span><span class="text-[#505f76]">Belum Lengkap</span></div>
                            <span class="font-semibold text-on-surface">{{ $totalMapel - $lengkapCount }} Mapel</span>
                        </div>
                    </div>
                    @if ($status === 'terbit')
                        <div class="mt-4 flex items-center justify-center gap-1.5 text-xs px-2 py-1.5 rounded-lg bg-green-50 text-green-700 border border-green-200 font-medium">
                            <span class="material-symbols-outlined text-[15px] filled">verified</span> Rapor Sudah Terbit
                        </div>
                    @endif
                </div>

                {{-- Catatan wali kelas --}}
                <div class="bg-white border border-[#c5c5d7] rounded-xl p-4 space-y-2">
                    <p class="text-sm font-semibold text-on-surface">Catatan Wali Kelas</p>
                    <textarea wire:model="catatanWali" rows="4" placeholder="Tulis catatan untuk seluruh kelas…"
                              class="w-full px-3 py-2 border border-[#c5c5d7] rounded-lg text-sm resize-none focus:outline-none focus:border-[#3c50e0] focus:ring-1 focus:ring-[#3c50e0]"></textarea>
                    <button wire:click="saveCatatan" wire:loading.attr="disabled"
                            class="w-full py-2 text-sm border border-[#c5c5d7] rounded-lg text-[#505f76] hover:bg-[#f0f4f8] transition-colors cursor-pointer">
                        Simpan Catatan
                    </button>
                </div>

                {{-- Pratinjau & cetak --}}
                <a href="{{ route('guru.rapor.preview') }}" wire:navigate
                   class="bg-white border border-[#c5c5d7] rounded-xl p-4 flex items-center gap-3 hover:bg-[#f6fafe] transition-colors cursor-pointer">
                    <div class="w-9 h-9 rounded-full bg-[#EEF2FF] text-[#3c50e0] flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-[18px]">print</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-on-surface">Pratinjau &amp; Cetak Rapor</p>
                        <p class="text-xs text-[#757686]">Lihat & unduh rapor per peserta didik.</p>
                    </div>
                    <span class="material-symbols-outlined text-[#757686]">chevron_right</span>
                </a>

                {{-- Tindakan final --}}
                <div class="bg-white border border-[#c5c5d7] rounded-xl p-4 space-y-3">
                    <p class="text-sm font-semibold text-on-surface">Tindakan Final</p>
                    @if ($status === 'terbit')
                        <p class="text-xs text-[#757686]">Rapor sudah diterbitkan dan dapat dilihat peserta didik.</p>
                        <button disabled class="w-full py-2.5 bg-green-50 text-green-700 border border-green-200 rounded-lg text-sm font-semibold cursor-not-allowed flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-[18px] filled">verified</span> Rapor Terbit
                        </button>
                    @else
                        <p class="text-xs text-[#757686]">Pastikan semua nilai telah terisi sebelum menerbitkan rapor.</p>
                        <button wire:click="$set('showPublish', true)" @disabled(! $allComplete)
                                @class([
                                    'w-full py-2.5 rounded-lg text-sm font-semibold flex items-center justify-center gap-2 transition-colors',
                                    'bg-[#3c50e0] text-white hover:bg-[#2a3db0] cursor-pointer' => $allComplete,
                                    'bg-[#e4e9ed] text-[#757686] cursor-not-allowed' => ! $allComplete,
                                ])>
                            <span class="material-symbols-outlined text-[18px]">publish</span> Terbitkan Rapor
                        </button>
                        @unless ($allComplete)
                            <p class="text-xs text-red-500 flex items-center gap-1 justify-center">
                                <span class="material-symbols-outlined text-[14px]">info</span> Data belum lengkap ({{ $totalMapel - $lengkapCount }} mapel)
                            </p>
                        @endunless
                    @endif
                </div>
            </div>

            {{-- ══ Kanan: daftar mapel ════════════════════════════════════════ --}}
            <div class="lg:col-span-2 bg-white border border-[#c5c5d7] rounded-xl overflow-hidden">
                <div class="px-4 py-3 border-b border-[#c5c5d7]">
                    <p class="text-sm font-semibold text-on-surface">Daftar Mata Pelajaran</p>
                    <p class="text-xs text-[#757686]">Status pengisian nilai oleh guru pengampu.</p>
                </div>
                <ul class="divide-y divide-[#c5c5d7]">
                    @forelse ($mapelRows as $row)
                        <li class="flex items-center gap-3 px-4 py-3.5 hover:bg-[#f6fafe] transition-colors">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 {{ $row['lengkap'] ? 'bg-[#EEF2FF] text-[#3c50e0]' : 'bg-amber-50 text-amber-600' }}">
                                <span class="material-symbols-outlined text-[18px]">menu_book</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-on-surface">{{ $row['mapel']?->nama ?? '—' }}</p>
                                <p class="text-xs text-[#757686]">{{ $row['guru']?->nama_lengkap ?? 'Belum ada guru' }}</p>
                            </div>
                            @if ($row['lengkap'])
                                <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full bg-green-50 text-green-700 border border-green-200 font-medium whitespace-nowrap">
                                    <span class="material-symbols-outlined text-[13px] filled flex-shrink-0">check_circle</span> Lengkap
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200 font-medium whitespace-nowrap">
                                    <span class="material-symbols-outlined text-[13px] flex-shrink-0">pending</span> Belum Lengkap
                                </span>
                            @endif
                        </li>
                    @empty
                        <li class="px-4 py-16 text-center">
                            <span class="material-symbols-outlined text-[48px] text-[#c5c5d7] mb-3 block">book</span>
                            <p class="text-[15px] font-medium text-on-surface mb-1">Belum ada mata pelajaran</p>
                            <p class="text-[13px] text-[#757686]">Belum ada pemetaan guru-mapel untuk rombel ini.</p>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    @endif

    {{-- ── Modal konfirmasi terbit ─────────────────────────────────────────── --}}
    @if ($showPublish)
        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/40 backdrop-blur-sm p-0 sm:p-4">
            <div class="bg-white w-full max-w-4xl sm:max-w-sm rounded-t-2xl sm:rounded-xl overflow-hidden shadow-xl p-6">
                <div class="flex items-start gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-[#EEF2FF] flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-[#3c50e0]">publish</span>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-on-surface">Terbitkan Rapor?</h3>
                        <p class="text-sm text-[#505f76] mt-1">Setelah terbit, rapor seluruh peserta didik di kelas ini dapat dilihat & dicetak. Nilai masih bisa diperbarui guru bila diperlukan.</p>
                    </div>
                </div>
                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2">
                    <button wire:click="$set('showPublish', false)"
                            class="w-full sm:w-auto px-4 py-2 text-sm rounded-lg border border-[#c5c5d7] text-[#505f76] hover:bg-[#f0f4f8] cursor-pointer text-center">Batal</button>
                    <button wire:click="publish"
                            class="w-full sm:w-auto px-4 py-2 text-sm rounded-lg bg-[#3c50e0] text-white hover:bg-[#2a3db0] font-medium cursor-pointer text-center">Ya, Terbitkan</button>
                </div>
            </div>
        </div>
    @endif

</div>
