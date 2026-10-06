<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\CustomerOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /** Jenis pembayaran yang didukung: 'dp' dan 'remaining' (sisa). */
    private const PAY_TYPES = ['dp', 'remaining'];

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
        $order = $this->ownOrder($orderNumber, ['invoice']);

        abort_unless($order->invoice, 404, 'Faktur belum dibuat.');

        return app(\App\Http\Controllers\Admin\InvoiceController::class)->download($order->invoice);
    }

    /** Halaman pembayaran DP / Sisa (satu view: member.orders.pay) */
    public function payment(string $orderNumber, string $type)
    {
        abort_unless(in_array($type, self::PAY_TYPES, true), 404);

        $order = $this->ownOrder($orderNumber);

        if (! $this->canPay($order, $type)) {
            return redirect()->route('member.profile')->with('error', $this->payDeniedMessage($type));
        }

        return view('member.orders.pay', [
            'type'         => $type,
            'order'        => $order,
            'bankAccounts' => $this->bankAccounts(),
        ]);
    }

    /**
     * Upload bukti transfer DP / Sisa.
     * Disimpan sebagai PaymentConfirmation berstatus 'pending'; admin yang
     * menandai lunas lewat ManageOrderController (markDpPaid / markRemainingPaid).
     */
    public function storePayment(Request $request, string $orderNumber, string $type)
    {
        abort_unless(in_array($type, self::PAY_TYPES, true), 404);

        $order = $this->ownOrder($orderNumber);

        if (! $this->canPay($order, $type)) {
            return redirect()->route('member.profile')->with('error', $this->payDeniedMessage($type));
        }

        $request->validate([
            'payment_proof' => ['required', 'image', 'mimes:jpg,jpeg,png,gif', 'max:2048'],
        ]);

        $path = $request->file('payment_proof')->store('payment-proofs', 'public');

        // Nominal yang seharusnya dibayar: DP = amount_due, Sisa = total - amount_due.
        $amount = $type === 'dp'
            ? (int) $order->amount_due
            : max(0, (int) $order->total - (int) $order->amount_due);

        // Kolom `amount` wajib diisi (NOT NULL tanpa default di tabel payment_confirmations).
        // Kolom `type` ('dp' / 'remaining') dipakai admin untuk memisahkan bukti DP dan pelunasan,
        // jadi tabel payment_confirmations harus punya kolom itu (lihat migration).
        $order->paymentConfirmations()->forceCreate([
            'type'          => $type,
            'amount'        => $amount,
            'account_name'  => trim($order->first_name . ' ' . $order->last_name),
            'transfer_date' => now('Asia/Jakarta')->toDateString(),
            'proof_path'    => $path,
            'status'        => 'pending',
        ]);

        return redirect()->route('member.profile')->with(
            'success',
            $type === 'dp'
                ? 'Bukti pembayaran DP berhasil diunggah.'
                : 'Bukti pelunasan berhasil diunggah.'
        );
    }

    /** Pesanan milik user yang login (dicocokkan lewat email / no telp). */
    private function ownOrder(string $orderNumber, array $with = []): CustomerOrder
    {
        return CustomerOrder::with($with)
            ->where('order_number', $orderNumber)
            ->ownedBy(Auth::user()->load('detail'))
            ->firstOrFail();
    }

    /** Syarat boleh membayar. Dicek di halaman maupun saat upload. */
    private function canPay(CustomerOrder $order, string $type): bool
    {
        if ($order->payment_method === 'full' || $order->status === 'cancelled') {
            return false;
        }

        return $type === 'dp'
            ? ! $order->dp_paid_at
            : (bool) ($order->dp_paid_at && ! $order->remaining_paid_at);
    }

    private function payDeniedMessage(string $type): string
    {
        return $type === 'dp'
            ? 'Pesanan ini tidak memerlukan pembayaran DP.'
            : 'Pesanan ini tidak memerlukan pembayaran sisa.';
    }

    /**
     * Rekening aktif dari database, dinormalisasi ke format yang dipakai view
     * (bank_name, account_number, account_name, is_active).
     */
    private function bankAccounts()
    {
        return BankAccount::query()
            ->where('status', true) // sama seperti CheckoutController
            ->orderBy('id')
            ->get()
            ->map(fn ($b) => (object) [
                'bank_name'      => $b->bank_name,
                'account_number' => $b->account_number,
                'account_name'   => $b->account_holder_name,
                'is_active'      => true,
            ]);
    }
}