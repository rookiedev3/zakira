@extends('layouts.sidebar')

@section('title', 'Laporan Penjualan Mitra — Zakira Admin')

@section('content')
<div class="space-y-6">
    
    <!-- Header Halaman & Tombol Reset Filter -->
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900">Laporan Penjualan Mitra</h1>
            <p class="text-sm text-zinc-500 mt-0.5">Monitoring performa brand dan seller secara real-time</p>
        </div>
        <div>
            <a href="#" class="text-xs font-semibold text-zinc-700 bg-white border border-zinc-200 px-3.5 py-2 rounded-lg hover:bg-zinc-50 transition shadow-sm inline-block">
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
                <h3 class="text-3xl font-bold text-zinc-900 mt-2">0</h3>
            </div>
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center shadow-inner">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                </svg>
            </div>
        </div>

        <!-- Kartu 2: Total Penjualan (September 2026) -->
        <div class="bg-white p-6 rounded-2xl border border-zinc-200 shadow-sm flex justify-between items-center">
            <div>
                <p class="text-zinc-500 text-xs font-medium">Total Penjualan (September 2026)</p>
                <h3 class="text-2xl font-bold text-emerald-600 mt-2">Rp 1.590.000</h3>
            </div>
            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center shadow-inner">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.75A.75.75 0 0 1 3 4.5h.75m0 0h15.75A2.25 2.25 0 0 1 21 6.75v10.5m-18 0V6.75m18 10.5h-18" />
                </svg>
            </div>
        </div>

        <!-- Kartu 3: Total Penjualan (August 2026) -->
        <div class="bg-white p-6 rounded-2xl border border-zinc-200 shadow-sm flex justify-between items-center">
            <div>
                <p class="text-zinc-500 text-xs font-medium">Total Penjualan (August 2026)</p>
                <h3 class="text-2xl font-bold text-zinc-900 mt-2">Rp 0</h3>
            </div>
            <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center shadow-inner">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.28m5.94 2.28-2.28 5.941" />
                </svg>
            </div>
        </div>

    </div>

    <!-- Kotak Filter Form -->
    <div class="bg-white p-6 rounded-2xl border border-zinc-200 shadow-sm space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- Filter Brand -->
            <div>
                <label class="block text-xs font-semibold text-zinc-600 mb-1.5">Brand</label>
                <select class="w-full text-xs bg-white border border-zinc-200 rounded-xl px-3 py-2.5 text-zinc-800 focus:outline-none focus:border-zinc-400">
                    <option>Semua Brand</option>
                </select>
            </div>

            <!-- Filter ID Seller -->
            <div>
                <label class="block text-xs font-semibold text-zinc-600 mb-1.5">ID Seller</label>
                <input type="text" placeholder="Cari berdasarkan ID seller" class="w-full text-xs bg-white border border-zinc-200 rounded-xl px-3 py-2.5 text-zinc-800 placeholder-zinc-400 focus:outline-none focus:border-zinc-400">
            </div>

            <!-- Filter Periode -->
            <div>
                <label class="block text-xs font-semibold text-zinc-600 mb-1.5">Periode</label>
                <select class="w-full text-xs bg-white border border-zinc-200 rounded-xl px-3 py-2.5 text-zinc-800 focus:outline-none focus:border-zinc-400">
                    <option>Per Bulan</option>
                    <option>Per Tahun</option>
                </select>
            </div>

            <!-- Filter Tahun -->
            <div>
                <label class="block text-xs font-semibold text-zinc-600 mb-1.5">Tahun</label>
                <select class="w-full text-xs bg-white border border-zinc-200 rounded-xl px-3 py-2.5 text-zinc-800 focus:outline-none focus:border-zinc-400">
                    <option>2026</option>
                    <option>2025</option>
                </select>
            </div>

        </div>

        <!-- Filter Bulan (Baris Kedua) -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 pt-2">
            <div>
                <label class="block text-xs font-semibold text-zinc-600 mb-1.5">Bulan</label>
                <select class="w-full text-xs bg-white border border-zinc-200 rounded-xl px-3 py-2.5 text-zinc-800 focus:outline-none focus:border-zinc-400">
                    <option>September</option>
                    <option>August</option>
                    <option>July</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Tabel Detail Closing -->
    <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm overflow-hidden">
        
        <!-- Header Tabel & Tombol Export -->
        <div class="p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-zinc-100">
            <div>
                <h3 class="font-bold text-zinc-900 text-base">Detail Closing</h3>
                <p class="text-xs text-zinc-500 mt-0.5">Filter per brand, ID seller, dan periode (bulanan/tahunan)</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="#" class="px-4 py-2 bg-[#8C6239] hover:bg-[#724e2c] text-white text-xs font-semibold rounded-xl transition shadow-sm">
                    Export Excel
                </a>
                <span class="px-3 py-1.5 bg-zinc-100 text-zinc-600 text-xs font-medium rounded-lg">
                    6 data
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
                    
                    <!-- Baris 1 -->
                    <tr>
                        <td class="py-4 px-6 font-medium text-zinc-800">24 Sep 2026</td>
                        <td class="py-4 px-6 font-semibold">ZAKIRA</td>
                        <td class="py-4 px-6 text-zinc-400">-</td>
                        <td class="py-4 px-6">Baju Koko</td>
                        <td class="py-4 px-6 text-right">1</td>
                        <td class="py-4 px-6 text-right font-bold text-zinc-900">Rp 100.000</td>
                        <td class="py-4 px-6 text-center space-x-1">
                            <span class="px-2.5 py-1 bg-zinc-100 text-zinc-700 rounded-md font-medium text-[10px]">Pending</span>
                            <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-md font-medium text-[10px]">Paid</span>
                        </td>
                    </tr>

                    <!-- Baris 2 -->
                    <tr>
                        <td class="py-4 px-6 font-medium text-zinc-800">22 Sep 2026</td>
                        <td class="py-4 px-6 font-semibold">ZAKIRA</td>
                        <td class="py-4 px-6 text-zinc-600 font-mono">asdk123</td>
                        <td class="py-4 px-6">Baju Koko</td>
                        <td class="py-4 px-6 text-right">3</td>
                        <td class="py-4 px-6 text-right font-bold text-zinc-900">Rp 500.000</td>
                        <td class="py-4 px-6 text-center space-x-1">
                            <span class="px-2.5 py-1 bg-zinc-100 text-zinc-700 rounded-md font-medium text-[10px]">Pending</span>
                            <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-md font-medium text-[10px]">Paid</span>
                        </td>
                    </tr>

                    <!-- Baris 3 -->
                    <tr>
                        <td class="py-4 px-6 font-medium text-zinc-800">22 Sep 2026</td>
                        <td class="py-4 px-6 font-semibold">ZAKIRA</td>
                        <td class="py-4 px-6 text-zinc-400">-</td>
                        <td class="py-4 px-6">Baju Koko</td>
                        <td class="py-4 px-6 text-right">1</td>
                        <td class="py-4 px-6 text-right font-bold text-zinc-900">Rp 100.000</td>
                        <td class="py-4 px-6 text-center space-x-1">
                            <span class="px-2.5 py-1 bg-zinc-100 text-zinc-700 rounded-md font-medium text-[10px]">Pending</span>
                            <span class="px-2.5 py-1 bg-amber-100 text-amber-800 rounded-md font-medium text-[10px]">Pending</span>
                        </td>
                    </tr>

                    <!-- Baris 4 -->
                    <tr>
                        <td class="py-4 px-6 font-medium text-zinc-800">18 Sep 2026</td>
                        <td class="py-4 px-6 font-semibold">ZAKIRA</td>
                        <td class="py-4 px-6 text-zinc-600 font-mono">asdk123</td>
                        <td class="py-4 px-6">Baju Koko</td>
                        <td class="py-4 px-6 text-right">1</td>
                        <td class="py-4 px-6 text-right font-bold text-zinc-900">Rp 100.000</td>
                        <td class="py-4 px-6 text-center space-x-1">
                            <span class="px-2.5 py-1 bg-zinc-100 text-zinc-700 rounded-md font-medium text-[10px]">Pending</span>
                            <span class="px-2.5 py-1 bg-amber-100 text-amber-800 rounded-md font-medium text-[10px]">Pending</span>
                        </td>
                    </tr>

                    <!-- Baris 5 -->
                    <tr>
                        <td class="py-4 px-6 font-medium text-zinc-800">18 Sep 2026</td>
                        <td class="py-4 px-6 font-semibold">ZAKIRA</td>
                        <td class="py-4 px-6 text-zinc-400">-</td>
                        <td class="py-4 px-6">Baju Koko</td>
                        <td class="py-4 px-6 text-right">1</td>
                        <td class="py-4 px-6 text-right font-bold text-zinc-900">Rp 300.000</td>
                        <td class="py-4 px-6 text-center space-x-1">
                            <span class="px-2.5 py-1 bg-zinc-100 text-zinc-700 rounded-md font-medium text-[10px]">Pending</span>
                            <span class="px-2.5 py-1 bg-amber-100 text-amber-800 rounded-md font-medium text-[10px]">Pending</span>
                        </td>
                    </tr>

                    <!-- Baris 6 -->
                    <tr>
                        <td class="py-4 px-6 font-medium text-zinc-800">17 Sep 2026</td>
                        <td class="py-4 px-6 font-semibold">ZAKIRA</td>
                        <td class="py-4 px-6 text-zinc-600 font-mono">asdk123</td>
                        <td class="py-4 px-6">Baju Koko</td>
                        <td class="py-4 px-6 text-right">5</td>
                        <td class="py-4 px-6 text-right font-bold text-zinc-900">Rp 990.000</td>
                        <td class="py-4 px-6 text-center space-x-1">
                            <span class="px-2.5 py-1 bg-zinc-100 text-zinc-700 rounded-md font-medium text-[10px]">Processing</span>
                            <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-md font-medium text-[10px]">Paid</span>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>

    </div>

</div>
@endsection