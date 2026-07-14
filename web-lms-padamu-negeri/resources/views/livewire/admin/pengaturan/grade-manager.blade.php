<div class="max-w-2xl mx-auto space-y-4">

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
        <h1 class="text-xl font-bold text-on-surface">Konfigurasi Nilai</h1>
        <p class="text-sm text-[#505f76] mt-0.5">Atur rentang grade dan bobot penilaian (CBT, komponen TUGAS, dan rapor). Tiap pasangan bobot wajib total 100%.</p>
    </div>

    <div class="bg-white border border-[#c5c5d7] rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-white border-b border-[#c5c5d7]">
                        <th class="px-4 py-3 text-xs font-semibold text-[#505f76] uppercase tracking-wide w-24">Grade</th>
                        <th class="px-4 py-3 text-xs font-semibold text-[#505f76] uppercase tracking-wide text-center w-32">Nilai Min</th>
                        <th class="px-4 py-3 text-xs font-semibold text-[#505f76] uppercase tracking-wide text-center w-32">Nilai Max</th>
                        <th class="px-4 py-3 text-xs font-semibold text-[#505f76] uppercase tracking-wide">Rentang</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#c5c5d7]">
                    @foreach ($ranges as $id => $r)
                        <tr class="hover:bg-[#f6fafe] transition-colors">
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full font-bold text-sm {{ $gradeCls[$r['grade']] ?? 'bg-[#f0f4f8] text-[#757686]' }}">{{ $r['grade'] }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <input type="number" min="0" max="100" wire:model="ranges.{{ $id }}.min"
                                       class="w-20 px-2 py-1.5 text-center border rounded-lg text-sm focus:outline-none focus:border-[#3c50e0] focus:ring-1 focus:ring-[#3c50e0] @error('ranges.'.$id.'.min') border-[#ba1a1a] @else border-[#c5c5d7] @enderror">
                            </td>
                            <td class="px-4 py-3 text-center">
                                <input type="number" min="0" max="100" wire:model="ranges.{{ $id }}.max"
                                       class="w-20 px-2 py-1.5 text-center border rounded-lg text-sm focus:outline-none focus:border-[#3c50e0] focus:ring-1 focus:ring-[#3c50e0] @error('ranges.'.$id.'.max') border-[#ba1a1a] @else border-[#c5c5d7] @enderror">
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $min = is_numeric($r['min']) ? max(0, min(100, (int) $r['min'])) : 0;
                                    $max = is_numeric($r['max']) ? max(0, min(100, (int) $r['max'])) : 0;
                                    $left = min($min, $max); $width = max(0, abs($max - $min));
                                @endphp
                                <div class="relative h-2 bg-[#f0f4f8] rounded-full overflow-hidden">
                                    <div class="absolute h-2 rounded-full {{ ['A'=>'bg-green-500','B'=>'bg-blue-500','C'=>'bg-amber-500','D'=>'bg-red-500'][$r['grade']] ?? 'bg-[#3c50e0]' }}"
                                         style="left: {{ $left }}%; width: {{ $width }}%"></div>
                                </div>
                                <p class="text-[11px] text-[#757686] mt-1">{{ $r['min'] }} – {{ $r['max'] }}</p>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Error umum (overlap / min>max) --}}
        @php $firstError = collect($errors->all())->first(); @endphp
        @if ($firstError)
            <div class="px-4 py-3 border-t border-[#c5c5d7] bg-[#fff9f9]">
                <p class="text-[13px] text-[#ba1a1a] flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">error</span> {{ $firstError }}
                </p>
            </div>
        @endif

        <div class="px-4 py-3 border-t border-[#c5c5d7] flex items-center justify-between gap-3">
            <p class="text-xs text-[#757686]">Nilai 0–100. Grade dihitung otomatis saat guru menginput nilai rapor.</p>
            <button wire:click="save" wire:loading.attr="disabled"
                    class="px-5 py-2 bg-[#3c50e0] text-white rounded-lg text-sm font-semibold hover:bg-[#2a3db0] transition-colors flex items-center gap-2 cursor-pointer">
                <span wire:loading wire:target="save" class="material-symbols-outlined text-[16px] animate-spin">progress_activity</span>
                <span wire:loading.remove wire:target="save" class="material-symbols-outlined text-[16px]">save</span>
                Simpan
            </button>
        </div>
    </div>

    {{-- ── Card Bobot (Revisi Tahap 4) ──────────────────────────────────────────
         Tiap grup WAJIB total 100%. Bila salah satu sumber nilai tidak ada,
         bobot otomatis dinormalisasi ke 100% saat perhitungan (lihat
         NilaiConfigService) — mis. CBT tanpa uraian → nilai PG dipakai penuh. --}}
    @foreach ($grupBobot as $grup => $items)
        @php
            $total = $items->sum(fn ($i) => (int) ($bobot[$i->key] ?? 0));
            $penjelasan = match ($grup) {
                'cbt'            => 'Menentukan nilai akhir sebuah CBT dari porsi soal Pilihan Ganda dan Uraian.',
                'komponen_tugas' => 'Komponen <b>TUGAS</b> di rapor adalah gabungan nilai Tugas dan nilai CBT.',
                'rapor'          => 'Nilai akhir tiap mata pelajaran di rapor = <b>TUGAS</b> + <b>SAS/SAT</b> sesuai bobot ini.',
                default          => '',
            };
        @endphp

        <div class="bg-white border border-[#c5c5d7] rounded-xl overflow-hidden">
            <div class="px-5 py-3.5 border-b border-[#c5c5d7] bg-[#f6fafe]">
                <p class="text-sm font-semibold text-on-surface">{{ $labelGrup[$grup] ?? $grup }}</p>
                <p class="text-[12px] text-[#757686] mt-0.5">{!! $penjelasan !!}</p>
            </div>

            <div class="p-5 space-y-4">
                @foreach ($items as $item)
                    <div class="flex items-center justify-between gap-4">
                        <label class="text-[13.5px] text-on-surface">{{ $item->label }}</label>
                        <div class="flex items-center gap-1.5 flex-shrink-0">
                            <input type="number" min="0" max="100" wire:model="bobot.{{ $item->key }}"
                                   class="w-24 px-3 py-2 border rounded-lg text-sm text-right focus:outline-none focus:border-[#3c50e0] focus:ring-1 focus:ring-[#3c50e0] @error('bobot.'.$item->key) border-[#ba1a1a] @else border-[#c5c5d7] @enderror">
                            <span class="text-[13px] text-[#757686]">%</span>
                        </div>
                    </div>
                    @error('bobot.'.$item->key)
                        <p class="text-[12px] text-[#ba1a1a] -mt-2">{{ $message }}</p>
                    @enderror
                @endforeach

                {{-- Indikator total: harus tepat 100% --}}
                <div class="flex items-center justify-between pt-3 border-t border-[#c5c5d7]">
                    <span class="text-[13px] font-medium text-[#505f76]">Total</span>
                    <span class="text-[14px] font-bold {{ $total === 100 ? 'text-green-600' : 'text-[#ba1a1a]' }}">
                        {{ $total }}%
                        @if ($total !== 100)
                            <span class="text-[12px] font-normal">(harus 100%)</span>
                        @endif
                    </span>
                </div>
            </div>

            <div class="px-5 py-3.5 border-t border-[#c5c5d7] flex justify-end">
                <button wire:click="saveBobot('{{ $grup }}')" wire:loading.attr="disabled"
                        class="px-5 py-2 bg-[#3c50e0] text-white rounded-lg text-sm font-semibold hover:bg-[#2a3db0] transition-colors flex items-center gap-2 cursor-pointer">
                    <span wire:loading wire:target="saveBobot('{{ $grup }}')" class="material-symbols-outlined text-[16px] animate-spin">progress_activity</span>
                    <span wire:loading.remove wire:target="saveBobot('{{ $grup }}')" class="material-symbols-outlined text-[16px]">save</span>
                    Simpan
                </button>
            </div>
        </div>
    @endforeach

</div>
