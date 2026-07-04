@props(['pageTitle' => 'Dashboard'])

@php
    $user = auth()->user();
    $namaUser = match(true) {
        $user?->hasRole('admin')         => 'Admin',
        $user?->hasRole('guru')          => $user->guru?->nama_lengkap ?? $user->username,
        $user?->hasRole('peserta_didik') => $user->pesertaDidik?->nama_lengkap ?? $user->username,
        default                          => $user?->username ?? 'Pengguna',
    };
    $roleLabel = match(true) {
        $user?->hasRole('admin')         => 'Administrator',
        $user?->hasRole('guru')          => 'Guru',
        $user?->hasRole('peserta_didik') => 'Peserta Didik',
        default                          => '',
    };
@endphp

<header class="sticky top-0 z-30 w-full bg-white border-b border-outline-variant shadow-sm h-[60px] px-4 sm:px-6 flex items-center justify-between flex-shrink-0">

    {{-- Kiri: hamburger (mobile) + judul halaman --}}
    <div class="flex items-center gap-3">
        {{-- Tombol hamburger — hanya tampil di mobile --}}
        <button
            type="button"
            class="lg:hidden p-2 text-secondary hover:bg-surface-container rounded-lg transition-colors"
            @click="$dispatch('toggle-sidebar')"
            aria-label="Buka menu"
        >
            <span class="material-symbols-outlined">menu</span>
        </button>

        <h2 class="text-[16px] sm:text-[18px] font-bold text-[#171c1f]">{{ $pageTitle }}</h2>
    </div>

    {{-- Kanan: periode switcher + notif + profil --}}
    <div class="flex items-center gap-2 sm:gap-3">

        {{-- Periode Switcher --}}
        @auth
            <livewire:shared.periode-switcher />
        @endauth

        {{-- Lapor Bug — semua role --}}
        @auth
            <a href="{{ route('bug-report.index', ['from' => url()->current()]) }}" wire:navigate
               class="relative w-9 h-9 flex items-center justify-center text-secondary hover:bg-surface-container rounded-full transition-colors cursor-pointer"
               title="Lapor Bug">
                <span class="material-symbols-outlined text-[20px]">bug_report</span>
            </a>
        @endauth

        {{-- Profil pengguna --}}
        <div x-data="{ open: false }" @click.outside="open = false" class="relative pl-3 border-l border-outline-variant">
            <button @click="open = !open"
                    class="flex items-center gap-2.5 rounded-lg p-1 hover:bg-surface-container transition-colors cursor-pointer"
                    aria-haspopup="true" :aria-expanded="open">
                <div class="hidden md:block text-right">
                    <p class="text-[13px] font-semibold leading-tight text-[#171c1f]">{{ $namaUser }}</p>
                    <p class="text-[11px] leading-tight text-[#757686]">{{ $roleLabel }}</p>
                </div>
                <div class="w-8 h-8 rounded-full bg-primary-container flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-on-primary text-[16px]">person</span>
                </div>
                <span class="material-symbols-outlined text-[16px] text-secondary hidden md:block transition-transform duration-150"
                      :class="open ? 'rotate-180' : ''">expand_more</span>
            </button>

            {{-- Dropdown --}}
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                 class="absolute right-0 top-full mt-2 w-52 bg-white rounded-xl border border-[#c5c5d7] shadow-lg z-50 py-1 origin-top-right"
                 style="display:none">

                {{-- Info user --}}
                <div class="px-4 py-3 border-b border-[#c5c5d7]">
                    <p class="text-[13px] font-semibold text-on-surface truncate">{{ $namaUser }}</p>
                    <p class="text-[12px] text-[#505f76]">{{ $roleLabel }}</p>
                </div>

                {{-- Profil (disabled) --}}
                <button disabled
                        class="w-full flex items-center gap-3 px-4 py-2.5 text-[14px] text-[#b0b0c0] cursor-not-allowed opacity-60">
                    <span class="material-symbols-outlined text-[18px]">manage_accounts</span>
                    Profil Saya
                    <span class="ml-auto text-[11px] bg-[#f0f4f8] text-[#757686] px-1.5 py-0.5 rounded">soon</span>
                </button>

                <div class="border-t border-[#c5c5d7] my-1"></div>

                {{-- Logout --}}
                <button @click="open = false; $dispatch('open-logout-modal')"
                        class="w-full flex items-center gap-3 px-4 py-2.5 text-[14px] text-[#ba1a1a] hover:bg-[#ffdad6] transition-colors rounded-b-xl cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">logout</span>
                    Keluar
                </button>
            </div>
        </div>

    </div>
</header>
