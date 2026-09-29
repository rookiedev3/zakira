{{-- Overlay --}}
<div id="cartOverlay" class="hidden fixed inset-0 bg-black/30 z-[60]"></div>

{{-- Sidebar --}}
<aside id="cartSidebar"
       class="fixed right-0 top-0 h-full w-96 max-w-full bg-white shadow-xl z-[70] transform translate-x-full transition-transform duration-300 flex flex-col">
    <div class="flex items-center justify-between p-4 border-b flex-shrink-0">
        <h2 class="text-lg font-semibold">Keranjang Belanja</h2>
        <button type="button" id="cartClose" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div id="cartItems" class="flex-1 overflow-y-auto p-4 min-h-0"></div>

    <div id="cartFooter" class="hidden border-t p-4 space-y-3 flex-shrink-0 bg-white">
        <div class="flex items-center justify-between font-semibold">
            <span>Total:</span>
            <span id="cartTotal" class="text-lg text-[#B4775E]">Rp 0</span>
        </div>

        <a href="{{ Route::has('cart.page') ? route('cart.page') : '#' }}"
           class="w-full bg-gray-100 text-gray-700 py-3 rounded-lg font-medium hover:bg-gray-200 transition-colors flex items-center justify-center">
            <i class="fas fa-shopping-cart mr-2"></i>Lihat Keranjang
        </a>

        <a href="{{ Route::has('checkout') ? route('checkout') : '#' }}"
           class="w-full bg-[#B4775E] text-white py-3 rounded-lg font-medium hover:bg-[#9C6650] transition-colors flex items-center justify-center">
            Checkout
        </a>
    </div>
</aside>

{{-- Toast --}}
<div id="toastBox" class="hidden fixed top-20 right-4 z-[80]">
    <div id="toastMsg" class="px-6 py-4 rounded-lg shadow-lg text-white transition-all duration-300">
        <div class="flex items-center">
            <i id="toastIcon" class="fas fa-check-circle mr-3"></i>
            <div>
                <div id="toastTitle" class="font-medium"></div>
                <div id="toastText" class="text-sm opacity-90"></div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const csrf = @json(csrf_token());
    const URLS = {
        data:   @json(route('cart.data')),
        items:  @json(url('cart/items')),
    };

    const $ = (id) => document.getElementById(id);
    const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) =>
        ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

    async function api(url, method = 'GET', body = null) {
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
        return res.json();
    }

    function render(data) {
        // Badge angka
        const badge = $('cartBadge');
        if (badge) {
            badge.textContent = data.count > 99 ? '99+' : data.count;
            badge.classList.toggle('hidden', data.count < 1);
            badge.classList.toggle('flex', data.count >= 1);
        }

        // Daftar produk
        const list = $('cartItems');
        if (!data.items.length) {
            list.innerHTML = `
                <div class="h-full flex flex-col items-center justify-center text-gray-400">
                    <i class="fas fa-shopping-cart text-4xl mb-3"></i>
                    <p>Keranjang masih kosong</p>
                </div>`;
        } else {
            list.innerHTML = data.items.map((it) => `
                <div class="flex items-center space-x-3 py-3 border-b">
                    <div class="w-16 h-16 bg-gray-100 rounded overflow-hidden flex-shrink-0">
                        ${it.image
                            ? `<img src="${esc(it.image)}" alt="${esc(it.name)}" class="w-full h-full object-cover">`
                            : `<div class="w-full h-full flex items-center justify-center"><i class="fas fa-image text-gray-400"></i></div>`}
                    </div>
                    <div class="flex-1">
                        <h3 class="font-medium text-sm">${esc(it.name)}</h3>
                        <div class="text-xs text-gray-500 space-y-1">
                            ${it.model ? `<div>Model: ${esc(it.model)}</div>` : ''}
                            ${it.color ? `<div>Warna: ${esc(it.color)}</div>` : ''}
                            ${it.size  ? `<div>Ukuran: ${esc(it.size)}</div>`  : ''}
                        </div>
                        <div class="flex items-center justify-between mt-2">
                            <div class="text-sm font-medium text-[#B4775E]">${esc(it.price_formatted)}</div>
                            <div class="flex items-center space-x-2">
                                <button type="button" data-action="dec" data-key="${esc(it.key)}" data-qty="${it.quantity}"
                                        class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-xs disabled:opacity-50"
                                        ${it.quantity <= 1 ? 'disabled' : ''}>
                                    <i class="fas fa-minus"></i>
                                </button>
                                <span class="text-sm">${it.quantity}</span>
                                <button type="button" data-action="inc" data-key="${esc(it.key)}" data-qty="${it.quantity}"
                                        class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-xs">
                                    <i class="fas fa-plus"></i>
                                </button>
                                <button type="button" data-action="remove" data-key="${esc(it.key)}"
                                        class="w-6 h-6 rounded-full bg-red-200 text-red-600 flex items-center justify-center text-xs ml-2">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>`).join('');
        }

        // Footer total
        $('cartFooter').classList.toggle('hidden', !data.items.length);
        $('cartTotal').textContent = data.total_formatted;
    }

    function openCart() {
        $('cartOverlay').classList.remove('hidden');
        $('cartSidebar').classList.remove('translate-x-full');
        document.body.style.overflow = 'hidden';
    }
    function closeCart() {
        $('cartOverlay').classList.add('hidden');
        $('cartSidebar').classList.add('translate-x-full');
        document.body.style.overflow = '';
    }

    // Toast
    let toastTimer;
    window.showToast = function (title, message, type = 'success') {
        const box = $('toastBox'), msg = $('toastMsg');
        $('toastTitle').textContent = title;
        $('toastText').textContent = message;
        $('toastIcon').className = 'fas mr-3 ' + (type === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle');
        msg.className = 'px-6 py-4 rounded-lg shadow-lg text-white transition-all duration-300 ' +
                        (type === 'error' ? 'bg-red-600' : 'bg-green-600');
        box.classList.remove('hidden');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => box.classList.add('hidden'), 3000);
    };

    // Event
    document.addEventListener('DOMContentLoaded', () => {
        $('cartToggle')?.addEventListener('click', openCart);
        $('cartClose').addEventListener('click', closeCart);
        $('cartOverlay').addEventListener('click', closeCart);
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeCart(); });

        $('cartItems').addEventListener('click', async (e) => {
            const btn = e.target.closest('button[data-action]');
            if (!btn || btn.disabled) return;
            const key = btn.dataset.key;
            const qty = parseInt(btn.dataset.qty || '1', 10);

            if (btn.dataset.action === 'remove') {
                render(await api(`${URLS.items}/${key}`, 'DELETE'));
            } else if (btn.dataset.action === 'inc') {
                render(await api(`${URLS.items}/${key}`, 'PATCH', { quantity: qty + 1 }));
            } else if (btn.dataset.action === 'dec') {
                render(await api(`${URLS.items}/${key}`, 'PATCH', { quantity: Math.max(1, qty - 1) }));
            }
        });

        // Muat isi keranjang saat halaman dibuka
        api(URLS.data).then(render).catch(() => {});
    });

    // Dipakai halaman lain (mis. show.blade.php)
    window.Cart = { render, open: openCart, close: closeCart };
})();
</script>