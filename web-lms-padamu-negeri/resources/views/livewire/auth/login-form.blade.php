<div class="relative w-full max-w-[360px]" x-data="{ showPassword: false }">

    {{-- Card --}}
    <div class="bg-white rounded-2xl overflow-hidden" style="box-shadow: 0 8px 32px -4px rgba(0,0,0,0.10), 0 0 0 1px #d1d5db">

        {{-- Card Header / Brand --}}
        <div class="px-8 pt-8 pb-6 text-center">
            <div class="w-16 h-16 rounded-2xl mx-auto mb-4 flex items-center justify-center" style="background: #EEF2FF">
                <span class="material-symbols-outlined text-[30px]" style="color:#3c50e0;font-variation-settings:'FILL' 1, 'wght' 400">school</span>
            </div>
            <h1 class="text-[20px] font-bold leading-tight tracking-tight" style="color:#171c1f">LMS Padamu Negeri</h1>
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
                <span class="font-medium" style="color:#3c50e0">Hubungi Admin</span>
            </p>
        </div>

    </div>

</div>
