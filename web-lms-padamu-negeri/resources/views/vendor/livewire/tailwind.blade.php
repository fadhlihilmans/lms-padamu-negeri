@php
if (! isset($scrollTo)) {
    $scrollTo = 'body';
}

$scrollIntoViewJsSnippet = ($scrollTo !== false)
    ? <<<JS
       (\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()
    JS
    : '';
@endphp

@if ($paginator->hasPages())
<nav role="navigation" aria-label="Navigasi Halaman" class="flex items-center gap-1">

    {{-- Previous --}}
    @if ($paginator->onFirstPage())
        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-[#c5c5d7] cursor-not-allowed select-none"
              aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
            <span class="material-symbols-outlined text-[18px]">chevron_left</span>
        </span>
    @else
        <button
            type="button"
            wire:click="previousPage('{{ $paginator->getPageName() }}')"
            x-on:click="{{ $scrollIntoViewJsSnippet }}"
            wire:loading.attr="disabled"
            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-[#505f76] border border-[#c5c5d7] bg-white hover:bg-[#EEF2FF] hover:text-[#3c50e0] hover:border-[#3c50e0] transition-colors cursor-pointer"
            aria-label="{{ __('pagination.previous') }}"
        >
            <span class="material-symbols-outlined text-[18px]">chevron_left</span>
        </button>
    @endif

    {{-- Page numbers --}}
    @foreach ($elements as $element)

        {{-- "..." separator --}}
        @if (is_string($element))
            <span class="inline-flex items-center justify-center w-8 h-8 text-[14px] text-[#505f76] select-none">
                {{ $element }}
            </span>
        @endif

        @if (is_array($element))
            @foreach ($element as $page => $url)
                <span wire:key="paginator-{{ $paginator->getPageName() }}-page{{ $page }}">
                    @if ($page == $paginator->currentPage())
                        <span
                            aria-current="page"
                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-[14px] font-semibold text-white bg-[#3c50e0] cursor-default select-none"
                        >{{ $page }}</span>
                    @else
                        <button
                            type="button"
                            wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                            x-on:click="{{ $scrollIntoViewJsSnippet }}"
                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-[14px] text-[#505f76] border border-[#c5c5d7] bg-white hover:bg-[#EEF2FF] hover:text-[#3c50e0] hover:border-[#3c50e0] transition-colors cursor-pointer"
                            aria-label="{{ __('Go to page :page', ['page' => $page]) }}"
                        >{{ $page }}</button>
                    @endif
                </span>
            @endforeach
        @endif

    @endforeach

    {{-- Next --}}
    @if ($paginator->hasMorePages())
        <button
            type="button"
            wire:click="nextPage('{{ $paginator->getPageName() }}')"
            x-on:click="{{ $scrollIntoViewJsSnippet }}"
            wire:loading.attr="disabled"
            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-[#505f76] border border-[#c5c5d7] bg-white hover:bg-[#EEF2FF] hover:text-[#3c50e0] hover:border-[#3c50e0] transition-colors cursor-pointer"
            aria-label="{{ __('pagination.next') }}"
        >
            <span class="material-symbols-outlined text-[18px]">chevron_right</span>
        </button>
    @else
        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-[#c5c5d7] cursor-not-allowed select-none"
              aria-disabled="true" aria-label="{{ __('pagination.next') }}">
            <span class="material-symbols-outlined text-[18px]">chevron_right</span>
        </span>
    @endif

</nav>
@endif
