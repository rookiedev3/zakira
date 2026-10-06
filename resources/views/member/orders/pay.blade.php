{{--
    Halaman Pembayaran (satu view untuk DP & Sisa).
    Lokasi: resources/views/member/orders/pay.blade.php

    Variabel dari controller:
      $type         : 'dp' atau 'remaining'
      $order        : model CustomerOrder (order_number, created_at, status, total, amount_due, dp_paid_at)
      $bankAccounts : koleksi rekening aktif (bank_name, account_number, account_name, is_active)
--}}
@extends('layouts.app')

@section('title', ($type === 'dp' ? 'Pembayaran DP' : 'Pembayaran Sisa') . ' — Zakira Moslem Hijab Identity')

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

    $isDp = $type === 'dp';

    $total         = (int) $order->total;
    $dpAmount      = (int) $order->amount_due;
    $remaining     = max(0, $total - $dpAmount);
    $dpPercent     = $total > 0 ? number_format($dpAmount / $total * 100, 1) : '0.0';
    $remainPercent = $total > 0 ? number_format($remaining / $total * 100, 1) : '0.0';

    // Isi yang berbeda antara DP & Sisa
    $pageTitle  = $isDp ? 'Pembayaran DP' : 'Pembayaran Sisa';
    $amount     = $isDp ? $dpAmount : $remaining;
    $noteSuffix = $isDp ? ' (DP ' . $dpPercent . '%)' : '';
    $proofLabel = $isDp ? 'Bukti Transfer DP' : 'Bukti Transfer Sisa Pembayaran';
    $formAction = route($isDp ? 'member.orders.pay-dp.store' : 'member.orders.pay-remaining.store', $order->order_number);
@endphp

