<div>

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-[24px] font-bold tracking-tight text-on-surface">Jadwal Pelajaran</h2>
            <p class="text-[14px] text-[#505f76] mt-0.5">
                {{ $guru?->nama_lengkap ?? 'Guru' }}
                @if ($rombelWaliKelas)
                    &bull; <span class="text-[#3c50e0]">Wali Kelas {{ $rombelWaliKelas->nama }}</span>
                @endif
            </p>
        </div>
        @if ($rombelWaliKelas && $pemetaanWk->isNotEmpty())
            <button wire:click="openCreateForm"
                    class="inline-flex items-center gap-2 bg-[#3c50e0] text-white text-[14px] font-medium px-4 py-2.5 rounded-lg hover:bg-[#1c33c8] transition-colors shadow-sm cursor-pointer flex-shrink-0">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Tambah Jadwal Rombel
            </button>
        @endif
    </div>

    {{-- ── Modal Form (Wali Kelas) ─────────────────────────────────────────── --}}
    @if ($showForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
            <div class="bg-white rounded-xl shadow-md w-full max-w-lg border border-[#c5c5d7]">
                <div class="flex items-center justify-between p-6 border-b border-[#c5c5d7]">
                    <h3 class="text-[20px] font-semibold text-on-surface">{{ $editId ? 'Edit Jadwal' : 'Tambah Jadwal' }}</h3>
                    <button wire:click="closeForm" class="text-[#505f76] hover:text-[#ba1a1a] transition-colors p-1 cursor-pointer">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <form wire:submit="save" class="p-6 flex flex-col gap-4">

                    <div class="p-3 rounded-lg bg-[#EEF2FF] border border-[#c5d0ff] text-[13px] text-[#1c33c8] font-medium">
                        <span class="material-symbols-outlined text-[14px] align-middle">groups</span>
                        {{ $rombelWaliKelas?->nama ?? '—' }}
                    </div>

                    {{-- Pemetaan Guru–Mapel --}}
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[14px] font-medium text-on-surface" for="gmrId">
                            Guru &amp; Mata Pelajaran <span class="text-[#ba1a1a]">*</span>
                        </label>
                        <select wire:model="gmrId" id="gmrId"
                                class="w-full border rounded-lg px-4 py-2.5 text-[14px] text-on-surface focus:outline-none focus:ring-1 transition-shadow cursor-pointer
                                       {{ $errors->has('gmrId') ? 'border-[#ba1a1a] focus:ring-[#ba1a1a]' : 'border-[#c5c5d7] focus:ring-[#3c50e0] focus:border-[#3c50e0]' }}">
                            <option value="">— Pilih Guru & Mapel —</option>
                            @foreach ($pemetaanWk as $p)
                                <option value="{{ $p->id }}">{{ $p->mapel->nama }} — {{ $p->guru->nama_lengkap }}</option>
                            @endforeach
                        </select>
                        @error('gmrId') <p class="text-[12px] text-[#ba1a1a]">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[14px] font-medium text-on-surface" for="hari">
                                Hari <span class="text-[#ba1a1a]">*</span>
                            </label>
                            <select wire:model="hari" id="hari"
                                    class="w-full border rounded-lg px-4 py-2.5 text-[14px] text-on-surface focus:outline-none focus:ring-1 transition-shadow cursor-pointer
                                           {{ $errors->has('hari') ? 'border-[#ba1a1a] focus:ring-[#ba1a1a]' : 'border-[#c5c5d7] focus:ring-[#3c50e0] focus:border-[#3c50e0]' }}">
                                <option value="">— Pilih —</option>
                                @foreach (['senin','selasa','rabu','kamis','jumat','sabtu','minggu'] as $h)
                                    <option value="{{ $h }}">{{ ucfirst($h) }}</option>
                                @endforeach
                            </select>
                            @error('hari') <p class="text-[12px] text-[#ba1a1a]">{{ $message }}</p> @enderror
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[14px] font-medium text-on-surface" for="jamMulai">
                                Jam Mulai <span class="text-[#ba1a1a]">*</span>
                            </label>
                            <input wire:model="jamMulai" id="jamMulai" type="time"
                                   class="w-full border rounded-lg px-4 py-2.5 text-[14px] focus:outline-none focus:ring-1 transition-shadow cursor-pointer
                                          {{ $errors->has('jamMulai') ? 'border-[#ba1a1a] focus:ring-[#ba1a1a]' : 'border-[#c5c5d7] focus:ring-[#3c50e0]' }}">
                            @error('jamMulai') <p class="text-[12px] text-[#ba1a1a]">{{ $message }}</p> @enderror
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[14px] font-medium text-on-surface" for="jamSelesai">
                                Jam Selesai <span class="text-[#ba1a1a]">*</span>
                            </label>
                            <input wire:model="jamSelesai" id="jamSelesai" type="time"
                                   class="w-full border rounded-lg px-4 py-2.5 text-[14px] focus:outline-none focus:ring-1 transition-shadow cursor-pointer
                                          {{ $errors->has('jamSelesai') ? 'border-[#ba1a1a] focus:ring-[#ba1a1a]' : 'border-[#c5c5d7] focus:ring-[#3c50e0]' }}">
                            @error('jamSelesai') <p class="text-[12px] text-[#ba1a1a]">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-2 border-t border-[#c5c5d7] mt-1">
                        <button type="button" wire:click="closeForm"
                                class="px-4 py-2.5 rounded-lg border border-[#c5c5d7] text-[#505f76] text-[14px] font-medium hover:bg-[#f0f4f8] transition-colors cursor-pointer">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-4 py-2.5 rounded-lg bg-[#3c50e0] text-white text-[14px] font-medium hover:bg-[#1c33c8] transition-colors flex items-center gap-2 cursor-pointer"
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
                        <h4 class="text-[16px] font-semibold text-on-surface">Hapus jadwal ini?</h4>
                        <p class="text-[14px] text-[#505f76] mt-1">Jadwal akan dihapus permanen.</p>
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

    {{-- ── Jadwal Mengajar Pribadi ──────────────────────────────────────────── --}}
    <div class="mb-6">
        <h3 class="text-[16px] font-semibold text-on-surface mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px] text-[#3c50e0]">person_play</span>
            Jadwal Mengajar Saya
        </h3>

        @if ($jadwalPribadi->isEmpty())
            <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm px-6 py-12 text-center">
                <span class="material-symbols-outlined text-[48px] text-[#c5c5d7] mb-3 block">event_busy</span>
                <p class="text-[14px] text-[#505f76]">Belum ada jadwal mengajar yang tercatat.</p>
            </div>
        @else
            <div class="flex flex-col gap-3">
                @foreach ($hariOrder as $h)
                    @if ($jadwalPribadi->has($h))
                        <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm overflow-hidden">
                            <div class="px-5 py-2.5 bg-[#EEF2FF] border-b border-[#c5d0ff]">
                                <p class="text-[13px] font-bold text-[#1c33c8] uppercase tracking-wide">{{ $hariLabel[$h] }}</p>
                            </div>
                            <div class="divide-y divide-[#c5c5d7]">
                                @foreach ($jadwalPribadi[$h] as $jadwal)
                                    <div class="flex items-center gap-4 px-5 py-3">
                                        <span class="font-mono text-[13px] text-[#1c33c8] font-semibold w-28 flex-shrink-0">
                                            {{ substr($jadwal->jam_mulai, 0, 5) }} – {{ substr($jadwal->jam_selesai, 0, 5) }}
                                        </span>
                                        <div>
                                            <p class="text-[14px] font-medium text-on-surface">
                                                {{ $jadwal->guruMapelRombel?->mapel?->nama ?? '—' }}
                                            </p>
                                            <p class="text-[12px] text-[#505f76]">
                                                {{ $jadwal->guruMapelRombel?->rombel?->nama ?? '—' }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>

    {{-- ── Jadwal Rombel (Wali Kelas) ───────────────────────────────────────── --}}
    @if ($rombelWaliKelas)
        <div>
            <h3 class="text-[16px] font-semibold text-on-surface mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px] text-[#3c50e0]">groups</span>
                Jadwal Rombel — {{ $rombelWaliKelas->nama }}
            </h3>

            @if ($jadwalWkByHari->isEmpty())
                <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm px-6 py-12 text-center">
                    <span class="material-symbols-outlined text-[48px] text-[#c5c5d7] mb-3 block">calendar_today</span>
                    <p class="text-[14px] text-[#505f76]">Belum ada jadwal untuk rombel ini.</p>
                    @if ($pemetaanWk->isNotEmpty())
                        <button wire:click="openCreateForm"
                                class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[#3c50e0] text-white text-[14px] font-medium hover:bg-[#1c33c8] transition-colors cursor-pointer">
                            <span class="material-symbols-outlined text-[16px]">add</span>
                            Tambah Jadwal
                        </button>
                    @endif
                </div>
            @else
                <div class="flex flex-col gap-3">
                    @foreach ($hariOrder as $h)
                        @if ($jadwalWkByHari->has($h))
                            <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm overflow-hidden">
                                <div class="px-5 py-2.5 bg-[#EEF2FF] border-b border-[#c5d0ff] flex items-center justify-between">
                                    <p class="text-[13px] font-bold text-[#1c33c8] uppercase tracking-wide">{{ $hariLabel[$h] }}</p>
                                    <span class="text-[12px] text-[#3c50e0]">{{ $jadwalWkByHari[$h]->count() }} sesi</span>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left">
                                        <thead>
                                            <tr class="bg-[#f0f4f8] border-b border-[#c5c5d7] text-[11px] font-semibold text-[#505f76] uppercase tracking-wider">
                                                <th class="px-5 py-3 w-36">Waktu</th>
                                                <th class="px-5 py-3">Mata Pelajaran</th>
                                                <th class="px-5 py-3">Guru</th>
                                                <th class="px-5 py-3 text-right">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-[#c5c5d7] text-[14px]">
                                            @foreach ($jadwalWkByHari[$h] as $jadwal)
                                                <tr class="hover:bg-[#f6fafe] transition-colors">
                                                    <td class="px-5 py-3 font-mono text-[13px] text-[#1c33c8] font-semibold">
                                                        {{ substr($jadwal->jam_mulai, 0, 5) }} – {{ substr($jadwal->jam_selesai, 0, 5) }}
                                                    </td>
                                                    <td class="px-5 py-3">{{ $jadwal->guruMapelRombel?->mapel?->nama ?? '—' }}</td>
                                                    <td class="px-5 py-3 text-[#505f76]">{{ $jadwal->guruMapelRombel?->guru?->nama_lengkap ?? '—' }}</td>
                                                    <td class="px-5 py-3">
                                                        <div class="flex items-center justify-end gap-1">
                                                            <button wire:click="openEditForm({{ $jadwal->id }})"
                                                                    class="p-1.5 text-[#505f76] hover:text-[#1c33c8] hover:bg-[#eaeef2] rounded-lg transition-colors cursor-pointer">
                                                                <span class="material-symbols-outlined text-[18px]">edit</span>
                                                            </button>
                                                            <button wire:click="confirmDelete({{ $jadwal->id }})"
                                                                    class="p-1.5 text-[#505f76] hover:text-[#ba1a1a] hover:bg-[#ffdad6] rounded-lg transition-colors cursor-pointer">
                                                                <span class="material-symbols-outlined text-[18px]">delete</span>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    @endif

</div>
