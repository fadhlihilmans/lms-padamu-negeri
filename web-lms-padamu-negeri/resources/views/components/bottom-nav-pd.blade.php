@php
    $navItems = [
        ['route' => 'dashboard', 'icon' => 'dashboard', 'label' => 'Beranda'],
        ['route' => 'peserta-didik.materi', 'icon' => 'menu_book', 'label' => 'Materi'],
        ['route' => 'peserta-didik.tugas', 'icon' => 'assignment', 'label' => 'Tugas'],
        ['route' => 'peserta-didik.jadwal', 'icon' => 'schedule', 'label' => 'Jadwal'],
        ['route' => 'peserta-didik.absensi', 'icon' => 'how_to_reg', 'label' => 'Absensi'],
    ];
@endphp

<nav class="fixed bottom-0 left-0 right-0 z-30 bg-white border-t flex items-center justify-around px-1 lg:hidden"
     style="height: 60px; border-color: #c5c5d7; box-shadow: 0 -2px 8px rgba(0,0,0,0.06)">
    @foreach ($navItems as $item)
        @php $active = request()->routeIs($item['route'] . '*'); @endphp
        <a href="{{ route($item['route']) }}"
           class="flex flex-col items-center gap-1 px-2 py-2 cursor-pointer transition-colors rounded-xl flex-1"
           style="color: {{ $active ? '#3c50e0' : '#9da4b0' }}"
           @unless($active) onmouseover="this.style.color='#505f76'" onmouseout="this.style.color='#9da4b0'" @endunless>
            <span class="material-symbols-outlined text-[22px]" @if($active) style="font-variation-settings:'FILL' 1" @endif>{{ $item['icon'] }}</span>
            <span class="text-[10px] font-semibold">{{ $item['label'] }}</span>
        </a>
    @endforeach
</nav>
