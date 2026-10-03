<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ManageOrderController extends Controller
{
    private const STATUSES = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
    private const PAYMENT  = ['pending', 'paid', 'failed', 'refunded'];

    /** Daftar pesanan + filter (semua dari database) */
    public function index(Request $request)
    {
        $q = CustomerOrder::query()
            ->withCount('items')
            ->with(['paymentConfirmations' => fn ($c) => $c->latest('id')])
            ->latest('id');

        if ($s = trim((string) $request->input('search'))) {
            $q->where(function ($w) use ($s) {
                $w->where('order_number', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('whatsapp_number', 'like', "%{$s}%")
                  ->orWhere('first_name', 'like', "%{$s}%")
                  ->orWhere('last_name', 'like', "%{$s}%")
                  ->orWhereRaw("CONCAT(first_name, ' ', last_name) like ?", ["%{$s}%"]);
            });
        }

        if (in_array($request->input('status_filter'), self::STATUSES, true)) {
            $q->where('status', $request->input('status_filter'));
        }

        if (in_array($request->input('payment_status_filter'), self::PAYMENT, true)) {
            $q->where('payment_status', $request->input('payment_status_filter'));
        }

        match ($request->input('dp_status_filter')) {
            'full_payment'      => $q->where('payment_method', 'full'),
            'dp_pending'        => $q->where('payment_method', 'dp')->whereNull('dp_paid_at'),
            'dp_paid'           => $q->where('payment_method', 'dp')->whereNotNull('dp_paid_at'),
            'remaining_pending' => $q->where('payment_method', 'dp')->whereNotNull('dp_paid_at')->whereNull('remaining_paid_at'),
            'fully_paid'        => $q->where('payment_method', 'dp')->whereNotNull('remaining_paid_at'),
            default             => null,
        };

        if ($brand = $request->input('brand_filter')) {
            $q->whereHas('items', fn ($i) => $i->whereIn(
                'product_id',
                DB::table('products')->where('brand_id', $brand)->select('id')
            ));
        }

        if ($product = $request->input('product_filter')) {
            $q->whereHas('items', fn ($i) => $i->where('product_id', $product));
        }

        return view('orders.index', [
            'orders'   => $q->paginate(15)->withQueryString(),
            'brands'   => DB::table('brands')->orderBy('name')->pluck('name', 'id'),
            'products' => DB::table('products')->orderBy('name')->pluck('name', 'id'),
        ]);
    }

    /** Ubah status pesanan */
    public function updateStatus(Request $request, CustomerOrder $order)
    {
        $data = $request->validate(['status' => 'required|in:' . implode(',', self::STATUSES)]);

        $order->forceFill(['status' => $data['status']])->save();

        return back()->with('success', "Status pesanan {$order->order_number} diperbarui.");
    }

    /** Ubah status pembayaran (pesanan Bayar Penuh) */
    public function updatePayment(Request $request, CustomerOrder $order)
    {
        abort_unless($order->payment_method === 'full', 404);

        $data = $request->validate(['payment_status' => 'required|in:' . implode(',', self::PAYMENT)]);

        $order->forceFill(['payment_status' => $data['payment_status']])->save();

        // Sinkronkan status bukti transfer yang dilihat pelanggan
        if ($data['payment_status'] === 'paid')   $this->reviewLatestProof($order, 'verified');
        if ($data['payment_status'] === 'failed') $this->reviewLatestProof($order, 'rejected');

        return back()->with('success', "Status pembayaran {$order->order_number} diperbarui.");
    }

    /** Tandai DP lunas (pesanan Down Payment) */
    public function markDpPaid(CustomerOrder $order)
    {
        abort_unless($order->payment_method === 'dp' && ! $order->dp_paid_at, 404);

        $order->forceFill(['dp_paid_at' => now()])->save();
        $this->reviewLatestProof($order, 'verified');

        return back()->with('success', "DP pesanan {$order->order_number} ditandai lunas.");
    }

    /** Tandai sisa pembayaran lunas (pesanan Down Payment) */
    public function markRemainingPaid(CustomerOrder $order)
    {
        abort_unless($order->payment_method === 'dp' && $order->dp_paid_at && ! $order->remaining_paid_at, 404);

        $order->forceFill(['remaining_paid_at' => now(), 'payment_status' => 'paid'])->save();
        $this->reviewLatestProof($order, 'verified');

        return back()->with('success', "Sisa pembayaran {$order->order_number} ditandai lunas.");
    }

    /** Hapus pesanan beserta file bukti transfernya */
    public function destroy(CustomerOrder $order)
    {
        foreach ($order->paymentConfirmations as $c) {
            Storage::disk('public')->delete($c->proof_path);
        }

        $number = $order->order_number;
        $order->delete(); // item & konfirmasi ikut terhapus (cascade)

        return back()->with('success', "Pesanan {$number} dihapus.");
    }

    private function reviewLatestProof(CustomerOrder $order, string $status): void
    {
        $proof = $order->paymentConfirmations()->where('status', 'pending')->latest('id')->first();
        $proof?->update(['status' => $status]);
    }
}