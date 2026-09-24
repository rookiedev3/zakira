<?php

use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SocialMediaController;
use App\Http\Controllers\CustomerServiceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'auth']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

    Route::middleware('role:admin')->group(function () {
        // Route::get('/dashboard', function () {
        //     return view('admin.dashboard');
        // })->name('dashboard');

    });
});


/////// ROUTES ZAHWAAA
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::patch('/users/{user}/status', [UserController::class, 'updateStatus'])->name('users.status');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('/banks', [BankAccountController::class, 'index'])->name('banks.index');
    Route::post('/banks', [BankAccountController::class, 'store'])->name('banks.store');
    Route::put('/banks/{bank}', [BankAccountController::class, 'update'])->name('banks.update');
    Route::patch('/banks/{bank}/status', [BankAccountController::class, 'updateStatus'])->name('banks.status');
    Route::delete('/banks/{bank}', [BankAccountController::class, 'destroy'])->name('banks.destroy');


Route::get('/customer-services', [CustomerServiceController::class, 'index'])->name('customer-services.index');
Route::post('/customer-services', [CustomerServiceController::class, 'store'])->name('customer-services.store');
Route::put('/customer-services/{customerService}', [CustomerServiceController::class, 'update'])->name('customer-services.update');
Route::patch('/customer-services/{customerService}/status', [CustomerServiceController::class, 'updateStatus'])->name('customer-services.status');
Route::delete('/customer-services/{customerService}', [CustomerServiceController::class, 'destroy'])->name('customer-services.destroy');


Route::get('/social-media', [SocialMediaController::class, 'index'])->name('social-media.index');
Route::post('/social-media', [SocialMediaController::class, 'store'])->name('social-media.store');
Route::put('/social-media/{socialMedium}', [SocialMediaController::class, 'update'])->name('social-media.update');
Route::patch('/social-media/{socialMedium}/status', [SocialMediaController::class, 'updateStatus'])->name('social-media.status');
Route::delete('/social-media/{socialMedium}', [SocialMediaController::class, 'destroy'])->name('social-media.destroy');

Route::get('/banners', [BannerController::class, 'index'])->name('banners.index');
Route::post('/banners', [BannerController::class, 'store'])->name('banners.store');
Route::put('/banners/{banner}', [BannerController::class, 'update'])->name('banners.update');
Route::patch('/banners/{banner}/status', [BannerController::class, 'updateStatus'])->name('banners.status');
Route::delete('/banners/{banner}', [BannerController::class, 'destroy'])->name('banners.destroy');

    //routes chyntia
    // Route untuk halaman Katalog / Ready Stock
Route::get('/katalog', function () {
    return view('katalog');
});