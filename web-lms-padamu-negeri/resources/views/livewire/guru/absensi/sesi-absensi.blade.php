<div>

    {{-- Header --}}
    <div class="mb-6">
        <h2 class="text-[18px] font-bold text-on-surface">Absensi</h2>
        <p class="text-[13px] text-[#757686] mt-0.5">Kelola sesi absensi untuk kelas yang Anda ampu.</p>
    </div>

    {{-- Pilih Mapel-Rombel --}}
    <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm p-5 mb-5">
        <label class="block text-[13px] font-semibold text-[#505f76] uppercase tracking-wide mb-2">
            Pilih Mapel & Rombel
        </label>
        @if ($gmrs->isEmpty())
            <p class="text-[14px] text-[#757686] italic">Anda belum memiliki pemetaan mengajar pada periode aktif.</p>
        @else
            <select wire:model.live="selectedGmrId"
                    class="w-full max-w-4xl sm:max-w-md border border-[#c5c5d7] rounded-lg px-4 py-2.5 text-[14px] text-on-surface bg-white focus:outline-none focus:ring-1 focus:ring-[#3c50e0] cursor-pointer">
                <option value="">-- Pilih Mapel & Rombel --</option>
                @foreach ($gmrs as $gmr)
                    <option value="{{ $gmr->id }}">{{ $gmr->mapel->nama }} — {{ $gmr->rombel->nama }}</option>
                @endforeach
            </select>
        @endif
    </div>

    @if ($selectedGmrId)
        @if (! $sesi)

            {{-- Form Buka / Jadwalkan Sesi --}}
            <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm overflow-hidden mb-5">
                <div class="px-4 py-3.5 border-b border-[#c5c5d7] bg-[#f6fafe] flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-[#EEF2FF] flex items-center justify-center">
                        <span class="material-symbols-outlined text-[#3c50e0] text-[20px]">event_available</span>
                    </div>
                    <div>
                        <h3 class="text-[15px] font-semibold text-on-surface">Buka / Jadwalkan Sesi Absensi</h3>
                        <p class="text-[12px] text-[#505f76]">Atur waktu buka dan tutup. Peserta didik hanya bisa absen dalam rentang waktu tersebut.</p>
                    </div>
                </div>
                <div class="p-6 space-y-5">

                    {{-- Waktu Buka --}}
                    <div>
                        <p class="text-[11px] font-semibold text-[#757686] uppercase tracking-widest mb-3 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[13px]">play_circle</span>Waktu Buka Sesi
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[13px] font-medium text-on-surface mb-1.5">Tanggal Buka <span class="text-[#ba1a1a]">*</span></label>
                                <div class="relative">
                                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#505f76] text-[17px] pointer-events-none">calendar_today</span>
                                    <input wire:model="formTanggalBuka" type="date" min="{{ today()->format('Y-m-d') }}"
                                           class="w-full pl-10 pr-4 py-2.5 border border-[#c5c5d7] rounded-lg text-[14px] focus:outline-none focus:ring-2 focus:ring-[#3c50e0]/30 focus:border-[#3c50e0] cursor-pointer @error('formTanggalBuka') border-[#ba1a1a] @enderror">
                                </div>
                                @error('formTanggalBuka') <p class="text-[12px] text-[#ba1a1a] mt-1 flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span> {{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-[13px] font-medium text-on-surface mb-1.5">Jam Buka <span class="text-[#ba1a1a]">*</span></label>
                                <div class="relative">
                                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#505f76] text-[17px] pointer-events-none">schedule</span>
                                    <input wire:model="formJamBuka" type="time"
                                           class="w-full pl-10 pr-4 py-2.5 border border-[#c5c5d7] rounded-lg text-[14px] focus:outline-none focus:ring-2 focus:ring-[#3c50e0]/30 focus:border-[#3c50e0] cursor-pointer @error('formJamBuka') border-[#ba1a1a] @enderror">
                                </div>
                                @error('formJamBuka') <p class="text-[12px] text-[#ba1a1a] mt-1 flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span> {{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-dashed border-[#c5c5d7]"></div>

                    {{-- Waktu Tutup --}}
                    <div>
                        <p class="text-[11px] font-semibold text-[#757686] uppercase tracking-widest mb-3 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[13px]">stop_circle</span>Waktu Tutup Sesi
                            <span class="normal-case font-normal tracking-normal text-[#ba1a1a] ml-1">— harus lebih dari waktu buka</span>
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[13px] font-medium text-on-surface mb-1.5">Tanggal Tutup <span class="text-[#ba1a1a]">*</span></label>
                                <div class="relative">
                                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#505f76] text-[17px] pointer-events-none">calendar_today</span>
                                    <input wire:model="formTanggalTutup" type="date" min="{{ today()->format('Y-m-d') }}"
                                           class="w-full pl-10 pr-4 py-2.5 border border-[#c5c5d7] rounded-lg text-[14px] focus:outline-none focus:ring-2 focus:ring-[#3c50e0]/30 focus:border-[#3c50e0] cursor-pointer @error('formTanggalTutup') border-[#ba1a1a] @enderror">
                                </div>
                                @error('formTanggalTutup') <p class="text-[12px] text-[#ba1a1a] mt-1 flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span> {{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-[13px] font-medium text-on-surface mb-1.5">Jam Tutup <span class="text-[#ba1a1a]">*</span></label>
                                <div class="relative">
                                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#505f76] text-[17px] pointer-events-none">schedule</span>
                                    <input wire:model="formJamTutup" type="time"
                                           class="w-full pl-10 pr-4 py-2.5 border border-[#c5c5d7] rounded-lg text-[14px] focus:outline-none focus:ring-2 focus:ring-[#3c50e0]/30 focus:border-[#3c50e0] cursor-pointer @error('formJamTutup') border-[#ba1a1a] @enderror">
                                </div>
                                @error('formJamTutup') <p class="text-[12px] text-[#ba1a1a] mt-1 flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span> {{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="p-3 bg-[#f6fafe] rounded-lg border border-[#c5d0ff] flex items-start gap-2">
                        <span class="material-symbols-outlined text-[#3c50e0] text-[16px] flex-shrink-0 mt-0.5">info</span>
                        <p class="text-[12px] text-[#505f76]">
                            Jika jam buka di masa depan, sesi berstatus <strong>Terjadwal</strong> dan PD baru bisa absen setelah jam buka tercapai.
                            Sesi otomatis ditutup ketika jam tutup tercapai.
                        </p>
                    </div>

                    <div class="flex justify-end">
                        <button wire:click="openSession" wire:loading.attr="disabled"
                                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg bg-[#3c50e0] text-white text-[14px] font-semibold hover:bg-[#2e3eb0] transition-colors cursor-pointer disabled:opacity-60 shadow-sm">
                            <span wire:loading wire:target="openSession" class="material-symbols-outlined text-[16px] animate-spin">progress_activity</span>
                            <span class="material-symbols-outlined text-[18px]" wire:loading.class="hidden" wire:target="openSession">play_circle</span>
                            Buka / Jadwalkan Sesi
                        </button>
                    </div>
                </div>
            </div>

        @else
            @php
                $isScheduled = $sesi->tanggal_buka && $sesi->tanggal_buka->isFuture();
                $isActive    = $sesi->status_sesi === 'terbuka' && ! $isScheduled;
                $isClosed    = $sesi->status_sesi === 'ditutup';
                $tsBuka      = $sesi->tanggal_buka ? $sesi->tanggal_buka->timestamp : 0;
                $tsTutup     = $sesi->tutup_pada   ? $sesi->tutup_pada->timestamp   : 0;
            @endphp

            {{-- Header sesi --}}
            <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm p-5 mb-5">

                {{-- Baris: badge + nama + tombol --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        @if ($isScheduled)
                            <span class="flex items-center gap-1.5 px-3 py-1 rounded-full text-[12px] font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                                <span class="material-symbols-outlined text-[14px]">schedule</span>Terjadwal
                            </span>
                        @elseif ($isActive)
                            <span class="flex items-center gap-1.5 px-3 py-1 rounded-full text-[12px] font-semibold bg-green-100 text-green-800 border border-green-200">
                                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>Sesi Terbuka
                            </span>
                        @else
                            <span class="flex items-center gap-1.5 px-3 py-1 rounded-full text-[12px] font-semibold bg-[#f0f4f8] text-[#505f76] border border-[#c5c5d7]">
                                <span class="material-symbols-outlined text-[14px]">lock</span>Sesi Ditutup
                            </span>
                        @endif
                        <div>
                            <p class="text-[15px] font-semibold text-on-surface">{{ $sesi->guruMapelRombel->mapel->nama }}</p>
                            <p class="text-[13px] text-[#505f76]">{{ $sesi->guruMapelRombel->rombel->nama }} · {{ $sesi->tanggal->translatedFormat('l, d F Y') }}</p>
                        </div>
                    </div>
                    @if (! $isClosed)
                        <button wire:click="requestCloseSession"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-[#ba1a1a] text-[#ba1a1a] text-[14px] font-medium hover:bg-[#ffdad6] transition-colors cursor-pointer">
                            <span class="material-symbols-outlined text-[18px]">stop_circle</span>
                            {{ $isScheduled ? 'Batalkan Sesi' : 'Tutup Sesi' }}
                        </button>
                    @endif
                </div>

                {{-- Panel waktu --}}
                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">

                    {{-- Waktu buka --}}
                    @if ($sesi->tanggal_buka)
                        @if ($isScheduled)
                            <div x-data="{
                                    ts: {{ $tsBuka }},
                                    label: '--:--',
                                    init() { this.tick(); setInterval(() => this.tick(), 1000); },
                                    tick() {
                                        var diff = this.ts - Math.floor(Date.now() / 1000);
                                        if (diff <= 0) { this.label = 'Segera dibuka...'; return; }
                                        var h = Math.floor(diff / 3600);
                                        var m = Math.floor((diff % 3600) / 60);
                                        var s = diff % 60;
                                        this.label = (h > 0 ? h + ' jam ' : '') + String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
                                    }
                                 }"
                                 class="flex items-center gap-3 px-4 py-3 rounded-xl border bg-amber-50 border-amber-200 text-[13px]">
                                <span class="material-symbols-outlined text-amber-600 text-[20px] flex-shrink-0">event_upcoming</span>
                                <div>
                                    <p class="font-semibold text-on-surface">Dibuka pada: {{ $sesi->tanggal_buka->translatedFormat('d M Y, H:i') }} WIB</p>
                                    <p class="font-mono font-bold text-[14px] mt-0.5 text-amber-700" x-text="'Mulai dalam: ' + label"></p>
                                </div>
                            </div>
                        @else
                            <div class="flex items-center gap-3 px-4 py-3 rounded-xl border bg-[#f6fafe] border-[#c5d0ff] text-[#505f76] text-[13px]">
                                <span class="material-symbols-outlined text-[#3c50e0] text-[20px] flex-shrink-0">play_circle</span>
                                <div>
                                    <p class="font-semibold text-on-surface">Sesi Dibuka</p>
                                    <p>{{ $sesi->tanggal_buka->translatedFormat('d M Y, H:i') }} WIB</p>
                                </div>
                            </div>
                        @endif
                    @endif

                    {{-- Waktu tutup --}}
                    @if ($sesi->tutup_pada)
                        @if ($isClosed)
                            <div class="flex items-center gap-3 px-4 py-3 rounded-xl border bg-[#ffdad6] border-[#ffb4ab] text-[#ba1a1a] text-[13px]">
                                <span class="material-symbols-outlined text-[20px] flex-shrink-0">timer_off</span>
                                <div>
                                    <p class="font-semibold">Sesi Ditutup</p>
                                    <p>{{ $sesi->tutup_pada->translatedFormat('d M Y, H:i') }} WIB</p>
                                </div>
                            </div>
                        @else
                            <div x-data="{
                                    dl: {{ $tsTutup }},
                                    rem: '--:--',
                                    urg: false,
                                    init() { this.tick(); setInterval(() => this.tick(), 1000); },
                                    tick() {
                                        var diff = this.dl - Math.floor(Date.now() / 1000);
                                        if (diff <= 0) { this.rem = 'Waktu habis'; this.urg = true; return; }
                                        var h = Math.floor(diff / 3600);
                                        var m = Math.floor((diff % 3600) / 60);
                                        var s = diff % 60;
                                        this.urg = diff < 300;
                                        this.rem = (h > 0 ? h + ' jam ' : '') + String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
                                    }
                                 }"
                                 :class="urg ? 'bg-[#ffdad6] border-[#ffb4ab] text-[#ba1a1a]' : 'bg-[#fff3cd] border-[#ffd966] text-[#856404]'"
                                 class="flex items-center gap-3 px-4 py-3 rounded-xl border text-[13px] transition-colors">
                                <span class="material-symbols-outlined text-[20px] flex-shrink-0">timer</span>
                                <div>
                                    <p class="font-semibold text-on-surface">Tutup: {{ $sesi->tutup_pada->translatedFormat('d M Y, H:i') }} WIB</p>
                                    <p class="font-mono font-bold text-[15px] mt-0.5" x-text="'Sisa: ' + rem"></p>
                                </div>
                            </div>
                        @endif
                    @endif

                </div>
            </div>

            {{-- Placeholder saat terjadwal --}}
            @if ($isScheduled)
                <div class="bg-amber-50 border border-amber-200 rounded-xl px-6 py-10 text-center mb-5">
                    <span class="material-symbols-outlined text-[40px] text-amber-400 mb-2 block">event_upcoming</span>
                    <p class="text-[15px] font-semibold text-amber-800 mb-1">Sesi Belum Dimulai</p>
                    <p class="text-[13px] text-amber-700">
                        Daftar kehadiran muncul setelah sesi dibuka pada
                        <strong>{{ $sesi->tanggal_buka->translatedFormat('d M Y, H:i') }} WIB</strong>.
                    </p>
                </div>
            @endif

            {{-- Stats + Roster (hanya saat aktif atau ditutup) --}}
            @if (! $isScheduled)
                <div wire:poll.10000ms class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-5">
                    @php
                        $statCards = [
                            ['label' => 'Total',       'value' => $stats['total'], 'bg' => 'bg-white',     'text' => 'text-on-surface', 'border' => 'border-[#c5c5d7]'],
                            ['label' => 'Hadir',       'value' => $stats['hadir'], 'bg' => 'bg-green-50',  'text' => 'text-green-700',  'border' => 'border-green-200'],
                            ['label' => 'Izin',        'value' => $stats['izin'],  'bg' => 'bg-amber-50',  'text' => 'text-amber-700',  'border' => 'border-amber-200'],
                            ['label' => 'Sakit',       'value' => $stats['sakit'], 'bg' => 'bg-blue-50',   'text' => 'text-blue-700',   'border' => 'border-blue-200'],
                            ['label' => 'Alpa',        'value' => $stats['alpa'],  'bg' => 'bg-[#ffdad6]', 'text' => 'text-[#93000a]',  'border' => 'border-[#ba1a1a]/20'],
                            ['label' => 'Belum Absen', 'value' => $stats['belum'], 'bg' => 'bg-[#f0f4f8]', 'text' => 'text-[#505f76]',  'border' => 'border-[#c5c5d7]'],
                        ];
                    @endphp
                    @foreach ($statCards as $card)
                        <div class="{{ $card['bg'] }} {{ $card['border'] }} border rounded-xl p-4 text-center shadow-sm">
                            <p class="text-[24px] font-bold {{ $card['text'] }}">{{ $card['value'] }}</p>
                            <p class="text-[11px] uppercase tracking-wider {{ $card['text'] }} opacity-70 mt-0.5">{{ $card['label'] }}</p>
                        </div>
                    @endforeach
                </div>

                {{-- Roster --}}
                <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-[#c5c5d7] flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between">
                        <p class="text-[14px] font-semibold text-on-surface">Daftar Peserta Didik</p>
                        <div class="flex items-center gap-2">
                            <span class="text-[13px] text-[#505f76]">Filter:</span>
                            <select wire:model.live="filterStatus"
                                    class="border border-[#c5c5d7] rounded-lg px-3 py-1.5 text-[13px] bg-white cursor-pointer focus:outline-none focus:ring-1 focus:ring-[#3c50e0]">
                                <option value="">Semua Status</option>
                                <option value="hadir">Hadir</option>
                                <option value="izin">Izin</option>
                                <option value="sakit">Sakit</option>
                                <option value="alpa">Alpa</option>
                                <option value="belum">Belum Absen</option>
                            </select>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-[14px]">
                            <thead class="bg-white border-b border-[#c5c5d7]">
                                <tr>
                                    <th class="px-4 py-3 text-left text-[12px] font-semibold text-[#505f76] uppercase tracking-wide w-12">No</th>
                                    <th class="px-4 py-3 text-left text-[12px] font-semibold text-[#505f76] uppercase tracking-wide">Nama Peserta Didik</th>
                                    <th class="px-4 py-3 text-left text-[12px] font-semibold text-[#505f76] uppercase tracking-wide w-36">Status</th>
                                    <th class="px-4 py-3 text-left text-[12px] font-semibold text-[#505f76] uppercase tracking-wide w-36">Waktu / Sumber</th>
                                    <th class="px-4 py-3 text-center text-[12px] font-semibold text-[#505f76] uppercase tracking-wide w-44">
                                        @if ($isActive) Override Status @endif
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#e8e8f0]">
                                @forelse ($roster as $i => $pd)
                                    @php $detail = $details[$pd->id] ?? null; @endphp
                                    <tr class="hover:bg-[#f8f9fb] transition-colors">
                                        <td class="px-4 py-3 text-[#757686]">{{ $i + 1 }}</td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-8 h-8 rounded-full bg-[#EEF2FF] flex items-center justify-center flex-shrink-0">
                                                    <span class="text-[11px] font-bold text-[#3c50e0]">{{ strtoupper(substr($pd->nama_lengkap, 0, 2)) }}</span>
                                                </div>
                                                <div>
                                                    <p class="font-medium text-on-surface">{{ $pd->nama_lengkap }}</p>
                                                    <p class="text-[12px] text-[#757686]">NIPD: {{ $pd->nipd }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            @if (! $detail)
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-[#f0f4f8] text-[#505f76] border border-[#c5c5d7]">Belum Absen</span>
                                            @elseif ($detail->status === 'hadir')
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-green-100 text-green-800 border border-green-200">Hadir</span>
                                            @elseif ($detail->status === 'izin')
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-amber-100 text-amber-800 border border-amber-200">Izin</span>
                                            @elseif ($detail->status === 'sakit')
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-blue-100 text-blue-800 border border-blue-200">Sakit</span>
                                            @elseif ($detail->status === 'alpa')
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-[#ffdad6] text-[#93000a] border border-[#ba1a1a]/20">Alpa</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-[12px] text-[#757686]">
                                            @if ($detail)
                                                @if ($detail->waktu_klik)
                                                    {{ $detail->waktu_klik->format('H:i') }} WIB
                                                @else
                                                    <span class="text-[#3c50e0]">Manual</span>
                                                @endif
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            @if ($isActive)
                                                <div class="flex justify-center gap-1">
                                                    <button wire:click="setStatus({{ $pd->id }}, 'hadir')" title="Hadir"
                                                            class="p-1.5 rounded cursor-pointer {{ ($detail->status ?? '') === 'hadir' ? 'text-green-600 bg-green-100' : 'text-[#757686] hover:text-green-600 hover:bg-green-50' }}">
                                                        <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' {{ ($detail->status ?? '') === 'hadir' ? 1 : 0 }}">check_circle</span>
                                                    </button>
                                                    <button wire:click="setStatus({{ $pd->id }}, 'izin')" title="Izin"
                                                            class="p-1.5 rounded cursor-pointer {{ ($detail->status ?? '') === 'izin' ? 'text-amber-600 bg-amber-100' : 'text-[#757686] hover:text-amber-600 hover:bg-amber-50' }}">
                                                        <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' {{ ($detail->status ?? '') === 'izin' ? 1 : 0 }}">info</span>
                                                    </button>
                                                    <button wire:click="setStatus({{ $pd->id }}, 'sakit')" title="Sakit"
                                                            class="p-1.5 rounded cursor-pointer {{ ($detail->status ?? '') === 'sakit' ? 'text-blue-600 bg-blue-100' : 'text-[#757686] hover:text-blue-600 hover:bg-blue-50' }}">
                                                        <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' {{ ($detail->status ?? '') === 'sakit' ? 1 : 0 }}">local_hospital</span>
                                                    </button>
                                                    <button wire:click="setStatus({{ $pd->id }}, 'alpa')" title="Alpa"
                                                            class="p-1.5 rounded cursor-pointer {{ ($detail->status ?? '') === 'alpa' ? 'text-[#ba1a1a] bg-[#ffdad6]' : 'text-[#757686] hover:text-[#ba1a1a] hover:bg-[#ffdad6]' }}">
                                                        <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' {{ ($detail->status ?? '') === 'alpa' ? 1 : 0 }}">cancel</span>
                                                    </button>
                                                </div>
                                            @else
                                                <p class="text-center text-[12px] text-[#c5c5d7]">—</p>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-12 text-center text-[14px] text-[#757686]">
                                            @if ($filterStatus)
                                                Tidak ada peserta didik dengan status "{{ $filterStatus }}".
                                            @else
                                                Belum ada peserta didik di rombel ini.
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        @endif
    @endif

    {{-- Modal Konfirmasi Tutup Sesi --}}
    @if ($confirmCloseSession)
        <div class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center bg-black/50 backdrop-blur-sm p-0 sm:p-4">
            <div class="bg-white rounded-t-2xl sm:rounded-xl shadow-xl shadow-black/10 w-full max-w-4xl sm:max-w-sm p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 rounded-full bg-[#ffdad6] flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-[#ba1a1a] text-[26px]">stop_circle</span>
                    </div>
                    <div>
                        <h4 class="text-[16px] font-semibold text-on-surface">Tutup Sesi Absensi?</h4>
                        <p class="text-[13px] text-[#505f76] mt-0.5">Peserta didik tidak dapat melakukan absensi mandiri setelah sesi ditutup.</p>
                    </div>
                </div>
                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 mt-5">
                    <button wire:click="cancelCloseSession"
                            class="w-full sm:w-auto px-5 py-2.5 text-[14px] font-medium text-[#505f76] border border-[#c5c5d7] rounded-lg hover:bg-[#f0f4f8] transition-colors cursor-pointer text-center">
                        Batal
                    </button>
                    <button wire:click="closeSession"
                            class="w-full sm:w-auto px-5 py-2.5 text-[14px] font-semibold text-white bg-[#ba1a1a] rounded-lg hover:bg-[#93000a] transition-colors cursor-pointer flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">stop_circle</span>
                        Ya, Tutup Sesi
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
