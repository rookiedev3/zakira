{{-- resources/views/catalog/show.blade.php --}}
@extends('layouts.app')
@section('title', $product->name)

@php
    $waNumber = $whatsappNumber ?? null;
    $waUrl = $waNumber
        ? 'https://wa.me/' . $waNumber . '?text=' . urlencode('Halo, saya tertarik dengan produk ' . $product->name)
        : '#';

    // Harga awal sesuai pilihan default (item pertama tiap varian)
    $firstKey = ($product->models->first()->id ?? 0) . '|'
              . ($product->colors->first()->id ?? 0) . '|'
              . ($product->sizes->first()->id ?? 0);
    $initialPrice = $priceMap[$firstKey] ?? 0;
@endphp

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-0 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        {{-- ========== Kolom 1: Gambar ========== --}}
        <div class="space-y-4">
            <div class="relative aspect-[2/3] bg-gray-100 rounded-lg overflow-hidden cursor-zoom-in" id="imageContainer">
                @if($product->image)
                    <img id="mainImage"
                         src="{{ asset('storage/' . $product->image) }}"
                         alt="{{ $product->name }}"
                         class="w-full h-full object-cover transition-transform duration-200 ease-out">

                    <div id="zoomIndicator"
                         class="hidden lg:block absolute bottom-3 right-3 bg-black/60 text-white text-xs px-2 py-1 rounded pointer-events-none opacity-0 transition-opacity">
                        <i class="fas fa-search-plus mr-1"></i>Arahkan untuk perbesar
                    </div>
                @else
                    <div class="w-full h-full flex items-center justify-center">
                        <i class="fas fa-image text-gray-400 text-4xl"></i>
                    </div>
                @endif
            </div>
        </div>

        {{-- ========== Kolom 2: Detail ========== --}}
        <div class="space-y-6">

            {{-- Breadcrumb --}}
            <nav class="text-sm text-gray-500">
                <a href="{{ url('/') }}" class="hover:text-[#B4775E]">Beranda</a>
                <span class="mx-2">/</span>
                <a href="{{ route('catalog.index') }}" class="hover:text-[#B4775E]">Katalog</a>
                <span class="mx-2">/</span>
                <span class="text-gray-900">{{ $product->name }}</span>
            </nav>

            {{-- Nama + badge --}}
            <div class="flex items-center gap-3 flex-wrap">
                <h1 class="text-3xl font-bold text-gray-900">{{ $product->name }}</h1>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                    {{ $product->product_type === 'ready' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">
                    {{ $product->product_type === 'ready' ? 'READY STOCK' : 'PRE-ORDER' }}
                </span>
            </div>

            {{-- Deskripsi --}}
            @if($product->description)
                <p class="text-gray-700 leading-relaxed">{{ $product->description }}</p>
            @endif

            {{-- Harga (berubah sesuai varian yang dipilih) --}}
            <div id="productPrice" class="text-2xl font-bold text-[#B4775E]">
                {{ $initialPrice > 0
                    ? 'Rp ' . number_format($initialPrice, 0, ',', '.')
                    : ($product->price_range ?? 'Hubungi penjual') }}
            </div>

            {{-- Merek --}}
            @if($product->brand)
                <div class="flex items-center space-x-2 text-sm text-gray-600">
                    <span class="font-medium">Merek:</span>
                    <span class="text-[#B4775E] font-medium">{{ $product->brand->name }}</span>
                </div>
            @endif

            {{-- SKU & Kategori --}}
            <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                @if(!empty($product->sku))
                    <div>
                        <span class="font-medium">SKU:</span>
                        <span>{{ $product->sku }}</span>
                    </div>
                @endif
                @if($product->categories->isNotEmpty())
                    <div>
                        <span class="font-medium">Kategori:</span>
                        @foreach($product->categories as $cat)
                            <span class="inline-block bg-gray-100 px-2 py-1 rounded text-xs ml-1">{{ $cat->name }}</span>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Notifikasi (untuk wishlist / redirect biasa) --}}
            @if (session('success'))
                <div class="bg-green-100 border border-green-200 text-green-800 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            {{-- ========== Form Keranjang (AJAX -> CartController@add) ========== --}}
            <form id="cartForm" method="POST" action="{{ route('cart.add') }}" class="space-y-6">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">

                {{-- Model --}}
                @if($product->models->isNotEmpty())
                    <div class="space-y-3">
                        <h3 class="font-medium text-gray-900">Model: <span class="text-red-500">*</span></h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($product->models as $model)
                                <label class="cursor-pointer">
                                    <input type="radio" name="model_id" value="{{ $model->id }}"
                                           class="peer sr-only" required @checked($loop->first)>
                                    <span class="block px-4 py-2 border rounded-lg transition-all border-gray-300 hover:border-[#D9A896]
                                                 peer-checked:border-[#B4775E] peer-checked:bg-[#FBF1EC] peer-checked:text-[#B4775E]">
                                        {{ $model->name }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Warna --}}
                @if($product->colors->isNotEmpty())
                    <div class="space-y-3">
                        <h3 class="font-medium text-gray-900">Warna: <span class="text-red-500">*</span></h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($product->colors as $color)
                                <label class="cursor-pointer">
                                    <input type="radio" name="color_id" value="{{ $color->id }}"
                                           class="peer sr-only" required @checked($loop->first)>
                                    <span class="block px-4 py-2 border rounded-lg transition-all border-gray-300 hover:border-[#D9A896]
                                                 peer-checked:border-[#B4775E] peer-checked:bg-[#FBF1EC] peer-checked:text-[#B4775E]">
                                        {{ $color->name }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Ukuran --}}
                @if($product->sizes->isNotEmpty())
                    <div class="space-y-3">
                        <h3 class="font-medium text-gray-900">Ukuran: <span class="text-red-500">*</span></h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($product->sizes as $size)
                                <label class="cursor-pointer">
                                    <input type="radio" name="size_id" value="{{ $size->id }}"
                                           class="peer sr-only" required @checked($loop->first)>
                                    <span class="block px-4 py-2 border rounded-lg font-medium transition-all border-gray-300 hover:border-[#D9A896]
                                                 peer-checked:border-[#B4775E] peer-checked:bg-[#FBF1EC] peer-checked:text-[#B4775E]">
                                        {{ $size->size }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Kuantitas + Tambah ke Keranjang --}}
                <div class="flex items-center space-x-4">
                    <div class="flex items-center border border-gray-300 rounded-lg">
                        <button type="button" id="qtyMinus" class="px-3 py-2 hover:bg-gray-100 transition-colors">
                            <i class="fas fa-minus text-sm"></i>
                        </button>
                        <input type="number" name="quantity" id="qtyInput" min="1" value="1" required
                               class="w-14 text-center border-0 focus:ring-0 p-2 [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none">
                        <button type="button" id="qtyPlus" class="px-3 py-2 hover:bg-gray-100 transition-colors">
                            <i class="fas fa-plus text-sm"></i>
                        </button>
                    </div>

                    <button type="submit" id="addToCartBtn"
                            class="flex-1 bg-[#B4775E] text-white px-6 py-3 rounded-lg font-medium hover:bg-[#9C6650] transition-colors disabled:opacity-60 disabled:cursor-not-allowed">
                        <i class="fas fa-shopping-cart mr-2"></i>Tambah ke Keranjang
                    </button>
                </div>
            </form>

            {{-- Pesan lewat WhatsApp --}}
            <div>
                <a href="{{ $waUrl }}"
                   @if($waNumber) target="_blank" rel="noopener" @endif
                   class="inline-flex items-center justify-center bg-[#16A34A] text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-[#15803D] transition-colors">
                    <i class="fab fa-whatsapp mr-2 text-lg"></i>Pesan lewat WhatsApp
                </a>
            </div>

            {{-- Wishlist --}}
            <form method="POST" action="{{ Route::has('wishlist.toggle') ? route('wishlist.toggle') : '#' }}">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <button type="submit"
                        class="inline-flex items-center bg-gray-100 text-gray-800 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                    <i class="fas fa-heart mr-2"></i>Tambah ke Wishlist
                </button>
            </form>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // ---------- Zoom gambar ----------
    const box = document.getElementById('imageContainer');
    const img = document.getElementById('mainImage');
    const hint = document.getElementById('zoomIndicator');

    if (box && img) {
        const isDesktop = () => window.innerWidth >= 1024;

        box.addEventListener('mouseenter', () => { if (isDesktop() && hint) hint.style.opacity = 1; });
        box.addEventListener('mouseleave', () => {
            img.style.transform = 'scale(1)';
            img.style.transformOrigin = 'center center';
            if (hint) hint.style.opacity = 0;
        });
        box.addEventListener('mousemove', (e) => {
            if (!isDesktop()) return;
            const r = box.getBoundingClientRect();
            img.style.transformOrigin = `${((e.clientX - r.left) / r.width) * 100}% ${((e.clientY - r.top) / r.height) * 100}%`;
            img.style.transform = 'scale(2.5)';
        });
        box.addEventListener('click', () => {
            if (isDesktop()) return;
            img.style.transform = img.style.transform === 'scale(2)' ? 'scale(1)' : 'scale(2)';
        });
    }

    // ---------- Stepper kuantitas ----------
    const qty = document.getElementById('qtyInput');
    document.getElementById('qtyMinus')?.addEventListener('click', () => {
        qty.value = Math.max(1, (parseInt(qty.value) || 1) - 1);
    });
    document.getElementById('qtyPlus')?.addEventListener('click', () => {
        qty.value = (parseInt(qty.value) || 1) + 1;
    });

    // ---------- Harga berubah sesuai varian ----------
    const form = document.getElementById('cartForm');
    const btn = document.getElementById('addToCartBtn');
    const priceEl = document.getElementById('productPrice');

    const PRICES = @json($priceMap);
    const FALLBACK = @json($product->price_range ?? 'Hubungi penjual');
    const rupiah = new Intl.NumberFormat('id-ID');

    const selected = (name) => {
        const el = form.querySelector(`input[name="${name}"]:checked`);
        return el ? el.value : 0;
    };

    function updatePrice() {
        const key = [selected('model_id'), selected('color_id'), selected('size_id')].join('|');
        const price = PRICES[key] || 0;
        priceEl.textContent = price > 0 ? 'Rp ' + rupiah.format(price) : FALLBACK;
    }

    form.addEventListener('change', updatePrice);
    updatePrice();

    // ---------- Tambah ke keranjang (tanpa reload) ----------
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        btn.disabled = true;

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: new FormData(form),
            });
            const data = await res.json();

            if (!res.ok) {
                const msg = data.errors
                    ? Object.values(data.errors).flat().join(' ')
                    : (data.message || 'Gagal menambahkan ke keranjang.');
                window.showToast?.('Gagal', msg, 'error');
                return;
            }

            window.Cart?.render(data);   // update angka di ikon keranjang + isi sidebar
            window.showToast?.('Berhasil!', data.message, 'success');
        } catch (err) {
            window.showToast?.('Gagal', 'Terjadi kesalahan jaringan.', 'error');
        } finally {
            btn.disabled = false;
        }
    });
});
</script>
@endsection