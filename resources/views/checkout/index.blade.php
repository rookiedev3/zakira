{{-- resources/views/checkout/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Checkout')

@php
$in = 'w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#B4775E] focus:border-[#B4775E]';
$lbl = 'block text-sm font-medium text-gray-700 mb-2';
$req = '<span class="text-red-500">*</span>';
@endphp

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-0 py-8">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Checkout</h1>
        <nav class="text-sm text-gray-500 mt-2">
            <a href="{{ url('/') }}" class="hover:text-[#B4775E]">Beranda</a>
            <span class="mx-2">/</span>
            <a href="{{ url('/cart') }}" class="hover:text-[#B4775E]">Keranjang</a>
            <span class="mx-2">/</span>
            <span class="text-gray-900">Checkout</span>
        </nav>
    </div>

    @if ($errors->has('coupon') || session('error'))
    <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        {{ $errors->first('coupon') ?: session('error') }}
    </div>
    @endif

    <form id="checkoutForm" method="POST" action="{{ route('checkout.store') }}" novalidate>
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            {{-- ============ KOLOM KIRI ============ --}}
            <div class="space-y-6">

                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">Informasi Pemesanan</h2>
                    <div class="mb-5 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                        <i class="fas fa-check-circle mr-1"></i> Ready Stock dapat checkout langsung tanpa login member.
                    </div>

                    <div class="space-y-4">
                        <div class="rounded-xl border border-green-200 bg-green-50 p-4">
                            <div class="font-semibold text-green-900">Checkout Cepat — Ready Stock</div>
                            <p class="text-sm text-green-700 mt-1">Tidak perlu login member. Isi data penerima dan pilih pengiriman untuk menyelesaikan pesanan.</p>
                        </div>

                        {{-- Nama Seller (otomatis dari ID) --}}
                        <div>
                            <label for="seller_name" class="{{ $lbl }}">Nama Seller {!! $req !!}</label>
                            <input type="text" id="seller_name" readonly
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50 focus:outline-none"
                                placeholder="Nama seller otomatis dari ID">
                        </div>

                        {{-- ID Seller + Cek ID --}}
                        <div>
                            <label for="seller_id" class="{{ $lbl }}">ID Seller {!! $req !!}</label>
                            <div class="flex gap-2">
                                <input type="text" id="seller_id" name="seller_id" value="{{ old('seller_id', $prefill['seller_id']) }}" autocomplete="off"
                                    class="flex-1 {{ $in }}" placeholder="Masukkan ID seller, contoh ZK1234">
                                <button type="button" id="sellerCheck"
                                    class="px-4 py-2 rounded-lg bg-[#B4775E] text-white font-medium hover:bg-[#9C6650] transition-colors disabled:opacity-60 whitespace-nowrap">
                                    Cek ID
                                </button>
                            </div>
                            <div id="sellerMsg" class="mt-1"></div>
                            @error('seller_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        {{-- Admin Handle (muncul setelah ID Seller valid) --}}
                        <div id="adminHandleWrap" class="{{ (old('seller_id', $prefill['seller_id']) || $errors->has('admin_handle')) ? '' : 'hidden' }}">
                            <label for="admin_handle" class="{{ $lbl }}">Admin Handle {!! $req !!}</label>
                            <select id="admin_handle" name="admin_handle" class="{{ $in }}">
                                <option value="">Pilih admin</option>
                                @foreach (($admins ?? []) as $a)
                                <option value="{{ $a }}" @selected(old('admin_handle', $prefill['admin_handle'] ?? '')===$a)>{{ $a }}</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Pilih admin yang menangani pesanan Anda.</p>
                            @error('admin_handle')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="whatsapp_number" class="{{ $lbl }}">No WhatsApp {!! $req !!}</label>
                            <input type="text" id="whatsapp_number" name="whatsapp_number" value="{{ old('whatsapp_number', $prefill['whatsapp_number']) }}"
                                inputmode="tel" class="{{ $in }}" placeholder="Contoh: 081234567890">
                            @error('whatsapp_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="country" class="{{ $lbl }}">Negara</label>
                            <input type="text" id="country" value="Indonesia" disabled
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-100 text-gray-500">
                        </div>

                        <div>
                            <label for="shipping_method" class="{{ $lbl }}">Expedisi {!! $req !!}</label>
                            <select id="shipping_method" name="shipping_method" class="{{ $in }}">
                                <option value="">Pilih ekspedisi</option>
                                @foreach ($shipping as $s)
                                <option value="{{ $s }}" @selected(old('shipping_method', $prefill['shipping_method'])===$s)>{{ $s }}</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Pilih ekspedisi yang digunakan. Biaya ongkir tidak dihitung otomatis di website.</p>
                            @error('shipping_method')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        {{-- Kirim ke alamat yang berbeda --}}
                        <label for="ship_different" class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" id="ship_different" name="ship_different" value="1" @checked(old('ship_different'))
                                class="rounded border-gray-300 text-[#B4775E] focus:ring-[#B4775E]">
                            <span class="text-sm text-gray-700">Kirim ke alamat yang berbeda</span>
                        </label>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Alamat Pengiriman</h2>

                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="first_name" class="{{ $lbl }}">Nama Depan {!! $req !!}</label>
                                <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $prefill['first_name']) }}" class="{{ $in }}" placeholder="Nama depan">
                                @error('first_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="last_name" class="{{ $lbl }}">Nama Belakang {!! $req !!}</label>
                                <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $prefill['last_name']) }}" class="{{ $in }}" placeholder="Nama belakang">
                                @error('last_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div>
                            <label for="address" class="{{ $lbl }}">Alamat {!! $req !!}</label>
                            <textarea id="address" name="address" rows="3" class="{{ $in }}" placeholder="Alamat lengkap">{{ old('address', $prefill['address']) }}</textarea>
                            @error('address')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="city" class="{{ $lbl }}">Kota {!! $req !!}</label>
                                <input type="text" id="city" name="city" value="{{ old('city', $prefill['city']) }}" class="{{ $in }}" placeholder="Kota">
                                @error('city')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="province" class="{{ $lbl }}">Provinsi {!! $req !!}</label>
                                <select id="province" name="province" class="{{ $in }}">
                                    <option value="">Pilih provinsi</option>
                                    @foreach ($provinces as $p)
                                    <option value="{{ $p }}" @selected(old('province', $prefill['province'])===$p)>{{ $p }}</option>
                                    @endforeach
                                </select>
                                @error('province')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div>
                            <label for="postal_code" class="{{ $lbl }}">Kode Pos {!! $req !!}</label>
                            <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code', $prefill['postal_code']) }}" inputmode="numeric" maxlength="5"
                                class="{{ $in }}" placeholder="12345">
                            @error('postal_code')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="notes" class="{{ $lbl }}">Catatan (Opsional)</label>
                            <textarea id="notes" name="notes" rows="3" class="{{ $in }}" placeholder="Catatan tambahan untuk pesanan Anda">{{ old('notes') }}</textarea>
                            @error('notes')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- Alamat Pengiriman Berbeda (muncul bila checkbox dicentang) --}}
                <div id="shipDifferentCard" class="bg-white rounded-lg shadow-sm border p-6 {{ old('ship_different') ? '' : 'hidden' }}">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Alamat Pengiriman Berbeda</h2>

                    <div class="space-y-4">
                        <div>
                            <label for="ship_recipient" class="{{ $lbl }}">Nama Penerima {!! $req !!}</label>
                            <input type="text" id="ship_recipient" name="ship_recipient" value="{{ old('ship_recipient') }}" class="{{ $in }}" placeholder="Nama penerima">
                            @error('ship_recipient')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="ship_phone" class="{{ $lbl }}">No WhatsApp Penerima {!! $req !!}</label>
                            <input type="text" id="ship_phone" name="ship_phone" value="{{ old('ship_phone') }}" inputmode="tel" class="{{ $in }}" placeholder="Contoh: 081234567890">
                            @error('ship_phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="ship_address" class="{{ $lbl }}">Alamat {!! $req !!}</label>
                            <textarea id="ship_address" name="ship_address" rows="3" class="{{ $in }}" placeholder="Alamat lengkap penerima">{{ old('ship_address') }}</textarea>
                            @error('ship_address')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="ship_city" class="{{ $lbl }}">Kota {!! $req !!}</label>
                                <input type="text" id="ship_city" name="ship_city" value="{{ old('ship_city') }}" class="{{ $in }}" placeholder="Kota">
                                @error('ship_city')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="ship_province" class="{{ $lbl }}">Provinsi {!! $req !!}</label>
                                <select id="ship_province" name="ship_province" class="{{ $in }}">
                                    <option value="">Pilih provinsi</option>
                                    @foreach ($provinces as $p)
                                    <option value="{{ $p }}" @selected(old('ship_province')===$p)>{{ $p }}</option>
                                    @endforeach
                                </select>
                                @error('ship_province')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div>
                            <label for="ship_postal_code" class="{{ $lbl }}">Kode Pos {!! $req !!}</label>
                            <input type="text" id="ship_postal_code" name="ship_postal_code" value="{{ old('ship_postal_code') }}" inputmode="numeric" maxlength="5"
                                class="{{ $in }}" placeholder="12345">
                            @error('ship_postal_code')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============ KOLOM KANAN ============ --}}
            <div class="space-y-6">

                {{-- Pesanan Anda --}}
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Pesanan Anda</h2>
                    <div class="space-y-4">
                        @foreach ($cart['items'] as $it)
                        <div class="flex items-start space-x-4 py-4 border-b border-gray-200 last:border-b-0">
                            <div class="w-16 h-16 bg-gray-100 rounded overflow-hidden flex-shrink-0">
                                @if ($it['image'])
                                <img src="{{ $it['image'] }}" alt="{{ $it['name'] }}" class="w-full h-full object-cover">
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-medium text-gray-900 text-sm">{{ $it['name'] }}</h3>
                                <div class="text-xs text-gray-500 mt-1">
                                    @if ($it['model'])<div>Model: {{ $it['model'] }}</div>@endif
                                    @if ($it['color'])<div>Warna: {{ $it['color'] }}</div>@endif
                                    @if ($it['size'])<div>Ukuran: {{ $it['size'] }}</div>@endif
                                    <div>Qty: {{ $it['quantity'] }}</div>
                                </div>
                            </div>
                            <div class="text-sm font-medium text-gray-900">{{ $it['subtotal_formatted'] }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Kupon Diskon --}}
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Kupon Diskon</h3>

                    <div class="mb-6">
                        <label for="coupon_code" class="{{ $lbl }}">Kode Kupon</label>
                        <div class="flex space-x-2">
                            <input type="text" id="coupon_code" placeholder="Masukkan kode kupon" class="flex-1 {{ $in }}">
                            <button type="button" id="couponApply"
                                class="bg-[#B4775E] text-white px-4 h-10 rounded-lg text-sm font-medium hover:bg-[#9C6650] transition-colors whitespace-nowrap">
                                Terapkan
                            </button>
                        </div>
                        <div id="couponMsg" class="mt-2"></div>
                    </div>

                    @if (count($cart['available_coupons']))
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-md font-medium text-gray-800">Voucher Tersedia</h4>
                            <span class="text-xs text-gray-500">Opsional</span>
                        </div>
                        <div class="space-y-3">
                            @foreach ($cart['available_coupons'] as $c)
                            <div data-coupon="{{ $c['code'] }}"
                                class="border rounded-lg p-3 transition-all cursor-pointer {{ $c['applied'] ? 'border-[#B4775E] bg-[#B4775E]/5' : 'border-gray-200 hover:border-[#B4775E]' }}">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h5 class="font-medium text-gray-900 text-sm">{{ $c['title'] }}</h5>
                                            <span class="border border-dashed border-[#B4775E] text-[#B4775E] rounded px-1.5 text-[11px] font-semibold">{{ $c['code'] }}</span>
                                        </div>
                                        @if ($c['description'])<p class="text-xs text-gray-600 mt-1">{{ $c['description'] }}</p>@endif
                                        <div class="text-xs text-[#B4775E] font-medium mt-1">
                                            {{ str_replace(' OFF', ' Off', $c['discount_label']) }}@if ($c['max_discount_formatted']) (Maks. {{ $c['max_discount_formatted'] }})@endif
                                        </div>
                                        @if ($c['min_amount_formatted'])<div class="text-xs text-gray-500 mt-1">Min. pembelian {{ $c['min_amount_formatted'] }}</div>@endif
                                    </div>
                                    <input type="radio" name="coupon" value="{{ $c['code'] }}" @checked($c['applied']) class="mt-1 text-[#B4775E] pointer-events-none">
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Informasi Pembayaran --}}
                <div class="bg-[#B4775E]/10 rounded-xl shadow-md border border-gray-200 p-6 space-y-6">
                    <h3 class="text-xl font-bold text-gray-800">Informasi Pembayaran</h3>
                    <p class="text-gray-700">Mohon transfer ke rekening berikut dengan menyertakan bukti transfer:</p>
                    <div class="space-y-5">
                        @forelse ($banks as $b)
                        <div class="pt-4 border-t-2 border-dashed border-gray-700">
                            <div class="text-sm text-gray-700 space-y-1">
                                <div><span class="font-semibold text-gray-800">{{ $b['bank'] }}:</span> <span class="font-mono text-[#B4775E]">{{ $b['number'] }}</span></div>
                                <div>A/N: <span class="font-medium text-gray-800">{{ $b['name'] }}</span></div>
                            </div>
                        </div>
                        @empty
                        <p class="text-sm text-gray-600">Informasi rekening akan dikirim admin melalui WhatsApp.</p>
                        @endforelse
                    </div>
                    <p class="text-gray-700 font-medium mt-4">Alhamdulillah, Jazakumullahu Khairan 🥰</p>
                </div>

                {{-- Total Pembayaran --}}
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Total Pembayaran</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between text-gray-600"><span>Subtotal</span><span>{{ $cart['subtotal_formatted'] }}</span></div>
                        <div id="shippingSummaryRow" class="{{ old('shipping_method', $prefill['shipping_method']) ? '' : 'hidden' }}">
                            <div class="flex justify-between text-gray-600">
                                <span>Pengiriman</span>
                                <span id="shippingSummary">{{ old('shipping_method', $prefill['shipping_method']) }}</span>
                            </div>
                        </div>
                        @if ($cart['coupon'])
                        <div class="flex justify-between text-green-600">
                            <span>Diskon ({{ $cart['coupon']['code'] }})</span><span>- {{ $cart['discount_formatted'] }}</span>
                        </div>
                        @endif
                        <hr>
                        <div class="flex justify-between text-lg font-semibold text-gray-900">
                            <span>Total</span><span class="text-[#B4775E]">{{ $cart['total_formatted'] }}</span>
                        </div>

                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 space-y-3 mt-4">
                            <h4 class="font-medium text-gray-800 text-sm">Metode Pembayaran</h4>

                            <div class="flex items-start space-x-3">
                                <input type="radio" id="checkout_payment_dp" name="checkout_payment_method" value="dp"
                                    class="mt-1 text-[#B4775E] focus:ring-[#B4775E]" @checked($cart['payment_method']==='dp' )>
                                <label for="checkout_payment_dp" class="flex-1 cursor-pointer">
                                    <div class="flex justify-between items-center">
                                        <span class="font-medium text-gray-900">Down Payment (DP)</span>
                                        <span class="text-sm font-bold text-blue-600">{{ $cart['dp_formatted'] }}</span>
                                    </div>
                                    <p class="text-xs text-gray-600 mt-1">Bayar {{ $cart['dp_percent'] }}% sekarang, sisanya kapan saja</p>
                                    <div class="mt-2 p-2 bg-blue-50 rounded text-xs text-blue-700">
                                        <i class="fas fa-info-circle mr-1"></i> Sisa pembayaran: {{ $cart['remaining_formatted'] }}
                                    </div>
                                </label>
                            </div>

                            <div class="flex items-start space-x-3">
                                <input type="radio" id="checkout_payment_full" name="checkout_payment_method" value="full"
                                    class="mt-1 text-[#B4775E] focus:ring-[#B4775E]" @checked($cart['payment_method']==='full' )>
                                <label for="checkout_payment_full" class="flex-1 cursor-pointer">
                                    <div class="flex justify-between items-center">
                                        <span class="font-medium text-gray-900">Pembayaran Penuh</span>
                                        <span class="text-sm font-bold text-green-600">{{ $cart['total_formatted'] }}</span>
                                    </div>
                                    <p class="text-xs text-gray-600 mt-1">Bayar seluruh jumlah sekarang</p>
                                </label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" id="submitOrder"
                        class="mt-6 w-full h-11 inline-flex items-center justify-center bg-[#B4775E] text-white rounded-lg text-sm font-medium hover:bg-[#9C6650] transition-colors disabled:opacity-60 disabled:cursor-not-allowed">
                        Buat Pesanan
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const csrf = @json(csrf_token());
        const URLS = {
            apply: @json(route('cart.coupon.apply')),
            remove: @json(route('cart.coupon.remove')),
            payment: @json(route('cart.payment')),
            seller: @json(route('checkout.seller')),
        };
        const coupons = @json($cart['available_coupons']);
        const msg = document.getElementById('couponMsg');
        const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;'
        } [c]));

        const showErrors = (lines) => {
            msg.innerHTML = [].concat(lines).map((l) => `<p class="text-red-500 text-sm">${esc(l)}</p>`).join('');
        };

        async function api(url, method, body = null) {
            const res = await fetch(url, {
                method,
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: body ? JSON.stringify(body) : null,
            });
            return {
                ok: res.ok,
                data: await res.json().catch(() => ({}))
            };
        }

        // Total & DP dihitung server, jadi cukup muat ulang halaman setelah kupon/pembayaran berubah.
        // Isian formulir dijaga lewat sessionStorage agar tidak hilang saat reload.
        const form = document.getElementById('checkoutForm');
        const KEY = 'zk_checkout_draft';
        const fields = ['seller_id', 'whatsapp_number', 'admin_handle', 'shipping_method', 'first_name', 'last_name', 'address', 'city', 'province', 'postal_code', 'notes', 'ship_recipient', 'ship_phone', 'ship_address', 'ship_city', 'ship_province', 'ship_postal_code'];
        const save = () => sessionStorage.setItem(KEY, JSON.stringify({
            ...Object.fromEntries(fields.map((f) => [f, form.elements[f]?.value ?? ''])),
            ship_different: form.elements.ship_different.checked,
        }));
        const reload = () => {
            save();
            location.reload();
        };
        try {
            const draft = JSON.parse(sessionStorage.getItem(KEY) || 'null');
            // Draf (setelah reload kupon/pembayaran) adalah keadaan terbaru, jadi menimpa isian otomatis dari akun
            if (draft) fields.forEach((f) => {
                if (form.elements[f]) form.elements[f].value = draft[f] ?? '';
            });
            if (draft) form.elements.ship_different.checked = !!draft.ship_different;
        } catch (e) {}

        // ===== Kirim ke alamat yang berbeda =====
        const shipChk = form.elements.ship_different;
        const shipCard = document.getElementById('shipDifferentCard');
        const toggleShip = () => {
            shipCard.classList.toggle('hidden', !shipChk.checked);
        };
        shipChk.addEventListener('change', toggleShip);
        toggleShip();

        // ===== Ringkasan pengiriman di Total Pembayaran =====
        const shipMethod = form.elements.shipping_method;
        const shipSummary = document.getElementById('shippingSummary');
        const shipSummaryRow = document.getElementById('shippingSummaryRow');
        const updateShipSummary = () => {
            shipSummary.textContent = shipMethod.value;
            shipSummaryRow.classList.toggle('hidden', !shipMethod.value);
        };
        shipMethod.addEventListener('change', updateShipSummary);
        updateShipSummary();

        // ===== Cek ID Seller =====
        const sellerId = document.getElementById('seller_id');
        const sellerName = document.getElementById('seller_name');
        const sellerMsg = document.getElementById('sellerMsg');
        const sellerBtn = document.getElementById('sellerCheck');
        const adminWrap = document.getElementById('adminHandleWrap');
        const toggleAdmin = (show) => adminWrap.classList.toggle('hidden', !show);
        let sellerTimer;

        async function lookupSeller() {
            const id = sellerId.value.trim();
            if (!id) {
                sellerName.value = '';
                sellerMsg.innerHTML = '';
                toggleAdmin(false);
                return;
            }

            sellerBtn.disabled = true;
            sellerBtn.textContent = 'Cek...';
            try {
                const res = await fetch(`${URLS.seller}?seller_id=${encodeURIComponent(id)}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                });
                const data = await res.json().catch(() => ({}));
                if (id !== sellerId.value.trim()) return; // input sudah berubah, abaikan hasil lama

                if (res.ok && data.found) {
                    sellerName.value = data.name;
                    toggleAdmin(true);
                    sellerMsg.innerHTML = `<p class="text-green-600 text-sm">✓ Seller ditemukan: <strong>${esc(data.name)}</strong></p>`;
                } else {
                    sellerName.value = '';
                    toggleAdmin(false);
                    sellerMsg.innerHTML = `<p class="text-red-500 text-sm">ID Seller tidak ditemukan.</p>`;
                }
            } finally {
                sellerBtn.disabled = false;
                sellerBtn.textContent = 'Cek ID';
            }
        }

        sellerBtn.addEventListener('click', lookupSeller);
        sellerId.addEventListener('blur', lookupSeller);
        sellerId.addEventListener('input', () => {
            clearTimeout(sellerTimer);
            sellerName.value = '';
            toggleAdmin(false);
            sellerMsg.innerHTML = '';
            sellerTimer = setTimeout(lookupSeller, 350);
        });
        sellerId.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                lookupSeller();
            }
        });

        // Isi nama seller saat halaman dimuat ulang (old input / draf)
        if (sellerId.value.trim()) lookupSeller();

        async function applyCode(code) {
            const {
                ok,
                data
            } = await api(URLS.apply, 'POST', {
                code
            });
            if (ok) return reload();
            showErrors(data.errors ? Object.values(data.errors).flat() : (data.message || 'Kupon tidak dapat dipakai.'));
        }

        const input = document.getElementById('coupon_code');
        document.getElementById('couponApply').addEventListener('click', () => {
            const code = input.value.trim();
            code ? applyCode(code) : showErrors('Masukkan kode kupon terlebih dahulu.');
        });
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('couponApply').click();
            }
        });

        document.querySelectorAll('[data-coupon]').forEach((el) => el.addEventListener('click', async () => {
            const c = coupons.find((x) => x.code === el.dataset.coupon);
            if (!c) return;
            if (c.applied) {
                await api(URLS.remove, 'DELETE');
                return reload();
            }
            if (!c.eligible) return showErrors(c.errors);
            applyCode(c.code);
        }));

        document.querySelectorAll('input[name=checkout_payment_method]').forEach((r) => r.addEventListener('change', async () => {
            const {
                ok
            } = await api(URLS.payment, 'POST', {
                method: r.value
            });
            if (ok) reload();
        }));

        // Cegah klik ganda pada "Buat Pesanan"; bersihkan draf setelah berhasil dikirim
        form.addEventListener('submit', () => {
            sessionStorage.removeItem(KEY);
            const b = document.getElementById('submitOrder');
            b.disabled = true;
            b.textContent = 'Memproses...';
        });
    });
</script>
@endsection