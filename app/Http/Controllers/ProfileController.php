<?php

namespace App\Http\Controllers;

use App\Models\CustomerOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user()->load('detail');

        // Riwayat pesanan milik user ini, dicocokkan lewat email atau no telp (5 per halaman)
        $orders = CustomerOrder::ownedBy($user)
            ->withCount('items')
            ->with(['items', 'invoice'])
            ->latest()
            ->paginate(5, ['*'], 'pesanan');

        // Statistik pesanan
        $base = CustomerOrder::ownedBy($user);
        $stats = [
            'total'  => (clone $base)->count(),
            'active' => (clone $base)->whereNotIn('status', ['delivered', 'cancelled'])->count(),
            'done'   => (clone $base)->where('status', 'delivered')->count(),
            'spent'  => (clone $base)->where('status', '!=', 'cancelled')->sum('total'),
        ];

        return view('member.profile', compact('user', 'orders', 'stats'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name'                => ['required', 'string', 'max:255'],
            'email'               => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone'               => ['nullable', 'string', 'max:20'],
            'address'             => ['nullable', 'string', 'max:500'],
            'city'                => ['nullable', 'string', 'max:100'],
            'province'            => ['nullable', 'string', 'max:100'],
            'postal_code'         => ['nullable', 'string', 'max:10'],
            'seller_id'           => ['nullable', 'string', 'max:100'],
            'shipping_expedition' => ['nullable', 'string', 'max:50'],
        ]);

        $user->update([
            'name'  => $data['name'],
            'email' => $data['email'],
        ]);

        $user->detail()->updateOrCreate(
            ['user_id' => $user->id],
            collect($data)->except(['name', 'email'])->toArray()
        );

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        Auth::user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password berhasil diubah.');
    }

    /**
     * Unduh faktur milik user. Faktur dibuat admin (model Invoice);
     * di sini hanya dicek kepemilikannya, lalu file dikirim lewat logika
     * download admin supaya PDF/Excel-nya persis sama.
     */
    public function invoice(string $orderNumber)
    {
        $order = CustomerOrder::with('invoice')
            ->where('order_number', $orderNumber)
            ->ownedBy(Auth::user()->load('detail'))
            ->firstOrFail();

        abort_unless($order->invoice, 404, 'Faktur belum dibuat.');

        return app(\App\Http\Controllers\Admin\InvoiceController::class)->download($order->invoice);
    }
}