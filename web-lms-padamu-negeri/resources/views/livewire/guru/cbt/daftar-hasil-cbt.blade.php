<div class="max-w-5xl mx-auto space-y-4">

    @php
        $jenisMap = [
            'pilihan_ganda' => 'PG',
            'uraian'        => 'Uraian',
            'campuran'      => 'Campuran',
        ];
        $jenisLabel = $jenisMap[$cbt->jenis_cbt] ?? '-';
    @endphp

    {{-- ── Header + tombol kembali ─────────────────────────────────────────── --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('guru.cbt') }}" wire:navigate
           class="w-9 h-9 flex items-center justify-center rounded-lg border border-[#c5c5d7] bg-white text-[#505f76] hover:bg-[#f0f4f8] transition-colors flex-shrink-0 cursor-pointer">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        </a>
        <div>
            <h1 class="text-[18px] font-bold text-on-surface">Hasil CBT</h1>
            <p class="text-[13px] text-[#757686] mt-0.5">Rekap nilai &amp; status penilaian peserta didik.</p>
        </div>
    </div>

    {{-- ── CBT info + stat cards ───────────────────────────────────────────── --}}
    <div class="bg-white border border-[#c5c5d7] rounded-xl p-4">
        <div class="flex flex-col sm:flex-row sm:items-center gap-2 justify-between mb-4">
            <div>
                <h2 class="text-lg font-bold text-on-surface">{{ $cbt->nama_ujian }}</h2>
                <div class="flex flex-wrap gap-3 mt-1 text-xs text-[#757686]">
                    <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">event</span> {{ $cbt->tanggal_mulai->translatedFormat('d M Y, H:i') }}</span>
                    <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">timer</span> {{ $cbt->durasi_menit }} menit</span>
                    <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">grade</span> KKM {{ $kkm }}</span>
                    <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">quiz</span> {{ $cbt->soal()->count() }} soal ({{ $jenisLabel }})</span>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @unless ($cbt->tampilkan_nilai_otomatis)
                    <button wire:click="showAllNilai"
                            class="flex items-center gap-1.5 px-3 py-2 rounded-lg border border-[#3c50e0]/30 bg-[#EEF2FF] text-[#3c50e0] text-xs font-medium hover:bg-[#e0e7ff] transition-colors whitespace-nowrap cursor-pointer"
                            title="Tampilkan semua nilai final ke peserta didik">
                        <span class="material-symbols-outlined text-[16px]">visibility</span> Tampilkan Semua Nilai
                    </button>
                @endunless
                @if ($menungguCount > 0 && Route::has('guru.cbt.koreksi'))
                    <a href="{{ route('guru.cbt.koreksi', $cbt->id) }}" wire:navigate
                       class="flex items-center gap-1.5 px-3 py-2 rounded-lg border border-amber-300 bg-amber-50 text-amber-700 text-xs font-medium hover:bg-amber-100 transition-colors whitespace-nowrap cursor-pointer">
                        <span class="material-symbols-outlined text-[16px]">rate_review</span> Koreksi Uraian ({{ $menungguCount }} menunggu)
                    </a>
                @elseif ($menungguCount > 0)
                    <span class="flex items-center gap-1.5 px-3 py-2 rounded-lg border border-amber-300 bg-amber-50 text-amber-700 text-xs font-medium whitespace-nowrap">
                        <span class="material-symbols-outlined text-[16px]">pending</span> {{ $menungguCount }} menunggu koreksi
                    </span>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div class="bg-[#f0f4f8] rounded-xl p-3 text-center">
                <p class="text-2xl font-bold text-[#3c50e0]">{{ $stats['rata'] ?? '–' }}</p>
                <p class="text-xs text-[#757686] mt-0.5">Rata-rata</p>
            </div>
            <div class="bg-green-50 rounded-xl p-3 text-center">
                <p class="text-2xl font-bold text-green-600">{{ $stats['tertinggi'] ?? '–' }}</p>
                <p class="text-xs text-[#757686] mt-0.5">Tertinggi</p>
            </div>
            <div class="bg-red-50 rounded-xl p-3 text-center">
                <p class="text-2xl font-bold text-red-500">{{ $stats['terendah'] ?? '–' }}</p>
                <p class="text-xs text-[#757686] mt-0.5">Terendah</p>
            </div>
            <div class="bg-[#EEF2FF] rounded-xl p-3 text-center">
                <p class="text-2xl font-bold text-[#3c50e0]">{{ $stats['lulus'] }}<span class="text-2xl font-bold text-[#757686]">/{{ $stats['finalized'] }}</span></p>
                <p class="text-xs text-[#757686] mt-0.5">Lulus KKM</p>
            </div>
        </div>
    </div>

    {{-- ── Toolbar ─────────────────────────────────────────────────────────── --}}
    <div class="bg-white border border-[#c5c5d7] rounded-xl px-4 py-3 flex flex-col sm:flex-row sm:flex-wrap gap-2 sm:items-center sm:justify-between">
        <div class="flex flex-col sm:flex-row gap-2 sm:items-center w-full sm:w-auto">
            <div class="relative w-full sm:w-auto">
                <span class="material-symbols-outlined absolute left-2 top-1/2 -translate-y-1/2 text-[#757686] text-[18px] pointer-events-none">search</span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama peserta didik…"
                       class="w-full sm:w-[220px] pl-9 pr-3 py-2 border border-[#c5c5d7] rounded-md text-sm bg-white focus:outline-none focus:border-[#3c50e0] focus:ring-2 focus:ring-[#3c50e0]/15">
            </div>
            <select wire:model.live="statusFilter"
                    class="w-full sm:w-auto px-3 py-2 border border-[#c5c5d7] rounded-lg text-sm bg-white text-[#505f76] cursor-pointer focus:outline-none focus:border-[#3c50e0]">
                <option value="">Semua Status</option>
                <option value="lulus">Lulus KKM</option>
                <option value="tidak_lulus">Tidak Lulus</option>
                <option value="menunggu">Menunggu Koreksi</option>
                <option value="belum">Belum Mengerjakan</option>
            </select>
        </div>
        <div class="flex items-center gap-1.5 text-sm text-[#505f76] w-full sm:w-auto justify-between sm:justify-start">
            Tampilkan
            <select wire:model.live="perPage"
                    class="px-2 py-1.5 border border-[#c5c5d7] rounded-md text-sm bg-white cursor-pointer focus:outline-none focus:border-[#3c50e0]">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
            Data
        </div>
    </div>

    {{-- ── Table ───────────────────────────────────────────────────────────── --}}
    <div class="bg-white border border-[#c5c5d7] rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-white border-b border-[#c5c5d7]">
                        <th class="px-4 py-3 text-xs font-semibold text-[#505f76] uppercase tracking-wide w-10">No</th>
                        <th class="px-4 py-3 text-xs font-semibold text-[#505f76] uppercase tracking-wide">Nama Peserta Didik</th>
                        <th class="px-4 py-3 text-xs font-semibold text-[#505f76] uppercase tracking-wide text-center">Skor PG</th>
                        <th class="px-4 py-3 text-xs font-semibold text-[#505f76] uppercase tracking-wide text-center">Skor Uraian</th>
                        <th class="px-4 py-3 text-xs font-semibold text-[#505f76] uppercase tracking-wide text-center">Nilai Akhir</th>
                        <th class="px-4 py-3 text-xs font-semibold text-[#505f76] uppercase tracking-wide">Status</th>
                        <th class="px-4 py-3 text-xs font-semibold text-[#505f76] uppercase tracking-wide">Penilaian</th>
                        <th class="px-4 py-3 text-xs font-semibold text-[#505f76] uppercase tracking-wide text-center">Tampilkan Nilai ke PD</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#c5c5d7]">
                    @forelse ($peserta as $i => $pd)
                        @php
                            $sudah    = $pd->hasil_id !== null;
                            $menunggu = $pd->status_penilaian === 'menunggu_koreksi';
                            $lulus    = $sudah && $pd->nilai_akhir !== null && $pd->nilai_akhir >= $kkm;
                        @endphp
                        <tr class="hover:bg-[#f6fafe] transition-colors">
                            <td class="px-4 py-3 text-[#757686] whitespace-nowrap">{{ $peserta->firstItem() + $i }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <p class="font-medium text-on-surface">{{ $pd->nama_lengkap }}</p>
                                <p class="text-xs text-[#757686]">NIPD: {{ $pd->nipd }}</p>
                            </td>

                            @if (! $sudah)
                                <td class="px-4 py-3 text-center text-[#c5c5d7] italic">—</td>
                                <td class="px-4 py-3 text-center text-[#c5c5d7] italic">—</td>
                                <td class="px-4 py-3 text-center text-[#c5c5d7] italic">—</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full bg-[#f0f4f8] text-[#757686] font-medium whitespace-nowrap">
                                        <span class="material-symbols-outlined text-[12px]">schedule</span> Belum Mengerjakan
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-[#c5c5d7] italic text-xs">—</td>
                            @else
                                <td class="px-4 py-3 text-center font-medium">{{ $pd->nilai_pg ?? '—' }}</td>
                                <td class="px-4 py-3 text-center @if ($menunggu || $pd->nilai_uraian === null) text-[#c5c5d7] italic @else font-medium @endif">
                                    {{ $menunggu ? '—' : ($pd->nilai_uraian ?? '—') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if ($menunggu || $pd->nilai_akhir === null)
                                        <span class="text-[#c5c5d7] italic">—</span>
                                    @else
                                        <span class="text-lg font-bold {{ $lulus ? 'text-green-600' : 'text-red-500' }}">{{ $pd->nilai_akhir }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if ($menunggu)
                                        <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 font-medium whitespace-nowrap">
                                            <span class="material-symbols-outlined text-[12px]">pending</span> Menunggu Koreksi
                                        </span>
                                    @elseif ($lulus)
                                        <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full bg-green-50 text-green-700 font-medium whitespace-nowrap">
                                            <span class="material-symbols-outlined text-[12px] filled">check_circle</span> Lulus
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full bg-red-50 text-red-600 font-medium whitespace-nowrap">
                                            <span class="material-symbols-outlined text-[12px]">cancel</span> Tidak Lulus
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if ($menunggu)
                                        @if (Route::has('guru.cbt.koreksi'))
                                            <a href="{{ route('guru.cbt.koreksi', $cbt->id) }}" wire:navigate class="text-xs text-[#3c50e0] underline hover:text-[#2a3db0] cursor-pointer">Koreksi Sekarang</a>
                                        @else
                                            <span class="text-xs text-amber-700">Menunggu koreksi</span>
                                        @endif
                                    @elseif ($pd->status_penilaian === 'otomatis')
                                        <span class="text-xs text-[#505f76]">Nilai otomatis</span>
                                    @else
                                        <span class="text-xs text-green-700 font-medium">Selesai dikoreksi</span>
                                    @endif
                                </td>
                            @endif

                            {{-- Tampil ke PD --}}
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                @php
                                    $finalized   = $sudah && $pd->nilai_akhir !== null;
                                    $autoVisible = $finalized && ($pd->status_penilaian === 'selesai_dinilai' || ($pd->status_penilaian === 'otomatis' && $cbt->tampilkan_nilai_otomatis));
                                @endphp
                                @if (! $finalized)
                                    <span class="text-[#c5c5d7] italic text-xs">—</span>
                                @elseif ($autoVisible)
                                    <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full bg-green-50 text-green-700 font-medium">
                                        <span class="material-symbols-outlined text-[12px]">visibility</span> Tampil
                                    </span>
                                @else
                                    <button wire:click="toggleNilai({{ $pd->hasil_id }})"
                                            class="inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-lg border transition-colors cursor-pointer {{ $pd->nilai_ditampilkan ? 'border-green-300 bg-green-50 text-green-700 hover:bg-green-100' : 'border-[#c5c5d7] bg-white text-[#505f76] hover:bg-[#f0f4f8]' }}">
                                        <span class="material-symbols-outlined text-[14px]">{{ $pd->nilai_ditampilkan ? 'visibility' : 'visibility_off' }}</span>
                                        {{ $pd->nilai_ditampilkan ? 'Ditampilkan' : 'Tampilkan' }}
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-16 text-center">
                                <span class="material-symbols-outlined text-[48px] text-[#c5c5d7] mb-3 block">groups</span>
                                <p class="text-[15px] font-medium text-on-surface mb-1">
                                    {{ $search || $statusFilter ? 'Tidak ada peserta didik yang cocok' : 'Belum ada peserta didik di rombel ini' }}
                                </p>
                                <p class="text-[13px] text-[#757686]">
                                    {{ $search || $statusFilter ? 'Coba ubah pencarian / filter.' : 'Hubungi Admin untuk menambahkan anggota rombel.' }}
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-[#c5c5d7] flex flex-col sm:flex-row items-center justify-between gap-3 text-sm text-[#505f76]">
            <span>Menampilkan {{ $peserta->firstItem() ?? 0 }}–{{ $peserta->lastItem() ?? 0 }} dari {{ $peserta->total() }} peserta didik</span>
            @if ($peserta->hasPages())
                <div>{{ $peserta->links() }}</div>
            @endif
        </div>
    </div>

</div>
