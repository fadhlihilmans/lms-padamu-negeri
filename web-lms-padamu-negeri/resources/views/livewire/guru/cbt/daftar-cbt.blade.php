<div>

    {{-- ── Header ─────────────────────────────────────────────────────────────── --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-[24px] font-bold tracking-tight text-on-surface">CBT</h2>
            <p class="text-[14px] text-[#505f76] mt-0.5">Kelola ujian berbasis komputer untuk peserta didik</p>
        </div>
        @if (Route::has('guru.cbt.create'))
            <a href="{{ route('guru.cbt.create') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#3c50e0] text-white text-[14px] font-semibold rounded-xl hover:bg-[#2e3eb0] transition-colors shadow-sm cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Buat CBT
            </a>
        @else
            <button type="button" disabled title="Form CBT belum tersedia"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#c5c5d7] text-white text-[14px] font-semibold rounded-xl shadow-sm cursor-not-allowed opacity-70">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Buat CBT
            </button>
        @endif
    </div>

    {{-- ── Filter Bar ──────────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm p-4 mb-6 flex flex-col sm:flex-row gap-3">
        <div class="flex-1 relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#505f76] text-[18px] pointer-events-none">search</span>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama ujian..."
                   class="w-full pl-10 pr-4 py-2.5 border border-[#c5c5d7] rounded-lg text-[14px] focus:outline-none focus:ring-1 focus:ring-[#3c50e0]">
        </div>
        @if ($gmrList->isNotEmpty())
            <div class="sm:w-56">
                <select wire:model.live="gmrId"
                        class="w-full border border-[#c5c5d7] rounded-lg px-4 py-2.5 text-[14px] focus:outline-none focus:ring-1 focus:ring-[#3c50e0] cursor-pointer">
                    <option value="">Semua Mapel</option>
                    @foreach ($gmrList as $gmr)
                        <option value="{{ $gmr->id }}">{{ $gmr->mapel?->nama }} — {{ $gmr->rombel?->nama }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        <div class="flex-shrink-0">
            <select wire:model.live="perPage"
                    class="border border-[#c5c5d7] rounded-lg px-4 py-2.5 text-[14px] focus:outline-none focus:ring-1 focus:ring-[#3c50e0] cursor-pointer">
                <option value="12">12 / hal</option>
                <option value="24">24 / hal</option>
                <option value="48">48 / hal</option>
            </select>
        </div>
    </div>

    {{-- ── Empty States ──────────────────────────────────────────────────────── --}}
    @if ($gmrList->isEmpty())
        <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm px-6 py-16 text-center">
            <span class="material-symbols-outlined text-[48px] text-[#c5c5d7] mb-3 block">school</span>
            <p class="text-[15px] font-medium text-on-surface mb-1">Belum ada pemetaan mapel</p>
            <p class="text-[13px] text-[#505f76]">Hubungi Admin untuk menambahkan pemetaan mengajar Anda.</p>
        </div>

    @elseif ($cbt->isEmpty())
        <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm px-6 py-16 text-center">
            <span class="material-symbols-outlined text-[48px] text-[#c5c5d7] mb-3 block">quiz</span>
            <p class="text-[15px] font-medium text-on-surface mb-1">
                {{ $search ? 'Tidak ada CBT yang cocok' : 'Belum ada CBT' }}
            </p>
            <p class="text-[13px] text-[#505f76]">
                {{ $search ? 'Coba ubah kata kunci pencarian.' : 'Klik "Buat CBT" untuk mulai menambahkan ujian.' }}
            </p>
        </div>

    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            @foreach ($cbt as $c)
                @php
                    $mulai    = $c->tanggal_mulai;
                    $selesai  = $c->tanggal_mulai?->copy()->addMinutes($c->durasi_menit);
                    $now      = now();
                    $belumAdaSoal = $c->soal_count === 0;

                    if ($belumAdaSoal) {
                        $statusLabel = 'Belum ada soal';
                        $statusIcon  = 'warning';
                        $statusCls   = 'bg-[#fff3cd] text-[#856404]';
                    } elseif ($now->lt($mulai)) {
                        $statusLabel = 'Terjadwal';
                        $statusIcon  = 'event_upcoming';
                        $statusCls   = 'bg-[#EEF2FF] text-[#3c50e0]';
                    } elseif ($now->between($mulai, $selesai)) {
                        $statusLabel = 'Berlangsung';
                        $statusIcon  = 'play_circle';
                        $statusCls   = 'bg-[#d1f0dd] text-[#1c8c4e]';
                    } else {
                        $statusLabel = 'Selesai';
                        $statusIcon  = 'check_circle';
                        $statusCls   = 'bg-[#f0f4f8] text-[#505f76]';
                    }

                    $jenisMap = [
                        'pilihan_ganda' => ['label' => 'Pilihan Ganda', 'cls' => 'bg-[#EEF2FF] text-[#3c50e0]'],
                        'uraian'        => ['label' => 'Uraian',        'cls' => 'bg-[#fde8d4] text-[#a85b00]'],
                        'campuran'      => ['label' => 'Campuran',      'cls' => 'bg-[#e8e0ff] text-[#5b3ec8]'],
                    ];
                    $jenis = $jenisMap[$c->jenis_cbt] ?? ['label' => ucfirst($c->jenis_cbt ?? '-'), 'cls' => 'bg-[#f0f4f8] text-[#505f76]'];

                    $totalPD = $pdCountByGmrId[$c->guru_mapel_rombel_id] ?? 0;
                @endphp
                <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm flex flex-col relative overflow-hidden hover:shadow-md transition-shadow">
                    <div class="absolute top-0 left-0 w-1 h-full {{ $belumAdaSoal ? 'bg-[#856404]' : 'bg-[#3c50e0]' }}"></div>

                    <div class="p-5 flex flex-col flex-1 pl-6">
                        <div class="flex items-start justify-between gap-3 mb-2">
                            <h3 class="text-[15px] font-semibold text-on-surface line-clamp-2 flex-1 leading-snug">{{ $c->nama_ujian }}</h3>
                            <span class="flex-shrink-0 inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $statusCls }}">
                                <span class="material-symbols-outlined text-[12px]">{{ $statusIcon }}</span>
                                {{ $statusLabel }}
                            </span>
                        </div>

                        <p class="text-[12px] text-[#505f76] mb-3">
                            {{ $c->guruMapelRombel?->mapel?->nama }}
                            <span class="text-[#c5c5d7] mx-1">·</span>
                            {{ $c->guruMapelRombel?->rombel?->nama }}
                        </p>

                        <div class="flex flex-wrap items-center gap-2 mb-3">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $jenis['cls'] }}">
                                {{ $jenis['label'] }}
                            </span>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-[#f0f4f8] text-[#505f76]">
                                <span class="material-symbols-outlined text-[12px]">flag</span>KKM {{ $c->kkm }}
                            </span>
                        </div>

                        <div class="space-y-1.5 text-[12px] text-[#505f76] mb-4">
                            <div class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[14px]">calendar_today</span>
                                {{ $mulai?->translatedFormat('d M Y, H:i') }}
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[14px]">timer</span>
                                {{ $c->durasi_menit }} menit
                            </div>
                        </div>

                        <div class="mt-auto pt-4 border-t border-[#f0f4f8]">
                            <div class="flex items-center justify-between text-[12px] mb-4">
                                <span class="inline-flex items-center gap-1 text-[#505f76]">
                                    <span class="material-symbols-outlined text-[14px]">help</span>
                                    {{ $c->soal_count }} soal
                                </span>
                                <span class="inline-flex items-center gap-1 text-[#505f76]">
                                    <span class="material-symbols-outlined text-[14px]">groups</span>
                                    {{ $c->hasil_cbt_count }}/{{ $totalPD }} mengerjakan
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-2 mb-2">
                                @if (Route::has('guru.cbt.soal'))
                                    <a href="{{ route('guru.cbt.soal', $c->id) }}"
                                       class="flex items-center justify-center gap-1.5 px-3 py-2 text-[13px] font-medium text-[#3c50e0] border border-[#c5d0ff] bg-[#f6fafe] rounded-lg hover:bg-[#EEF2FF] transition-colors cursor-pointer">
                                        <span class="material-symbols-outlined text-[15px]">edit_note</span>
                                        Kelola Soal
                                    </a>
                                @else
                                    <button type="button" disabled
                                            class="flex items-center justify-center gap-1.5 px-3 py-2 text-[13px] font-medium text-[#9aa0ac] border border-[#e4e6ee] bg-[#f6f7f9] rounded-lg cursor-not-allowed">
                                        <span class="material-symbols-outlined text-[15px]">edit_note</span>
                                        Kelola Soal
                                    </button>
                                @endif

                                @if (Route::has('guru.cbt.hasil'))
                                    <a href="{{ route('guru.cbt.hasil', $c->id) }}"
                                       class="flex items-center justify-center gap-1.5 px-3 py-2 text-[13px] font-medium text-white bg-[#3c50e0] rounded-lg hover:bg-[#2e3eb0] transition-colors cursor-pointer">
                                        <span class="material-symbols-outlined text-[15px]">grading</span>
                                        Lihat Hasil
                                    </a>
                                @else
                                    <button type="button" disabled
                                            class="flex items-center justify-center gap-1.5 px-3 py-2 text-[13px] font-medium text-white bg-[#c5c5d7] rounded-lg cursor-not-allowed opacity-70">
                                        <span class="material-symbols-outlined text-[15px]">grading</span>
                                        Lihat Hasil
                                    </button>
                                @endif
                            </div>

                            <div class="flex items-center gap-2">
                                @if (Route::has('guru.cbt.edit'))
                                    <a href="{{ route('guru.cbt.edit', $c->id) }}"
                                       class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 text-[13px] font-medium text-[#505f76] border border-[#c5c5d7] rounded-lg hover:bg-[#f0f4f8] transition-colors cursor-pointer" title="Edit">
                                        <span class="material-symbols-outlined text-[16px]">edit</span>
                                        Edit
                                    </a>
                                @else
                                    <button type="button" disabled
                                            class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 text-[13px] font-medium text-[#9aa0ac] border border-[#e4e6ee] rounded-lg cursor-not-allowed" title="Edit">
                                        <span class="material-symbols-outlined text-[16px]">edit</span>
                                        Edit
                                    </button>
                                @endif
                                <button wire:click="confirmDelete({{ $c->id }})"
                                        class="flex items-center justify-center px-3 py-2 text-[#ba1a1a] border border-[#ffdad6] rounded-lg hover:bg-[#ffdad6] transition-colors cursor-pointer" title="Hapus">
                                    <span class="material-symbols-outlined text-[16px]">delete</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if ($cbt->hasPages())
            <div class="mt-6">{{ $cbt->links() }}</div>
        @endif
    @endif

    {{-- ── Modal Konfirmasi Hapus ──────────────────────────────────────────── --}}
    @if ($confirmDeleteId)
        <div class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center bg-black/50 backdrop-blur-sm p-0 sm:p-4">
            <div class="bg-white rounded-t-2xl sm:rounded-xl shadow-xl shadow-black/10 w-full max-w-4xl sm:max-w-sm p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 rounded-full bg-[#ffdad6] flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-[#ba1a1a] text-[24px]">delete_forever</span>
                    </div>
                    <div>
                        <h4 class="text-[16px] font-semibold text-on-surface">Hapus CBT?</h4>
                        <p class="text-[13px] text-[#505f76] mt-0.5">Soal dan seluruh hasil pengerjaan peserta didik untuk CBT ini juga akan terhapus.</p>
                    </div>
                </div>
                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                    <button wire:click="cancelDelete"
                            class="w-full sm:w-auto px-5 py-2.5 text-[14px] font-medium text-[#505f76] border border-[#c5c5d7] rounded-xl hover:bg-[#f0f4f8] transition-colors cursor-pointer text-center">
                        Batal
                    </button>
                    <button wire:click="delete"
                            class="w-full sm:w-auto px-5 py-2.5 text-[14px] font-semibold text-white bg-[#ba1a1a] rounded-xl hover:bg-[#93000a] transition-colors cursor-pointer text-center">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
