@php
    $setting = app(\App\Services\SettingService::class);
    $waAdmin = preg_replace('/[^0-9]/', '', $setting->get('whatsapp_admin', '') ?? '');
    $waLink  = $waAdmin ? 'https://wa.me/' . $waAdmin . '?text=' . rawurlencode('Halo Admin, saya butuh bantuan untuk akun LMS (reset password / kendala login).') : '';
    $logoApp = $setting->get('logo_aplikasi_path', '');
@endphp

<div class="relative w-full max-w-[360px]" x-data="{ showPassword: false, showContact: false }">

    {{-- Card --}}
    <div class="bg-white rounded-2xl overflow-hidden" style="box-shadow: 0 8px 32px -4px rgba(0,0,0,0.10), 0 0 0 1px #d1d5db">

        {{-- Card Header / Brand --}}
        <div class="px-8 pt-8 pb-6 text-center">
            @if ($logoApp)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($logoApp) }}" alt="Logo"
                     class="w-16 h-16 rounded-2xl mx-auto mb-4 object-contain">
            @else
                <div class="w-16 h-16 rounded-2xl mx-auto mb-4 flex items-center justify-center" style="background: #EEF2FF">
                    <span class="material-symbols-outlined text-[30px]" style="color:#3c50e0;font-variation-settings:'FILL' 1, 'wght' 400">school</span>
                </div>
            @endif
            <h1 class="text-[20px] font-bold leading-tight tracking-tight" style="color:#171c1f">{{ app(\App\Services\SettingService::class)->get('nama_pkbm', 'LMS Padamu Negeri') }}</h1>
            <p class="text-[13px] mt-1" style="color:#757686">Sistem Akademik PKBM</p>
        </div>

        {{-- Form Body --}}
        <div class="px-6 py-6">
            <form wire:submit="login" class="space-y-4">

                {{-- Error umum (username salah / akun tidak ditemukan) --}}
                @if ($errors->has('username') && ! $errors->has('password'))
                    <div class="flex items-center gap-2 p-3 rounded-lg text-[13px]"
                         style="background:#ffdad6; border:1px solid rgba(186,26,26,0.2); color:#93000a">
                        <span class="material-symbols-outlined text-[18px]">error</span>
                        {{ $errors->first('username') }}
                    </div>
                @endif

                {{-- Username --}}
                <div>
                    <label for="username" class="block text-[13px] font-medium mb-1.5" style="color:#171c1f">
                        Username
                        <span class="font-normal" style="color:#757686">(NIP / NIPD)</span>
                    </label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-[18px]"
                              style="color:#9da4b0">person</span>
                        <input
                            wire:model="username"
                            id="username" type="text" autocomplete="username"
                            placeholder="Masukkan username"
                            class="w-full pl-9 pr-4 py-2.5 rounded-lg text-[14px] bg-white outline-none transition-all"
                            style="border: 1.5px solid {{ $errors->has('username') ? '#ba1a1a' : '#c5c5d7' }}; color:#171c1f;"
                            onfocus="this.style.borderColor='#3c50e0'; this.style.boxShadow='0 0 0 3px rgba(60,80,224,0.12)'"
                            onblur="this.style.borderColor='{{ $errors->has('username') ? '#ba1a1a' : '#c5c5d7' }}'; this.style.boxShadow='none'"
                        >
                    </div>
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-[13px] font-medium mb-1.5" style="color:#171c1f">
                        Password
                    </label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-[18px]"
                              style="color:#9da4b0">lock</span>
                        <input
                            wire:model="password"
                            id="password" :type="showPassword ? 'text' : 'password'" autocomplete="current-password"
                            placeholder="Masukkan password"
                            class="w-full pl-9 pr-10 py-2.5 rounded-lg text-[14px] bg-white outline-none transition-all"
                            style="border: 1.5px solid {{ $errors->has('password') ? '#ba1a1a' : '#c5c5d7' }}; color:#171c1f;"
                            onfocus="this.style.borderColor='#3c50e0'; this.style.boxShadow='0 0 0 3px rgba(60,80,224,0.12)'"
                            onblur="this.style.borderColor='{{ $errors->has('password') ? '#ba1a1a' : '#c5c5d7' }}'; this.style.boxShadow='none'"
                        >
                        <button type="button" @click="showPassword = !showPassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center justify-center w-6 h-6 cursor-pointer transition-colors"
                                style="color:#9da4b0" tabindex="-1"
                                onmouseover="this.style.color='#505f76'" onmouseout="this.style.color='#9da4b0'">
                            <span class="material-symbols-outlined text-[18px]"
                                  x-text="showPassword ? 'visibility_off' : 'visibility'"
                                  style="font-variation-settings:'FILL' 0;"></span>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-[12px]" style="color:#ba1a1a">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit --}}
                <div class="pt-2">
                    <button type="submit"
                            class="w-full py-2.5 rounded-lg text-[14px] font-semibold text-white cursor-pointer transition-all flex items-center justify-center gap-2"
                            style="background:#3c50e0; box-shadow:0 2px 8px rgba(60,80,224,0.35)"
                            onmouseover="this.style.background='#2e3eb0'" onmouseout="this.style.background='#3c50e0'"
                            wire:loading.attr="disabled" wire:loading.class="opacity-70 cursor-not-allowed">
                        <span wire:loading wire:target="login" class="material-symbols-outlined text-[18px] animate-spin">progress_activity</span>
                        <span>Masuk</span>
                    </button>
                </div>

            </form>

            {{-- Helper --}}
            <p class="text-center text-[13px] mt-5" style="color:#757686">
                Lupa password?
                <button type="button" @click="showContact = true"
                        class="font-medium cursor-pointer hover:underline" style="color:#3c50e0">Hubungi Admin</button>
            </p>
        </div>

    </div>

    {{-- ── Modal Hubungi Admin ──────────────────────────────────────────────
         Bottom-sheet di mobile (muncul dari bawah), dialog terpusat di desktop.
         Diteleport ke <body> agar tidak terpotong stacking context kartu login. --}}
    <template x-teleport="body">
        <div x-show="showContact" style="display:none"
             @keydown.escape.window="showContact = false"
             class="fixed inset-0 z-[9999] flex items-end sm:items-center justify-center sm:p-4">
            {{-- Backdrop --}}
            <div x-show="showContact" x-transition.opacity
                 class="absolute inset-0 bg-black/50" @click="showContact = false"></div>

            {{-- Panel --}}
            <div x-show="showContact"
                 x-transition:enter="transition ease-out duration-250"
                 x-transition:enter-start="translate-y-full sm:translate-y-0 sm:opacity-0 sm:scale-95"
                 x-transition:enter-end="translate-y-0 sm:opacity-100 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="translate-y-0 sm:opacity-100 sm:scale-100"
                 x-transition:leave-end="translate-y-full sm:translate-y-0 sm:opacity-0 sm:scale-95"
                 class="relative z-10 w-full sm:max-w-sm bg-white rounded-t-2xl sm:rounded-2xl shadow-xl px-6 pt-6 pb-8 sm:pb-6"
                 style="border:1px solid #e5e7eb">
                {{-- Grip mobile --}}
                <div class="sm:hidden w-10 h-1.5 rounded-full mx-auto mb-4" style="background:#e5e7eb"></div>

                <div class="flex flex-col items-center text-center">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-4" style="background:#EEF2FF">
                        <span class="material-symbols-outlined text-[28px]" style="color:#3c50e0">support_agent</span>
                    </div>
                    <h4 class="text-[17px] font-bold" style="color:#171c1f">Butuh Bantuan?</h4>
                    <p class="text-[13.5px] mt-1.5 leading-relaxed" style="color:#757686">
                        Untuk <strong>reset password</strong> atau kendala login, silakan hubungi Admin sekolah.
                        Sampaikan nama lengkap dan username (NIP/NIPD) Anda.
                    </p>

                    @if ($waLink)
                        <a href="{{ $waLink }}" target="_blank" rel="noopener"
                           class="mt-5 w-full flex items-center justify-center gap-2 py-2.5 rounded-lg text-[14px] font-semibold text-white transition-colors"
                           style="background:#16a34a" onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">
                            <span class="material-symbols-outlined text-[18px]">chat</span>
                            WhatsApp Admin
                        </a>
                    @else
                        <div class="mt-5 w-full flex items-center gap-2 p-3 rounded-lg text-[12.5px] text-left"
                             style="background:#f6fafe; border:1px solid #c5c5d7; color:#505f76">
                            <span class="material-symbols-outlined text-[18px]" style="color:#9da4b0">info</span>
                            Nomor WhatsApp admin belum diatur. Silakan hubungi admin sekolah secara langsung.
                        </div>
                    @endif

                    <button type="button" @click="showContact = false"
                            class="mt-3 w-full py-2.5 rounded-lg text-[14px] font-semibold transition-colors cursor-pointer"
                            style="background:#f0f4f8; color:#505f76" onmouseover="this.style.background='#e5eaf0'" onmouseout="this.style.background='#f0f4f8'">
                        Mengerti
                    </button>
                </div>
            </div>
        </div>
    </template>

</div>
