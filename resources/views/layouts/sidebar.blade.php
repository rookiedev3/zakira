{{--
resources/views/layouts/sidebar.blade.php

LAYOUT KLASIK (@extends / @yield) — Tailwind CSS murni, tanpa Flux/Livewire.
Toggle sidebar mobile & dropdown profile pakai JS vanilla biasa.

Cara pakai di halaman lain:

@extends('layouts.sidebar')

@section('title', 'Dashboard')

@section('content')
... isi halaman ...
@endsection
--}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Zakira Admin')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-white font-sans text-zinc-800" style="font-family: 'Inter', sans-serif;">

    <!-- Backdrop mobile (klik untuk tutup sidebar) -->
    <div id="sidebar-backdrop" onclick="toggleSidebar()" class="fixed inset-0 bg-black/30 z-20 hidden lg:hidden"></div>

    <div class="flex min-h-screen">

        <!-- SIDEBAR -->
        <aside id="sidebar"
            class="fixed lg:sticky top-0 h-screen w-64 -translate-x-full lg:translate-x-0 transition-transform duration-200 z-30 flex flex-col gap-4 p-4 border-r border-zinc-200 bg-zinc-50 overflow-y-auto">

            <!-- Toggle close (mobile) -->
            <button type="button" onclick="toggleSidebar()"
                class="lg:hidden self-end w-10 h-10 flex items-center justify-center rounded-lg text-zinc-500 hover:bg-zinc-800/5 hover:text-zinc-800">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
                </svg>
            </button>

            <!-- Brand -->
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-1">
                <img src="{{ asset('images/logo-zakira.png') }}" alt="Zakira Logo" class="h-16 mx-auto object-contain">
            </a>

            <!-- Nav -->
            <nav class="flex flex-col gap-1 flex-1">

                <div class="px-1 py-2 text-xs text-zinc-400">Platform</div>
                <a href="{{ route('dashboard') }}"
                    class="flex items-center gap-3 h-9 px-3 rounded-lg text-sm font-medium
                          {{ request()->routeIs('dashboard') ? 'bg-white border border-zinc-200 text-zinc-800' : 'text-zinc-500 hover:bg-zinc-800/5 hover:text-zinc-800' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                    Dashboard
                </a>

                <div class="px-1 py-2 text-xs text-zinc-400">Manajemen Produk</div>
                <a href="{{ route('products.index') }}"
                    class="flex items-center gap-3 h-9 px-3 rounded-lg text-sm font-medium text-zinc-500 hover:bg-zinc-800/5 hover:text-zinc-800">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                    </svg>
                    Produk
                </a>
                <a href="{{ route('brands.index') }}"
                    class="flex items-center gap-3 h-9 px-3 rounded-lg text-sm font-medium text-zinc-500 hover:bg-zinc-800/5 hover:text-zinc-800">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
                    </svg>
                    Brand
                </a>
                <a href="{{ route('categories.index') }}"
                    class="flex items-center gap-3 h-9 px-3 rounded-lg text-sm font-medium text-zinc-500 hover:bg-zinc-800/5 hover:text-zinc-800">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
                    </svg>
                    Kategori
                </a>

                <div class="px-1 py-2 text-xs text-zinc-400">Manajemen Konten</div>
                <a href="{{ route('banners.index') }}"
                    class="flex items-center gap-3 h-9 px-3 rounded-lg text-sm font-medium text-zinc-500 hover:bg-zinc-800/5 hover:text-zinc-800">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>
                    Banner
                </a>
                <a href="{{ route('advantages.index') }}"
                    class="flex items-center gap-3 h-9 px-3 rounded-lg text-sm font-medium text-zinc-500 hover:bg-zinc-800/5 hover:text-zinc-800">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                    </svg>
                    Keunggulan
                </a>
                <a href="{{ route('social-media.index') }}"
                    class="flex items-center gap-3 h-9 px-3 rounded-lg text-sm font-medium text-zinc-500 hover:bg-zinc-800/5 hover:text-zinc-800">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.933-2.185 2.25 2.25 0 0 0-3.933 2.185Z" />
                    </svg>
                    Media Sosial
                </a>
                <a href="{{ route('customer-service.index') }}"
                    class="flex items-center gap-3 h-9 px-3 rounded-lg text-sm font-medium text-zinc-500 hover:bg-zinc-800/5 hover:text-zinc-800">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                    </svg>
                    Customer Service
                </a>
                <a href="{{ route('admin-handles.index') }}"
                    class="flex items-center gap-3 h-9 px-3 rounded-lg text-sm font-medium text-zinc-500 hover:bg-zinc-800/5 hover:text-zinc-800">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                    Admin Handle
                </a>
                <a href="{{ route('banks.index') }}"
                    class="flex items-center gap-3 h-9 px-3 rounded-lg text-sm font-medium text-zinc-500 hover:bg-zinc-800/5 hover:text-zinc-800">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                    </svg>
                    Informasi Bank
                </a>

                <div class="px-1 py-2 text-xs text-zinc-400">Pesanan</div>
                <a href="{{ route('orders.index') }}"
                    class="flex items-center gap-3 h-9 px-3 rounded-lg text-sm font-medium text-zinc-500 hover:bg-zinc-800/5 hover:text-zinc-800">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>
                    Kelola Pesanan
                </a>

                <div class="px-1 py-2 text-xs text-zinc-400">Laporan</div>
                <a href="{{ route('reports.mitra-sales') }}"
                    class="flex items-center gap-3 h-9 px-3 rounded-lg text-sm font-medium text-zinc-500 hover:bg-zinc-800/5 hover:text-zinc-800">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                    </svg>
                    Laporan Penjualan Mitra
                </a>

                <div class="px-1 py-2 text-xs text-zinc-400">Pengguna</div>
                <a href="{{ route('users.index') }}"
                    class="flex items-center gap-3 h-9 px-3 rounded-lg text-sm font-medium text-zinc-500 hover:bg-zinc-800/5 hover:text-zinc-800">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                    </svg>
                    Kelola Pengguna
                </a>
                <a href="{{ route('sellers.index') }}"
                    class="flex items-center gap-3 h-9 px-3 rounded-lg text-sm font-medium text-zinc-500 hover:bg-zinc-800/5 hover:text-zinc-800">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                    </svg>
                    Kelola Seller
                </a>

                <div class="px-1 py-2 text-xs text-zinc-400">Pemasaran</div>
                <a href="{{ route('coupons.index') }}"
                    class="flex items-center gap-3 h-9 px-3 rounded-lg text-sm font-medium text-zinc-500 hover:bg-zinc-800/5 hover:text-zinc-800">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
                    </svg>
                    Kupon
                </a>
            </nav>

            <!-- Profile dropdown (pakai <details>, tanpa JS library) -->
            <details class="relative">
                <summary class="list-none flex items-center gap-2 p-1 rounded-lg hover:bg-zinc-800/5 cursor-pointer">
                    <span
                        class="w-8 h-8 shrink-0 flex items-center justify-center rounded-md bg-zinc-200 text-zinc-800 text-sm font-medium">UA</span>
                    <span class="text-sm text-zinc-500 font-medium truncate">User Admin</span>
                    <svg class="w-4 h-4 ml-auto text-zinc-400" fill="none" stroke="currentColor" stroke-width="2.5"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m7 15 5 5 5-5M7 9l5-5 5 5" />
                    </svg>
                </summary>

                <div
                    class="absolute bottom-full left-0 mb-2 w-56 bg-white border border-zinc-200 rounded-lg shadow-lg p-1 z-40">
                    <div class="flex items-center gap-2 px-2 py-2 text-sm">
                        <span
                            class="w-8 h-8 flex items-center justify-center rounded-lg bg-neutral-200 text-black text-xs font-medium">UA</span>
                        <div class="leading-tight">
                            <p class="font-semibold text-zinc-800">User Admin</p>
                            <p class="text-xs text-zinc-500">admin@gmail.com</p>
                        </div>
                    </div>
                    <hr class="my-1 border-zinc-100">
                    <a href="{{ route('settings.profile') }}"
                        class="flex items-center px-2 py-1.5 rounded-md text-sm font-medium text-zinc-800 hover:bg-zinc-50">
                        Pengaturan
                    </a>
                    <hr class="my-1 border-zinc-100">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full text-left flex items-center px-2 py-1.5 rounded-md text-sm font-medium text-zinc-800 hover:bg-zinc-50">
                            Keluar
                        </button>
                    </form>
                </div>
            </details>
        </aside>

        <!-- MAIN AREA -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Header mobile -->
            <header class="lg:hidden flex items-center h-14 px-6 border-b border-zinc-200">
                <button type="button" onclick="toggleSidebar()"
                    class="w-10 h-10 -ms-2.5 flex items-center justify-center rounded-lg text-zinc-500 hover:bg-zinc-800/5">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M2 6.75A.75.75 0 0 1 2.75 6h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 6.75Zm0 6.5a.75.75 0 0 1 .75-.75h14.5a.75.75 0 0 1 0 1.5H2.75a.75.75 0 0 1-.75-.75Z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <span
                    class="ml-auto w-8 h-8 flex items-center justify-center rounded-md bg-zinc-200 text-zinc-800 text-sm font-medium">UA</span>
            </header>

            <main class="p-6 lg:p-8">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('-translate-x-full');
            document.getElementById('sidebar-backdrop').classList.toggle('hidden');
        }
    </script>
</body>

</html>