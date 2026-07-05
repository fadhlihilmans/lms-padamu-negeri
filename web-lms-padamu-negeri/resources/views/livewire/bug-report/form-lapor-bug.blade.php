<div class="max-w-2xl mx-auto space-y-4">

    {{-- ── Heading ─────────────────────────────────────────────────────────── --}}
    <div>
        <h1 class="text-xl font-bold text-on-surface">Lapor Bug</h1>
        <p class="text-sm text-[#505f76] mt-0.5">Temukan sesuatu yang janggal atau tidak berfungsi? Beri tahu kami.</p>
    </div>

    {{-- ── Form ────────────────────────────────────────────────────────────── --}}
    <form wire:submit="submit" class="bg-white border border-[#c5c5d7] rounded-xl overflow-hidden">
        <div class="p-5 space-y-4">
            <div>
                <label class="block text-[13px] font-medium text-on-surface mb-1.5">Judul <span class="text-[#ba1a1a]">*</span></label>
                <input wire:model="judul" type="text" placeholder="Ringkas masalahnya, mis. Tombol simpan tidak berfungsi"
                       class="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none focus:border-[#3c50e0] focus:ring-1 focus:ring-[#3c50e0] @error('judul') border-[#ba1a1a] @else border-[#c5c5d7] @enderror">
                @error('judul') <p class="text-[12px] text-[#ba1a1a] mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-[13px] font-medium text-on-surface mb-1.5">Deskripsi <span class="text-[#ba1a1a]">*</span></label>
                <textarea wire:model="deskripsi" rows="5" placeholder="Ceritakan apa yang terjadi, langkah-langkahnya, dan apa yang Anda harapkan…"
                          class="w-full px-3 py-2.5 border rounded-lg text-sm resize-y focus:outline-none focus:border-[#3c50e0] focus:ring-1 focus:ring-[#3c50e0] @error('deskripsi') border-[#ba1a1a] @else border-[#c5c5d7] @enderror"></textarea>
                @error('deskripsi') <p class="text-[12px] text-[#ba1a1a] mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-[13px] font-medium text-on-surface mb-1.5">Lampiran Gambar <span class="text-[12px] font-normal text-[#757686]">(opsional)</span></label>
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-lg border border-[#c5c5d7] bg-[#f0f4f8] flex items-center justify-center overflow-hidden flex-shrink-0">
                        @if ($screenshot)
                            <img src="{{ $screenshot->temporaryUrl() }}" class="w-full h-full object-cover" alt="preview">
                        @else
                            <span class="material-symbols-outlined text-[#757686]">image</span>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <label class="inline-flex items-center gap-1.5 px-3 py-2 text-[13px] border border-[#c5c5d7] rounded-lg text-[#505f76] hover:bg-[#f0f4f8] transition-colors cursor-pointer">
                            <span class="material-symbols-outlined text-[16px]">upload</span> Pilih Gambar
                            <input type="file" wire:model="screenshot" accept="image/*" class="hidden">
                        </label>
                        <div wire:loading wire:target="screenshot" class="flex items-center gap-1.5 text-[12px] text-[#3c50e0] mt-1">
                            <span class="material-symbols-outlined text-[14px] animate-spin">progress_activity</span> Mengunggah…
                        </div>
                        @error('screenshot') <p class="text-[12px] text-[#ba1a1a] mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            @if ($halamanUrl)
                <p class="text-[12px] text-[#757686] flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">link</span>
                    Dilaporkan dari: <span class="truncate">{{ $halamanUrl }}</span>
                </p>
            @endif
        </div>

        <div class="px-5 py-3.5 border-t border-[#c5c5d7] flex justify-end">
            <button type="submit" wire:loading.attr="disabled"
                    class="px-5 py-2 bg-[#3c50e0] text-white rounded-lg text-sm font-semibold hover:bg-[#2a3db0] transition-colors flex items-center gap-2 cursor-pointer">
                <span wire:loading wire:target="submit" class="material-symbols-outlined text-[16px] animate-spin">progress_activity</span>
                <span wire:loading.remove wire:target="submit" class="material-symbols-outlined text-[16px]">send</span>
                Kirim Laporan
            </button>
        </div>
    </form>

    {{-- ── Riwayat laporan saya ────────────────────────────────────────────── --}}
    <div class="bg-white border border-[#c5c5d7] rounded-xl overflow-hidden">
        <div class="px-5 py-3.5 border-b border-[#c5c5d7] bg-[#f6fafe]">
            <p class="text-sm font-semibold text-on-surface">Laporan Saya</p>
        </div>
        <ul class="divide-y divide-[#c5c5d7]">
            @forelse ($laporan as $b)
                @php $meta = $statusMeta[$b->status] ?? [$b->status, 'bg-[#f0f4f8] text-[#757686] border-[#c5c5d7]']; @endphp
                <li class="px-5 py-3.5 flex items-start gap-3">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-on-surface">{{ $b->judul }}</p>
                        <p class="text-xs text-[#757686] line-clamp-2 mt-0.5">{{ $b->deskripsi }}</p>
                        <p class="text-[11px] text-[#9da4b0] mt-1">{{ $b->created_at->diffForHumans() }}</p>
                    </div>
                    <span class="text-xs px-2 py-0.5 rounded-full border font-medium whitespace-nowrap {{ $meta[1] }}">{{ $meta[0] }}</span>
                </li>
            @empty
                <li class="px-5 py-10 text-center text-[13px] text-[#757686]">Belum ada laporan.</li>
            @endforelse
        </ul>
        @if ($laporan->hasPages())
            <div class="px-5 py-3 border-t border-[#c5c5d7]">{{ $laporan->links() }}</div>
        @endif
    </div>

</div>
