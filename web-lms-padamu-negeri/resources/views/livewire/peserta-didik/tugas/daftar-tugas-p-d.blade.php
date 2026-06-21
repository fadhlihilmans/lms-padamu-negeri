<div>

    {{-- ── Header ─────────────────────────────────────────────────────────────── --}}
    <div class="mb-6">
        <h2 class="text-[24px] font-bold tracking-tight text-on-surface">Tugas</h2>
        <p class="text-[14px] text-[#505f76] mt-0.5">Daftar tugas yang diberikan guru untuk Anda</p>
    </div>

    {{-- ── Filter Bar ──────────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm p-4 mb-6 flex flex-col sm:flex-row gap-3 flex-wrap">
        <div class="flex-1 min-w-[200px] relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#505f76] text-[18px] pointer-events-none">search</span>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari judul tugas..."
                   class="w-full pl-10 pr-4 py-2.5 border border-[#c5c5d7] rounded-lg text-[14px] focus:outline-none focus:ring-1 focus:ring-[#3c50e0]">
        </div>
        @if ($mapels->isNotEmpty())
            <div class="sm:w-44">
                <select wire:model.live="filterMapel"
                        class="w-full border border-[#c5c5d7] rounded-lg px-3 py-2.5 text-[14px] focus:outline-none focus:ring-1 focus:ring-[#3c50e0] cursor-pointer">
                    <option value="">Semua Mapel</option>
                    @foreach ($mapels as $m)
                        <option value="{{ $m->id }}">{{ $m->nama }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        <div class="sm:w-44">
            <select wire:model.live="filterStatus"
                    class="w-full border border-[#c5c5d7] rounded-lg px-3 py-2.5 text-[14px] focus:outline-none focus:ring-1 focus:ring-[#3c50e0] cursor-pointer">
                <option value="">Semua Status</option>
                <option value="belum">Belum Dikerjakan</option>
                <option value="terlewat">Terlewat</option>
                <option value="tepat">Sudah Dikumpulkan</option>
                <option value="terlambat">Dikumpulkan Terlambat</option>
            </select>
        </div>
        <div class="flex-shrink-0">
            <select wire:model.live="perPage"
                    class="border border-[#c5c5d7] rounded-lg px-4 py-2.5 text-[14px] focus:outline-none focus:ring-1 focus:ring-[#3c50e0] cursor-pointer">
                <option value="15">15 / hal</option>
                <option value="30">30 / hal</option>
            </select>
        </div>
    </div>

    {{-- ── Empty States ──────────────────────────────────────────────────────── --}}
    @if ($rombels->isEmpty())
        <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm px-6 py-16 text-center">
            <span class="material-symbols-outlined text-[48px] text-[#c5c5d7] mb-3 block">groups</span>
            <p class="text-[15px] font-medium text-on-surface mb-1">Anda belum terdaftar di rombel aktif</p>
            <p class="text-[13px] text-[#505f76]">Hubungi Admin untuk mendaftarkan Anda ke rombel.</p>
        </div>

    @elseif ($tugas->isEmpty())
        <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm px-6 py-16 text-center">
            <span class="material-symbols-outlined text-[48px] text-[#c5c5d7] mb-3 block">assignment</span>
            <p class="text-[15px] font-medium text-on-surface mb-1">
                {{ $search || $filterMapel || $filterStatus ? 'Tidak ada tugas yang cocok' : 'Belum ada tugas' }}
            </p>
            <p class="text-[13px] text-[#505f76]">
                {{ $search || $filterMapel || $filterStatus ? 'Coba ubah filter.' : 'Guru belum memberikan tugas.' }}
            </p>
        </div>

    @else
        <div class="flex flex-col gap-3">
            @foreach ($tugas as $t)
                @php
                    $sub      = $mySubmisiByTugasId[$t->id] ?? null;
                    $isPast   = $t->deadline->isPast();
                    $isSudah  = $sub && $sub->waktu_submit->lte($t->deadline);
                    $isLate   = $sub && $sub->waktu_submit->gt($t->deadline);
                    $isMissed = ! $sub && $isPast;
                @endphp
                <a href="{{ route('peserta-didik.tugas.detail', $t->id) }}"
                   class="flex items-start gap-4 bg-white rounded-xl border border-[#c5c5d7] shadow-sm px-5 py-4 hover:bg-[#f6fafe] hover:border-[#3c50e0]/40 hover:shadow transition-all cursor-pointer group">

                    {{-- Status icon --}}
                    <div @class([
                        'w-11 h-11 rounded-xl flex-shrink-0 flex items-center justify-center mt-0.5 transition-colors',
                        'bg-[#d1f5e0]' => $isSudah,
                        'bg-[#ffdad6]' => $isMissed || $isLate,
                        'bg-[#fff3cd]' => ! $sub && ! $isPast,
                    ])>
                        <span @class([
                            'material-symbols-outlined text-[22px]',
                            'text-[#0d6e34]' => $isSudah,
                            'text-[#ba1a1a]' => $isMissed || $isLate,
                            'text-[#856404]' => ! $sub && ! $isPast,
                        ])>
                            {{ $isSudah ? 'check_circle' : ($isMissed ? 'cancel' : ($isLate ? 'history_toggle_off' : 'pending')) }}
                        </span>
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-3">
                            <h3 class="text-[15px] font-semibold text-on-surface group-hover:text-[#3c50e0] transition-colors line-clamp-1">
                                {{ $t->judul }}
                            </h3>
                            {{-- Status badge --}}
                            @if ($isSudah)
                                <span class="flex-shrink-0 text-[11px] font-semibold px-2.5 py-1 rounded-full bg-[#d1f5e0] text-[#0d6e34]">Sudah Dikumpulkan</span>
                            @elseif ($isLate)
                                <span class="flex-shrink-0 text-[11px] font-semibold px-2.5 py-1 rounded-full bg-[#ffdad6] text-[#ba1a1a]">Terlambat</span>
                            @elseif ($isMissed)
                                <span class="flex-shrink-0 text-[11px] font-semibold px-2.5 py-1 rounded-full bg-[#ffdad6] text-[#ba1a1a]">Terlewat</span>
                            @else
                                <span class="flex-shrink-0 text-[11px] font-semibold px-2.5 py-1 rounded-full bg-[#fff3cd] text-[#856404]">Belum Dikerjakan</span>
                            @endif
                        </div>

                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1.5 text-[12px] text-[#505f76]">
                            <span class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-[13px]">book</span>
                                {{ $t->guruMapelRombel?->mapel?->nama }}
                            </span>
                            <span class="flex items-center gap-1 {{ $isMissed ? 'text-[#ba1a1a] font-medium' : '' }}">
                                <span class="material-symbols-outlined text-[13px]">calendar_today</span>
                                Deadline: {{ $t->deadline->translatedFormat('d M Y, H:i') }}
                                @if (! $isPast)
                                    <span class="opacity-60">({{ $t->deadline->diffForHumans() }})</span>
                                @endif
                            </span>
                            @if ($sub && $sub->nilai !== null)
                                <span class="flex items-center gap-1 font-semibold text-[#3c50e0]">
                                    <span class="material-symbols-outlined text-[13px]">grade</span>
                                    Nilai: {{ $sub->nilai }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <span class="material-symbols-outlined text-[20px] text-[#505f76] group-hover:text-[#3c50e0] flex-shrink-0 transition-colors mt-1">chevron_right</span>
                </a>
            @endforeach
        </div>

        @if ($tugas->hasPages())
            <div class="mt-6">{{ $tugas->links() }}</div>
        @endif
    @endif

</div>
