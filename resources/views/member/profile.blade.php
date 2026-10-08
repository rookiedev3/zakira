@extends('layouts.app')

@section('title', 'Akun Saya — Zakira Moslem Hijab Identity')

@section('content')
@php
    use Illuminate\Support\Facades\Storage;

    $statusLabels  = ['pending' => 'Menunggu', 'processing' => 'Diproses', 'shipped' => 'Dikirim', 'delivered' => 'Terkirim', 'cancelled' => 'Dibatalkan'];
    $statusClasses = [
        'pending'    => 'bg-[#fef9c2] text-[#894b00]',
        'processing' => 'bg-[#dbeafe] text-[#193cb8]',
        'shipped'    => 'bg-purple-100 text-purple-800',
        'delivered'  => 'bg-[#dcfce7] text-[#016630]',
        'cancelled'  => 'bg-red-100 text-red-800',
    ];
    $paymentLabels  = ['pending' => 'Menunggu', 'paid' => 'Lunas', 'failed' => 'Gagal', 'refunded' => 'Dikembalikan'];
    $paymentClasses = [
        'pending'  => 'bg-[#fef9c2] text-[#894b00]',
        'paid'     => 'bg-[#dcfce7] text-[#016630]',
        'failed'   => 'bg-red-100 text-red-800',
        'refunded' => 'bg-gray-100 text-gray-800',
    ];
    $rp  = fn ($n) => 'Rp ' . number_format((int) $n, 0, ',', '.');
    $wib = fn ($date, $format = 'd M Y') => $date ? \Illuminate\Support\Carbon::parse($date)->timezone('Asia/Jakarta')->format($format) : '-';
@endphp

