@extends('layouts.sidebar')

@section('title', 'Seller — Zakira Admin')

@section('content')

<div class="space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900">Seller</h1>
        <div class="flex items-center space-x-4">
            <div class="text-sm text-gray-500">Total: {{ $sellers->count() }} seller</div>
            @if (! $showCreateForm && ! $editingSeller)
                <a href="{{ route('seller.index', ['form' => 'create']) }}"
                   class="relative items-center font-medium justify-center gap-2 whitespace-nowrap h-10 text-sm rounded-lg px-5 inline-flex bg-[#8B5E3C] hover:bg-[#7a5134] text-white shadow-sm transition-colors">
                    <span>Tambah Seller</span>
                </a>
            @endif
        </div>
    </div>

    @if (session('success'))
        <div class="bg-green-50 text-green-700 text-sm rounded-lg px-4 py-3 border border-green-200">{{ session('success') }}</div>
    @endif

    {{-- ==== FORM TAMBAH ==== --}}
    @if ($showCreateForm)
        <div class="bg-white shadow-[0_1px_4px_rgba(0,0,0,0.08)] rounded-xl p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Buat Seller</h2>

            @if ($errors->any())
                <div class="bg-red-50 text-red-600 text-sm rounded-lg px-4 py-3 mb-4">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('seller.store') }}" class="grid md:grid-cols-2 gap-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">ID Seller</label>
                    <input type="text" name="seller_id" value="{{ old('seller_id') }}" placeholder="contoh: zahwa123" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#8B5E3C] focus:border-[#8B5E3C]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Masukkan nama seller" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#8B5E3C] focus:border-[#8B5E3C]">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Brand</label>
                    <select name="brand_id" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-[#8B5E3C] focus:border-[#8B5E3C]">
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
                            class="h-10 px-5 rounded-lg text-sm font-medium bg-[#8B5E3C] hover:bg-[#7a5134] text-white shadow-sm transition-colors">
                        Buat Seller
                    </button>
                    <a href="{{ route('seller.index') }}"
                       class="h-10 px-6 inline-flex items-center rounded-lg text-sm font-medium border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    @endif

    {{-- ==== FORM EDIT ==== --}}
    @if ($editingSeller)
        <div class="bg-white shadow-[0_1px_4px_rgba(0,0,0,0.08)] rounded-xl p-6">
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
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-[#8B5E3C] focus:border-[#8B5E3C]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama</label>
                    <input type="text" name="name" value="{{ old('name', $editingSeller->name) }}" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-[#8B5E3C] focus:border-[#8B5E3C]">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Brand</label>
                    <select name="brand_id" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-[#8B5E3C] focus:border-[#8B5E3C]">
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
                            class="h-10 px-5 rounded-lg text-sm font-medium bg-[#8B5E3C] hover:bg-[#7a5134] text-white shadow-sm transition-colors">
                        Perbarui Seller
                    </button>
                    <a href="{{ route('seller.index') }}"
                       class="h-10 px-6 inline-flex items-center rounded-lg text-sm font-medium border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white shadow-[0_1px_4px_rgba(0,0,0,0.08)] rounded-xl p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label for="seller-search" class="block text-sm font-medium text-gray-700 mb-2">Cari Seller</label>
                <input type="text" id="seller-search" placeholder="ID Seller, nama, brand..."
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#8B5E3C] focus:border-[#8B5E3C]">
            </div>

            <div>
                <label for="seller-brand-filter" class="block text-sm font-medium text-gray-700 mb-2">Brand</label>
                <select id="seller-brand-filter"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-[#8B5E3C] focus:border-[#8B5E3C]">
                    <option value="">Semua Brand</option>
                    @foreach ($brands as $b)
                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end">
                <button type="button" id="seller-reset"
                        class="w-full h-10 bg-gray-100 text-gray-700 text-sm font-medium px-5 rounded-lg hover:bg-gray-200 transition-colors">
                    Reset Filter
                </button>
            </div>
        </div>
    </div>

    <!-- Tabel Seller -->
    <div class="bg-white shadow-[0_1px_4px_rgba(0,0,0,0.08)] rounded-xl overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">ID Seller</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Brand</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wide">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($sellers as $seller)
                    <tr data-seller-row
                        data-brand="{{ $seller->brand_id }}"
                        data-search="{{ strtolower($seller->seller_id . ' ' . $seller->name . ' ' . ($seller->brand->name ?? '')) }}"
                        class="hover:bg-gray-50/60 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $seller->seller_id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $seller->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $seller->brand->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('seller.index', ['edit' => $seller->id]) }}"
                                   class="h-8 px-3 inline-flex items-center rounded-md text-sm hover:bg-zinc-800/5 text-gray-900 transition-colors">Edit</a>

                                <form method="POST" action="{{ route('seller.destroy', $seller) }}"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus seller ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="h-8 px-3 inline-flex items-center rounded-md text-sm hover:bg-zinc-800/5 text-gray-900 transition-colors">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500">Tidak ada seller ditemukan.</td>
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