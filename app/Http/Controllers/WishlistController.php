<?php

namespace App\Http\Controllers;

use App\Models\Product;

class WishlistController extends Controller
{
    public function index()
    {
        $products = auth()->user()
            ->wishlistProducts()
            ->with('brand')
            ->latest('wishlists.created_at')
            ->get();

        return view('wishlist', compact('products'));
    }

    public function toggle(Product $product)
    {
        $result = auth()->user()->wishlistProducts()->toggle($product->id);
        $liked = count($result['attached']) > 0;

        if (request()->expectsJson()) {
            return response()->json(['liked' => $liked]);
        }

        return back()->with(
            'success',
            $liked ? 'Ditambahkan ke wishlist.' : 'Dihapus dari wishlist.'
        );
    }
}