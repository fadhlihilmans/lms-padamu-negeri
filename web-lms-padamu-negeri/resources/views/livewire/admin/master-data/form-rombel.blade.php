<div>
    <div class="max-w-2xl mx-auto">

        {{-- Page header (3.2.2) --}}
        <div class="flex items-center gap-3 mb-5">
            <a href="{{ route('admin.master.rombel') }}"
               class="w-9 h-9 flex items-center justify-center rounded-lg border cursor-pointer transition-colors"
               style="border-color: #c5c5d7; background: white; color: #505f76"
               onmouseover="this.style.background='#f0f4f8'" onmouseout="this.style.background='white'">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            </a>
            <div>
                <h1 class="text-[18px] font-bold" style="color: #171c1f">{{ $editId ? 'Edit Rombel' : 'Tambah Rombel' }}</h1>
                <p class="text-[13px] mt-0.5" style="color: #757686">{{ $editId ? 'Perbarui data rombongan belajar.' : 'Isi data rombongan belajar baru.' }}</p>
            </div>
        </div>

        {{-- Form card --}}
        <div class="bg-white rounded-xl border overflow-hidden" style="border-color: #c5c5d7; box-shadow: 0 1px 3px rgba(0,0,0,0.05)">
            <div class="px-5 py-4 border-b" style="border-color: #c5c5d7; background: white">
                <p class="text-[13px] font-semibold" style="color: #171c1f">Informasi Rombel</p>
            </div>

            <form wire:submit="save">
                <div class="px-5 py-5 space-y-4">

                    {{-- Periode Ajaran --}}
                    <div>
                        <label for="periodeAjaranId" class="block text-[13px] font-medium mb-1.5" style="color: #171c1f">Periode Ajaran <span style="color: #ba1a1a">*</span></label>
                        <select wire:model="periodeAjaranId" id="periodeAjaranId"
                                class="w-full px-3 py-2.5 rounded-lg text-[14px] bg-white outline-none cursor-pointer transition-all"
                                style="border: 1.5px solid {{ $errors->has('periodeAjaranId') ? '#ba1a1a' : '#c5c5d7' }}; color: #171c1f"
                                onfocus="this.style.borderColor='#3c50e0'; this.style.boxShadow='0 0 0 3px rgba(60,80,224,0.1)'"
                                onblur="this.style.borderColor='{{ $errors->has('periodeAjaranId') ? '#ba1a1a' : '#c5c5d7' }}'; this.style.boxShadow='none'">
                            <option value="" selected>Pilih periode...</option>
                            @foreach ($periodes as $p)
                                <option value="{{ $p->id }}">{{ $p->tahun_ajaran }} — {{ ucfirst($p->semester) }}{{ $p->is_aktif ? ' (Aktif)' : '' }}</option>
                            @endforeach
                        </select>
                        @error('periodeAjaranId')
                            <p class="text-[12px] mt-1.5" style="color: #ba1a1a">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Paket & Tingkat --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="paketId" class="block text-[13px] font-medium mb-1.5" style="color: #171c1f">Paket <span style="color: #ba1a1a">*</span></label>
                            <select wire:model.live="paketId" id="paketId"
                                    class="w-full px-3 py-2.5 rounded-lg text-[14px] bg-white outline-none cursor-pointer transition-all"
                                    style="border: 1.5px solid {{ $errors->has('paketId') ? '#ba1a1a' : '#c5c5d7' }}; color: #171c1f"
                                    onfocus="this.style.borderColor='#3c50e0'; this.style.boxShadow='0 0 0 3px rgba(60,80,224,0.1)'"
                                    onblur="this.style.borderColor='{{ $errors->has('paketId') ? '#ba1a1a' : '#c5c5d7' }}'; this.style.boxShadow='none'">
                                <option value="" selected>Pilih paket...</option>
                                @foreach ($pakets as $p)
                                    <option value="{{ $p->id }}">{{ $p->nama }}</option>
                                @endforeach
                            </select>
                            @error('paketId')
                                <p class="text-[12px] mt-1.5" style="color: #ba1a1a">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="tingkatId" class="block text-[13px] font-medium mb-1.5" style="color: #171c1f">Tingkat <span style="color: #ba1a1a">*</span></label>
                            <select wire:model="tingkatId" id="tingkatId" @disabled(! $paketId)
                                    class="w-full px-3 py-2.5 rounded-lg text-[14px] bg-white outline-none cursor-pointer transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                                    style="border: 1.5px solid {{ $errors->has('tingkatId') ? '#ba1a1a' : '#c5c5d7' }}; color: #171c1f"
                                    onfocus="this.style.borderColor='#3c50e0'; this.style.boxShadow='0 0 0 3px rgba(60,80,224,0.1)'"
                                    onblur="this.style.borderColor='{{ $errors->has('tingkatId') ? '#ba1a1a' : '#c5c5d7' }}'; this.style.boxShadow='none'">
                                <option value="" selected>Pilih tingkat...</option>
                                @foreach ($tingkats as $t)
                                    <option value="{{ $t->id }}">{{ $t->nama }}</option>
                                @endforeach
                            </select>
                            @error('tingkatId')
                                <p class="text-[12px] mt-1.5" style="color: #ba1a1a">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Wilayah --}}
                    <div>
                        <label for="wilayahId" class="block text-[13px] font-medium mb-1.5" style="color: #171c1f">Wilayah <span style="color: #ba1a1a">*</span></label>
                        <select wire:model="wilayahId" id="wilayahId"
                                class="w-full px-3 py-2.5 rounded-lg text-[14px] bg-white outline-none cursor-pointer transition-all"
                                style="border: 1.5px solid {{ $errors->has('wilayahId') ? '#ba1a1a' : '#c5c5d7' }}; color: #171c1f"
                                onfocus="this.style.borderColor='#3c50e0'; this.style.boxShadow='0 0 0 3px rgba(60,80,224,0.1)'"
                                onblur="this.style.borderColor='{{ $errors->has('wilayahId') ? '#ba1a1a' : '#c5c5d7' }}'; this.style.boxShadow='none'">
                            <option value="" selected>Pilih wilayah...</option>
                            @foreach ($wilayahs as $w)
                                <option value="{{ $w->id }}">{{ $w->nama }}</option>
                            @endforeach
                        </select>
                        @error('wilayahId')
                            <p class="text-[12px] mt-1.5" style="color: #ba1a1a">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Wali Kelas (opsional) --}}
                    <div>
                        <label for="waliKelasId" class="block text-[13px] font-medium mb-1.5" style="color: #171c1f">
                            Wali Kelas <span class="font-normal" style="color: #9da4b0">(opsional)</span>
                        </label>
                        <select wire:model="waliKelasId" id="waliKelasId"
                                class="w-full px-3 py-2.5 rounded-lg text-[14px] bg-white outline-none cursor-pointer transition-all"
                                style="border: 1.5px solid #c5c5d7; color: #171c1f"
                                onfocus="this.style.borderColor='#3c50e0'; this.style.boxShadow='0 0 0 3px rgba(60,80,224,0.1)'"
                                onblur="this.style.borderColor='#c5c5d7'; this.style.boxShadow='none'">
                            <option value="">— Belum ditentukan —</option>
                            @forelse ($gurus as $g)
                                <option value="{{ $g->id }}">{{ $g->nama_lengkap }} ({{ $g->nip }})</option>
                            @empty
                                <option value="" disabled>Belum ada guru terdaftar</option>
                            @endforelse
                        </select>
                        <p class="text-[12px] mt-1.5" style="color: #9da4b0">Wali kelas bisa diisi setelah rombel dibuat.</p>
                    </div>

                    {{-- Preview nama otomatis --}}
                    @if ($periodeAjaranId && $wilayahId && $paketId && $tingkatId)
                        @php
                            $prevW  = $wilayahs->firstWhere('id', $wilayahId)?->nama ?? '?';
                            $prevP  = $pakets->firstWhere('id', $paketId)?->nama ?? '?';
                            $prevT  = $tingkats->firstWhere('id', $tingkatId)?->nama ?? '?';
                            $prevTA = $periodes->firstWhere('id', $periodeAjaranId)?->tahun_ajaran ?? '?';
                        @endphp
                        <div class="flex items-start gap-2 p-3 rounded-lg border" style="background: #EEF2FF; border-color: #c5d0ff">
                            <span class="material-symbols-outlined text-[16px] flex-shrink-0 mt-0.5" style="color: #3c50e0">auto_awesome</span>
                            <div>
                                <p class="text-[11px] font-semibold uppercase tracking-wide" style="color: #3c50e0">Nama Rombel (otomatis)</p>
                                <p class="text-[13px] font-medium mt-0.5" style="color: #1c33c8">{{ $prevT }} {{ $prevW }} {{ $prevP }} – TA {{ $prevTA }}</p>
                            </div>
                        </div>
                    @endif

                </div>

                {{-- Footer action --}}
                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 px-5 py-4 border-t" style="border-color: #c5c5d7; background: white">
                    <a href="{{ route('admin.master.rombel') }}"
                       class="px-4 py-2.5 rounded-lg text-[13px] font-medium border text-center cursor-pointer transition-colors"
                       style="color: #505f76; border-color: #c5c5d7; background: white"
                       onmouseover="this.style.background='#f0f4f8'" onmouseout="this.style.background='white'">Batal</a>
                    <button type="submit"
                            class="px-6 py-2.5 rounded-lg text-[13.5px] font-semibold text-white cursor-pointer transition-colors flex items-center justify-center gap-2"
                            style="background: #3c50e0" onmouseover="this.style.background='#2e3eb0'" onmouseout="this.style.background='#3c50e0'"
                            wire:loading.attr="disabled" wire:loading.class="opacity-70 cursor-not-allowed">
                        <span wire:loading wire:target="save" class="material-symbols-outlined text-[16px] animate-spin">progress_activity</span>
                        Simpan Rombel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
