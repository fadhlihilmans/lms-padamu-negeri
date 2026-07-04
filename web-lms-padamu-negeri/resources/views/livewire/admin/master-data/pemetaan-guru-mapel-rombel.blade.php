<div>

    {{-- ── Header (3.3.1) ──────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
        <div>
            <h1 class="text-[18px] font-bold" style="color: #171c1f">Pemetaan Guru Mapel per Rombel</h1>
            <p class="text-[13px] mt-0.5" style="color: #757686">Tentukan guru pengampu untuk setiap mata pelajaran di setiap rombel.</p>
        </div>
        <button wire:click="openCreateForm"
                class="flex items-center gap-2 px-4 py-2.5 rounded-lg text-[13.5px] font-semibold text-white transition-colors cursor-pointer self-start sm:self-auto bg-[#3c50e0] hover:bg-[#2e3eb0] shadow-sm">
            <span class="material-symbols-outlined text-[17px]">add</span>Tambah Pemetaan
        </button>
    </div>

    @if (! $periodeAktif)
        {{-- Tidak ada periode aktif --}}
        <div class="bg-white rounded-xl border px-6 py-16 text-center" style="border-color: #c5c5d7; box-shadow: 0 1px 3px rgba(0,0,0,0.05)">
            <span class="material-symbols-outlined text-[48px] mb-3 block" style="color: #c5c5d7">event_busy</span>
            <p class="text-[15px] font-semibold" style="color: #171c1f">Tidak ada Periode Ajaran aktif</p>
            <p class="text-[13px] mt-1" style="color: #757686">Aktifkan salah satu Periode Ajaran terlebih dahulu di menu Periode Ajaran.</p>
        </div>
    @else

        {{-- ── Modal Form Tambah/Edit (3.1.2) ────────────────────────────────── --}}
        @if ($showForm)
            <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4"
                 style="background: rgba(0,0,0,0.5); backdrop-filter: blur(3px); -webkit-backdrop-filter: blur(3px)"
                 wire:keydown.escape="closeForm">
                <div class="relative z-10 w-full sm:max-w-md bg-white rounded-t-2xl sm:rounded-xl overflow-hidden shadow-xl flex flex-col max-h-[85dvh] sm:max-h-[80vh]"
                     style="box-shadow: 0 20px 60px -10px rgba(0,0,0,0.25)">

                    <div class="flex items-center justify-between px-5 py-4 border-b flex-shrink-0" style="border-color: #c5c5d7">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: #EEF2FF">
                                <span class="material-symbols-outlined text-[18px]" style="color: #3c50e0">hub</span>
                            </div>
                            <h3 class="text-[15px] font-semibold" style="color: #171c1f">{{ $editId ? 'Edit Pemetaan' : 'Tambah Pemetaan' }}</h3>
                        </div>
                        <button type="button" wire:click="closeForm"
                                class="w-8 h-8 flex items-center justify-center rounded-lg cursor-pointer transition-colors" style="color: #757686"
                                onmouseover="this.style.background='#f0f4f8'" onmouseout="this.style.background='transparent'">
                            <span class="material-symbols-outlined text-[18px]">close</span>
                        </button>
                    </div>

                    <form wire:submit="save" class="flex flex-col flex-1 min-h-0">
                        <div class="px-5 py-5 space-y-4 overflow-y-auto flex-1">
                            <p class="text-[12px]" style="color: #757686">
                                Periode Ajaran: <strong style="color: #171c1f">{{ $periodeAktif->tahun_ajaran }} {{ ucfirst($periodeAktif->semester) }}</strong>
                            </p>

                            <div>
                                <label for="rombelId" class="block text-[13px] font-medium mb-1.5" style="color: #171c1f">Rombel <span style="color: #ba1a1a">*</span></label>
                                <select wire:model="rombelId" id="rombelId"
                                        class="w-full px-3 py-2.5 rounded-lg text-[14px] bg-white outline-none cursor-pointer transition-all"
                                        style="border: 1.5px solid {{ $errors->has('rombelId') ? '#ba1a1a' : '#c5c5d7' }}; color: #171c1f"
                                        onfocus="this.style.borderColor='#3c50e0'; this.style.boxShadow='0 0 0 3px rgba(60,80,224,0.1)'"
                                        onblur="this.style.borderColor='{{ $errors->has('rombelId') ? '#ba1a1a' : '#c5c5d7' }}'; this.style.boxShadow='none'">
                                    <option value="" selected>Pilih rombel...</option>
                                    @foreach ($rombels as $r)
                                        <option value="{{ $r->id }}">{{ $r->nama }}</option>
                                    @endforeach
                                </select>
                                @error('rombelId')
                                    <p class="text-[12px] mt-1.5" style="color: #ba1a1a">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="mapelId" class="block text-[13px] font-medium mb-1.5" style="color: #171c1f">Mata Pelajaran <span style="color: #ba1a1a">*</span></label>
                                <select wire:model="mapelId" id="mapelId"
                                        class="w-full px-3 py-2.5 rounded-lg text-[14px] bg-white outline-none cursor-pointer transition-all"
                                        style="border: 1.5px solid {{ $errors->has('mapelId') ? '#ba1a1a' : '#c5c5d7' }}; color: #171c1f"
                                        onfocus="this.style.borderColor='#3c50e0'; this.style.boxShadow='0 0 0 3px rgba(60,80,224,0.1)'"
                                        onblur="this.style.borderColor='{{ $errors->has('mapelId') ? '#ba1a1a' : '#c5c5d7' }}'; this.style.boxShadow='none'">
                                    <option value="" selected>Pilih mata pelajaran...</option>
                                    @foreach ($mapels as $m)
                                        <option value="{{ $m->id }}">{{ $m->nama }}</option>
                                    @endforeach
                                </select>
                                @error('mapelId')
                                    <p class="text-[12px] mt-1.5" style="color: #ba1a1a">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="guruId" class="block text-[13px] font-medium mb-1.5" style="color: #171c1f">Guru Pengampu <span style="color: #ba1a1a">*</span></label>
                                <select wire:model="guruId" id="guruId"
                                        class="w-full px-3 py-2.5 rounded-lg text-[14px] bg-white outline-none cursor-pointer transition-all"
                                        style="border: 1.5px solid {{ $errors->has('guruId') ? '#ba1a1a' : '#c5c5d7' }}; color: #171c1f"
                                        onfocus="this.style.borderColor='#3c50e0'; this.style.boxShadow='0 0 0 3px rgba(60,80,224,0.1)'"
                                        onblur="this.style.borderColor='{{ $errors->has('guruId') ? '#ba1a1a' : '#c5c5d7' }}'; this.style.boxShadow='none'">
                                    <option value="" selected>Pilih guru...</option>
                                    @forelse ($gurus as $g)
                                        <option value="{{ $g->id }}">{{ $g->nama_lengkap }} ({{ $g->nip }})</option>
                                    @empty
                                        <option value="" disabled>Belum ada guru terdaftar</option>
                                    @endforelse
                                </select>
                                @error('guruId')
                                    <p class="text-[12px] mt-1.5" style="color: #ba1a1a">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 px-5 py-4 border-t flex-shrink-0" style="border-color: #d1d5db; background: white">
                            <button type="button" wire:click="closeForm"
                                    class="px-4 py-2.5 rounded-lg text-[13px] font-medium border cursor-pointer transition-colors"
                                    style="color: #505f76; border-color: #c5c5d7; background: white"
                                    onmouseover="this.style.background='#f0f4f8'" onmouseout="this.style.background='white'">Batal</button>
                            <button type="submit"
                                    class="px-5 py-2.5 rounded-lg text-[13.5px] font-semibold text-white cursor-pointer transition-colors flex items-center justify-center gap-2"
                                    style="background: #3c50e0" onmouseover="this.style.background='#2e3eb0'" onmouseout="this.style.background='#3c50e0'"
                                    wire:loading.attr="disabled" wire:loading.class="opacity-70 cursor-not-allowed">
                                <span wire:loading wire:target="save" class="material-symbols-outlined text-[16px] animate-spin">progress_activity</span>
                                Simpan Pemetaan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        {{-- ── Modal Konfirmasi Hapus ───────────────────────────────────────── --}}
        @if ($confirmDeleteId)
            <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4"
                 style="background: rgba(0,0,0,0.5); backdrop-filter: blur(3px); -webkit-backdrop-filter: blur(3px)">
                <div class="w-full sm:max-w-sm bg-white rounded-t-2xl sm:rounded-xl overflow-hidden shadow-xl flex flex-col"
                     style="box-shadow: 0 20px 60px -10px rgba(0,0,0,0.25)">
                    <div class="px-5 py-5 flex items-start gap-3">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0" style="background: #ffdad6">
                            <span class="material-symbols-outlined text-[18px]" style="color: #ba1a1a">delete</span>
                        </div>
                        <div>
                            <h4 class="text-[15px] font-semibold" style="color: #171c1f">Hapus Pemetaan ini?</h4>
                            <p class="text-[13px] mt-0.5" style="color: #505f76">Guru tidak lagi memiliki akses ke Materi, Tugas, dan CBT di rombel ini.</p>
                        </div>
                    </div>
                    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 px-5 py-4 border-t flex-shrink-0" style="border-color: #d1d5db; background: white">
                        <button wire:click="$set('confirmDeleteId', null)"
                                class="px-4 py-2.5 rounded-lg text-[13px] font-medium border cursor-pointer transition-colors text-center"
                                style="color: #505f76; border-color: #c5c5d7; background: white"
                                onmouseover="this.style.background='#f0f4f8'" onmouseout="this.style.background='white'">Batal</button>
                        <button wire:click="delete"
                                class="px-5 py-2.5 rounded-lg text-[13.5px] font-semibold text-white cursor-pointer transition-colors text-center"
                                style="background: #ba1a1a" onmouseover="this.style.background='#93000a'" onmouseout="this.style.background='#ba1a1a'">Ya, Hapus</button>
                    </div>
                </div>
            </div>
        @endif

        {{-- ── Table Card (3.3.1) ────────────────────────────────────────────── --}}
        <div class="bg-white rounded-xl border overflow-hidden" style="border-color: #c5c5d7; box-shadow: 0 1px 3px rgba(0,0,0,0.05)">

            {{-- Toolbar standar + filter Rombel --}}
            <div class="flex flex-col sm:flex-row sm:flex-wrap sm:items-center gap-2.5 px-4 py-3 border-b bg-white" style="border-color: #d1d5db">
                <div class="relative w-full sm:flex-1 sm:min-w-[180px]">
                    <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-[17px] pointer-events-none" style="color: #9da4b0">search</span>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari guru atau mata pelajaran..."
                           class="w-full pl-9 pr-3 py-2 rounded-lg text-[13.5px] bg-white outline-none transition-all"
                           style="border: 1.5px solid #c5c5d7; color: #171c1f"
                           onfocus="this.style.borderColor='#3c50e0'; this.style.boxShadow='0 0 0 3px rgba(60,80,224,0.1)'"
                           onblur="this.style.borderColor='#c5c5d7'; this.style.boxShadow='none'">
                </div>
                <select wire:model.live="filterRombelId" class="w-full sm:w-auto py-2 pl-2.5 rounded-lg text-[13px] cursor-pointer outline-none sm:flex-shrink-0"
                        style="border: 1.5px solid #c5c5d7; color: #505f76; background: white">
                    <option value="">Semua Rombel</option>
                    @foreach ($rombels as $r)
                        <option value="{{ $r->id }}">{{ $r->nama }}</option>
                    @endforeach
                </select>
                <div class="flex items-center gap-1.5 w-full sm:w-auto justify-between sm:justify-start sm:flex-shrink-0">
                    <span class="text-[12.5px] whitespace-nowrap" style="color: #757686">Tampil</span>
                    <select wire:model.live="perPage" class="py-2 pl-2.5 rounded-lg text-[13px] cursor-pointer outline-none"
                            style="border: 1.5px solid #c5c5d7; color: #505f76; background: white">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                    <span class="text-[12.5px] whitespace-nowrap" style="color: #757686">Data</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left min-w-[580px]">
                    <thead>
                        <tr class="border-b" style="border-color: #c5c5d7">
                            <th class="px-4 py-3 text-[11.5px] font-semibold uppercase tracking-wide w-10" style="color: #757686">No</th>
                            <th class="px-4 py-3 text-[11.5px] font-semibold uppercase tracking-wide" style="color: #757686">Rombel</th>
                            <th class="px-4 py-3 text-[11.5px] font-semibold uppercase tracking-wide" style="color: #757686">Mata Pelajaran</th>
                            <th class="px-4 py-3 text-[11.5px] font-semibold uppercase tracking-wide" style="color: #757686">Guru Pengampu</th>
                            <th class="px-4 py-3 text-[11.5px] font-semibold uppercase tracking-wide text-center w-24" style="color: #757686">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#c5c5d7]">
                        @forelse ($pemetaan as $i => $pm)
                            <tr class="transition-colors" onmouseover="this.style.background='#f6fafe'" onmouseout="this.style.background='transparent'">
                                <td class="px-4 py-3.5 text-[13px]" style="color: #9da4b0">{{ $pemetaan->firstItem() + $i }}</td>
                                <td class="px-4 py-3.5">
                                    <span class="px-2 py-0.5 rounded-full text-[11.5px] font-semibold whitespace-nowrap" style="background: #EEF2FF; color: #3c50e0">{{ $pm->rombel?->nama ?? '—' }}</span>
                                </td>
                                <td class="px-4 py-3.5 text-[13.5px] font-medium" style="color: #171c1f">{{ $pm->mapel?->nama ?? '—' }}</td>
                                <td class="px-4 py-3.5 text-[13px]" style="color: #505f76">{{ $pm->guru?->nama_lengkap ?? '—' }}</td>
                                <td class="px-4 py-3.5 text-center">
                                    <div class="inline-flex items-center gap-1">
                                        <button wire:click="openEditForm({{ $pm->id }})" title="Edit"
                                                class="w-8 h-8 inline-flex items-center justify-center rounded-lg cursor-pointer transition-colors" style="color: #505f76"
                                                onmouseover="this.style.background='#EEF2FF'; this.style.color='#3c50e0'" onmouseout="this.style.background='transparent'; this.style.color='#505f76'">
                                            <span class="material-symbols-outlined text-[17px]">edit</span>
                                        </button>
                                        <button wire:click="confirmDelete({{ $pm->id }})" title="Hapus"
                                                class="w-8 h-8 inline-flex items-center justify-center rounded-lg cursor-pointer transition-colors" style="color: #505f76"
                                                onmouseover="this.style.background='#ffdad6'; this.style.color='#ba1a1a'" onmouseout="this.style.background='transparent'; this.style.color='#505f76'">
                                            <span class="material-symbols-outlined text-[17px]">delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-16 text-center">
                                    @if ($search || $filterRombelId)
                                        <span class="material-symbols-outlined text-[40px] mb-2 block" style="color: #c5c5d7">search_off</span>
                                        <p class="text-[13px]" style="color: #757686">Tidak ada data ditemukan.</p>
                                        <button wire:click="$set('search', ''); $set('filterRombelId', '')" class="mt-2 text-[12.5px] cursor-pointer hover:underline" style="color: #3c50e0">Hapus Filter</button>
                                    @else
                                        <span class="material-symbols-outlined text-[40px] mb-2 block" style="color: #c5c5d7">hub</span>
                                        <p class="text-[13px]" style="color: #757686">Belum ada pemetaan untuk periode aktif ini.</p>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination standar --}}
            @if ($pemetaan->total() > 0)
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-4 py-3.5 border-t" style="border-color: #c5c5d7">
                    <p class="text-[12.5px]" style="color: #757686">
                        Menampilkan {{ $pemetaan->firstItem() }}–{{ $pemetaan->lastItem() }} dari {{ $pemetaan->total() }} pemetaan
                    </p>
                    @if ($pemetaan->lastPage() > 1)
                        <div class="flex items-center gap-1">
                            <button wire:click="previousPage" @disabled($pemetaan->onFirstPage())
                                    class="w-8 h-8 inline-flex items-center justify-center rounded-lg transition-colors {{ $pemetaan->onFirstPage() ? 'cursor-not-allowed' : 'cursor-pointer hover:bg-[#f0f4f8]' }}"
                                    style="color: {{ $pemetaan->onFirstPage() ? '#c5c5d7' : '#505f76' }}">
                                <span class="material-symbols-outlined text-[16px]">chevron_left</span>
                            </button>
                            @foreach (range(1, $pemetaan->lastPage()) as $page)
                                <button wire:click="gotoPage({{ $page }})"
                                        class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-[13px] transition-colors {{ $page == $pemetaan->currentPage() ? 'font-semibold text-white' : 'cursor-pointer hover:bg-[#f0f4f8]' }}"
                                        style="{{ $page == $pemetaan->currentPage() ? 'background:#3c50e0' : 'color:#505f76' }}">
                                    {{ $page }}
                                </button>
                            @endforeach
                            <button wire:click="nextPage" @disabled(! $pemetaan->hasMorePages())
                                    class="w-8 h-8 inline-flex items-center justify-center rounded-lg transition-colors {{ ! $pemetaan->hasMorePages() ? 'cursor-not-allowed' : 'cursor-pointer hover:bg-[#f0f4f8]' }}"
                                    style="color: {{ ! $pemetaan->hasMorePages() ? '#c5c5d7' : '#505f76' }}">
                                <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                            </button>
                        </div>
                    @endif
                </div>
            @endif

        </div>
    @endif

</div>
