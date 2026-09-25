<?php

use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminHandleController;
use App\Http\Controllers\AdvantageController;
use App\Http\Controllers\SocialMediaController;
use App\Http\Controllers\CustomerServiceController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'auth']);

        Route::get('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');

    Route::get('/reset-password/{token}', [AuthController::class, 'resetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'updatePassword'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/logout', [AuthController::class, 'destroy'])->name('logout');


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

Route::get('/advantages', [AdvantageController::class, 'index'])->name('advantages.index');
Route::post('/advantages', [AdvantageController::class, 'store'])->name('advantages.store');
Route::put('/advantages/{advantage}', [AdvantageController::class, 'update'])->name('advantages.update');
Route::patch('/advantages/{advantage}/status', [AdvantageController::class, 'updateStatus'])->name('advantages.status');
Route::delete('/advantages/{advantage}', [AdvantageController::class, 'destroy'])->name('advantages.destroy');

Route::get('/admin-handles', [AdminHandleController::class, 'index'])->name('admin-handles.index');
Route::post('/admin-handles', [AdminHandleController::class, 'store'])->name('admin-handles.store');
Route::put('/admin-handles/{adminHandle}', [AdminHandleController::class, 'update'])->name('admin-handles.update');
Route::delete('/admin-handles/{adminHandle}', [AdminHandleController::class, 'destroy'])->name('admin-handles.destroy');

    Route::get('/settings/profile', [SettingsController::class, 'profile'])->name('settings.profile');
    Route::patch('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile.update');
    Route::delete('/settings/profile', [SettingsController::class, 'destroyAccount'])->name('settings.profile.destroy');

    Route::get('/settings/password', [SettingsController::class, 'password'])->name('settings.password');
    Route::put('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password.update');

    //routes chyntia
    // Route untuk halaman Katalog / Ready Stock
Route::get('/katalog', function () {
    return view('katalog');
});
// Route untuk Halaman Pre Order Member
Route::middleware(['auth'])->group(function () {
    Route::get('/member/pre-order', function () {
        return view('member.pre-order');
    })->name('member.pre-order');
});
Route::middleware(['auth'])->group(function () {
    Route::get('/member/profile', function () {
        return view('member.profile');
    })->name('member.profile');
});