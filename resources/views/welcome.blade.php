@extends('layouts.app')

@section('title', 'Zakira — Moslem Hijab Identity')

@section('content')
<!-- Section Beranda / Hero -->
<section id="beranda" class="zk-hero">
    <div class="zakira-container zk-hero-inner">
        <div class="zk-hero-copy">
            <span class="zk-eyebrow">ZAKIRA — MOSLEM HIJAB IDENTITY</span>
            <h1>Elegan, Syar’i, Nyaman Setiap Hari</h1>
            <p>
                Koleksi pakaian muslimah berkualitas dengan bahan premium pilihan untuk penampilan anggun, nyaman, dan bernilai ibadah.
            </p>
            <div class="zk-hero-cta">
                <a href="/katalog" class="zk-btn zk-btn-primary">Belanja Sekarang</a>
                <a href="/katalog" class="zk-btn zk-btn-light">Lihat Katalog</a>
            </div>
        </div>
        <div class="zk-hero-visual">
            <img class="zk-hero-main" src="{{ asset('images/hijab.jpeg') }}" alt="Hero Model">
            @if (file_exists(public_path('images/zakira-emblem.png')))
            <img class="zk-hero-mark" src="{{ asset('images/zakira-emblem.png') }}" alt="">
            @endif
        </div>
    </div>
</section>

<!-- Section Slider (lebar penuh, menempel di bawah hero) -->
@if ($sliders->isNotEmpty())
<section>
    <div x-data="{
                    current: 0,
                    total: {{ $sliders->count() }},
                    next() { this.current = (this.current + 1) % this.total },
                    prev() { this.current = (this.current - 1 + this.total) % this.total }
                 }"
        class="zk-banner-slider">

        @foreach ($sliders as $index => $slider)
        <a href="{{ $slider->url ?? '#' }}"
            x-show="current === {{ $index }}"
            x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            class="zk-banner-slide">
            <img src="{{ $slider->image_url }}" alt="{{ $slider->title }}">
            @if (!empty($slider->title))
            <div class="zk-banner-caption">
                <h3>{{ $slider->title }}</h3>
                @if (isset($slider->description) && $slider->description)
                <p>{{ $slider->description }}</p>
                @endif
            </div>
            @endif
        </a>
        @endforeach

        @if ($sliders->count() > 1)
        <button @click="prev()" class="zk-banner-arrow is-prev">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button @click="next()" class="zk-banner-arrow is-next">
            <i class="fas fa-chevron-right"></i>
        </button>

        <div class="zk-banner-dots">
            @foreach ($sliders as $index => $slider)
            <button @click="current = {{ $index }}"
                :class="current === {{ $index }} ? 'is-active' : ''"
                class="zk-banner-dot"></button>
            @endforeach
        </div>
        @endif
    </div>
</section>
@endif

<!-- Section Keunggulan (bar putih lebar penuh) -->
<section class="zk-benefits">
    <div class="zakira-container zk-benefit-grid">
        @if ($advantages->isNotEmpty())
        @foreach ($advantages as $advantage)
        <div class="zk-benefit">
            @if ($advantage->image_url)
            <span class="zk-benefit-thumb">
                <img src="{{ $advantage->image_url }}" alt="{{ $advantage->title }}">
            </span>
            @else
            <i class="fas fa-circle-check"></i>
            @endif
            <div>
                <strong>{{ $advantage->title }}</strong>
                <span class="zk-benefit-desc">{{ $advantage->description }}</span>
            </div>
        </div>
        @endforeach
        @else
        @foreach ([
        ['Bahan Premium', 'Kualitas bahan pilihan'],
        ["Syar'i & Nyaman", 'Menutup aurat dengan anggun'],
        ['Desain Exclusive', 'Model terbaru dan elegan'],
        ['Amanah & Terpercaya', 'Pelayanan terbaik'],
        ] as [$title, $desc])
        <div class="zk-benefit">
            <i class="fas fa-circle-check"></i>
            <div>
                <strong>{{ $title }}</strong>
                <span class="zk-benefit-desc">{{ $desc }}</span>
            </div>
        </div>
        @endforeach
        @endif
    </div>
