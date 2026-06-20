<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? config('app.name') }}</title>

    {{-- Google Fonts: Inter + Material Symbols Outlined --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-[#F1F5F9] text-on-surface font-sans min-h-screen">

    {{-- Mobile sidebar overlay --}}
    <div
        x-data="{ sidebarOpen: false }"
        class="flex h-screen overflow-hidden"
        @keydown.escape.window="sidebarOpen = false"
    >
        {{-- Overlay (mobile) --}}
        <div
            x-show="sidebarOpen"
            x-transition:enter="transition-opacity ease-linear duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-40 bg-black/50 lg:hidden"
            @click="sidebarOpen = false"
        ></div>

        {{-- Sidebar --}}
        <aside
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-50 w-[280px] transform bg-white border-r border-outline-variant shadow-sm flex flex-col transition-transform duration-200 ease-in-out lg:translate-x-0 lg:static lg:inset-auto lg:z-auto"
        >
            <x-sidebar />
        </aside>

        {{-- Main area --}}
        <div class="flex flex-1 flex-col overflow-hidden lg:ml-0">

            {{-- Topbar --}}
            <x-topbar :pageTitle="$pageTitle ?? 'Dashboard'" @toggle-sidebar="sidebarOpen = !sidebarOpen" />

            {{-- Banner Mode Arsip (tampil saat melihat periode non-aktif) --}}
            @auth
                @inject('periodeService', 'App\Services\PeriodeService')
                @if ($periodeService->isReadOnlyMode())
                    @php $p = $periodeService->periodeSelected(); @endphp
                    <div class="bg-amber-50 border-b border-amber-200 px-gutter py-2.5 flex items-center justify-between gap-4 text-[14px] text-amber-900 flex-shrink-0">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px] text-amber-600">history</span>
                            <span>
                                Mode Arsip — Anda sedang melihat data
                                <strong>TA {{ $p->tahun_ajaran }} {{ ucfirst($p->semester) }}</strong>.
                                Aksi pengubahan data tidak tersedia.
                            </span>
                        </div>
                        <a
                            href="{{ route('periode.reset') }}"
                            class="flex-shrink-0 text-[13px] font-medium text-amber-800 underline hover:text-amber-900 transition-colors"
                        >
                            Kembali ke Periode Aktif
                        </a>
                    </div>
                @endif
            @endauth

            {{-- Page content --}}
            <main class="flex-1 overflow-y-auto p-gutter">
                {{ $slot }}
            </main>

        </div>
    </div>

    {{-- Modal ganti password — muncul jika user belum pernah ganti password --}}
    @auth
        <livewire:auth.modal-ganti-password />
    @endauth

    {{-- Toast Notifications (mendengarkan event 'notify' dari semua Livewire komponen) --}}
    <div
        x-data="{
            toasts: [],
            add(detail) {
                const id = Date.now();
                this.toasts.push({ id, visible: true, type: detail.type ?? 'success', message: detail.message ?? detail[0] ?? '' });
                setTimeout(() => {
                    const t = this.toasts.find(t => t.id === id);
                    if (t) t.visible = false;
                    setTimeout(() => { this.toasts = this.toasts.filter(t => t.id !== id); }, 300);
                }, 3500);
            }
        }"
        @notify.window="add($event.detail)"
        class="fixed top-4 right-4 z-[200] flex flex-col gap-2 w-80 max-w-[calc(100vw-2rem)] pointer-events-none"
    >
        <template x-for="toast in toasts" :key="toast.id">
            <div
                x-show="toast.visible"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-x-4"
                x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-x-0"
                x-transition:leave-end="opacity-0 translate-x-4"
                class="pointer-events-auto flex items-start gap-3 px-4 py-3 rounded-lg shadow-lg border text-[14px]"
                :class="{
                    'bg-white border-green-200 text-green-800': toast.type === 'success',
                    'bg-white border-[#ba1a1a]/30 text-[#93000a]': toast.type === 'error',
                    'bg-white border-amber-200 text-amber-800': toast.type === 'warning',
                }"
            >
                <span class="material-symbols-outlined text-[20px] flex-shrink-0"
                      :class="{
                          'text-green-600': toast.type === 'success',
                          'text-[#ba1a1a]': toast.type === 'error',
                          'text-amber-600': toast.type === 'warning',
                      }"
                      x-text="toast.type === 'success' ? 'check_circle' : (toast.type === 'error' ? 'error' : 'warning')"
                      style="font-variation-settings: 'FILL' 1;"></span>
                <span x-text="toast.message" class="leading-snug"></span>
            </div>
        </template>
    </div>

    @livewireScripts
</body>
</html>
