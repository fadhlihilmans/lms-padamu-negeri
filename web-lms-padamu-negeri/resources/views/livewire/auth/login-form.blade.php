<div class="w-full max-w-[400px] px-4 mx-auto" x-data="{ showPassword: false }">

    {{-- Card --}}
    <div class="bg-white rounded-lg border border-[#E2E8F0] shadow-sm p-6">

        {{-- Header --}}
        <div class="text-center mb-6">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-[#EEF2FF] flex items-center justify-center">
                <span class="material-symbols-outlined text-[32px] text-primary">school</span>
            </div>
            <h1 class="text-[24px] leading-[32px] font-bold tracking-[-0.01em] text-on-surface mb-1">
                LMS Padamu Negeri
            </h1>
            <p class="text-[14px] leading-[20px] text-[#505f76]">Sistem Akademik PKBM</p>
        </div>

        {{-- Form --}}
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
                <label class="block text-[14px] leading-[20px] font-medium text-on-surface mb-2"
                       for="username">
                    Username (NIP / NIPD)
                </label>
                <input
                    wire:model="username"
                    id="username"
                    type="text"
                    autocomplete="username"
                    placeholder="Masukkan Username"
                    class="w-full bg-white border rounded-lg px-4 py-2.5 text-[14px] text-on-surface focus:outline-none focus:ring-1 transition-shadow duration-200
                           {{ $errors->has('username') ? 'border-[#ba1a1a] focus:ring-[#ba1a1a] focus:border-[#ba1a1a]' : 'border-[#E2E8F0] focus:ring-[#3c50e0] focus:border-[#3c50e0]' }}"
                >
            </div>

            {{-- Password --}}
            <div>
                <label class="block text-[14px] leading-[20px] font-medium text-on-surface mb-2"
                       for="password">
                    Password
                </label>
                <div class="relative">
                    <input
                        wire:model="password"
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        autocomplete="current-password"
                        placeholder="Masukkan Password"
                        class="w-full bg-white border border-[#E2E8F0] rounded-lg pl-4 pr-10 py-2.5 text-[14px] text-on-surface focus:outline-none focus:ring-1 focus:ring-[#3c50e0] focus:border-[#3c50e0] transition-shadow duration-200"
                    >
                    <button
                        type="button"
                        @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-[#505f76] hover:text-on-surface focus:outline-none"
                        tabindex="-1"
                    >
                        <span class="material-symbols-outlined text-[20px]"
                              x-text="showPassword ? 'visibility_off' : 'visibility'"
                              style="font-variation-settings: 'FILL' 0;"></span>
                    </button>
                </div>
                @error('password')
                    <p class="mt-1 text-[12px] text-[#ba1a1a]">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit --}}
            <button
                type="submit"
                class="w-full bg-[#3c50e0] text-white text-[14px] font-medium py-2.5 rounded-lg hover:bg-[#1c33c8] transition-colors duration-200 mt-2 shadow-sm flex items-center justify-center gap-2"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-70 cursor-not-allowed"
            >
                <span wire:loading wire:target="login" class="material-symbols-outlined text-[18px] animate-spin">progress_activity</span>
                <span>Masuk</span>
            </button>

        </form>
    </div>

    {{-- Helper text --}}
    <div class="text-center mt-4">
        <p class="text-[14px] text-[#505f76]">
            Lupa password?
            <span class="text-[#3c50e0] font-medium">Hubungi Admin</span>
        </p>
    </div>

</div>
