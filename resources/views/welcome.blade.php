@extends('layouts.app')

@section('title', 'Zakira — Moslem Hijab Identity')

@section('content')
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

<!-- Section Slider + Keunggulan -->
<section class="max-w-7xl mx-auto px-6 py-6">
    @if ($sliders->isNotEmpty())
    <div x-data="{
                    current: 0,
                    total: {{ $sliders->count() }},
                    next() { this.current = (this.current + 1) % this.total },
                    prev() { this.current = (this.current - 1 + this.total) % this.total }
                 }"
        class="relative w-full h-[350px] md:h-[450px] rounded-2xl overflow-hidden shadow-md bg-gray-200 mb-8">

        @foreach ($sliders as $index => $slider)
        <a href="{{ $slider->url ?? '#' }}"
            x-show="current === {{ $index }}"
            x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            class="absolute inset-0 block">
            <img src="{{ $slider->image_url }}" alt="{{ $slider->title }}" class="w-full h-full object-cover">
        </a>
        @endforeach

        @if ($sliders->count() > 1)
        <button @click="prev()" class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/80 rounded-full flex items-center justify-center hover:bg-white transition shadow">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button @click="next()" class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/80 rounded-full flex items-center justify-center hover:bg-white transition shadow">
            <i class="fas fa-chevron-right"></i>
        </button>

        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2">
            @foreach ($sliders as $index => $slider)
            <button @click="current = {{ $index }}"
                :class="current === {{ $index }} ? 'bg-white' : 'bg-white/50'"
                class="w-2 h-2 rounded-full transition"></button>
            @endforeach
        </div>
        @endif
    </div>
    @endif

    @php
    $checkIcon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-white">
        <path d="M20 6 9 17l-5-5" />
    </svg>';
    @endphp

    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
        @if ($advantages->isNotEmpty())
        @foreach ($advantages as $advantage)
        <div class="p-2 flex items-center gap-3 text-left">
            @if ($advantage->image_url)
            <img src="{{ $advantage->image_url }}" alt="{{ $advantage->title }}"
                class="w-10 h-10 object-cover rounded-full flex-shrink-0">
            @else
            <div class="w-10 h-10 rounded-full bg-[#3D2C24] flex-shrink-0 flex items-center justify-center">
                {!! $checkIcon !!}
            </div>
            @endif
            <div>
                <h3 class="font-bold text-sm mb-1">{{ $advantage->title }}</h3>
                <p class="text-xs text-gray-500">{{ $advantage->description }}</p>
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
        <div class="p-2 flex items-center gap-3 text-left">
            <div class="w-10 h-10 rounded-full bg-[#3D2C24] flex-shrink-0 flex items-center justify-center">
                {!! $checkIcon !!}
            </div>
            <div>
                <h3 class="font-bold text-sm mb-1">{{ $title }}</h3>
                <p class="text-xs text-gray-500">{{ $desc }}</p>
            </div>
        </div>
        @endforeach
        @endif
    </div>
</section>

<!-- Section Brand -->
<section id="brand" class="max-w-7xl mx-auto px-6 py-20">
    <span class="text-xs uppercase tracking-widest text-gray-500 font-semibold">BRAND PILIHAN</span>
    <h2 class="text-3xl font-serif font-medium mt-2 mb-2">Temukan Koleksi Berdasarkan Brand</h2>
    <p class="text-gray-600 text-sm mb-10">Pilih brand untuk melihat koleksi Ready Stock yang tersedia.</p>

    @if ($brands->isNotEmpty())
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
        @foreach ($brands as $brand)
        <a href="/katalog?brand={{ $brand->id }}"
            class="border border-gray-200 rounded-2xl p-6 bg-white shadow-sm hover:shadow-md transition text-center block">
            <div class="h-32 bg-gray-100 rounded-xl mb-4 overflow-hidden">
                @if ($brand->logo)
                <img src="{{ asset('storage/' . $brand->logo) }}"
                    alt="{{ $brand->name }}"
                    class="w-full h-full object-cover">
                @endif
            </div>
            <h4 class="font-bold text-sm uppercase">{{ $brand->name }}</h4>
        </a>
        @endforeach
    </div>
    @else
    <p class="text-gray-500 text-sm">Brand belum tersedia.</p>
    @endif
</section>

<!-- Section Promo (foto lebar di atas "Koleksi Pilihan") -->
<section class="max-w-7xl mx-auto px-6 py-6">
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
    <div class="grid {{ $gridClass }} gap-4">
        @foreach ($promos as $promo)
        <a href="{{ $promo->url ?? '#' }}" class="block rounded-2xl overflow-hidden shadow-md bg-gray-200 aspect-square">
            <img src="{{ $promo->image_url }}" alt="{{ $promo->title }}" class="w-full h-full object-cover hover:scale-105 transition duration-300">
        </a>
        @endforeach
    </div>
    @endif
</section>

<!-- Section Ready Stock / Koleksi Pilihan (dari data produk) -->
<section class="bg-[#FAF8F5] py-20 border-t border-gray-200">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center max-w-xl mx-auto mb-12">
            <span class="text-xs uppercase tracking-widest text-gray-500 font-semibold">PILIHAN TERBAIK</span>
            <h2 class="text-3xl font-serif font-medium mt-2 mb-2">Koleksi Pilihan</h2>
            <p class="text-gray-600 text-sm">Produk Zakira dengan bahan premium dan detail yang dirancang untuk kenyamanan muslimah.</p>
        </div>

        @if ($products->isNotEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 mb-12">
            @foreach ($products as $product)
            <a href="{{ route('catalog.show', $product) }}"
                class="bg-white rounded-xl overflow-hidden shadow-sm border border-gray-200 hover:shadow-md transition block">
                <div class="h-64 bg-gray-200 overflow-hidden">
                    @if ($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}"
                        alt="{{ $product->name }}"
                        class="w-full h-full object-cover hover:scale-105 transition duration-300">
                    @endif
                </div>
                <div class="p-4">
                    <span class="text-[10px] bg-green-100 text-green-800 px-2 py-1 rounded font-semibold">READY STOCK</span>
                    <h4 class="font-medium text-sm mt-2">{{ $product->name }}</h4>
                    <p class="text-amber-800 font-bold text-sm mt-1">Lihat detail</p>
                </div>
            </a>
            @endforeach
        </div>
        @else
        <p class="text-center text-gray-500 text-sm mb-12">Produk belum tersedia.</p>
        @endif

        <div class="text-center">
            <a href="/katalog" class="border border-[#3D2C24] text-[#3D2C24] px-8 py-3 rounded-md text-sm font-medium hover:bg-[#3D2C24] hover:text-white transition">
                Lihat Semua Produk
            </a>
        </div>
    </div>
</section>

<!-- Section Tentang Kami / Member -->
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
@endsection