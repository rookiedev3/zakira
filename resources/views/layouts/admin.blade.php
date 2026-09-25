<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel — Zakira Moslem Hijab Identity')</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body {
            background-color: #F8F9FA;
            color: #2D2522;
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="antialiased flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">

    <!-- SIDEBAR KIRI -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 flex flex-col h-full shrink-0 transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static">
        
        <!-- Logo Brand (Monokrom profesional) -->
        <div class="p-6 border-b border-gray-100 flex flex-col items-center justify-center text-center relative shrink-0">
            <button @click="sidebarOpen = false" class="lg:hidden absolute top-4 right-4 text-gray-500 hover:text-gray-800">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <img src="{{ asset('images/logo-zakira.png') }}" alt="Zakira Logo" class="h-12 object-contain mb-1 filter grayscale contrast-200">
            <span class="text-[11px] text-gray-400 tracking-wider uppercase font-medium">moslem hijab identity</span>
        </div>

        <!-- 
          CONTAINER UTAMA SIDEBAR (Bisa di-scroll bersamaan termasuk bagian profil admin di bawahnya 
          sehingga posisinya menyatu dan tidak "ngambang") 
        -->
        <div class="flex-grow p-4 space-y-6 text-xs text-gray-600 overflow-y-auto flex flex-col justify-between">
            
            <!-- Daftar Menu Navigasi -->
            <div class="space-y-6">
                
                <!-- Platform -->
                <div>
                    <p class="text-[10px] uppercase font-bold text-gray-400 px-3 mb-2 tracking-wider">Platform</p>
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-white border border-gray-200 shadow-sm text-amber-900 font-semibold transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-amber-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </a>
                </div>

                <!-- Manajemen Produk -->
                <div>
                    <p class="text-[10px] uppercase font-bold text-gray-400 px-3 mb-2 tracking-wider">Manajemen Produk</p>
                    <div class="space-y-1">
                        <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-gray-50 text-gray-700 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                            Produk
                        </a>
                        <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-gray-50 text-gray-700 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" /></svg>
                            Brand
                        </a>
                        <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-gray-50 text-gray-700 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" /></svg>
                            Kategori
                        </a>
                    </div>
                </div>

                <!-- Manajemen Konten -->
                <div>
                    <p class="text-[10px] uppercase font-bold text-gray-400 px-3 mb-2 tracking-wider">Manajemen Konten</p>
                    <div class="space-y-1">
                        <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-gray-50 text-gray-700 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            Banner
                        </a>
                        <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-gray-50 text-gray-700 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" /></svg>
                            Keunggulan
                        </a>
                        <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-gray-50 text-gray-700 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg>
                            Media Sosial
                        </a>
                        <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-gray-50 text-gray-700 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636l3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                            Customer Service
                        </a>
                        <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-gray-50 text-gray-700 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                            Admin Handle
                        </a>
                        <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-gray-50 text-gray-700 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                            Informasi Bank
                        </a>
                    </div>
                </div>

                <!-- Pesanan -->
                <div>
                    <p class="text-[10px] uppercase font-bold text-gray-400 px-3 mb-2 tracking-wider">Pesanan</p>
                    <div class="space-y-1">
                        <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-gray-50 text-gray-700 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                            Kelola Pesanan
                        </a>
                    </div>
                </div>

                <!-- Laporan -->
                <div>
                    <p class="text-[10px] uppercase font-bold text-gray-400 px-3 mb-2 tracking-wider">Laporan</p>
                    <div class="space-y-1">
                        <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-gray-50 text-gray-700 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                            Laporan Penjualan Mitra
                        </a>
                    </div>
                </div>

                <!-- Pengguna -->
                <div>
                    <p class="text-[10px] uppercase font-bold text-gray-400 px-3 mb-2 tracking-wider">Pengguna</p>
                    <div class="space-y-1">
                        <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-gray-50 text-gray-700 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                            Kelola Pengguna
                        </a>
                        <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-gray-50 text-gray-700 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            Kelola Seller
                        </a>
                    </div>
                </div>

                <!-- Pemasaran -->
                <div>
                    <p class="text-[10px] uppercase font-bold text-gray-400 px-3 mb-2 tracking-wider">Pemasaran</p>
                    <div class="space-y-1">
                        <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-gray-50 text-gray-700 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" /></svg>
                            Kupon
                        </a>
                    </div>
                </div>

            </div>

            <!-- Footer Sidebar dengan Dropup Profil Admin (Mengikuti scroll di bagian bawah daftar menu) -->
            <div class="pt-6 mt-6 border-t border-gray-100 relative shrink-0" x-data="{ dropupOpen: false }">
                
                <!-- Kotak Menu Dropup (Muncul ke atas ketika dipencet) -->
                <div x-show="dropupOpen" @click.outside="dropupOpen = false" x-transition class="absolute bottom-full left-0 right-0 mb-2 bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden z-50 text-xs" style="display: none;">
                    
                    <!-- Info Akun di Dropdown -->
                    <div class="p-4 flex items-center gap-3 border-b border-gray-100 bg-gray-50/50">
                        <span class="w-10 h-10 rounded-xl bg-gray-200 text-gray-700 font-bold flex items-center justify-center shrink-0">UA</span>
                        <div class="overflow-hidden">
                            <span class="font-bold text-gray-900 block truncate">User Admin</span>
                            <span class="text-[11px] text-gray-500 block truncate">admin@gmail.com</span>
                        </div>
                    </div>

                    <!-- Menu Pengaturan -->
                    <a href="#" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 text-gray-700 font-medium transition border-b border-gray-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        Pengaturan
                    </a>

                    <!-- Tombol Keluar (Logout) -->
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 hover:bg-red-50 text-red-600 font-medium transition text-left cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                            Keluar
                        </button>
                    </form>

                </div>

                <!-- Tombol Pemicu Dropup -->
                <button @click="dropupOpen = !dropupOpen" class="w-full flex items-center justify-between p-2 rounded-xl hover:bg-gray-100 transition cursor-pointer">
                    <div class="flex items-center gap-2 text-xs">
                        <span class="w-8 h-8 rounded-lg bg-gray-200 text-gray-700 font-bold flex items-center justify-center">UA</span>
                        <span class="font-bold text-gray-800">User Admin</span>
                    </div>
                    <!-- Ikon Panah Atas-Bawah -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                    </svg>
                </button>
            </div>

        </div>
    </aside>

    <!-- AREA KONTEN UTAMA -->
    <div class="flex-grow flex flex-col h-screen overflow-hidden">
        
        <!-- NAVBAR ATAS (Responsif HP/Tablet) -->
        <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-6 shrink-0 lg:hidden">
            <button @click="sidebarOpen = !sidebarOpen" class="text-gray-600 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <span class="font-bold text-gray-800 text-sm">Zakira Admin</span>
            
            <!-- Profil Admin di Navbar Atas (Mode HP) -->
            <div class="flex items-center gap-2 text-xs">
                <span class="w-7 h-7 rounded-lg bg-gray-200 text-gray-700 font-bold flex items-center justify-center text-[10px]">UA</span>
            </div>
        </header>

        <!-- KONTEN HALAMAN -->
        <main class="flex-grow overflow-y-auto">
            @yield('content')
        </main>
    </div>

</body>
</html>