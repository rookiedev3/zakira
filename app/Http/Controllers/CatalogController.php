<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    /** Nilai sort yang diizinkan (sama dengan opsi dropdown di view). */
    private const SORTS = ['terbaru', 'termahal', 'termurah'];

    public function index(Request $request, string $defaultType = 'ready')
    {
        $canSeePo = $this->canSeePo($request);

        // Tipe diminta dari ?type=..., kalau kosong pakai default route.
        // PO hanya boleh untuk yang berhak, selain itu jatuh ke Ready Stock.
        $requested   = $request->query('type', $defaultType);
        $currentType = ($canSeePo && $requested === 'po') ? 'po' : 'ready';

        // Hanya ambil produk yang aktif.
        // Ready Stock dicek lewat show_public, PO dicek lewat show_member.
        $visibleColumn = $currentType === 'po' ? 'show_member' : 'show_public';

        // Urutan: nilai di luar daftar dianggap 'terbaru'
        $sort = in_array($request->query('sort'), self::SORTS, true)
            ? $request->query('sort')
            : 'terbaru';

        $query = Product::with(['brand', 'categories', 'colors', 'prices'])
            ->withMin('prices', 'price')
            ->withMax('prices', 'price')
            ->where('is_active', true)
            ->where($visibleColumn, true)
            ->where('product_type', $currentType)
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->query('search') . '%');
            })
            ->when($request->filled('brand'), function ($q) use ($request) {
                $q->where('brand_id', $request->query('brand'));
            })
            ->when($request->filled('category'), function ($q) use ($request) {
                $q->whereHas('categories', function ($c) use ($request) {
                    $c->where('categories.id', $request->query('category'));
                });
            });

        // Produk tanpa harga selalu ditaruh paling bawah; id dipakai sebagai
        // pembeda terakhir supaya urutan antar halaman tidak tertukar.
        match ($sort) {
            'termahal' => $query->orderByRaw('prices_max_price IS NULL')
                                ->orderByDesc('prices_max_price')
                                ->orderByDesc('id'),
            'termurah' => $query->orderByRaw('prices_min_price IS NULL')
                                ->orderBy('prices_min_price')
                                ->orderByDesc('id'),
            default    => $query->latest()->orderByDesc('id'),
        };

        $products = $query->paginate(12)->withQueryString();

        return view('catalog.index', [
            'products'        => $products,
            'brandOptions'    => Brand::orderBy('name')->get(['id', 'name']),
            'categoryOptions' => Category::orderBy('name')->get(['id', 'name']),
            'canSeePo'        => $canSeePo,
            'currentType'     => $currentType,
        ]);
    }

    public function show(Request $request, Product $product)
    {
        // Ready Stock dicek lewat show_public, PO dicek lewat show_member
        $visibleColumn = $product->product_type === 'po' ? 'show_member' : 'show_public';

        if (! $product->is_active || ! $product->{$visibleColumn}) {
            abort(404);
        }

        // Produk PO hanya boleh dibuka oleh yang berhak
        if ($product->product_type === 'po' && ! $this->canSeePo($request)) {
            abort(404);
        }

        $product->load(['brand', 'categories', 'colors', 'models', 'sizes', 'prices']);

        $product->price_range = $product->price_range ?? '-';

        // Peta harga: "modelId|colorId|sizeId" => harga
        $models = $product->models->isNotEmpty() ? $product->models->pluck('id') : collect([null]);
        $colors = $product->colors->isNotEmpty() ? $product->colors->pluck('id') : collect([null]);
        $sizes  = $product->sizes->isNotEmpty()  ? $product->sizes->pluck('id')  : collect([null]);

        $priceMap = [];
        foreach ($models as $m) {
            foreach ($colors as $c) {
                foreach ($sizes as $s) {
                    $priceMap[($m ?? 0) . '|' . ($c ?? 0) . '|' . ($s ?? 0)] = $product->priceFor($m, $c, $s);
                }
            }
        }

        return view('catalog.show', compact('product', 'priceMap'));
    }

    /**
     * PO hanya untuk user yang login dengan role 'customer' atau 'admin'.
     * (Tamu yang belum login hanya melihat Ready Stock.)
     */
    private function canSeePo(Request $request): bool
    {
        $user = $request->user();

        return $user !== null && in_array($user->role, ['customer', 'admin'], true);
    }
}