</section>

<!-- Section Brand -->
<section id="brand" class="zk-section zk-brand-section">
    <div class="zakira-container">
        <div class="zk-brand-heading">
            <div>
                <span class="zk-eyebrow">BRAND PILIHAN</span>
                <h2>Temukan Koleksi Berdasarkan Brand</h2>
                <p>Pilih brand untuk melihat koleksi Ready Stock yang tersedia.</p>
            </div>
        </div>

        @if ($brands->isNotEmpty())
        <div class="zk-brand-carousel">
            @foreach ($brands as $brand)
            <a href="/katalog?brand={{ $brand->id }}" class="zk-brand-card" title="Lihat produk {{ $brand->name }}">
                <div class="zk-brand-logo-wrap">
                    @if ($brand->logo)
                    <img src="{{ asset('storage/' . $brand->logo) }}" alt="{{ $brand->name }}">
                    @else
                    <div class="zk-brand-placeholder"><i class="fas fa-tag"></i></div>
                    @endif
                </div>
                <span>{{ $brand->name }}</span>
            </a>
            @endforeach
        </div>
        @else
        <p class="text-gray-500 text-sm">Brand belum tersedia.</p>
        @endif
    </div>
</section>

<!-- Section Promo (foto lebar di atas "Koleksi Pilihan") -->
<section class="zk-promo-section">
    <div class="zakira-container">
        @if ($promos->isNotEmpty())
        @php
        $columnCount = min($promos->count(), 4);
        $gridClass = match ($columnCount) {
        1 => 'grid-cols-1',
        2 => 'grid-cols-2',
        3 => 'grid-cols-3',
        default => 'grid-cols-2 md:grid-cols-4',
        };
        @endphp
        <div class="zk-promo-grid grid {{ $gridClass }}">
            @foreach ($promos as $promo)
            <a href="{{ $promo->url ?? '#' }}" class="zk-promo-card">
                <img src="{{ $promo->image_url }}" alt="{{ $promo->title }}">
            </a>
            @endforeach
        </div>
        @endif
    </div>
</section>

<!-- Section Ready Stock / Koleksi Pilihan (dari data produk) -->
<section class="zk-section zk-section-soft">
    <div class="zakira-container">
        <div class="zk-section-heading">
            <span class="zk-eyebrow">PILIHAN TERBAIK</span>
            <h2>Koleksi Pilihan</h2>
            <p>Produk Zakira dengan bahan premium dan detail yang dirancang untuk kenyamanan muslimah.</p>
        </div>

        @if ($products->isNotEmpty())
        <div class="zk-product-scroll">
            @foreach ($products as $product)
            <a href="{{ route('catalog.show', $product) }}" class="zakira-product-card">
                <div class="zakira-card-image">
                    @if ($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                    @endif
                    <span class="zakira-stock-badge is-ready">READY STOCK</span>
                </div>
                <div class="zakira-card-body">
                    <h3>{{ $product->name }}</h3>
                    <div class="zakira-card-price">Lihat detail</div>
                </div>
            </a>
            @endforeach
        </div>
        @else
        <p class="text-center text-gray-500 text-sm mb-12">Produk belum tersedia.</p>
        @endif

        <div style="text-align:center;margin-top:30px">
            <a href="/katalog" class="zk-btn zk-btn-light">Lihat Semua Produk</a>
        </div>
    </div>
</section>

<!-- Section Tentang Kami / Member -->
<section id="tentang-kami" class="zk-section">
    <div class="zakira-container">
        <div class="zk-member-banner">
            <div>
                <span class="zk-eyebrow">MEMBER ZAKIRA</span>
                <h3>Produk khusus sesuai akses akun</h3>
                <p>
                    Produk Ready Stock dapat dibeli tanpa login. Produk PO hanya dapat dilihat dan dibeli oleh Member Zakira yang sudah login.
                </p>
            </div>
            <div class="zk-member-actions">
                <a href="/login" class="zk-btn zk-btn-light">Login Member</a>
            </div>
        </div>
    </div>
</section>
@endsection