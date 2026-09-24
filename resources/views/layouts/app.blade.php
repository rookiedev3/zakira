<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Zakira — Moslem Hijab Identity')</title>
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
<body class="antialiased flex flex-col min-h-screen">

    <!-- Topbar Informasi (Satu tempat untuk semua halaman) -->
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

    <!-- Navbar Utama (Satu tempat untuk semua halaman) -->
    <header class="sticky top-0 z-50 bg-[#FDFBF7]/90 backdrop-blur-md border-b border-[#EFECE6]">
        <div class="max-w-7xl mx-auto px-6 h-20 flex justify-between items-center">
            <!-- Logo -->
            <a href="/" class="flex flex-col">
                <span class="text-2xl font-bold tracking-wider font-serif">Zakira</span>
                <span class="text-[10px] tracking-widest uppercase text-gray-500">moslem hijab identity</span>
            </a>

            <!-- Navigasi -->
            <nav class="hidden md:flex space-x-8 text-sm font-medium">
                <a href="/" class="hover:text-amber-800 transition">Beranda</a>
                <a href="/#brand" class="hover:text-amber-800 transition">Brand</a>
                <a href="/katalog" class="hover:text-amber-800 transition">Ready Stock</a>
                <a href="/#tentang-kami" class="hover:text-amber-800 transition">Tentang Kami</a>
                <a href="/#kontak" class="hover:text-amber-800 transition">Kontak</a>
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

    <!-- Konten Utama yang Dinamis (Berubah sesuai halaman) -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer (Satu tempat untuk semua halaman) -->
    <footer class="bg-[#2D2522] text-gray-300 pt-16 pb-8 border-t border-gray-800 mt-auto">
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
                    <li><a href="/katalog" class="hover:text-white">Ready Stock</a></li>
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