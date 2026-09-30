<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
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

    /**
     * Cari harga sesuai varian yang dipilih.
     * Baris harga dengan size_id/color_id/model_id NULL dianggap berlaku untuk semua.
     * Baris yang paling spesifik (paling banyak kolom cocok) dipilih lebih dulu.
     */
    private function resolvePrice(Product $product, $modelId, $colorId, $sizeId): int
    {
        $match = $product->prices
            ->filter(function ($p) use ($modelId, $colorId, $sizeId) {
                return (is_null($p->size_id)  || $p->size_id  == $sizeId)
                    && (is_null($p->color_id) || $p->color_id == $colorId)
                    && (is_null($p->model_id) || $p->model_id == $modelId);
            })
            ->sortByDesc(fn ($p) =>
                (int) ! is_null($p->size_id) +
                (int) ! is_null($p->color_id) +
                (int) ! is_null($p->model_id)
            )
            ->first();

        // Jika tidak ada yang cocok, pakai harga terendah sebagai cadangan
        return (int) ($match->price ?? $product->prices->min('price') ?? 0);
    }

    private function payload(): array
    {
        $cart = session('cart', []);

        $products = Product::with(['models', 'colors', 'sizes', 'prices'])
            ->whereIn('id', collect($cart)->pluck('product_id')->unique())
            ->get()
            ->keyBy('id');

        $items = [];
        $count = 0;
        $total = 0;

        foreach ($cart as $key => $row) {
            $product = $products->get($row['product_id']);

            // Produk sudah dihapus dari database -> buang dari keranjang
            if (! $product) {
                unset($cart[$key]);
                continue;
            }

            $price = $this->resolvePrice(
                $product,
                $row['model_id'] ?? null,
                $row['color_id'] ?? null,
                $row['size_id'] ?? null
            );

            $items[] = [
                'key'             => $key,
                'name'            => $product->name,
                'image'           => $product->image ? asset('storage/' . $product->image) : null,
                'model'           => optional($product->models->firstWhere('id', $row['model_id']))->name,
                'color'           => optional($product->colors->firstWhere('id', $row['color_id']))->name,
                'size'            => optional($product->sizes->firstWhere('id', $row['size_id']))->size,
                'price_formatted' => 'Rp ' . number_format($price, 0, ',', '.'),
                'quantity'        => $row['quantity'],
            ];

            $count += $row['quantity'];
            $total += $price * $row['quantity'];
        }

        session(['cart' => $cart]);

        return [
            'items'           => $items,
            'count'           => $count,
            'total_formatted' => 'Rp ' . number_format($total, 0, ',', '.'),
        ];
    }
}