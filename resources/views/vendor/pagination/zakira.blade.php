{{-- resources/views/vendor/pagination/zakira.blade.php --}}
@if ($paginator->total() > 0)
<nav role="navigation" aria-label="Pagination Navigation"
     class="flex flex-col sm:flex-row items-center justify-between gap-4">

    {{-- Info kiri --}}
    <p class="text-sm text-zinc-600">
        Showing
        <span class="font-semibold text-zinc-900">{{ $paginator->firstItem() }}</span>
        to
        <span class="font-semibold text-zinc-900">{{ $paginator->lastItem() }}</span>
        of
        <span class="font-semibold text-zinc-900">{{ $paginator->total() }}</span>
        results
    </p>

    {{-- Kontrol kanan --}}
    @if ($paginator->hasPages())
    <div class="inline-flex items-stretch border border-zinc-200 rounded-lg overflow-hidden shadow-sm bg-white divide-x divide-zinc-200">

        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span class="px-3.5 py-2 text-zinc-300 cursor-not-allowed" aria-disabled="true">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                </svg>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
               class="px-3.5 py-2 text-zinc-600 hover:bg-zinc-50 transition flex items-center">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                </svg>
            </a>
        @endif

        {{-- Nomor halaman --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="px-4 py-2 text-sm text-zinc-400">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span aria-current="page"
                              class="px-4 py-2 text-sm font-bold text-zinc-900 bg-zinc-100">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}"
                           class="px-4 py-2 text-sm text-zinc-600 hover:bg-zinc-50 transition">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next"
               class="px-3.5 py-2 text-zinc-600 hover:bg-zinc-50 transition flex items-center">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>
            </a>
        @else
            <span class="px-3.5 py-2 text-zinc-300 cursor-not-allowed" aria-disabled="true">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>
            </span>
        @endif
    </div>
    @endif
</nav>
@endif