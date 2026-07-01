<div>
    @if ($terbuka)
        {{-- Overlay — scrim gelap + blur latar (di design, background di-blur 3px).
             Di app blur diterapkan lewat backdrop-filter agar konten dashboard di
             belakang ikut buram. --}}
        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center sm:p-4"
             style="background: rgba(0,0,0,0.5); backdrop-filter: blur(3px); -webkit-backdrop-filter: blur(3px)"
             x-data="{ showBaru: false, showKonfirmasi: false }">

            {{-- Modal Panel --}}
            <div class="w-full bg-white rounded-t-2xl sm:rounded-xl overflow-hidden flex flex-col max-h-[92dvh]"
                 style="max-width: 420px; box-shadow: 0 20px 60px -10px rgba(0,0,0,0.25), 0 0 0 1px rgba(0,0,0,0.06)">

                {{-- Header --}}
                <div class="flex items-center justify-between px-5 py-4 border-b flex-shrink-0" style="border-color: #c5c5d7">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background: #EEF2FF">
                            <span class="material-symbols-outlined text-[18px]" style="color: #3c50e0">lock_reset</span>
                        </div>
                        <h3 class="text-[15px] font-semibold" style="color: #171c1f">Ganti Password</h3>
                    </div>
                    <button type="button" wire:click="skip" title="Tutup"
                            class="w-8 h-8 flex items-center justify-center rounded-lg cursor-pointer transition-colors"
                            style="color: #757686"
                            onmouseover="this.style.background='#f0f4f8'" onmouseout="this.style.background='transparent'">
                        <span class="material-symbols-outlined text-[18px]">close</span>
                    </button>
                </div>

                <form wire:submit="save" class="flex flex-col flex-1 min-h-0">

                    {{-- Body --}}
                    <div class="px-5 py-5 space-y-4 overflow-y-auto flex-1">

                        {{-- Info banner --}}
                        <div class="flex items-start gap-3 p-3.5 rounded-lg border" style="background: #EEF2FF; border-color: #c5d0ff">
                            <span class="material-symbols-outlined text-[18px] flex-shrink-0 mt-0.5"
                                  style="color: #3c50e0; font-variation-settings:'FILL' 1">info</span>
                            <p class="text-[13px] leading-relaxed" style="color: #2e3eb0">
                                Demi keamanan akun, segera ganti password default Anda sebelum menggunakan sistem.
                            </p>
                        </div>

                        {{-- Password Baru --}}
                        <div>
                            <label for="passwordBaru" class="block text-[13px] font-medium mb-1.5" style="color: #171c1f">
                                Password Baru <span style="color: #ba1a1a">*</span>
                            </label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-[17px]"
                                      style="color: #9da4b0">lock</span>
                                <input wire:model="passwordBaru" id="passwordBaru"
                                       :type="showBaru ? 'text' : 'password'"
                                       placeholder="Masukkan password baru"
                                       class="w-full pl-9 pr-10 py-2.5 rounded-lg text-[14px] bg-white outline-none transition-all"
                                       style="border: 1.5px solid {{ $errors->has('passwordBaru') ? '#ba1a1a' : '#c5c5d7' }}; color: #171c1f"
                                       onfocus="this.style.borderColor='#3c50e0'; this.style.boxShadow='0 0 0 3px rgba(60,80,224,0.12)'"
                                       onblur="this.style.borderColor='{{ $errors->has('passwordBaru') ? '#ba1a1a' : '#c5c5d7' }}'; this.style.boxShadow='none'">
                                <button type="button" @click="showBaru = !showBaru" tabindex="-1"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center justify-center w-6 h-6 cursor-pointer"
                                        style="color: #9da4b0">
                                    <span class="material-symbols-outlined text-[17px]"
                                          x-text="showBaru ? 'visibility_off' : 'visibility'"
                                          style="font-variation-settings:'FILL' 0"></span>
                                </button>
                            </div>
                            @error('passwordBaru')
                                <p class="mt-1 text-[12px]" style="color: #ba1a1a">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Konfirmasi Password Baru --}}
                        <div>
                            <label for="konfirmasiPassword" class="block text-[13px] font-medium mb-1.5" style="color: #171c1f">
                                Konfirmasi Password Baru <span style="color: #ba1a1a">*</span>
                            </label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-[17px]"
                                      style="color: #9da4b0">lock</span>
                                <input wire:model="konfirmasiPassword" id="konfirmasiPassword"
                                       :type="showKonfirmasi ? 'text' : 'password'"
                                       placeholder="Ulangi password baru"
                                       class="w-full pl-9 pr-10 py-2.5 rounded-lg text-[14px] bg-white outline-none transition-all"
                                       style="border: 1.5px solid {{ $errors->has('konfirmasiPassword') ? '#ba1a1a' : '#c5c5d7' }}; color: #171c1f"
                                       onfocus="this.style.borderColor='#3c50e0'; this.style.boxShadow='0 0 0 3px rgba(60,80,224,0.12)'"
                                       onblur="this.style.borderColor='{{ $errors->has('konfirmasiPassword') ? '#ba1a1a' : '#c5c5d7' }}'; this.style.boxShadow='none'">
                                <button type="button" @click="showKonfirmasi = !showKonfirmasi" tabindex="-1"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center justify-center w-6 h-6 cursor-pointer"
                                        style="color: #9da4b0">
                                    <span class="material-symbols-outlined text-[17px]"
                                          x-text="showKonfirmasi ? 'visibility_off' : 'visibility'"
                                          style="font-variation-settings:'FILL' 0"></span>
                                </button>
                            </div>
                            @error('konfirmasiPassword')
                                <p class="mt-1 text-[12px]" style="color: #ba1a1a">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    {{-- Footer --}}
                    <div class="flex items-center justify-between px-5 py-4 border-t flex-shrink-0" style="border-color: #d1d5db; background: white">
                        <button type="button" wire:click="skip"
                                class="px-4 py-2.5 rounded-lg text-[13px] cursor-pointer transition-colors border"
                                style="color: #505f76; border-color: #c5c5d7; background: white"
                                onmouseover="this.style.background='#f0f4f8'" onmouseout="this.style.background='white'">
                            Nanti Saja
                        </button>
                        <button type="submit"
                                class="px-5 py-2.5 rounded-lg text-[14px] font-semibold text-white cursor-pointer transition-colors flex items-center gap-2"
                                style="background: #3c50e0; box-shadow: 0 1px 4px rgba(60,80,224,0.3)"
                                onmouseover="this.style.background='#2e3eb0'" onmouseout="this.style.background='#3c50e0'"
                                wire:loading.attr="disabled" wire:loading.class="opacity-70 cursor-not-allowed">
                            <span wire:loading wire:target="save" class="material-symbols-outlined text-[16px] animate-spin">progress_activity</span>
                            Simpan Password
                        </button>
                    </div>

                </form>
            </div>
        </div>
    @endif
</div>
