<?php

namespace App\Http\Controllers;

use App\Models\Advantage;
use App\Models\Banner;
use App\Models\Product;
use App\Models\Brand;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function index(): View
    {
        $sliders = Banner::where('type', 'slider')
            ->where('status', 'aktif')
            ->orderBy('order')
            ->orderByDesc('created_at')
            ->get();

        $promos = Banner::where('type', 'promo')
            ->where('status', 'aktif')
            ->orderBy('order')
            ->orderByDesc('created_at')
            ->take(4)
            ->get();

        $advantages = Advantage::where('status', 'aktif')
            ->orderBy('order')
            ->orderByDesc('created_at')
            ->get();

        // Koleksi Pilihan: hanya Ready Stock (PO khusus member), 8 terbaru
        $products = Product::where('product_type', 'ready')
            ->where('is_active', true)
            ->where('show_public', true)
            ->latest()
            ->take(8)
            ->get();

        $brands = Brand::home()->get();

        return view('welcome', compact('sliders', 'promos', 'advantages', 'products', 'brands'));
    }
}
