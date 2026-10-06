@extends('layouts.sidebar')

@section('title', 'Dashboard Admin — Zakira Moslem Hijab Identity')

@section('content')
@php
    // Format rupiah
    $rp = fn ($n) => 'Rp ' . number_format((int) $n, 0, ',', '.');

    // Link aman: bila nama route belum ada, jatuh ke '#'
    $link = fn ($name) => \Illuminate\Support\Facades\Route::has($name) ? route($name) : '#';
@endphp

<div class="max-w-7xl mx-auto px-6 py-8 space-y-10">

    <!-- Header Dashboard -->
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
        <p class="text-gray-500 text-sm mt-0.5">Selamat datang di panel admin Zakira</p>
    </div>

    <!-- Baris 1: 4 Kartu Statistik Utama -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

        <!-- Total Pesanan -->
        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex justify-between items-center">
            <div>
                <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Total Pesanan</p>
                <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['totalOrders'], 0, ',', '.') }}</h3>
            </div>
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center shadow-inner">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
            </div>
        </div>

        <!-- Total Pendapatan -->
        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex justify-between items-center">
            <div>
                <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Total Pendapatan</p>
                <h3 class="text-xl font-bold text-emerald-600 mt-2">{{ $rp($stats['totalRevenue']) }}</h3>
            </div>
            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center shadow-inner">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
        </div>

        <!-- Total Pelanggan -->
        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex justify-between items-center">
            <div>
                <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Total Pelanggan</p>
                <h3 class="text-3xl font-bold text-purple-600 mt-2">{{ number_format($stats['totalCustomers'], 0, ',', '.') }}</h3>
            </div>
            <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center shadow-inner">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
            </div>
        </div>

        <!-- Produk Aktif -->
        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex justify-between items-center">
            <div>
                <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Produk Aktif</p>
                <h3 class="text-3xl font-bold text-amber-600 mt-2">{{ number_format($stats['totalProducts'], 0, ',', '.') }}</h3>
            </div>
            <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center shadow-inner">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
            </div>
        </div>

    </div>

    <!-- Baris 2: Status Pesanan -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">

        <!-- Pesanan Pending -->
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex justify-between items-center">
            <div>
                <p class="text-gray-500 text-xs font-medium">Pesanan Pending</p>
                <h4 class="text-2xl font-bold text-amber-600 mt-1">{{ $orderStatus['pending'] }}</h4>
            </div>
            <div class="w-10 h-10 bg-amber-50 text-amber-600 rounded-full flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
        </div>

        <!-- Pesanan Diproses -->
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex justify-between items-center">
            <div>
                <p class="text-gray-500 text-xs font-medium">Pesanan Diproses</p>
                <h4 class="text-2xl font-bold text-blue-600 mt-1">{{ $orderStatus['processing'] }}</h4>
            </div>
            <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
            </div>
        </div>

        <!-- Pesanan Selesai -->
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex justify-between items-center">
            <div>
                <p class="text-gray-500 text-xs font-medium">Pesanan Selesai</p>
                <h4 class="text-2xl font-bold text-emerald-600 mt-1">{{ $orderStatus['completed'] }}</h4>
            </div>
            <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            </div>
        </div>

    </div>

    <!-- Baris 3: Statistik Down Payment (DP) -->
    <div class="bg-white p-7 rounded-2xl border border-gray-100 shadow-sm">
        <h3 class="text-xl font-medium text-gray-900 mb-5">Statistik Down Payment</h3>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-x-4 gap-y-8 text-center">
            <div>
                <p class="text-[#155dfc] font-medium text-xl">{{ $dpStats['total_dp_orders'] }}</p>
                <span class="text-gray-500 text-base mt-2 block">Total Pesanan DP</span>
            </div>
            <div>
                <p class="text-[#00a63e] font-medium text-xl">{{ $dpStats['paid_dp'] }}</p>
                <span class="text-gray-500 text-base mt-2 block">DP Terbayar</span>
            </div>
            <div>
                <p class="text-[#d08700] font-medium text-xl">{{ $dpStats['pending_dp'] }}</p>
                <span class="text-gray-500 text-base mt-2 block">DP Pending</span>
            </div>
            <div>
                <p class="text-[#9810fa] font-medium text-xl">{{ $dpStats['fully_paid'] }}</p>
                <span class="text-gray-500 text-base mt-2 block">Lunas Penuh</span>
            </div>
            <div>
                <p class="text-[#4f39f6] font-medium text-xl">{{ $rp($dpStats['dp_revenue']) }}</p>
                <span class="text-gray-500 text-base mt-2 block">Pendapatan DP</span>
            </div>
            <div>
                <p class="text-[#009689] font-medium text-xl">{{ $rp($dpStats['remaining_revenue']) }}</p>
                <span class="text-gray-500 text-base mt-2 block">Pendapatan Sisa</span>
            </div>
        </div>
    </div>

    <!-- Baris 4: Pesanan Terbaru & Produk Terpopuler -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">

        <!-- Pesanan Terbaru -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="flex justify-between items-center px-7 py-6 border-b border-gray-200">
                <h3 class="text-xl font-medium text-gray-900">Pesanan Terbaru</h3>
                <a href="{{ $link('admin.orders.index') }}" class="text-base font-medium text-gray-900 mr-4 hover:underline">Lihat Semua</a>
            </div>

            <div class="px-7 pt-3">
                @forelse ($recentOrders as $order)
                    <div class="flex justify-between items-start gap-4 pt-8 pb-4 border-b border-gray-100 last:border-b-0">
                        <div>
                            <p class="text-base text-gray-500">{{ $order['number'] }}</p>
                            <p class="text-base text-gray-600 mt-1">{{ $order['customer'] }}</p>
                            <p class="text-base text-gray-500 mt-1">{{ $order['date'] }}</p>
                        </div>
                        <div class="flex items-start gap-5">
                            <span class="{{ $order['status_badge'] }} text-sm font-medium px-3 py-1 rounded-lg">{{ $order['status'] }}</span>
                            <div class="text-right mt-1.5">
                                <p class="text-xl font-semibold text-gray-900">{{ $rp($order['amount']) }}</p>
                                <span class="inline-block mt-1 {{ $order['payment_badge'] }} text-sm font-medium px-3 py-1 rounded-lg">{{ $order['payment_type'] }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="py-10 text-center text-sm text-gray-400">Belum ada pesanan.</p>
                @endforelse
            </div>
        </div>

        <!-- Produk Terpopuler -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="flex justify-between items-center px-7 py-6 border-b border-gray-200">
                <h3 class="text-xl font-medium text-gray-900">Produk Terpopuler</h3>
                <a href="{{ $link('products.index') }}" class="text-base font-medium text-gray-900 mr-4 hover:underline">Lihat Semua</a>
            </div>

            <div class="px-7 pt-3">
                @forelse ($topProducts as $product)
                    <div class="flex justify-between items-center pt-8 pb-4 border-b border-gray-100 last:border-b-0">
                        <div class="flex items-center gap-5">
                            <div class="w-14 h-14 bg-gray-200 rounded-lg flex items-center justify-center text-gray-500 shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            </div>
                            <div>
                                <h4 class="text-base text-gray-500">{{ $product['name'] }}</h4>
                                <p class="text-sm text-gray-600 mt-1">{{ number_format($product['sold'], 0, ',', '.') }} terjual (30 hari)</p>
                            </div>
                        </div>
                        <span class="text-xl font-semibold text-gray-900">{{ $rp($product['revenue']) }}</span>
                    </div>
                @empty
                    <p class="py-10 text-center text-sm text-gray-400">Belum ada produk.</p>
                @endforelse
            </div>
        </div>

    </div>

    <!-- Baris 5: Pendapatan 6 Bulan, Aksi Cepat & Kupon Aktif -->
    <div class="grid grid-cols-1 gap-6">

        <!-- Pendapatan 6 Bulan Terakhir -->
        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
            <h3 class="font-bold text-gray-800 text-base mb-4">Pendapatan 6 Bulan Terakhir</h3>
            <div class="space-y-2">
                @foreach ($monthlyRevenue as $label => $amount)
                    <div class="flex justify-between items-center p-4 bg-gray-50 rounded-xl text-xs font-medium">
                        <span class="text-gray-600">{{ $label }}</span>
                        <span class="text-gray-900 font-bold text-sm">{{ $rp($amount) }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Aksi Cepat -->
        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-4">
            <h3 class="font-bold text-gray-800 text-base">Aksi Cepat</h3>
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs">

                <!-- Tambah Produk -->
                <a href="{{ $link('products.create') }}" class="p-4 bg-blue-50/50 border border-blue-100/60 rounded-2xl hover:bg-blue-100/50 transition flex items-center gap-4 block shadow-sm">
                    <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    </div>
                    <div>
                        <strong class="text-blue-950 block font-bold text-sm">Tambah Produk</strong>
                        <span class="text-blue-600 text-[11px] font-medium">Produk baru</span>
                    </div>
                </a>

                <!-- Tambah Kupon -->
                <a href="{{ $link('kupon.create') }}" class="p-4 bg-emerald-50/50 border border-emerald-100/60 rounded-2xl hover:bg-emerald-100/50 transition flex items-center gap-4 block shadow-sm">
                    <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" /></svg>
                    </div>
                    <div>
                        <strong class="text-emerald-950 block font-bold text-sm">Tambah Kupon</strong>
                        <span class="text-emerald-600 text-[11px] font-medium">Diskon baru</span>
                    </div>
                </a>

                <!-- Kelola Pesanan -->
                <a href="{{ $link('admin.orders.index') }}" class="p-4 bg-purple-50/50 border border-purple-100/60 rounded-2xl hover:bg-purple-100/50 transition flex items-center gap-4 block shadow-sm">
                    <div class="w-12 h-12 bg-purple-100 text-purple-600 rounded-xl flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                    </div>
                    <div>
                        <strong class="text-purple-950 block font-bold text-sm">Kelola Pesanan</strong>
                        <span class="text-purple-600 text-[11px] font-medium">{{ $orderStatus['pending'] }} pending</span>
                    </div>
                </a>

                <!-- Kelola User -->
                <a href="{{ $link('users.index') }}" class="p-4 bg-orange-50/50 border border-orange-100/60 rounded-2xl hover:bg-orange-100/50 transition flex items-center gap-4 block shadow-sm">
                    <div class="w-12 h-12 bg-orange-100 text-orange-600 rounded-xl flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    </div>
                    <div>
                        <strong class="text-orange-950 block font-bold text-sm">Kelola User</strong>
                        <span class="text-orange-600 text-[11px] font-medium">{{ $stats['totalCustomers'] }} pelanggan</span>
                    </div>
                </a>

            </div>
        </div>

        <!-- Kupon Aktif -->
        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-4">
            <div class="flex justify-between items-center">
                <h3 class="font-bold text-gray-800 text-base">Kupon Aktif</h3>
                <span class="bg-emerald-100 text-emerald-800 px-2.5 py-0.5 rounded-full text-[11px] font-semibold">{{ $activeCoupons }} aktif</span>
            </div>

            <div class="text-center py-8 space-y-3">
                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mx-auto">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" /></svg>
                </div>
                <h4 class="font-bold text-gray-800 text-sm">{{ $activeCoupons }} Kupon Aktif</h4>
                <p class="text-gray-400 text-xs">Kelola kupon untuk meningkatkan penjualan</p>
                <div>
                    <a href="{{ $link('kupon.index') }}" class="inline-block mt-2 px-4 py-2 bg-[#8C6239] hover:bg-[#724e2c] text-white text-xs font-semibold rounded-lg transition">
                        Kelola Kupon
                    </a>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection