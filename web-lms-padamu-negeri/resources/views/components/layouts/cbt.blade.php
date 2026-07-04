<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Pengerjaan CBT' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-[#f9fafb] text-on-surface font-sans h-screen overflow-hidden flex flex-col">

    {{ $slot }}

    {{-- Toast Notifications --}}
    <div
        x-data="{
            toasts: [],
            add(detail) {
                const payload = Array.isArray(detail) ? detail[0] : detail;
                const id = Date.now();
                this.toasts.push({ id, visible: true, type: payload.type ?? 'success', message: payload.message ?? '' });
                setTimeout(() => {
                    const t = this.toasts.find(t => t.id === id);
                    if (t) t.visible = false;
                    setTimeout(() => { this.toasts = this.toasts.filter(t => t.id !== id); }, 300);
                }, 3500);
            }
        }"
        @notify.window="add($event.detail)"
        x-init="@if (session()->has('toast')) add(@js(session('toast'))) @endif"
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
    @stack('scripts')
</body>
</html>
