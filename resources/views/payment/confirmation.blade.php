{{-- resources/views/payment/confirmation.blade.php --}}
@extends('layouts.app')
@section('title', 'Konfirmasi Pembayaran')

@php
    use Illuminate\Support\Facades\URL;

    $rp         = fn ($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');
    $postUrl    = URL::signedRoute('payment.confirmation.store', ['order' => $order->order_number]);
    $detailUrl  = URL::signedRoute('checkout.success', ['order' => $order->order_number]);

    $status     = $confirmation->status ?? null;           // pending | verified | rejected
    $showForm   = ! $confirmation || $errors->has('proof'); // form upload vs tampilan "sudah dikirim"
    $canReplace = $confirmation && $status !== 'verified';
    $sentAt     = $confirmation?->updated_at?->timezone('Asia/Jakarta')->format('d M Y, H:i');

    $badge = [
        'pending'  => ['MENUNGGU VERIFIKASI', 'bg-amber-100 text-amber-800'],
        'verified' => ['PEMBAYARAN BERHASIL', 'bg-emerald-100 text-emerald-800'],
        'rejected' => ['BUKTI DITOLAK',       'bg-red-100 text-red-800'],
    ][$status] ?? ['MENUNGGU VERIFIKASI', 'bg-amber-100 text-amber-800'];
@endphp

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-0 py-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8">

        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Konfirmasi Pembayaran</h1>
            <p class="text-gray-500 mt-1">Pantau status pembayaran pesanan Anda di halaman ini.</p>
        </div>

        {{-- Ringkasan pesanan --}}
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 mb-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center shrink-0">
                    <i class="fas fa-receipt text-blue-600"></i>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-blue-900">Order #{{ $order->order_number }}</h2>
                    <p class="text-sm text-blue-700">Total pembayaran: <strong>{{ $rp($order->total) }}</strong></p>
                    @if ($order->payment_method === 'dp')
                        <p class="text-sm text-blue-700">DP yang harus dibayar: <strong>{{ $rp($order->amount_due) }}</strong></p>
                    @endif
                </div>
            </div>
        </div>

        @if (session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl mb-6 flex items-start gap-3">
                <i class="fas fa-exclamation-circle mt-1"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- ============ TAMPILAN SETELAH BUKTI DIKIRIM ============ --}}
        @if ($confirmation)
            <div id="submittedState" class="{{ $showForm ? 'hidden' : '' }}">
                @if (session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl mb-6 flex items-start gap-3">
                        <i class="fas fa-check-circle mt-1"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <div class="text-center py-5">
                    <div class="mx-auto w-20 h-20 rounded-full bg-emerald-100 flex items-center justify-center mb-5">
                        <i class="fas fa-check-circle text-emerald-600 text-4xl"></i>
                    </div>

                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold mb-3 {{ $badge[1] }}">
                        {{ $badge[0] }}
                    </span>

                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Bukti Pembayaran Berhasil Dikirim</h2>
                    <p class="text-gray-600 max-w-lg mx-auto">
                        Bukti transfer Anda sudah tersimpan. Tim kami akan melakukan verifikasi pembayaran terlebih dahulu.
                    </p>

                    <div class="mt-5 inline-flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-lg px-4 py-3 text-sm text-gray-600">
                        <i class="fas fa-clock text-gray-400"></i>
                        Dikirim {{ $sentAt }} WIB
                    </div>

                    <div class="mt-7 flex flex-col sm:flex-row gap-3 justify-center">
                        <a href="{{ $detailUrl }}"
                           class="inline-flex items-center justify-center bg-[#B4775E] text-white py-3 px-6 rounded-lg font-semibold hover:bg-[#9C6650] transition-colors">
                            <i class="fas fa-file-invoice mr-2"></i>
                            Lihat Detail Pesanan
                        </a>
                        @if ($canReplace)
                            <button type="button" id="replaceProof"
                                    class="inline-flex items-center justify-center bg-gray-100 text-gray-700 py-3 px-6 rounded-lg font-semibold hover:bg-gray-200 transition-colors">
                                <i class="fas fa-sync-alt mr-2"></i>
                                Ganti Bukti Pembayaran
                            </button>
                        @endif
                    </div>

                    <div class="mt-6 bg-amber-50 border border-amber-200 rounded-xl p-4 text-left">
                        <div class="flex gap-3">
                            <i class="fas fa-info-circle text-amber-600 mt-1"></i>
                            <p class="text-sm text-amber-800">
                                @if ($status === 'verified')
                                    Pembayaran Anda telah <strong>diverifikasi</strong> oleh admin. Terima kasih.
                                @elseif ($status === 'rejected')
                                    Bukti pembayaran belum dapat kami terima. Silakan klik <strong>Ganti Bukti Pembayaran</strong> dan unggah bukti yang lebih jelas.
                                @else
                                    Status akan berubah menjadi <strong>Pembayaran Berhasil</strong> setelah bukti pembayaran diverifikasi oleh admin.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- ============ FORM UPLOAD ============ --}}
        <div id="uploadState" class="{{ $showForm ? '' : 'hidden' }}">
            <form method="POST" action="{{ $postUrl }}" enctype="multipart/form-data" id="paymentForm" novalidate class="space-y-6">
                @csrf

                <div>
                    <label for="order_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Order ID <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="order_id" value="{{ $order->order_number }}" readonly
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-100 text-gray-600">
                </div>

                <div>
                    <label for="proof" class="block text-sm font-medium text-gray-700 mb-2">
                        Bukti Transfer <span class="text-red-500">*</span>
                    </label>
                    <label for="proof"
                           class="mt-1 flex justify-center px-6 pt-7 pb-7 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer hover:border-[#B4775E] hover:bg-[#B4775E]/5 transition-colors">
                        <div class="space-y-2 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <div class="text-sm text-gray-600">
                                <span class="font-semibold text-[#B4775E]">Upload bukti transfer</span>
                                <span> atau pilih file</span>
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG, PDF hingga 2MB</p>
                            <input id="proof" name="proof" type="file" class="sr-only" accept="image/*,.pdf">
                        </div>
                    </label>

                    <div id="fileChosen" class="hidden mt-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-2"></i>
                            <span class="text-sm text-green-800 break-all">File terpilih: <span id="fileName"></span></span>
                        </div>
                    </div>

                    <p id="proofError" class="text-red-500 text-xs mt-1">@error('proof'){{ $message }}@enderror</p>
                </div>

                <div class="flex flex-col space-y-3">
                    <button type="submit" id="submitPayment"
                            class="w-full bg-[#B4775E] text-white py-3 px-6 rounded-lg font-semibold hover:bg-[#9C6650] transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed">
                        <span id="submitIdle"><i class="fas fa-upload mr-2"></i>Kirim Bukti Pembayaran</span>
                        <span id="submitBusy" class="hidden"><i class="fas fa-spinner fa-spin mr-2"></i>Mengirim...</span>
                    </button>

                   <a href="{{ $detailUrl }}"
   class="w-full bg-gray-100 text-gray-700 py-3 px-6 rounded-lg font-semibold hover:bg-gray-200 transition-colors text-center inline-flex items-center justify-center gap-2">
    <!-- Ikon Panah Kiri -->
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
    </svg>
    Kembali ke Detail Order 
