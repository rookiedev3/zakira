<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Zakira — Moslem Hijab Identity')</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Font Instrument Sans -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Tailwind CSS CDN (tetap dipakai halaman lain & keranjang) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Instrument Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        serif: ['Georgia', '"Times New Roman"', 'serif'],
                    }
                }
            }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Style storefront (class zk-*) -->
    <link rel="stylesheet" href="{{ asset('css/zakira-ui.css') }}">
</head>

<body class="zk-body antialiased">

    <!-- Topbar Informasi (Satu tempat untuk semua halaman) -->
    <div class="zk-topbar">
        <div class="zakira-container zk-topbar-inner">
            <div class="zk-topbar-links">
                <span><i class="fas fa-circle-check"></i>Moslem Hijab Identity</span>
                @foreach ($socialMedia as $sm)
                <a href="{{ $sm->url }}" target="_blank" rel="noopener" class="zk-topbar-social">
                    <span class="zk-topbar-social-icon" style="background-color: {{ $sm->color ?? '#333' }}">
                        <i class="{{ $sm->icon_class }}"></i>
                    </span>
                    {{ $sm->platform }}
                </a>
                @endforeach
            </div>
            <div class="zk-topbar-actions">
                @auth
                <span class="font-medium">Halo, {{ Auth::user()->name }}</span>
                @else
                <a href="/login"><i class="fas fa-user"></i>Login Member</a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Navbar Utama (Satu tempat untuk semua halaman) -->
    <nav class="zk-navbar">
        <div class="zakira-container zk-nav-inner">
            <!-- Logo -->
            <a class="zk-brand" href="/">
                <img src="{{ asset('images/logo-zakira.png') }}" alt="Zakira Logo">
            </a>

            <!-- Navigasi -->
            <div class="zk-nav-links">
                <a href="/">Beranda</a>
                <a href="/#brand">Brand</a>
                <a href="/katalog">Ready Stock</a>

                <!-- MENU PRE ORDER (PO) - Hanya muncul untuk Admin atau Customer berstatus 'member' -->
                @auth
                @if(Auth::user()->role !== 'customer' || Auth::user()->customer_type === 'member')
            <a href="{{ route('catalog.index', ['type' => 'po']) }}">PO (Pre Order)</a>
        @endif
                @endauth

                <a href="/#tentang-kami">Tentang Kami</a>
                <a href="/#kontak">Kontak</a>
            </div>

            <!-- Aksi Kanan (Akun & Keranjang) -->
            <div class="zk-nav-actions">
                <!-- Tombol Profil (Dinamis: Jika login ke profil, jika belum ke login) -->
                @auth
                {{-- Gabungan: pakai member.profile jika ada, kalau tidak fallback ke member.index --}}
                <a class="zk-icon-btn" href="{{ route('member.profile') }}" title="Akun Saya">
                    <i class="far fa-user"></i>
                </a>
                @else
                <a class="zk-icon-btn" href="{{ route('login') }}" title="Login Member">
                    <i class="far fa-user"></i>
                </a>
                @endauth

                <!-- Tombol Keranjang (buka sidebar) -->
                <button type="button" id="cartToggle" title="Keranjang" class="zk-icon-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span id="cartBadge"
                        class="hidden absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-amber-800 text-white text-[10px] font-semibold items-center justify-center">
                        0
                    </span>
                </button>

                <!-- Hamburger (layar kecil) -->
                <button class="zk-mobile-toggle" type="button" id="zk-mobile-toggle" aria-label="Menu">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>

        <!-- Menu mobile (isi sama dengan menu desktop) -->
        <div class="zk-mobile-menu" id="zk-mobile-menu">
            <a href="/">Beranda</a>
            <a href="/#brand">Brand</a>
            <a href="/katalog">Ready Stock</a>
            @auth
             @if(Auth::user()->role !== 'customer' || Auth::user()->customer_type === 'member')
            <a href="{{ route('catalog.index', ['type' => 'po']) }}">PO (Pre Order)</a>
        @endif
            @endauth
            <a href="/#tentang-kami">Tentang Kami</a>
            <a href="/#kontak">Kontak</a>
        </div>
    </nav>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const btn = document.getElementById('zk-mobile-toggle');
            const menu = document.getElementById('zk-mobile-menu');
            if (btn && menu) btn.addEventListener('click', () => menu.classList.toggle('is-open'));

            // Klik menu (Beranda, Brand, Tentang Kami, Kontak) saat sudah di halaman beranda:
            // scroll halus ke bagiannya, tanpa memuat ulang halaman.
            document.addEventListener('click', function (e) {
                const a = e.target.closest('a[href]');
                if (!a || e.ctrlKey || e.metaKey || e.shiftKey || a.target === '_blank') return;

                const url = new URL(a.href, window.location.href);
                if (url.origin !== window.location.origin || url.pathname !== window.location.pathname) return;

                let target = null;
                if (url.hash) {
                    target = document.getElementById(decodeURIComponent(url.hash.slice(1)));
                    if (!target) return;
                } else if (url.search !== window.location.search) {
                    return;
                }

                e.preventDefault();
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                } else {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
                history.pushState(null, '', url.pathname + url.search + url.hash);
                if (menu) menu.classList.remove('is-open');
            });
        });
    </script>

    <!-- Konten Utama yang Dinamis (Berubah sesuai halaman) -->
    <main>
        @yield('content')
    </main>

    <!-- Footer (Satu tempat untuk semua halaman) -->
    <footer class="zk-footer" id="kontak">
        <div class="zakira-container zk-footer-main">
            <div>
                <img class="zk-footer-logo" src="{{ asset('images/logo-zakira2.png') }}" alt="Zakira Logo">
                <p>Busana muslimah syar'i dengan pilihan bahan premium, nyaman, anggun, dan elegan untuk keseharian.</p>

                <div class="zk-social">
                    @foreach ($socialMedia as $sm)
                    <a href="{{ $sm->url }}" target="_blank" rel="noopener"
                        style="background-color: {{ $sm->color ?? '#333' }}">
                        <i class="{{ $sm->icon_class }}"></i>
                    </a>
                    @endforeach
                </div>
            </div>
            <div>
                <h4>Alamat Toko</h4>
                <p>Lokasi Zakira tersedia melalui Google Maps.</p>
            </div>
            <div>
                <h4>Hubungi Kami</h4>
                <div>
                    @forelse ($footerContacts as $cs)
                    <a href="{{ $cs->whatsapp_url }}" target="_blank" rel="noopener" class="zk-footer-contact">
                        <span class="zk-footer-contact-icon">
                            <i class="fab fa-whatsapp"></i>
                        </span>
                        {{ $cs->name }}: {{ $cs->phone_number }}
                    </a>
                    @empty
                    <p>Belum ada kontak.</p>
                    @endforelse
                </div>
            </div>
            <div>
                <h4>Informasi</h4>
                <ul>
                    <li><a href="/katalog">Ready Stock</a></li>
                    @auth
                    @if(Auth::user()->role !== 'customer' || Auth::user()->customer_type === 'member')
            <a href="{{ route('catalog.index', ['type' => 'po']) }}">PO (Pre Order)</a>
        @endif
                    @endauth
                    <li><a href="/login">Login Member</a></li>
                    <li><a href="/cart">Keranjang</a></li>
                </ul>
            </div>
        </div>
        <div class="zakira-container zk-footer-bottom">
            &copy; 2026 Zakira — Moslem Hijab Identity. Powered by IT Solution Yogyakarta.
        </div>
    </footer>

    @if ($floatingWhatsapp)
    <a href="{{ $floatingWhatsapp->whatsapp_url }}" target="_blank" rel="noopener"
        class="zk-wa-float"
        title="Chat {{ $floatingWhatsapp->name }}">
        <i class="fab fa-whatsapp"></i>
    </a>
    @endif

    @include('partials.cart-sidebar')
</body>

</html>