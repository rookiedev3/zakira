<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        // Hanya ambil produk yang aktif dan ditampilkan ke publik
        $products = Product::with(['brand', 'categories', 'colors', 'prices'])
            ->withMin('prices', 'price')
            ->withMax('prices', 'price')
            ->where('is_active', true)
            ->where('show_public', true)
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
            })
            ->when(in_array($request->query('type'), ['ready', 'po'], true), function ($q) use ($request) {
                $q->where('product_type', $request->query('type'));
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('catalog.index', [
            'products'        => $products,
            'brandOptions'    => Brand::orderBy('name')->get(['id', 'name']),
            'categoryOptions' => Category::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function show(Product $product)
    {
        // Pastikan hanya produk yang aktif & ditampilkan publik yang dapat diakses
        if (! $product->is_active || ! $product->show_public) {
            abort(404);
        }

        // Muat relasi yang diperlukan untuk tampilan detail
        $product->load(['brand', 'categories', 'colors', 'models', 'sizes', 'prices']);

        // Hitung rentang harga (sama seperti yang di‑index)
        $product->price_range = $product->price_range ?? '-';

        return view('catalog.show', compact('product'));
    }
}