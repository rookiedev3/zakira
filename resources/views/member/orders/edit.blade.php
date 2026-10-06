{{-- resources/views/member/orders/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Pesanan ' . $order->order_number)

{{-- Data dikirim OrderEditController@edit: $order, $items, $variants, $product --}}
@php
    $remaining = $order->editSecondsLeft();
    $dpPercent = $order->dp_percent ?? 30;
    $isFull    = $order->payment_method === 'full';

    // Tombol Batal -> kembali ke halaman sebelumnya (fallback ke beranda)
    $backUrl = url()->previous(url('/'));
    if ($backUrl === url()->current()) { $backUrl = url('/'); }
@endphp

@section('content')
<div class="bg-[#FDFBF7] min-h-screen py-8"
     x-data="orderEdit({
        items: @js($items),
        variants: @js($variants),
        product: @js($product),
        remaining: {{ $remaining }},
        dpPercent: {{ $dpPercent }},
        isFull: {{ $isFull ? 'true' : 'false' }},
        coupon: @js($order->coupon_code ?? ''),
        couponUrl: @js(route('member.order.coupon', $order->order_number))
     })">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Header --}}
        <div class="bg-white rounded-2xl shadow-sm p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit Pesanan</h1>
                <p class="text-gray-600 mt-1">Pesanan #{{ $order->order_number }}</p>
            </div>
            <div class="sm:text-right">
                <p class="text-gray-600">Waktu tersisa untuk edit:</p>
                <p class="text-2xl font-semibold text-[#A67C5B]" x-text="countdownText"></p>
                <p class="text-xs text-gray-400 mt-2 max-w-xs sm:ml-auto">
                    *Edit diperbolehkan max 3x24 jam, selama pesanan pending dan bukti transfer DP belum dikirim.
                </p>
            </div>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 text-sm">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('member.order.update', $order->order_number) }}" x-ref="form">
            @csrf
            @method('PUT')

            {{-- Item pesanan dikirim sebagai hidden input --}}
            <template x-for="(item, i) in items" :key="i">
                <div>
                    <input type="hidden" :name="`items[${i}][variant_id]`" :value="item.variant_id">
                    <input type="hidden" :name="`items[${i}][quantity]`" :value="item.qty">
                </div>
            </template>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- ============ KOLOM KIRI ============ --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Informasi Pengiriman --}}
                    <div class="bg-white rounded-2xl shadow-sm p-6 sm:p-8">
                        <h2 class="text-xl font-semibold text-[#8B5E3C] mb-6">Informasi Pengiriman</h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                            <div>
                                <label class="block text-gray-800 mb-2">Nama Lengkap</label>
                                <input type="text" name="customer_name" required
                                       value="{{ old('customer_name', trim($order->first_name . ' ' . $order->last_name)) }}"
                                       class="w-full rounded-lg border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#8B5E3C]/40 focus:border-[#8B5E3C]">
                            </div>
                            <div>
                                <label class="block text-gray-800 mb-2">WhatsApp</label>
                                <input type="text" name="whatsapp" required
                                       value="{{ old('whatsapp', $order->whatsapp_number) }}"
                                       class="w-full rounded-lg border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#8B5E3C]/40 focus:border-[#8B5E3C]">
                            </div>
                            <div>
                                <label class="block text-gray-800 mb-2">Email (Opsional)</label>
                                <input type="email" name="email"
                                       value="{{ old('email', $order->email) }}"
                                       class="w-full rounded-lg border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#8B5E3C]/40 focus:border-[#8B5E3C]">
                            </div>
                            <div>
                                <label class="block text-gray-800 mb-2">Provinsi</label>
                                <input type="text" name="province" required
                                       value="{{ old('province', $order->province) }}"
                                       class="w-full rounded-lg border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#8B5E3C]/40 focus:border-[#8B5E3C]">
                            </div>
                            <div>
                                <label class="block text-gray-800 mb-2">Kota/Kabupaten</label>
                                <input type="text" name="city" required
                                       value="{{ old('city', $order->city) }}"
                                       class="w-full rounded-lg border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#8B5E3C]/40 focus:border-[#8B5E3C]">
                            </div>
                            <div>
                                <label class="block text-gray-800 mb-2">Kode Pos</label>
                                <input type="text" name="postal_code" required
                                       value="{{ old('postal_code', $order->postal_code) }}"
                                       class="w-full rounded-lg border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#8B5E3C]/40 focus:border-[#8B5E3C]">
                            </div>
                        </div>

                        <div class="mt-5">
                            <label class="block text-gray-800 mb-2">Alamat Lengkap</label>
                            <textarea name="address" rows="4" required
                                      class="w-full rounded-lg border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#8B5E3C]/40 focus:border-[#8B5E3C]">{{ old('address', $order->address) }}</textarea>
                        </div>

                        <div class="mt-5">
                            <label class="block text-gray-800 mb-2">Catatan Pesanan (Opsional)</label>
                            <textarea name="notes" rows="3"
                                      class="w-full rounded-lg border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#8B5E3C]/40 focus:border-[#8B5E3C]">{{ old('notes', $order->notes) }}</textarea>
                        </div>
                    </div>

                    {{-- Item Pesanan --}}
                    <div class="bg-white rounded-2xl shadow-sm p-6 sm:p-8">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-semibold text-gray-900">Item Pesanan</h2>
                            <span class="text-gray-500" x-text="items.length + ' item(s)'"></span>
                        </div>

                        <div class="space-y-3">
                            <template x-for="(item, i) in items" :key="item.variant_id">
                                <div class="border border-gray-200 rounded-xl p-4 flex flex-col sm:flex-row sm:items-center gap-4">
                                    <div class="flex items-center gap-4 flex-1 min-w-0">
                                        <img :src="item.image" :alt="item.name"
                                             class="w-24 h-24 rounded-lg object-cover shrink-0 bg-gray-100">
                                        <div class="min-w-0">
                                            <p class="font-medium text-gray-900 truncate" x-text="item.name"></p>
                                            <p class="text-gray-500" x-text="rupiah(item.price) + ' per item'"></p>
                                            <p class="text-sm text-gray-500"
                                               x-text="`Model: ${item.model} | Warna: ${item.color} | Ukuran: ${item.size}`"></p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3 justify-between sm:justify-end">
                                        <div class="flex items-center gap-3">
                                            <button type="button" @click="changeQty(i, -1)"
                                                    class="w-8 h-8 flex items-center justify-center text-gray-700 hover:text-[#8B5E3C] text-xl"
                                                    aria-label="Kurangi jumlah">&minus;</button>
                                            <input type="number" min="1" x-model.number="item.qty" @change="item.qty = Math.max(1, item.qty || 1)"
                                                   class="w-16 text-center rounded-md border border-gray-200 py-2">
                                            <button type="button" @click="changeQty(i, 1)"
                                                    class="w-8 h-8 flex items-center justify-center text-gray-700 hover:text-[#8B5E3C] text-xl"
                                                    aria-label="Tambah jumlah">+</button>
                                        </div>
                                        <button type="button" @click="removeItem(i)"
                                                class="w-12 h-10 rounded-md bg-red-500 hover:bg-red-600 text-white flex items-center justify-center transition"
                                                aria-label="Hapus item">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <p class="font-semibold text-gray-900 w-28 text-right" x-text="rupiah(item.price * item.qty)"></p>
                                    </div>
                                </div>
                            </template>

                            <p x-show="items.length === 0" class="text-center text-gray-500 py-6">
                                Belum ada item. Tambahkan variant untuk melanjutkan.
                            </p>
                        </div>

                        <button type="button" @click="openModal()"
                                class="mt-4 inline-flex items-center gap-2 rounded-lg border border-blue-200 bg-blue-50 text-blue-700 hover:bg-blue-100 px-4 py-2.5 font-medium transition">
                            <i class="fas fa-circle-plus"></i> Variant
                        </button>
                    </div>

                    {{-- Kupon Diskon --}}
                    <div class="bg-white rounded-2xl shadow-sm p-6 sm:p-8">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Kupon Diskon</h2>
                        <div class="flex gap-3">
                            <input type="text" name="coupon_code" x-model="couponCode"
                                   @keydown.enter.prevent="applyCoupon()"
                                   placeholder="Masukkan kode kupon"
                                   class="flex-1 min-w-0 rounded-lg border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#8B5E3C]/40 focus:border-[#8B5E3C]">
                            <button type="button" @click="applyCoupon()"
                                    class="rounded-lg bg-[#8B5E3C] hover:bg-[#744c2f] text-white font-semibold px-6 transition">
                                Terapkan
                            </button>
                        </div>
                        <p x-show="couponMsg" x-text="couponMsg"
                           :class="couponOk ? 'text-green-600' : 'text-red-600'" class="text-sm mt-2"></p>
                    </div>
                </div>

                {{-- ============ KOLOM KANAN ============ --}}
                <div class="space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm p-6 sm:p-8">
                        <h2 class="text-xl font-semibold text-gray-900 mb-5">Ringkasan Pesanan</h2>

                        <div class="flex justify-between text-gray-600 pb-4 border-b border-gray-200">
                            <span>Subtotal:</span>
                            <span class="font-semibold text-gray-900" x-text="rupiah(subtotal)"></span>
                        </div>
                        <div x-show="discount > 0" class="flex justify-between text-gray-600 py-4 border-b border-gray-200">
                            <span>Diskon:</span>
                            <span class="font-semibold text-green-600" x-text="'- ' + rupiah(discount)"></span>
                        </div>
                        <div class="flex justify-between py-4 border-b border-gray-200">
                            <span class="text-lg font-bold text-gray-900">Total:</span>
                            <span class="text-lg font-bold text-[#A67C5B]" x-text="rupiah(total)"></span>
                        </div>

                        <div x-show="!isFull" class="mt-5 rounded-xl bg-blue-50 p-4 space-y-2">
                            <p class="font-medium text-blue-800">Rincian Down Payment</p>
                            <div class="flex justify-between text-gray-600">
                                <span x-text="`DP (${dpPercent.toFixed(1)}%):`"></span>
                                <span class="font-semibold text-blue-900" x-text="rupiah(dpAmount)"></span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span x-text="`Sisa (${(100 - dpPercent).toFixed(1)}%):`"></span>
                                <span class="font-semibold text-blue-900" x-text="rupiah(total - dpAmount)"></span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <button type="submit" :disabled="items.length === 0"
                                class="w-full rounded-lg bg-[#8B5E3C] hover:bg-[#744c2f] disabled:opacity-50 disabled:cursor-not-allowed text-white font-semibold py-3.5 transition">
                            Simpan Perubahan
                        </button>
                        <a href="{{ $backUrl }}"
                           class="block w-full text-center rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-3.5 transition">
                            Batal
                        </a>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm p-6 sm:p-8">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Informasi Pesanan</h2>
                        <dl class="space-y-3 text-gray-600">
                            <div class="flex justify-between"><dt>Status:</dt><dd class="text-gray-900 capitalize">{{ $order->status }}</dd></div>
                            <div class="flex justify-between"><dt>Pembayaran:</dt><dd class="text-gray-900 capitalize">{{ $order->payment_status }}</dd></div>
                            <div class="flex justify-between"><dt>Dibuat:</dt><dd class="text-gray-900">{{ $order->created_at->format('d M Y H:i') }}</dd></div>
                        </dl>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- ============ MODAL VARIANT ============ --}}
    <div x-show="modal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60"
         @keydown.escape.window="modal = false" @click.self="modal = false">
        <div class="bg-white rounded-xl w-full max-w-3xl max-h-[92vh] flex flex-col overflow-hidden shadow-xl" @click.stop>

            {{-- Kepala --}}
            <div class="p-6 flex items-start gap-4 border-b border-gray-100">
                <img :src="product.image" :alt="product.name" class="w-24 h-24 rounded-lg object-cover bg-gray-100">
                <div class="flex-1">
                    <h3 class="text-xl font-semibold text-gray-900" x-text="product.name"></h3>
                    <p class="text-gray-700 mt-2">Pilih variant untuk pesanan ini</p>
                    <p class="text-gray-500 mt-2">Pilih model, warna, dan ukuran untuk melihat harga</p>
                </div>
                <button type="button" @click="modal = false" class="text-gray-400 hover:text-gray-700 text-2xl leading-none" aria-label="Tutup">&times;</button>
            </div>

            {{-- Isi --}}
            <div class="p-6 space-y-7 overflow-y-auto">
                {{-- Model --}}
                <div>
                    <p class="font-medium text-gray-900 mb-3">Model <span class="text-red-500">*</span></p>
                    <div class="flex flex-wrap gap-3">
                        <template x-for="m in models" :key="m.name">
                            <button type="button" @click="pick('model', m.name)"
                                    :class="sel.model === m.name ? 'border-[#8B5E3C] bg-[#FBF6F1]' : 'border-gray-200 hover:border-gray-300'"
                                    class="border rounded-lg px-5 py-4 text-left min-w-[14rem] transition">
                                <span class="block text-lg text-gray-900" x-text="m.name"></span>
                                <span class="block text-sm text-gray-500" x-text="m.description"></span>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Warna --}}
                <div>
                    <p class="font-medium text-gray-900 mb-3">Warna <span class="text-red-500">*</span></p>
                    <div class="flex flex-wrap gap-3">
                        <template x-for="c in colors" :key="c.name">
                            <button type="button" @click="pick('color', c.name)"
                                    :class="sel.color === c.name ? 'border-[#8B5E3C] bg-[#FBF6F1]' : 'border-gray-200 hover:border-gray-300'"
                                    class="border rounded-lg px-10 py-4 flex flex-col items-center gap-2 transition">
                                <span class="w-12 h-12 rounded-full border border-gray-200" :style="`background:${c.hex || '#fff'}`"></span>
                                <span class="text-gray-900" x-text="c.name"></span>
                            </button>
                        </template>
                        <p x-show="!sel.model" class="text-sm text-gray-400">Pilih model terlebih dahulu.</p>
                    </div>
                </div>

                {{-- Ukuran --}}
                <div>
                    <p class="font-medium text-gray-900 mb-3">Ukuran <span class="text-red-500">*</span></p>
                    <div class="flex flex-wrap gap-3">
                        <template x-for="s in sizes" :key="s.name">
                            <button type="button" :disabled="s.stock <= 0" @click="pick('size', s.name)"
                                    :class="s.stock <= 0
                                        ? 'bg-gray-50 border-transparent cursor-not-allowed'
                                        : (sel.size === s.name ? 'border-[#8B5E3C] bg-[#FBF6F1]' : 'border-gray-200 hover:border-gray-300')"
                                    class="border rounded-lg px-10 py-4 text-center transition">
                                <span class="block text-lg" :class="s.stock <= 0 ? 'text-gray-300' : 'text-gray-900'" x-text="s.name"></span>
                                <span x-show="s.stock <= 0" class="block text-sm text-red-300">Tidak tersedia</span>
                            </button>
                        </template>
                        <p x-show="!sel.color" class="text-sm text-gray-400">Pilih warna terlebih dahulu.</p>
                    </div>
                </div>
            </div>

            {{-- Variant terpilih --}}
            <div x-show="sel.model || sel.color || sel.size" class="px-6 py-4 bg-blue-50 border-t border-blue-100">
                <p class="text-sm text-blue-800 mb-2">Variant yang dipilih:</p>
                <div class="flex flex-wrap items-center gap-2">
                    <template x-for="v in [sel.model, sel.color, sel.size].filter(Boolean)" :key="v">
                        <span class="bg-blue-100 text-blue-800 rounded-full px-3 py-1 text-sm" x-text="v"></span>
                    </template>
                    <span x-show="chosen" class="ml-auto font-semibold text-blue-900" x-text="chosen ? rupiah(chosen.price) : ''"></span>
                </div>
            </div>

            {{-- Aksi --}}
            <div class="p-5 border-t border-gray-100 bg-gray-50 flex justify-end gap-3">
                <button type="button" @click="modal = false"
                        class="rounded-lg border border-gray-200 bg-white hover:bg-gray-100 px-6 py-3 font-medium text-gray-800">Batal</button>
                <button type="button" :disabled="!chosen" @click="addVariant()"
                        class="rounded-lg bg-[#8B5E3C] hover:bg-[#744c2f] disabled:bg-[#D5BBA6] disabled:cursor-not-allowed text-white px-6 py-3 font-semibold transition">
                    + Tambahkan Variant
                </button>
            </div>
        </div>
    </div>
