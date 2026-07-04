<div>

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-[18px] font-bold text-on-surface">Pemetaan Guru – Mapel – Rombel</h2>
            <p class="text-[13px] text-[#757686] mt-0.5">Tentukan guru yang mengajar mata pelajaran tertentu di setiap rombel dan periode.</p>
        </div>
        @if ($filterRombelId && $filterPeriodeId)
            <button wire:click="openAddForm"
                    class="inline-flex items-center gap-2 bg-[#3c50e0] text-white text-[14px] font-medium px-4 py-2.5 rounded-lg hover:bg-[#1c33c8] transition-colors shadow-sm cursor-pointer flex-shrink-0">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Tambah Pemetaan
            </button>
        @endif
    </div>

    {{-- ── Selector Konteks (Rombel + Periode) ─────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm p-5 mb-6">
        <p class="text-[13px] font-semibold text-[#505f76] uppercase tracking-wide mb-3">Filter Tampilan</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="flex flex-col gap-1.5">
                <label class="text-[14px] font-medium text-on-surface" for="filterRombelId">Rombel</label>
                <select wire:model.live="filterRombelId" id="filterRombelId"
                        class="w-full border border-[#c5c5d7] rounded-lg px-4 py-2.5 text-[14px] text-on-surface focus:outline-none focus:ring-1 focus:ring-[#3c50e0] cursor-pointer transition-shadow">
                    <option value="">— Pilih Rombel —</option>
                    @foreach ($rombels as $r)
                        <option value="{{ $r->id }}">{{ $r->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-col gap-1.5">
                <label class="text-[14px] font-medium text-on-surface" for="filterPeriodeId">Periode Ajaran</label>
                <select wire:model.live="filterPeriodeId" id="filterPeriodeId"
                        class="w-full border border-[#c5c5d7] rounded-lg px-4 py-2.5 text-[14px] text-on-surface focus:outline-none focus:ring-1 focus:ring-[#3c50e0] cursor-pointer transition-shadow">
                    <option value="">— Pilih Periode —</option>
                    @foreach ($periodes as $p)
                        <option value="{{ $p->id }}">
                            TA {{ $p->tahun_ajaran }} – {{ ucfirst($p->semester) }}
                            @if ($p->is_aktif) (Aktif) @endif
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- ── Modal Form Tambah ────────────────────────────────────────────────── --}}
    @if ($showForm)
        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/50 backdrop-blur-sm p-0 sm:p-4">
            <div class="relative bg-white rounded-t-2xl sm:rounded-xl shadow-xl shadow-black/10 w-full max-w-4xl sm:max-w-lg border border-[#c5c5d7]">
                <button wire:click="closeForm" type="button"
                        class="absolute top-4 right-4 text-[#505f76] hover:text-[#ba1a1a] transition-colors p-1 cursor-pointer z-10 bg-white rounded-full">
                    <span class="material-symbols-outlined">close</span>
                </button>
                <div class="p-6 sm:p-8 max-h-[90vh] overflow-y-auto">
                <div class="mb-6 border-b border-[#c5c5d7] pb-4 pr-8">
                    <h3 class="text-[20px] font-semibold text-on-surface">Tambah Pemetaan</h3>
                </div>
                <form wire:submit="save" class="flex flex-col gap-4">

                    {{-- Info konteks --}}
                    <div class="p-3 rounded-lg bg-[#f0f4f8] border border-[#c5c5d7] text-[13px] text-[#505f76]">
                        <p><strong>Rombel:</strong> {{ $rombels->firstWhere('id', $filterRombelId)?->nama ?? '—' }}</p>
                        <p class="mt-0.5"><strong>Periode:</strong>
                            @php $pd = $periodes->firstWhere('id', $filterPeriodeId); @endphp
                            {{ $pd ? "TA {$pd->tahun_ajaran} – " . ucfirst($pd->semester) : '—' }}
                        </p>
                    </div>

                    {{-- Guru --}}
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[14px] font-medium text-on-surface" for="guruId">
                            Guru <span class="text-[#ba1a1a]">*</span>
                        </label>
                        <select wire:model="guruId" id="guruId"
                                class="w-full border rounded-lg px-4 py-2.5 text-[14px] text-on-surface focus:outline-none focus:ring-1 transition-shadow cursor-pointer
                                       {{ $errors->has('guruId') ? 'border-[#ba1a1a] focus:ring-[#ba1a1a]' : 'border-[#c5c5d7] focus:ring-[#3c50e0] focus:border-[#3c50e0]' }}">
                            <option value="">— Pilih Guru —</option>
                            @forelse ($gurus as $g)
                                <option value="{{ $g->id }}">{{ $g->nama_lengkap }} ({{ $g->nip }})</option>
                            @empty
                                <option value="" disabled>Belum ada guru terdaftar</option>
                            @endforelse
                        </select>
                        @error('guruId')
                            <p class="text-[12px] text-[#ba1a1a]">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Mata Pelajaran --}}
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[14px] font-medium text-on-surface" for="mapelId">
                            Mata Pelajaran <span class="text-[#ba1a1a]">*</span>
                        </label>
                        <select wire:model="mapelId" id="mapelId"
                                class="w-full border rounded-lg px-4 py-2.5 text-[14px] text-on-surface focus:outline-none focus:ring-1 transition-shadow cursor-pointer
                                       {{ $errors->has('mapelId') ? 'border-[#ba1a1a] focus:ring-[#ba1a1a]' : 'border-[#c5c5d7] focus:ring-[#3c50e0] focus:border-[#3c50e0]' }}">
                            <option value="">— Pilih Mata Pelajaran —</option>
                            @foreach ($mapels as $m)
                                <option value="{{ $m->id }}">{{ $m->nama }}</option>
                            @endforeach
                        </select>
                        @error('mapelId')
                            <p class="text-[12px] text-[#ba1a1a]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-4 border-t border-[#c5c5d7] mt-2">
                        <button type="button" wire:click="closeForm"
                                class="w-full sm:w-auto px-4 py-2.5 rounded-lg border border-[#c5c5d7] text-[#505f76] text-[14px] font-medium hover:bg-[#f0f4f8] transition-colors cursor-pointer text-center">
                            Batal
                        </button>
                        <button type="submit"
                                class="w-full sm:w-auto px-4 py-2.5 rounded-lg bg-[#3c50e0] text-white text-[14px] font-medium hover:bg-[#1c33c8] transition-colors shadow-sm flex items-center justify-center gap-2 cursor-pointer"
                                wire:loading.attr="disabled" wire:loading.class="opacity-70 cursor-not-allowed">
                            <span wire:loading wire:target="save" class="material-symbols-outlined text-[16px] animate-spin">progress_activity</span>
                            Simpan Pemetaan
                        </button>
                    </div>
                </form>
                </div>{{-- end scrollable --}}
            </div>
        </div>
    @endif

    {{-- ── Modal Konfirmasi Hapus ───────────────────────────────────────────── --}}
    @if ($confirmDeleteId)
        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/50 backdrop-blur-sm p-0 sm:p-4">
            <div class="bg-white rounded-t-2xl sm:rounded-xl shadow-xl shadow-black/10 w-full max-w-4xl sm:max-w-sm border border-[#c5c5d7] p-6 flex flex-col gap-4">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full bg-[#ffdad6] flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-[#ba1a1a]">delete</span>
                    </div>
                    <div>
                        <h4 class="text-[16px] font-semibold text-on-surface">Hapus Pemetaan ini?</h4>
                        <p class="text-[14px] text-[#505f76] mt-1">Guru tidak lagi memiliki akses ke Materi, Tugas, dan CBT di rombel ini.</p>
                    </div>
                </div>
                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                    <button wire:click="$set('confirmDeleteId', null)"
                            class="w-full sm:w-auto px-4 py-2.5 rounded-lg border border-[#c5c5d7] text-[14px] text-[#505f76] hover:bg-[#f0f4f8] transition-colors cursor-pointer text-center">
                        Batal
                    </button>
                    <button wire:click="delete"
                            class="w-full sm:w-auto px-4 py-2.5 rounded-lg bg-[#ba1a1a] text-white text-[14px] font-medium hover:bg-[#93000a] transition-colors cursor-pointer text-center">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ── Konten Utama ─────────────────────────────────────────────────────── --}}
    @if (! $filterRombelId || ! $filterPeriodeId)
        {{-- Prompt: pilih rombel dan periode dulu --}}
        <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm px-6 py-20 text-center">
            <span class="material-symbols-outlined text-[56px] text-[#c5c5d7] mb-3 block">hub</span>
            <p class="text-[16px] font-semibold text-on-surface">Pilih Rombel dan Periode terlebih dahulu</p>
            <p class="text-[14px] text-[#505f76] mt-1">Gunakan filter di atas untuk melihat dan mengatur pemetaan guru.</p>
        </div>

    @else
        {{-- Tabel pemetaan --}}
        <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm overflow-hidden">

            <div class="px-4 py-3.5 border-b border-[#c5c5d7] bg-white flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <p class="text-[13px] font-semibold text-on-surface">
                        {{ $rombels->firstWhere('id', $filterRombelId)?->nama ?? '—' }}
                    </p>
                    @php $pd = $periodes->firstWhere('id', $filterPeriodeId); @endphp
                    <p class="text-[12px] text-[#505f76]">
                        TA {{ $pd?->tahun_ajaran }} – {{ ucfirst($pd?->semester ?? '') }}
                    </p>
                </div>
                <span class="text-[12px] font-semibold text-[#3c50e0] bg-[#EEF2FF] px-3 py-1 rounded-full flex-shrink-0">
                    {{ $pemetaan?->count() ?? 0 }} pemetaan
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-white border-b border-[#c5c5d7] text-[11.5px] font-semibold text-[#757686] uppercase tracking-wide">
                            <th class="px-4 py-3.5 w-12">No.</th>
                            <th class="px-4 py-3.5">Guru</th>
                            <th class="px-4 py-3.5">Mata Pelajaran</th>
                            <th class="px-4 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-[13.5px] text-on-surface divide-y divide-[#c5c5d7]">
                        @forelse ($pemetaan ?? [] as $i => $pm)
                            <tr class="hover:bg-[#f6fafe] transition-colors">
                                <td class="px-4 py-3.5 text-[#505f76]">{{ $i + 1 }}</td>
                                <td class="px-4 py-3.5">
                                    <p class="font-medium">{{ $pm->guru?->nama_lengkap ?? '—' }}</p>
                                    <p class="text-[12px] text-[#505f76]">NIP: {{ $pm->guru?->nip ?? '—' }}</p>
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[12px] font-semibold whitespace-nowrap bg-[#EEF2FF] text-[#1c33c8]">
                                        {{ $pm->mapel?->nama ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="flex justify-end">
                                        <button wire:click="confirmDelete({{ $pm->id }})"
                                                class="p-1.5 text-[#505f76] hover:text-[#ba1a1a] hover:bg-[#ffdad6] rounded-lg transition-colors cursor-pointer"
                                                title="Hapus pemetaan">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-16 text-center">
                                    <span class="material-symbols-outlined text-[48px] text-[#c5c5d7] mb-3 block">hub</span>
                                    <p class="text-[14px] text-[#505f76]">Belum ada pemetaan untuk rombel dan periode ini.</p>
                                    <button wire:click="openAddForm"
                                            class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[#3c50e0] text-white text-[14px] font-medium hover:bg-[#1c33c8] transition-colors cursor-pointer">
                                        <span class="material-symbols-outlined text-[16px]">add</span>
                                        Tambah Pemetaan
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    @endif

</div>
