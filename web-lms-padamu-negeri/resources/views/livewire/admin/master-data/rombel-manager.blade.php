<div>

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-[24px] font-bold tracking-tight text-on-surface">Rombel</h2>
            <p class="text-[14px] text-[#505f76] mt-0.5">Kelola rombongan belajar, wali kelas, dan anggota peserta didik.</p>
        </div>
        <button wire:click="openCreateForm"
                class="inline-flex items-center gap-2 bg-[#3c50e0] text-white text-[14px] font-medium px-4 py-2.5 rounded-lg hover:bg-[#1c33c8] transition-colors shadow-sm cursor-pointer">
            <span class="material-symbols-outlined text-[18px]">add</span>
            Tambah Rombel
        </button>
    </div>

    {{-- ── Modal Form Tambah / Edit ─────────────────────────────────────────── --}}
    @if ($showForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
            <div class="bg-white rounded-xl shadow-md w-full max-w-lg border border-[#c5c5d7]">
                <div class="flex items-center justify-between p-6 border-b border-[#c5c5d7]">
                    <h3 class="text-[20px] font-semibold text-on-surface">
                        {{ $editId ? 'Edit Rombel' : 'Tambah Rombel' }}
                    </h3>
                    <button wire:click="closeForm" class="text-[#505f76] hover:text-[#ba1a1a] transition-colors p-1 cursor-pointer">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <form wire:submit="save" class="p-6 flex flex-col gap-4">

                    {{-- Periode Ajaran --}}
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[14px] font-medium text-on-surface" for="periodeAjaranId">
                            Periode Ajaran <span class="text-[#ba1a1a]">*</span>
                        </label>
                        <select wire:model="periodeAjaranId" id="periodeAjaranId"
                                class="w-full border rounded-lg px-4 py-2.5 text-[14px] text-on-surface focus:outline-none focus:ring-1 transition-shadow cursor-pointer
                                       {{ $errors->has('periodeAjaranId') ? 'border-[#ba1a1a] focus:ring-[#ba1a1a]' : 'border-[#c5c5d7] focus:ring-[#3c50e0] focus:border-[#3c50e0]' }}">
                            <option value="">— Pilih Periode —</option>
                            @foreach ($periodes as $p)
                                <option value="{{ $p->id }}">{{ $p->tahun_ajaran }} {{ $p->semester }}{{ $p->is_aktif ? ' (Aktif)' : '' }}</option>
                            @endforeach
                        </select>
                        @error('periodeAjaranId')
                            <p class="text-[12px] text-[#ba1a1a]">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Paket & Tingkat (2 kolom) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[14px] font-medium text-on-surface" for="paketId">
                                Paket <span class="text-[#ba1a1a]">*</span>
                            </label>
                            <select wire:model.live="paketId" id="paketId"
                                    class="w-full border rounded-lg px-4 py-2.5 text-[14px] text-on-surface focus:outline-none focus:ring-1 transition-shadow cursor-pointer
                                           {{ $errors->has('paketId') ? 'border-[#ba1a1a] focus:ring-[#ba1a1a]' : 'border-[#c5c5d7] focus:ring-[#3c50e0] focus:border-[#3c50e0]' }}">
                                <option value="">— Pilih Paket —</option>
                                @foreach ($pakets as $p)
                                    <option value="{{ $p->id }}">{{ $p->nama }}</option>
                                @endforeach
                            </select>
                            @error('paketId')
                                <p class="text-[12px] text-[#ba1a1a]">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-[14px] font-medium text-on-surface" for="tingkatId">
                                Tingkat <span class="text-[#ba1a1a]">*</span>
                            </label>
                            <select wire:model="tingkatId" id="tingkatId"
                                    @disabled(!$paketId)
                                    class="w-full border rounded-lg px-4 py-2.5 text-[14px] text-on-surface focus:outline-none focus:ring-1 transition-shadow cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed
                                           {{ $errors->has('tingkatId') ? 'border-[#ba1a1a] focus:ring-[#ba1a1a]' : 'border-[#c5c5d7] focus:ring-[#3c50e0] focus:border-[#3c50e0]' }}">
                                <option value="">— Pilih Tingkat —</option>
                                @foreach ($tingkats as $t)
                                    <option value="{{ $t->id }}">{{ $t->nama }}</option>
                                @endforeach
                            </select>
                            @error('tingkatId')
                                <p class="text-[12px] text-[#ba1a1a]">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Wilayah --}}
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[14px] font-medium text-on-surface" for="wilayahId">
                            Wilayah <span class="text-[#ba1a1a]">*</span>
                        </label>
                        <select wire:model="wilayahId" id="wilayahId"
                                class="w-full border rounded-lg px-4 py-2.5 text-[14px] text-on-surface focus:outline-none focus:ring-1 transition-shadow cursor-pointer
                                       {{ $errors->has('wilayahId') ? 'border-[#ba1a1a] focus:ring-[#ba1a1a]' : 'border-[#c5c5d7] focus:ring-[#3c50e0] focus:border-[#3c50e0]' }}">
                            <option value="">— Pilih Wilayah —</option>
                            @foreach ($wilayahs as $w)
                                <option value="{{ $w->id }}">{{ $w->nama }}</option>
                            @endforeach
                        </select>
                        @error('wilayahId')
                            <p class="text-[12px] text-[#ba1a1a]">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Wali Kelas (opsional) --}}
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[14px] font-medium text-on-surface" for="waliKelasId">
                            Wali Kelas <span class="text-[12px] font-normal text-[#757686]">(opsional)</span>
                        </label>
                        <select wire:model="waliKelasId" id="waliKelasId"
                                class="w-full border border-[#c5c5d7] rounded-lg px-4 py-2.5 text-[14px] text-on-surface focus:outline-none focus:ring-1 focus:ring-[#3c50e0] transition-shadow cursor-pointer">
                            <option value="">— Belum ditentukan —</option>
                            @forelse ($gurus as $g)
                                <option value="{{ $g->id }}">{{ $g->nama_lengkap }} ({{ $g->nip }})</option>
                            @empty
                                <option value="" disabled>Belum ada guru terdaftar</option>
                            @endforelse
                        </select>
                    </div>

                    {{-- Preview nama otomatis --}}
                    @if ($periodeAjaranId && $wilayahId && $paketId && $tingkatId)
                        @php
                            $prevW  = $wilayahs->firstWhere('id', $wilayahId)?->nama ?? '?';
                            $prevP  = $pakets->firstWhere('id', $paketId)?->nama ?? '?';
                            $prevT  = $tingkats->firstWhere('id', $tingkatId)?->nama ?? '?';
                            $prevTA = $periodes->firstWhere('id', $periodeAjaranId)?->tahun_ajaran ?? '?';
                        @endphp
                        <div class="flex items-start gap-2 p-3 rounded-lg bg-[#EEF2FF] border border-[#c5d0ff]">
                            <span class="material-symbols-outlined text-[#3c50e0] text-[16px] flex-shrink-0 mt-0.5">auto_awesome</span>
                            <div>
                                <p class="text-[11px] font-semibold text-[#3c50e0] uppercase tracking-wide">Nama Rombel (otomatis)</p>
                                <p class="text-[13px] text-[#1c33c8] font-medium mt-0.5">
                                    {{ $prevT }} {{ $prevW }} {{ $prevP }} – TA {{ $prevTA }}
                                </p>
                            </div>
                        </div>
                    @endif

                    <div class="flex justify-end gap-3 pt-2 border-t border-[#c5c5d7] mt-1">
                        <button type="button" wire:click="closeForm"
                                class="px-4 py-2.5 rounded-lg border border-[#c5c5d7] text-[#505f76] text-[14px] font-medium hover:bg-[#f0f4f8] transition-colors cursor-pointer">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-4 py-2.5 rounded-lg bg-[#3c50e0] text-white text-[14px] font-medium hover:bg-[#1c33c8] transition-colors shadow-sm flex items-center gap-2 cursor-pointer"
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
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
            <div class="bg-white rounded-xl shadow-md w-full max-w-sm border border-[#c5c5d7] p-6 flex flex-col gap-4">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full bg-[#ffdad6] flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-[#ba1a1a]">delete</span>
                    </div>
                    <div>
                        <h4 class="text-[16px] font-semibold text-on-surface">Hapus Rombel?</h4>
                        <p class="text-[14px] text-[#505f76] mt-1">Tindakan ini tidak dapat dibatalkan.</p>
                    </div>
                </div>
                <div class="flex justify-end gap-3">
                    <button wire:click="$set('confirmDeleteId', null)"
                            class="px-4 py-2 rounded-lg border border-[#c5c5d7] text-[14px] text-[#505f76] hover:bg-[#f0f4f8] transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button wire:click="delete"
                            class="px-4 py-2 rounded-lg bg-[#ba1a1a] text-white text-[14px] font-medium hover:bg-[#93000a] transition-colors cursor-pointer">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ── Modal Kelola Anggota ─────────────────────────────────────────────── --}}
    @if ($kelolaRombelId && $rombelKelola)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
            <div class="bg-white rounded-xl shadow-md w-full max-w-2xl border border-[#c5c5d7] flex flex-col max-h-[90vh]">

                {{-- Header --}}
                <div class="flex items-center justify-between p-6 border-b border-[#c5c5d7] flex-shrink-0">
                    <div>
                        <h3 class="text-[18px] font-semibold text-on-surface">Kelola Anggota Rombel</h3>
                        <p class="text-[13px] text-[#505f76] mt-0.5">{{ $rombelKelola->nama }}</p>
                    </div>
                    <button wire:click="closeKelolaMember" class="text-[#505f76] hover:text-[#ba1a1a] transition-colors p-1 cursor-pointer">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto p-6 flex flex-col gap-6">

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

                {{-- Footer --}}
                <div class="flex justify-end p-4 border-t border-[#c5c5d7] flex-shrink-0">
                    <button wire:click="closeKelolaMember"
                            class="px-5 py-2.5 rounded-lg border border-[#c5c5d7] text-[#505f76] text-[14px] font-medium hover:bg-[#f0f4f8] transition-colors cursor-pointer">
                        Selesai
                    </button>
                </div>

            </div>
        </div>
    @endif

    {{-- ── Tabel ───────────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm overflow-hidden">

        <x-table-controls searchPlaceholder="Cari nama rombel...">
            <x-slot:filters>
                {{-- Filter Wilayah --}}
                <select wire:model.live="filterWilayahId"
                        class="border border-[#c5c5d7] rounded-lg px-3 py-2 text-[14px] text-on-surface bg-white focus:outline-none focus:ring-1 focus:ring-[#3c50e0] cursor-pointer transition-shadow flex-shrink-0">
                    <option value="">Semua Wilayah</option>
                    @foreach ($wilayahs as $w)
                        <option value="{{ $w->id }}">{{ $w->nama }}</option>
                    @endforeach
                </select>

                {{-- Filter Paket --}}
                <select wire:model.live="filterPaketId"
                        class="border border-[#c5c5d7] rounded-lg px-3 py-2 text-[14px] text-on-surface bg-white focus:outline-none focus:ring-1 focus:ring-[#3c50e0] cursor-pointer transition-shadow flex-shrink-0">
                    <option value="">Semua Paket</option>
                    @foreach ($pakets as $p)
                        <option value="{{ $p->id }}">{{ $p->nama }}</option>
                    @endforeach
                </select>

                {{-- Filter Periode Ajaran --}}
                <select wire:model.live="filterPeriodeId"
                        class="border border-[#c5c5d7] rounded-lg px-3 py-2 text-[14px] text-on-surface bg-white focus:outline-none focus:ring-1 focus:ring-[#3c50e0] cursor-pointer transition-shadow flex-shrink-0">
                    <option value="">Semua Periode</option>
                    @foreach ($periodes as $p)
                        <option value="{{ $p->id }}">{{ $p->tahun_ajaran }} {{ $p->semester }}</option>
                    @endforeach
                </select>
            </x-slot:filters>
        </x-table-controls>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-[#f0f4f8] border-b border-[#c5c5d7] text-[12px] font-semibold text-[#505f76] uppercase tracking-wider">
                        <th class="px-6 py-4 w-12">No.</th>
                        <th class="px-6 py-4">Nama Rombel</th>
                        <th class="px-6 py-4">Wali Kelas</th>
                        <th class="px-6 py-4 text-center">Anggota</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-[14px] text-on-surface divide-y divide-[#c5c5d7]">
                    @forelse ($rombels as $i => $r)
                        <tr class="hover:bg-[#f6fafe] transition-colors">
                            <td class="px-6 py-4 text-[#505f76]">{{ $rombels->firstItem() + $i }}</td>
                            <td class="px-6 py-4">
                                <p class="font-medium">{{ $r->nama }}</p>
                                <p class="text-[12px] text-[#505f76] mt-0.5">
                                    TA {{ $r->periodeAjaran?->tahun_ajaran }} {{ $r->periodeAjaran?->semester }} &bull; {{ $r->paket?->nama }} &bull; {{ $r->wilayah?->nama }}
                                </p>
                            </td>
                            <td class="px-6 py-4">
                                @if ($r->waliKelas)
                                    <p class="text-[14px] font-medium">{{ $r->waliKelas->nama_lengkap }}</p>
                                    <p class="text-[12px] text-[#505f76]">{{ $r->waliKelas->nip }}</p>
                                @else
                                    <span class="text-[13px] text-amber-600 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-full inline-flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[12px]">warning</span>
                                        Belum ditentukan
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button wire:click="openKelolaMember({{ $r->id }})"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[12px] font-semibold transition-colors cursor-pointer
                                               {{ $r->peserta_didik_rombel_count > 0
                                                   ? 'bg-[#EEF2FF] text-[#1c33c8] hover:bg-[#3c50e0] hover:text-white'
                                                   : 'bg-[#eaeef2] text-[#505f76] hover:bg-[#3c50e0] hover:text-white' }}"
                                        title="Kelola anggota">
                                    <span class="material-symbols-outlined text-[14px]">group</span>
                                    {{ $r->peserta_didik_rombel_count }} orang
                                </button>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:click="openEditForm({{ $r->id }})"
                                            class="p-1.5 text-[#505f76] hover:text-[#1c33c8] hover:bg-[#eaeef2] rounded-lg transition-colors cursor-pointer"
                                            title="Edit">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </button>
                                    <button wire:click="confirmDelete({{ $r->id }})"
                                            class="p-1.5 text-[#505f76] hover:text-[#ba1a1a] hover:bg-[#ffdad6] rounded-lg transition-colors cursor-pointer"
                                            title="Hapus">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                @if ($search || $filterWilayahId || $filterPaketId || $filterPeriodeId)
                                    <span class="material-symbols-outlined text-[48px] text-[#c5c5d7] mb-3 block">search_off</span>
                                    <p class="text-[14px] text-[#505f76]">Tidak ada data ditemukan.</p>
                                    <button wire:click="$set('search', '')"
                                            class="mt-3 text-[13px] text-[#3c50e0] hover:underline cursor-pointer">
                                        Hapus Filter
                                    </button>
                                @else
                                    <span class="material-symbols-outlined text-[48px] text-[#c5c5d7] mb-3 block">groups</span>
                                    <p class="text-[14px] text-[#505f76]">Belum ada data Rombel.</p>
                                    <button wire:click="openCreateForm"
                                            class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[#3c50e0] text-white text-[14px] font-medium hover:bg-[#1c33c8] transition-colors cursor-pointer">
                                        <span class="material-symbols-outlined text-[16px]">add</span>
                                        Tambah Sekarang
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($rombels->total() > 0)
            <div class="px-6 py-4 border-t border-[#c5c5d7] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <p class="text-[13px] text-[#505f76]">
                    Menampilkan {{ $rombels->firstItem() }}–{{ $rombels->lastItem() }} dari {{ $rombels->total() }} data
                </p>
                <div class="text-[13px]">{{ $rombels->links() }}</div>
            </div>
        @endif

    </div>

</div>
