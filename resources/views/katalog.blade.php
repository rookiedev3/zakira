@extends('layouts.app')

@section('title', 'Katalog Produk — Zakira Moslem Hijab Identity')

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
            
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-500 mb-2">Cari produk</label>
                <input type="text" placeholder="Cari Shafa, Jenna, Lur..." class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-amber-800">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-gray-500 mb-2">Brand</label>
                <select class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-1 focus:ring-amber-800">
                    <option value="">Semua Brand</option>
                    <option value="zakira">Zakira</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-gray-500 mb-2">Jenis Produk</label>
                <select class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-1 focus:ring-amber-800">
                    <option value="ready">Ready Stock</option>
                    <option value="po">Pre Order (PO)</option>
                </select>
            </div>
        </aside>

        <!-- AREA PRODUK & SORTING -->
        <section class="lg:col-span-3 space-y-6">
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

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-xl overflow-hidden shadow-sm border border-gray-200 hover:shadow-md transition">
                    <div class="h-64 bg-gray-100 flex items-center justify-center text-gray-400 text-xs relative">
                        <span class="absolute top-3 left-3 text-[10px] bg-green-700 text-white px-2 py-1 rounded font-semibold">READY STOCK</span>
<img src="{{ asset('images/hijab.jpeg') }}" alt="Nama Produk Hijab" class="w-full h-full object-cover">                    </div>
                    <div class="p-4">
                        <span class="text-[10px] uppercase text-gray-400 tracking-wider">ZAKIRA</span>
                        <h4 class="font-medium text-sm mt-1 text-gray-800">Baju Koko / Hijab</h4>
                        <p class="text-amber-900 font-bold text-sm mt-2">Rp 100.000 - Rp 300.000</p>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection