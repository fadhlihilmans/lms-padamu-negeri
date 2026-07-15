<div class="max-w-4xl mx-auto">

    {{-- ── Back ───────────────────────────────────────────────────────────── --}}
    <div class="mb-5">
        <a href="{{ route('admin.pengguna.peserta-didik') }}"
           class="w-9 h-9 flex items-center justify-center rounded-lg border cursor-pointer transition-colors"
           style="border-color: #c5c5d7; background: white; color: #505f76"
           onmouseover="this.style.background='#f0f4f8'" onmouseout="this.style.background='white'">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        </a>
    </div>

    {{-- ── Header Card ─────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm p-4 sm:p-6 mb-6">
        <div class="flex flex-col sm:flex-row items-start gap-4 sm:gap-5">
            <div class="flex items-center gap-4 w-full sm:w-auto">
                <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-2xl bg-[#EEF2FF] flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-[#3c50e0] text-[24px] sm:text-[32px]">person</span>
                </div>
                <div class="min-w-0 flex-1 sm:hidden">
                    <h1 class="text-[17px] font-bold text-on-surface break-words">{{ $pesertaDidik->nama_lengkap }}</h1>
                </div>
            </div>
            <div class="flex-1 min-w-0 w-full">
                <h1 class="hidden sm:block text-[22px] font-bold text-on-surface mb-1">{{ $pesertaDidik->nama_lengkap }}</h1>
                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-[12.5px] sm:text-[13px] text-[#505f76] mb-3 mt-2 sm:mt-0">
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">badge</span>
                        NIPD: <span class="font-mono font-semibold text-on-surface ml-1">{{ $pesertaDidik->nipd }}</span>
                    </span>
                </div>
                <div class="flex flex-wrap gap-2">
                    @php
                        $statusColors = [
                            'aktif'  => 'bg-green-100 text-green-700',
                            'lulus'  => 'bg-blue-100 text-blue-700',
                            'pindah' => 'bg-amber-100 text-amber-700',
                            'keluar' => 'bg-red-100 text-red-700',
                        ];
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[12px] font-semibold whitespace-nowrap {{ $statusColors[$pesertaDidik->status_akademik] ?? 'bg-gray-100 text-gray-700' }}">
                       Peserta Didik {{ ucfirst($pesertaDidik->status_akademik) }}
                    </span>
                </div>
            </div>
            <a href="{{ route('admin.pengguna.peserta-didik') }}"
               class="inline-flex items-center justify-center gap-2 px-4 py-2.5 sm:py-2 text-[13px] font-medium text-[#505f76] border border-[#c5c5d7] rounded-lg hover:bg-[#f0f4f8] transition-colors cursor-pointer flex-shrink-0 w-full sm:w-auto">
                <span class="material-symbols-outlined text-[16px]">manage_accounts</span>
                Kelola Peserta Didik
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">

        {{-- ── Kolom Kiri: Data Pribadi ────────────────────────────────────── --}}
        <div class="lg:col-span-2 flex flex-col gap-4 sm:gap-6">

            @php
                $tidakAdaKartuLain = ! $pesertaDidik->alamat && $pesertaDidik->ortu->isEmpty();
            @endphp

            {{-- Data Pribadi --}}
            <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm p-4 sm:p-6 flex flex-col {{ $tidakAdaKartuLain ? 'flex-1' : '' }}">
                <h2 class="text-[13px] font-semibold text-[#757686] uppercase tracking-widest mb-4 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[14px]">person_pin</span>Data Pribadi
                </h2>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <p class="text-[12px] text-[#757686] mb-0.5">NIPD / NISN</p>
                        <p class="text-[14px] font-medium font-mono text-on-surface">{{ $pesertaDidik->nipd }} / {{ $pesertaDidik->nisn }}</p>
                    </div>
                    <div>
                        <p class="text-[12px] text-[#757686] mb-0.5">NIK</p>
                        <p class="text-[14px] font-medium font-mono text-on-surface">{{ $pesertaDidik->nik ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-[12px] text-[#757686] mb-0.5">Jenis Kelamin</p>
                        <p class="text-[14px] font-medium text-on-surface">
                            {{ $pesertaDidik->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-[12px] text-[#757686] mb-0.5">Tempat, tanggal Lahir</p>
                        <p class="text-[14px] font-medium text-on-surface">{{ $pesertaDidik->tempat_lahir ?? '—' }}, {{ $pesertaDidik->tanggal_lahir ? $pesertaDidik->tanggal_lahir->translatedFormat('d M Y') : '—' }}
                        </p></p>
                    </div>
                    <div>
                        <p class="text-[12px] text-[#757686] mb-0.5">Agama</p>
                        <p class="text-[14px] font-medium text-on-surface">{{ $pesertaDidik->agama ? ucfirst($pesertaDidik->agama) : '—' }}</p>
                    </div>
                    <div>
                        <p class="text-[12px] text-[#757686] mb-0.5">No. HP</p>
                        <p class="text-[14px] font-medium text-on-surface">{{ $pesertaDidik->no_hp ?? '—' }}</p>
                    </div>
                </div>
            </div>

            {{-- Alamat --}}
            @if ($pesertaDidik->alamat)
                @php $a = $pesertaDidik->alamat; @endphp
                <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm p-4 sm:p-6">
                    <h2 class="text-[13px] font-semibold text-[#757686] uppercase tracking-widest mb-4 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[14px]">location_on</span>Alamat
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @if ($a->jalan)
                            <div class="sm:col-span-2">
                                <p class="text-[12px] text-[#757686] mb-0.5">Jalan / Alamat</p>
                                <p class="text-[14px] font-medium text-on-surface">{{ $a->jalan }}</p>
                            </div>
                        @endif
                        @if ($a->rt || $a->rw)
                            <div>
                                <p class="text-[12px] text-[#757686] mb-0.5">RT / RW</p>
                                <p class="text-[14px] font-medium text-on-surface">{{ $a->rt ?? '—' }} / {{ $a->rw ?? '—' }}</p>
                            </div>
                        @endif
                        @if ($a->desa_kelurahan)
                            <div>
                                <p class="text-[12px] text-[#757686] mb-0.5">Desa / Kelurahan</p>
                                <p class="text-[14px] font-medium text-on-surface">{{ $a->desa_kelurahan }}</p>
                            </div>
                        @endif
                        @if ($a->kecamatan)
                            <div>
                                <p class="text-[12px] text-[#757686] mb-0.5">Kecamatan</p>
                                <p class="text-[14px] font-medium text-on-surface">{{ $a->kecamatan }}</p>
                            </div>
                        @endif
                        @if ($a->kota_kabupaten)
                            <div>
                                <p class="text-[12px] text-[#757686] mb-0.5">Kota / Kabupaten</p>
                                <p class="text-[14px] font-medium text-on-surface">{{ $a->kota_kabupaten }}</p>
                            </div>
                        @endif
                        @if ($a->provinsi)
                            <div>
                                <p class="text-[12px] text-[#757686] mb-0.5">Provinsi</p>
                                <p class="text-[14px] font-medium text-on-surface">{{ $a->provinsi }}</p>
                            </div>
                        @endif
                        @if ($a->kode_pos)
                            <div>
                                <p class="text-[12px] text-[#757686] mb-0.5">Kode Pos</p>
                                <p class="text-[14px] font-medium text-on-surface">{{ $a->kode_pos }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Data Orang Tua --}}
            @if ($pesertaDidik->ortu->isNotEmpty())
                <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm p-4 sm:p-6">
                    <h2 class="text-[13px] font-semibold text-[#757686] uppercase tracking-widest mb-4 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[14px]">family_restroom</span>Data Orang Tua / Wali
                    </h2>
                    <div class="flex flex-col gap-4">
                        @foreach ($pesertaDidik->ortu as $ortu)
                            <div class="p-4 bg-[#f6fafe] rounded-xl border border-[#e8ecf0]">
                                <p class="text-[12px] font-semibold text-[#3c50e0] mb-2 uppercase tracking-wide">{{ ucfirst($ortu->tipe ?? 'Wali') }}</p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <p class="text-[12px] text-[#757686]">Nama</p>
                                        <p class="text-[14px] font-medium text-on-surface">{{ $ortu->nama ?? '—' }}</p>
                                    </div>
                                    @if ($ortu->nik)
                                        <div>
                                            <p class="text-[12px] text-[#757686]">NIK</p>
                                            <p class="text-[14px] font-medium font-mono text-on-surface">{{ $ortu->nik }}</p>
                                        </div>
                                    @endif
                                    @if ($ortu->pekerjaan)
                                        <div>
                                            <p class="text-[12px] text-[#757686]">Pekerjaan</p>
                                            <p class="text-[14px] font-medium text-on-surface">{{ $ortu->pekerjaan }}</p>
                                        </div>
                                    @endif
                                    @if ($ortu->no_hp)
                                        <div>
                                            <p class="text-[12px] text-[#757686]">No. HP</p>
                                            <p class="text-[14px] font-medium text-on-surface">{{ $ortu->no_hp }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

        {{-- ── Kolom Kanan: Akun & Rombel ─────────────────────────────────── --}}
        <div class="flex flex-col gap-4 sm:gap-6">

            {{-- Info Akun --}}
            <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm p-4 sm:p-5">
                <h2 class="text-[13px] font-semibold text-[#757686] uppercase tracking-widest mb-3 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[14px]">manage_accounts</span>Akun Login
                </h2>
                <div class="space-y-2">
                    <div>
                        <p class="text-[12px] text-[#757686]">Username</p>
                        <p class="text-[14px] font-mono font-semibold text-on-surface">{{ $pesertaDidik->user?->username ?? $pesertaDidik->nipd }}</p>
                    </div>
                    <div>
                        <p class="text-[12px] text-[#757686]">Status Akun</p>
                        <span @class([
                            'inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[12px] font-semibold',
                            'bg-green-100 text-green-700' => $pesertaDidik->user?->is_active,
                            'bg-red-100 text-red-700' => ! $pesertaDidik->user?->is_active,
                        ])>
                            <span class="material-symbols-outlined text-[12px]">{{ $pesertaDidik->user?->is_active ? 'check_circle' : 'cancel' }}</span>
                            {{ $pesertaDidik->user?->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                    <div>
                        <p class="text-[12px] text-[#757686]">Terdaftar</p>
                        <p class="text-[13px] text-on-surface">{{ $pesertaDidik->created_at->translatedFormat('d M Y') }}</p>
                    </div>
                </div>
            </div>

            {{-- Riwayat Rombel --}}
            <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm p-4 sm:p-5">
                <h2 class="text-[13px] font-semibold text-[#757686] uppercase tracking-widest mb-3 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[14px]">groups</span>Riwayat Rombel
                </h2>
                @if ($pesertaDidik->pesertaDidikRombel->isEmpty())
                    <div class="text-center py-4">
                        <span class="material-symbols-outlined text-[32px] text-[#c5c5d7] mb-1 block">group_off</span>
                        <p class="text-[13px] text-[#505f76]">Belum terdaftar di rombel manapun.</p>
                    </div>
                @else
                    <div class="flex flex-col gap-2">
                        @foreach ($pesertaDidik->pesertaDidikRombel as $pdr)
                            <div class="p-3 rounded-lg border border-[#e8ecf0] bg-[#f6fafe]">
                                <p class="text-[13px] font-semibold text-on-surface">{{ $pdr->rombel?->nama ?? '—' }}</p>
                                <p class="text-[12px] text-[#505f76] mt-0.5">
                                    TA {{ $pdr->rombel?->tahun_ajaran ?? '—' }}
                                </p>
                                @if ($pdr->rombel?->paket || $pdr->rombel?->tingkat)
                                    <p class="text-[11px] text-[#757686] mt-0.5">
                                        {{ $pdr->rombel?->paket?->nama }}
                                        @if ($pdr->rombel?->paket && $pdr->rombel?->tingkat) · @endif
                                        {{ $pdr->rombel?->tingkat?->nama }}
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

    </div>

</div>
