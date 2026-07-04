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
        <h1 class="text-xl font-bold text-on-surface">Konfigurasi Grade</h1>
        <p class="text-sm text-[#505f76] mt-0.5">Atur rentang nilai untuk konversi otomatis ke huruf grade. Rentang antar grade tidak boleh tumpang tindih.</p>
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

</div>
