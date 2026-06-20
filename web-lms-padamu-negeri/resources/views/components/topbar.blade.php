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

<header class="sticky top-0 z-30 w-full bg-white border-b border-outline-variant shadow-sm h-16 px-gutter flex items-center justify-between flex-shrink-0">

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

        <h2 class="text-headline-md text-on-surface font-bold">{{ $pageTitle }}</h2>
    </div>

    {{-- Kanan: periode switcher + notif + profil --}}
    <div class="flex items-center gap-4">

        {{-- Periode Switcher --}}
        @auth
            <livewire:shared.periode-switcher />
        @endauth

        {{-- Lapor Bug — semua role --}}
        <button
            type="button"
            class="relative p-2 text-secondary hover:bg-surface-container rounded-full transition-colors"
            title="Lapor Bug"
        >
            <span class="material-symbols-outlined text-[20px]">bug_report</span>
        </button>

        {{-- Profil pengguna --}}
        <div class="flex items-center gap-3 pl-4 border-l border-outline-variant">
            <div class="hidden md:block text-right">
                <p class="text-label-md text-on-surface font-medium leading-tight">{{ $namaUser }}</p>
                <p class="text-label-sm text-secondary leading-tight">{{ $roleLabel }}</p>
            </div>
            <div class="w-9 h-9 rounded-full bg-primary-container flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-on-primary text-[18px]">person</span>
            </div>
        </div>

    </div>
</header>
