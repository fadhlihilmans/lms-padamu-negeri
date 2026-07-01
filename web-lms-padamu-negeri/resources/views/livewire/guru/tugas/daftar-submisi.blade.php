<div>

    {{-- ── Back + Header ───────────────────────────────────────────────────────── --}}
    <div class="mb-6">
        <a href="{{ route('guru.tugas') }}"
           class="inline-flex items-center gap-1.5 text-[13px] text-[#505f76] hover:text-[#3c50e0] transition-colors mb-4 cursor-pointer">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            Kembali ke Daftar Tugas
        </a>

        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
            <div>
                <h2 class="text-[22px] font-bold tracking-tight text-on-surface">{{ $tugas->judul }}</h2>
                <p class="text-[13px] text-[#757686] mt-0.5">
                    {{ $tugas->guruMapelRombel?->mapel?->nama }}
                    <span class="text-[#c5c5d7] mx-1">·</span>
                    {{ $tugas->guruMapelRombel?->rombel?->nama }}
                    <span class="text-[#c5c5d7] mx-1">·</span>
                    Deadline: {{ $tugas->deadline->translatedFormat('d M Y, H:i') }}
                    @if ($tugas->deadline->isPast())
                        <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full bg-[#ffdad6] text-[#ba1a1a] text-[11px] font-semibold ml-1">
                            Lewat Batas
                        </span>
                    @endif
                </p>
            </div>
        </div>
    </div>

    {{-- ── Statistik ────────────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm p-4 text-center">
            <p class="text-[28px] font-bold text-on-surface">{{ $stats['total'] }}</p>
            <p class="text-[12px] text-[#505f76]">Total PD</p>
        </div>
        <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm p-4 text-center">
            <p class="text-[28px] font-bold text-[#3c50e0]">{{ $stats['sudah'] }}</p>
            <p class="text-[12px] text-[#505f76]">Sudah Kumpul</p>
        </div>
        <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm p-4 text-center">
            <p class="text-[28px] font-bold text-[#1c8c4e]">{{ $stats['dinilai'] }}</p>
            <p class="text-[12px] text-[#505f76]">Sudah Dinilai</p>
        </div>
    </div>

    {{-- ── Tabel Submisi ────────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm overflow-hidden">

        {{-- Filter bar --}}
        <div class="px-5 py-3.5 border-b border-[#c5c5d7] bg-white flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#505f76] text-[17px] pointer-events-none">search</span>
                <input wire:model.live.debounce.300ms="search" type="text"
                       placeholder="Cari nama peserta didik..."
                       class="w-full pl-9 pr-4 py-2 border border-[#c5c5d7] rounded-lg text-[13px] focus:outline-none focus:ring-1 focus:ring-[#3c50e0] bg-white">
            </div>
            <select wire:model.live="filterStatus"
                    class="border border-[#c5c5d7] rounded-lg px-4 py-2 text-[13px] focus:outline-none focus:ring-1 focus:ring-[#3c50e0] cursor-pointer bg-white">
                <option value="">Semua Status</option>
                <option value="sudah">Sudah Mengumpulkan</option>
                <option value="terlambat">Terlambat</option>
                <option value="belum">Belum Mengumpulkan</option>
            </select>
        </div>

        @if ($pdList->isEmpty())
            <div class="px-6 py-12 text-center">
                <span class="material-symbols-outlined text-[40px] text-[#c5c5d7] mb-2 block">people</span>
                <p class="text-[14px] text-[#505f76]">
                    {{ $search || $filterStatus ? 'Tidak ada peserta didik yang sesuai filter.' : 'Belum ada peserta didik di rombel ini.' }}
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-white border-b border-[#c5c5d7]">
                            <th class="px-5 py-3 text-[11px] font-semibold text-[#757686] uppercase tracking-wider w-10">No</th>
                            <th class="px-5 py-3 text-[11px] font-semibold text-[#757686] uppercase tracking-wider">Nama Peserta Didik</th>
                            <th class="px-5 py-3 text-[11px] font-semibold text-[#757686] uppercase tracking-wider">Status</th>
                            <th class="px-5 py-3 text-[11px] font-semibold text-[#757686] uppercase tracking-wider">Waktu Submit</th>
                            <th class="px-5 py-3 text-[11px] font-semibold text-[#757686] uppercase tracking-wider w-28 text-center">Nilai</th>
                            <th class="px-5 py-3 text-[11px] font-semibold text-[#757686] uppercase tracking-wider text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#f0f4f8]">
                        @foreach ($pdList as $i => $pdr)
                            @php
                                $pd     = $pdr->pesertaDidik;
                                $sub    = $submisiByPdId[$pd?->id] ?? null;
                                $status = $this->resolveStatus($sub);
                            @endphp
                            <tr @class([
                                'hover:bg-[#f6fafe] transition-colors',
                                'bg-[#f6fafe]/40' => ! $sub,
                            ])>
                                <td class="px-5 py-3.5 text-[13px] text-[#505f76]">{{ $i + 1 }}</td>
                                <td class="px-5 py-3.5">
                                    <p class="text-[14px] font-medium text-on-surface">{{ $pd?->nama_lengkap }}</p>
                                    <p class="text-[12px] text-[#505f76]">{{ $pd?->nipd }}</p>
                                </td>
                                <td class="px-5 py-3.5">
                                    @if ($status === 'tepat')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-[#d1f5e0] text-[#0d6e34]">
                                            <span class="material-symbols-outlined text-[12px]">check_circle</span>Sudah Mengumpulkan
                                        </span>
                                    @elseif ($status === 'terlambat')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-[#ffdad6] text-[#ba1a1a]">
                                            <span class="material-symbols-outlined text-[12px]">history_toggle_off</span>Terlambat
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-[#f0f4f8] text-[#505f76] border border-[#c5c5d7]">
                                            <span class="material-symbols-outlined text-[12px]">schedule</span>Belum Mengumpulkan
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-[13px] text-[#505f76]">
                                    {{ $sub ? $sub->waktu_submit->translatedFormat('d M Y, H:i') : '—' }}
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    @if ($sub && $sub->nilai !== null)
                                        <span class="inline-block px-3 py-1 rounded-lg text-[15px] font-bold
                                            {{ $sub->nilai >= 75 ? 'bg-[#d1f5e0] text-[#0d6e34]' : ($sub->nilai >= 60 ? 'bg-[#fff3cd] text-[#856404]' : 'bg-[#ffdad6] text-[#ba1a1a]') }}">
                                            {{ $sub->nilai }}
                                        </span>
                                    @else
                                        <span class="text-[13px] text-[#c5c5d7]">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('guru.tugas.submisi.detail', ['tugasId' => $tugas->id, 'pdId' => $pd->id]) }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[12px] font-medium text-[#3c50e0] border border-[#c5d0ff] bg-[#EEF2FF] rounded-lg hover:bg-[#3c50e0] hover:text-white transition-colors cursor-pointer">
                                            <span class="material-symbols-outlined text-[14px]">visibility</span>
                                            Detail
                                        </a>
                                        @if ($sub)
                                            <button wire:click="openGradeModal({{ $sub->id }})"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[12px] font-medium text-white bg-[#3c50e0] rounded-lg hover:bg-[#2e3eb0] transition-colors cursor-pointer">
                                                <span class="material-symbols-outlined text-[14px]">grade</span>
                                                {{ $sub->nilai !== null ? 'Ubah' : 'Nilai' }}
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- ── Modal Beri Nilai ─────────────────────────────────────────────────────── --}}
    @if ($gradeTargetId)
        <div class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center bg-black/50 backdrop-blur-sm p-0 sm:p-4">
            <div class="bg-white rounded-t-2xl sm:rounded-xl shadow-xl shadow-black/10 w-full max-w-4xl sm:max-w-sm p-6 max-h-[90vh] sm:max-h-[85vh] overflow-y-auto">
                <div class="flex items-center gap-4 mb-5">
                    <div class="w-12 h-12 rounded-xl bg-[#EEF2FF] flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-[#3c50e0] text-[24px]">grade</span>
                    </div>
                    <div>
                        <h4 class="text-[16px] font-semibold text-on-surface">Beri Nilai</h4>
                        <p class="text-[13px] text-[#505f76]">Masukkan nilai 0 – 100</p>
                    </div>
                </div>

                <div class="mb-5">
                    <label class="block text-[13px] font-medium text-on-surface mb-2">Nilai</label>
                    <input wire:model="nilaiInput" type="number" min="0" max="100"
                           placeholder="0 – 100"
                           class="w-full border border-[#c5c5d7] rounded-xl px-4 py-3 text-[20px] font-bold text-center text-on-surface focus:outline-none focus:ring-2 focus:ring-[#3c50e0]/30 focus:border-[#3c50e0] transition-colors @error('nilaiInput') border-[#ba1a1a] @enderror"
                           x-ref="nilaiInput"
                           x-init="$nextTick(() => $refs.nilaiInput?.focus())">
                    @error('nilaiInput')
                        <p class="text-[12px] text-[#ba1a1a] mt-1.5 text-center">{{ $message }}</p>
                    @enderror

                    {{-- Visual grade indicator --}}
                    @if (is_numeric($nilaiInput) && $nilaiInput !== '')
                        @php $n = (int) $nilaiInput; @endphp
                        <div class="mt-3 flex items-center justify-center gap-2">
                            <span @class([
                                'px-4 py-1.5 rounded-full text-[13px] font-semibold',
                                'bg-[#d1f5e0] text-[#0d6e34]' => $n >= 75,
                                'bg-[#fff3cd] text-[#856404]' => $n >= 60 && $n < 75,
                                'bg-[#ffdad6] text-[#ba1a1a]' => $n < 60,
                            ])>
                                {{ $n >= 75 ? 'Tuntas' : ($n >= 60 ? 'Cukup' : 'Belum Tuntas') }}
                            </span>
                        </div>
                    @endif
                </div>

                <div class="flex gap-3">
                    <button wire:click="closeGradeModal"
                            class="flex-1 px-4 py-2.5 text-[14px] font-medium text-[#505f76] border border-[#c5c5d7] rounded-xl hover:bg-[#f0f4f8] transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button wire:click="saveNilai"
                            class="flex-1 px-4 py-2.5 text-[14px] font-semibold text-white bg-[#3c50e0] rounded-xl hover:bg-[#2e3eb0] transition-colors cursor-pointer">
                        Simpan Nilai
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
