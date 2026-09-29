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
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SellerController;
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

/// INi nanti masukin di auth member
Route::middleware('auth')->prefix('member')->name('member.')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
});
    

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

Route::get('/sellers', [SellerController::class, 'index'])->name('seller.index');
Route::post('/sellers', [SellerController::class, 'store'])->name('seller.store');
Route::put('/sellers/{seller}', [SellerController::class, 'update'])->name('seller.update');
Route::delete('/sellers/{seller}', [SellerController::class, 'destroy'])->name('seller.destroy');

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
// Route::middleware(['auth'])->group(function () {
//     Route::get('/member/profile', function () {
//         return view('member.profile');
//     })->name('member.profile');
// });

Route::middleware(['auth'])->group(function () {
    // Route Dashboard Admin
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Route untuk Manajemen Kategori
Route::prefix('c/categories')->name('categories.')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('index');
    Route::get('/create', [CategoryController::class, 'create'])->name('create');
    Route::post('/', [CategoryController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [CategoryController::class, 'edit'])->name('edit');
    Route::put('/{id}', [CategoryController::class, 'update'])->name('update');
    Route::patch('/{id}/toggle', [CategoryController::class, 'toggleStatus'])->name('toggle');
    Route::delete('/{id}', [CategoryController::class, 'destroy'])->name('destroy');
});

Route::prefix('c/brands')->name('brands.')->group(function () {

    // ---------- CRUD ----------
    Route::get('/',          [BrandController::class, 'index'])->name('index');
    Route::get('/create',   [BrandController::class, 'create'])->name('create');
    Route::post('/',        [BrandController::class, 'store'])->name('store');
    Route::get('/{id}/edit',[BrandController::class, 'edit'])->name('edit');
    Route::put('/{id}',     [BrandController::class, 'update'])->name('update');
    Route::delete('/{id}',  [BrandController::class, 'destroy'])->name('destroy');

    // ---------- FITUR TAMBAHAN ----------
    // 1. Toggle “Tampil di Homepage”
    Route::patch('/{id}/toggle-home', [BrandController::class, 'toggleHome'])
         ->name('toggleHome');

    // 2. Naik / Turun urutan di homepage carousel
    Route::patch('/{id}/move-order/{direction}', [BrandController::class, 'moveOrder'])
         ->where('direction', 'up|down')
         ->name('moveOrder');

    // 3. Aktif / Non‑aktif brand
    Route::patch('/{id}/toggle-status', [BrandController::class, 'toggleStatus'])
         ->name('toggleStatus');
});

// Route untuk Manajemen Produk
Route::prefix('c/products')->name('products.')->group(function () {

    // ---------- Sudah aktif ----------
    Route::get('/',                    [ProductController::class, 'index'])->name('index');
    Route::post('/',                    [ProductController::class, 'store'])->name('store');
    Route::patch('/{id}/toggle-status', [ProductController::class, 'toggleStatus'])->name('toggleStatus');
    Route::delete('/{id}',             [ProductController::class, 'destroy'])->name('destroy');

    // ---------- Sementara dummy, ganti ke controller saat halamannya dibuat ----------
    Route::get('/create',    [ProductController::class, 'create'])->name('create');
    Route::get('/{id}/edit', [ProductController::class, 'edit'])->name('edit');
});


// DUMMY ROUTE
// ==== Manajemen Konten ====
// Route::get('/banners', fn() => 'Halaman Banner (dummy)')->name('banners.index');
// Route::get('/advantages', fn() => 'Halaman Keunggulan (dummy)')->name('advantages.index');
// Route::get('/social-media', fn() => 'Halaman Media Sosial (dummy)')->name('social-media.index');
// Route::get('/customer-service', fn() => 'Halaman Customer Service (dummy)')->name('customer-service.index');
// Route::get('/admin-handles', fn() => 'Halaman Admin Handle (dummy)')->name('admin-handles.index');
Route::get('/banks', fn() => 'Halaman Informasi Bank (dummy)')->name('banks.index');

// ==== Pesanan ====
// Route::get('/orders', fn () => 'Halaman Kelola Pesanan (dummy)')->name('orders.index');
 Route::get('orders/index', function () {
        return view('orders.index');
    })->name('orders.index');
 
// ==== Laporan ====
// Route::get('/reports/mitra-sales', fn () => 'Halaman Laporan Penjualan Mitra (dummy)')->name('reports.mitra-sales');
// Route Laporan Penjualan Mitra (File: admin/reports/mitra.blade.php)
    Route::get('/admin/laporan', function () {
        return view('admin.laporan');
    })->name('admin.laporan'); 
    
// ==== Pengguna ====
// // Route::get('/users', fn () => 'Halaman Kelola Pengguna (dummy)')->name('users.index');
//  Route::get('seller/index', function () {
//         return view('seller.index');
//     })->name('seller.index');
//  Route::get('seller/create', function () {
//         return view('seller.create');
//     })->name('seller.create');

//     Route::get('seller/edit', function () {
//         return view('seller.edit');
//     })->name('seller.edit');
   
     
// ==== Pemasaran ====
// Route::get('/coupons', fn () => 'Halaman Kupon (dummy)')->name('coupons.index');
// Route::get('/coupons/create', fn () => 'Halaman Tambah Kupon (dummy)')->name('coupons.create');
 Route::middleware(['auth'])->group(function () {
    // Route Manajemen Kupon
    Route::get('kupon/index', function () {
        return view('kupon.index');
    })->name('kupon.index');

    Route::get('kupon/create', function () {
        return view('kupon.create');
    })->name('kupon.create');

    Route::get('kupon/edit', function () {
        return view('kupon.edit');
    })->name('kupon.edit');

   
});

// ==== Akun ====
Route::get('/settings/profile', fn() => 'Halaman Pengaturan Profil (dummy)')->name('settings.profile');

// logout harus POST karena dipanggil lewat <form method="POST"> di sidebar
Route::post('/logout', function () {
    // nanti ganti dengan Auth::logout() beneran
    return redirect()->route('dashboard');
})->name('logout');