<div class="zakira-container">
<div class="px-4 py-12 w-full">

    <!-- Judul & Breadcrumb -->
    <div class="mb-10">
        <h1 class="text-4xl font-semibold text-gray-900">{{ $pageTitle }}</h1>
        <nav class="text-lg text-gray-500 mt-3 flex items-center gap-3">
            <a href="{{ url('/') }}" class="hover:underline">Beranda</a>
            <span>/</span>
            <a href="{{ route('member.profile') }}" class="hover:underline">Akun</a>
            <span>/</span>
            <span class="text-gray-900">{{ $pageTitle }}</span>
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

            @if ($isDp)
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
            @else
                <!-- DP sudah dibayar -->
                <div class="mt-6 bg-[#f0fdf4] border border-[#b9f8cf] rounded-xl p-5 text-[#016630]">
                    <div class="flex justify-between items-start gap-4">
                        <div>
                            <h4 class="text-xl font-medium">DP ({{ $dpPercent }}%) - Sudah Dibayar</h4>
                            <p class="text-base mt-1">Dibayar: {{ $wib($order->dp_paid_at) }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xl font-semibold">{{ $rp($dpAmount) }}</p>
                            <p class="text-base mt-1 inline-flex items-center gap-1.5 text-[#008236]">
                                <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" /></svg>
                                Lunas
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Sisa pembayaran -->
                <div class="mt-4 bg-[#eff6ff] border border-[#bfdbfe] rounded-xl p-5 text-[#1447e6]">
                    <h4 class="text-xl font-medium mb-4">Sisa Pembayaran ({{ $remainPercent }}%)</h4>
                    <div class="flex justify-between items-center text-lg">
                        <span>Jumlah yang harus dibayar:</span>
                        <span class="font-semibold text-[#193cb8] text-xl">{{ $rp($remaining) }}</span>
                    </div>
                    <p class="text-sm mt-3 flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-7-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM9 9a.75.75 0 0 0 0 1.5h.253a.25.25 0 0 1 .244.304l-.459 2.066A1.75 1.75 0 0 0 10.747 15H11a.75.75 0 0 0 0-1.5h-.253a.25.25 0 0 1-.244-.304l.459-2.066A1.75 1.75 0 0 0 9.253 9H9Z" clip-rule="evenodd" /></svg>
                        Tidak ada batas waktu untuk pembayaran sisa.
                    </p>
                </div>
            @endif
        </div>

        <!-- KANAN: Rekening + Upload -->
        <div class="space-y-7">

            <!-- Informasi Rekening -->
            <div class="bg-white p-7 rounded-2xl border border-gray-100 shadow-sm">
                <h3 class="text-[1.35rem] font-semibold text-gray-900 mb-6">Informasi Rekening</h3>

                <div class="space-y-4">
                    @forelse ($bankAccounts as $bank)
                        <div class="border border-gray-200 rounded-xl p-5" x-data="{ copied: false }">
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-lg font-medium text-gray-900">{{ $bank->bank_name }}</span>
                                @if ($bank->is_active ?? true)
                                    <span class="bg-[#dcfce7] text-[#016630] text-sm font-medium px-3 py-0.5 rounded-full">Aktif</span>
                                @endif
                            </div>

                            <div class="flex justify-between items-start gap-3 text-base">
                                <div class="space-y-1">
                                    <div class="flex gap-6">
                                        <span class="text-gray-600">No. Rekening:</span>
                                        <span class="font-mono text-gray-900 tracking-wide">{{ $bank->account_number }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Atas Nama:</span>
                                        <span class="font-medium text-gray-900">{{ $bank->account_name }}</span>
                                    </div>
                                </div>

                                <button type="button"
                                        @click="navigator.clipboard.writeText(@js($bank->account_number)); copied = true; setTimeout(() => copied = false, 1500)"
                                        class="text-[#8C6239] hover:text-[#724e2c] p-1 cursor-pointer"
                                        title="Salin nomor rekening">
                                    <svg x-show="!copied" class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor"><path d="M7 3.5A1.5 1.5 0 0 1 8.5 2h3.879a1.5 1.5 0 0 1 1.06.44l3.122 3.12A1.5 1.5 0 0 1 17 6.622V12.5a1.5 1.5 0 0 1-1.5 1.5h-1v-3.379a3 3 0 0 0-.879-2.121L10.5 5.379A3 3 0 0 0 8.379 4.5H7v-1Z" /><path d="M4.5 6A1.5 1.5 0 0 0 3 7.5v9A1.5 1.5 0 0 0 4.5 18h7a1.5 1.5 0 0 0 1.5-1.5v-5.879a1.5 1.5 0 0 0-.44-1.06L9.44 6.439A1.5 1.5 0 0 0 8.378 6H4.5Z" /></svg>
                                    <svg x-show="copied" x-cloak class="w-5 h-5 text-green-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" /></svg>
                                </button>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-base">Rekening belum tersedia. Silakan hubungi admin.</p>
                    @endforelse
                </div>

                <div class="mt-5 bg-[#fef9c2] border border-yellow-200 text-[#894b00] rounded-xl p-4 text-base flex gap-3">
                    <svg class="w-5 h-5 mt-1 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495ZM10 5a.75.75 0 0 1 .75.75v3.5a.75.75 0 0 1-1.5 0v-3.5A.75.75 0 0 1 10 5Zm0 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" /></svg>
                    <p>
                        <strong>Penting:</strong> Transfer tepat sebesar <strong>{{ $rp($amount) }}</strong>{{ $noteSuffix }} untuk mempercepat verifikasi.
                    </p>
                </div>
            </div>

            <!-- Upload Bukti Pembayaran -->
            <div class="bg-white p-7 rounded-2xl border border-gray-100 shadow-sm">
                <h3 class="text-[1.35rem] font-semibold text-gray-900 mb-6">Upload Bukti Pembayaran</h3>

                <form action="{{ $formAction }}" method="POST" enctype="multipart/form-data" x-data="{ fileName: '' }">
                    @csrf

                    <label for="payment_proof" class="block text-base font-medium text-gray-900 mb-3">{{ $proofLabel }}</label>

                    <div class="flex items-center gap-4">
                        <label for="payment_proof"
                               class="bg-[#f6ede4] hover:bg-[#efe0d0] text-[#8C6239] text-base font-medium px-5 py-3 rounded-lg cursor-pointer transition">
                            Pilih File
                        </label>
                        <span class="text-gray-600 text-base truncate" x-text="fileName || 'Belum ada file dipilih'"></span>
                        <input id="payment_proof" type="file" name="payment_proof" accept="image/jpeg,image/png,image/gif" class="sr-only"
                               @change="fileName = $event.target.files[0]?.name ?? ''">
                    </div>

                    @error('payment_proof')
                        <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                    @enderror

                    <p class="text-gray-500 text-sm mt-4 flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-7-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM9 9a.75.75 0 0 0 0 1.5h.253a.25.25 0 0 1 .244.304l-.459 2.066A1.75 1.75 0 0 0 10.747 15H11a.75.75 0 0 0 0-1.5h-.253a.25.25 0 0 1-.244-.304l.459-2.066A1.75 1.75 0 0 0 9.253 9H9Z" clip-rule="evenodd" /></svg>
                        Format yang didukung: JPG, PNG, GIF. Maksimal 2MB.
                    </p>

                    <button type="submit"
                            class="w-full mt-6 bg-[#8C6239] hover:bg-[#724e2c] text-white text-lg font-medium py-4 rounded-xl transition cursor-pointer">
                        Upload Bukti Pembayaran
                    </button>
                </form>
            </div>

        </div>

    </div>
</div>
</div>
@endsection