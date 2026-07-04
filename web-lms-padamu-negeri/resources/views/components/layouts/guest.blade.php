<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Login – ' . config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    {{-- Material Symbols di-load via @import layer(base) di app.css (bukan <link>). --}}

    {{-- Base style disamakan dengan design-references/1.1-halaman-login.html.
         Layout & tipografi dasar pakai inline style agar tampilan login tetap
         benar meski build Tailwind belum ter-refresh. --}}
    <style>
        /* JANGAN set font-family di selektor `*` — itu unlayered dan akan menimpa
           font-family ikon Material Symbols (yang kini di cascade layer), membuat
           ikon tampil sebagai teks. Cukup di `body` (diwarisi, ikon tetap menang
           via rule .material-symbols-outlined). */
        * { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; }
        html, body { overflow: hidden; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body style="height:100vh; display:flex; align-items:center; justify-content:center; padding:1rem; position:relative; background:#ffffff;">

    {{-- Background decoration circles (subtle, on white) --}}
    <div class="pointer-events-none" style="position:absolute; top:-80px; right:-80px; width:288px; height:288px; border-radius:9999px; background:rgba(60,80,224,0.05)"></div>
    <div class="pointer-events-none" style="position:absolute; bottom:-60px; left:-60px; width:224px; height:224px; border-radius:9999px; background:rgba(60,80,224,0.04)"></div>

    {{ $slot }}

    {{-- Footer note --}}
    <p style="position:absolute; bottom:1rem; left:0; right:0; text-align:center; font-size:11px; color:#b0b0c0">
        © {{ date('Y') }} {{ app(\App\Services\SettingService::class)->get('nama_pkbm', 'LMS Padamu Negeri') }} — Semua hak dilindungi
    </p>

    @livewireScripts
</body>
</html>