</a>
                </div>
            </form>

            <div class="mt-8 bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                <div class="flex items-start gap-3">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-lg mt-1"></i>
                    <div>
                        <h3 class="font-medium text-yellow-800">Petunjuk Upload Bukti Transfer</h3>
                        <ul class="text-sm text-yellow-700 mt-2 space-y-1">
                            <li>• Pastikan bukti transfer terlihat jelas dan lengkap</li>
                            <li>• Format JPG, PNG, atau PDF</li>
                            <li>• Maksimal ukuran file 2MB</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form    = document.getElementById('paymentForm');
    const file    = document.getElementById('proof');
    const name    = document.getElementById('fileName');
    const chosen  = document.getElementById('fileChosen');
    const err     = document.getElementById('proofError');
    const btn     = document.getElementById('submitPayment');
    const MAX     = 2 * 1024 * 1024;

    // "Ganti Bukti Pembayaran" -> tampilkan form upload
    const replaceBtn = document.getElementById('replaceProof');
    if (replaceBtn) replaceBtn.addEventListener('click', () => {
        document.getElementById('submittedState').classList.add('hidden');
        document.getElementById('uploadState').classList.remove('hidden');
    });

    file.addEventListener('change', () => {
        const f = file.files[0];
        err.textContent = '';
        if (f && f.size > MAX) {
            err.textContent = 'Ukuran bukti transfer maksimal 2MB.';
            file.value = '';
            chosen.classList.add('hidden');
            return;
        }
        name.textContent = f ? f.name : '';
        chosen.classList.toggle('hidden', !f);
    });

    form.addEventListener('submit', (e) => {
        if (!file.files.length) {
            e.preventDefault();
            err.textContent = 'Bukti transfer wajib diunggah.';
            return;
        }
        btn.disabled = true;
        document.getElementById('submitIdle').classList.add('hidden');
        document.getElementById('submitBusy').classList.remove('hidden');
    });
});
</script>
@endsection