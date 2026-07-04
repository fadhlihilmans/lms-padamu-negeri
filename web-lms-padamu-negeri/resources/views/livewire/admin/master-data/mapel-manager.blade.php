<div>

    {{-- ── Header (3.1.1) ──────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
        <div>
            <h1 class="text-[18px] font-bold text-[#171c1f]">Mata Pelajaran</h1>
            <p class="text-[13px] text-[#757686] mt-0.5">Kelola daftar mata pelajaran yang tersedia.</p>
        </div>
        <button wire:click="openCreateForm"
                class="flex items-center gap-2 px-4 py-2.5 rounded-lg text-[13.5px] font-semibold text-white transition-colors cursor-pointer self-start sm:self-auto bg-[#3c50e0] hover:bg-[#2e3eb0] shadow-sm">
            <span class="material-symbols-outlined text-[17px]">add</span>Tambah Mata Pelajaran
        </button>
    </div>

    {{-- ── Modal Form (3.1.2) ─────────────────────────────────────────────── --}}
    @if ($showForm)
        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4"
             style="background: rgba(0,0,0,0.5); backdrop-filter: blur(3px); -webkit-backdrop-filter: blur(3px)"
             wire:keydown.escape="closeForm">
            <div class="relative z-10 w-full sm:max-w-sm bg-white rounded-t-2xl sm:rounded-xl overflow-hidden shadow-xl flex flex-col max-h-[80dvh] sm:max-h-[70vh]"
                 style="box-shadow: 0 20px 60px -10px rgba(0,0,0,0.25)">

                {{-- Header --}}
                <div class="flex items-center justify-between px-5 py-4 border-b flex-shrink-0" style="border-color: #c5c5d7">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: #EEF2FF">
                            <span class="material-symbols-outlined text-[18px]" style="color: #3c50e0">menu_book</span>
                        </div>
                        <h3 class="text-[15px] font-semibold" style="color: #171c1f">{{ $editId ? 'Edit Mata Pelajaran' : 'Tambah Mata Pelajaran' }}</h3>
                    </div>
                    <button type="button" wire:click="closeForm"
                            class="w-8 h-8 flex items-center justify-center rounded-lg cursor-pointer transition-colors" style="color: #757686"
                            onmouseover="this.style.background='#f0f4f8'" onmouseout="this.style.background='transparent'">
                        <span class="material-symbols-outlined text-[18px]">close</span>
                    </button>
                </div>

                <form wire:submit="save" class="flex flex-col flex-1 min-h-0">
                    {{-- Body --}}
                    <div class="px-5 py-5 overflow-y-auto flex-1">
                        <label for="nama" class="block text-[13px] font-medium mb-1.5" style="color: #171c1f">Nama Mata Pelajaran <span style="color: #ba1a1a">*</span></label>
                        <input wire:model="nama" id="nama" type="text" autofocus placeholder="Contoh: Matematika"
                               class="w-full px-3 py-2.5 rounded-lg text-[14px] bg-white outline-none transition-all"
                               style="border: 1.5px solid {{ $errors->has('nama') ? '#ba1a1a' : '#c5c5d7' }}; color: #171c1f"
                               onfocus="this.style.borderColor='#3c50e0'; this.style.boxShadow='0 0 0 3px rgba(60,80,224,0.1)'"
                               onblur="this.style.borderColor='{{ $errors->has('nama') ? '#ba1a1a' : '#c5c5d7' }}'; this.style.boxShadow='none'">
                        @error('nama')
                            <p class="text-[12px] mt-1.5" style="color: #ba1a1a">{{ $message }}</p>
                        @else
                            <p class="text-[12px] mt-1.5" style="color: #9da4b0">Nama mata pelajaran harus unik dan tidak boleh duplikat.</p>
                        @enderror
                    </div>

                    {{-- Footer --}}
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
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

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
                        <h4 class="text-[15px] font-semibold" style="color: #171c1f">Hapus Mata Pelajaran?</h4>
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

    {{-- ── Table Card (3.1.1) ─────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border overflow-hidden" style="border-color: #c5c5d7; box-shadow: 0 1px 3px rgba(0,0,0,0.05)">

        {{-- Toolbar standar --}}
        <div class="flex flex-col sm:flex-row sm:flex-wrap sm:items-center gap-2.5 px-4 py-3 border-b bg-white" style="border-color: #d1d5db">
            <div class="relative w-full sm:flex-1 sm:min-w-[180px]">
                <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-[17px] pointer-events-none" style="color: #9da4b0">search</span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama mata pelajaran..."
                       class="w-full pl-9 pr-3 py-2 rounded-lg text-[13.5px] bg-white outline-none transition-all"
                       style="border: 1.5px solid #c5c5d7; color: #171c1f"
                       onfocus="this.style.borderColor='#3c50e0'; this.style.boxShadow='0 0 0 3px rgba(60,80,224,0.1)'"
                       onblur="this.style.borderColor='#c5c5d7'; this.style.boxShadow='none'">
            </div>
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

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left min-w-[420px]">
                <thead>
                    <tr class="border-b" style="border-color: #c5c5d7">
                        <th class="px-4 py-3 text-[11.5px] font-semibold uppercase tracking-wide w-12" style="color: #757686">No</th>
                        <th class="px-4 py-3 text-[11.5px] font-semibold uppercase tracking-wide" style="color: #757686">Nama Mata Pelajaran</th>
                        <th class="px-4 py-3 text-[11.5px] font-semibold uppercase tracking-wide text-center" style="color: #757686">Digunakan</th>
                        <th class="px-4 py-3 text-[11.5px] font-semibold uppercase tracking-wide text-center w-24" style="color: #757686">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#c5c5d7]">
                    @forelse ($mapels as $i => $m)
                        <tr class="transition-colors" onmouseover="this.style.background='#f6fafe'" onmouseout="this.style.background='transparent'">
                            <td class="px-4 py-3.5 text-[13px]" style="color: #9da4b0">{{ $mapels->firstItem() + $i }}</td>
                            <td class="px-4 py-3.5 text-[13.5px] font-medium" style="color: #171c1f">{{ $m->nama }}</td>
                            <td class="px-4 py-3.5 text-center">
                                @if ($m->guru_mapel_rombel_count > 0)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[12px] font-semibold whitespace-nowrap" style="background: #dcfce7; color: #15803d">
                                        {{ $m->guru_mapel_rombel_count }} pemetaan
                                    </span>
                                @else
                                    <span class="text-[12px]" style="color: #9da4b0">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <div class="inline-flex items-center gap-1">
                                    <button wire:click="openEditForm({{ $m->id }})" title="Edit"
                                            class="w-8 h-8 inline-flex items-center justify-center rounded-lg cursor-pointer transition-colors" style="color: #505f76"
                                            onmouseover="this.style.background='#EEF2FF'; this.style.color='#3c50e0'" onmouseout="this.style.background='transparent'; this.style.color='#505f76'">
                                        <span class="material-symbols-outlined text-[17px]">edit</span>
                                    </button>
                                    <button wire:click="confirmDelete({{ $m->id }})" title="Hapus"
                                            class="w-8 h-8 inline-flex items-center justify-center rounded-lg cursor-pointer transition-colors" style="color: #505f76"
                                            onmouseover="this.style.background='#ffdad6'; this.style.color='#ba1a1a'" onmouseout="this.style.background='transparent'; this.style.color='#505f76'">
                                        <span class="material-symbols-outlined text-[17px]">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-16 text-center">
                                @if ($search)
                                    <span class="material-symbols-outlined text-[40px] mb-2 block" style="color: #c5c5d7">search_off</span>
                                    <p class="text-[13px]" style="color: #757686">Tidak ada data ditemukan.</p>
                                    <button wire:click="$set('search', '')" class="mt-2 text-[12.5px] cursor-pointer hover:underline" style="color: #3c50e0">Hapus Filter</button>
                                @else
                                    <span class="material-symbols-outlined text-[40px] mb-2 block" style="color: #c5c5d7">book</span>
                                    <p class="text-[13px]" style="color: #757686">Belum ada data Mata Pelajaran.</p>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination standar --}}
        @if ($mapels->total() > 0)
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-4 py-3.5 border-t" style="border-color: #c5c5d7">
                <p class="text-[12.5px]" style="color: #757686">
                    Menampilkan {{ $mapels->firstItem() }}–{{ $mapels->lastItem() }} dari {{ $mapels->total() }} data
                </p>
                @if ($mapels->lastPage() > 1)
                    <div class="flex items-center gap-1">
                        <button wire:click="previousPage" @disabled($mapels->onFirstPage())
                                class="w-8 h-8 inline-flex items-center justify-center rounded-lg transition-colors {{ $mapels->onFirstPage() ? 'cursor-not-allowed' : 'cursor-pointer hover:bg-[#f0f4f8]' }}"
                                style="color: {{ $mapels->onFirstPage() ? '#c5c5d7' : '#505f76' }}">
                            <span class="material-symbols-outlined text-[16px]">chevron_left</span>
                        </button>
                        @foreach (range(1, $mapels->lastPage()) as $page)
                            <button wire:click="gotoPage({{ $page }})"
                                    class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-[13px] transition-colors {{ $page == $mapels->currentPage() ? 'font-semibold text-white' : 'cursor-pointer hover:bg-[#f0f4f8]' }}"
                                    style="{{ $page == $mapels->currentPage() ? 'background:#3c50e0' : 'color:#505f76' }}">
                                {{ $page }}
                            </button>
                        @endforeach
                        <button wire:click="nextPage" @disabled(! $mapels->hasMorePages())
                                class="w-8 h-8 inline-flex items-center justify-center rounded-lg transition-colors {{ ! $mapels->hasMorePages() ? 'cursor-not-allowed' : 'cursor-pointer hover:bg-[#f0f4f8]' }}"
                                style="color: {{ ! $mapels->hasMorePages() ? '#c5c5d7' : '#505f76' }}">
                            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                        </button>
                    </div>
                @endif
            </div>
        @endif

    </div>

</div>
