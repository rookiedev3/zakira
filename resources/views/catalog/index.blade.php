@extends('layouts.app')

@section('title', 'Katalog Produk — Zakira Moslem Hijab Identity')

@section('content')
    <!-- Header Judul Katalog -->
    <section class="max-w-7xl mx-auto px-6 py-12 w-full">
        <span class="text-xs uppercase tracking-widest text-gray-500 font-semibold">KOLEKSI ZAKIRA</span>
        <h1 class="text-4xl font-serif font-medium mt-2 mb-3">Katalog Produk</h1>
        <p class="text-gray-600 text-sm">Ready Stock untuk semua pembeli. Produk PO khusus member Zakira yang sudah login.
        </p>
    </section>

    <!-- Konten Utama: Sidebar Filter & Grid Produk -->
    <main class="max-w-7xl mx-auto px-6 pb-24 grid grid-cols-1 lg:grid-cols-4 gap-8 w-full flex-grow">

        <!-- SIDEBAR FILTER -->
        <form method="GET" action="{{ route('catalog.index') }}" id="filter-form" class="lg:col-span-1">
            <aside class="space-y-6 bg-white p-6 rounded-2xl border border-gray-200 shadow-sm h-fit">
                <h3 class="font-bold text-base border-b border-gray-100 pb-3">Filter Produk</h3>

                <div>
                    <label class="block text-xs font-semibold uppercase text-gray-500 mb-2">Cari produk</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama produk..."
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-amber-800">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-gray-500 mb-2">Brand</label>
                    <select name="brand" onchange="this.form.submit()"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-1 focus:ring-amber-800">
                        <option value="">Semua Brand</option>
                        @foreach($brandOptions as $brand)
                            <option value="{{ $brand->id }}" @selected(request('brand') == $brand->id)>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-gray-500 mb-2">Kategori</label>
                    <select name="category" onchange="this.form.submit()"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-1 focus:ring-amber-800">
                        <option value="">Semua Kategori</option>
                        @foreach($categoryOptions as $category)
                            <option value="{{ $category->id }}" @selected(request('category') == $category->id)>
                                {{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="flex-1 h-10 text-sm font-medium text-white rounded-lg shadow-sm"
                        style="background-color:#8b5a33;">Filter</button>
                    @if(request()->hasAny(['search', 'brand', 'category', 'sort']))
                        <a href="{{ route('catalog.index') }}"
                            class="text-sm text-gray-500 hover:underline whitespace-nowrap">Reset</a>
                    @endif
                </div>
            </aside>

            {{-- Sorting ikut terkirim bersama form filter --}}
            <input type="hidden" name="sort" id="sort-input" value="{{ request('sort', 'terbaru') }}">
        </form>

        <!-- AREA PRODUK & SORTING -->
        <section class="lg:col-span-3 space-y-6">
            <div
                class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white p-4 rounded-xl border border-gray-200 shadow-sm gap-4">
                <span class="text-xs text-gray-600 font-medium">{{ $products->total() }} produk ditemukan</span>
                <div class="w-full sm:w-auto">
                    <select
                        onchange="document.getElementById('sort-input').value = this.value; document.getElementById('filter-form').submit();"
                        class="w-full sm:w-48 px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-1 focus:ring-amber-800">
                        <option value="terbaru" @selected(request('sort', 'terbaru') === 'terbaru')>Terbaru</option>
                        <option value="termahal" @selected(request('sort') === 'termahal')>Termahal</option>
                        <option value="termurah" @selected(request('sort') === 'termurah')>Termurah</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                @forelse($products as $product)
                    <a href="{{ route('catalog.show', $product->id) }}" class="block group">
                        <div class="relative h-52 w-full bg-gray-100 overflow-hidden rounded-t-lg">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                    class="w-full h-full object-cover transition-transform group-hover:scale-105">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400 text-sm">Tidak Ada Gambar
                                </div>
                            @endif
                            <span
                                class="absolute top-3 left-3 px-2 py-0.5 text-xs font-semibold rounded-full {{ $product->product_type === 'ready' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ $product->product_type === 'ready' ? 'Ready' : 'PO' }}
                            </span>
                        </div>

                        <div class="p-4 bg-white rounded-b-lg border border-t-0 border-gray-200">
                            <h3 class="font-medium text-gray-900 line-clamp-1">{{ $product->name }}</h3>
                            <p class="text-sm text-gray-600 mt-1">{{ $product->brand->name ?? '' }}</p>
                            <p class="text-sm font-semibold text-gray-900 mt-2">{{ $product->price_range ?? '-' }}</p>
                        </div>
                    </a>
                @empty
                    <div
                        class="col-span-full py-16 text-center text-gray-500 bg-white rounded-xl border border-gray-200 shadow-sm">
                        <p class="text-base font-medium">Belum ada produk yang ditemukan.</p>
                        <p class="text-sm text-gray-400 mt-1">Coba ubah kata kunci atau filter pencarian Anda.</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $products->links() }}
            </div>
        </section>
    </main>
@endsection