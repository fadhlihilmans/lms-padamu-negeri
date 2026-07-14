<div class="max-w-5xl mx-auto">

    {{-- ── Back ───────────────────────────────────────────────────────────── --}}
    <div class="mb-5">
        <a href="{{ route('admin.master.rombel') }}"
           class="w-9 h-9 flex items-center justify-center rounded-lg border cursor-pointer transition-colors"
           style="border-color: #c5c5d7; background: white; color: #505f76"
           onmouseover="this.style.background='#f0f4f8'" onmouseout="this.style.background='white'">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        </a>
    </div>

    {{-- ── Header Card ─────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm p-6 mb-6">
        <div class="flex flex-col sm:flex-row items-start gap-5">
            <div class="w-14 h-14 rounded-2xl bg-[#EEF2FF] flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-[#3c50e0] text-[28px]">groups</span>
            </div>
            <div class="flex-1 min-w-0">
                <h1 class="text-[22px] font-bold text-on-surface mb-1.5">{{ $rombel->nama }}</h1>
                <div class="flex flex-wrap items-center gap-3 text-[13px] text-[#505f76]">
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">calendar_month</span>
                        TA {{ $rombel->tahun_ajaran }}
                    </span>
                    @if ($rombel->paket)
                        <span class="text-[#c5c5d7]">·</span>
                        <span class="flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">layers</span>
                            {{ $rombel->paket->nama }}
                        </span>
                    @endif
                    @if ($rombel->tingkat)
                        <span class="text-[#c5c5d7]">·</span>
                        <span class="flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">bar_chart</span>
                            {{ $rombel->tingkat->nama }}
                        </span>
                    @endif
                    @if ($rombel->wilayah)
                        <span class="text-[#c5c5d7]">·</span>
                        <span class="flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">location_on</span>
                            {{ $rombel->wilayah->nama }}
                        </span>
                    @endif
                </div>
            </div>
            <a href="{{ route('admin.master.rombel') }}"
               class="inline-flex items-center gap-2 px-4 py-2 text-[13px] font-medium text-[#505f76] border border-[#c5c5d7] rounded-lg hover:bg-[#f0f4f8] transition-colors cursor-pointer flex-shrink-0">
                <span class="material-symbols-outlined text-[16px]">list</span>
                Kelola Rombel
            </a>
        </div>
    </div>

    {{-- ── Stats ───────────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm p-4 text-center">
            <p class="text-[28px] font-bold text-[#3c50e0]">{{ $rombel->pesertaDidikRombel->count() }}</p>
            <p class="text-[12px] text-[#505f76]">Peserta Didik</p>
        </div>
        <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm p-4 text-center">
            <p class="text-[28px] font-bold text-on-surface">{{ $rombel->guruMapelRombel->count() }}</p>
            <p class="text-[12px] text-[#505f76]">Guru Mapel</p>
        </div>
        <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm p-4 text-center col-span-2 sm:col-span-1">
            <p class="text-[14px] font-semibold text-on-surface leading-tight mt-1">{{ $rombel->waliKelas?->nama_lengkap ?? '—' }}</p>
            <p class="text-[12px] text-[#505f76]">Wali Kelas</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- ── Daftar Peserta Didik ─────────────────────────────────────────── --}}
        <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-[#c5c5d7] bg-[#f6fafe]">
                <h2 class="text-[14px] font-semibold text-on-surface flex items-center gap-2">
                    <span class="material-symbols-outlined text-[16px] text-[#3c50e0]">person</span>
                    Daftar Peserta Didik
                    <span class="ml-auto text-[12px] font-normal text-[#757686]">{{ $rombel->pesertaDidikRombel->count() }} orang</span>
                </h2>
            </div>
            @if ($rombel->pesertaDidikRombel->isEmpty())
                <div class="px-5 py-10 text-center">
                    <span class="material-symbols-outlined text-[36px] text-[#c5c5d7] mb-2 block">person_off</span>
                    <p class="text-[13px] text-[#505f76]">Belum ada peserta didik di rombel ini.</p>
                </div>
            @else
                <div class="divide-y divide-[#f0f4f8] max-h-[420px] overflow-y-auto">
                    @foreach ($rombel->pesertaDidikRombel as $i => $pdr)
                        @php $pd = $pdr->pesertaDidik; @endphp
                        <div class="flex items-center gap-3 px-5 py-3 hover:bg-[#f6fafe] transition-colors">
                            <span class="text-[12px] text-[#757686] w-5 flex-shrink-0 text-right">{{ $i + 1 }}</span>
                            <div class="w-8 h-8 rounded-lg bg-[#EEF2FF] flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined text-[#3c50e0] text-[16px]">person</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-[13px] font-medium text-on-surface truncate">{{ $pd?->nama_lengkap ?? '—' }}</p>
                                <p class="text-[11px] text-[#505f76] font-mono">{{ $pd?->nipd }}</p>
                            </div>
                            @if ($pd)
                                <a href="{{ route('admin.pengguna.peserta-didik.show', $pd->id) }}"
                                   class="p-1 text-[#505f76] hover:text-[#3c50e0] hover:bg-[#EEF2FF] rounded transition-colors cursor-pointer flex-shrink-0"
                                   title="Lihat detail">
                                    <span class="material-symbols-outlined text-[16px]">open_in_new</span>
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ── Pemetaan Guru Mapel ───────────────────────────────────────────── --}}
        <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-[#c5c5d7] bg-[#f6fafe]">
                <h2 class="text-[14px] font-semibold text-on-surface flex items-center gap-2">
                    <span class="material-symbols-outlined text-[16px] text-[#3c50e0]">hub</span>
                    Pemetaan Guru Mapel
                    <span class="ml-auto text-[12px] font-normal text-[#757686]">{{ $rombel->guruMapelRombel->count() }} mapel</span>
                </h2>
            </div>
            @if ($rombel->guruMapelRombel->isEmpty())
                <div class="px-5 py-10 text-center">
                    <span class="material-symbols-outlined text-[36px] text-[#c5c5d7] mb-2 block">school</span>
                    <p class="text-[13px] text-[#505f76]">Belum ada pemetaan guru mapel.</p>
                </div>
            @else
                <div class="divide-y divide-[#f0f4f8] max-h-[420px] overflow-y-auto">
                    @foreach ($rombel->guruMapelRombel as $gmr)
                        <div class="flex items-center gap-3 px-5 py-3 hover:bg-[#f6fafe] transition-colors">
                            <div class="w-8 h-8 rounded-lg bg-[#f6fafe] border border-[#c5d0ff] flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined text-[#3c50e0] text-[16px]">book</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-[13px] font-medium text-on-surface truncate">{{ $gmr->mapel?->nama ?? '—' }}</p>
                                <p class="text-[11px] text-[#505f76] truncate">
                                    <span class="material-symbols-outlined text-[11px]">badge</span>
                                    {{ $gmr->guru?->nama_lengkap ?? '—' }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

</div>