</div>

<style>[x-cloak]{display:none !important}</style>
<script>
    function orderEdit(cfg) {
        return {
            items: cfg.items,
            variants: cfg.variants,   // [{id, model, model_desc, color, color_hex, size, stock, price, image}]
            product: cfg.product,     // {name, image}
            dpPercent: Number(cfg.dpPercent),
            isFull: !!cfg.isFull,
            couponUrl: cfg.couponUrl,
            couponCode: cfg.coupon || '',
            coupon: null,
            couponMsg: '',
            couponOk: false,
            remaining: cfg.remaining,
            modal: false,
            sel: { model: null, color: null, size: null },

            init() {
                setInterval(() => { if (this.remaining > 0) this.remaining--; }, 1000);
                if (this.couponCode) this.applyCoupon();
            },

            rupiah(n) { return 'Rp ' + Number(n || 0).toLocaleString('id-ID'); },

            get countdownText() {
                const h = Math.floor(this.remaining / 3600);
                const m = Math.floor((this.remaining % 3600) / 60);
                return this.remaining > 0 ? `${h} jam ${m} menit` : 'Waktu edit habis';
            },

            get subtotal() { return this.items.reduce((t, i) => t + i.price * i.qty, 0); },
            get totalQty() { return this.items.reduce((t, i) => t + i.qty, 0); },
            get discount() {
                if (!this.coupon) return 0;
                if (this.coupon.type === 'percent') {
                    let d = Math.round(this.subtotal * this.coupon.value / 100);
                    if (this.coupon.max) d = Math.min(d, this.coupon.max);
                    return Math.min(d, this.subtotal);
                }
                return Math.min(this.coupon.value, this.subtotal);
            },
            get total() { return Math.max(0, this.subtotal - this.discount); },
            get dpAmount() { return this.isFull ? this.total : Math.round(this.total * this.dpPercent / 100); },

            // Cek kupon ke server (total akhir tetap dihitung ulang di server saat disimpan)
            async applyCoupon() {
                const code = (this.couponCode || '').trim().toUpperCase();
                if (!code) {
                    this.coupon = null; this.couponOk = false;
                    this.couponMsg = 'Masukkan kode kupon terlebih dahulu.';
                    return;
                }
                try {
                    const res = await fetch(this.couponUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '',
                        },
                        body: JSON.stringify({ code, subtotal: this.subtotal, quantity: this.totalQty }),
                    });
                    const json = await res.json();
                    this.couponOk   = !!json.valid;
                    this.coupon     = json.valid ? { type: json.type, value: json.value, max: json.max } : null;
                    this.couponCode = json.valid ? json.code : this.couponCode;
                    this.couponMsg  = json.message;
                } catch (e) {
                    this.coupon = null; this.couponOk = false;
                    this.couponMsg = 'Gagal memeriksa kupon, coba lagi.';
                }
            },

            // Opsi modal bertingkat: model -> warna -> ukuran
            get models() {
                const seen = {};
                this.variants.forEach(v => { seen[v.model] = seen[v.model] || { name: v.model, description: v.model_desc }; });
                return Object.values(seen);
            },
            get colors() {
                if (!this.sel.model) return [];
                const seen = {};
                this.variants.filter(v => v.model === this.sel.model)
                    .forEach(v => { seen[v.color] = seen[v.color] || { name: v.color, hex: v.color_hex }; });
                return Object.values(seen);
            },
            get sizes() {
                if (!this.sel.model || !this.sel.color) return [];
                const seen = {};
                this.variants.filter(v => v.model === this.sel.model && v.color === this.sel.color)
                    .forEach(v => { seen[v.size] = { name: v.size, stock: v.stock }; });
                return Object.values(seen);
            },
            get chosen() {
                const { model, color, size } = this.sel;
                if (!model || !color || !size) return null;
                return this.variants.find(v => v.model === model && v.color === color && v.size === size && v.stock > 0) || null;
            },

            pick(level, value) {
                this.sel[level] = value;
                if (level === 'model') { this.sel.color = null; this.sel.size = null; }
                if (level === 'color') { this.sel.size = null; }
            },
            openModal() { this.sel = { model: null, color: null, size: null }; this.modal = true; },

            addVariant() {
                const v = this.chosen;
                if (!v) return;
                const existing = this.items.find(i => i.variant_id === v.id);
                if (existing) {
                    existing.qty++;
                } else {
                    this.items.push({
                        variant_id: v.id, name: this.product.name, image: v.image || this.product.image,
                        model: v.model, color: v.color, size: v.size, price: v.price, qty: 1,
                    });
                }
                this.modal = false;
            },

            changeQty(i, d) { this.items[i].qty = Math.max(1, this.items[i].qty + d); },
            removeItem(i) {
                if (this.items.length === 1 && !confirm('Ini item terakhir. Yakin ingin menghapusnya?')) return;
                this.items.splice(i, 1);
            },
        };
    }

    // Cadangan: muat Alpine jika layout belum memuatnya
    window.addEventListener('load', () => {
        if (!window.Alpine) {
            const s = document.createElement('script');
            s.src = 'https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js';
            s.defer = true;
            document.head.appendChild(s);
        }
    });
</script>
@endsection