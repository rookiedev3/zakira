<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Admin\InvoiceController as AdminInvoiceController;
use App\Http\Controllers\Controller;
use App\Models\CustomerOrder;

class InvoiceController extends Controller
{
    public function download(string $orderNumber)
    {
        $user  = auth()->user();
        $phone = preg_replace('/\D/', '', $user->detail?->phone ?? '');

        // Variasi nomor: 0821..., 62821..., +62821...
        $variants = [];
        if ($phone !== '') {
            $local    = preg_replace('/^(62|0)/', '', $phone);
            $variants = ['0' . $local, '62' . $local, '+62' . $local];
        }

        // Filter sama seperti di halaman profil: email ATAU nomor WA
        $order = CustomerOrder::where('order_number', $orderNumber)
            ->where(function ($q) use ($user, $variants) {
                $q->where('email', $user->email);
                if ($variants) {
                    $q->orWhereIn('whatsapp_number', $variants);
                }
            })
            ->with('invoice')
            ->firstOrFail();

        abort_unless($order->invoice, 404, 'Faktur belum tersedia.');

        return app(AdminInvoiceController::class)->download($order->invoice);
    }
}