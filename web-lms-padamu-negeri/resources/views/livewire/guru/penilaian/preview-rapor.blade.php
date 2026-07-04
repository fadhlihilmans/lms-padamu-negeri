<div class="space-y-4">

    @php
        $semLabel = fn ($s) => $s === 'genap' ? 'Genap (2)' : 'Ganjil (1)';
    @endphp

    {{-- ── Toolbar ─────────────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center gap-3 justify-between">
        <div>
            <h1 class="text-xl font-bold text-on-surface">Pratinjau Rapor</h1>
            <p class="text-sm text-[#505f76] mt-0.5">{{ $data ? ($data['pd']?->nama_lengkap ?? '') : 'Rapor peserta didik' }}</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            @if ($rombels->count() > 1)
                <select wire:model.live="rombelId"
                        class="px-3 py-2 border border-[#c5c5d7] rounded-lg text-sm bg-white cursor-pointer focus:outline-none focus:border-[#3c50e0]">
                    @foreach ($rombels as $r)
                        <option value="{{ $r->id }}">{{ $r->nama }}</option>
                    @endforeach
                </select>
            @endif
            @if ($peserta->isNotEmpty())
                <select wire:model.live="pesertaDidikId"
                        class="px-3 py-2 border border-[#c5c5d7] rounded-lg text-sm bg-white cursor-pointer focus:outline-none focus:border-[#3c50e0] sm:w-56">
                    @foreach ($peserta as $pd)
                        <option value="{{ $pd->id }}">{{ $pd->nama_lengkap }}</option>
                    @endforeach
                </select>
            @endif
            @if ($data && ! $lockedForPd)
                <button wire:click="downloadPdf" wire:loading.attr="disabled"
                        class="flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg bg-[#3c50e0] text-white text-sm font-medium hover:bg-[#2a3db0] transition-colors cursor-pointer whitespace-nowrap">
                    <span wire:loading wire:target="downloadPdf" class="material-symbols-outlined text-[18px] animate-spin">progress_activity</span>
                    <span wire:loading.remove wire:target="downloadPdf" class="material-symbols-outlined text-[18px]">print</span>
                    Cetak PDF
                </button>
            @endif
        </div>
    </div>

    @if ($lockedForPd)
        <div class="bg-white border border-[#c5c5d7] rounded-xl px-6 py-16 text-center">
            <span class="material-symbols-outlined text-[48px] text-[#c5c5d7] mb-3 block">hourglass_top</span>
            <p class="text-[15px] font-medium text-on-surface mb-1">Rapor belum diterbitkan</p>
            <p class="text-[13px] text-[#757686]">Rapor akan tampil setelah Wali Kelas menerbitkannya.</p>
        </div>
    @elseif (! $data)
        <div class="bg-white border border-[#c5c5d7] rounded-xl px-6 py-16 text-center">
            <span class="material-symbols-outlined text-[48px] text-[#c5c5d7] mb-3 block">grade</span>
            <p class="text-[15px] font-medium text-on-surface mb-1">Rapor belum tersedia</p>
            <p class="text-[13px] text-[#757686]">Belum ada nilai yang diinput untuk peserta didik ini pada periode berjalan.</p>
        </div>
    @else
        {{-- Note mobile --}}
        <div class="lg:hidden bg-amber-50 border border-amber-300 rounded-xl px-4 py-3 flex items-start gap-2.5 text-sm text-amber-800">
            <span class="material-symbols-outlined text-[18px] text-amber-600 flex-shrink-0 mt-0.5">info</span>
            <p>Pratinjau lebih optimal di <strong>mode desktop</strong>. Gunakan <strong>Cetak PDF</strong> untuk hasil final.</p>
        </div>

        {{-- A4 --}}
        <div class="overflow-x-auto -mx-4 px-4 lg:mx-0 lg:px-0">
            <div class="bg-white shadow-md rounded-sm border border-[#c5c5d7] px-8 py-10 sm:px-14 sm:py-14 mx-auto" style="width:100%;max-width:794px;min-width:560px">

                {{-- Kop --}}
                <div class="flex items-center gap-4 pb-4 mb-6" style="border-bottom:3px double #171c1f">
                    <div class="w-16 h-16 flex-shrink-0 bg-[#f0f4f8] border border-[#c5c5d7] rounded-full flex items-center justify-center overflow-hidden">
                        @if ($data['kop']['logo_kab'])
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($data['kop']['logo_kab']) }}" class="w-full h-full object-contain" alt="Logo Kabupaten">
                        @else
                            <span class="material-symbols-outlined text-[28px] text-[#757686]">account_balance</span>
                        @endif
                    </div>
                    <div class="flex-1 text-center">
                        <p class="text-xs font-semibold uppercase tracking-wide">{{ $data['kop']['kabupaten'] }}</p>
                        <p class="text-xs font-semibold uppercase">{{ $data['kop']['dinas'] }}</p>
                        <h2 class="text-lg font-bold uppercase tracking-tight mt-1">{{ $data['kop']['nama_pkbm'] }}</h2>
                        <p class="text-[11px] text-[#505f76] mt-0.5">{{ $data['kop']['alamat'] }}</p>
                        <p class="text-[11px] text-[#505f76]">
                            @if ($data['kop']['telepon']) Telp: {{ $data['kop']['telepon'] }} @endif
                            @if ($data['kop']['telepon'] && $data['kop']['email']) · @endif
                            @if ($data['kop']['email']) Email: {{ $data['kop']['email'] }} @endif
                        </p>
                    </div>
                    <div class="w-16 h-16 flex-shrink-0 bg-[#f0f4f8] border border-[#c5c5d7] rounded-full flex items-center justify-center overflow-hidden">
                        @if ($data['kop']['logo_pkbm'])
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($data['kop']['logo_pkbm']) }}" class="w-full h-full object-contain" alt="Logo PKBM">
                        @else
                            <span class="material-symbols-outlined text-[28px] text-[#757686]">school</span>
                        @endif
                    </div>
                </div>

                {{-- Judul --}}
                <div class="text-center mb-8">
                    <h1 class="text-base font-bold uppercase border-b-2 border-[#171c1f] inline-block pb-1 tracking-wide">Laporan Hasil Belajar Peserta Didik</h1>
                </div>

                {{-- Identitas --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-10 gap-y-2 mb-8 text-sm">
                    <div class="flex"><span class="w-40 font-medium">Nama Peserta Didik</span><span class="mr-2">:</span><span class="font-semibold uppercase">{{ $data['pd']?->nama_lengkap }}</span></div>
                    <div class="flex"><span class="w-40 font-medium">Kelas / Rombel</span><span class="mr-2">:</span><span>{{ $data['rombel']?->nama }}</span></div>
                    <div class="flex"><span class="w-40 font-medium">NIPD / NISN</span><span class="mr-2">:</span><span>{{ $data['pd']?->nipd }} / {{ $data['pd']?->nisn ?: '—' }}</span></div>
                    <div class="flex"><span class="w-40 font-medium">Semester</span><span class="mr-2">:</span><span>{{ $semLabel($data['periode']?->semester) }}</span></div>
                    <div class="flex"><span class="w-40 font-medium">Tahun Pelajaran</span><span class="mr-2">:</span><span>{{ $data['periode']?->tahun_ajaran }}</span></div>
                </div>

                {{-- Tabel nilai --}}
                <div class="mb-8 overflow-x-auto">
                    <table class="w-full text-left border-collapse border border-[#171c1f] text-sm" style="min-width:500px">
                        <thead>
                            <tr>
                                <th class="border border-[#171c1f] p-2.5 text-center w-10">No</th>
                                <th class="border border-[#171c1f] p-2.5">Mata Pelajaran</th>
                                <th class="border border-[#171c1f] p-2.5 text-center w-24">Nilai Angka</th>
                                <th class="border border-[#171c1f] p-2.5 text-center w-20">Predikat</th>
                                <th class="border border-[#171c1f] p-2.5">Deskripsi Kemajuan Belajar</th>
                            </tr>
                        </thead>
                        <tbody class="align-top">
                            @forelse ($data['rows'] as $i => $row)
                                <tr>
                                    <td class="border border-[#171c1f] p-2.5 text-center">{{ $i + 1 }}</td>
                                    <td class="border border-[#171c1f] p-2.5">{{ $row['mapel'] }}</td>
                                    <td class="border border-[#171c1f] p-2.5 text-center">{{ $row['nilai'] ?? '—' }}</td>
                                    <td class="border border-[#171c1f] p-2.5 text-center font-semibold">{{ $row['grade'] ?? '—' }}</td>
                                    <td class="border border-[#171c1f] p-2.5 text-xs text-[#505f76]">{{ $row['deskripsi'] ?: '—' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="border border-[#171c1f] p-4 text-center text-[#757686] text-xs">Belum ada nilai mata pelajaran.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Catatan wali --}}
                <div class="border border-[#171c1f] rounded p-4 mb-14 min-h-[96px]">
                    <p class="text-sm font-semibold mb-2">Catatan Wali Kelas:</p>
                    <p class="text-sm text-[#505f76] italic">{{ $data['catatanWali'] ? '"' . $data['catatanWali'] . '"' : '—' }}</p>
                </div>

                {{-- TTD --}}
                <div class="grid grid-cols-3 gap-6 text-center text-sm">
                    <div>
                        <p class="mb-16">Mengetahui,<br>Orang Tua/Wali</p>
                        <div class="border-b border-[#171c1f]"></div>
                        <p class="mt-1 font-semibold">&nbsp;</p>
                    </div>
                    <div>
                        <p class="mb-1">{{ now()->translatedFormat('d F Y') }}</p>
                        <p class="mb-14">Wali Kelas</p>
                        <div class="border-b border-[#171c1f]"></div>
                        <p class="mt-1 font-semibold">{{ $data['waliKelas']?->nama_lengkap ?? '(...................)' }}</p>
                        <p class="text-xs text-[#757686]">{{ $data['waliKelas']?->nip ? 'NIP. ' . $data['waliKelas']->nip : '' }}</p>
                    </div>
                    <div>
                        <p class="mb-16">Mengetahui,<br>Kepala PKBM</p>
                        <div class="border-b border-[#171c1f]"></div>
                        <p class="mt-1 font-semibold">{{ $data['kop']['kepala_nama'] ?: '(...................)' }}</p>
                        <p class="text-xs text-[#757686]">{{ $data['kop']['kepala_nip'] ? 'NIP. ' . $data['kop']['kepala_nip'] : '' }}</p>
                    </div>
                </div>

            </div>
        </div>
    @endif

</div>
