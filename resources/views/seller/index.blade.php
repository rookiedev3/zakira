@extends('layouts.sidebar')

@section('title', 'Seller — Zakira Admin')

@section('content')
@php
    // ===== DATA DUMMY (ganti dengan data controller nanti) =====
    $brands = [
        ['id' => 1, 'nama' => 'ZAKIRA'],
    ];
    $sellers = [
        ['id' => 1, 'seller_id' => 'asdk123', 'nama' => 'AKN', 'brand_id' => 1, 'brand' => 'ZAKIRA'],
    ];
@endphp

<div class="space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-900">Seller</h1>
        <div class="flex items-center space-x-4">
            <div class="text-sm text-gray-500">Total: {{ count($sellers) }} seller</div>
            <a href="{{ route('seller.create') }}"
               class="relative items-center font-medium justify-center gap-2 whitespace-nowrap h-10 text-sm rounded-lg px-4 inline-flex bg-[var(--color-accent)] hover:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_10%)] text-[var(--color-accent-foreground)] border border-black/10 dark:border-0 shadow-xs transition">
                <span>Tambah Seller</span>
            </a>
        </div>
    </div>

    {{-- Slot form: diisi oleh create.blade.php / edit.blade.php, kosong di halaman index --}}
    @yield('form')

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
                        <option value="{{ $b['id'] }}">{{ $b['nama'] }}</option>
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
                @foreach ($sellers as $seller)
                    <tr data-seller-row
                        data-brand="{{ $seller['brand_id'] }}"
                        data-search="{{ strtolower($seller['seller_id'] . ' ' . $seller['nama'] . ' ' . $seller['brand']) }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $seller['seller_id'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $seller['nama'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $seller['brand'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('seller.edit', $seller['id']) }}"
                                   class="h-8 px-3 inline-flex items-center rounded-md text-sm hover:bg-zinc-800/5 text-zinc-800 transition">Edit</a>
                                <button type="button" data-delete-seller
                                        class="h-8 px-3 inline-flex items-center rounded-md text-sm hover:bg-zinc-800/5 text-red-600 hover:text-red-800 transition">Hapus</button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>

<script>
    (function () {
        // Hindari pendaftaran ganda saat berpindah halaman lewat wire:navigate
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
            if (e.target.closest('[data-delete-seller]')) {
                confirm('Apakah Anda yakin ingin menghapus seller ini?');
            }
        });
    })();
</script>
@endsection