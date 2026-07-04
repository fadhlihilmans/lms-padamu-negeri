@php
    $labels = ['cbt' => 'CBT', 'tugas' => 'Tugas', 'absensi' => 'Absensi', 'materi' => 'Materi'];
    $label = $labels[$module ?? ''] ?? ucfirst($module ?? 'Ini');
@endphp

<x-layouts.app :pageTitle="'Modul ' . $label">
    <div class="max-w-lg mx-auto">
        <div class="bg-white border border-[#c5c5d7] rounded-2xl shadow-sm px-6 py-16 text-center">
            <div class="w-16 h-16 rounded-full bg-[#f0f4f8] text-[#757686] flex items-center justify-center mx-auto mb-5">
                <span class="material-symbols-outlined" style="font-size:34px">block</span>
            </div>
            <h1 class="text-[18px] font-bold text-on-surface mb-2">Modul {{ $label }} Belum Tersedia</h1>
            <p class="text-[14px] text-[#505f76] leading-relaxed mb-6">
                Modul {{ $label }} sedang dinonaktifkan oleh Administrator. Silakan hubungi Admin bila Anda merasa ini keliru.
            </p>
            <a href="{{ route('dashboard') }}" wire:navigate
               class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-[#3c50e0] text-white text-[14px] font-medium hover:bg-[#2a3db0] transition-colors cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">home</span> Kembali ke Dashboard
            </a>
        </div>
    </div>
</x-layouts.app>
