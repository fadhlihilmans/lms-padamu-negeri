<div>

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
        <div>
            <h1 class="text-[18px] font-bold" style="color: #171c1f">Jadwal Pelajaran</h1>
            <p class="text-[13px] mt-0.5" style="color: #757686">
                {{ $guru?->nama_lengkap ?? 'Guru' }}
                @if ($rombelWaliKelas)
                    &bull; <span style="color: #3c50e0">Wali Kelas {{ $rombelWaliKelas->nama }}</span>
                @endif
            </p>
        </div>
        @if ($rombelWaliKelas && $pemetaanWk->isNotEmpty())
            <button wire:click="openCreateForm"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-lg text-[13.5px] font-semibold text-white transition-colors cursor-pointer self-start sm:self-auto bg-[#3c50e0] hover:bg-[#2e3eb0] shadow-sm">
                <span class="material-symbols-outlined text-[17px]">add</span>Tambah Jadwal Rombel
            </button>
        @endif
    </div>

    {{-- ── Modal Form (Wali Kelas, 4.2) ──────────────────────────────────── --}}
    @if ($showForm)
        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4"
             style="background: rgba(0,0,0,0.5); backdrop-filter: blur(3px); -webkit-backdrop-filter: blur(3px)"
             wire:keydown.escape="closeForm">
            <div class="relative z-10 w-full sm:max-w-lg bg-white rounded-t-2xl sm:rounded-xl overflow-hidden shadow-xl flex flex-col max-h-[92dvh] sm:max-h-[88vh]"
                 style="box-shadow: 0 20px 60px -10px rgba(0,0,0,0.25)">

                <div class="flex items-center justify-between px-5 py-4 border-b flex-shrink-0" style="border-color: #c5c5d7">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: #EEF2FF">
                            <span class="material-symbols-outlined text-[18px]" style="color: #3c50e0">schedule</span>
                        </div>
                        <h3 class="text-[15px] font-semibold" style="color: #171c1f">{{ $editId ? 'Edit Jadwal' : 'Tambah Jadwal' }}</h3>
                    </div>
                    <button type="button" wire:click="closeForm"
                            class="w-8 h-8 flex items-center justify-center rounded-lg cursor-pointer transition-colors" style="color: #757686"
                            onmouseover="this.style.background='#f0f4f8'" onmouseout="this.style.background='transparent'">
                        <span class="material-symbols-outlined text-[18px]">close</span>
                    </button>
                </div>

                <form wire:submit="save" class="flex flex-col flex-1 min-h-0">
                    <div class="px-5 py-5 space-y-4 overflow-y-auto flex-1">

                        <div class="flex items-start gap-2.5 p-3.5 rounded-lg border" style="background: #EEF2FF; border-color: #c5d0ff">
                            <span class="material-symbols-outlined text-[17px] flex-shrink-0 mt-0.5" style="color: #3c50e0">groups</span>
                            <p class="text-[13px]" style="color: #1c33c8">{{ $rombelWaliKelas?->nama ?? '—' }}</p>
                        </div>

                        <div>
                            <label for="gmrId" class="block text-[13px] font-medium mb-1.5" style="color: #171c1f">Guru &amp; Mata Pelajaran <span style="color: #ba1a1a">*</span></label>
                            <select wire:model="gmrId" id="gmrId"
                                    class="w-full px-3 py-2.5 rounded-lg text-[13.5px] bg-white outline-none cursor-pointer transition-all"
                                    style="border: 1.5px solid {{ $errors->has('gmrId') ? '#ba1a1a' : '#c5c5d7' }}; color: #171c1f"
                                    onfocus="this.style.borderColor='#3c50e0'; this.style.boxShadow='0 0 0 3px rgba(60,80,224,0.1)'"
                                    onblur="this.style.borderColor='{{ $errors->has('gmrId') ? '#ba1a1a' : '#c5c5d7' }}'; this.style.boxShadow='none'">
                                <option value="" selected>Pilih Guru & Mapel</option>
                                @foreach ($pemetaanWk as $p)
                                    <option value="{{ $p->id }}">{{ $p->mapel->nama }} — {{ $p->guru->nama_lengkap }}</option>
                                @endforeach
                            </select>
                            @error('gmrId')
                                <p class="text-[12px] mt-1.5" style="color: #ba1a1a">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="hari" class="block text-[13px] font-medium mb-1.5" style="color: #171c1f">Hari <span style="color: #ba1a1a">*</span></label>
                                <select wire:model="hari" id="hari"
                                        class="w-full px-3 py-2.5 rounded-lg text-[13.5px] bg-white outline-none cursor-pointer transition-all"
                                        style="border: 1.5px solid {{ $errors->has('hari') ? '#ba1a1a' : '#c5c5d7' }}; color: #171c1f"
                                        onfocus="this.style.borderColor='#3c50e0'; this.style.boxShadow='0 0 0 3px rgba(60,80,224,0.1)'"
                                        onblur="this.style.borderColor='{{ $errors->has('hari') ? '#ba1a1a' : '#c5c5d7' }}'; this.style.boxShadow='none'">
                                    <option value="" selected>Pilih hari...</option>
                                    @foreach (['senin','selasa','rabu','kamis','jumat','sabtu','minggu'] as $h)
                                        <option value="{{ $h }}">{{ ucfirst($h) }}</option>
                                    @endforeach
                                </select>
                                @error('hari')
                                    <p class="text-[12px] mt-1.5" style="color: #ba1a1a">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-[13px] font-medium mb-1.5" style="color: #171c1f">Jam <span style="color: #ba1a1a">*</span></label>
                                <div class="flex items-center gap-2">
                                    <input wire:model="jamMulai" type="time"
                                           class="flex-1 px-3 py-2.5 rounded-lg text-[13.5px] bg-white outline-none cursor-pointer transition-all"
                                           style="border: 1.5px solid {{ $errors->has('jamMulai') ? '#ba1a1a' : '#c5c5d7' }}; color: #171c1f"
                                           onfocus="this.style.borderColor='#3c50e0'; this.style.boxShadow='0 0 0 3px rgba(60,80,224,0.1)'"
                                           onblur="this.style.borderColor='{{ $errors->has('jamMulai') ? '#ba1a1a' : '#c5c5d7' }}'; this.style.boxShadow='none'">
                                    <span class="text-[12px] flex-shrink-0" style="color: #757686">s/d</span>
                                    <input wire:model="jamSelesai" type="time"
                                           class="flex-1 px-3 py-2.5 rounded-lg text-[13.5px] bg-white outline-none cursor-pointer transition-all"
                                           style="border: 1.5px solid {{ $errors->has('jamSelesai') ? '#ba1a1a' : '#c5c5d7' }}; color: #171c1f"
                                           onfocus="this.style.borderColor='#3c50e0'; this.style.boxShadow='0 0 0 3px rgba(60,80,224,0.1)'"
                                           onblur="this.style.borderColor='{{ $errors->has('jamSelesai') ? '#ba1a1a' : '#c5c5d7' }}'; this.style.boxShadow='none'">
                                </div>
                                @error('jamMulai')
                                    <p class="text-[12px] mt-1.5" style="color: #ba1a1a">{{ $message }}</p>
                                @enderror
                                @error('jamSelesai')
                                    <p class="text-[12px] mt-1.5" style="color: #ba1a1a">{{ $message }}</p>
                                @enderror
                            </div>
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
                        <h4 class="text-[15px] font-semibold" style="color: #171c1f">Hapus jadwal ini?</h4>
                        <p class="text-[13px] mt-0.5" style="color: #505f76">Jadwal akan dihapus permanen.</p>
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
                                            <tr class="bg-white border-b border-[#c5c5d7] text-[11px] font-semibold text-[#505f76] uppercase tracking-wider">
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
