<div>

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
        <div>
            <h2 class="text-[18px] font-bold text-[#171c1f]">Rombel</h2>
            <p class="text-[13px] text-[#757686] mt-0.5">Kelola rombongan belajar beserta wali kelas dan peserta didik.</p>
        </div>
        <a href="{{ route('admin.master.rombel.create') }}"
           class="inline-flex items-center gap-2 bg-[#3c50e0] text-white text-[13.5px] font-semibold px-4 py-2.5 rounded-lg hover:bg-[#2e3eb0] transition-colors shadow-sm cursor-pointer self-start sm:self-auto">
            <span class="material-symbols-outlined text-[17px]">add</span>
            Tambah Rombel
        </a>
    </div>

    {{-- ── Modal Konfirmasi Hapus ───────────────────────────────────────────── --}}
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
                        <h4 class="text-[15px] font-semibold" style="color: #171c1f">Hapus Rombel?</h4>
                        <p class="text-[13px] mt-0.5" style="color: #505f76">Tindakan ini tidak dapat dibatalkan.</p>
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

    {{-- ── Modal Kelola Anggota ─────────────────────────────────────────────── --}}
    @if ($kelolaRombelId && $rombelKelola)
        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/50 backdrop-blur-sm p-0 sm:p-4">
            <div class="relative bg-white rounded-t-2xl sm:rounded-xl shadow-xl shadow-black/10 w-full max-w-4xl sm:max-w-2xl border border-[#c5c5d7]">
                <button wire:click="closeKelolaMember" type="button"
                        class="absolute top-4 right-4 text-[#505f76] hover:text-[#ba1a1a] transition-colors p-1 cursor-pointer z-10 bg-white rounded-full">
                    <span class="material-symbols-outlined">close</span>
                </button>
                <div class="p-6 sm:p-8 max-h-[90vh] overflow-y-auto">
                <div class="mb-6 border-b border-[#c5c5d7] pb-4 pr-8">
                    <h3 class="text-[18px] font-semibold text-on-surface">Kelola Anggota Rombel</h3>
                    <p class="text-[13px] text-[#505f76] mt-0.5">{{ $rombelKelola->nama }}</p>
                </div>
                <div class="flex flex-col gap-6">

                    {{-- Tambah Anggota --}}
                    <div>
                        <h4 class="text-[14px] font-semibold text-on-surface mb-2">Tambah Peserta Didik</h4>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-[#757686] pointer-events-none">search</span>
                            <input wire:model.live.debounce.400ms="searchAnggota"
                                   type="search"
                                   placeholder="Cari nama atau NIPD peserta didik (min. 2 karakter)..."
                                   class="w-full pl-10 pr-4 py-2.5 border border-[#c5c5d7] rounded-lg text-[14px] text-on-surface bg-white focus:outline-none focus:ring-1 focus:ring-[#3c50e0] transition-shadow">
                        </div>

                        @if (strlen(trim($searchAnggota)) >= 2)
                            @if ($calonAnggota->isNotEmpty())
                                <ul class="mt-2 border border-[#c5c5d7] rounded-lg overflow-hidden divide-y divide-[#c5c5d7]">
                                    @foreach ($calonAnggota as $pd)
                                        <li class="flex items-center justify-between px-4 py-3 hover:bg-[#f6fafe] transition-colors">
                                            <div>
                                                <p class="text-[14px] font-medium text-on-surface">{{ $pd->nama_lengkap }}</p>
                                                <p class="text-[12px] text-[#505f76]">NIPD: {{ $pd->nipd }}</p>
                                            </div>
                                            <button wire:click="addMember({{ $pd->id }})"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-[12px] font-medium bg-[#3c50e0] text-white hover:bg-[#1c33c8] transition-colors cursor-pointer flex-shrink-0">
                                                <span class="material-symbols-outlined text-[14px]">add</span>
                                                Tambahkan
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="mt-2 px-4 py-6 text-center border border-[#c5c5d7] rounded-lg bg-[#f8fafc]">
                                    <p class="text-[13px] text-[#505f76]">Tidak ada peserta didik yang cocok dengan pencarian.</p>
                                </div>
                            @endif
                        @elseif (strlen(trim($searchAnggota)) > 0)
                            <p class="mt-2 text-[12px] text-[#757686] px-1">Ketik minimal 2 karakter untuk mencari.</p>
                        @endif
                    </div>

                    {{-- Daftar Anggota Saat Ini --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-[14px] font-semibold text-on-surface">Anggota Terdaftar</h4>
                            <span class="text-[12px] font-semibold text-[#3c50e0] bg-[#EEF2FF] px-2 py-0.5 rounded-full">
                                {{ $rombelKelola->pesertaDidikRombel->count() }} peserta didik
                            </span>
                        </div>

                        @if ($rombelKelola->pesertaDidikRombel->isNotEmpty())
                            <div class="border border-[#c5c5d7] rounded-lg overflow-hidden divide-y divide-[#c5c5d7]">
                                @foreach ($rombelKelola->pesertaDidikRombel as $i => $pdr)
                                    <div class="flex items-center justify-between px-4 py-3 hover:bg-[#f6fafe] transition-colors">
                                        <div class="flex items-center gap-3">
                                            <span class="text-[12px] text-[#757686] w-6 text-right flex-shrink-0">{{ $i + 1 }}.</span>
                                            <div>
                                                <p class="text-[14px] font-medium text-on-surface">{{ $pdr->pesertaDidik?->nama_lengkap ?? '—' }}</p>
                                                <p class="text-[12px] text-[#505f76]">NIPD: {{ $pdr->pesertaDidik?->nipd ?? '—' }}</p>
                                            </div>
                                        </div>
                                        <button wire:click="removeMember({{ $pdr->id }})"
                                                wire:confirm="Hapus peserta didik ini dari Rombel?"
                                                class="p-1.5 text-[#505f76] hover:text-[#ba1a1a] hover:bg-[#ffdad6] rounded-lg transition-colors cursor-pointer flex-shrink-0"
                                                title="Hapus dari Rombel">
                                            <span class="material-symbols-outlined text-[18px]">person_remove</span>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="px-4 py-10 text-center border border-[#c5c5d7] rounded-lg bg-[#f8fafc]">
                                <span class="material-symbols-outlined text-[36px] text-[#c5c5d7] mb-2 block">group_off</span>
                                <p class="text-[13px] text-[#505f76]">Belum ada peserta didik di rombel ini.</p>
                                <p class="text-[12px] text-[#757686] mt-1">Gunakan pencarian di atas untuk menambahkan.</p>
                            </div>
                        @endif
                    </div>

                </div>

                <div class="flex justify-end pt-4 border-t border-[#c5c5d7] mt-2">
                    <button wire:click="closeKelolaMember"
                            class="px-5 py-2.5 rounded-lg border border-[#c5c5d7] text-[#505f76] text-[14px] font-medium hover:bg-[#f0f4f8] transition-colors cursor-pointer">
                        Selesai
                    </button>
                </div>
                </div>{{-- end scrollable --}}
            </div>
        </div>
    @endif

    {{-- ── Tabel ───────────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border overflow-hidden" style="border-color: #c5c5d7; box-shadow: 0 1px 3px rgba(0,0,0,0.05)">

        {{-- Toolbar standar + filter Wilayah/Paket/Periode --}}
        <div class="flex flex-col sm:flex-row sm:flex-wrap sm:items-center gap-2.5 px-4 py-3 border-b bg-white" style="border-color: #d1d5db">
            <div class="relative w-full sm:flex-1 sm:min-w-[180px]">
                <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-[17px] pointer-events-none" style="color: #9da4b0">search</span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama rombel..."
                       class="w-full pl-9 pr-3 py-2 rounded-lg text-[13.5px] bg-white outline-none transition-all"
                       style="border: 1.5px solid #c5c5d7; color: #171c1f"
                       onfocus="this.style.borderColor='#3c50e0'; this.style.boxShadow='0 0 0 3px rgba(60,80,224,0.1)'"
                       onblur="this.style.borderColor='#c5c5d7'; this.style.boxShadow='none'">
            </div>
            <select wire:model.live="filterWilayahId" class="w-full sm:w-auto py-2 pl-2.5 rounded-lg text-[13px] cursor-pointer outline-none sm:flex-shrink-0"
                    style="border: 1.5px solid #c5c5d7; color: #505f76; background: white">
                <option value="">Semua Wilayah</option>
                @foreach ($wilayahs as $w)
                    <option value="{{ $w->id }}">{{ $w->nama }}</option>
                @endforeach
            </select>
            <select wire:model.live="filterPaketId" class="w-full sm:w-auto py-2 pl-2.5 rounded-lg text-[13px] cursor-pointer outline-none sm:flex-shrink-0"
                    style="border: 1.5px solid #c5c5d7; color: #505f76; background: white">
                <option value="">Semua Paket</option>
                @foreach ($pakets as $p)
                    <option value="{{ $p->id }}">{{ $p->nama }}</option>
                @endforeach
            </select>
            <select wire:model.live="filterPeriodeId" class="w-full sm:w-auto py-2 pl-2.5 rounded-lg text-[13px] cursor-pointer outline-none sm:flex-shrink-0"
                    style="border: 1.5px solid #c5c5d7; color: #505f76; background: white">
                <option value="">Semua Periode</option>
                @foreach ($periodes as $p)
                    <option value="{{ $p->id }}">{{ $p->tahun_ajaran }} {{ ucfirst($p->semester) }}</option>
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
            <table class="w-full text-left min-w-[720px]">
                <thead>
                    <tr class="border-b" style="border-color: #c5c5d7">
                        <th class="px-4 py-3 text-[11.5px] font-semibold uppercase tracking-wide w-12" style="color: #757686">No</th>
                        <th class="px-4 py-3 text-[11.5px] font-semibold uppercase tracking-wide" style="color: #757686">Nama Rombel</th>
                        <th class="px-4 py-3 text-[11.5px] font-semibold uppercase tracking-wide" style="color: #757686">Paket / Tingkat</th>
                        <th class="px-4 py-3 text-[11.5px] font-semibold uppercase tracking-wide" style="color: #757686">Wali Kelas</th>
                        <th class="px-4 py-3 text-[11.5px] font-semibold uppercase tracking-wide text-center w-16" style="color: #757686">PD</th>
                        <th class="px-4 py-3 text-[11.5px] font-semibold uppercase tracking-wide text-center w-28" style="color: #757686">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#c5c5d7]">
                    @forelse ($rombels as $i => $r)
                        <tr class="transition-colors" onmouseover="this.style.background='#f6fafe'" onmouseout="this.style.background='transparent'">
                            <td class="px-4 py-3.5 text-[13px]" style="color: #9da4b0">{{ $rombels->firstItem() + $i }}</td>
                            <td class="px-4 py-3.5 text-[13.5px] font-semibold" style="color: #171c1f">{{ $r->nama }}</td>
                            <td class="px-4 py-3.5">
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold whitespace-nowrap" style="background: #EEF2FF; color: #3c50e0">{{ $r->paket?->nama ?? '—' }}</span>
                                <span class="ml-1 text-[12px] whitespace-nowrap" style="color: #757686">· {{ $r->tingkat?->nama ?? '—' }}</span>
                            </td>
                            <td class="px-4 py-3.5 text-[13px]" style="color: #505f76">
                                {{ $r->waliKelas?->nama_lengkap ?? '—' }}
                            </td>
                            <td class="px-4 py-3.5 text-center text-[13.5px] font-semibold" style="color: #171c1f">{{ $r->peserta_didik_rombel_count }}</td>
                            <td class="px-4 py-3.5 text-center">
                                <div class="inline-flex items-center gap-1">
                                    <button wire:click="openKelolaMember({{ $r->id }})" title="Lihat PD"
                                            class="w-8 h-8 inline-flex items-center justify-center rounded-lg cursor-pointer transition-colors" style="color: #505f76"
                                            onmouseover="this.style.background='#f0f4f8'" onmouseout="this.style.background='transparent'">
                                        <span class="material-symbols-outlined text-[17px]">group</span>
                                    </button>
                                    <a href="{{ route('admin.master.rombel.edit', $r->id) }}" title="Edit"
                                       class="w-8 h-8 inline-flex items-center justify-center rounded-lg cursor-pointer transition-colors" style="color: #505f76"
                                       onmouseover="this.style.background='#EEF2FF'; this.style.color='#3c50e0'" onmouseout="this.style.background='transparent'; this.style.color='#505f76'">
                                        <span class="material-symbols-outlined text-[17px]">edit</span>
                                    </a>
                                    <button wire:click="confirmDelete({{ $r->id }})" title="Hapus"
                                            class="w-8 h-8 inline-flex items-center justify-center rounded-lg cursor-pointer transition-colors" style="color: #505f76"
                                            onmouseover="this.style.background='#ffdad6'; this.style.color='#ba1a1a'" onmouseout="this.style.background='transparent'; this.style.color='#505f76'">
                                        <span class="material-symbols-outlined text-[17px]">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-16 text-center">
                                @if ($search || $filterWilayahId || $filterPaketId || $filterPeriodeId)
                                    <span class="material-symbols-outlined text-[40px] mb-2 block" style="color: #c5c5d7">search_off</span>
                                    <p class="text-[13px]" style="color: #757686">Tidak ada data ditemukan.</p>
                                    <button wire:click="$set('search', '')" class="mt-2 text-[12.5px] cursor-pointer hover:underline" style="color: #3c50e0">Hapus Filter</button>
                                @else
                                    <span class="material-symbols-outlined text-[40px] mb-2 block" style="color: #c5c5d7">groups</span>
                                    <p class="text-[13px]" style="color: #757686">Belum ada data Rombel.</p>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination standar --}}
        @if ($rombels->total() > 0)
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-4 py-3.5 border-t" style="border-color: #c5c5d7">
                <p class="text-[12.5px]" style="color: #757686">
                    Menampilkan {{ $rombels->firstItem() }}–{{ $rombels->lastItem() }} dari {{ $rombels->total() }} data
                </p>
                @if ($rombels->lastPage() > 1)
                    <div class="flex items-center gap-1">
                        <button wire:click="previousPage" @disabled($rombels->onFirstPage())
                                class="w-8 h-8 inline-flex items-center justify-center rounded-lg transition-colors {{ $rombels->onFirstPage() ? 'cursor-not-allowed' : 'cursor-pointer hover:bg-[#f0f4f8]' }}"
                                style="color: {{ $rombels->onFirstPage() ? '#c5c5d7' : '#505f76' }}">
                            <span class="material-symbols-outlined text-[16px]">chevron_left</span>
                        </button>
                        @foreach (range(1, $rombels->lastPage()) as $page)
                            <button wire:click="gotoPage({{ $page }})"
                                    class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-[13px] transition-colors {{ $page == $rombels->currentPage() ? 'font-semibold text-white' : 'cursor-pointer hover:bg-[#f0f4f8]' }}"
                                    style="{{ $page == $rombels->currentPage() ? 'background:#3c50e0' : 'color:#505f76' }}">
                                {{ $page }}
                            </button>
                        @endforeach
                        <button wire:click="nextPage" @disabled(! $rombels->hasMorePages())
                                class="w-8 h-8 inline-flex items-center justify-center rounded-lg transition-colors {{ ! $rombels->hasMorePages() ? 'cursor-not-allowed' : 'cursor-pointer hover:bg-[#f0f4f8]' }}"
                                style="color: {{ ! $rombels->hasMorePages() ? '#c5c5d7' : '#505f76' }}">
                            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                        </button>
                    </div>
                @endif
            </div>
        @endif

    </div>

</div>
