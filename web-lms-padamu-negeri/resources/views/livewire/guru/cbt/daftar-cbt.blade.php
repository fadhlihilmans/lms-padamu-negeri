<div class="max-w-5xl mx-auto space-y-4">

    {{-- ── Title + action ─────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center gap-3 justify-between">
        <div>
            <h1 class="text-xl font-bold text-on-surface">CBT (Computer Based Test)</h1>
            <p class="text-sm text-[#757686] mt-0.5">Pilih mata pelajaran & rombel untuk mengelola CBT</p>
        </div>
        <button wire:click="openCreateForm"
                class="flex items-center gap-1.5 px-4 py-2 bg-[#3c50e0] text-white text-sm font-medium rounded-lg hover:bg-[#2a3db0] shadow-sm whitespace-nowrap cursor-pointer">
            <span class="material-symbols-outlined text-[18px]">add</span> Buat CBT Baru
        </button>
    </div>

    @if ($gmrList->isEmpty())
        {{-- ── Empty: belum ada pemetaan ──────────────────────────────────── --}}
        <div class="bg-white border border-[#c5c5d7] rounded-xl px-6 py-16 text-center">
            <span class="material-symbols-outlined text-[48px] text-[#c5c5d7] mb-3 block">school</span>
            <p class="text-[15px] font-medium text-on-surface mb-1">Belum ada pemetaan mapel</p>
            <p class="text-[13px] text-[#757686]">Hubungi Admin untuk menambahkan pemetaan mengajar Anda.</p>
        </div>
    @else
        {{-- ── GMR selector ───────────────────────────────────────────────── --}}
        <div class="bg-white border border-[#c5c5d7] rounded-xl p-4 flex flex-col sm:flex-row sm:flex-wrap gap-3 sm:items-end">
            <div class="w-full sm:flex-1 sm:min-w-[180px]">
                <label class="text-xs font-medium text-[#505f76] block mb-1">Mata Pelajaran</label>
                <select wire:model.live="mapelId"
                        class="w-full px-3 py-2 border border-[#c5c5d7] rounded-lg text-sm bg-white cursor-pointer focus:outline-none focus:border-[#3c50e0] focus:ring-1 focus:ring-[#3c50e0]">
                    @foreach ($mapelOptions as $opt)
                        <option value="{{ $opt->mapel_id }}">{{ $opt->mapel?->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full sm:flex-1 sm:min-w-[160px]">
                <label class="text-xs font-medium text-[#505f76] block mb-1">Rombel</label>
                <select wire:model.live="gmrId"
                        class="w-full px-3 py-2 border border-[#c5c5d7] rounded-lg text-sm bg-white cursor-pointer focus:outline-none focus:border-[#3c50e0] focus:ring-1 focus:ring-[#3c50e0]">
                    @foreach ($rombelOptions as $opt)
                        <option value="{{ $opt->id }}">{{ $opt->rombel?->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- ── Context banner ─────────────────────────────────────────────── --}}
        @if ($gmrSelected)
            <div class="bg-[#EEF2FF] border border-[#3c50e0]/30 rounded-lg px-4 py-2.5 flex flex-wrap items-center gap-x-2 gap-y-0.5 text-sm">
                <span class="material-symbols-outlined text-[18px] text-[#3c50e0]">info</span>
                <span class="text-[#3c50e0] font-medium">{{ $gmrSelected->mapel?->nama }}</span>
                <span class="w-full sm:w-auto text-[#505f76]">— {{ $gmrSelected->rombel?->nama }}@if ($periode) · Periode {{ $periode->tahun_ajaran }}@endif</span>
            </div>
        @endif

        {{-- ── Toolbar ────────────────────────────────────────────────────── --}}
        <div class="bg-white border border-[#c5c5d7] rounded-xl px-4 py-3 flex flex-col sm:flex-row sm:flex-wrap gap-2 sm:items-center sm:justify-between">
            <div class="flex flex-col sm:flex-row gap-2 sm:items-center w-full sm:w-auto">
                <div class="relative w-full sm:w-auto">
                    <span class="material-symbols-outlined absolute left-2 top-1/2 -translate-y-1/2 text-[#757686] text-[18px] pointer-events-none">search</span>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama ujian…"
                           class="w-full sm:w-[220px] pl-9 pr-3 py-2 border border-[#c5c5d7] rounded-md text-sm bg-white focus:outline-none focus:border-[#3c50e0] focus:ring-2 focus:ring-[#3c50e0]/15">
                </div>
                <select wire:model.live="statusFilter"
                        class="w-full sm:w-auto px-3 py-2 border border-[#c5c5d7] rounded-lg text-sm bg-white text-[#505f76] cursor-pointer focus:outline-none focus:border-[#3c50e0]">
                    <option value="">Semua Status</option>
                    <option value="terjadwal">Terjadwal</option>
                    <option value="berlangsung">Berlangsung</option>
                    <option value="selesai">Selesai</option>
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

        {{-- ── Table ──────────────────────────────────────────────────────── --}}
        <div class="bg-white border border-[#c5c5d7] rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-white border-b border-[#c5c5d7]">
                            <th class="px-4 py-3 text-xs font-semibold text-[#505f76] uppercase tracking-wide w-10">No</th>
                            <th class="px-4 py-3 text-xs font-semibold text-[#505f76] uppercase tracking-wide">Nama Ujian</th>
                            <th class="px-4 py-3 text-xs font-semibold text-[#505f76] uppercase tracking-wide">Jadwal</th>
                            <th class="px-4 py-3 text-xs font-semibold text-[#505f76] uppercase tracking-wide">Durasi</th>
                            <th class="px-4 py-3 text-xs font-semibold text-[#505f76] uppercase tracking-wide">Jenis / Soal</th>
                            <th class="px-4 py-3 text-xs font-semibold text-[#505f76] uppercase tracking-wide">KKM</th>
                            <th class="px-4 py-3 text-xs font-semibold text-[#505f76] uppercase tracking-wide">Status</th>
                            <th class="px-4 py-3 text-xs font-semibold text-[#505f76] uppercase tracking-wide text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#c5c5d7]">
                        @forelse ($cbt as $i => $c)
                            @php
                                $mulai   = $c->tanggal_mulai;
                                $selesai = $c->tanggal_mulai?->copy()->addMinutes($c->durasi_menit);
                                $now     = now();

                                if ($now->lt($mulai)) {
                                    $status = 'terjadwal';
                                    $statusChip = 'bg-[#EEF2FF] text-[#3c50e0]';
                                    $statusDot  = 'bg-[#3c50e0]';
                                    $statusLabel = 'Terjadwal';
                                } elseif ($now->between($mulai, $selesai)) {
                                    $status = 'berlangsung';
                                    $statusChip = 'bg-amber-50 text-amber-700';
                                    $statusDot  = 'bg-amber-500 animate-pulse';
                                    $statusLabel = 'Berlangsung';
                                } else {
                                    $status = 'selesai';
                                    $statusChip = 'bg-green-50 text-green-700';
                                    $statusDot  = 'bg-green-500';
                                    $statusLabel = 'Selesai';
                                }

                                $jenisMap = [
                                    'pilihan_ganda' => ['PG', 'bg-green-50 text-green-700'],
                                    'uraian'        => ['Uraian', 'bg-purple-50 text-purple-700'],
                                    'campuran'      => ['Campuran', 'bg-[#d0e1fb] text-[#3c50e0]'],
                                ];
                                [$jenisLabel, $jenisCls] = $jenisMap[$c->jenis_cbt] ?? ['-', 'bg-[#f0f4f8] text-[#505f76]'];
                            @endphp
                            <tr class="hover:bg-[#f6fafe] transition-colors">
                                <td class="px-4 py-3 text-[#757686]">{{ $cbt->firstItem() + $i }}</td>
                                <td class="px-4 py-3 whitespace-nowrap"><p class="font-medium text-on-surface">{{ $c->nama_ujian }}</p></td>
                                <td class="px-4 py-3 text-[#505f76] whitespace-nowrap text-xs">
                                    {{ $mulai?->translatedFormat('d M Y, H:i') }}<br/>s.d. {{ $selesai?->translatedFormat('H:i') }}
                                </td>
                                <td class="px-4 py-3 text-[#505f76] whitespace-nowrap">{{ $c->durasi_menit }} menit</td>
                                <td class="px-4 py-3 text-xs whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded-full font-medium {{ $jenisCls }}">{{ $jenisLabel }}</span>
                                    <span class="text-[#757686] ml-1">{{ $c->soal_count }} soal</span>
                                </td>
                                <td class="px-4 py-3 font-medium text-on-surface">{{ $c->kkm }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full font-medium whitespace-nowrap {{ $statusChip }}">
                                        <span class="w-1.5 h-1.5 rounded-full flex-shrink-0 {{ $statusDot }}"></span> {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-1">
                                        @if ($status === 'terjadwal')
                                            @if (Route::has('guru.cbt.soal'))
                                                <a href="{{ route('guru.cbt.soal', $c->id) }}" wire:navigate
                                                   class="w-8 h-8 rounded flex items-center justify-center text-[#505f76] hover:text-[#3c50e0] hover:bg-[#EEF2FF] cursor-pointer" title="Kelola Soal">
                                                    <span class="material-symbols-outlined text-[18px]">edit_note</span>
                                                </a>
                                            @endif
                                            <button wire:click="openEditForm({{ $c->id }})"
                                                    class="w-8 h-8 rounded flex items-center justify-center text-[#505f76] hover:text-[#3c50e0] hover:bg-[#EEF2FF] cursor-pointer" title="Edit">
                                                <span class="material-symbols-outlined text-[18px]">edit</span>
                                            </button>
                                        @else
                                            @if (Route::has('guru.cbt.hasil'))
                                                <a href="{{ route('guru.cbt.hasil', $c->id) }}" wire:navigate
                                                   class="w-8 h-8 rounded flex items-center justify-center text-[#505f76] hover:text-[#3c50e0] hover:bg-[#EEF2FF] cursor-pointer" title="Lihat Hasil">
                                                    <span class="material-symbols-outlined text-[18px]">bar_chart</span>
                                                </a>
                                            @endif
                                            @if ($status === 'selesai' && $c->uraian_count > 0 && Route::has('guru.cbt.koreksi'))
                                                <a href="{{ route('guru.cbt.koreksi', $c->id) }}" wire:navigate
                                                   class="w-8 h-8 rounded flex items-center justify-center text-[#505f76] hover:text-amber-600 hover:bg-amber-50 cursor-pointer" title="Koreksi Uraian">
                                                    <span class="material-symbols-outlined text-[18px]">rate_review</span>
                                                </a>
                                            @endif
                                            <button wire:click="openEditForm({{ $c->id }})"
                                                    class="w-8 h-8 rounded flex items-center justify-center text-[#505f76] hover:text-[#3c50e0] hover:bg-[#EEF2FF] cursor-pointer" title="Edit (nama, KKM, tampilkan nilai)">
                                                <span class="material-symbols-outlined text-[18px]">edit</span>
                                            </button>
                                        @endif
                                        <button wire:click="confirmDelete({{ $c->id }})"
                                                class="w-8 h-8 rounded flex items-center justify-center text-[#505f76] hover:text-red-600 hover:bg-red-50 cursor-pointer" title="Hapus">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-16 text-center">
                                    <span class="material-symbols-outlined text-[48px] text-[#c5c5d7] mb-3 block">quiz</span>
                                    <p class="text-[15px] font-medium text-on-surface mb-1">
                                        {{ $search || $statusFilter ? 'Tidak ada CBT yang cocok' : 'Belum ada CBT' }}
                                    </p>
                                    <p class="text-[13px] text-[#757686]">
                                        {{ $search || $statusFilter ? 'Coba ubah pencarian / filter.' : 'Klik "Buat CBT Baru" untuk menambahkan ujian.' }}
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($cbt instanceof \Illuminate\Contracts\Pagination\Paginator || $cbt instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="px-4 py-3 border-t border-[#c5c5d7] flex flex-col sm:flex-row items-center justify-between gap-3 text-sm text-[#505f76]">
                    <span>Menampilkan {{ $cbt->firstItem() ?? 0 }}–{{ $cbt->lastItem() ?? 0 }} dari {{ $cbt->total() }} CBT</span>
                    @if ($cbt->hasPages())
                        <div>{{ $cbt->links() }}</div>
                    @endif
                </div>
            @endif
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- Modal Buat / Edit CBT (9.3)                                            --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    @if ($showForm)
        <div class="fixed inset-0 z-50 bg-black/40 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4">
            <div class="bg-white w-full max-w-4xl sm:max-w-lg rounded-t-2xl sm:rounded-xl overflow-hidden shadow-xl flex flex-col max-h-[92vh]">
                <div class="px-5 py-4 border-b border-[#c5c5d7] flex items-center justify-between flex-shrink-0">
                    <h2 class="text-base font-semibold text-on-surface">{{ $editId ? 'Edit CBT' : 'Buat CBT Baru' }}</h2>
                    <button wire:click="closeForm" class="text-[#757686] hover:text-[#171c1f] cursor-pointer">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form wire:submit="save" class="flex flex-col flex-1 min-h-0">
                    <div class="p-5 overflow-y-auto min-h-0 space-y-4 flex-1">
                        @if ($scheduleLocked)
                            <div class="flex items-start gap-2 p-3 rounded-lg bg-amber-50 border border-amber-200 text-xs text-amber-700">
                                <span class="material-symbols-outlined text-[16px]">lock</span>
                                <span>Ujian sudah berjalan — jadwal &amp; durasi terkunci. Kamu masih bisa mengubah <strong>Nama Ujian</strong>, <strong>KKM</strong>, dan <strong>Tampilkan Nilai Otomatis</strong>.</span>
                            </div>
                        @endif

                        {{-- Nama Ujian --}}
                        <div>
                            <label class="text-xs font-medium text-[#505f76] block mb-1">Nama Ujian <span class="text-red-500">*</span></label>
                            <input wire:model="namaUjian" type="text" placeholder="cth: Ulangan Harian 1 – Eksponen"
                                   class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:border-[#3c50e0] focus:ring-1 focus:ring-[#3c50e0] @error('namaUjian') border-[#ba1a1a] @else border-[#c5c5d7] @enderror">
                            @error('namaUjian') <p class="text-xs text-[#ba1a1a] mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Tanggal + Jam --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-xs font-medium text-[#505f76] block mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
                                <input wire:model="tanggalMulai" type="date" @disabled($scheduleLocked)
                                       class="w-full px-3 py-2 border rounded-lg text-sm cursor-pointer focus:outline-none focus:border-[#3c50e0] focus:ring-1 focus:ring-[#3c50e0] disabled:bg-[#f0f4f8] disabled:cursor-not-allowed disabled:text-[#757686] @error('tanggalMulai') border-[#ba1a1a] @else border-[#c5c5d7] @enderror">
                                @error('tanggalMulai') <p class="text-xs text-[#ba1a1a] mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="text-xs font-medium text-[#505f76] block mb-1">Jam Mulai <span class="text-red-500">*</span></label>
                                <input wire:model="jamMulai" type="time" @disabled($scheduleLocked)
                                       class="w-full px-3 py-2 border rounded-lg text-sm cursor-pointer focus:outline-none focus:border-[#3c50e0] focus:ring-1 focus:ring-[#3c50e0] disabled:bg-[#f0f4f8] disabled:cursor-not-allowed disabled:text-[#757686] @error('jamMulai') border-[#ba1a1a] @else border-[#c5c5d7] @enderror">
                                @error('jamMulai') <p class="text-xs text-[#ba1a1a] mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Durasi --}}
                        <div>
                            <label class="text-xs font-medium text-[#505f76] block mb-1">Durasi (menit) <span class="text-red-500">*</span></label>
                            <input wire:model="durasiMenit" type="number" min="1" max="1440" placeholder="90" @disabled($scheduleLocked)
                                   class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:border-[#3c50e0] focus:ring-1 focus:ring-[#3c50e0] disabled:bg-[#f0f4f8] disabled:cursor-not-allowed disabled:text-[#757686] @error('durasiMenit') border-[#ba1a1a] @else border-[#c5c5d7] @enderror">
                            @error('durasiMenit') <p class="text-xs text-[#ba1a1a] mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- KKM --}}
                        <div>
                            <label class="text-xs font-medium text-[#505f76] block mb-1">KKM <span class="text-red-500">*</span></label>
                            <input wire:model="kkm" type="number" min="0" max="100" placeholder="75"
                                   class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:border-[#3c50e0] focus:ring-1 focus:ring-[#3c50e0] @error('kkm') border-[#ba1a1a] @else border-[#c5c5d7] @enderror">
                            @error('kkm') <p class="text-xs text-[#ba1a1a] mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Tampilkan nilai otomatis --}}
                        <div>
                            <label class="text-xs font-medium text-[#505f76] block mb-2">Tampilkan Nilai Otomatis setelah Submit?</label>
                            <div class="flex items-center gap-3">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" wire:click="$set('tampilkanNilaiOtomatis', true)" @checked($tampilkanNilaiOtomatis) name="tampil_nilai" class="accent-[#3c50e0]"/>
                                    <span class="text-sm">Ya (khusus PG)</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" wire:click="$set('tampilkanNilaiOtomatis', false)" @checked(! $tampilkanNilaiOtomatis) name="tampil_nilai" class="accent-[#3c50e0]"/>
                                    <span class="text-sm">Tidak</span>
                                </label>
                            </div>
                            <p class="text-xs text-[#757686] mt-1">Hanya berlaku jika semua soal berjenis Pilihan Ganda.</p>
                        </div>
                    </div>

                    <div class="px-5 py-4 border-t border-[#c5c5d7] flex flex-col-reverse sm:flex-row sm:justify-end gap-2 flex-shrink-0 bg-white">
                        <button type="button" wire:click="closeForm"
                                class="w-full sm:w-auto px-4 py-2 text-sm rounded-lg border border-[#c5c5d7] text-[#505f76] hover:bg-[#f0f4f8] cursor-pointer text-center">
                            Batal
                        </button>
                        <button type="submit" wire:loading.attr="disabled"
                                class="w-full sm:w-auto px-4 py-2 text-sm rounded-lg bg-[#3c50e0] text-white hover:bg-[#2a3db0] font-medium cursor-pointer flex items-center justify-center gap-2">
                            <span wire:loading wire:target="save" class="material-symbols-outlined text-[16px] animate-spin">progress_activity</span>
                            {{ $editId ? 'Simpan Perubahan' : 'Simpan & Kelola Soal' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ── Modal Konfirmasi Hapus ─────────────────────────────────────────── --}}
    @if ($confirmDeleteId)
        <div class="fixed inset-0 z-[60] flex items-end sm:items-center justify-center bg-black/40 backdrop-blur-sm p-0 sm:p-4">
            <div class="bg-white rounded-t-2xl sm:rounded-xl shadow-xl w-full max-w-4xl sm:max-w-sm p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-red-600 text-[24px]">delete_forever</span>
                    </div>
                    <div>
                        <h4 class="text-[16px] font-semibold text-on-surface">Hapus CBT?</h4>
                        <p class="text-[13px] text-[#757686] mt-0.5">Soal dan seluruh hasil pengerjaan peserta didik untuk CBT ini juga akan terhapus.</p>
                    </div>
                </div>
                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                    <button wire:click="cancelDelete"
                            class="w-full sm:w-auto px-5 py-2.5 text-[14px] font-medium text-[#505f76] border border-[#c5c5d7] rounded-xl hover:bg-[#f0f4f8] cursor-pointer text-center">
                        Batal
                    </button>
                    <button wire:click="delete"
                            class="w-full sm:w-auto px-5 py-2.5 text-[14px] font-semibold text-white bg-[#ba1a1a] rounded-xl hover:bg-[#93000a] cursor-pointer text-center">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
