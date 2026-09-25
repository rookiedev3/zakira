@extends('layouts.admin')

@section('title', 'Dashboard Admin — Zakira Moslem Hijab Identity')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-8 space-y-6">
    
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
                <h3 class="text-3xl font-bold text-gray-900 mt-2">6</h3>
            </div>
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center shadow-inner">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
            </div>
        </div>

        <!-- Total Pendapatan -->
        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex justify-between items-center">
            <div>
                <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Total Pendapatan</p>
                <h3 class="text-xl font-bold text-emerald-600 mt-2">Rp 1.590.000</h3>
            </div>
            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center shadow-inner">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
        </div>

        <!-- Total Pelanggan -->
        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex justify-between items-center">
            <div>
                <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Total Pelanggan</p>
                <h3 class="text-3xl font-bold text-purple-600 mt-2">4</h3>
            </div>
            <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center shadow-inner">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
            </div>
        </div>

        <!-- Produk Aktif -->
        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex justify-between items-center">
            <div>
                <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Produk Aktif</p>
                <h3 class="text-3xl font-bold text-amber-600 mt-2">2</h3>
            </div>
            <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center shadow-inner">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
            </div>
        </div>

    </div>

    <!-- Baris 2: Status Pesanan (Pending, Diproses, Selesai) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        
        <!-- Pesanan Pending -->
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex justify-between items-center">
            <div>
                <p class="text-gray-500 text-xs font-medium">Pesanan Pending</p>
                <h4 class="text-2xl font-bold text-amber-600 mt-1">5</h4>
            </div>
            <div class="w-10 h-10 bg-amber-50 text-amber-600 rounded-full flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
        </div>

        <!-- Pesanan Diproses -->
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex justify-between items-center">
            <div>
                <p class="text-gray-500 text-xs font-medium">Pesanan Diproses</p>
                <h4 class="text-2xl font-bold text-blue-600 mt-1">1</h4>
            </div>
            <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
            </div>
        </div>

        <!-- Pesanan Selesai -->
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex justify-between items-center">
            <div>
                <p class="text-gray-500 text-xs font-medium">Pesanan Selesai</p>
                <h4 class="text-2xl font-bold text-emerald-600 mt-1">0</h4>
            </div>
            <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            </div>
        </div>

    </div>

    <!-- Baris 3: Statistik Down Payment (DP) lengkap dengan Pendapatan Sisa -->
    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <h3 class="font-bold text-gray-800 text-base mb-6">Statistik Down Payment</h3>
        <div class="grid grid-cols-2 md:grid-cols-6 gap-6 text-center">
            <div class="border-r border-gray-100 last:border-none">
                <p class="text-blue-600 font-bold text-xl">4</p>
                <span class="text-gray-400 text-xs mt-1 block">Total Pesanan DP</span>
            </div>
            <div class="border-r border-gray-100 last:border-none">
                <p class="text-emerald-600 font-bold text-xl">3</p>
                <span class="text-gray-400 text-xs mt-1 block">DP Terbayar</span>
            </div>
            <div class="border-r border-gray-100 last:border-none">
                <p class="text-amber-600 font-bold text-xl">3</p>
                <span class="text-gray-400 text-xs mt-1 block">DP Pending</span>
            </div>
            <div class="border-r border-gray-100 last:border-none">
                <p class="text-purple-600 font-bold text-xl">2</p>
                <span class="text-gray-400 text-xs mt-1 block">Lunas Penuh</span>
            </div>
            <div class="border-r border-gray-100 last:border-none">
                <p class="text-blue-600 font-bold text-base">Rp 210.000</p>
                <span class="text-gray-400 text-xs mt-1 block">Pendapatan DP</span>
            </div>
            <div>
                <p class="text-emerald-600 font-bold text-base">Rp 420.000</p>
                <span class="text-gray-400 text-xs mt-1 block">Pendapatan Sisa</span>
            </div>
        </div>
    </div>

    <!-- Baris 4: Pesanan Terbaru & Produk Terpopuler -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Pesanan Terbaru (dilengkapi badge DP System / Full Payment)[cite: 10] -->
        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-4">
            <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                <h3 class="font-bold text-gray-800 text-base">Pesanan Terbaru</h3>
                <a href="#" class="text-xs text-amber-800 hover:underline font-semibold">Lihat Semua</a>
            </div>

            <div class="space-y-3">
                <!-- Item Pesanan 1 -->
                <div class="p-3 bg-gray-50 rounded-xl flex justify-between items-center text-xs">
                    <div>
                        <span class="font-bold text-gray-800">#ORD260924113552OSF</span>
                        <p class="text-gray-400 mt-0.5">Guest • 24 Sep 2026 11:35</p>
                    </div>
                    <div class="text-right space-y-1">
                        <div>
                            <span class="font-bold text-gray-900">Rp 100.000</span>
                            <span class="bg-amber-100 text-amber-800 px-2 py-0.5 rounded text-[10px] font-semibold inline-block ml-1">Pending</span>
                        </div>
                        <div>
                            <span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded text-[10px] font-semibold inline-block">DP System</span>
                        </div>
                    </div>
                </div>

                <!-- Item Pesanan 2 -->
                <div class="p-3 bg-gray-50 rounded-xl flex justify-between items-center text-xs">
                    <div>
                        <span class="font-bold text-gray-800">#ORD260922170018I42</span>
                        <p class="text-gray-400 mt-0.5">User Customer • 22 Sep 2026 17:00</p>
                    </div>
                    <div class="text-right space-y-1">
                        <div>
                            <span class="font-bold text-gray-900">Rp 500.000</span>
                            <span class="bg-amber-100 text-amber-800 px-2 py-0.5 rounded text-[10px] font-semibold inline-block ml-1">Pending</span>
                        </div>
                        <div>
                            <span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded text-[10px] font-semibold inline-block">DP System</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Produk Terpopuler -->
        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-4">
            <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                <h3 class="font-bold text-gray-800 text-base">Produk Terpopuler</h3>
                <a href="#" class="text-xs text-amber-800 hover:underline font-semibold">Lihat Semua</a>
            </div>

            <div class="space-y-3">
                <div class="p-3 bg-gray-50 rounded-xl flex justify-between items-center text-xs">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800">Baju Koko</h4>
                            <p class="text-gray-400 text-[11px]">0 terjual (30 hari)</p>
                        </div>
                    </div>
                    <span class="font-bold text-gray-800">Rp 0</span>
                </div>

                <div class="p-3 bg-gray-50 rounded-xl flex justify-between items-center text-xs">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800">Almet PNC1</h4>
                            <p class="text-gray-400 text-[11px]">0 terjual (30 hari)</p>
                        </div>
                    </div>
                    <span class="font-bold text-gray-800">Rp 0</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Baris 5: Pendapatan 6 Bulan Terakhir & Aksi Cepat -->
    <div class="grid grid-cols-1 gap-6">
        
        <!-- Pendapatan 6 Bulan Terakhir -->
        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
            <h3 class="font-bold text-gray-800 text-base mb-4">Pendapatan 6 Bulan Terakhir</h3>
            <div class="flex justify-between items-center p-4 bg-gray-50 rounded-xl text-xs font-medium">
                <span class="text-gray-600">Sep 2026</span>
                <span class="text-gray-900 font-bold text-sm">Rp 1.590.000</span>
            </div>
        </div>

      <!-- Aksi Cepat (Desain warna-warni serasi sesuai referensi) -->
