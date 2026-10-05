{{--
    Halaman Pembayaran DP.

    Variabel dari controller:
      $order        : model Order (order_number, created_at, status, total, amount_due)
      $bankAccounts : koleksi rekening aktif
--}}
@extends('layouts.app')

@section('title', 'Pembayaran DP — Zakira Moslem Hijab Identity')

@section('content')
@php
    $statusLabels  = ['pending' => 'Menunggu', 'processing' => 'Diproses', 'shipped' => 'Dikirim', 'delivered' => 'Terkirim', 'cancelled' => 'Dibatalkan'];
    $statusClasses = [
        'pending'    => 'bg-[#fef9c2] text-[#894b00]',
        'processing' => 'bg-[#dbeafe] text-[#193cb8]',
        'shipped'    => 'bg-purple-100 text-purple-800',
        'delivered'  => 'bg-[#dcfce7] text-[#016630]',
        'cancelled'  => 'bg-red-100 text-red-800',
    ];
    $rp  = fn ($n) => 'Rp ' . number_format((int) $n, 0, ',', '.');
    $wib = fn ($date, $format = 'd M Y H:i') => $date ? \Illuminate\Support\Carbon::parse($date)->timezone('Asia/Jakarta')->format($format) : '-';

    $total     = (int) $order->total;
    $dpAmount  = (int) $order->amount_due;
    $remaining = max(0, $total - $dpAmount);
    $dpPercent = $total > 0 ? number_format($dpAmount / $total * 100, 1) : '0.0';
@endphp

<div class="zakira-container">
<div class="px-4 py-12 w-full">

    <!-- Judul & Breadcrumb -->
    <div class="mb-10">
        <h1 class="text-4xl font-semibold text-gray-900">Pembayaran DP</h1>
        <nav class="text-lg text-gray-500 mt-3 flex items-center gap-3">
            <a href="{{ url('/') }}" class="hover:underline">Beranda</a>
            <span>/</span>
            <a href="{{ route('member.profile') }}" class="hover:underline">Akun</a>
            <span>/</span>
            <span class="text-gray-900">Pembayaran DP</span>
        </nav>
    </div>

    @if (session('success'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm p-3 rounded-lg">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 text-sm p-3 rounded-lg">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-start">

        <!-- KIRI: Detail Pesanan -->
        <div class="bg-white p-7 rounded-2xl border border-gray-100 shadow-sm">
            <h3 class="text-[1.35rem] font-semibold text-gray-900 mb-6">Detail Pesanan</h3>

            <div class="space-y-4 text-base pb-6 border-b border-gray-200">
                <div class="flex justify-between gap-4">
                    <span class="text-gray-600">Nomor Pesanan:</span>
                    <span class="text-gray-900 font-medium">{{ $order->order_number }}</span>
                </div>
                <div class="flex justify-between gap-4">
                    <span class="text-gray-600">Tanggal Pesanan:</span>
                    <span class="text-gray-900 font-medium">{{ $wib($order->created_at) }}</span>
                </div>
                <div class="flex justify-between items-center gap-4">
                    <span class="text-gray-600">Status Pesanan:</span>
                    <span class="{{ $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-800' }} text-sm font-medium px-3 py-1 rounded">
                        {{ $statusLabels[$order->status] ?? ucfirst($order->status) }}
                    </span>
                </div>
                <div class="flex justify-between gap-4">
                    <span class="text-gray-600">Total Pesanan:</span>
                    <span class="text-gray-900 font-medium">{{ $rp($total) }}</span>
                </div>
            </div>

            <!-- Informasi Down Payment -->
            <div class="mt-6 bg-[#eff6ff] border border-[#bfdbfe] rounded-xl p-5 text-[#1447e6]">
                <h4 class="text-xl font-medium mb-4">Informasi Down Payment</h4>
                <div class="space-y-2 text-lg">
                    <div class="flex justify-between">
                        <span>Jumlah DP ({{ $dpPercent }}%):</span>
                        <span class="font-semibold text-[#193cb8]">{{ $rp($dpAmount) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Sisa Pembayaran:</span>
                        <span class="font-semibold text-[#193cb8]">{{ $rp($remaining) }}</span>
                    </div>
                </div>
                <p class="text-sm mt-3 flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-7-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM9 9a.75.75 0 0 0 0 1.5h.253a.25.25 0 0 1 .244.304l-.459 2.066A1.75 1.75 0 0 0 10.747 15H11a.75.75 0 0 0 0-1.5h-.253a.25.25 0 0 1-.244-.304l.459-2.066A1.75 1.75 0 0 0 9.253 9H9Z" clip-rule="evenodd" /></svg>
                    Setelah DP dibayar, sisa pembayaran dapat dilakukan kapan saja.
                </p>
            </div>
        </div>

        <!-- KANAN: Rekening + Upload -->
        @include('member.orders.partials.payment-sidebar', [
            'bankAccounts' => $bankAccounts,
            'amount'       => $dpAmount,
            'noteSuffix'   => ' (DP ' . $dpPercent . '%)',
            'proofLabel'   => 'Bukti Transfer DP',
            'formAction'   => route('member.orders.pay-dp.store', $order->order_number),
        ])

    </div>
</div>
</div>
@endsection