<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Login – ' . config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-white font-sans text-on-surface flex items-center justify-center p-4 relative overflow-hidden">

    {{-- Background decoration circles (subtle, on white) --}}
    <div class="absolute top-[-80px] right-[-80px] w-72 h-72 rounded-full pointer-events-none" style="background: rgba(60,80,224,0.05)"></div>
    <div class="absolute bottom-[-60px] left-[-60px] w-56 h-56 rounded-full pointer-events-none" style="background: rgba(60,80,224,0.04)"></div>

    {{ $slot }}

    <p class="absolute bottom-4 left-0 right-0 text-center text-[11px]" style="color: #b0b0c0">
        © {{ date('Y') }} LMS Padamu Negeri — Semua hak dilindungi
    </p>

    @livewireScripts
</body>
</html>
