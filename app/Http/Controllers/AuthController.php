<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function login(): View
    {
        return view('auth.login');
    }

    public function auth(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Email atau password tidak salah.',
            ])->onlyInput('email');
        }

        $user = Auth::user();

        // Customer tipe umum/non-member tidak boleh login.
        // Hanya admin, member, dan distributor yang boleh punya sesi login.
        if ($user->role === 'customer' && $user->customer_type === 'umum') {
            Auth::logout();

            return back()
                ->with('error', 'Login khusus member Zakira. Pembelian Ready Stock dapat dilakukan tanpa login.')
                ->onlyInput('email');
        }

        if ($user->status !== 'aktif') {
            Auth::logout();

            return back()->with('error', match ($user->status) {
                'ditangguhkan' => 'Akun Anda sedang ditangguhkan. Silakan hubungi admin.',
                default => 'Akun Anda tidak aktif. Silakan hubungi admin.',
            })->onlyInput('email');
        }

        $request->session()->regenerate();

        $redirect = $user->role === 'admin' ? route('admin.dashboard') : route('home');

        return redirect()->intended($redirect)
            ->with('success', 'Selamat datang kembali, ' . $user->name . '!');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}