<?php

namespace App\Http\Controllers;

use App\Models\Advantage;
use App\Models\Banner;
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

        return view('welcome', compact('sliders', 'promos', 'advantages'));
    }
}