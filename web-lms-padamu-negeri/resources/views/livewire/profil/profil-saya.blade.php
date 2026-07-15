<div class="max-w-3xl mx-auto space-y-4">

    @php
        $roleLabel = ['admin' => 'Administrator', 'guru' => 'Guru', 'peserta_didik' => 'Peserta Didik'][$role] ?? 'Pengguna';
        $namaUser  = $guru?->nama_lengkap ?? $pd?->nama_lengkap ?? ($role === 'admin' ? 'Admin' : $user->username);
    @endphp

    {{-- ── Header profil ───────────────────────────────────────────────────── --}}
    <div class="bg-white border border-[#c5c5d7] rounded-xl p-5 flex items-center gap-4" style="box-shadow:0 1px 3px rgba(0,0,0,0.05)">
        <div class="w-16 h-16 rounded-2xl flex items-center justify-center flex-shrink-0" style="background:#EEF2FF">
            <span class="material-symbols-outlined text-[32px]" style="color:#3c50e0; font-variation-settings:'FILL' 1">person</span>
        </div>
        <div class="min-w-0">
            <h1 class="text-[18px] sm:text-[20px] font-bold text-on-surface truncate">{{ $namaUser }}</h1>
            <div class="flex items-center gap-2 mt-1">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold" style="background:#EEF2FF; color:#3c50e0">{{ $roleLabel }}</span>
                <span class="text-[12.5px] text-[#757686]">&middot; {{ $user->username }}</span>
            </div>
        </div>
    </div>

    {{-- ── Informasi Akun (identitas read-only) ────────────────────────────── --}}
    <div class="bg-white border border-[#c5c5d7] rounded-xl overflow-hidden">
        <div class="px-5 py-3.5 border-b border-[#c5c5d7] bg-[#f6fafe]">
            <p class="text-sm font-semibold text-on-surface">Informasi Akun</p>
        </div>
        <div class="p-5">
            @php
                $fields = [];
                if ($role === 'guru') {
                    $fields = [
                        ['Nama Lengkap', $guru?->nama_lengkap],
                        ['NIP (Username)', $guru?->nip],
                    ];
                } elseif ($role === 'peserta_didik') {
                    $fields = [
                        ['Nama Lengkap', $pd?->nama_lengkap],
                        ['NIPD (Username)', $pd?->nipd],
                        ['NISN', $pd?->nisn],
                        ['NIK', $pd?->nik],
                        ['Jenis Kelamin', $pd?->jenis_kelamin === 'L' ? 'Laki-laki' : ($pd?->jenis_kelamin === 'P' ? 'Perempuan' : null)],
                        ['Tempat Lahir', $pd?->tempat_lahir],
                        ['Tanggal Lahir', $pd?->tanggal_lahir ? \Illuminate\Support\Carbon::parse($pd->tanggal_lahir)->format('d/m/Y') : null],
                        ['Agama', $pd?->agama],
                    ];
                } else {
                    $fields = [
                        ['Username', $user->username],
                        ['Peran', $roleLabel],
                    ];
                }
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                @foreach ($fields as [$label, $value])
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-[#9da4b0] mb-0.5">{{ $label }}</p>
                        <p class="text-[13.5px] text-on-surface break-words">{{ $value ?: '—' }}</p>
                    </div>
                @endforeach
            </div>

            @if ($role === 'admin')
                <p class="mt-4 text-[12.5px] text-[#757686] flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">info</span>
                    Akun administrator tidak memiliki data biodata tambahan.
                </p>
            @endif
        </div>
    </div>

    {{-- ── Alamat & Orang Tua (Peserta Didik, read-only) ───────────────────── --}}
    @if ($role === 'peserta_didik')
        <div class="bg-white border border-[#c5c5d7] rounded-xl overflow-hidden">
            <div class="px-5 py-3.5 border-b border-[#c5c5d7] bg-[#f6fafe]">
                <p class="text-sm font-semibold text-on-surface">Alamat &amp; Keluarga</p>
            </div>
            <div class="p-5 space-y-5">
                {{-- Alamat --}}
                <div>
                    <p class="text-[12px] font-semibold text-[#505f76] mb-2">Alamat</p>
                    @if ($pd?->alamat)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3">
                            @foreach ([
                                ['Alamat', $pd->alamat->alamat],
                                ['RT / RW', trim(($pd->alamat->rt ?? '-') . ' / ' . ($pd->alamat->rw ?? '-'))],
                                ['Dusun', $pd->alamat->dusun],
                                ['Kelurahan', $pd->alamat->kelurahan],
                                ['Kecamatan', $pd->alamat->kecamatan],
                                ['Kode Pos', $pd->alamat->kode_pos],
                            ] as [$label, $value])
                                <div>
                                    <p class="text-[11px] font-semibold uppercase tracking-wide text-[#9da4b0] mb-0.5">{{ $label }}</p>
                                    <p class="text-[13.5px] text-on-surface break-words">{{ $value ?: '—' }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-[13px] text-[#757686]">Data alamat belum diisi.</p>
                    @endif
                </div>

                {{-- Orang tua / wali --}}
                <div>
                    <p class="text-[12px] font-semibold text-[#505f76] mb-2">Orang Tua / Wali</p>
                    @if ($pd?->ortu?->isNotEmpty())
                        <div class="border border-[#c5c5d7] rounded-lg divide-y divide-[#c5c5d7]">
                            @foreach ($pd->ortu as $o)
                                <div class="flex items-center justify-between px-4 py-2.5">
                                    <div class="min-w-0">
                                        <p class="text-[13.5px] font-medium text-on-surface">{{ $o->nama ?: '—' }}</p>
                                        <p class="text-[12px] text-[#757686] capitalize">{{ $o->jenis }}@if ($o->jenis === 'wali' && $o->hubungan_wali) &middot; {{ $o->hubungan_wali }} @endif</p>
                                    </div>
                                    <span class="text-[12.5px] text-[#505f76] flex-shrink-0">{{ $o->no_hp ?: '—' }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-[13px] text-[#757686]">Data orang tua/wali belum diisi.</p>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- ── Kontak (editable, guru & peserta didik) ─────────────────────────── --}}
    @if ($role === 'guru' || $role === 'peserta_didik')
        <form wire:submit="saveKontak" class="bg-white border border-[#c5c5d7] rounded-xl overflow-hidden">
            <div class="px-5 py-3.5 border-b border-[#c5c5d7] bg-[#f6fafe]">
                <p class="text-sm font-semibold text-on-surface">Kontak</p>
            </div>
            <div class="p-5">
                <label class="block text-[13px] font-medium text-on-surface mb-1.5">Nomor HP / WhatsApp</label>
                <input type="text" wire:model="no_hp" placeholder="Contoh: 081234567890"
                       class="w-full sm:max-w-xs px-3 py-2 border rounded-lg text-sm focus:outline-none focus:border-[#3c50e0] focus:ring-1 focus:ring-[#3c50e0] @error('no_hp') border-[#ba1a1a] @else border-[#c5c5d7] @enderror">
                @error('no_hp') <p class="text-[12px] text-[#ba1a1a] mt-1">{{ $message }}</p> @enderror
                <p class="text-[12px] text-[#757686] mt-1">Nomor ini dapat dihubungi Admin/Guru bila diperlukan.</p>
            </div>
            <div class="px-5 py-3.5 border-t border-[#c5c5d7] flex justify-end">
                <button type="submit" wire:loading.attr="disabled"
                        class="px-5 py-2 bg-[#3c50e0] text-white rounded-lg text-sm font-semibold hover:bg-[#2a3db0] transition-colors flex items-center gap-2 cursor-pointer">
                    <span wire:loading wire:target="saveKontak" class="material-symbols-outlined text-[16px] animate-spin">progress_activity</span>
                    <span wire:loading.remove wire:target="saveKontak" class="material-symbols-outlined text-[16px]">save</span>
                    Simpan Kontak
                </button>
            </div>
        </form>
    @endif

    {{-- ── Keamanan ────────────────────────────────────────────────────────── --}}
    <div x-data="{ openPw: false, show: { lama: false, baru: false, konf: false } }"
         @password-changed.window="openPw = false"
         @keydown.escape.window="openPw = false">

        {{-- Kartu ringkas + tombol pembuka --}}
        <div class="bg-white border border-[#c5c5d7] rounded-xl overflow-hidden">
            <div class="px-5 py-3.5 border-b border-[#c5c5d7] bg-[#f6fafe]">
                <p class="text-sm font-semibold text-on-surface">Keamanan</p>
            </div>
            <div class="p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background:#EEF2FF">
                        <span class="material-symbols-outlined text-[20px]" style="color:#3c50e0">lock</span>
                    </div>
                    <div>
                        <p class="text-[14px] font-medium text-on-surface">Password</p>
                        <p class="text-[12px] text-[#757686]">Ubah password akun Anda secara berkala demi keamanan.</p>
                    </div>
                </div>
                <button type="button" @click="openPw = true"
                        class="self-start sm:self-auto flex-shrink-0 px-4 py-2 rounded-lg border border-[#c5c5d7] text-[13.5px] font-semibold text-[#3c50e0] hover:bg-[#EEF2FF] transition-colors flex items-center gap-2 cursor-pointer">
                    <span class="material-symbols-outlined text-[17px]">lock_reset</span>
                    Ganti Password
                </button>
            </div>
        </div>

        {{-- Modal Ganti Password (bottom-sheet di mobile) --}}
        <div x-show="openPw" style="display:none"
             class="fixed inset-0 z-[9999] flex items-end sm:items-center justify-center sm:p-4">
            {{-- Backdrop --}}
            <div x-show="openPw" x-transition.opacity
                 class="absolute inset-0 bg-black/50"
                 @click="openPw = false; $wire.resetPasswordForm()"></div>

            {{-- Panel --}}
            <div x-show="openPw"
                 x-transition:enter="transition ease-out duration-250"
                 x-transition:enter-start="translate-y-full sm:translate-y-0 sm:opacity-0 sm:scale-95"
                 x-transition:enter-end="translate-y-0 sm:opacity-100 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="translate-y-0 sm:opacity-100 sm:scale-100"
                 x-transition:leave-end="translate-y-full sm:translate-y-0 sm:opacity-0 sm:scale-95"
                 class="relative z-10 w-full sm:max-w-md bg-white rounded-t-2xl sm:rounded-xl shadow-xl flex flex-col max-h-[92vh]">

                {{-- Header --}}
                <div class="flex items-center justify-between gap-3 px-5 py-4 border-b border-[#c5c5d7] flex-shrink-0">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px]" style="color:#3c50e0">lock_reset</span>
                        <h4 class="text-[15px] font-semibold text-on-surface">Ganti Password</h4>
                    </div>
                    <button type="button" @click="openPw = false; $wire.resetPasswordForm()"
                            class="p-1.5 -mr-1.5 text-[#505f76] hover:bg-[#f0f4f8] rounded-lg cursor-pointer" title="Tutup">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>

                <form wire:submit="gantiPassword" class="flex flex-col min-h-0">
                    {{-- Grip mobile --}}
                    <div class="sm:hidden w-10 h-1.5 rounded-full mx-auto mt-3 -mb-1" style="background:#e5e7eb"></div>

                    <div class="p-5 space-y-4 overflow-y-auto">
                        @foreach ([
                            ['passwordLama', 'Password Saat Ini', 'lama'],
                            ['passwordBaru', 'Password Baru', 'baru'],
                            ['konfirmasiPassword', 'Konfirmasi Password Baru', 'konf'],
                        ] as [$model, $label, $key])
                            <div>
                                <label class="block text-[13px] font-medium text-on-surface mb-1.5">{{ $label }}</label>
                                <div class="relative w-full">
                                    <input :type="show.{{ $key }} ? 'text' : 'password'" wire:model="{{ $model }}" autocomplete="off"
                                           class="w-full px-3 pr-10 py-2 border rounded-lg text-sm focus:outline-none focus:border-[#3c50e0] focus:ring-1 focus:ring-[#3c50e0] @error($model) border-[#ba1a1a] @else border-[#c5c5d7] @enderror">
                                    <button type="button" @click="show.{{ $key }} = !show.{{ $key }}" tabindex="-1"
                                            class="absolute right-2.5 top-1/2 -translate-y-1/2 text-[#9da4b0] hover:text-[#505f76] cursor-pointer">
                                        <span class="material-symbols-outlined text-[18px]" x-text="show.{{ $key }} ? 'visibility_off' : 'visibility'" style="font-variation-settings:'FILL' 0;"></span>
                                    </button>
                                </div>
                                @error($model) <p class="text-[12px] text-[#ba1a1a] mt-1">{{ $message }}</p> @enderror
                            </div>
                        @endforeach
                        <p class="text-[12px] text-[#757686]">Password minimal 3 karakter dan berbeda dari password saat ini.</p>
                    </div>

                    {{-- Footer --}}
                    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2 px-5 py-3.5 border-t border-[#c5c5d7] flex-shrink-0">
                        <button type="button" @click="openPw = false; $wire.resetPasswordForm()"
                                class="w-full sm:w-auto px-4 py-2 rounded-lg border border-[#c5c5d7] text-[13.5px] text-[#505f76] hover:bg-[#f0f4f8] transition-colors cursor-pointer text-center">
                            Batal
                        </button>
                        <button type="submit" wire:loading.attr="disabled"
                                class="w-full sm:w-auto px-5 py-2 bg-[#3c50e0] text-white rounded-lg text-[13.5px] font-semibold hover:bg-[#2a3db0] transition-colors flex items-center justify-center gap-2 cursor-pointer">
                            <span wire:loading wire:target="gantiPassword" class="material-symbols-outlined text-[16px] animate-spin">progress_activity</span>
                            <span wire:loading.remove wire:target="gantiPassword" class="material-symbols-outlined text-[16px]">check</span>
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
