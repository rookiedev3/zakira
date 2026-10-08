<?php

namespace App\Http\Controllers;

use App\Models\Product;

class WishlistController extends Controller
{
    public const SESSION_KEY = 'guest_wishlist';

    public function index()
    {
        if (auth()->check()) {
            $products = auth()->user()
                ->wishlistProducts()
                ->with('brand')
                ->latest('wishlists.created_at')
                ->get();
        } else {
            $ids = session(self::SESSION_KEY, []);

            $products = empty($ids)
                ? collect()
                : Product::with('brand')
                    ->whereIn('id', $ids)
                    ->get()
                    ->sortByDesc(fn ($p) => array_search($p->id, $ids)) // terbaru di atas
                    ->values();
        }

        return view('wishlist', compact('products'));
    }

    public function toggle(Product $product)
    {
        if (auth()->check()) {
            $result = auth()->user()->wishlistProducts()->toggle($product->id);
            $liked  = count($result['attached']) > 0;
        } else {
            $ids = session(self::SESSION_KEY, []);

            if (in_array($product->id, $ids)) {
                $ids   = array_values(array_diff($ids, [$product->id]));
                $liked = false;
            } else {
                $ids[] = $product->id;
                $liked = true;
            }

            session([self::SESSION_KEY => $ids]);
        }

        if (request()->expectsJson()) {
            return response()->json(['liked' => $liked]);
        }

        return back()->with(
            'success',
            $liked ? 'Ditambahkan ke wishlist.' : 'Dihapus dari wishlist.'
        );
    }
}