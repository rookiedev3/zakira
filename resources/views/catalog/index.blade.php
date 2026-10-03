@extends('layouts.app')

@section('title', 'Katalog Produk — Zakira Moslem Hijab Identity')

@section('content')
    @php
        // PO hanya muncul untuk Admin atau Customer berstatus 'member' (aturan sama dengan menu PO di navbar)
        $canSeePo = Auth::check() && (Auth::user()->role !== 'customer' || Auth::user()->customer_type === 'member');
        $currentType = ($canSeePo && request('type') === 'po') ? 'po' : 'ready';
    @endphp
    <style>
        /* Kolom filter: tinggi besar, sudut membulat, border beige tipis */
        .zk-filter-field {
            width: 100%;
            height: 3.5rem;
            padding: 0 1.25rem;
            font-size: 1.125rem;
            color: #111827;
            background-color: #fff;
            border: 1px solid #e5ddd5;
            border-radius: .75rem;
        }
        .zk-filter-field::placeholder { color: #9ca3af; }

        /* Dropdown: panah di kanan */
        .zk-chevron {
            appearance: none;
            -webkit-appearance: none;
            padding-right: 3rem;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%231f2937' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1.25rem center;
            background-size: .9rem;
        }

        /* Daftar dropdown: sorotan biru */
        .zk-dd { position: fixed; z-index: 9999; box-sizing: border-box; max-height: 15rem; overflow-y: auto; background: #fff; border: 1px solid #767676; }
        .zk-dd-item { padding: .3rem 1rem; color: #111827; cursor: default; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; user-select: none; }
        .zk-dd-item.is-active { background-color: #1967d2; color: #fff; }
    </style>

    <!-- Header Judul Katalog -->
    <section class="max-w-7xl mx-auto px-6 py-12 w-full">
        <span class="text-xs uppercase tracking-widest text-gray-500 font-semibold">KOLEKSI ZAKIRA</span>
        <h1 class="text-4xl font-serif font-medium mt-2 mb-3">Katalog Produk</h1>
        <p class="text-gray-600 text-sm">ReadyStock untuk semua pembeli. Produk PO khusus member Zakira yang sudah login.
        </p>
    </section>

    <!-- Konten Utama: Sidebar Filter & Grid Produk -->
    <main class="max-w-7xl mx-auto px-6 pb-24 grid grid-cols-1 lg:grid-cols-4 gap-8 w-full flex-grow">

        <!-- SIDEBAR FILTER -->
        <form method="GET" action="{{ route('catalog.index') }}" id="filter-form" class="lg:col-span-1"
            x-data="{ type: '{{ $currentType }}' }">
            <aside class="space-y-5 bg-white p-6 rounded-2xl border border-[#eee6de] shadow-sm h-fit">
                <h3 class="text-lg font-medium text-gray-900 pb-1">Filter Produk</h3>

                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Cari produk</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama produk..."
                        class="zk-filter-field">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Brand</label>
                    <select name="brand" data-dd onchange="this.form.submit()" class="zk-filter-field zk-chevron">
                        <option value="">Semua Brand</option>
                        @foreach($brandOptions as $brand)
                            <option value="{{ $brand->id }}" @selected(request('brand') == $brand->id)>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Kategori</label>
                    <select name="category" data-dd onchange="this.form.submit()" class="zk-filter-field zk-chevron">
                        <option value="">Semua Kategori</option>
                        @foreach($categoryOptions as $category)
                            <option value="{{ $category->id }}" @selected(request('category') == $category->id)>
                                {{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Jenis Produk</label>
                    <select name="type" data-dd x-model="type" onchange="this.form.submit()" class="zk-filter-field zk-chevron">
                        <option value="ready" @selected($currentType === 'ready')>Ready Stock</option>
                        @if($canSeePo)
                            <option value="po" @selected($currentType === 'po')>Pre Order (PO)</option>
                        @endif
                    </select>
                </div>

                <!-- Keterangan jenis produk -->
                <div class="bg-[#f3ece5] rounded-xl p-4 flex items-start gap-3">
                    <i class="fas fa-user-shield text-[#6b5c52] mt-1"></i>
                    <div>
                        <p class="text-sm font-semibold text-[#5b4c43]" x-text="type === 'po' ? 'Pre Order (PO)' : 'Ready Stock'">Ready Stock</p>
                        <p class="text-sm text-[#6b5c52]" x-text="type === 'po' ? 'Khusus Member Zakira yang sudah login.' : 'Dapat dibeli tanpa login.'">Dapat dibeli tanpa login.</p>
                    </div>
                </div>

                {{-- @if(request()->hasAny(['search', 'brand', 'category', 'sort', 'type']))
                    <div class="text-center">
                        <a href="{{ route('catalog.index') }}" class="text-sm text-gray-500 hover:underline">Reset filter</a>
                    </div>
                @endif --}}
            </aside>

            {{-- Sorting ikut terkirim bersama form filter --}}
            <input type="hidden" name="sort" id="sort-input" value="{{ request('sort', 'terbaru') }}">
        </form>

        <!-- AREA PRODUK & SORTING -->
        <section class="lg:col-span-3 space-y-6">
            <div
                class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white p-4 rounded-xl border border-gray-200 shadow-sm gap-4">
                <span class="text-xs text-gray-600 font-medium">{{ $products->total() }} produk ditemukan</span>
                <div class="w-full sm:w-auto">
                    <select data-dd
                        onchange="document.getElementById('sort-input').value = this.value; document.getElementById('filter-form').submit();"
                        class="w-full sm:w-48 px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-1 focus:ring-amber-800">
                        <option value="terbaru" @selected(request('sort', 'terbaru') === 'terbaru')>Terbaru</option>
                        <option value="termahal" @selected(request('sort') === 'termahal')>Termahal</option>
                        <option value="termurah" @selected(request('sort') === 'termurah')>Termurah</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                @forelse($products as $product)
                    <a href="{{ route('catalog.show', $product->id) }}" class="block group">
                        <div class="relative h-52 w-full bg-gray-100 overflow-hidden rounded-t-lg">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                    class="w-full h-full object-cover transition-transform group-hover:scale-105">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400 text-sm">Tidak Ada Gambar
                                </div>
                            @endif
                            <span
                                class="absolute top-3 left-3 px-2 py-0.5 text-xs font-semibold rounded-full {{ $product->product_type === 'ready' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ $product->product_type === 'ready' ? 'Ready' : 'PO' }}
                            </span>
                        </div>

                        <div class="p-4 bg-white rounded-b-lg border border-t-0 border-gray-200">
                            <h3 class="font-medium text-gray-900 line-clamp-1">{{ $product->name }}</h3>
                            <p class="text-sm text-gray-600 mt-1">{{ $product->brand->name ?? '' }}</p>
                            <p class="text-sm font-semibold text-gray-900 mt-2">{{ $product->price_range ?? '-' }}</p>
                        </div>
                    </a>
                @empty
                    <div
                        class="col-span-full py-16 text-center text-gray-500 bg-white rounded-xl border border-gray-200 shadow-sm">
                        <p class="text-base font-medium">Belum ada produk yang ditemukan.</p>
                        <p class="text-sm text-gray-400 mt-1">Coba ubah kata kunci atau filter pencarian Anda.</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $products->links() }}
            </div>
        </section>
    </main>

    <script>
    // Dropdown: kolom tetap bawaan, hanya daftar pilihannya diganti agar sorotannya biru
    (function () {
        let dd = null, sel = null, active = -1;

        function close() {
            if (dd) dd.remove();
            dd = null; sel = null; active = -1;
        }

        function setActive(i) {
            if (!dd) return;
            active = i;
            Array.from(dd.children).forEach((el, k) => el.classList.toggle('is-active', k === i));
            const el = dd.children[i];
            if (el) el.scrollIntoView({ block: 'nearest' });
        }

        function choose(i) {
            if (!sel) return;
            const s = sel;
            s.selectedIndex = i;
            close();
            s.dispatchEvent(new Event('input', { bubbles: true }));
            s.dispatchEvent(new Event('change', { bubbles: true }));   // memicu onchange (submit filter / sort)
            s.focus();
        }

        function open(select) {
            close();
            sel = select;
            const cs = getComputedStyle(select);
            dd = document.createElement('div');
            dd.className = 'zk-dd';
            dd.setAttribute('role', 'listbox');
            dd.style.fontSize = cs.fontSize;
            dd.style.fontFamily = cs.fontFamily;

            Array.from(select.options).forEach((opt, i) => {
                const it = document.createElement('div');
                it.className = 'zk-dd-item';
                it.setAttribute('role', 'option');
                it.textContent = opt.text;
                it.style.paddingLeft = cs.paddingLeft;
                if (opt.disabled) it.style.opacity = '.5';
                it.addEventListener('mouseenter', () => setActive(i));
                it.addEventListener('mousedown', ev => {
                    ev.preventDefault();
                    if (!opt.disabled) choose(i);
                });
                dd.appendChild(it);
            });

            document.body.appendChild(dd);

            const r = select.getBoundingClientRect();
            dd.style.width = r.width + 'px';
            dd.style.left = r.left + 'px';
            const h = Math.min(dd.scrollHeight, 240);
            const below = window.innerHeight - r.bottom;
            if (below < h + 12 && r.top > below) {
                dd.style.bottom = (window.innerHeight - r.top + 2) + 'px';
            } else {
                dd.style.top = (r.bottom + 2) + 'px';
            }
            setActive(select.selectedIndex);
        }

        document.addEventListener('mousedown', e => {
            const s = e.target.closest ? e.target.closest('select[data-dd]') : null;
            if (s && !s.disabled) {
                e.preventDefault();          // cegah daftar bawaan browser
                s.focus();
                if (dd && sel === s) close(); else open(s);
                return;
            }
            if (dd && !dd.contains(e.target)) close();
        }, true);

        document.addEventListener('keydown', e => {
            if (dd) {
                if (e.key === 'Escape') { e.preventDefault(); close(); return; }
                if (e.key === 'ArrowDown') { e.preventDefault(); setActive(Math.min(active + 1, sel.options.length - 1)); return; }
                if (e.key === 'ArrowUp') { e.preventDefault(); setActive(Math.max(active - 1, 0)); return; }
                if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); choose(active); return; }
                if (e.key === 'Tab') close();
                return;
            }
            const s = e.target.closest ? e.target.closest('select[data-dd]') : null;
            if (s && !s.disabled && (e.key === 'Enter' || e.key === ' ' || (e.key === 'ArrowDown' && e.altKey))) {
                e.preventDefault();
                open(s);
            }
        }, true);

        window.addEventListener('scroll', e => { if (dd && !dd.contains(e.target) && e.target !== dd) close(); }, true);
        window.addEventListener('resize', close);
    })();
    </script>
@endsection