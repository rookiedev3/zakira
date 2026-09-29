<form method="POST" action="{{ $action }}" class="space-y-8">
    @csrf
    @if ($method === 'PUT') @method('PUT') @endif

    <!-- Informasi Dasar -->
    <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-xs">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Dasar</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <label class="block text-sm font-medium text-zinc-800">Nama Kupon</label>
                <input type="text" name="name" value="{{ old('name', $coupon->name ?? '') }}" placeholder="Contoh: Diskon Lebaran 2026" required
                       class="w-full border rounded-lg text-sm py-2 h-10 px-3 bg-white text-zinc-700 shadow-xs border-zinc-200 focus:outline-none">
                @error('name') <span class="text-sm font-medium text-red-500">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-medium text-zinc-800">Kode Kupon</label>
                <div class="flex gap-2">
                    <input type="text" name="code" id="kupon-code-input" value="{{ old('code', $coupon->code ?? '') }}" placeholder="LEBARAN2026" required
                           class="w-full border rounded-lg text-sm py-2 h-10 px-3 bg-white text-zinc-700 shadow-xs border-zinc-200 focus:outline-none flex-1">
                    <button type="button" onclick="generateKuponCode('kupon-code-input')"
                            class="h-10 px-4 inline-flex items-center rounded-lg text-sm bg-white hover:bg-zinc-50 text-zinc-800 border border-zinc-200 shadow-xs transition">
                        Generate
                    </button>
                </div>
                @error('code') <span class="text-sm font-medium text-red-500">{{ $message }}</span> @enderror
            </div>

            <div class="md:col-span-2 space-y-2">
                <label class="block text-sm font-medium text-zinc-800">Deskripsi</label>
                <textarea name="description" rows="3" placeholder="Deskripsi kupon (opsional)"
                          class="w-full border rounded-lg text-sm p-3 bg-white text-zinc-700 shadow-xs border-zinc-200 focus:outline-none">{{ old('description', $coupon->description ?? '') }}</textarea>
            </div>
        </div>
    </div>

    <!-- Konfigurasi Diskon -->
    <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-xs">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Konfigurasi Diskon</h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="space-y-2">
                <label class="block text-sm font-medium text-zinc-800">Tipe Diskon</label>
                <select name="type" class="w-full border rounded-lg text-sm px-3 h-10 bg-white text-zinc-700 shadow-xs border-zinc-200 focus:outline-none">
                    <option value="percentage" @selected(old('type', $coupon->type ?? 'percentage') === 'percentage')>Persentase (%)</option>
                    <option value="fixed" @selected(old('type', $coupon->type ?? '') === 'fixed')>Nominal Tetap (Rp)</option>
                </select>
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-medium text-zinc-800">Nilai Diskon</label>
                <input type="number" name="value" step="0.01" value="{{ old('value', $coupon->value ?? '') }}" placeholder="10" required
                       class="w-full border rounded-lg text-sm h-10 px-3 bg-white text-zinc-700 shadow-xs border-zinc-200 focus:outline-none">
                <p class="text-sm text-zinc-500">Persentase diskon (0-100)</p>
                @error('value') <span class="text-sm font-medium text-red-500">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-medium text-zinc-800">Maksimal Diskon</label>
                <input type="number" name="max_discount_amount" step="0.01" value="{{ old('max_discount_amount', $coupon->max_discount_amount ?? '') }}" placeholder="100000"
                       class="w-full border rounded-lg text-sm h-10 px-3 bg-white text-zinc-700 shadow-xs border-zinc-200 focus:outline-none">
                <p class="text-sm text-zinc-500">Batas maksimal diskon (opsional)</p>
            </div>
        </div>
    </div>

    <!-- Tanggal & Batasan Penggunaan -->
    <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-xs">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Tanggal & Batasan Penggunaan</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <label class="block text-sm font-medium text-zinc-800">Tanggal Mulai</label>
                <input type="datetime-local" name="starts_at" value="{{ old('starts_at', optional($coupon->starts_at ?? null)->format('Y-m-d\TH:i')) }}"
                       class="w-full border rounded-lg text-sm h-10 px-3 bg-white text-zinc-700 shadow-xs border-zinc-200 focus:outline-none">
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-medium text-zinc-800">Tanggal Kedaluwarsa</label>
                <input type="datetime-local" name="expires_at" value="{{ old('expires_at', optional($coupon->expires_at ?? null)->format('Y-m-d\TH:i')) }}"
                       class="w-full border rounded-lg text-sm h-10 px-3 bg-white text-zinc-700 shadow-xs border-zinc-200 focus:outline-none">
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-medium text-zinc-800">Batas Total Penggunaan</label>
                <input type="number" name="usage_limit" value="{{ old('usage_limit', $coupon->usage_limit ?? '') }}" placeholder="100"
                       class="w-full border rounded-lg text-sm h-10 px-3 bg-white text-zinc-700 shadow-xs border-zinc-200 focus:outline-none">
                <p class="text-sm text-zinc-500">Kosongkan untuk tidak terbatas</p>
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-medium text-zinc-800">Batas per Pelanggan</label>
                <input type="number" name="usage_limit_per_customer" value="{{ old('usage_limit_per_customer', $coupon->usage_limit_per_customer ?? '') }}" placeholder="1"
                       class="w-full border rounded-lg text-sm h-10 px-3 bg-white text-zinc-700 shadow-xs border-zinc-200 focus:outline-none">
                <p class="text-sm text-zinc-500">Berapa kali satu pelanggan bisa menggunakan kupon ini</p>
            </div>
        </div>
    </div>

    <!-- Target Customer -->
    <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-xs">
        <div class="flex items-start justify-between gap-4 mb-4">
            <div>
                <h3 class="text-lg font-medium text-gray-900">Target Voucher</h3>
                <p class="text-sm text-gray-500 mt-1">Tentukan voucher ini dapat dipakai oleh Member, Non Member, atau semua pembeli.</p>
            </div>
            <span class="text-xs font-semibold rounded-full bg-blue-50 text-blue-700 px-3 py-1">Wajib dipilih</span>
        </div>

        @php $scope = old('customer_scope', $coupon->customer_scope ?? 'all'); @endphp
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <label class="cursor-pointer rounded-xl border p-4 transition {{ $scope === 'all' ? 'border-blue-500 bg-blue-50/50 ring-1 ring-blue-200' : 'border-gray-200 hover:border-blue-300' }}">
                <input type="radio" name="customer_scope" value="all" @checked($scope === 'all') class="mr-2">
                <span class="font-semibold text-gray-900">Semua Pembeli</span>
                <p class="text-xs text-gray-500 mt-2">Berlaku untuk Member dan pembelian tanpa login.</p>
            </label>
            <label class="cursor-pointer rounded-xl border p-4 transition {{ $scope === 'member' ? 'border-purple-500 bg-purple-50/50 ring-1 ring-purple-200' : 'border-gray-200 hover:border-purple-300' }}">
                <input type="radio" name="customer_scope" value="member" @checked($scope === 'member') class="mr-2">
                <span class="font-semibold text-gray-900">Khusus Member</span>
                <p class="text-xs text-gray-500 mt-2">Hanya muncul dan dapat digunakan setelah member login.</p>
            </label>
            <label class="cursor-pointer rounded-xl border p-4 transition {{ $scope === 'non_member' ? 'border-blue-500 bg-blue-50/50 ring-1 ring-blue-200' : 'border-gray-200 hover:border-blue-300' }}">
                <input type="radio" name="customer_scope" value="non_member" @checked($scope === 'non_member') class="mr-2">
                <span class="font-semibold text-gray-900">Khusus Non Member</span>
                <p class="text-xs text-gray-500 mt-2">Hanya untuk Ready Stock yang checkout langsung tanpa login.</p>
            </label>
        </div>
    </div>

    <!-- Persyaratan -->
    <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-xs">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Persyaratan</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <label class="block text-sm font-medium text-zinc-800">Minimal Pembelian</label>
                <input type="number" name="minimum_amount" value="{{ old('minimum_amount', $coupon->minimum_amount ?? '') }}" placeholder="200000"
                       class="w-full border rounded-lg text-sm h-10 px-3 bg-white text-zinc-700 shadow-xs border-zinc-200 focus:outline-none">
                <p class="text-sm text-zinc-500">Minimal total belanja untuk menggunakan kupon. Contoh: Rp 200.000</p>
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-medium text-zinc-800">Minimal Kuantitas</label>
                <input type="number" name="minimum_quantity" value="{{ old('minimum_quantity', $coupon->minimum_quantity ?? '') }}" placeholder="2"
                       class="w-full border rounded-lg text-sm h-10 px-3 bg-white text-zinc-700 shadow-xs border-zinc-200 focus:outline-none">
                <p class="text-sm text-zinc-500">Minimal jumlah item dalam keranjang</p>
            </div>
        </div>
    </div>

    <!-- Batasan Produk & Kategori -->
    <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-xs">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Batasan Produk & Kategori</h3>

        <div class="space-y-4">
            <div class="space-y-2">
                <label class="block text-sm font-medium text-zinc-800">Tipe Batasan</label>
                <select name="restriction_type" class="w-full border rounded-lg text-sm px-3 h-10 bg-white text-zinc-700 shadow-xs border-zinc-200 focus:outline-none">
                    <option value="">Tidak ada batasan</option>
                    <option value="only" @selected(old('restriction_type', $coupon->restriction_type ?? '') === 'only')>Hanya untuk produk/kategori terpilih</option>
                    <option value="except" @selected(old('restriction_type', $coupon->restriction_type ?? '') === 'except')>Kecuali produk/kategori terpilih</option>
                </select>
            </div>

            @php
                $isEdit = (bool) $coupon;
                $selectedCategories = collect(old('categories', $isEdit ? $coupon->categories->pluck('id')->all() : []))->map(fn ($v) => (int) $v)->all();
                $selectedBrands = collect(old('brands', $isEdit ? $coupon->brands->pluck('id')->all() : []))->map(fn ($v) => (int) $v)->all();
                $selectedProducts = collect(old('products', $isEdit ? $coupon->products->pluck('id')->all() : []))->map(fn ($v) => (int) $v)->all();
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-zinc-800">Kategori</label>
                    <div class="border rounded-lg border-zinc-200 max-h-48 overflow-y-auto p-3 space-y-2 bg-white">
                        @forelse ($categories as $category)
                            <label class="flex items-center gap-2 cursor-pointer text-sm text-zinc-700">
                                <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                                       @checked(in_array($category->id, $selectedCategories))
                                       class="w-4 h-4 rounded border-zinc-300">
                                {{ $category->name }}
                            </label>
                        @empty
                            <p class="text-xs text-zinc-400">Belum ada kategori.</p>
                        @endforelse
                    </div>
                    <p class="text-sm text-zinc-500">Pilih kategori yang ingin dibatasi (opsional)</p>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-medium text-zinc-800">Brand</label>
                    <div class="border rounded-lg border-zinc-200 max-h-48 overflow-y-auto p-3 space-y-2 bg-white">
                        @forelse ($brands as $brand)
                            <label class="flex items-center gap-2 cursor-pointer text-sm text-zinc-700">
                                <input type="checkbox" name="brands[]" value="{{ $brand->id }}"
                                       @checked(in_array($brand->id, $selectedBrands))
                                       class="w-4 h-4 rounded border-zinc-300">
                                {{ $brand->name }}
                            </label>
                        @empty
                            <p class="text-xs text-zinc-400">Belum ada brand.</p>
                        @endforelse
                    </div>
                    <p class="text-sm text-zinc-500">Pilih brand yang ingin dibatasi (opsional)</p>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-medium text-zinc-800">Produk Spesifik</label>
                    <div class="border rounded-lg border-zinc-200 max-h-48 overflow-y-auto p-3 space-y-2 bg-white">
                        @forelse ($products as $product)
                            <label class="flex items-center gap-2 cursor-pointer text-sm text-zinc-700">
                                <input type="checkbox" name="products[]" value="{{ $product->id }}"
                                       @checked(in_array($product->id, $selectedProducts))
                                       class="w-4 h-4 rounded border-zinc-300">
                                {{ $product->name }}
                            </label>
                        @empty
                            <p class="text-xs text-zinc-400">Belum ada produk.</p>
                        @endforelse
                    </div>
                    <p class="text-sm text-zinc-500">Pilih produk spesifik (opsional)</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Pengaturan Status & Aksi -->
    <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-xs space-y-4">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Pengaturan</h3>

        <div class="space-y-3">
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="active" value="1" @checked(old('active', $coupon->active ?? true)) class="w-4 h-4 rounded border-zinc-300 text-[var(--color-accent)] focus:ring-[var(--color-accent)]">
                <span class="text-sm font-medium text-zinc-800">Aktifkan kupon</span>
            </label>
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="new_customers_only" value="1" @checked(old('new_customers_only', $coupon->new_customers_only ?? false)) class="w-4 h-4 rounded border-zinc-300 text-[var(--color-accent)] focus:ring-[var(--color-accent)]">
                <span class="text-sm font-medium text-zinc-800">Hanya untuk pelanggan baru</span>
            </label>
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="stackable" value="1" @checked(old('stackable', $coupon->stackable ?? false)) class="w-4 h-4 rounded border-zinc-300 text-[var(--color-accent)] focus:ring-[var(--color-accent)]">
                <span class="text-sm font-medium text-zinc-800">Dapat dikombinasi dengan kupon lain</span>
            </label>
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="show_in_checkout" value="1" @checked(old('show_in_checkout', $coupon->show_in_checkout ?? false)) class="w-4 h-4 rounded border-zinc-300 text-[var(--color-accent)] focus:ring-[var(--color-accent)]">
                <span class="text-sm font-medium text-zinc-800">Tampilkan di halaman checkout</span>
            </label>
        </div>
    </div>

    <!-- Tampilan di Checkout -->
    <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-xs">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Tampilan di Checkout</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <label class="block text-sm font-medium text-zinc-800">Label Checkout</label>
                <input type="text" name="checkout_label" placeholder="Diskon Spesial Hari Ini!" value="{{ old('checkout_label', $coupon->checkout_label ?? '') }}"
                       class="w-full border rounded-lg text-sm h-10 px-3 bg-white text-zinc-700 shadow-xs border-zinc-200 focus:outline-none">
                <p class="text-sm text-zinc-500">Judul yang ditampilkan di halaman checkout (opsional)</p>
            </div>
            <div class="space-y-2">
                <label class="block text-sm font-medium text-zinc-800">Deskripsi Checkout</label>
                <input type="text" name="checkout_description" 
                placeholder="Hemat hingga 50% untuk pembelian hari ini!" value="{{ old('checkout_description', $coupon->checkout_description ?? '') }}"
                       class="w-full border rounded-lg text-sm h-10 px-3 bg-white text-zinc-700 shadow-xs border-zinc-200 focus:outline-none">
                <p class="text-sm text-zinc-500">Deskripsi yang ditampilkan di halaman checkout (opsional)</p>
            </div>
        </div>
    </div>

    <!-- Tombol Aksi Bawah -->
    <div class="flex justify-end gap-3">
        <a href="{{ route('kupon.index') }}" class="relative items-center font-medium justify-center gap-2 whitespace-nowrap h-10 text-sm rounded-lg px-4 inline-flex bg-transparent hover:bg-zinc-800/5 text-zinc-800 dark:text-white transition">
            Batal
        </a>
        <button type="submit" class="relative items-center font-medium justify-center gap-2 whitespace-nowrap h-10 text-sm rounded-lg px-4 inline-flex bg-[var(--color-accent)] hover:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_10%)] text-[var(--color-accent-foreground)] border border-black/10 shadow-xs transition">
            <span>{{ $coupon ? 'Perbarui Kupon' : 'Simpan Kupon' }}</span>
        </button>
    </div>

</form>

<script>
    function generateKuponCode(inputId) {
        var chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        var code = '';
        for (var i = 0; i < 8; i++) {
            code += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        document.getElementById(inputId).value = code;
    }
</script> 