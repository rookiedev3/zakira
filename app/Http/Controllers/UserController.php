<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query();

        if ($request->filled('q')) {
            $search = $request->string('q');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('role')) {
            $query->where('role', $request->string('role'));
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        // User yang sedang diedit (kalau ada ?edit=ID di URL)
        $editingUser = $request->filled('edit')
            ? User::find($request->integer('edit'))
            : null;

        $showCreateForm = $request->query('form') === 'create';

        return view('user.index', compact('users', 'editingUser', 'showCreateForm'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,customer',
            'status' => 'required|in:aktif,tidak_aktif,ditangguhkan',
            'customer_type' => 'nullable|in:umum,member,distributor',
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'status' => $data['status'],
            'customer_type' => $data['customer_type'] ?? 'umum',
            'email_verified_at' => $request->boolean('email_verified') ? now() : null,
        ]);

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dibuat.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,customer',
            'status' => 'required|in:aktif,tidak_aktif,ditangguhkan',
            'customer_type' => 'nullable|in:umum,member,distributor',
        ]);

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'status' => $data['status'],
            'customer_type' => $data['customer_type'] ?? 'umum',
            'email_verified_at' => $request->boolean('email_verified') ? ($user->email_verified_at ?? now()) : null,
        ]);

        if ($request->boolean('reset_password')) {
            Password::sendResetLink(['email' => $user->email]);

            return redirect()->route('users.index')
                ->with('success', 'Pengguna diperbarui. Link reset password dikirim ke ' . $user->email . '.');
        }

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function updateStatus(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'status' => 'required|in:aktif,tidak_aktif,ditangguhkan',
        ]);

        $user->update(['status' => $data['status']]);

        return redirect()->route('users.index')->with('success', 'Status pengguna diperbarui.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Anda tidak bisa menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}