<div class="zakira-container">
<!-- Wrapper x-data: kontrol semua modal. openDetail menyimpan ID pesanan yang sedang dibuka (null = tertutup) -->
<div class="px-4 py-12 w-full"
     x-data="{
        openEdit: {{ $errors->hasAny(['name','email','phone','address','city','province','postal_code','seller_id','shipping_expedition']) ? 'true' : 'false' }},
        openPass: {{ $errors->hasAny(['current_password','password','password_confirmation']) ? 'true' : 'false' }},
        openDetail: null
     }">

    <!-- Judul Halaman -->
    <div class="mb-10">
        <h1 class="text-4xl font-semibold text-gray-900">Akun Saya</h1>
        <p class="text-gray-600 text-lg mt-3">Kelola profil dan riwayat pesanan Anda</p>
    </div>

    <!-- Flash message (error validasi tampil di bawah masing-masing input) -->
    @if (session('success'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm p-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 text-sm p-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <!-- Layout Grid Utama (Kiri: Profil, Kanan: Riwayat Pesanan) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

        <!-- KOLOM KIRI: PROFIL SAYA & STATISTIK -->
        <div class="lg:col-span-1 space-y-7">

            <!-- Card Informasi Profil -->
            <div class="bg-white p-7 rounded-2xl border border-gray-100 shadow-sm relative z-20">
                <div class="flex justify-between items-center mb-7">
                    <h3 class="text-[1.35rem] font-semibold text-gray-900">Profil Saya</h3>

                    <div class="flex items-center gap-8 text-base relative z-30">
                        <button type="button" @click="openEdit = true" class="text-gray-800 cursor-pointer hover:underline bg-transparent border-none p-0 focus:outline-none">
                            Profil
                        </button>
                        <button type="button" @click="openPass = true" class="text-gray-800 cursor-pointer hover:underline bg-transparent border-none p-0 focus:outline-none">
                            Password
                        </button>
                    </div>
                </div>

                <!-- Avatar Inisial -->
                <div class="flex flex-col items-center text-center pb-7 border-b border-gray-200">
                    <div class="w-24 h-24 bg-[#d1d5dc] text-[#364153] rounded-full flex items-center justify-center font-bold text-3xl mb-5">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <h4 class="font-medium text-gray-900 text-xl">{{ $user->name }}</h4>
                    <p class="text-gray-600 text-lg mt-1">{{ $user->email }}</p>
                </div>

                <!-- Detail Data Diri -->
                <div class="pt-6 pb-2 space-y-5">
                    <div>
                        <span class="text-gray-600 block text-base">Nama:</span>
                        <span class="font-medium text-gray-900 text-lg">{{ $user->name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600 block text-base">Email:</span>
                        <span class="font-medium text-gray-900 text-lg">{{ $user->email }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600 block text-base">Telepon:</span>
                        <span class="font-medium text-gray-900 text-lg">{{ $user->detail?->phone ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600 block text-base">Alamat:</span>
                        <span class="font-medium text-gray-900 text-lg">{{ $user->detail?->address ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600 block text-base">Kota:</span>
                        <span class="font-medium text-gray-900 text-lg">{{ $user->detail?->city ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600 block text-base">Provinsi:</span>
                        <span class="font-medium text-gray-900 text-lg">{{ $user->detail?->province ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600 block text-base">Kode Pos:</span>
                        <span class="font-medium text-gray-900 text-lg">{{ $user->detail?->postal_code ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600 block text-base">ID Seller:</span>
                        <span class="font-medium text-gray-900 text-lg">{{ $user->detail?->seller_id ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600 block text-base">Ekspedisi Favorit:</span>
                        <span class="font-medium text-gray-900 text-lg">{{ $user->detail?->shipping_expedition ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600 block text-base">Bergabung:</span>
                        <span class="font-medium text-gray-900 text-lg">{{ $user->created_at?->format('d M Y') ?? '-' }}</span>
                    </div>
                </div>

                <!-- Tombol Logout -->
                <div class="pt-6 border-t border-gray-200 mt-4">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-[#fb2c36] hover:bg-[#e7000b] text-white text-base font-medium py-3 rounded-lg transition flex items-center justify-center gap-2 cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </div>

            <!-- Card Statistik Pesanan -->
            <div class="bg-white p-7 rounded-2xl border border-gray-100 shadow-sm text-base">
                <h3 class="text-[1.35rem] font-semibold text-gray-900 mb-4">Statistik Pesanan</h3>
                <div class="flex justify-between py-3">
                    <span class="text-gray-600">Total Pesanan:</span>
                    <span class="text-gray-900 font-medium">{{ $stats['total'] }}</span>
                </div>
                <div class="flex justify-between py-3">
                    <span class="text-gray-600">Pesanan Aktif:</span>
                    <span class="text-gray-900 font-medium">{{ $stats['active'] }}</span>
                </div>
                <div class="flex justify-between py-3">
                    <span class="text-gray-600">Pesanan Selesai:</span>
                    <span class="text-gray-900 font-medium">{{ $stats['done'] }}</span>
                </div>
                <div class="flex justify-between items-center pt-4 mt-2 border-t border-gray-200">
                    <span class="text-gray-600">Total Belanja:</span>
                    <span class="font-medium text-[#00a63e] text-lg">{{ $rp($stats['spent']) }}</span>
                </div>
            </div>

        </div>

        <!-- KOLOM KANAN: RIWAYAT PESANAN -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <h3 class="text-[1.35rem] font-semibold text-gray-900 px-7 py-5 border-b border-gray-200">Riwayat Pesanan</h3>

                <div class="divide-y divide-gray-200">
                    @forelse ($orders as $order)
                        @php
                            $isFull     = $order->payment_method === 'full';
                            $dpPaid     = (bool) $order->dp_paid_at;
                            $remPaid    = (bool) $order->remaining_paid_at;
                            $payKey     = $order->payment_status ?? 'pending';
                            $invoice    = $order->invoice;
                            $hasInvoice = (bool) $invoice;
                            $isExcel    = $invoice?->format === 'excel';
                            $cancelled  = $order->status === 'cancelled';

                            // Tombol bayar
                            $canPayDp        = ! $isFull && ! $dpPaid && ! $cancelled;
                            $canPayRemaining = ! $isFull && $dpPaid && ! $remPaid && ! $cancelled;

                            // Masa edit: aturan ada di model CustomerOrder::editBlockReason()
                            // (pending, bukti transfer DP belum dikirim, faktur belum ada, masih dalam 72 jam)
                            $editOpen  = $order->isEditable();
                            $editBlock = $order->editBlockReason();
                            $editCount = (int) ($order->edit_count ?? 0);
                            $secsLeft  = $order->editSecondsLeft();
                            $hLeft     = intdiv($secsLeft, 3600);
                            $mLeft     = intdiv($secsLeft % 3600, 60);
                        @endphp

                        <div class="bg-gray-50 px-7 py-6 space-y-5">

                            <!-- Header Pesanan -->
                            <div class="flex justify-between items-center gap-3">
                                <span class="text-xl font-medium text-gray-900">Order #{{ $order->order_number }}</span>
                                <span class="{{ $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-800' }} text-base font-medium px-4 py-1.5 rounded-full">
                                    {{ $statusLabels[$order->status] ?? ucfirst($order->status) }}
                                </span>
                            </div>

                            <!-- Baris 1: Tanggal, Items, Total, Pembayaran -->
                            <div class="grid grid-cols-2 lg:grid-cols-4 gap-x-4 gap-y-5 text-base">
                                <div>
                                    <span class="text-gray-600 block">Tanggal:</span>
                                    <span class="text-gray-500">{{ $wib($order->created_at) }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600 block">Items:</span>
                                    <span class="text-gray-500">{{ $order->items_count }} item(s)</span>
                                </div>
                                <div>
                                    <span class="text-gray-600 block">Total:</span>
                                    <span class="font-semibold text-gray-900">{{ $rp($order->total) }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600 block">Pembayaran:</span>
                                    <span class="inline-block mt-1 {{ $paymentClasses[$payKey] ?? 'bg-gray-100 text-gray-800' }} text-sm font-medium px-3 py-0.5 rounded-full">
                                        {{ $paymentLabels[$payKey] ?? ucfirst($payKey) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Baris 2: Metode Bayar, DP Status, Sisa Bayar, Faktur -->
                            <div class="grid grid-cols-2 lg:grid-cols-4 gap-x-4 gap-y-5 text-base">
                                <div>
                                    <span class="text-gray-600 block">Metode Bayar:</span>
                                    @if ($isFull)
                                        <span class="inline-flex items-center gap-1.5 mt-1 bg-[#dcfce7] text-[#016630] text-sm font-medium px-3 py-1 rounded-full">
                                            <i class="fas fa-money-bill text-xs"></i> Bayar Penuh
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 mt-1 bg-[#dbeafe] text-[#193cb8] text-sm font-medium px-3 py-1 rounded-full">
                                            <i class="fas fa-credit-card text-xs"></i> Down Payment
                                        </span>
                                    @endif
                                </div>

                                <div>
                                    <span class="text-gray-600 block">DP Status:</span>
                                    @if ($isFull)
                                        <span class="text-gray-500">-</span>
                                    @elseif ($dpPaid)
                                        <span class="inline-flex items-center gap-1.5 mt-1 bg-[#dcfce7] text-[#016630] text-sm font-medium px-3 py-1 rounded-full">
                                            <i class="fas fa-circle-check text-[#008236]"></i> DP Lunas
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 mt-1 bg-[#fef9c2] text-[#894b00] text-sm font-medium px-3 py-1 rounded-full">
                                            <i class="fas fa-clock"></i> DP Pending
                                        </span>
                                    @endif
                                </div>

                                <div>
                                    <span class="text-gray-600 block">Sisa Bayar:</span>
                                    @if ($isFull)
                                        <span class="text-gray-500">-</span>
                                    @elseif ($remPaid)
                                        <span class="inline-flex items-center gap-1.5 mt-1 bg-[#dcfce7] text-[#016630] text-sm font-medium px-3 py-1 rounded-full">
                                            <i class="fas fa-circle-check text-[#008236]"></i> Lunas
                                        </span>
                                    @elseif ($dpPaid)
                                        <span class="inline-flex items-center gap-1.5 mt-1 bg-orange-100 text-orange-800 text-sm font-medium px-3 py-1 rounded-full">
                                            <i class="fas fa-hourglass-half"></i> Menunggu
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 mt-1 bg-gray-100 text-gray-600 text-sm font-medium px-3 py-1 rounded-full">
                                            <i class="fas fa-lock"></i> Tunggu DP
                                        </span>
                                    @endif
                                </div>

                                <div>
                                    <span class="text-gray-600 block">Faktur:</span>
                                    @if ($hasInvoice)
                                        <span class="inline-flex items-center gap-2 mt-1 bg-[#dbeafe] text-[#1447e6] text-sm px-3 py-1.5 rounded-2xl">
                                            <i class="fas {{ $isExcel ? 'fa-file-excel' : 'fa-file-pdf' }}"></i> {{ $invoice->invoice_number }} ({{ $isExcel ? 'Excel' : 'PDF' }})
                                        </span>
                                        <span class="text-sm text-gray-500 block mt-1">{{ $wib($invoice->invoice_date ?? $invoice->created_at, 'd M Y H:i') }}</span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 mt-1 bg-gray-100 text-gray-600 text-sm font-medium px-3 py-1 rounded-full">
                                            <i class="fas fa-clock"></i> Belum dibuat
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Edit: sisa waktu jika masih bisa diedit, alasan jika tidak (mis. bukti DP sudah dikirim) -->
                            @if (! $cancelled)
                                <div class="text-base">
                                    <span class="text-gray-600 block">Edit:</span>
                                    @if ($editOpen)
                                        <span class="text-gray-900 font-medium">{{ $hLeft }} jam {{ $mLeft }} menit tersisa</span>
                                    @else
                                        <span class="text-gray-500">{{ $editBlock }}</span>
                                    @endif
                                    @if ($editCount > 0)
                                        <span class="text-sm text-gray-500 block mt-0.5">Sudah diedit {{ $editCount }}x</span>
                                    @endif
                                </div>
                            @endif

                            <!-- Tombol Aksi -->
                            <div class="flex flex-wrap items-center gap-3 pt-1">
                                <button type="button" @click="openDetail = {{ $order->id }}" class="bg-[#8C6239] hover:bg-[#724e2c] text-white text-base font-medium px-6 py-2.5 rounded-md transition flex items-center gap-2 cursor-pointer">
                                    <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z" /><path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 0 1 0-1.186A10.004 10.004 0 0 1 10 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0 1 10 17c-4.257 0-7.893-2.66-9.336-6.41ZM14 10a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z" clip-rule="evenodd" /></svg>
                                    Lihat Detail
                                </button>

                                {{-- Bayar DP (biru): metode DP & DP belum lunas --}}
                                @if ($canPayDp)
                                    <a href="{{ route('member.orders.pay-dp', $order->order_number) }}"
                                       class="bg-blue-600 hover:bg-blue-700 text-white text-base font-medium px-6 py-2.5 rounded-md transition flex items-center gap-2 cursor-pointer">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" /></svg>
                                        Bayar DP
                                    </a>
                                @endif

                                {{-- Edit Pesanan: hanya selama pending, bukti DP belum dikirim, dan masih dalam masa edit 72 jam --}}
                                @if ($editOpen)
                                    <a href="{{ route('member.order.edit', $order->order_number) }}"
                                       class="bg-white hover:bg-gray-100 text-gray-800 border border-gray-300 text-base font-medium px-6 py-2.5 rounded-md transition flex items-center gap-2 cursor-pointer">
                                        <i class="fas fa-pen-to-square"></i>
                                        Edit Pesanan
                                    </a>
                                @endif

                                {{-- Bayar Sisa: DP sudah disetujui & sisa belum lunas --}}
                                @if ($canPayRemaining)
                                    <a href="{{ route('member.orders.pay-remaining', $order->order_number) }}"
                                       class="bg-green-600 hover:bg-green-700 text-white text-base font-medium px-6 py-2.5 rounded-md transition flex items-center gap-2 cursor-pointer">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" /></svg>
                                        Bayar Sisa
                                    </a>
                                @endif

                                {{-- Download: hanya jika faktur ada DAN DP sudah disetujui --}}
                                @if ($hasInvoice && $dpPaid)
                                    <a href="{{ route('member.orders.invoice', $order->order_number) }}" download
                                       class="bg-gray-100 hover:bg-gray-200 text-gray-800 text-base font-medium px-6 py-2.5 rounded-md transition flex items-center gap-2 cursor-pointer">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                                        Download {{ $isExcel ? 'Excel' : 'PDF' }}
                                    </a>
                                @endif
                            </div>

                        </div>
                    @empty
                        <div class="bg-gray-50 px-7 py-14 text-center text-gray-500 text-base">
                            Belum ada pesanan.
                        </div>
                    @endforelse
                </div>

                {{-- Pagination gaya "Showing 1 to 10 of 23 results" --}}
                <div class="px-7 py-4 border-t border-gray-200">
                    {{ $orders->links('vendor.pagination.zakira') }}
                </div>
            </div>
        </div>

    </div>

    <!-- ================= MODAL EDIT PROFIL ================= -->
    <div x-show="openEdit" class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4" style="display: none;" x-transition.opacity>
        <div @click.outside="openEdit = false" class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl border border-gray-200">

            <div class="flex justify-between items-center p-6 border-b border-gray-100 sticky top-0 bg-white z-10">
                <h3 class="font-bold text-lg text-gray-900">Edit Profil</h3>
                <button type="button" @click="openEdit = false" class="text-gray-400 hover:text-gray-600 p-1 rounded-full hover:bg-gray-100 transition cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6 text-xs">
                <form action="{{ route('member.profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4">
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Nama</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-1 text-sm {{ $errors->has('name') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-amber-800' }}">
                            @error('name') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-1 text-sm {{ $errors->has('email') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-amber-800' }}">
                            @error('email') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Telepon</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->detail?->phone) }}"
                                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-1 text-sm {{ $errors->has('phone') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-amber-800' }}">
                            @error('phone') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Alamat</label>
                            <textarea name="address" rows="3"
                                      class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-1 text-sm {{ $errors->has('address') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-amber-800' }}">{{ old('address', $user->detail?->address) }}</textarea>
                            @error('address') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-semibold text-gray-700 mb-1">Kota / Kabupaten</label>
                                <input type="text" name="city" value="{{ old('city', $user->detail?->city) }}"
                                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-1 text-sm {{ $errors->has('city') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-amber-800' }}">
                                @error('city') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-700 mb-1">Provinsi</label>
                                <select name="province"
                                        class="w-full px-3 py-2 border rounded-lg bg-white focus:outline-none focus:ring-1 text-sm {{ $errors->has('province') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-amber-800' }}">
                                    <option value="">- Pilih -</option>
                                    @foreach ($provinces as $prov)
                                        <option value="{{ $prov }}" @selected(old('province', $user->detail?->province) === $prov)>
                                            {{ $prov }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('province') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-semibold text-gray-700 mb-1">Kode Pos</label>
                                <input type="text" name="postal_code" value="{{ old('postal_code', $user->detail?->postal_code) }}"
                                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-1 text-sm {{ $errors->has('postal_code') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-amber-800' }}">
                                @error('postal_code') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-700 mb-1">ID Seller</label>
                                <input type="text" name="seller_id" value="{{ old('seller_id', $user->detail?->seller_id) }}"
                                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-1 text-sm {{ $errors->has('seller_id') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-amber-800' }}">
                                @error('seller_id') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Ekspedisi Favorit</label>
                            <select name="shipping_expedition"
                                    class="w-full px-3 py-2 border rounded-lg bg-white focus:outline-none focus:ring-1 text-sm {{ $errors->has('shipping_expedition') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-amber-800' }}">
                                <option value="">- Pilih -</option>
                                @foreach ($shipping as $exp)
                                    <option value="{{ $exp }}" @selected(old('shipping_expedition', $user->detail?->shipping_expedition) === $exp)>
                                        {{ $exp }}
                                    </option>
                                @endforeach
                            </select>
                            @error('shipping_expedition') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-3 rounded-lg text-xs leading-relaxed">
                            Data ini otomatis masuk ke Checkout. Kota, provinsi dan kode pos juga dipakai untuk menghitung ongkir.
                        </div>
                    </div>

                    <div class="pt-6 mt-6 border-t border-gray-100 flex justify-end gap-3">
                        <button type="button" @click="openEdit = false" class="px-4 py-2 border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg font-medium transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 bg-[#8C6239] hover:bg-[#724e2c] text-white rounded-lg font-medium transition cursor-pointer">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <!-- ================= MODAL UBAH PASSWORD ================= -->
    <div x-show="openPass" class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4" style="display: none;" x-transition.opacity>
        <div @click.outside="openPass = false" class="bg-white rounded-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto shadow-2xl border border-gray-200">

            <div class="flex justify-between items-center p-6 border-b border-gray-100 sticky top-0 bg-white z-10">
                <h3 class="font-bold text-lg text-gray-900">Ubah Password</h3>
                <button type="button" @click="openPass = false" class="text-gray-400 hover:text-gray-600 p-1 rounded-full hover:bg-gray-100 transition cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6 text-xs">
                <form action="{{ route('member.password.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4">
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Password Saat Ini</label>
                            <input type="password" name="current_password"
                                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-1 text-sm {{ $errors->has('current_password') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-amber-800' }}">
                            @error('current_password') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Password Baru</label>
                            <input type="password" name="password"
                                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-1 text-sm {{ $errors->has('password') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-amber-800' }}">
                            @error('password') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation"
                                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-1 text-sm {{ $errors->has('password_confirmation') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-amber-800' }}">
                            @error('password_confirmation') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="pt-6 mt-6 border-t border-gray-100 flex justify-end items-center gap-3">
                        <button type="button" @click="openPass = false" class="text-sm font-medium text-gray-700 hover:underline cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 bg-[#8C6239] hover:bg-[#724e2c] text-white text-xs rounded-lg font-medium transition cursor-pointer">
                            Ubah Password
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <!-- ================= MODAL DETAIL PESANAN (satu per pesanan) ================= -->
    @foreach ($orders as $order)
        @php
            $isFull    = $order->payment_method === 'full';
            $dpPaid    = (bool) $order->dp_paid_at;
            $remPaid   = (bool) $order->remaining_paid_at;
            $payKey    = $order->payment_status ?? 'pending';
            $remaining = max(0, (int) $order->total - (int) $order->amount_due);
            $discount  = (int) $order->discount;
        @endphp

        <div x-show="openDetail === {{ $order->id }}" class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4" style="display: none;" x-transition.opacity>
            <div @click.outside="openDetail = null" class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl border border-gray-200">

                <div class="flex justify-between items-center p-6 border-b border-gray-100 sticky top-0 bg-white z-10">
                    <h3 class="font-bold text-lg text-gray-900">Detail Pesanan #{{ $order->order_number }}</h3>
                    <button type="button" @click="openDetail = null" class="text-gray-400 hover:text-gray-600 p-1 rounded-full hover:bg-gray-100 transition cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-6 space-y-6 text-xs">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                        <div class="space-y-2">
                            <h4 class="font-bold text-gray-800 text-sm mb-3">Informasi Pesanan</h4>
                            <div class="flex justify-between"><span class="text-gray-500">Order ID:</span> <span class="font-medium text-gray-800">{{ $order->order_number }}</span></div>
                            <div class="flex justify-between"><span class="text-gray-500">Tanggal:</span> <span class="font-medium text-gray-800">{{ $wib($order->created_at, 'd M Y H:i') }}</span></div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500">Status:</span>
                                <span class="{{ $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-800' }} font-semibold px-2.5 py-0.5 rounded-full text-[10px]">{{ $statusLabels[$order->status] ?? ucfirst($order->status) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500">Pembayaran:</span>
                                <span class="{{ $paymentClasses[$payKey] ?? 'bg-gray-100 text-gray-800' }} font-semibold px-2 py-0.5 rounded text-[10px]">{{ $paymentLabels[$payKey] ?? ucfirst($payKey) }}</span>
                            </div>
                            <div class="flex justify-between"><span class="text-gray-500">Metode Bayar:</span> <span class="font-medium text-gray-800">{{ $isFull ? 'Bayar Penuh' : 'Down Payment' }}</span></div>
                            <div class="flex justify-between"><span class="text-gray-500">Subtotal:</span> <span class="font-medium text-gray-800">{{ $rp($order->subtotal) }}</span></div>
                            @if ($discount > 0)
                                <div class="flex justify-between text-emerald-700">
                                    <span>Diskon{{ $order->coupon_code ? ' (' . $order->coupon_code . ')' : '' }}:</span>
                                    <span class="font-medium">-{{ $rp($discount) }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between border-t pt-2 mt-2"><span class="text-gray-500">Total:</span> <span class="font-bold text-emerald-700 text-sm">{{ $rp($order->total) }}</span></div>
                            @unless ($isFull)
                                <div class="flex justify-between"><span class="text-gray-500">DP:</span> <span class="font-medium text-gray-800">{{ $rp($order->amount_due) }} {{ $dpPaid ? '(Lunas)' : '(Pending)' }}</span></div>
                                <div class="flex justify-between"><span class="text-gray-500">Sisa:</span> <span class="font-medium text-gray-800">{{ $rp($remaining) }} {{ $remPaid ? '(Lunas)' : '(Belum)' }}</span></div>
                            @endunless
                        </div>

                        <!-- Informasi pengiriman: diambil dari data pesanan -->
                        <div class="space-y-2">
                            <h4 class="font-bold text-gray-800 text-sm mb-3">Informasi Pengiriman</h4>
                            <div class="flex justify-between"><span class="text-gray-500">Nama:</span> <span class="font-medium text-gray-800">{{ trim($order->first_name . ' ' . $order->last_name) }}</span></div>
                            <div class="flex justify-between"><span class="text-gray-500">Email:</span> <span class="font-medium text-gray-800">{{ $order->email ?: '-' }}</span></div>
                            <div class="flex justify-between"><span class="text-gray-500">WhatsApp:</span> <span class="font-medium text-gray-800">{{ $order->whatsapp_number ?: '-' }}</span></div>
                            <div class="flex justify-between"><span class="text-gray-500">Ekspedisi:</span> <span class="font-medium text-gray-800">{{ $order->shipping_method ?: '-' }}</span></div>
                            <div>
                                <span class="text-gray-500 block mb-1">Alamat:</span>
                                <p class="font-medium text-gray-800 leading-relaxed">
                                    {{ $order->address }}<br>
                                    {{ $order->city }}, {{ $order->province }} {{ $order->postal_code }}<br>
                                    Indonesia
                                </p>
                            </div>
                            @if ($order->notes)
                                <div>
                                    <span class="text-gray-500 block mb-1">Catatan:</span>
                                    <p class="font-medium text-gray-800 leading-relaxed">{{ $order->notes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-3">
                        <h4 class="font-bold text-gray-800 text-sm">Item Pesanan</h4>
                        @forelse ($order->items as $item)
                            @php
                                $name  = $item->product_name ?? $item->product->name ?? '-';
                                $image = $item->product_image ?? $item->product->image ?? null;
                                $price = (int) $item->price;
                                $qty   = (int) $item->quantity;
                                $attrs = array_filter([
                                    ! empty($item->model) ? 'Model: '  . $item->model : null,
                                    ! empty($item->color) ? 'Warna: '  . $item->color : null,
                                    ! empty($item->size)  ? 'Ukuran: ' . $item->size  : null,
                                ]);
                            @endphp
                            <div class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-xl shadow-sm">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-gray-100 rounded-lg overflow-hidden flex items-center justify-center text-gray-400 shrink-0">
                                        @if ($image)
                                            <img src="{{ Storage::disk('public')->url($image) }}" alt="{{ $name }}" class="w-full h-full object-cover">
                                        @else
                                            📷
                                        @endif
                                    </div>
                                    <div>
                                        <h5 class="font-semibold text-gray-800">{{ $name }}</h5>
                                        @if ($attrs)
                                            <p class="text-[11px] text-gray-500">{{ implode(' | ', $attrs) }}</p>
                                        @endif
                                        <p class="text-[11px] text-gray-500">{{ $rp($price) }} × {{ $qty }}</p>
                                    </div>
                                </div>
                                <span class="font-bold text-gray-800 text-sm">{{ $rp($price * $qty) }}</span>
                            </div>
                        @empty
                            <p class="text-gray-500">Tidak ada item.</p>
                        @endforelse
                    </div>
                </div>

                <div class="p-4 border-t border-gray-100 flex justify-end bg-gray-50 rounded-b-2xl">
                    <button type="button" @click="openDetail = null" class="bg-gray-800 hover:bg-gray-900 text-white text-xs font-semibold px-5 py-2 rounded-lg transition cursor-pointer">
                        Tutup
                    </button>
                </div>

            </div>
        </div>
    @endforeach

</div>
</div>
@endsection