<div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-4">
    <h3 class="font-bold text-gray-800 text-base">Aksi Cepat</h3>
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs">
        
        <!-- Tambah Produk (Tema Biru) -->
        <a href="#" class="p-4 bg-blue-50/50 border border-blue-100/60 rounded-2xl hover:bg-blue-100/50 transition flex items-center gap-4 block shadow-sm">
            <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            </div>
            <div>
                <strong class="text-blue-950 block font-bold text-sm">Tambah Produk</strong>
                <span class="text-blue-600 text-[11px] font-medium">Produk baru</span>
            </div>
        </a>

        <!-- Tambah Kupon (Tema Hijau) -->
        <a href="#" class="p-4 bg-emerald-50/50 border border-emerald-100/60 rounded-2xl hover:bg-emerald-100/50 transition flex items-center gap-4 block shadow-sm">
            <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" /></svg>
            </div>
            <div>
                <strong class="text-emerald-950 block font-bold text-sm">Tambah Kupon</strong>
                <span class="text-emerald-600 text-[11px] font-medium">Diskon baru</span>
            </div>
        </a>

        <!-- Kelola Pesanan (Tema Ungu) -->
        <a href="#" class="p-4 bg-purple-50/50 border border-purple-100/60 rounded-2xl hover:bg-purple-100/50 transition flex items-center gap-4 block shadow-sm">
            <div class="w-12 h-12 bg-purple-100 text-purple-600 rounded-xl flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
            </div>
            <div>
                <strong class="text-purple-950 block font-bold text-sm">Kelola Pesanan</strong>
                <span class="text-purple-600 text-[11px] font-medium">5 pending</span>
            </div>
        </a>

        <!-- Kelola User (Tema Oranye/Jingga) -->
        <a href="#" class="p-4 bg-orange-50/50 border border-orange-100/60 rounded-2xl hover:bg-orange-100/50 transition flex items-center gap-4 block shadow-sm">
            <div class="w-12 h-12 bg-orange-100 text-orange-600 rounded-xl flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
            </div>
            <div>
                <strong class="text-orange-950 block font-bold text-sm">Kelola User</strong>
                <span class="text-orange-600 text-[11px] font-medium">4 pelanggan</span>
            </div>
        </a>

    </div>
</div>
        <!-- Kupon Aktif -->
        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-4">
            <div class="flex justify-between items-center">
                <h3 class="font-bold text-gray-800 text-base">Kupon Aktif</h3>
                <span class="bg-emerald-100 text-emerald-800 px-2.5 py-0.5 rounded-full text-[11px] font-semibold">0 aktif</span>
            </div>
            
            <div class="text-center py-8 space-y-3">
                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mx-auto">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" /></svg>
                </div>
                <h4 class="font-bold text-gray-800 text-sm">0 Kupon Aktif</h4>
                <p class="text-gray-400 text-xs">Kelola kupon untuk meningkatkan penjualan</p>
                <div>
                    <a href="#" class="inline-block mt-2 px-4 py-2 bg-[#8C6239] hover:bg-[#724e2c] text-white text-xs font-semibold rounded-lg transition">
                        Kelola Kupon
                    </a>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection