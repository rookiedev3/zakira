<?php

use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\UserController;
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
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

    Route::middleware('role:admin')->group(function () {
        // Route::get('/dashboard', function () {
        //     return view('admin.dashboard');
        // })->name('dashboard');

    });
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