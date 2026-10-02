@extends('layouts.sidebar')

@section('title', 'Laporan Penjualan Mitra — Zakira Admin')

@section('content')
@php
    $rupiah = fn ($n) => 'Rp ' . number_format((int) $n, 0, ',', '.');

    // Status order (Pending, Processing, Shipped, dll.): selalu abu-abu
    $statusClass = fn ($s) => 'bg-zinc-100 text-zinc-700';

    // Status pembayaran: Paid = hijau, Pending = kuning
    $paymentClass = fn ($s) => match (strtolower((string) $s)) {
        'paid'    => 'bg-emerald-100 text-emerald-800',
        'pending' => 'bg-yellow-100 text-yellow-800',
        default   => 'bg-zinc-100 text-zinc-700',
    };
@endphp

<div class="space-y-6">

    <!-- Header Halaman & Tombol Reset Filter -->
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900">Laporan Penjualan Mitra</h1>
            <p class="text-sm text-zinc-500 mt-0.5">Monitoring performa brand dan seller secara real-time</p>
        </div>
        <div>
            <a href="{{ route('admin.laporan') }}" class="text-xs font-semibold text-zinc-700 bg-white border border-zinc-200 px-3.5 py-2 rounded-lg hover:bg-zinc-50 transition shadow-sm inline-block">
                Reset Filter
            </a>
        </div>
    </div>

    <!-- 3 Kartu Statistik Atas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Kartu 1: Order/Closing Hari Ini -->
        <div class="bg-white p-6 rounded-2xl border border-zinc-200 shadow-sm flex justify-between items-center">
            <div>
                <p class="text-zinc-500 text-xs font-medium">Order/Closing Hari Ini</p>
                <h3 class="text-3xl font-bold text-zinc-900 mt-2">{{ number_format($closingHariIni, 0, ',', '.') }}</h3>
            </div>
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center shadow-inner">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                </svg>
            </div>
        </div>

        <!-- Kartu 2: Total Penjualan Bulan Ini -->
        <div class="bg-white p-6 rounded-2xl border border-zinc-200 shadow-sm flex justify-between items-center">
            <div>
                <p class="text-zinc-500 text-xs font-medium">Total Penjualan ({{ $labelBulanIni }})</p>
                <h3 class="text-2xl font-bold text-emerald-600 mt-2">{{ $rupiah($totalBulanIni) }}</h3>
            </div>
            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center shadow-inner">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.75A.75.75 0 0 1 3 4.5h.75m0 0h15.75A2.25 2.25 0 0 1 21 6.75v10.5m-18 0V6.75m18 10.5h-18" />
                </svg>
            </div>
        </div>

        <!-- Kartu 3: Total Penjualan Bulan Lalu -->
        <div class="bg-white p-6 rounded-2xl border border-zinc-200 shadow-sm flex justify-between items-center">
            <div>
                <p class="text-zinc-500 text-xs font-medium">Total Penjualan ({{ $labelBulanLalu }})</p>
                <h3 class="text-2xl font-bold text-zinc-900 mt-2">{{ $rupiah($totalBulanLalu) }}</h3>
            </div>
            <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center shadow-inner">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.28m5.94 2.28-2.28 5.941" />
                </svg>
            </div>
        </div>

    </div>

    <!-- Kotak Filter Form -->
    <form method="GET" action="{{ route('admin.laporan') }}" id="filter-form"
          class="bg-white p-6 rounded-2xl border border-zinc-200 shadow-sm space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

            <!-- Filter Brand -->
            <div>
                <label class="block text-xs font-semibold text-zinc-600 mb-1.5">Brand</label>
                <select name="brand_id" onchange="this.form.submit()"
                        class="w-full text-xs bg-white border border-zinc-200 rounded-xl px-3 py-2.5 text-zinc-800 focus:outline-none focus:border-zinc-400">
                    <option value="">Semua Brand</option>
                    @foreach ($brands as $brand)
                        <option value="{{ $brand->id }}" @selected((string) $brandId === (string) $brand->id)>{{ $brand->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter ID Seller -->
            <div>
                <label class="block text-xs font-semibold text-zinc-600 mb-1.5">ID Seller</label>
                <input type="text" name="seller_id" value="{{ $sellerId }}" placeholder="Cari berdasarkan ID seller"
                       class="w-full text-xs bg-white border border-zinc-200 rounded-xl px-3 py-2.5 text-zinc-800 placeholder-zinc-400 focus:outline-none focus:border-zinc-400">
            </div>

            <!-- Filter Periode -->
            <div>
                <label class="block text-xs font-semibold text-zinc-600 mb-1.5">Periode</label>
                <select name="periode" id="periode" onchange="this.form.submit()"
                        class="w-full text-xs bg-white border border-zinc-200 rounded-xl px-3 py-2.5 text-zinc-800 focus:outline-none focus:border-zinc-400">
                    <option value="bulan" @selected($periode === 'bulan')>Per Bulan</option>
                    <option value="tahun" @selected($periode === 'tahun')>Per Tahun</option>
                </select>
            </div>

            <!-- Filter Tahun -->
            <div>
                <label class="block text-xs font-semibold text-zinc-600 mb-1.5">Tahun</label>
                <select name="tahun" onchange="this.form.submit()"
                        class="w-full text-xs bg-white border border-zinc-200 rounded-xl px-3 py-2.5 text-zinc-800 focus:outline-none focus:border-zinc-400">
                    @foreach ($daftarTahun as $th)
                        <option value="{{ $th }}" @selected($tahun === $th)>{{ $th }}</option>
                    @endforeach
                </select>
            </div>

        </div>

        <!-- Filter Bulan (Baris Kedua, hanya tampil saat Per Bulan) -->
        <div id="filter-bulan" class="grid grid-cols-1 sm:grid-cols-4 gap-4 pt-2 {{ $periode === 'tahun' ? 'hidden' : '' }}">
            <div>
                <label class="block text-xs font-semibold text-zinc-600 mb-1.5">Bulan</label>
                <select name="bulan" onchange="this.form.submit()"
                        class="w-full text-xs bg-white border border-zinc-200 rounded-xl px-3 py-2.5 text-zinc-800 focus:outline-none focus:border-zinc-400">
                    @foreach ($daftarBulan as $num => $nama)
                        <option value="{{ $num }}" @selected($bulan === $num)>{{ $nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Submit tersembunyi supaya tekan Enter di kolom ID Seller langsung mencari -->
        <button type="submit" class="hidden">Cari</button>
    </form>

    <!-- Tabel Detail Closing -->
    <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm overflow-hidden">

        <!-- Header Tabel & Tombol Export -->
        <div class="p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-zinc-100">
            <div>
                <h3 class="font-bold text-zinc-900 text-base">Detail Closing</h3>
                <p class="text-xs text-zinc-500 mt-0.5">Filter per brand, ID seller, dan periode (bulanan/tahunan)</p>
            </div>
<div class="flex flex-wrap items-center gap-3">
    <a href="{{ route('admin.laporan.export', request()->except('page')) }}"
       class="px-4 py-2 bg-[#8C6239] hover:bg-[#724e2c] text-white text-xs font-semibold rounded-xl transition shadow-sm">
        Export Excel
    </a>
    <span class="px-3 py-1.5 bg-zinc-100 text-zinc-600 text-xs font-medium rounded-lg">
        {{ number_format($closings->total(), 0, ',', '.') }} data
    </span>
</div>
        </div>

        <!-- Tabel Data -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-zinc-100 text-zinc-400 uppercase tracking-wider font-bold text-[10px]">
                        <th class="py-4 px-6">Tanggal</th>
                        <th class="py-4 px-6">Brand</th>
                        <th class="py-4 px-6">ID Seller</th>
                        <th class="py-4 px-6">Produk</th>
                        <th class="py-4 px-6 text-right">Jumlah Produk</th>
                        <th class="py-4 px-6 text-right">Total (RP)</th>
                        <th class="py-4 px-6 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 text-zinc-700">
                    @forelse ($closings as $row)
                        <tr>
                            <td class="py-4 px-6 font-medium text-zinc-800 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($row->tanggal)->format('d M Y') }}
                            </td>
                            <td class="py-4 px-6 font-semibold uppercase">{{ $row->brand_name }}</td>
                            <td class="py-4 px-6 {{ $row->seller_id ? 'text-zinc-600 font-mono' : 'text-zinc-400' }}">
                                {{ $row->seller_id ?: '-' }}
                            </td>
                            <td class="py-4 px-6">{{ $row->produk ?: '-' }}</td>
                            <td class="py-4 px-6 text-right">{{ number_format((int) $row->total_qty, 0, ',', '.') }}</td>
                            <td class="py-4 px-6 text-right font-bold text-zinc-900 whitespace-nowrap">{{ $rupiah($row->total) }}</td>
                            <td class="py-4 px-6 text-center space-x-1 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-md font-medium text-[10px] {{ $statusClass($row->status) }}">
                                    {{ ucfirst($row->status) }}
                                </span>
                                <span class="px-2.5 py-1 rounded-md font-medium text-[10px] {{ $paymentClass($row->payment_status) }}">
                                    {{ ucfirst($row->payment_status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 px-6 text-center text-zinc-400">
                                Belum ada closing pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($closings->hasPages())
            <div class="px-6 py-4 border-t border-zinc-100">
                {{ $closings->links() }}
            </div>
        @endif

    </div>

</div>
@endsection