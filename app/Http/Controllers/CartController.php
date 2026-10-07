<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\CustomerOrder;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /** Halaman Keranjang Belanja */
    public function page()
    {
        return view('cart.index', ['cart' => $this->payload()]);
    }

    public function index()
    {
        return response()->json($this->payload());
    }

    public function add(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|integer',
            'model_id'   => 'nullable|integer',
            'color_id'   => 'nullable|integer',
            'size_id'    => 'nullable|integer',
            'quantity'   => 'required|integer|min:1|max:999',
        ]);

        $product = Product::where('is_active', true)->findOrFail($data['product_id']);

        // Ready Stock dicek lewat show_public, PO dicek lewat show_member
        $visibleColumn = $product->product_type === 'po' ? 'show_member' : 'show_public';
        abort_unless($product->{$visibleColumn}, 404);

        // Produk PO hanya untuk customer / admin yang sudah login
        if ($product->product_type === 'po' && ! $this->canSeePo()) {
            abort(403, 'Produk PO khusus akun customer dan admin yang sudah login.');
        }

        // Pastikan model / warna / ukuran yang dikirim memang milik produk ini
        $variants = [
            'model_id' => 'models',
            'color_id' => 'colors',
            'size_id'  => 'sizes',
        ];

        foreach ($variants as $field => $relation) {
            if (! empty($data[$field]) && ! $product->{$relation}()->whereKey($data[$field])->exists()) {
                return response()->json(['message' => 'Pilihan varian tidak valid untuk produk ini.'], 422);
            }
        }

        $cart = session('cart', []);
        $key  = md5(implode('|', [
            $data['product_id'],
            $data['model_id'] ?? 0,
            $data['color_id'] ?? 0,
            $data['size_id'] ?? 0,
        ]));

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] = min(999, $cart[$key]['quantity'] + $data['quantity']);
        } else {
            $cart[$key] = [
                'product_id' => (int) $data['product_id'],
                'model_id'   => $data['model_id'] ?? null,
                'color_id'   => $data['color_id'] ?? null,
                'size_id'    => $data['size_id'] ?? null,
                'quantity'   => (int) $data['quantity'],
            ];
        }

        session(['cart' => $cart]);

        return response()->json($this->payload() + [
            'message' => 'Produk berhasil ditambahkan ke keranjang',
        ]);
    }

    public function update(Request $request, string $key)
    {
        $data = $request->validate([
            'quantity' => 'required|integer|min:1|max:999',
        ]);

        $cart = session('cart', []);
        if (isset($cart[$key])) {
            $cart[$key]['quantity'] = $data['quantity'];
            session(['cart' => $cart]);
        }

        return response()->json($this->payload());
    }

    public function remove(string $key)
    {
        $cart = session('cart', []);
        unset($cart[$key]);
        session(['cart' => $cart]);

        return response()->json($this->payload());
    }

    /* ---------------- Kupon ---------------- */

    public function applyCoupon(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:50',
        ]);

        $summary = $this->summary();

        if ($summary['subtotal'] <= 0) {
            return response()->json(['message' => 'Keranjang masih kosong.'], 422);
        }

        $coupon = $this->findCoupon(trim($data['code']));

        if (! $coupon) {
            return response()->json(['message' => 'Kode kupon tidak ditemukan.'], 422);
        }

        if ($error = $this->couponError($coupon, $summary)) {
            return response()->json(['message' => $error], 422);
        }

        session(['coupon' => $coupon->code]);

        return response()->json($this->payload() + [
            'message' => 'Kupon berhasil diterapkan.',
        ]);
    }

    public function removeCoupon()
    {
        session()->forget('coupon');

        return response()->json($this->payload());
    }

    /* ---------------- Metode pembayaran ---------------- */

    public function selectPayment(Request $request)
    {
        $data = $request->validate([
            'method' => 'required|in:dp,full',
        ]);

        session(['payment_method' => $data['method']]);

        return response()->json($this->payload());
    }

    /* ================================================================
     |  Helper Akses PO
     ================================================================ */

    /**
     * PO hanya untuk user yang login dengan role 'customer' atau 'admin'
     * (aturan yang sama dengan CatalogController::canSeePo).
     */
    private function canSeePo(): bool
    {
        $user = auth()->user();

        return $user !== null && in_array($user->role, ['customer', 'admin'], true);
    }

    /* ================================================================
     |  Helper Kupon
     ================================================================ */

    private function findCoupon(string $code): ?Coupon
    {
        return Coupon::with(['categories', 'brands', 'products'])
            ->whereRaw('LOWER(code) = ?', [mb_strtolower($code)])
            ->first();
    }

    /** Pesan error pertama, atau null bila kupon valid. */
    private function couponError(Coupon $coupon, array $summary): ?string
    {
        return $this->couponErrors($coupon, $summary)[0] ?? null;
    }

    /**
     * Semua alasan kupon belum bisa dipakai (array kosong = valid).
     * Dipakai untuk validasi saat diterapkan dan untuk daftar "Kupon Tersedia".
     *
     * @param array $summary hasil $this->summary()
     * @return string[]
     */
    private function couponErrors(Coupon $coupon, array $summary): array
    {
        // 1. Status (active / upcoming / inactive / expired / used_up) dari accessor model
        switch ($coupon->status) {
            case 'inactive':
                return ['Kupon tidak aktif.'];
            case 'upcoming':
                return ['Kupon belum bisa digunakan. Berlaku mulai '
                    . $coupon->starts_at->translatedFormat('d F Y H:i') . '.'];
            case 'expired':
                return ['Kupon sudah kedaluwarsa.'];
            case 'used_up':
                return ['Kuota penggunaan kupon sudah habis.'];
        }

        $errors = [];

        // 2. Minimal belanja & minimal jumlah barang
        $minAmount = (int) $coupon->minimum_amount;
        if ($minAmount > 0 && $summary['subtotal'] < $minAmount) {
            $errors[] = 'Minimal belanja Rp ' . number_format($minAmount, 0, ',', '.') . ' untuk memakai kupon ini.';
        }

        $minQty = (int) $coupon->minimum_quantity;
        if ($minQty > 0 && $summary['count'] < $minQty) {
            $errors[] = 'Minimal pembelian ' . $minQty . ' barang untuk memakai kupon ini.';
        }

        // 3. Target pelanggan (semua / member / non_member)
        // Member = pembeli yang sedang login; non member = pembeli tanpa login
        if ($coupon->customer_scope === 'member' && ! $this->isMember()) {
            $errors[] = 'Kupon ini khusus member. Silakan login terlebih dahulu.';
        }

        if ($coupon->customer_scope === 'non_member' && $this->isMember()) {
            $errors[] = 'Kupon ini hanya untuk pembeli tanpa login.';
        }

        // 4. Khusus pelanggan baru
        if ($coupon->new_customers_only && ! $this->isNewCustomer()) {
            $errors[] = 'Kupon ini hanya untuk pelanggan baru.';
        }

        // 5. Batas pemakaian per pelanggan
        if ($coupon->usage_limit_per_customer
            && $this->customerUsageCount($coupon) >= $coupon->usage_limit_per_customer) {
            $errors[] = 'Anda sudah mencapai batas pemakaian kupon ini.';
        }

        // 6. Batasan produk / kategori / brand
        if ($this->eligibleSubtotal($coupon, $summary['lines']) <= 0) {
            $errors[] = 'Kupon tidak berlaku untuk produk di keranjang Anda.';
        }

        return $errors;
    }

    /**
     * Daftar kupon yang ditampilkan di keranjang ("Kupon Tersedia").
     * Hanya kupon aktif, belum kedaluwarsa, kuota masih ada, dan show_in_checkout = true.
     * Kupon yang belum memenuhi syarat tetap dikirim beserta alasannya (view checkout menyaringnya).
     */
    private function availableCoupons(array $summary, ?string $appliedCode): array
    {
        if ($summary['subtotal'] <= 0) {
            return [];
        }

        $rupiah  = fn ($n) => 'Rp ' . number_format($n, 0, ',', '.');
        $applied = $appliedCode ? mb_strtolower($appliedCode) : null;

        return Coupon::with(['categories', 'brands', 'products'])
            ->where('active', true)
            ->where('show_in_checkout', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()->addMinute()))
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->where(fn ($q) => $q->whereNull('usage_limit')
                ->orWhere('usage_limit', 0)
                ->orWhereColumn('used_count', '<', 'usage_limit'))
            ->latest('id')
            ->get()
            ->map(function (Coupon $c) use ($summary, $rupiah, $applied) {
                $errors   = $this->couponErrors($c, $summary);
                $eligible = empty($errors);

                return [
                    'code'                     => $c->code,
                    'title'                    => $c->checkout_label ?: $c->name,
                    'description'              => $c->checkout_description ?: $c->description,
                    'discount_label'           => $c->discount_label . ' OFF',
                    'max_discount_formatted'   => ($c->type === 'percentage' && $c->max_discount_amount)
                        ? $rupiah((int) $c->max_discount_amount) : null,
                    'min_amount_formatted'     => (int) $c->minimum_amount > 0
                        ? $rupiah((int) $c->minimum_amount) : null,
                    'min_quantity'             => (int) $c->minimum_quantity ?: null,
                    'expires_at'               => optional($c->expires_at)->translatedFormat('d F Y'),
                    'audience'                 => $c->customer_scope !== 'all' ? $c->audience_label : null,
                    'eligible'                 => $eligible,
                    'errors'                   => $errors,
                    'saving_formatted'         => $eligible ? $rupiah($this->discountFor($c, $summary)) : null,
                    'applied'                  => $applied === mb_strtolower($c->code),
                ];
            })
            ->sortBy(fn ($c) => $c['applied'] ? 0 : ($c['eligible'] ? 1 : 2))
            ->values()
            ->all();
    }

    private function discountFor(Coupon $coupon, array $summary): int
    {
        // Diskon hanya dihitung dari barang yang memenuhi syarat kupon
        $base = $this->eligibleSubtotal($coupon, $summary['lines']);

        if ($coupon->type === 'percentage') {
            $discount = (int) floor($base * (float) $coupon->value / 100);

            if ($coupon->max_discount_amount) {
                $discount = min($discount, (int) $coupon->max_discount_amount);
            }
        } else { // fixed
            $discount = (int) $coupon->value;
        }

        return max(0, min($discount, $base));
    }

    /**
     * Total harga barang di keranjang yang memenuhi batasan kupon.
     * - restriction_type = null / tidak ada relasi dipilih -> semua barang
     * - 'only'   -> hanya produk/kategori/brand yang dipilih
     * - 'except' -> semua kecuali produk/kategori/brand yang dipilih
     */
    private function eligibleSubtotal(Coupon $coupon, array $lines): int
    {
        $productIds  = $coupon->products->pluck('id')->all();
        $categoryIds = $coupon->categories->pluck('id')->all();
        $brandIds    = $coupon->brands->pluck('id')->all();

        $hasRestriction = $coupon->restriction_type
            && ($productIds || $categoryIds || $brandIds);

        $total = 0;

        foreach ($lines as $line) {
            if (! $hasRestriction) {
                $total += $line['total'];
                continue;
            }

            $matches = in_array($line['product_id'], $productIds, true)
                || ($line['brand_id'] && in_array($line['brand_id'], $brandIds))
                || count(array_intersect($line['category_ids'], $categoryIds)) > 0;

            if (($coupon->restriction_type === 'only' && $matches)
                || ($coupon->restriction_type === 'except' && ! $matches)) {
                $total += $line['total'];
            }
        }

        return $total;
    }

    /* ---- Identitas pelanggan (dipakai aturan kupon) ---- */

    /** Member = pembeli yang sedang login. */
    private function isMember(): bool
    {
        return auth()->check();
    }

    /**
     * No. WhatsApp pembeli: dari form checkout (bila request membawanya),
     * atau dari pesanan terakhir di sesi ini (untuk tamu).
     */
    private function customerPhone(): ?string
    {
        return request()->input('whatsapp_number') ?: session('customer_phone');
    }

    /** Pelanggan baru = belum pernah punya pesanan (yang tidak dibatalkan). */
    private function isNewCustomer(): bool
    {
        return ! CustomerOrder::forCustomer(auth()->user(), $this->customerPhone())
            ->where('status', '!=', 'cancelled')
            ->exists();
    }

    /** Berapa kali pelanggan ini sudah memakai kupon (pesanan dibatalkan tidak dihitung). */
    private function customerUsageCount(Coupon $coupon): int
    {
        return CustomerOrder::forCustomer(auth()->user(), $this->customerPhone())
            ->where('coupon_code', $coupon->code)
            ->where('status', '!=', 'cancelled')
            ->count();
    }

    /* ================================================================
     |  Ringkasan & Payload Keranjang
     ================================================================ */

    /** Hitung isi keranjang (tanpa kupon). */
    public function summary(): array
    {
        $cart = session('cart', []);

        $products = Product::with(['categories', 'models', 'colors', 'sizes', 'prices'])
            ->whereIn('id', collect($cart)->pluck('product_id')->unique())
            ->get()
            ->keyBy('id');

        $rupiah = fn ($n) => 'Rp ' . number_format($n, 0, ',', '.');

        $canSeePo = $this->canSeePo();

        $items    = [];
        $lines    = [];
        $count    = 0;
        $subtotal = 0;

        foreach ($cart as $key => $row) {
            $product = $products->get($row['product_id']);

            // Produk sudah dihapus dari database -> buang dari keranjang
            if (! $product) {
                unset($cart[$key]);
                continue;
            }

            // Produk PO hanya boleh ada di keranjang milik customer / admin
            // (mis. setelah logout, item PO otomatis dibuang)
            if ($product->product_type === 'po' && ! $canSeePo) {
                unset($cart[$key]);
                continue;
            }

            $price = $product->priceFor(
                $row['model_id'] ?? null,
                $row['color_id'] ?? null,
                $row['size_id'] ?? null
            );
            $lineTotal = $price * $row['quantity'];

            $items[] = [
                'key'                => $key,
                'product_id'         => (int) $product->id,
                'unit_price'         => (int) $price,
                'line_total'         => (int) $lineTotal,
                'name'               => $product->name,
                'image'              => $product->image ? asset('storage/' . $product->image) : null,
                'categories'         => $product->categories->pluck('name')->values()->all(),
                'model'              => optional($product->models->firstWhere('id', $row['model_id']))->name,
                'color'              => optional($product->colors->firstWhere('id', $row['color_id']))->name,
                'size'               => optional($product->sizes->firstWhere('id', $row['size_id']))->size,
                'price_formatted'    => $rupiah($price),
                'subtotal_formatted' => $rupiah($lineTotal),
                'quantity'           => $row['quantity'],
            ];

            // Data mentah per baris, dipakai untuk validasi & hitung kupon
            $lines[] = [
                'product_id'   => (int) $product->id,
                'category_ids' => $product->categories->pluck('id')->all(),
                'brand_id'     => $product->brand_id,
                'quantity'     => (int) $row['quantity'],
                'total'        => (int) $lineTotal,
            ];

            $count    += $row['quantity'];
            $subtotal += $lineTotal;
        }

        session(['cart' => $cart]);

        return [
            'items'    => $items,
            'lines'    => $lines,
            'count'    => $count,
            'subtotal' => (int) $subtotal,
        ];
    }

    public function payload(): array
    {
        $summary  = $this->summary();
        $subtotal = $summary['subtotal'];
        $rupiah   = fn ($n) => 'Rp ' . number_format($n, 0, ',', '.');

        // Kupon: validasi ulang setiap kali keranjang berubah
        $coupon   = null;
        $discount = 0;
        $notice   = null;

        if ($subtotal <= 0) {
            session()->forget('coupon');
        } elseif ($code = session('coupon')) {
            $model = $this->findCoupon($code);

            if ($model && ! $this->couponError($model, $summary)) {
                $discount = $this->discountFor($model, $summary);
                $coupon   = [
                    'code'               => $model->code,
                    'name'               => $model->name,
                    'label'              => $model->discount_label,
                    'discount_formatted' => $rupiah($discount),
                ];
            } else {
                // Kupon tidak lagi memenuhi syarat (mis. jumlah barang dikurangi) -> lepas + beri tahu
                $reason = $model ? $this->couponError($model, $summary) : 'Kupon sudah tidak tersedia.';
                $notice = 'Kupon ' . $code . ' dilepas. ' . $reason;
                session()->forget('coupon');
            }
        }

        $total = max(0, $subtotal - $discount);

        // Pembayaran: DP (persentase) atau penuh
        $dpPercent = (int) config('cart.dp_percent', 30);
        $dpAmount  = (int) round($total * $dpPercent / 100);

        return [
            'items'               => $summary['items'],
            'count'               => $summary['count'],
            'subtotal'            => $subtotal,
            'subtotal_formatted'  => $rupiah($subtotal),
            'discount'            => $discount,
            'discount_formatted'  => $rupiah($discount),
            'coupon'              => $coupon,
            'coupon_notice'       => $notice,
            'available_coupons'   => $this->availableCoupons($summary, $coupon['code'] ?? null),
            'total'               => (int) $total,
            'total_formatted'     => $rupiah($total),
            'payment_method'      => session('payment_method', 'dp'),
            'dp_percent'          => $dpPercent,
            'dp_amount'           => $dpAmount,
            'dp_formatted'        => $rupiah($dpAmount),
            'remaining_formatted' => $rupiah($total - $dpAmount),
        ];
    }
}