<div class="relative w-full max-w-[360px]" x-data="{ showPassword: false }">

    {{-- Card --}}
    <div class="bg-white rounded-2xl overflow-hidden" style="box-shadow: 0 8px 32px -4px rgba(0,0,0,0.10), 0 0 0 1px #d1d5db">

        {{-- Header --}}
        <div class="px-8 pt-8 pb-6 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl flex items-center justify-center" style="background: #EEF2FF">
                <span class="material-symbols-outlined text-[30px]" style="color:#3c50e0;font-variation-settings: 'FILL' 1, 'wght' 400">school</span>
            </div>
            <h1 class="text-[20px] font-bold leading-tight tracking-tight text-on-surface">
                LMS Padamu Negeri
            </h1>
            <p class="text-[13px] mt-1" style="color: #757686">Sistem Akademik PKBM</p>
        </div>

        {{-- Form --}}
        <div class="px-6 py-6">
        <form wire:submit="login" class="space-y-4">

            {{-- Error umum --}}
            @if ($errors->has('username') && !$errors->has('password'))
                <div class="flex items-center gap-2 p-3 rounded-lg bg-[#ffdad6] border border-[#ba1a1a]/20 text-[14px] text-[#93000a]">
                    <span class="material-symbols-outlined text-[18px]">error</span>
                    {{ $errors->first('username') }}
                </div>
            @endif

            {{-- Username --}}
            <div>
                <label class="block text-[13px] font-medium mb-1.5 text-on-surface" for="username">
                    Username
                    <span class="font-normal" style="color: #757686">(NIP / NIPD)</span>
                </label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-[18px]" style="color: #9da4b0">person</span>
                    <input
                        wire:model="username"
                        id="username"
                        type="text"
                        autocomplete="username"
                        placeholder="Masukkan username"
                        class="w-full bg-white border rounded-lg pl-9 pr-4 py-2.5 text-[14px] text-on-surface focus:outline-none focus:ring-2 transition-shadow duration-200
                               {{ $errors->has('username') ? 'border-[#ba1a1a] focus:ring-[#ba1a1a]/30 focus:border-[#ba1a1a]' : 'border-[#c5c5d7] focus:ring-[#3c50e0]/30 focus:border-[#3c50e0]' }}"
                    >
                </div>
            </div>

            {{-- Password --}}
            <div>
                <label class="block text-[13px] font-medium mb-1.5 text-on-surface" for="password">
                    Password
                </label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-[18px]" style="color: #9da4b0">lock</span>
                    <input
                        wire:model="password"
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        autocomplete="current-password"
                        placeholder="Masukkan password"
                        class="w-full bg-white border border-[#c5c5d7] rounded-lg pl-9 pr-10 py-2.5 text-[14px] text-on-surface focus:outline-none focus:ring-2 focus:ring-[#3c50e0]/30 focus:border-[#3c50e0] transition-shadow duration-200"
                    >
                    <button
                        type="button"
                        @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-[#9da4b0] hover:text-[#505f76] focus:outline-none"
                        tabindex="-1"
                    >
                        <span class="material-symbols-outlined text-[18px]"
                              x-text="showPassword ? 'visibility_off' : 'visibility'"
                              style="font-variation-settings: 'FILL' 0;"></span>
                    </button>
                </div>
                @error('password')
                    <p class="mt-1 text-[12px] text-[#ba1a1a]">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit --}}
            <div class="pt-2">
                <button
                    type="submit"
                    class="w-full py-2.5 rounded-lg text-[14px] font-semibold text-white transition-all flex items-center justify-center gap-2"
                    style="background: #3c50e0; box-shadow: 0 2px 8px rgba(60,80,224,0.35)"
                    onmouseover="this.style.background='#2e3eb0'"
                    onmouseout="this.style.background='#3c50e0'"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-70 cursor-not-allowed"
                >
                    <span wire:loading wire:target="login" class="material-symbols-outlined text-[18px] animate-spin">progress_activity</span>
                    <span>Masuk</span>
                </button>
            </div>

        </form>

        {{-- Helper text --}}
        <p class="text-center text-[13px] mt-5" style="color: #757686">
            Lupa password?
            <span class="font-medium" style="color: #3c50e0">Hubungi Admin</span>
        </p>
        </div>
    </div>

</div>
