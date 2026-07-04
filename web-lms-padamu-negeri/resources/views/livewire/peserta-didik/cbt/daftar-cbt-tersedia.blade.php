<div class="w-full space-y-4">

    {{-- ── Header ─────────────────────────────────────────────────────────── --}}
    <div>
        <h1 class="text-[18px] font-bold text-on-surface">CBT (Ujian)</h1>
        <p class="text-[13px] text-[#757686] mt-0.5">Daftar ujian berbasis komputer untuk kamu kerjakan.</p>
    </div>

    {{-- ── Filter bar ─────────────────────────────────────────────────────── --}}
    <div class="bg-white border border-[#c5c5d7] rounded-xl p-4 flex flex-col sm:flex-row sm:flex-wrap gap-3">
        <div class="flex-1 relative min-w-0">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#757686] text-[18px] pointer-events-none">search</span>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama ujian…"
                   class="w-full pl-10 pr-4 py-2.5 border border-[#c5c5d7] rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-[#3c50e0]">
        </div>
        @if ($mapels->isNotEmpty())
            <select wire:model.live="filterMapel"
                    class="w-full sm:w-44 border border-[#c5c5d7] rounded-lg px-3 py-2.5 text-sm bg-white cursor-pointer focus:outline-none focus:ring-1 focus:ring-[#3c50e0]">
                <option value="">Semua Mapel</option>
                @foreach ($mapels as $m)
                    <option value="{{ $m->id }}">{{ $m->nama }}</option>
                @endforeach
            </select>
        @endif
        <select wire:model.live="filterStatus"
                class="w-full sm:w-44 border border-[#c5c5d7] rounded-lg px-3 py-2.5 text-sm bg-white cursor-pointer focus:outline-none focus:ring-1 focus:ring-[#3c50e0]">
            <option value="">Semua Status</option>
            <option value="berlangsung">Sedang Berlangsung</option>
            <option value="terjadwal">Terjadwal</option>
            <option value="sudah">Sudah Dikerjakan</option>
            <option value="terlewat">Terlewat</option>
        </select>
    </div>

    {{-- ── Cards ──────────────────────────────────────────────────────────── --}}
    @if ($cbt->isEmpty())
        <div class="bg-white border border-[#c5c5d7] rounded-xl px-6 py-16 text-center">
            <span class="material-symbols-outlined text-[48px] text-[#c5c5d7] mb-3 block">quiz</span>
            <p class="text-[15px] font-medium text-on-surface mb-1">
                {{ $search || $filterMapel || $filterStatus ? 'Tidak ada CBT yang cocok' : 'Belum ada CBT' }}
            </p>
            <p class="text-[13px] text-[#757686]">
                {{ $search || $filterMapel || $filterStatus ? 'Coba ubah pencarian / filter.' : 'Ujian akan muncul di sini saat guru menjadwalkannya.' }}
            </p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach ($cbt as $c)
                @php
                    $hasil   = $myHasil[$c->id] ?? null;
                    $mulai   = $c->tanggal_mulai;
                    $selesai = $c->tanggal_mulai?->copy()->addMinutes($c->durasi_menit);
                    $now     = now();

                    if ($hasil && $hasil->waktu_submit)      $state = 'sudah';
                    elseif ($now->lt($mulai))                $state = 'terjadwal';
                    elseif ($now->between($mulai, $selesai)) $state = 'berlangsung';
                    else                                     $state = 'terlewat';

                    $sedangKerja = $hasil && ! $hasil->waktu_submit; // sudah mulai tapi belum submit

                    $stateMeta = [
                        'berlangsung' => ['Sedang Berlangsung', 'bg-amber-50 text-amber-700', 'bg-amber-500 animate-pulse'],
                        'terjadwal'   => ['Terjadwal',          'bg-[#EEF2FF] text-[#3c50e0]', 'bg-[#3c50e0]'],
                        'sudah'       => ['Sudah Dikerjakan',   'bg-green-50 text-green-700',  'bg-green-500'],
                        'terlewat'    => ['Terlewat',           'bg-[#f0f4f8] text-[#757686]', 'bg-[#757686]'],
                    ][$state];

                    // Tampilkan nilai ke PD?
                    $showNilai = false;
                    $nilaiText = 'Menunggu penilaian';
                    if ($state === 'sudah' && $hasil) {
                        $autoVisible = $hasil->status_penilaian === 'selesai_dinilai'
                            || ($hasil->status_penilaian === 'otomatis' && $c->tampilkan_nilai_otomatis);
                        if ($hasil->nilai_akhir !== null && ($autoVisible || $hasil->nilai_ditampilkan)) {
                            $showNilai = true;
                        } elseif ($hasil->status_penilaian === 'menunggu_koreksi') {
                            $nilaiText = 'Menunggu koreksi guru';
                        }
                    }
                @endphp
                <div class="bg-white border border-[#c5c5d7] rounded-xl shadow-sm flex flex-col relative overflow-hidden hover:shadow-md transition-shadow">
                    <div class="absolute top-0 left-0 w-1 h-full {{ $state === 'berlangsung' ? 'bg-amber-500' : ($state === 'sudah' ? 'bg-green-500' : 'bg-[#3c50e0]') }}"></div>

                    <div class="p-5 pl-6 flex flex-col flex-1">
                        <div class="flex items-start justify-between gap-3 mb-2">
                            <h3 class="text-[15px] font-semibold text-on-surface line-clamp-2 flex-1 leading-snug">{{ $c->nama_ujian }}</h3>
                            <span class="flex-shrink-0 inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $stateMeta[1] }}">
                                <span class="w-1.5 h-1.5 rounded-full flex-shrink-0 {{ $stateMeta[2] }}"></span> {{ $stateMeta[0] }}
                            </span>
                        </div>

                        <p class="text-[12px] text-[#757686] mb-3">{{ $c->guruMapelRombel?->mapel?->nama }}</p>

                        <div class="space-y-1.5 text-[12px] text-[#757686] mb-4">
                            <div class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[14px]">event</span> {{ $mulai?->translatedFormat('d M Y, H:i') }}</div>
                            <div class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[14px]">timer</span> {{ $c->durasi_menit }} menit</div>
                            <div class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[14px]">help</span> {{ $c->soal_count }} soal
                                <span class="text-[#c5c5d7]">·</span>
                                <span class="material-symbols-outlined text-[14px]">flag</span> KKM {{ $c->kkm }}
                            </div>
                        </div>

                        <div class="mt-auto pt-4 border-t border-[#f0f4f8]">
                            @if ($state === 'berlangsung')
                                @if (Route::has('peserta-didik.cbt.kerjakan'))
                                    <a href="{{ route('peserta-didik.cbt.kerjakan', $c->id) }}" wire:navigate
                                       class="w-full flex items-center justify-center gap-1.5 px-3 py-2.5 text-[13px] font-semibold text-white bg-[#3c50e0] rounded-lg hover:bg-[#2a3db0] transition-colors cursor-pointer">
                                        <span class="material-symbols-outlined text-[16px]">{{ $sedangKerja ? 'resume' : 'play_arrow' }}</span>
                                        {{ $sedangKerja ? 'Lanjutkan Ujian' : 'Kerjakan Sekarang' }}
                                    </a>
                                @else
                                    <button type="button" disabled
                                            class="w-full flex items-center justify-center gap-1.5 px-3 py-2.5 text-[13px] font-semibold text-white bg-[#c5c5d7] rounded-lg cursor-not-allowed opacity-70">
                                        <span class="material-symbols-outlined text-[16px]">play_arrow</span> Kerjakan
                                    </button>
                                @endif
                            @elseif ($state === 'terjadwal')
                                <div class="flex items-center justify-center gap-1.5 px-3 py-2.5 text-[13px] font-medium text-[#505f76] bg-[#f0f4f8] rounded-lg">
                                    <span class="material-symbols-outlined text-[16px]">lock_clock</span>
                                    Dibuka {{ $mulai?->diffForHumans() }}
                                </div>
                            @elseif ($state === 'sudah')
                                @if ($showNilai)
                                    <div class="flex items-center justify-between px-3 py-2.5 rounded-lg {{ $hasil->nilai_akhir >= $c->kkm ? 'bg-green-50' : 'bg-red-50' }}">
                                        <span class="text-[12px] {{ $hasil->nilai_akhir >= $c->kkm ? 'text-green-700' : 'text-red-600' }}">Nilai Akhir</span>
                                        <span class="text-[18px] font-bold {{ $hasil->nilai_akhir >= $c->kkm ? 'text-green-600' : 'text-red-500' }}">{{ $hasil->nilai_akhir }}</span>
                                    </div>
                                @else
                                    <div class="flex items-center justify-center gap-1.5 px-3 py-2.5 text-[13px] font-medium text-[#505f76] bg-[#f0f4f8] rounded-lg">
                                        <span class="material-symbols-outlined text-[16px]">hourglass_top</span> {{ $nilaiText }}
                                    </div>
                                @endif
                            @else {{-- terlewat --}}
                                <div class="flex items-center justify-center gap-1.5 px-3 py-2.5 text-[13px] font-medium text-[#757686] bg-[#f0f4f8] rounded-lg">
                                    <span class="material-symbols-outlined text-[16px]">event_busy</span> Waktu ujian telah berakhir
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if ($cbt->hasPages())
            <div class="mt-2">{{ $cbt->links() }}</div>
        @endif
    @endif

</div>
