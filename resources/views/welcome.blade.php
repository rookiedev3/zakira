@extends('layouts.app')

@section('title', 'Zakira — Moslem Hijab Identity')

@section('content')
<html class="scroll-smooth">
    <!-- Section Beranda / Hero -->
    <section id="beranda" class="max-w-7xl mx-auto px-6 py-16 grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
        <div>
            <span class="text-xs uppercase tracking-widest text-gray-500 font-semibold">ZAKIRA — MOSLEM HIJAB IDENTITY</span>
            <h1 class="text-4xl md:text-5xl font-serif font-medium leading-tight mt-3 mb-6">
                Elegan, Syar'i, Nyaman Setiap Hari
            </h1>
            <p class="text-gray-600 mb-8 leading-relaxed">
                Koleksi pakaian muslimah berkualitas dengan bahan premium pilihan untuk penampilan anggun, nyaman, dan bernilai ibadah.
            </p>
            <div class="flex space-x-4">
                <a href="/katalog" class="bg-[#3D2C24] text-white px-6 py-3 rounded-md text-sm font-medium hover:bg-[#2D2522] transition shadow-md">
                    Belanja Sekarang
                </a>
                <a href="/katalog" class="border border-[#3D2C24] text-[#3D2C24] px-6 py-3 rounded-md text-sm font-medium hover:bg-[#3D2C24] hover:text-white transition">
                    Lihat Katalog
                </a>
            </div>
        </div>
        <div class="flex justify-center">
            <div class="w-full h-[400px] bg-gray-200 rounded-t-[150px] overflow-hidden shadow-inner flex items-center justify-center text-gray-400">
                <img src="{{ asset('images/hijab.jpeg') }}" alt="Hero Model" class="w-full h-full object-cover">
            </div>
        </div>
    </section>

    <!-- Section Wadah 1 Foto di atas Tulisan Keunggulan -->
    <section class="max-w-7xl mx-auto px-6 py-6">
        <div class="w-full h-[350px] md:h-[450px] rounded-2xl overflow-hidden shadow-md bg-gray-200 mb-8">
             <img src="{{ asset('images/hijab.jpeg') }}" alt="Banner Keunggulan" class="w-full h-full object-cover">
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
            <div class="p-2">
                <h3 class="font-bold text-sm mb-1">Bahan Premium</h3>
                <p class="text-xs text-gray-500">Kualitas bahan pilihan</p>
            </div>
            <div class="p-2">
                <h3 class="font-bold text-sm mb-1">Syar'i & Nyaman</h3>
                <p class="text-xs text-gray-500">Menutup aurat dengan anggun</p>
            </div>
            <div class="p-2">
                <h3 class="font-bold text-sm mb-1">Desain Exclusive</h3>
                <p class="text-xs text-gray-500">Model terbaru dan elegan</p>
            </div>
            <div class="p-2">
                <h3 class="font-bold text-sm mb-1">Amanah & Terpercaya</h3>
                <p class="text-xs text-gray-500">Pelayanan terbaik</p>
            </div>
        </div>
    </section>

    <!-- Section Brand -->
    <section id="brand" class="max-w-7xl mx-auto px-6 py-20">
        <span class="text-xs uppercase tracking-widest text-gray-500 font-semibold">BRAND PILIHAN</span>
        <h2 class="text-3xl font-serif font-medium mt-2 mb-2">Temukan Koleksi Berdasarkan Brand</h2>
        <p class="text-gray-600 text-sm mb-10">Pilih brand untuk melihat koleksi Ready Stock yang tersedia.</p>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
            <div class="border border-gray-200 rounded-2xl p-6 bg-white shadow-sm hover:shadow-md transition text-center">
                <div class="h-32 bg-gray-100 rounded-xl mb-4 overflow-hidden flex items-center justify-center text-gray-400 text-xs">
                     <img src="{{ asset('images/hijab.jpeg') }}" alt="Brand Zakira" class="w-full h-full object-cover">
                </div>
                <h4 class="font-bold text-sm">ZAKIRA</h4>
            </div>
        </div>
    </section>

    <!-- Section Foto Lebar di atas Tulisan "Koleksi Pilihan" -->
    <section class="max-w-7xl mx-auto px-6 py-6">
        <div class="w-full h-[300px] md:h-[400px] rounded-2xl overflow-hidden shadow-md bg-gray-200">
             <img src="{{ asset('images/hijab.jpeg') }}" alt="Banner Koleksi Pilihan" class="w-full h-full object-cover">
        </div>
    </section>

    <!-- Section Ready Stock / Katalog Pilihan -->
    <section class="bg-[#FAF8F5] py-20 border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center max-w-xl mx-auto mb-12">
                <span class="text-xs uppercase tracking-widest text-gray-500 font-semibold">PILIHAN TERBAIK</span>
                <h2 class="text-3xl font-serif font-medium mt-2 mb-2">Koleksi Pilihan</h2>
                <p class="text-gray-600 text-sm">Produk Zakira dengan bahan premium dan detail yang dirancang untuk kenyamanan muslimah.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 mb-12">
                <div class="bg-white rounded-xl overflow-hidden shadow-sm border border-gray-200">
                    <div class="h-64 bg-gray-200 flex items-center justify-center text-gray-400 text-xs">
                         <img src="{{ asset('images/hijab.jpeg') }}" alt="Produk Zakira" class="w-full h-full object-cover">
                    </div>
                    <div class="p-4">
                        <span class="text-[10px] bg-green-100 text-green-800 px-2 py-1 rounded font-semibold">READY STOCK</span>
                        <h4 class="font-medium text-sm mt-2">Baju Koko / Hijab Style</h4>
                        <p class="text-amber-800 font-bold text-sm mt-1">Rp 100.000 - Rp 300.000</p>
                    </div>
                </div>
            </div>

            <div class="text-center">
                <a href="/katalog" class="border border-[#3D2C24] text-[#3D2C24] px-8 py-3 rounded-md text-sm font-medium hover:bg-[#3D2C24] hover:text-white transition">
                    Lihat Semua Produk
                </a>
            </div>
        </div>
    </section>

    <!-- Section Tentang Kami -->
    <section id="tentang-kami" class="max-w-7xl mx-auto px-6 py-20">
        <div class="bg-[#3D2C24] text-white rounded-2xl p-8 md:p-12 flex flex-col md:flex-row justify-between items-center">
            <div class="max-w-xl mb-6 md:mb-0">
                <span class="text-xs uppercase tracking-widest text-amber-300 font-semibold">MEMBER ZAKIRA</span>
                <h2 class="text-3xl font-serif font-medium mt-2 mb-4">Produk khusus sesuai akses akun</h2>
                <p class="text-gray-300 text-sm leading-relaxed">
                    Produk Ready Stock dapat dibeli tanpa login. Produk PO hanya dapat dilihat dan dibeli oleh Member Zakira yang sudah login.
                </p>
            </div>
            <div>
                <a href="/login" class="bg-white text-[#3D2C24] px-6 py-3 rounded-md text-sm font-semibold hover:bg-gray-100 transition shadow">
                    Login Member
                </a>
            </div>
        </div>
    </section>
</html>
@endsection