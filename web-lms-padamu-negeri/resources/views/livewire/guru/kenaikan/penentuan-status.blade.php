<div class="max-w-4xl mx-auto space-y-4">

    @php
        $statusMeta = [
            'naik'           => ['Naik Tingkat',   'arrow_upward',      'border-[#3c50e0] bg-[#EEF2FF] text-[#3c50e0]'],
            'tinggal'        => ['Tinggal',        'refresh',           'border-amber-400 bg-amber-50 text-amber-700'],
            'lulus'          => ['Lulus',          'workspace_premium', 'border-green-400 bg-green-50 text-green-700'],
            'pindah_paket'   => ['Pindah Paket',   'swap_horiz',        'border-purple-300 bg-purple-50 text-purple-700'],
            'pindah_wilayah' => ['Pindah Wilayah', 'swap_horiz',        'border-purple-300 bg-purple-50 text-purple-700'],
        ];
        $akademikMeta = [
            'aktif'  => ['Aktif',  'bg-blue-50 text-blue-700 border-blue-200',   'bg-blue-500'],
            'lulus'  => ['Lulus',  'bg-green-50 text-green-700 border-green-200', 'bg-green-500'],
            'pindah' => ['Pindah', 'bg-purple-50 text-purple-700 border-purple-200', 'bg-purple-500'],
            'keluar' => ['Keluar', 'bg-[#f0f4f8] text-[#757686] border-[#c5c5d7]', 'bg-[#757686]'],
        ];
    @endphp

    {{-- ── Heading + ringkasan ─────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-end gap-2 justify-between">
        <div>
            <h1 class="text-xl font-bold text-on-surface">Kenaikan Kelas</h1>
            <p class="text-sm text-[#505f76] mt-0.5">
                {{ $rombel?->nama ?? 'Wali Kelas' }}
                @if ($rombel?->periodeAjaran) · TA {{ $rombel->periodeAjaran->tahun_ajaran }} @endif
            </p>
        </div>
        @if ($rombel)
            <div class="flex gap-2 text-xs">
                <span class="px-2 py-1 bg-[#f0f4f8] border border-[#c5c5d7] rounded-lg text-[#505f76]">Total: <strong class="text-on-surface">{{ $total }}</strong> PD</span>
                <span class="px-2 py-1 bg-amber-50 border border-amber-200 rounded-lg text-amber-700">Belum diputuskan: <strong>{{ $belum }}</strong></span>
            </div>
        @endif
    </div>

    @if (! $rombel)
        {{-- Bukan wali kelas / tidak ada rombel --}}
        <div class="bg-white border border-[#c5c5d7] rounded-xl px-6 py-16 text-center">
            <span class="material-symbols-outlined text-[48px] text-[#c5c5d7] mb-3 block">trending_up</span>
            <p class="text-[15px] font-medium text-on-surface mb-1">Belum ada rombel</p>
            <p class="text-[13px] text-[#757686]">Anda bukan wali kelas di periode yang dipilih.</p>
        </div>
    @else
        {{-- Selektor rombel (jika wali >1 rombel) --}}
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

        {{-- Info banner --}}
        <div class="bg-[#EEF2FF] border border-[#3c50e0]/20 rounded-xl px-4 py-3 flex items-start gap-2.5 text-sm text-[#505f76]">
            <span class="material-symbols-outlined text-[18px] text-[#3c50e0] flex-shrink-0 mt-0.5">info</span>
            <p>Tetapkan keputusan tiap peserta didik. Status <strong>Naik Tingkat</strong>, <strong>Tinggal</strong>, atau <strong>Pindah</strong> akan masuk Ruang Tunggu untuk di-assign Admin ke rombel baru. <strong>Lulus</strong> tidak masuk Ruang Tunggu — akun tetap aktif namun status akademik menjadi Lulus.</p>
        </div>

        {{-- Card daftar keputusan --}}
        <div class="bg-white border border-[#c5c5d7] rounded-xl overflow-hidden">
            <div class="px-4 py-3 border-b border-[#c5c5d7] flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <p class="text-sm font-semibold text-on-surface">Peserta Didik</p>
                    <p class="text-xs text-[#757686]">Pilih keputusan pada kolom Keputusan</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                    <div class="relative w-full sm:w-auto">
                        <span class="material-symbols-outlined absolute left-2 top-1/2 -translate-y-1/2 text-[#757686] text-[18px] pointer-events-none">search</span>
                        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama…"
                               class="w-full sm:w-52 pl-9 pr-3 py-2 border border-[#c5c5d7] rounded-lg text-sm bg-white focus:outline-none focus:border-[#3c50e0] focus:ring-1 focus:ring-[#3c50e0]">
                    </div>
                    <button wire:click="markAllNaik"
                            class="flex items-center justify-center gap-1.5 px-3 py-2 border border-[#3c50e0]/30 bg-[#EEF2FF] text-[#3c50e0] text-xs font-medium rounded-lg hover:bg-[#e0e7ff] transition-colors whitespace-nowrap cursor-pointer">
                        <span class="material-symbols-outlined text-[16px]">done_all</span> Tandai sisa: Naik
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-white border-b border-[#c5c5d7]">
                            <th class="px-4 py-2.5 text-xs font-semibold text-[#505f76] uppercase tracking-wide w-10">No</th>
                            <th class="px-4 py-2.5 text-xs font-semibold text-[#505f76] uppercase tracking-wide">Nama Peserta Didik</th>
                            <th class="px-4 py-2.5 text-xs font-semibold text-[#505f76] uppercase tracking-wide">Status</th>
                            <th class="px-4 py-2.5 text-xs font-semibold text-[#505f76] uppercase tracking-wide w-52">Keputusan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#c5c5d7]">
                        @forelse ($peserta as $i => $pd)
                            @php
                                $d       = $decisions[$pd->id] ?? '';
                                $selCls  = $d && isset($statusMeta[$d]) ? $statusMeta[$d][2] : 'border-[#c5c5d7] bg-white text-[#505f76]';
                                $ak      = $akademikMeta[$pd->status_akademik] ?? $akademikMeta['aktif'];
                            @endphp
                            <tr class="hover:bg-[#f6fafe] transition-colors {{ $d === 'naik' ? 'bg-[#EEF2FF]/40' : '' }}">
                                <td class="px-4 py-3 text-[#757686] whitespace-nowrap">{{ $i + 1 }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-[#e4e9ed] text-[#757686] flex items-center justify-center text-xs font-bold flex-shrink-0">
                                            {{ strtoupper(\Illuminate\Support\Str::of($pd->nama_lengkap)->explode(' ')->take(2)->map(fn($w) => mb_substr($w, 0, 1))->join('')) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-on-surface">{{ $pd->nama_lengkap }}</p>
                                            <p class="text-xs text-[#757686]">NIPD: {{ $pd->nipd }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full border font-medium {{ $ak[1] }}">
                                        <span class="w-1.5 h-1.5 rounded-full flex-shrink-0 {{ $ak[2] }}"></span> {{ $ak[0] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <select wire:model.live="decisions.{{ $pd->id }}"
                                            class="w-full px-3 py-1.5 border-2 rounded-lg text-xs font-medium cursor-pointer focus:outline-none transition-colors {{ $selCls }}">
                                        <option value="">Belum dipilih</option>
                                        <option value="naik">Naik Tingkat</option>
                                        <option value="tinggal">Tinggal</option>
                                        <option value="lulus">Lulus</option>
                                        <option value="pindah_paket">Pindah Paket</option>
                                        <option value="pindah_wilayah">Pindah Wilayah</option>
                                    </select>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-16 text-center">
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

            <div class="px-4 py-3 border-t border-[#c5c5d7] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <p class="text-sm text-[#505f76]">
                    <strong class="text-on-surface">{{ $naikCount }}</strong> Naik Tingkat ·
                    <strong class="text-on-surface">{{ $diputuskan }}</strong> sudah diputuskan
                </p>
                <button wire:click="requestSave" @disabled($diputuskan === 0)
                        class="flex items-center justify-center gap-2 px-5 py-2 bg-[#3c50e0] text-white text-sm font-semibold rounded-lg hover:bg-[#2a3db0] transition-colors shadow-sm cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                    <span class="material-symbols-outlined text-[18px]">save</span> Simpan Keputusan
                </button>
            </div>
        </div>
    @endif

    {{-- ── Modal konfirmasi simpan (bila ada yang belum diputuskan) ─────────── --}}
    @if ($showConfirm)
        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/40 backdrop-blur-sm p-0 sm:p-4">
            <div class="bg-white w-full max-w-4xl sm:max-w-sm rounded-t-2xl sm:rounded-xl overflow-hidden shadow-xl p-6">
                <h3 class="text-base font-bold text-on-surface mb-2">Konfirmasi Keputusan</h3>
                <p class="text-sm text-[#505f76] mb-4">
                    Masih ada <strong>{{ $belum }} peserta didik</strong> belum diberi keputusan. Simpan keputusan yang sudah ada saja?
                </p>
                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2">
                    <button wire:click="$set('showConfirm', false)"
                            class="w-full sm:w-auto px-4 py-2 text-sm rounded-lg border border-[#c5c5d7] text-[#505f76] hover:bg-[#f0f4f8] cursor-pointer text-center">
                        Batal
                    </button>
                    <button wire:click="save"
                            class="w-full sm:w-auto px-4 py-2 text-sm rounded-lg bg-[#3c50e0] text-white hover:bg-[#2a3db0] font-medium cursor-pointer text-center">
                        Ya, Simpan
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
