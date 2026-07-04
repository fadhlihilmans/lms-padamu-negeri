<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sedang Pemeliharaan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body class="bg-[#f9fafb] font-sans min-h-screen flex items-center justify-center p-6">
    <div class="max-w-md w-full bg-white border border-[#c5c5d7] rounded-2xl shadow-sm p-8 text-center">
        <div class="w-16 h-16 rounded-full bg-[#EEF2FF] text-[#3c50e0] flex items-center justify-center mx-auto mb-5">
            <span class="material-symbols-outlined" style="font-size:34px">construction</span>
        </div>
        <h1 class="text-[20px] font-bold text-[#171c1f] mb-2">Sedang Dalam Pemeliharaan</h1>
        <p class="text-[14px] text-[#505f76] leading-relaxed mb-6">
            {{ app(\App\Services\SettingService::class)->get('nama_pkbm', 'Aplikasi') }} sedang dalam pemeliharaan.
            Silakan coba beberapa saat lagi. Terima kasih atas pengertiannya.
        </p>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-[#c5c5d7] text-[14px] text-[#505f76] hover:bg-[#f0f4f8] transition-colors cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">logout</span> Keluar
            </button>
        </form>
    </div>
</body>
</html>
