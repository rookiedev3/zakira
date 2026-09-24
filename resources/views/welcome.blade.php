<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zakira — Moslem Hijab Identity</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-color: #FDFBF7;
            color: #2D2522;
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="antialiased">

    <!-- Topbar Informasi -->
    <div class="bg-[#2D2522] text-white text-xs py-2 px-6 flex justify-between items-center">
        <div class="flex items-center space-x-4">
            <span>Moslem Hijab Identity</span>
            <a href="#" class="hover:underline">TikTok</a>
            <a href="#" class="hover:underline">Facebook</a>
        </div>
        <div>
            <a href="/login" class="hover:underline font-medium">Login Member</a>
        </div>
    </div>

    <!-- Navbar Utama -->
    <header class="sticky top-0 z-50 bg-[#FDFBF7]/90 backdrop-blur-md border-b border-[#EFECE6]">
        <div class="max-w-7xl mx-auto px-6 h-20 flex justify-between items-center">
            <!-- Logo -->
            <div class="flex flex-col">
                <span class="text-2xl font-bold tracking-wider font-serif">Zakira</span>
                <span class="text-[10px] tracking-widest uppercase text-gray-500">moslem hijab identity</span>
            </div>

            <!-- Navigasi (Smooth Scroll Links) -->
            <nav class="hidden md:flex space-x-8 text-sm font-medium">
                <a href="#beranda" class="hover:text-amber-800 transition">Beranda</a>
                <a href="#brand" class="hover:text-amber-800 transition">Brand</a>
                <a href="#ready-stock" class="hover:text-amber-800 transition">Ready Stock</a>
                <a href="#tentang-kami" class="hover:text-amber-800 transition">Tentang Kami</a>
                <a href="#kontak" class="hover:text-amber-800 transition">Kontak</a>
            </nav>

            <!-- Aksi Kanan (Akun & Keranjang) -->
            <div class="flex items-center space-x-4">
                <a href="/login" class="p-2 border border-gray-300 rounded-full hover:bg-gray-100 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </a>

                <a href="/cart" class="p-2 border border-gray-300 rounded-full hover:bg-gray-100 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </a>
            </div>
        </div>
    </header>

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
                <a href="#ready-stock" class="bg-[#3D2C24] text-white px-6 py-3 rounded-md text-sm font-medium hover:bg-[#2D2522] transition shadow-md">
                    Belanja Sekarang
                </a>
                <a href="#ready-stock" class="border border-[#3D2C24] text-[#3D2C24] px-6 py-3 rounded-md text-sm font-medium hover:bg-[#3D2C24] hover:text-white transition">
                    Lihat Katalog
                </a>
            </div>
        </div>
        <div class="flex justify-center">
            <!-- Tempat Foto Utama Hero -->
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

        <!-- Box Keunggulan (Bahan Premium, Syar'i, dll) -->
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
    <section id="ready-stock" class="bg-[#FAF8F5] py-20 border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center max-w-xl mx-auto mb-12">
                <span class="text-xs uppercase tracking-widest text-gray-500 font-semibold">PILIHAN TERBAIK</span>
                <h2 class="text-3xl font-serif font-medium mt-2 mb-2">Koleksi Pilihan</h2>
                <p class="text-gray-600 text-sm">Produk Zakira dengan bahan premium dan detail yang dirancang untuk kenyamanan muslimah.</p>
            </div>

            <!-- Grid Produk -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 mb-12">
                <!-- Contoh Produk 1 -->
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
                <a href="#" class="border border-[#3D2C24] text-[#3D2C24] px-8 py-3 rounded-md text-sm font-medium hover:bg-[#3D2C24] hover:text-white transition">
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

    <!-- Footer -->
    <footer id="kontak" class="bg-[#2D2522] text-gray-300 pt-16 pb-8 border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
            <div>
                <span class="text-2xl font-bold tracking-wider font-serif text-white">Zakira</span>
                <p class="text-xs text-gray-400 mt-2">Busana muslimah syar'i dengan pilihan bahan premium, nyaman, anggun, dan elegan untuk keseharian.</p>
            </div>
            <div>
                <h4 class="text-white font-semibold text-sm mb-4">Alamat Toko</h4>
                <p class="text-xs text-gray-400">Lokasi Zakira tersedia melalui Google Maps.</p>
            </div>
            <div>
                <h4 class="text-white font-semibold text-sm mb-4">Hubungi Kami</h4>
                <p class="text-xs text-gray-400">WhatsApp Ahmad Dahlan: 087830614484</p>
            </div>
            <div>
                <h4 class="text-white font-semibold text-sm mb-4">Informasi</h4>
                <ul class="text-xs space-y-2 text-gray-400">
                    <li><a href="#ready-stock" class="hover:text-white">Ready Stock</a></li>
                    <li><a href="/login" class="hover:text-white">Login Member</a></li>
                    <li><a href="/cart" class="hover:text-white">Keranjang</a></li>
                </ul>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-6 pt-6 border-t border-gray-800 text-center text-xs text-gray-500">
            &copy; 2026 Zakira — Moslem Hijab Identity. Powered by IT Solution Yogyakarta.
        </div>
    </footer>

</body>
</html>