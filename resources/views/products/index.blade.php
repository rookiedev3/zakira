{{-- resources/views/products/index.blade.php --}}
@extends('layouts.sidebar')

@section('title', 'Produk')

@section('content')
{{-- Layout inti ditulis manual supaya tidak bergantung pada hasil build Tailwind --}}
<style>
    .product-filter-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        gap: 1rem;
    }
    @media (min-width: 768px) {
        .product-filter-grid {
            grid-template-columns: repeat(5, minmax(0, 1fr));
        }
    }
    .btn-brown {
        background-color: #8b5a33;
        color: #ffffff;
        border: 1px solid rgba(0, 0, 0, .1);
    }
    .btn-brown:hover { background-color: #744a2a; }
    .badge-category { background-color: #f3e9df; color: #6f4d3b; }
    .link-brown { color: #6f4d3b; }
    .product-table { width: 100%; }
    .product-table th, .product-table td { border-color: #e5e7eb; }
    /* Tombol "Hapus": teks hitam */
    .btn-hapus { color: #000000; }
</style>
<div class="w-full space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-900">Produk</h1>

        <a href="{{ route('products.create') }}"
           class="h-10 px-4 inline-flex items-center justify-center text-sm font-medium
                  btn-brown rounded-lg shadow-sm transition-colors">
            Tambah Produk
        </a>
    </div>

    {{-- Flash message --}}
    @if (session('success'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Filters (GET form, tanpa Livewire) --}}
    <form method="GET" action="{{ route('products.index') }}" class="bg-white p-6 rounded-lg shadow">
        <div class="product-filter-grid">
            {{-- Search --}}
            <div>
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Cari produk..."
                       class="w-full h-10 py-2 px-3 text-sm border rounded-lg bg-white text-zinc-700 placeholder-zinc-400
                              shadow-xs border-zinc-200 border-b-zinc-300/80 focus:outline-none focus:border-zinc-400">
            </div>

            {{-- Brand --}}
            <div>
                <select name="brand" onchange="this.form.submit()"
                        class="w-full h-10 py-2 px-3 text-sm border rounded-lg bg-white text-zinc-700
                               shadow-xs border-zinc-200 border-b-zinc-300/80 focus:outline-none focus:border-zinc-400">
                    <option value="">Semua Merek</option>
                    @foreach ($brandOptions as $option)
                        <option value="{{ $option->id }}" @selected(request('brand') == $option->id)>{{ $option->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Category --}}
            <div>
                <select name="category" onchange="this.form.submit()"
                        class="w-full h-10 py-2 px-3 text-sm border rounded-lg bg-white text-zinc-700
                               shadow-xs border-zinc-200 border-b-zinc-300/80 focus:outline-none focus:border-zinc-400">
                    <option value="">Semua Kategori</option>
                    @foreach ($categoryOptions as $option)
                        <option value="{{ $option->id }}" @selected(request('category') == $option->id)>{{ $option->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Tipe Produk --}}
            <div>
                <select name="type" onchange="this.form.submit()"
                        class="w-full h-10 py-2 px-3 text-sm border rounded-lg bg-white text-zinc-700
                               shadow-xs border-zinc-200 border-b-zinc-300/80 focus:outline-none focus:border-zinc-400">
                    <option value="">Ready &amp; PO</option>
                    <option value="ready" @selected(request('type') === 'ready')>Ready Stock</option>
                    <option value="po" @selected(request('type') === 'po')>PO / Pre Order</option>
                </select>
            </div>

            {{-- Status --}}
            <div>
                <select name="status" onchange="this.form.submit()"
                        class="w-full h-10 py-2 px-3 text-sm border rounded-lg bg-white text-zinc-700
                               shadow-xs border-zinc-200 border-b-zinc-300/80 focus:outline-none focus:border-zinc-400">
                    <option value="">Semua Status</option>
                    <option value="1" @selected(request('status') === '1')>Aktif</option>
                    <option value="0" @selected(request('status') === '0')>Tidak Aktif</option>
                </select>
            </div>
        </div>

        @if (request()->hasAny(['search', 'brand', 'category', 'type', 'status']))
            <div class="mt-4 flex items-center gap-3 text-sm">
                <span class="text-gray-500">{{ $products->total() }} produk ditemukan.</span>
                <a href="{{ route('products.index') }}" class="font-medium link-brown hover:underline">Reset filter</a>
            </div>
        @endif
    </form>

    {{-- Products Table --}}
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="product-table min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Merek</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe &amp; Akses</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rentang Harga</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($products as $product)
                        @php
                            // Gambar utama diambil dari relasi images (kolom path, is_primary, sort_order).
                            // Fallback ke kolom image lama bila ada.
                            $primaryImage = $product->relationLoaded('images') || method_exists($product, 'images')
                                ? ($product->images->firstWhere('is_primary', true)
                                    ?? $product->images->sortBy('sort_order')->first())
                                : null;
                            $imagePath = $primaryImage->path ?? $product->image ?? null;
                        @endphp
                        <tr>
                            {{-- Produk --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-16 w-16">
                                        @if ($imagePath)
                                            <img class="h-16 w-16 rounded-lg object-cover border border-zinc-200"
                                                 src="{{ asset('storage/' . ltrim($imagePath, '/')) }}"
                                                 alt="{{ $product->name }}">
                                        @else
                                            <div class="h-16 w-16 rounded-lg bg-gray-200 flex items-center justify-center">
                                                <span class="px-1 text-gray-400 text-[10px] leading-tight text-center">Tidak Ada Gambar</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                        @if (isset($product->colors_count) || isset($product->models_count))
                                            <div class="text-sm text-gray-500">
                                                {{ $product->colors_count ?? 0 }} warna, {{ $product->models_count ?? 0 }} model
                                            </div>
                                        @endif
                                        @if ($product->relationLoaded('colors') && $product->colors->isNotEmpty())
                                            <div class="text-xs text-gray-400">
                                                Warna: {{ $product->colors->pluck('name')->join(', ') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Merek --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $product->brand->name ?? '-' }}
                            </td>

                            {{-- Kategori --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @forelse ($product->categories as $category)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium badge-category mr-1">
                                        {{ $category->name }}
                                    </span>
                                @empty
                                    -
                                @endforelse
                            </td>

                            {{-- Tipe & Akses --}}
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <div class="mb-1">
                                    @if ($product->product_type === 'po')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                            PO / Pre Order
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Ready Stock
                                        </span>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ collect($product->access_labels ?? [])->join(' • ') ?: '-' }}
                                </div>
                            </td>

                            {{-- Rentang Harga --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="text-gray-900">{{ $product->price_range ?? '-' }}</span>
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($product->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Tidak Aktif
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('products.edit', $product->id) }}"
                                       class="h-8 px-3 inline-flex items-center justify-center text-sm rounded-md
                                              bg-transparent hover:bg-zinc-800/5 text-zinc-800">
                                        Edit
                                    </a>

                                    <form action="{{ route('products.toggleStatus', $product->id) }}" method="POST" class="inline">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                                class="h-8 px-3 inline-flex items-center justify-center text-sm rounded-md
                                                       bg-transparent hover:bg-zinc-800/5 text-zinc-800">
                                            {{ $product->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>

                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="btn-hapus h-8 px-3 inline-flex items-center justify-center text-sm rounded-md
                                                       bg-transparent hover:bg-zinc-800/5">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500">
                                {{ request()->hasAny(['search', 'brand', 'category', 'type', 'status'])
                                    ? 'Tidak ada produk yang cocok dengan filter.'
                                    : 'Belum ada produk yang tersedia.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if (method_exists($products, 'links') && $products->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection