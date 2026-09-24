@extends('layouts.app')

@section('title', 'Akun Saya — Zakira Moslem Hijab Identity')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-10 w-full flex-grow">
    
    <!-- Judul Halaman -->
    <div class="mb-8">
        <h1 class="text-3xl font-serif font-medium text-gray-900">Akun Saya</h1>
        <p class="text-gray-500 text-sm mt-1">Kelola profil dan riwayat pesanan Anda</p>
    </div>

    <!-- Layout Grid Utama (Kiri: Profil, Kanan: Riwayat Pesanan) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- KOLOM KIRI: PROFIL SAYA & STATISTIK -->
        <div class="lg:col-span-1 space-y-6">
            
            <!-- Card Informasi Profil -->
            <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-bold text-base text-gray-800">Profil Saya</h3>
                    <div class="space-x-2 text-xs">
                        <span class="text-amber-800 font-semibold cursor-pointer">Profil</span>
                        <span class="text-gray-300">|</span>
                        <span class="text-gray-400 cursor-pointer hover:text-gray-600">Password</span>
                    </div>
                </div>

                <!-- Avatar Inisial -->
                <div class="flex flex-col items-center text-center pb-6 border-b border-gray-100">
                    <div class="w-20 h-20 bg-gray-200 text-gray-600 rounded-full flex items-center justify-center font-bold text-xl mb-3 shadow-inner">
                        {{ strtoupper(substr(Auth::user()->name ?? 'UC', 0, 2)) }}
                    </div>
                    <h4 class="font-semibold text-gray-800 text-base">{{ Auth::user()->name ?? 'User Customer' }}</h4>
                    <p class="text-gray-400 text-xs mt-0.5">{{ Auth::user()->email ?? 'customer@gmail.com' }}</p>
                </div>

                <!-- Detail Data Diri -->
                <div class="py-4 space-y-3 text-xs">
                    <div>
                        <span class="text-gray-400 block">Nama:</span>
                        <span class="font-medium text-gray-800">{{ Auth::user()->name ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block">Email:</span>
                        <span class="font-medium text-gray-800">{{ Auth::user()->email ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block">Telepon:</span>
                        <span class="font-medium text-gray-800">{{ Auth::user()->phone ?? '085842199807' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block">Alamat:</span>
                        <span class="font-medium text-gray-800">{{ Auth::user()->address ?? 'Sleman' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block">Kota:</span>
                        <span class="font-medium text-gray-800">{{ Auth::user()->city ?? 'Sleman' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block">Provinsi:</span>
                        <span class="font-medium text-gray-800">{{ Auth::user()->province ?? 'DI Yogyakarta' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block">Kode Pos:</span>
                        <span class="font-medium text-gray-800">{{ Auth::user()->postal_code ?? '55556' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block">ID Seller:</span>
                        <span class="font-medium text-gray-800">{{ Auth::user()->seller_id ?? 'asdk123' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block">Ekspedisi Favorit:</span>
                        <span class="font-medium text-gray-800">{{ Auth::user()->shipping_expedition ?? 'JNE' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block">Bergabung:</span>
                        <span class="font-medium text-gray-800">{{ Auth::user()->created_at ? Auth::user()->created_at->format('d M Y') : '17 Jul 2025' }}</span>
                    </div>
                </div>

                <!-- Tombol Logout -->
                <div class="pt-4 border-t border-gray-100 mt-2">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white text-xs font-semibold py-2.5 rounded-lg transition flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </div>

            <!-- Card Statistik Pesanan -->
            <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm text-xs space-y-3">
                <h3 class="font-bold text-base text-gray-800 mb-2">Statistik Pesanan</h3>
                <div class="flex justify-between py-1 border-b border-gray-50">
                    <span class="text-gray-500">Total Pesanan:</span>
                    <span class="font-semibold text-gray-800">1</span>
                </div>
                <div class="flex justify-between py-1 border-b border-gray-50">
                    <span class="text-gray-500">Pesanan Aktif:</span>
                    <span class="font-semibold text-gray-800">1</span>
                </div>
                <div class="flex justify-between py-1 border-b border-gray-50">
                    <span class="text-gray-500">Pesanan Selesai:</span>
                    <span class="font-semibold text-gray-800">0</span>
                </div>
                <div class="flex justify-between pt-2">
                    <span class="text-gray-500 font-medium">Total Belanja:</span>
                    <span class="font-bold text-emerald-700 text-sm">Rp 500.000</span>
                </div>
            </div>

        </div>

        <!-- KOLOM KANAN: RIWAYAT PESANAN -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                <h3 class="font-bold text-base text-gray-800 pb-4 border-b border-gray-100 mb-6">Riwayat Pesanan</h3>

                <!-- Contoh Item Kartu Pesanan -->
                <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm space-y-4">
                    
                    <!-- Header Pesanan -->
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 pb-3 border-b border-gray-100">
                        <div>
                            <span class="font-bold text-gray-800 text-sm">Order #ORD260922170018I42</span>
                        </div>
                        <span class="bg-amber-100 text-amber-800 text-[11px] font-semibold px-3 py-1 rounded-full">Menunggu</span>
                    </div>

                    <!-- Detail Info Ringkas -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                        <div>
                            <span class="text-gray-400 block">Tanggal:</span>
                            <span class="font-medium text-gray-800">22 Sep 2026</span>
                        </div>
                        <div>
                            <span class="text-gray-400 block">Items:</span>
                            <span class="font-medium text-gray-800">2 item(s)</span>
                        </div>
                        <div>
                            <span class="text-gray-400 block">Total:</span>
                            <span class="font-bold text-gray-900">Rp 500.000</span>
                        </div>
                        <div>
                            <span class="text-gray-400 block">Pembayaran:</span>
                            <span class="bg-emerald-100 text-emerald-800 text-[10px] font-semibold px-2 py-0.5 rounded inline-block mt-0.5">Lunas</span>
                        </div>
                    </div>

                    <!-- Metode Bayar & Status DP -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-xs bg-gray-50 p-3 rounded-lg">
                        <div>
                            <span class="text-gray-400 block">Metode Bayar:</span>
                            <span class="font-medium text-gray-700 bg-blue-100 text-blue-800 px-2 py-0.5 rounded text-[10px] inline-block mt-1">Down Payment</span>
                        </div>
                        <div>
                            <span class="text-gray-400 block">DP Status:</span>
                            <span class="font-medium text-emerald-700 flex items-center gap-1 mt-1">✓ DP Lunas</span>
                        </div>
                        <div>
                            <span class="text-gray-400 block">Sisa Bayar:</span>
                            <span class="font-medium text-emerald-700 flex items-center gap-1 mt-1">✓ Lunas</span>
                        </div>
                    </div>

                    <!-- Bagian Faktur & Tombol Aksi -->
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pt-2 gap-3">
                        <div class="text-xs">
                            <span class="text-gray-400 block mb-0.5">Faktur:</span>
                            <a href="#" class="text-blue-600 hover:underline bg-blue-50 px-2 py-1 rounded border border-blue-100 inline-flex items-center gap-1">
                                📄 FKT-20260924-0001 (PDF)
                            </a>
                            <span class="text-[10px] text-gray-400 block mt-1">24 Sep 2026 10:18</span>
                        </div>

                        <div class="flex items-center gap-2">
                            <button class="bg-[#8C6239] hover:bg-[#724e2c] text-white text-xs font-medium px-4 py-2 rounded-lg transition flex items-center gap-1.5 shadow-sm">
                                🔍 Lihat Detail
                            </button>
                            <button class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs font-medium px-4 py-2 rounded-lg transition flex items-center gap-1.5">
                                📥 Download PDF
                            </button>
                        </div>
                    </div>

                </div>

            </div>
        </div>

    </div>
</div>
@endsection