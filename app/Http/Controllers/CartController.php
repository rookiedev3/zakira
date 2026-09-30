<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
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

        // Hanya produk aktif & tampil publik yang boleh masuk keranjang
        Product::where('is_active', true)
            ->where('show_public', true)
            ->findOrFail($data['product_id']);

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

        $subtotal = $this->payload()['subtotal'];

        if ($subtotal <= 0) {
            return response()->json(['message' => 'Keranjang masih kosong.'], 422);
        }

        $coupon = $this->findCoupon(trim($data['code']));

        if (! $coupon) {
            return response()->json(['message' => 'Kode kupon tidak ditemukan.'], 422);
        }

        if ($error = $this->couponError($coupon, $subtotal)) {
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

    /* ---------------- Helper ---------------- */

    private function findCoupon(string $code): ?Coupon
    {
        if (! class_exists(Coupon::class)) {
            return null;
        }

        return Coupon::whereRaw('LOWER(code) = ?', [mb_strtolower($code)])->first();
    }

    /** Kembalikan pesan error bila kupon tidak bisa dipakai, atau null bila valid. */
    private function couponError(Coupon $coupon, int $subtotal): ?string
    {
        if (! $coupon->is_active) {
            return 'Kupon tidak aktif.';
        }

        if ($coupon->expires_at && now()->gt($coupon->expires_at)) {
            return 'Kupon sudah kedaluwarsa.';
        }

        if ($coupon->min_purchase && $subtotal < $coupon->min_purchase) {
            return 'Minimal belanja Rp ' . number_format($coupon->min_purchase, 0, ',', '.') . ' untuk memakai kupon ini.';
        }

        return null;
    }

    private function discountFor(Coupon $coupon, int $subtotal): int
    {
        if ($coupon->type === 'percent') {
            $discount = (int) floor($subtotal * $coupon->value / 100);

            if ($coupon->max_discount) {
                $discount = min($discount, (int) $coupon->max_discount);
            }
        } else {
            $discount = (int) $coupon->value;
        }

        return max(0, min($discount, $subtotal));
    }

    private function payload(): array
    {
        $cart = session('cart', []);

        $products = Product::with(['categories', 'models', 'colors', 'sizes', 'prices'])
            ->whereIn('id', collect($cart)->pluck('product_id')->unique())
            ->get()
            ->keyBy('id');

        $rupiah = fn ($n) => 'Rp ' . number_format($n, 0, ',', '.');

        $items    = [];
        $count    = 0;
        $subtotal = 0;

        foreach ($cart as $key => $row) {
            $product = $products->get($row['product_id']);

            // Produk sudah dihapus dari database -> buang dari keranjang
            if (! $product) {
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

            $count    += $row['quantity'];
            $subtotal += $lineTotal;
        }

        session(['cart' => $cart]);

        // Kupon: validasi ulang setiap kali keranjang berubah
        $coupon   = null;
        $discount = 0;

        if ($subtotal <= 0) {
            session()->forget('coupon');
        } elseif ($code = session('coupon')) {
            $model = $this->findCoupon($code);

            if ($model && ! $this->couponError($model, $subtotal)) {
                $discount = $this->discountFor($model, $subtotal);
                $coupon   = [
                    'code'               => $model->code,
                    'discount_formatted' => $rupiah($discount),
                ];
            } else {
                session()->forget('coupon');
            }
        }

        $total = max(0, $subtotal - $discount);

        // Pembayaran: DP (persentase) atau penuh
        $dpPercent = (int) config('cart.dp_percent', 30);
        $dpAmount  = (int) round($total * $dpPercent / 100);

        return [
            'items'              => $items,
            'count'              => $count,
            'subtotal'           => $subtotal,
            'subtotal_formatted' => $rupiah($subtotal),
            'discount'           => $discount,
            'discount_formatted' => $rupiah($discount),
            'coupon'             => $coupon,
            'total_formatted'    => $rupiah($total),
            'payment_method'     => session('payment_method', 'dp'),
            'dp_percent'         => $dpPercent,
            'dp_formatted'       => $rupiah($dpAmount),
            'remaining_formatted'=> $rupiah($total - $dpAmount),
        ];
    }
}