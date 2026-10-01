<?php

namespace App\Http\Controllers;

use App\Models\CustomerOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class PaymentConfirmationController extends Controller
{
    /** Halaman upload / status bukti transfer (URL bertanda tangan, tidak bisa ditebak) */
    public function show(CustomerOrder $order)
    {
        return view('payment.confirmation', [
            'order'        => $order,
            'confirmation' => $order->paymentConfirmations()->latest('id')->first(),
        ]);
    }

    /** Kirim atau ganti bukti transfer */
    public function store(Request $request, CustomerOrder $order)
    {
        $back     = fn () => redirect()->to(URL::signedRoute('payment.confirmation', ['order' => $order->order_number]));
        $existing = $order->paymentConfirmations()->latest('id')->first();

        // Pembayaran yang sudah diverifikasi admin tidak boleh diganti
        if ($existing && $existing->status === 'verified') {
            return $back()->with('error', 'Pembayaran sudah diverifikasi, bukti tidak dapat diganti.');
        }

        $request->validate([
            'proof' => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:2048',
        ], [
            'proof.required' => 'Bukti transfer wajib diunggah.',
            'proof.mimes'    => 'Bukti transfer harus berupa JPG, PNG, atau PDF.',
            'proof.max'      => 'Ukuran bukti transfer maksimal 2MB.',
            'proof.uploaded' => 'Bukti transfer gagal diunggah. Pastikan ukurannya maksimal 2MB.',
        ]);

        $path = $request->file('proof')->store('payment-proofs', 'public');

        if ($existing) {
            // Ganti bukti: hapus file lama, status kembali menunggu verifikasi
            Storage::disk('public')->delete($existing->proof_path);
            $existing->update([
                'proof_path'    => $path,
                'amount'        => $order->amount_due,
                'transfer_date' => now()->toDateString(),
                'status'        => 'pending',
            ]);
        } else {
            $order->paymentConfirmations()->create([
                'bank_name'     => null,
                'account_name'  => trim($order->first_name . ' ' . $order->last_name),
                'amount'        => $order->amount_due,
                'transfer_date' => now()->toDateString(),
                'proof_path'    => $path,
                'note'          => null,
                'status'        => 'pending',
            ]);
        }

        return $back()->with('success', 'Bukti pembayaran berhasil dikirim. Pesanan Anda sekarang menunggu verifikasi admin.');
    }
}