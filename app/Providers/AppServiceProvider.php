<?php

namespace App\Providers;

use App\Models\SocialMedia;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
            View::composer('layouts.app', function ($view) {
            $view->with('socialMedia', SocialMedia::query()
                ->where('status', 'aktif')
                ->orderBy('order')
                ->orderByDesc('created_at')
                ->get());
        });
    }
}
