@if ($paginator->total() > 0)
@php
    $perPageOptions = $perPageOptions ?? [15, 30, 50, 100];
    $currentPerPage = (int) $paginator->perPage();
    $btn    = 'px-3.5 py-2 text-zinc-600 hover:bg-zinc-50 transition flex items-center';
    $btnOff = 'px-3.5 py-2 text-zinc-300 cursor-not-allowed flex items-center';
@endphp

<nav role="navigation" aria-label="Pagination Navigation"
     class="flex flex-col lg:flex-row items-center justify-between gap-4">

    {{-- Info kiri + pilihan per halaman --}}
    <div class="flex flex-col sm:flex-row items-center gap-3">
        <p class="text-sm text-zinc-600">
            Showing
            <span class="font-semibold text-zinc-900">{{ $paginator->firstItem() }}</span>
            to
            <span class="font-semibold text-zinc-900">{{ $paginator->lastItem() }}</span>
            of
            <span class="font-semibold text-zinc-900">{{ $paginator->total() }}</span>
            results
        </p>

        <label class="flex items-center gap-2 text-sm text-zinc-600">
            <span>Per halaman</span>
            <select aria-label="Jumlah data per halaman"
                    class="border border-zinc-200 rounded-lg px-2 py-1 text-sm text-zinc-800 bg-white focus:outline-none focus:ring-2 focus:ring-primary-500"
                    onchange="const u = new URL(window.location.href); u.searchParams.set('per_page', this.value); u.searchParams.delete('page'); window.location.href = u.toString();">
                @foreach ($perPageOptions as $opt)
                    <option value="{{ $opt }}" @selected($currentPerPage === $opt)>{{ $opt }}</option>
                @endforeach
            </select>
        </label>
    </div>

    {{-- Kontrol kanan --}}
    @if ($paginator->hasPages())
    <div class="inline-flex items-stretch border border-zinc-200 rounded-lg overflow-hidden shadow-sm bg-white divide-x divide-zinc-200">

        {{-- First + Previous --}}
        @if ($paginator->onFirstPage())
            <span class="{{ $btnOff }}" aria-disabled="true">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m18.75 4.5-7.5 7.5 7.5 7.5m-6-15L5.25 12l7.5 7.5"/></svg>
            </span>
            <span class="{{ $btnOff }}" aria-disabled="true">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
            </span>
        @else
            <a href="{{ $paginator->url(1) }}" class="{{ $btn }}" aria-label="Halaman pertama">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m18.75 4.5-7.5 7.5 7.5 7.5m-6-15L5.25 12l7.5 7.5"/></svg>
            </a>
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="{{ $btn }}" aria-label="Sebelumnya">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
            </a>
        @endif

        {{-- Layar kecil: tampilkan "x / y" saja --}}
        <span class="sm:hidden px-4 py-2 text-sm text-zinc-700">
            {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
        </span>

        {{-- Nomor halaman --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="hidden sm:block px-4 py-2 text-sm text-zinc-400">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span aria-current="page"
                              class="hidden sm:block px-4 py-2 text-sm font-bold text-zinc-900 bg-zinc-100">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}"
                           class="hidden sm:block px-4 py-2 text-sm text-zinc-600 hover:bg-zinc-50 transition">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next + Last --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="{{ $btn }}" aria-label="Berikutnya">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
            </a>
            <a href="{{ $paginator->url($paginator->lastPage()) }}" class="{{ $btn }}" aria-label="Halaman terakhir">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m5.25 4.5 7.5 7.5-7.5 7.5m6-15 7.5 7.5-7.5 7.5"/></svg>
            </a>
        @else
            <span class="{{ $btnOff }}" aria-disabled="true">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
            </span>
            <span class="{{ $btnOff }}" aria-disabled="true">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m5.25 4.5 7.5 7.5-7.5 7.5m6-15 7.5 7.5-7.5 7.5"/></svg>
            </span>
        @endif
    </div>
    @endif
</nav>
@endif