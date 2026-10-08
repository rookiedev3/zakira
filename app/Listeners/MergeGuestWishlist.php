<?php

namespace App\Listeners;

use App\Http\Controllers\WishlistController;
use Illuminate\Auth\Events\Login;

class MergeGuestWishlist
{
    public function handle(Login $event): void
    {
        $ids = session()->pull(WishlistController::SESSION_KEY, []);

        if (!empty($ids)) {
            $event->user->wishlistProducts()->syncWithoutDetaching($ids);
        }
    }
}