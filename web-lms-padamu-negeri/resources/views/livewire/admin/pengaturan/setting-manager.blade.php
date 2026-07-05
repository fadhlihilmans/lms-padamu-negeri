<div class="max-w-3xl mx-auto space-y-4">

    {{-- ── Heading ─────────────────────────────────────────────────────────── --}}
    <div>
        <h1 class="text-xl font-bold text-on-surface">Pengaturan Aplikasi</h1>
        <p class="text-sm text-[#505f76] mt-0.5">Kelola identitas lembaga, kop rapor, batas upload, dan modul aktif.</p>
    </div>

    @foreach ($groupLabel as $group => $label)
        @php $items = $grouped[$group] ?? collect(); @endphp
        @if ($items->isNotEmpty())
            <div class="bg-white border border-[#c5c5d7] rounded-xl overflow-hidden">
                <div class="px-5 py-3.5 border-b border-[#c5c5d7] bg-[#f6fafe]">
                    <p class="text-sm font-semibold text-on-surface">{{ $label }}</p>
                </div>

                <div class="p-5 space-y-4">
                    @foreach ($items as $s)
                        @php $key = $s->key; @endphp

                        {{-- Logo (path) --}}
                        @if (str_ends_with($key, '_path'))
                            <div>
                                <label class="block text-[13px] font-medium text-on-surface mb-1.5">{{ $s->label }}</label>
                                <div class="flex items-center gap-4">
                                    <div class="w-16 h-16 rounded-lg border border-[#c5c5d7] bg-[#f0f4f8] flex items-center justify-center overflow-hidden flex-shrink-0">
                                        @if (! empty($logoFiles[$key]))
                                            <img src="{{ $logoFiles[$key]->temporaryUrl() }}" class="w-full h-full object-contain" alt="preview">
                                        @elseif (! empty($values[$key]))
                                            <img src="{{ Storage::url($values[$key]) }}" class="w-full h-full object-contain" alt="logo">
                                        @else
                                            <span class="material-symbols-outlined text-[#757686]">image</span>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <label class="inline-flex items-center gap-1.5 px-3 py-2 text-[13px] border border-[#c5c5d7] rounded-lg text-[#505f76] hover:bg-[#f0f4f8] transition-colors cursor-pointer">
                                            <span class="material-symbols-outlined text-[16px]">upload</span> Pilih Gambar
                                            <input type="file" wire:model="logoFiles.{{ $key }}" accept="image/*" class="hidden">
                                        </label>
                                        @if (! empty($values[$key]))
                                            <button wire:click="removeLogo('{{ $key }}')"
                                                    class="ml-2 inline-flex items-center gap-1 px-3 py-2 text-[13px] text-[#ba1a1a] hover:bg-[#ffdad6] rounded-lg transition-colors cursor-pointer">
                                                <span class="material-symbols-outlined text-[16px]">delete</span> Hapus
                                            </button>
                                        @endif
                                        <div wire:loading wire:target="logoFiles.{{ $key }}" class="flex items-center gap-1.5 text-[12px] text-[#3c50e0] mt-1">
                                            <span class="material-symbols-outlined text-[14px] animate-spin">progress_activity</span> Mengunggah…
                                        </div>
                                        @error('logoFiles.'.$key) <p class="text-[12px] text-[#ba1a1a] mt-1">{{ $message }}</p> @enderror
                                        @if ($s->description) <p class="text-[12px] text-[#757686] mt-1">{{ $s->description }}</p> @endif
                                    </div>
                                </div>
                            </div>

                        {{-- Boolean → toggle --}}
                        @elseif ($s->type === 'boolean')
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-[14px] font-medium text-on-surface">{{ $s->label }}</p>
                                    @if ($s->description) <p class="text-[12px] text-[#757686] mt-0.5">{{ $s->description }}</p> @endif
                                </div>
                                <button type="button" wire:click="$toggle('values.{{ $key }}')"
                                        class="relative inline-flex h-6 w-11 flex-shrink-0 items-center rounded-full transition-colors cursor-pointer {{ ($values[$key] ?? false) ? 'bg-[#3c50e0]' : 'bg-[#c5c5d7]' }}"
                                        role="switch" aria-checked="{{ ($values[$key] ?? false) ? 'true' : 'false' }}">
                                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ ($values[$key] ?? false) ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                </button>
                            </div>

                        {{-- Integer --}}
                        @elseif ($s->type === 'integer')
                            <div>
                                <label class="block text-[13px] font-medium text-on-surface mb-1.5">{{ $s->label }}</label>
                                <input type="number" min="1" max="1024" wire:model="values.{{ $key }}"
                                       class="w-full sm:w-40 px-3 py-2 border rounded-lg text-sm focus:outline-none focus:border-[#3c50e0] focus:ring-1 focus:ring-[#3c50e0] @error('values.'.$key) border-[#ba1a1a] @else border-[#c5c5d7] @enderror">
                                @error('values.'.$key) <p class="text-[12px] text-[#ba1a1a] mt-1">{{ $message }}</p> @enderror
                                @if ($s->description) <p class="text-[12px] text-[#757686] mt-1">{{ $s->description }}</p> @endif
                            </div>

                        {{-- String / email --}}
                        @else
                            <div>
                                <label class="block text-[13px] font-medium text-on-surface mb-1.5">{{ $s->label }}</label>
                                <input type="{{ str_contains($key, 'email') ? 'email' : 'text' }}" wire:model="values.{{ $key }}"
                                       placeholder="{{ $s->label }}"
                                       class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:border-[#3c50e0] focus:ring-1 focus:ring-[#3c50e0] @error('values.'.$key) border-[#ba1a1a] @else border-[#c5c5d7] @enderror">
                                @error('values.'.$key) <p class="text-[12px] text-[#ba1a1a] mt-1">{{ $message }}</p> @enderror
                                @if ($s->description) <p class="text-[12px] text-[#757686] mt-1">{{ $s->description }}</p> @endif
                            </div>
                        @endif
                    @endforeach
                </div>

                <div class="px-5 py-3.5 border-t border-[#c5c5d7] flex justify-end">
                    <button wire:click="saveGroup('{{ $group }}')" wire:loading.attr="disabled"
                            class="px-5 py-2 bg-[#3c50e0] text-white rounded-lg text-sm font-semibold hover:bg-[#2a3db0] transition-colors flex items-center gap-2 cursor-pointer">
                        <span wire:loading wire:target="saveGroup('{{ $group }}')" class="material-symbols-outlined text-[16px] animate-spin">progress_activity</span>
                        <span wire:loading.remove wire:target="saveGroup('{{ $group }}')" class="material-symbols-outlined text-[16px]">save</span>
                        Simpan {{ $label }}
                    </button>
                </div>
            </div>
        @endif
    @endforeach

</div>
