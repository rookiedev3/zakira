<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
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
                'email' => 'Email atau password salah.',
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

        $user->forceFill(['last_login_at' => now()])->save();

        $redirect = $user->role === 'admin' ? route('admin.dashboard') : route('home');

        return redirect()->intended($redirect)
            ->with('success', 'Selamat datang kembali, ' . $user->name . '!');
    }

    public function destroy(Request $request): RedirectResponse
    {
         Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    public function forgotPassword(): View
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        Password::sendResetLink($request->only('email'));

        // Selalu tampilkan pesan sukses yang sama, walau email tidak terdaftar.
        // Ini praktik keamanan standar supaya orang luar tidak bisa "menebak"
        // email mana saja yang terdaftar di sistem kita.
        return back()->with('status', 'Kami telah mengirimkan tautan reset jika akun Anda terdaftar. Periksa folder spam jika Anda tidak menemukannya.');
    }


    // ============ RESET PASSWORD ============

    public function resetPassword(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('success', 'Password berhasil direset. Silakan login.');
        }

        return back()->withErrors([
            'email' => __($status),
        ]);
    }
}