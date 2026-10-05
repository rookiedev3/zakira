{{-- resources/views/checkout/success.blade.php --}}
@extends('layouts.app')
@section('title', 'Pesanan Berhasil')

@php
    $isDp      = $order->payment_method === 'dp';
    $amountDue = (int) $order->amount_due;
    $remaining = max(0, (int) $order->total - $amountDue);
    $rp        = fn ($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');
    $variant   = fn ($it) => array_filter([
        'Model'  => $it->model,
        'Warna'  => $it->color,
        'Ukuran' => $it->size,
    ]);
@endphp

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-0 py-8">

    {{-- Section 1: Konfirmasi Checkout --}}
    <div class="bg-white rounded-lg shadow-sm border p-8 mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Checkout</h1>

        <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-600 text-2xl mr-3"></i>
                <div>
                    <h2 class="text-xl font-semibold text-green-800">Terima kasih. Pesanan Anda telah diterima.</h2>
                </div>
            </div>
        </div>

        {{-- Detail pesanan --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div>
                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Order Number</h3>
                <p class="text-lg font-semibold text-gray-900 mt-1 break-all">{{ $order->order_number }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Tanggal</h3>
                <p class="text-lg font-semibold text-gray-900 mt-1">{{ $order->created_at->format('d M Y') }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Email</h3>
                <p class="text-lg font-semibold text-gray-900 mt-1 break-all">{{ $order->email ?: 'Tidak ada' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Total</h3>
                <p class="text-lg font-semibold text-[#B4775E] mt-1">{{ $rp($order->total) }}</p>
            </div>
        </div>

        <div class="mb-6">
            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-2">Payment Method</h3>
            <p class="text-gray-900">{{ $isDp ? 'Down Payment' : 'Pembayaran Penuh' }} - Bank Transfer</p>

            @if ($isDp)
                <div class="mt-2 p-3 bg-[#B4775E]/10 border border-[#B4775E]/30 rounded-lg">
                    <div class="text-sm text-[#7A4A38]">
                        <div class="font-medium mb-1">Informasi Down Payment:</div>
                        <div>• Jumlah DP ({{ rtrim(rtrim(number_format((float) $order->dp_percent, 1, '.', ''), '0'), '.') }}%): <span class="font-semibold">{{ $rp($amountDue) }}</span></div>
                        <div>• Sisa Pembayaran: <span class="font-semibold">{{ $rp($remaining) }}</span></div>
                        <div class="mt-2 text-xs text-[#8A5340]">
                            <i class="fas fa-info-circle mr-1"></i>
                            Sisa pembayaran dapat dibayar kapan saja setelah DP dikonfirmasi
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="bg-[#B4775E]/10 border border-[#B4775E]/30 rounded-lg p-6">
            <p class="text-[#7A4A38] mb-4">
                @if ($isDp)
                    Anda telah memilih metode <strong>Down Payment</strong>. Silakan transfer DP sebesar <strong>{{ $rp($amountDue) }}</strong> melalui salah satu rekening kami.
                @else
                    Anda telah memilih metode <strong>Pembayaran Penuh</strong>. Silakan transfer sebesar <strong>{{ $rp($amountDue) }}</strong> melalui salah satu rekening kami.
                @endif
            </p>

            <div class="bg-white border border-[#B4775E]/40 rounded-lg p-4 mb-4">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600">Jumlah yang harus dibayar sekarang:</span>
                    <span class="text-lg font-bold text-[#5E382A]">{{ $rp($amountDue) }}</span>
                </div>
            </div>

            {{-- Tombol hanya tampil bila controller mengirim $paymentConfirmUrl --}}
            @isset($paymentConfirmUrl)
                <p class="text-[#8A5340] font-medium">Konfirmasi Pembayaran</p>
                <a href="{{ $paymentConfirmUrl }}"
                   class="inline-flex items-center px-4 py-2 bg-[#B4775E] text-white font-medium rounded-lg hover:bg-[#9C6650] transition-colors mt-3">
                    <i class="fas fa-credit-card mr-2"></i>
                    Konfirmasi Pembayaran
                </a>
            @endisset
        </div>
    </div>

    {{-- Section 2: Detail Bank --}}
    @if (count($banks))
        <div class="bg-white rounded-lg shadow-sm border p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Detail Bank Kami</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach ($banks as $b)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $b['bank'] }}</h3>
                            <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Aktif</span>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">No. Rekening:</span>
                                <span class="font-mono font-medium">{{ $b['number'] }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Atas Nama:</span>
                                <span class="font-medium">{{ $b['name'] }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Section 3: Rincian Pesanan --}}
    <div class="bg-white rounded-lg shadow-sm border p-6 mb-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Rincian Pesanan</h2>

        {{-- Mobile (di bawah md) --}}
        <div class="block md:hidden space-y-4">
            @foreach ($order->items as $it)
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-start space-x-3">
                        <div class="w-16 h-16 bg-gray-100 rounded overflow-hidden flex-shrink-0 flex items-center justify-center">
                            <i class="fas fa-image text-gray-300"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-medium text-gray-900 text-sm leading-tight">{{ $it->product_name }}</h3>
                            @if (count($variant($it)))
                                <div class="text-xs text-gray-500 mt-1">
                                    @foreach ($variant($it) as $label => $val)
                                        <span class="inline-block mr-2">{{ $label }}: {{ $val }}</span>
                                    @endforeach
                                </div>
                            @endif
                            <div class="text-xs text-gray-500 mt-1">{{ $rp($it->price) }} per item</div>
                        </div>
                    </div>
                    <div class="flex justify-between items-center mt-3 pt-3 border-t border-gray-100">
                        <span class="text-sm text-gray-600">Jumlah: <span class="font-medium">{{ $it->quantity }}</span></span>
                        <span class="font-medium text-gray-900">{{ $rp($it->subtotal) }}</span>
                    </div>
                </div>
            @endforeach

            <div class="border-t border-gray-200 pt-4 mt-6 space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Subtotal</span>
                    <span class="font-medium text-gray-900">{{ $rp($order->subtotal) }}</span>
                </div>
                @if ($order->discount > 0)
                    <div class="flex justify-between items-center text-green-600">
                        <span>Diskon ({{ $order->coupon_code }})</span>
                        <span>- {{ $rp($order->discount) }}</span>
                    </div>
                @endif
                <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                    <span class="font-bold text-gray-900 text-lg">Total</span>
                    <span class="font-bold text-[#B4775E] text-lg">{{ $rp($order->total) }}</span>
                </div>
            </div>
        </div>

        {{-- Desktop (md ke atas) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3 px-4 font-medium text-gray-500 uppercase tracking-wide">Produk</th>
                        <th class="text-left py-3 px-4 font-medium text-gray-500 uppercase tracking-wide">Jumlah</th>
                        <th class="text-right py-3 px-4 font-medium text-gray-500 uppercase tracking-wide">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $it)
                        <tr class="border-b border-gray-100">
                            <td class="py-4 px-4">
                                <div class="flex items-start space-x-4">
                                    <div class="w-16 h-16 bg-gray-100 rounded overflow-hidden flex-shrink-0 flex items-center justify-center">
                                        <i class="fas fa-image text-gray-300"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="font-medium text-gray-900">{{ $it->product_name }}</h3>
                                        @if (count($variant($it)))
                                            <div class="text-sm text-gray-500 mt-1">
                                                @foreach ($variant($it) as $label => $val)
                                                    <div>{{ $label }}: {{ $val }}</div>
                                                @endforeach
                                            </div>
                                        @endif
                                        <div class="text-sm text-gray-500 mt-1">{{ $rp($it->price) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-gray-900">× {{ $it->quantity }}</td>
                            <td class="py-4 px-4 text-right font-medium text-gray-900">{{ $rp($it->subtotal) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t border-gray-200">
                        <td colspan="2" class="py-4 px-4 font-medium text-gray-900">Subtotal</td>
                        <td class="py-4 px-4 text-right font-medium text-gray-900">{{ $rp($order->subtotal) }}</td>
                    </tr>
                    @if ($order->discount > 0)
                        <tr class="border-t border-gray-200 text-green-600">
                            <td colspan="2" class="py-4 px-4 font-medium">Diskon ({{ $order->coupon_code }})</td>
                            <td class="py-4 px-4 text-right font-medium">- {{ $rp($order->discount) }}</td>
                        </tr>
                    @endif
                    <tr class="border-t border-gray-200">
                        <td colspan="2" class="py-4 px-4 font-bold text-gray-900 text-lg">Total</td>
                        <td class="py-4 px-4 text-right font-bold text-[#B4775E] text-lg">{{ $rp($order->total) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Section 4: Alamat Penagihan --}}
    <div class="bg-white rounded-lg shadow-sm border p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Alamat Penagihan</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="font-medium text-gray-900 mb-3">Informasi Kontak</h3>
                <div class="space-y-2 text-sm">
                    <div>
                        <span class="text-gray-600">Nama:</span>
                        <span class="font-medium ml-2">{{ $order->first_name }} {{ $order->last_name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">WhatsApp:</span>
                        <span class="font-medium ml-2">{{ $order->whatsapp_number }}</span>
                    </div>
                    @if ($order->seller_id)
                        <div>
                            <span class="text-gray-600">Seller:</span>
                            <span class="font-medium ml-2">{{ $order->seller_id }}</span>
                        </div>
                    @endif
                    <div>
                        <span class="text-gray-600">Ekspedisi:</span>
                        <span class="font-medium ml-2">{{ $order->shipping_method }}</span>
                    </div>
                </div>
            </div>
            <div>
                <h3 class="font-medium text-gray-900 mb-3">Alamat Pengiriman</h3>
                <div class="text-sm text-gray-600">
                    <p>{{ $order->address }}</p>
                    <p>{{ $order->city }}, {{ $order->province }} {{ $order->postal_code }}</p>
                    <p>Indonesia</p>
                    @if ($order->notes)
                        <p class="mt-2 text-gray-500">Catatan: {{ $order->notes }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection