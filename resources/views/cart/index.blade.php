{{-- resources/views/cart/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Keranjang Belanja')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-0 py-8">

    {{-- Header halaman --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Keranjang Belanja</h1>
        <nav class="text-sm text-gray-500 mt-2">
            <a href="{{ url('/') }}" class="hover:text-[#B4775E]">Beranda</a>
            <span class="mx-2">/</span>
            <span class="text-gray-900">Keranjang</span>
        </nav>
    </div>

    {{-- Isi keranjang (digambar oleh JavaScript) --}}
    <div id="cartPage"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const csrf = @json(csrf_token());
    const URLS = {
        items:         @json(url('cart/items')),
        couponApply:   @json(route('cart.coupon.apply')),
        couponRemove:  @json(route('cart.coupon.remove')),
        payment:       @json(route('cart.payment')),
        shop:          @json(url('/katalog')),
        checkout:      @json(Route::has('checkout') ? route('checkout') : '#'),
    };

    let state = @json($cart);
    let couponDraft = '';     // isi input kupon (agar tidak hilang saat tampilan digambar ulang)
    let couponMsg = null;     // { text, type: 'error' | 'success' }

    const root = document.getElementById('cartPage');

    const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) =>
        ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

    async function api(url, method, body = null) {
        const res = await fetch(url, {
            method,
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: body ? JSON.stringify(body) : null,
        });
        const data = await res.json();
        return { ok: res.ok, data };
    }

    function draw() {
        if (!state.items.length) {
            root.innerHTML = `
                <div class="bg-white rounded-lg shadow-sm border p-12 text-center">
                    <i class="fas fa-shopping-cart text-5xl text-gray-300 mb-4"></i>
                    <h2 class="text-xl font-semibold text-gray-800 mb-2">Keranjang masih kosong</h2>
                    <p class="text-gray-500 mb-6">Yuk, pilih produk favorit Anda terlebih dahulu.</p>
                    <a href="${esc(URLS.shop)}"
                       class="inline-block bg-[#B4775E] text-white px-6 py-3 rounded-lg font-medium hover:bg-[#9C6650] transition-colors">
                        Lihat Katalog
                    </a>
                </div>`;
            return;
        }

        const th = 'px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider';

        const rows = state.items.map((it) => `
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-16 w-16">
                            ${it.image
                                ? `<img src="${esc(it.image)}" alt="${esc(it.name)}" class="h-16 w-16 rounded object-cover">`
                                : `<div class="h-16 w-16 rounded bg-gray-100 flex items-center justify-center"><i class="fas fa-image text-gray-400"></i></div>`}
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">${esc(it.name)}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">
                        ${it.categories.length
                            ? it.categories.map((c) => `<span class="inline-block bg-gray-100 px-2 py-1 rounded text-xs mr-1">${esc(c)}</span>`).join('')
                            : '-'}
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900 space-y-1">
                        ${it.model ? `<div><span class="font-medium">Model:</span> ${esc(it.model)}</div>` : ''}
                        ${it.color ? `<div><span class="font-medium">Warna:</span> ${esc(it.color)}</div>` : ''}
                        ${it.size  ? `<div><span class="font-medium">Ukuran:</span> ${esc(it.size)}</div>`  : ''}
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">${esc(it.price_formatted)}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center space-x-2">
                        <button type="button" data-action="dec" data-key="${esc(it.key)}" data-qty="${it.quantity}"
                                class="w-8 h-8 rounded border border-gray-300 flex items-center justify-center hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                ${it.quantity <= 1 ? 'disabled' : ''}>
                            <i class="fas fa-minus text-xs"></i>
                        </button>
                        <span class="text-sm font-medium min-w-[2rem] text-center">${it.quantity}</span>
                        <button type="button" data-action="inc" data-key="${esc(it.key)}" data-qty="${it.quantity}"
                                class="w-8 h-8 rounded border border-gray-300 flex items-center justify-center hover:bg-gray-50">
                            <i class="fas fa-plus text-xs"></i>
                        </button>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-[#B4775E]">${esc(it.subtotal_formatted)}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <button type="button" data-action="remove" data-key="${esc(it.key)}"
                            class="text-red-600 hover:text-red-800 text-sm">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </td>
            </tr>`).join('');

        // ----- Kupon (tampilan mengikuti halaman referensi Zakira) -----
        const msgs = couponMsg ? [].concat(couponMsg.text) : [];
        const msgColor = couponMsg && couponMsg.type === 'success' ? 'text-green-600' : 'text-red-500';
        const couponAlert = msgs.length
            ? `<div class="mt-2">${msgs.map((m) => `<p class="${msgColor} text-sm">${esc(m)}</p>`).join('')}</div>`
            : '';

        // Input kode kupon manual
        const couponInput = `
            <div class="mb-6">
                <label for="couponCode" class="block text-sm font-medium text-gray-700 mb-2">Kode Kupon</label>
                <div class="flex space-x-2">
                    <input type="text" id="couponCode" value="${esc(couponDraft)}" placeholder="Masukkan kode kupon"
                           class="flex-1 border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#B4775E] focus:border-[#B4775E]">
                    <button type="button" data-action="coupon-apply"
                            class="bg-[#B4775E] text-white px-4 h-10 rounded-lg text-sm font-medium hover:bg-[#9C6650] transition-colors whitespace-nowrap">
                        Terapkan
                    </button>
                </div>
                ${couponAlert}
            </div>`;

        // Kupon Tersedia
        const couponCards = (state.available_coupons || []).map((c) => `
            <div data-action="coupon-pick" data-code="${esc(c.code)}" data-applied="${c.applied ? 1 : 0}"
                 class="border rounded-lg p-4 cursor-pointer transition-all ${c.applied ? 'border-[#B4775E] bg-[#B4775E]/5 ring-1 ring-[#B4775E]' : 'border-gray-200 hover:border-[#B4775E]'}">
                <div class="flex items-center justify-between mb-2">
                    <h5 class="font-medium text-gray-900">${esc(c.title)}</h5>
                    <input type="radio" name="coupon" value="${esc(c.code)}" class="text-[#B4775E]" ${c.applied ? 'checked' : ''}>
                </div>
                <div class="mb-2">
                    <span class="inline-block border border-dashed border-[#B4775E] text-[#B4775E] rounded px-2 py-0.5 text-xs font-semibold tracking-wide">${esc(c.code)}</span>
                </div>
                ${c.description ? `<p class="text-sm text-gray-600 mb-2">${esc(c.description)}</p>` : ''}
                <div class="text-sm">
                    <span class="font-medium text-[#B4775E]">
                        ${esc(c.discount_label.replace(' OFF', ' Off'))}${c.max_discount_formatted ? ` (Max ${esc(c.max_discount_formatted)})` : ''}
                    </span>
                    ${c.min_amount_formatted ? `<div class="text-xs text-gray-500 mt-1">Min. pembelian: ${esc(c.min_amount_formatted)}</div>` : ''}
                </div>
                ${c.expires_at ? `<div class="text-xs text-gray-500 mt-1">Berlaku sampai: ${esc(c.expires_at)}</div>` : ''}
            </div>`).join('');

        const couponBlock = couponInput + (couponCards
            ? `<div>
                    <h4 class="text-md font-medium text-gray-800 mb-3">Kupon Tersedia</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">${couponCards}</div>
               </div>`
            : '');

        const isDp = state.payment_method === 'dp';

        root.innerHTML = `
            <div class="bg-white rounded-lg shadow-sm border overflow-hidden mb-8">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="${th}">Produk</th>
                                <th class="${th}">Kategori</th>
                                <th class="${th}">Varian</th>
                                <th class="${th}">Harga</th>
                                <th class="${th}">Jumlah</th>
                                <th class="${th}">Subtotal</th>
                                <th class="${th}">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">${rows}</tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border p-6 mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Kupon Diskon</h3>
                ${couponBlock}
            </div>

            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-6">Total Keranjang Belanja</h3>

                <div class="space-y-4 mb-6">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span>${esc(state.subtotal_formatted)}</span>
                    </div>
                    ${state.coupon ? `
                    <div class="flex justify-between text-green-600">
                        <span>Diskon (${esc(state.coupon.code)})</span>
                        <span>- ${esc(state.discount_formatted)}</span>
                    </div>` : ''}
                    <hr>
                    <div class="flex justify-between text-lg font-semibold text-gray-900">
                        <span>Total</span>
                        <span class="text-[#B4775E]">${esc(state.total_formatted)}</span>
                    </div>

                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 space-y-3">
                        <h4 class="font-medium text-gray-800 text-sm">Pilih Metode Pembayaran</h4>

                        <div class="flex items-start space-x-3">
                            <input type="radio" id="payment_dp" name="payment_method" value="dp"
                                   class="mt-1 text-[#B4775E] focus:ring-[#B4775E]" ${isDp ? 'checked' : ''}>
                            <label for="payment_dp" class="flex-1 cursor-pointer">
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-gray-900">Down Payment (DP)</span>
                                    <span class="text-sm font-bold text-blue-600">${esc(state.dp_formatted)}</span>
                                </div>
                                <p class="text-xs text-gray-600 mt-1">
                                    Bayar ${state.dp_percent}% sekarang, sisanya kapan saja.
                                </p>
                                <div class="mt-2 p-2 bg-blue-50 rounded text-xs text-blue-700">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Sisa pembayaran: ${esc(state.remaining_formatted)}
                                </div>
                            </label>
                        </div>

                        <div class="flex items-start space-x-3">
                            <input type="radio" id="payment_full" name="payment_method" value="full"
                                   class="mt-1 text-[#B4775E] focus:ring-[#B4775E]" ${!isDp ? 'checked' : ''}>
                            <label for="payment_full" class="flex-1 cursor-pointer">
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-gray-900">Pembayaran Penuh</span>
                                    <span class="text-sm font-bold text-green-600">${esc(state.total_formatted)}</span>
                                </div>
                                <p class="text-xs text-gray-600 mt-1">Bayar seluruh jumlah sekarang juga</p>
                            </label>
                        </div>
                    </div>
                </div>

                <a href="${esc(URLS.checkout)}"
                   class="w-full inline-flex items-center justify-center bg-[#B4775E] text-white px-6 py-3 rounded-lg font-medium hover:bg-[#9C6650] transition-colors">
                    Lanjutkan ke Checkout
                </a>
            </div>`;
    }

    // Terapkan data terbaru ke halaman + badge + sidebar
    function apply(data) {
        state = data;
        if (data.coupon_notice) couponMsg = { text: data.coupon_notice, type: 'error' };
        draw();
        window.Cart?.render(data);
    }

    async function applyCode(code) {
        const { ok, data } = await api(URLS.couponApply, 'POST', { code });
        if (ok) {
            couponDraft = '';
            couponMsg = null;
            apply(data);
        } else {
            const text = data.errors ? Object.values(data.errors).flat().join(' ') : (data.message || 'Kupon tidak dapat dipakai.');
            couponMsg = { text, type: 'error' };
            draw();
        }
    }

    // Simpan isi input kupon selagi diketik
    root.addEventListener('input', (e) => {
        if (e.target.id === 'couponCode') couponDraft = e.target.value;
    });

    // Pilih metode pembayaran
    root.addEventListener('change', async (e) => {
        if (e.target.name !== 'payment_method') return;
        const { ok, data } = await api(URLS.payment, 'POST', { method: e.target.value });
        if (ok) apply(data);
    });

    // Tombol-tombol
    root.addEventListener('click', async (e) => {
        const btn = e.target.closest('[data-action]');
        if (!btn || btn.disabled) return;

        const action = btn.dataset.action;
        const key = btn.dataset.key;
        const qty = parseInt(btn.dataset.qty || '1', 10);

        if (action === 'remove') {
            if (!confirm('Hapus produk dari keranjang?')) return;
            couponMsg = null;
            apply((await api(`${URLS.items}/${key}`, 'DELETE')).data);

        } else if (action === 'inc') {
            couponMsg = null;
            apply((await api(`${URLS.items}/${key}`, 'PATCH', { quantity: qty + 1 })).data);

        } else if (action === 'dec') {
            couponMsg = null;
            apply((await api(`${URLS.items}/${key}`, 'PATCH', { quantity: Math.max(1, qty - 1) })).data);

        } else if (action === 'coupon-apply') {
            const code = couponDraft.trim();
            if (!code) {
                couponMsg = { text: 'Masukkan kode kupon terlebih dahulu.', type: 'error' };
                return draw();
            }
            applyCode(code);

        } else if (action === 'coupon-pick') {
            // Klik kartu: pakai kupon, atau lepas bila sedang dipakai
            if (btn.dataset.applied === '1') {
                couponMsg = null;
                apply((await api(URLS.couponRemove, 'DELETE')).data);
            } else {
                const c = (state.available_coupons || []).find((x) => x.code === btn.dataset.code);
                if (c && !c.eligible) {
                    couponMsg = { text: c.errors, type: 'error' };
                    return draw();
                }
                applyCode(btn.dataset.code);
            }

        } else if (action === 'coupon-remove') {
            couponMsg = null;
            apply((await api(URLS.couponRemove, 'DELETE')).data);
        }
    });

    draw();
});
</script>
@endsection