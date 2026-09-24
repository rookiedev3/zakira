<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'auth']);
});

Route::middleware('auth')->group(function () {
    Route::get('/logout', [AuthController::class, 'destroy'])->name('logout');

    // Route::middleware('checkrole:admin')->prefix('admin')->name('admin.')->group(function () {
    //     Route::get('/dashboard', function () {
    //         return view('admin.dashboard');
    //     })->name('dashboard');
    // });
});