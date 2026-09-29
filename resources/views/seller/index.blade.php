@extends('layouts.sidebar')

@section('title', 'Seller — Zakira Admin')

@section('content')

<div class="space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-900">Seller</h1>
        <div class="flex items-center space-x-4">
            <div class="text-sm text-gray-500">Total: {{ $sellers->count() }} seller</div>
            @if (! $showCreateForm && ! $editingSeller)
                <a href="{{ route('seller.index', ['form' => 'create']) }}"
                   class="relative items-center font-medium justify-center gap-2 whitespace-nowrap h-10 text-sm rounded-lg px-4 inline-flex bg-[var(--color-accent)] hover:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_10%)] text-[var(--color-accent-foreground)] border border-black/10 dark:border-0 shadow-xs transition">
                    <span>Tambah Seller</span>
                </a>
            @endif
        </div>
    </div>

    @if (session('success'))
        <div class="bg-green-50 text-green-700 text-sm rounded-lg px-4 py-3">{{ session('success') }}</div>
    @endif

    {{-- ==== FORM TAMBAH ==== --}}
    @if ($showCreateForm)
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Buat Seller</h2>

            @if ($errors->any())
                <div class="bg-red-50 text-red-600 text-sm rounded-lg px-4 py-3 mb-4">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('seller.store') }}" class="grid md:grid-cols-2 gap-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">ID Seller</label>
                    <input type="text" name="seller_id" value="{{ old('seller_id') }}" placeholder="contoh: zahwa123" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-zinc-300 focus:border-zinc-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Masukkan nama seller" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-zinc-300 focus:border-zinc-400">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Brand</label>
                    <select name="brand_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-zinc-300 focus:border-zinc-400">
                        <option value="">-- Pilih Brand --</option>
                        @foreach ($brands as $b)
                            <option value="{{ $b->id }}" @selected((int) old('brand_id') === $b->id)>
                                {{ $b->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2 flex gap-3 mt-2">
                    <button type="submit"
                            class="h-10 px-4 rounded-lg text-sm font-medium bg-[var(--color-accent)] hover:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_10%)] text-[var(--color-accent-foreground)] transition">
                        Buat Seller
                    </button>
                    <a href="{{ route('seller.index') }}"
                       class="h-10 px-4 inline-flex items-center rounded-lg text-sm font-medium border border-gray-300 text-gray-700 hover:bg-gray-50 transition">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    @endif

    {{-- ==== FORM EDIT ==== --}}
    @if ($editingSeller)
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Edit Seller</h2>

            @if ($errors->any())
                <div class="bg-red-50 text-red-600 text-sm rounded-lg px-4 py-3 mb-4">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('seller.update', $editingSeller) }}" class="grid md:grid-cols-2 gap-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">ID Seller</label>
                    <input type="text" name="seller_id" value="{{ old('seller_id', $editingSeller->seller_id) }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-zinc-300 focus:border-zinc-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama</label>
                    <input type="text" name="name" value="{{ old('name', $editingSeller->name) }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-zinc-300 focus:border-zinc-400">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Brand</label>
                    <select name="brand_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-zinc-300 focus:border-zinc-400">
                        <option value="">-- Pilih Brand --</option>
                        @foreach ($brands as $b)
                            <option value="{{ $b->id }}" @selected((int) old('brand_id', $editingSeller->brand_id) === $b->id)>
                                {{ $b->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2 flex gap-3 mt-2">
                    <button type="submit"
                            class="h-10 px-4 rounded-lg text-sm font-medium bg-[var(--color-accent)] hover:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_10%)] text-[var(--color-accent-foreground)] transition">
                        Perbarui Seller
                    </button>
                    <a href="{{ route('seller.index') }}"
                       class="h-10 px-4 inline-flex items-center rounded-lg text-sm font-medium border border-gray-300 text-gray-700 hover:bg-gray-50 transition">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="seller-search" class="block text-sm font-medium text-gray-700 mb-2">Cari Seller</label>
                <input type="text" id="seller-search" placeholder="ID Seller, nama, brand..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-zinc-300 focus:border-zinc-400">
            </div>

            <div>
                <label for="seller-brand-filter" class="block text-sm font-medium text-gray-700 mb-2">Brand</label>
                <select id="seller-brand-filter"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-zinc-300 focus:border-zinc-400">
                    <option value="">Semua Brand</option>
                    @foreach ($brands as $b)
                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end">
                <button type="button" id="seller-reset"
                        class="w-full bg-gray-100 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-200 transition-colors">
                    Reset Filter
                </button>
            </div>
        </div>
    </div>

    <!-- Tabel Seller -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Seller</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Brand</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($sellers as $seller)
                    <tr data-seller-row
                        data-brand="{{ $seller->brand_id }}"
                        data-search="{{ strtolower($seller->seller_id . ' ' . $seller->name . ' ' . ($seller->brand->name ?? '')) }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $seller->seller_id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $seller->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $seller->brand->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('seller.index', ['edit' => $seller->id]) }}"
                                   class="h-8 px-3 inline-flex items-center rounded-md text-sm hover:bg-zinc-800/5 text-zinc-800 transition">Edit</a>

                                <form method="POST" action="{{ route('seller.destroy', $seller) }}"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus seller ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="h-8 px-3 inline-flex items-center rounded-md text-sm hover:bg-zinc-800/5 text-red-600 hover:text-red-800 transition">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-400">Tidak ada seller ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

<script>
    (function () {
        if (window.__sellerPageInit) return;
        window.__sellerPageInit = true;

        function applyFilter() {
            var q = (document.getElementById('seller-search')?.value || '').toLowerCase();
            var brand = document.getElementById('seller-brand-filter')?.value || '';
            document.querySelectorAll('[data-seller-row]').forEach(function (row) {
                var okBrand = brand === '' || row.dataset.brand === brand;
                var okText = q === '' || row.dataset.search.includes(q);
                row.style.display = okBrand && okText ? '' : 'none';
            });
        }

        document.addEventListener('input', function (e) {
            if (e.target.id === 'seller-search') applyFilter();
        });
        document.addEventListener('change', function (e) {
            if (e.target.id === 'seller-brand-filter') applyFilter();
        });
        document.addEventListener('click', function (e) {
            if (e.target.closest('#seller-reset')) {
                document.getElementById('seller-search').value = '';
                document.getElementById('seller-brand-filter').value = '';
                applyFilter();
            }
        });
    })();
</script>
@endsection