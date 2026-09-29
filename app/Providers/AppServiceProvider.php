<?php

namespace App\Providers;

use App\Models\CustomerService;
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
            $socialMedia = SocialMedia::query()
                ->where('status', 'aktif')
                ->orderBy('order')
                ->orderByDesc('created_at')
                ->get();

            $customerServices = CustomerService::query()
                ->where('status', 'aktif')
                ->orderBy('order')
                ->orderBy('created_at') // asc, sesuai permintaan (beda dari social media)
                ->get();

            $view->with([
                'socialMedia' => $socialMedia,
                'floatingWhatsapp' => $customerServices->firstWhere('is_floating_whatsapp', true),
                'footerContacts' => $customerServices->reject->is_floating_whatsapp,
            ]);
        });
    }
}
