@extends('layouts.app')

@section('title', 'Katalog Pre Order — Zakira Moslem Hijab Identity')

@section('content')
    <!-- Header Judul Katalog -->
    <section class="max-w-7xl mx-auto px-6 py-12 w-full">
        <span class="text-xs uppercase tracking-widest text-gray-500 font-semibold">KOLEKSI ZAKIRA</span>
        <h1 class="text-4xl font-serif font-medium mt-2 mb-3">Katalog Produk</h1>
        <p class="text-gray-600 text-sm">Ready Stock untuk semua pembeli. Produk PO khusus member Zakira yang sudah login.</p>
    </section>

    <!-- Konten Utama: Sidebar Filter & Grid Produk -->
    <main class="max-w-7xl mx-auto px-6 pb-24 grid grid-cols-1 lg:grid-cols-4 gap-8 w-full flex-grow">
        
        <!-- SIDEBAR FILTER -->
        <aside class="lg:col-span-1 space-y-6 bg-white p-6 rounded-2xl border border-gray-200 shadow-sm h-fit">
            <h3 class="font-bold text-base border-b border-gray-100 pb-3">Filter Produk</h3>
            
            <!-- Cari Produk -->
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-500 mb-2">Cari produk</label>
                <input type="text" placeholder="Cari Shafa, Jenna, Lur..." class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-amber-800">
            </div>

            <!-- Filter Brand -->
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-500 mb-2">Brand</label>
                <select class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-1 focus:ring-amber-800">
                    <option value="">Semua Brand</option>
                    <option value="zakira">Zakira</option>
                </select>
            </div>

            <!-- Filter Jenis Produk (Default terpilih PO / Pre Order) -->
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-500 mb-2">Jenis Produk</label>
                <select class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-1 focus:ring-amber-800">
                    <option value="po" selected>PO / Pre Order - Member</option>
                    <option value="ready">Ready Stock</option>
                </select>
            </div>

            <!-- Kotak Informasi Khusus Member -->
            <div class="bg-amber-50/60 border border-amber-200/60 rounded-xl p-4 flex gap-3 items-start">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-800 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <div>
                    <h4 class="text-xs font-bold text-amber-900">Khusus Member</h4>
                    <p class="text-[11px] text-amber-800/80 mt-0.5 leading-relaxed">Produk PO hanya dapat dilihat dan dibeli member yang sudah login.</p>
                </div>
            </div>
        </aside>

        <!-- AREA PRODUK & SORTING -->
        <section class="lg:col-span-3 space-y-6">
            
            <!-- Bar Info Jumlah Produk & Dropdown Urutan (Terbaru, Termahal, Termurah) -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white p-4 rounded-xl border border-gray-200 shadow-sm gap-4">
                <span class="text-xs text-gray-600 font-medium">1 produk ditemukan</span>
                
                <div class="w-full sm:w-auto">
                    <select class="w-full sm:w-48 px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-1 focus:ring-amber-800">
                        <option value="terbaru">Terbaru</option>
                        <option value="termahal">Termahal</option>
                        <option value="termurah">Termurah</option>
                    </select>
                </div>
            </div>

            <!-- Grid Daftar Produk PO -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                <!-- Item Produk PO -->
                <div class="bg-white rounded-xl overflow-hidden shadow-sm border border-gray-200 hover:shadow-md transition">
                    <div class="h-64 bg-gray-100 flex items-center justify-center text-gray-400 text-xs relative overflow-hidden">
                        <!-- Label PO berwarna cokelat khas sesuai gambar -->
                        <span class="absolute top-3 left-3 z-10 text-[10px] bg-[#9c6b3a] text-white px-2.5 py-1 rounded-md font-semibold tracking-wide">PO / PRE ORDER</span>
                        
                        <img src="{{ asset('images/hijab.jpeg') }}" alt="Foto Produk PO" class="w-full h-full object-cover">
                    </div>
                    <div class="p-4">
                        <span class="text-[10px] uppercase text-gray-400 tracking-wider">PNC WEAR</span>
                        <h4 class="font-medium text-sm mt-1 text-gray-800">Almet PNC1</h4>
                        <p class="text-amber-900 font-bold text-sm mt-2">Rp 10.000 - Rp 20.000</p>
                    </div>
                </div>
            </div>

        </section>
    </main>
@endsection