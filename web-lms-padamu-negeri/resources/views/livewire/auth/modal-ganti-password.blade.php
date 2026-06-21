<div>
    @if ($terbuka)
        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/50 backdrop-blur-sm p-0 sm:p-4"
             x-data="{ showBaru: false, showKonfirmasi: false }">

            <div class="bg-white rounded-t-2xl sm:rounded-xl shadow-xl shadow-black/10 w-full max-w-4xl sm:max-w-md border border-[#c5c5d7] overflow-hidden flex flex-col max-h-[90vh] sm:max-h-[85vh]">

                {{-- Header --}}
                <div class="flex justify-between items-center p-6 border-b border-[#c5c5d7] bg-[#f6fafe]">
                    <h2 class="text-[20px] leading-[28px] font-semibold text-on-surface">Ganti Password Anda</h2>
                    <button wire:click="skip" title="Tutup"
                            class="text-[#505f76] hover:text-[#ba1a1a] transition-colors p-1 rounded-full hover:bg-[#ffdad6]/20 cursor-pointer">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                {{-- Body --}}
                <div class="p-6 flex flex-col gap-5">

                    <div class="flex items-start gap-3 p-4 rounded-lg bg-[#f0f4f8] border border-[#c5c5d7]">
                        <span class="material-symbols-outlined text-[#3c50e0] text-[20px] flex-shrink-0 mt-0.5"
                              style="font-variation-settings: 'FILL' 1;">info</span>
                        <p class="text-[14px] text-[#454655]">Demi keamanan, segera ganti password default Anda.</p>
                    </div>

                    <form wire:submit="save" class="flex flex-col gap-4">

                        {{-- Password Baru --}}
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[14px] font-medium text-on-surface" for="passwordBaru">Password Baru</label>
                            <div class="relative">
                                <input wire:model="passwordBaru" id="passwordBaru"
                                       :type="showBaru ? 'text' : 'password'"
                                       placeholder="Masukkan password baru"
                                       class="w-full bg-white border rounded-lg px-4 py-2.5 text-[14px] text-on-surface focus:outline-none focus:ring-1 transition-shadow placeholder:text-[#757686]
                                              {{ $errors->has('passwordBaru') ? 'border-[#ba1a1a] focus:ring-[#ba1a1a]' : 'border-[#c5c5d7] focus:ring-[#3c50e0] focus:border-[#3c50e0]' }}">
                                <button type="button" @click="showBaru = !showBaru" tabindex="-1"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-[#505f76] hover:text-[#1c33c8] transition-colors cursor-pointer">
                                    <span class="material-symbols-outlined text-[20px]"
                                          x-text="showBaru ? 'visibility_off' : 'visibility'"
                                          style="font-variation-settings: 'FILL' 0;"></span>
                                </button>
                            </div>
                            @error('passwordBaru')
                                <p class="text-[12px] text-[#ba1a1a]">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Konfirmasi Password --}}
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[14px] font-medium text-on-surface" for="konfirmasiPassword">Konfirmasi Password Baru</label>
                            <div class="relative">
                                <input wire:model="konfirmasiPassword" id="konfirmasiPassword"
                                       :type="showKonfirmasi ? 'text' : 'password'"
                                       placeholder="Ulangi password baru"
                                       class="w-full bg-white border rounded-lg px-4 py-2.5 text-[14px] text-on-surface focus:outline-none focus:ring-1 transition-shadow placeholder:text-[#757686]
                                              {{ $errors->has('konfirmasiPassword') ? 'border-[#ba1a1a] focus:ring-[#ba1a1a]' : 'border-[#c5c5d7] focus:ring-[#3c50e0] focus:border-[#3c50e0]' }}">
                                <button type="button" @click="showKonfirmasi = !showKonfirmasi" tabindex="-1"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-[#505f76] hover:text-[#1c33c8] transition-colors cursor-pointer">
                                    <span class="material-symbols-outlined text-[20px]"
                                          x-text="showKonfirmasi ? 'visibility_off' : 'visibility'"
                                          style="font-variation-settings: 'FILL' 0;"></span>
                                </button>
                            </div>
                            @error('konfirmasiPassword')
                                <p class="text-[12px] text-[#ba1a1a]">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Footer --}}
                        <div class="flex flex-col-reverse sm:flex-row sm:justify-end items-stretch sm:items-center pt-2 gap-3 border-t border-[#c5c5d7] mt-1">
                            <button type="button" wire:click="skip"
                                    class="w-full sm:w-auto px-5 py-2.5 rounded-lg border border-[#c5c5d7] bg-white text-[#505f76] text-[14px] font-medium hover:bg-[#f0f4f8] transition-colors cursor-pointer text-center">
                                Nanti Saja
                            </button>
                            <button type="submit"
                                    class="px-5 py-2.5 rounded-lg bg-[#3c50e0] text-white text-[14px] font-medium hover:bg-[#1c33c8] transition-colors shadow-sm flex items-center gap-2 cursor-pointer"
                                    wire:loading.attr="disabled" wire:loading.class="opacity-70 cursor-not-allowed">
                                <span wire:loading wire:target="save" class="material-symbols-outlined text-[16px] animate-spin">progress_activity</span>
                                Simpan Password Baru
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
