<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Zakira — Moslem Hijab Identity')</title>
    <!-- Tailwind CSS CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
            @foreach ($socialMedia as $sm)
                <a href="{{ $sm->url }}" target="_blank" rel="noopener" class="flex items-center gap-2 hover:underline">
                    <span class="w-6 h-6 rounded-full flex items-center justify-center text-white text-[10px]"
                        style="background-color: {{ $sm->color ?? '#333' }}">
                        <i class="{{ $sm->icon_class }}"></i>
                    </span>
                    {{ $sm->platform }}
                </a>
            @endforeach
        </div>
        <div>
            @auth
                <span class="font-medium">Halo, {{ Auth::user()->name }}</span>
            @else
                <a href="/login" class="hover:underline font-medium">Login Member</a>
            @endauth
        </div>
    </div>

    <!-- Navbar Utama (Satu tempat untuk semua halaman) -->
    <header class="sticky top-0 z-50 bg-[#FDFBF7]/90 backdrop-blur-md border-b border-[#EFECE6]">
        <div class="max-w-7xl mx-auto px-6 h-20 flex justify-between items-center">
            <!-- Logo -->
            <a href="/" class="flex flex-col">
                <img src="{{ asset('images/logo-zakira.png') }}" alt="Zakira Logo" class="h-16 mx-auto object-contain">
            </a>

            <!-- Navigasi -->
            <nav class="hidden md:flex space-x-8 text-sm font-medium">
                <a href="/" class="hover:text-amber-800 transition">Beranda</a>
                <a href="/#brand" class="hover:text-amber-800 transition">Brand</a>
                <a href="/katalog" class="hover:text-amber-800 transition">Ready Stock</a>

                <!-- MENU PRE ORDER (PO) - Hanya muncul untuk Admin atau Customer berstatus 'member' -->
                @auth
                    @if(Auth::user()->role !== 'customer' || (Auth::user()->role === 'customer' && Auth::user()->customer_type === 'member'))
                        <a href="/member/pre-order" class="text-amber-800 font-semibold transition">PO (Pre Order)</a>
                    @endif
                @endauth

                <a href="/#tentang-kami" class="hover:text-amber-800 transition">Tentang Kami</a>
                <a href="/#kontak" class="hover:text-amber-800 transition">Kontak</a>
            </nav>

            <!-- Aksi Kanan (Akun & Keranjang) -->
            <div class="flex items-center space-x-4">
                <!-- Tombol Profil (Dinamis: Jika login ke profil, jika belum ke login) -->
                @auth
                    <a href="{{ route('member.profile') }}" class="p-2 border border-gray-300 rounded-full hover:bg-gray-100 transition" title="Akun Saya">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="p-2 border border-gray-300 rounded-full hover:bg-gray-100 transition" title="Login Member">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </a>
                @endauth

                <!-- Tombol Keranjang -->
                <a href="/cart" class="p-2 border border-gray-300 rounded-full hover:bg-gray-100 transition" title="Keranjang">
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
            <img src="{{ asset('images/logo-zakira2.png') }}" alt="Zakira Logo" class="h-16 object-contain">
            <p class="text-xs text-gray-400 mt-2">Busana muslimah syar'i dengan pilihan bahan premium, nyaman, anggun, dan elegan untuk keseharian.</p>

            <div class="flex gap-3 mt-4">
                @foreach ($socialMedia as $sm)
                    <a href="{{ $sm->url }}" target="_blank" rel="noopener"
                    class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm hover:opacity-80 transition"
                    style="background-color: {{ $sm->color ?? '#333' }}">
                        <i class="{{ $sm->icon_class }}"></i>
                    </a>
                @endforeach
            </div>
        </div>
            <div>
                <h4 class="text-white font-semibold text-sm mb-4">Alamat Toko</h4>
                <p class="text-xs text-gray-400">Lokasi Zakira tersedia melalui Google Maps.</p>
            </div>
            <div>
                <h4 class="text-white font-semibold text-sm mb-4">Hubungi Kami</h4>
                <div class="space-y-2">
                    @forelse ($footerContacts as $cs)
                        <a href="{{ $cs->whatsapp_url }}" target="_blank" rel="noopener"
                        class="flex items-center gap-2 text-xs text-gray-400 hover:text-white transition">
                            <span class="w-5 h-5 rounded-full bg-[#3D2C24] flex items-center justify-center text-white text-[10px]">
                                <i class="fab fa-whatsapp"></i>
                            </span>
                            {{ $cs->name }}: {{ $cs->phone_number }}
                        </a>
                    @empty
                        <p class="text-xs text-gray-400">Belum ada kontak.</p>
                    @endforelse
                </div>
            </div>
            <div>
                <h4 class="text-white font-semibold text-sm mb-4">Informasi</h4>
                <ul class="text-xs space-y-2 text-gray-400">
                    <li><a href="/katalog" class="hover:text-white">Ready Stock</a></li>
                    @auth
                        @if(Auth::user()->role !== 'customer' || (Auth::user()->role === 'customer' && Auth::user()->customer_type === 'member'))
                            <li><a href="/member/pre-order" class="hover:text-white">Pre Order (PO)</a></li>
                        @endif
                    @endauth
                    <li><a href="/login" class="hover:text-white">Login Member</a></li>
                    <li><a href="/cart" class="hover:text-white">Keranjang</a></li>
                </ul>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-6 pt-6 border-t border-gray-800 text-center text-xs text-gray-500">
            &copy; 2026 Zakira — Moslem Hijab Identity. Powered by IT Solution Yogyakarta.
        </div>
    </footer>
    @if ($floatingWhatsapp)
    <a href="{{ $floatingWhatsapp->whatsapp_url }}" target="_blank" rel="noopener"
       class="fixed bottom-6 right-6 z-50 w-14 h-14 bg-green-500 rounded-full flex items-center justify-center shadow-lg hover:bg-green-600 transition"
       title="Chat {{ $floatingWhatsapp->name }}">
        <i class="fab fa-whatsapp text-white text-2xl"></i>
    </a>
@endif
</body>
</html>