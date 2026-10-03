@extends('layouts.sidebar')

@section('title', 'Manage Orders')

@section('content')
@php
    use Illuminate\Support\Facades\Storage;

    $statusLabels  = ['pending' => 'Menunggu', 'processing' => 'Diproses', 'shipped' => 'Dikirim', 'delivered' => 'Terkirim', 'cancelled' => 'Dibatalkan'];
    $statusClasses = [
        'pending'    => 'bg-yellow-100 text-yellow-800',
        'processing' => 'bg-blue-100 text-blue-800',
        'shipped'    => 'bg-purple-100 text-purple-800',
        'delivered'  => 'bg-green-100 text-green-800',
        'cancelled'  => 'bg-red-100 text-red-800',
    ];
    $paymentLabels  = ['pending' => 'Menunggu', 'paid' => 'Lunas', 'failed' => 'Gagal', 'refunded' => 'Dikembalikan'];
    $paymentClasses = [
        'pending'  => 'bg-yellow-100 text-yellow-800',
        'paid'     => 'bg-green-100 text-green-800',
        'failed'   => 'bg-red-100 text-red-800',
        'refunded' => 'bg-gray-100 text-gray-800',
    ];

    $inputClass    = 'w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500';
    $labelClass    = 'block text-sm font-medium text-gray-700 mb-2';
    $thClass       = 'px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider';

    // Menu dropdown
    $menuItemClass = 'flex items-center px-2 py-1.5 w-full rounded-md text-start text-sm font-medium text-zinc-800 hover:bg-zinc-100';
    $menuClass     = 'min-w-48 p-[.3125rem] rounded-lg shadow-lg border border-zinc-200 bg-white z-50';

    // Tombol aksi (tiap warna punya variabel sendiri supaya tidak bentrok)
    $btnBase   = 'inline-flex items-center justify-center gap-1 whitespace-nowrap h-8 px-3 text-sm font-medium rounded-md border cursor-pointer list-none transition-colors [&::-webkit-details-marker]:hidden';
    $btnView   = $btnBase . ' bg-gray-100 border-gray-300 text-gray-700 hover:bg-gray-200';
    $btnStatus = $btnBase . ' bg-blue-50 border-blue-200 text-blue-700 hover:bg-blue-100';
    $btnPay    = $btnBase . ' bg-green-50 border-green-200 text-green-700 hover:bg-green-100';
    $btnDelete = $btnBase . ' bg-red-50 border-red-200 text-red-700 hover:bg-red-100';

    $chevron       = '<svg class="shrink-0 ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>';

    $sel = fn (string $key, string $value) => (string) request($key) === $value ? 'selected' : '';
    $rp  = fn ($n) => 'Rp ' . number_format((int) $n, 0, ',', '.');

    // Nilai awal form Buat Faktur
    $nextInvoiceNumber = \App\Models\Invoice::nextNumber();
    $nowLocal          = now('Asia/Jakarta')->format('Y-m-d\TH:i');
@endphp

<div class="space-y-6">

    @if (session('success'))
        <div id="flash-toast" class="fixed top-4 right-4 z-[60] rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 shadow-lg transition-opacity duration-500">
            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
        </div>
    @endif

    <!-- Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-900">Kelola Pesanan</h1>
        <div class="text-sm text-gray-500">
            Total: {{ $orders->total() }} pesanan
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('admin.orders.index') }}" id="filterForm" class="bg-white shadow rounded-lg p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="search" class="{{ $labelClass }}">Cari Pesanan</label>
                <input type="text" id="search" name="search" value="{{ request('search') }}"
                    placeholder="Order ID, nama, WhatsApp..." class="{{ $inputClass }}">
            </div>

            <div>
                <label for="status_filter" class="{{ $labelClass }}">Status Pesanan</label>
                <select id="status_filter" name="status_filter" class="{{ $inputClass }}" data-autosubmit>
                    <option value="">Semua Status</option>
                    @foreach ($statusLabels as $value => $label)
                        <option value="{{ $value }}" {{ $sel('status_filter', $value) }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="payment_status_filter" class="{{ $labelClass }}">Status Pembayaran</label>
                <select id="payment_status_filter" name="payment_status_filter" class="{{ $inputClass }}" data-autosubmit>
                    <option value="">Semua Status</option>
                    @foreach ($paymentLabels as $value => $label)
                        <option value="{{ $value }}" {{ $sel('payment_status_filter', $value) }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="dp_status_filter" class="{{ $labelClass }}">Status DP</label>
                <select id="dp_status_filter" name="dp_status_filter" class="{{ $inputClass }}" data-autosubmit>
                    <option value="">Semua Status</option>
                    @foreach (['dp_pending' => 'DP Pending', 'dp_paid' => 'DP Lunas', 'remaining_pending' => 'Sisa Pending', 'fully_paid' => 'Lunas Semua', 'full_payment' => 'Bayar Penuh'] as $value => $label)
                        <option value="{{ $value }}" {{ $sel('dp_status_filter', $value) }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="brand_filter" class="{{ $labelClass }}">Brand Produk</label>
                <select id="brand_filter" name="brand_filter" class="{{ $inputClass }}" data-autosubmit>
                    <option value="">Semua Brand</option>
                    @foreach ($brands as $id => $name)
                        <option value="{{ $id }}" {{ $sel('brand_filter', (string) $id) }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="product_filter" class="{{ $labelClass }}">Produk</label>
                <select id="product_filter" name="product_filter" class="{{ $inputClass }}" data-autosubmit>
                    <option value="">Semua Produk</option>
                    @foreach ($products as $id => $name)
                        <option value="{{ $id }}" {{ $sel('product_filter', (string) $id) }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end">
                <a href="{{ route('admin.orders.index') }}"
                    class="inline-flex items-center justify-center gap-2 whitespace-nowrap h-10 text-sm font-medium rounded-lg px-4 w-full bg-gray-100 border border-gray-300 text-gray-700 hover:bg-gray-200 transition-colors">
                    <span>Reset Filter</span>
                </a>
            </div>
        </div>
    </form>

    <!-- Orders Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="{{ $thClass }}">Order ID</th>
                        <th class="{{ $thClass }}">Pelanggan</th>
                        <th class="{{ $thClass }}">Total &amp; Metode</th>
                        <th class="{{ $thClass }}">Status</th>
                        <th class="{{ $thClass }}">Status DP</th>
                        <th class="{{ $thClass }}">Pembayaran</th>
                        <th class="{{ $thClass }}">Bukti Bayar</th>
                        <th class="{{ $thClass }}">Faktur</th>
                        <th class="{{ $thClass }}">Tanggal</th>
                        <th class="{{ $thClass }} text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($orders as $order)
                        @php
                            $isFull    = $order->payment_method === 'full';
                            $dpPaid    = (bool) $order->dp_paid_at;
                            $remPaid   = (bool) $order->remaining_paid_at;
                            $dpLabel   = $isFull ? 'Bayar Penuh' : ($dpPaid ? 'DP Lunas' : 'DP Pending');
                            $remLabel  = ! $dpPaid ? 'Menunggu DP' : ($remPaid ? 'Lunas Semua' : 'Sisa Pending');
                            $remBadge  = match ($remLabel) {
                                'Lunas Semua'  => ['bg-green-100 text-green-800',   'fa-check-double'],
                                'Sisa Pending' => ['bg-orange-100 text-orange-800', 'fa-hourglass-half'],
                                default        => ['bg-gray-100 text-gray-600',     'fa-minus'],
                            };
                            $payKey    = $order->payment_status ?? 'pending';
                            $proof     = $order->paymentConfirmations->first();
                            $invoice   = $order->invoice;
                            $remaining = max(0, (int) $order->total - (int) $order->amount_due);
                            $param     = ['order' => $order->order_number];
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <!-- Order ID -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $order->order_number }}</div>
                                <div class="text-sm text-gray-500">{{ $order->items_count }} item(s)</div>
                            </td>

                            <!-- Pelanggan -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $order->seller_name ?? 'Pembelian Website' }}</div>
                                <div class="text-sm text-gray-500">{{ $order->email ?: $order->whatsapp_number }}</div>
                            </td>

                            <!-- Total & Metode -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    Rp {{ number_format($order->total, 0, ',', '.') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium {{ $isFull ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                        <i class="fas {{ $isFull ? 'fa-money-bill' : 'fa-chart-pie' }} mr-1"></i>
                                        {{ $isFull ? 'Bayar Penuh' : 'Down Payment' }}
                                    </span>
                                </div>
                            </td>

                            <!-- Status Pesanan -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $statusLabels[$order->status] ?? ucfirst($order->status) }}
                                </span>
                            </td>

                            <!-- Status DP -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($isFull)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check mr-1"></i>
                                        {{ $dpLabel }}
                                    </span>
                                @else
                                    <div class="space-y-1">
                                        <div class="flex items-center text-xs">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full {{ $dpPaid ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                <i class="fas {{ $dpPaid ? 'fa-check' : 'fa-clock' }} mr-1"></i>
                                                {{ $dpLabel }}
                                            </span>
                                        </div>
                                        <div class="flex items-center text-xs">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full {{ $remBadge[0] }}">
                                                <i class="fas {{ $remBadge[1] }} mr-1"></i>
                                                {{ $remLabel }}
                                            </span>
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            DP: {{ number_format($order->amount_due, 0, ',', '.') }} |
                                            Sisa: {{ number_format($remaining, 0, ',', '.') }}
                                        </div>
                                    </div>
                                @endif
                            </td>

                            <!-- Pembayaran -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $paymentClasses[$payKey] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $paymentLabels[$payKey] ?? ucfirst($payKey) }}
                                </span>
                            </td>

                            <!-- Bukti Bayar -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($proof)
                                    <a href="{{ Storage::disk('public')->url($proof->proof_path) }}" target="_blank" rel="noopener"
                                       title="Lihat bukti transfer"
                                       class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 hover:bg-green-200">
                                        <i class="fas fa-check mr-1"></i>
                                        Ada
                                    </a>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <i class="fas fa-times mr-1"></i>
                                        Belum
                                    </span>
                                @endif
                            </td>

                            <!-- Faktur -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($invoice)
                                    <div class="flex items-center space-x-2">
                                        <div class="space-y-1">
                                            @if ($invoice->format === 'excel')
                                                <a href="{{ route('admin.invoices.download', $invoice) }}" download
                                                   class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 hover:bg-green-200">
                                                    <i class="fas fa-file-excel mr-1"></i>{{ $invoice->invoice_number }} (Excel)
                                                </a>
                                            @else
                                                <a href="{{ route('admin.invoices.download', $invoice) }}" download
                                                   class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 hover:bg-blue-200">
                                                    <i class="fas fa-file-pdf mr-1"></i>{{ $invoice->invoice_number }} (PDF)
                                                </a>
                                            @endif
                                            <div class="text-xs text-gray-500">
                                                {{ ($invoice->invoice_date ?? $invoice->created_at)->timezone('Asia/Jakarta')->format('d M Y H:i') }}
                                            </div>
                                        </div>

                                        <form method="POST" action="{{ route('admin.invoices.destroy', $invoice) }}" data-keep-scroll
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus faktur ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" title="Hapus Faktur"
                                                    class="inline-flex items-center justify-center h-8 w-8 rounded-md bg-red-500 hover:bg-red-600 text-white">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <button type="button" class="{{ $btnView }}" data-invoice-open
                                            data-action="{{ route('admin.orders.invoice.store', $param) }}"
                                            data-order="{{ $order->order_number }}">
                                        <i class="fas fa-plus text-xs"></i> <span>Buat Faktur</span>
                                    </button>
                                @endif
                            </td>

                            <!-- Tanggal -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $order->created_at->timezone('Asia/Jakarta')->format('d M Y H:i') }}
                            </td>

                            <!-- Aksi -->
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    {{-- Lihat: buka modal Detail Pesanan --}}
                                    <button type="button" class="{{ $btnView }}"
                                            onclick="document.getElementById('order-detail-{{ $order->id }}').showModal()">
                                        <span>Lihat</span>
                                    </button>

                                    <!-- Dropdown Status Pesanan -->
                                    <details class="relative" data-dd>
                                        <summary class="{{ $btnStatus }}">Status {!! $chevron !!}</summary>
                                        <div class="{{ $menuClass }}" data-menu style="position:absolute">
                                            @foreach ($statusLabels as $value => $label)
                                                <form method="POST" action="{{ route('admin.orders.status', $param) }}">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="status" value="{{ $value }}">
                                                    <button type="submit" class="{{ $menuItemClass }}">{{ $label }}</button>
                                                </form>
                                            @endforeach
                                        </div>
                                    </details>

                                    @if ($isFull)
                                        <!-- Dropdown Status Pembayaran (Bayar Penuh) -->
                                        <details class="relative" data-dd>
                                            <summary class="{{ $btnPay }}">Pembayaran {!! $chevron !!}</summary>
                                            <div class="{{ $menuClass }}" data-menu style="position:absolute">
                                                @foreach ($paymentLabels as $value => $label)
                                                    <form method="POST" action="{{ route('admin.orders.payment', $param) }}">
                                                        @csrf @method('PATCH')
                                                        <input type="hidden" name="payment_status" value="{{ $value }}">
                                                        <button type="submit" class="{{ $menuItemClass }}">{{ $label }}</button>
                                                    </form>
                                                @endforeach
                                            </div>
                                        </details>
                                    @else
                                        <!-- Dropdown DP Action (Down Payment) -->
                                        <details class="relative" data-dd>
                                            <summary class="{{ $btnPay }}">DP Action {!! $chevron !!}</summary>
                                            <div class="{{ $menuClass }}" data-menu style="position:absolute">
                                                @if (! $dpPaid)
                                                    <form method="POST" action="{{ route('admin.orders.dp-paid', $param) }}"
                                                          onsubmit="return confirm('Tandai DP sebagai lunas?')">
                                                        @csrf @method('PATCH')
                                                        <button type="submit" class="{{ $menuItemClass }}">Tandai DP Lunas</button>
                                                    </form>
                                                @elseif (! $remPaid)
                                                    <form method="POST" action="{{ route('admin.orders.remaining-paid', $param) }}"
                                                          onsubmit="return confirm('Tandai sisa pembayaran sebagai lunas?')">
                                                        @csrf @method('PATCH')
                                                        <button type="submit" class="{{ $menuItemClass }}">Tandai Sisa Lunas</button>
                                                    </form>
                                                @else
                                                    <div class="px-2 py-1.5 text-sm text-zinc-500">Tidak ada aksi</div>
                                                @endif
                                            </div>
                                        </details>
                                    @endif

                                    <form method="POST" action="{{ route('admin.orders.destroy', $param) }}"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesanan ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="{{ $btnDelete }}">
                                            <span>Hapus</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-10 text-center text-sm text-gray-500">
                                Belum ada pesanan{{ request()->query() ? ' yang cocok dengan filter' : '' }}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($orders->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

</div>

{{-- ============ Modal Detail Pesanan (satu <dialog> per pesanan) ============ --}}
@foreach ($orders as $order)
    @php
        $isFull     = $order->payment_method === 'full';
        $dpPaid     = (bool) $order->dp_paid_at;
        $remPaid    = (bool) $order->remaining_paid_at;
        $payKey     = $order->payment_status ?? 'pending';
        $proofs     = $order->paymentConfirmations;
        $remaining  = max(0, (int) $order->total - (int) $order->amount_due);
        $dpPercent  = (int) $order->total > 0 ? round($order->amount_due / $order->total * 100, 1) : 0;
        $remPercent = round(100 - $dpPercent, 1);
        $coupon     = $order->coupon ?? null;
        $discount   = (int) ($order->discount_amount ?? 0);
        $wib        = fn ($date) => $date ? \Illuminate\Support\Carbon::parse($date)->timezone('Asia/Jakarta')->format('d M Y H:i') : '-';

        $dpLabel  = $dpPaid ? 'DP Lunas' : 'DP Pending';
        $remLabel = ! $dpPaid ? 'Menunggu DP' : ($remPaid ? 'Lunas Semua' : 'Sisa Pending');
        $remBadge = match ($remLabel) {
            'Lunas Semua'  => ['bg-green-100 text-green-800',   'fa-check-double'],
            'Sisa Pending' => ['bg-orange-100 text-orange-800', 'fa-hourglass-half'],
            default        => ['bg-gray-100 text-gray-600',     'fa-minus'],
        };

        // Bagian bukti bayar: [judul, label status, koleksi, tampil walau kosong?, label tombol]
        $proofSections = $isFull
            ? [['Bukti Pembayaran', 'Bukti Terupload', $proofs, true, 'Lihat Bukti']]
            : [
                ['Bukti Pembayaran DP', 'Bukti DP Terupload', $proofs->where('type', 'dp'), true, 'Lihat Bukti DP'],
                ['Bukti Pelunasan', 'Bukti Pelunasan Terupload', $proofs->where('type', 'remaining'), false, 'Lihat Bukti Pelunasan'],
            ];

        $btnPrimary = 'inline-flex items-center justify-center gap-2 whitespace-nowrap h-8 px-3 text-sm font-medium rounded-md bg-[var(--color-accent)] hover:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_10%)] text-[var(--color-accent-foreground)] border border-black/10 shadow-[inset_0px_1px_--theme(--color-white/.2)] transition-colors';
        $btnGhost   = 'inline-flex items-center justify-center gap-2 whitespace-nowrap h-8 px-3 text-sm font-medium rounded-md bg-transparent text-zinc-800 hover:bg-zinc-800/5 transition-colors';
    @endphp

    <dialog id="order-detail-{{ $order->id }}" data-order-dialog
            class="m-auto p-0 bg-transparent w-[calc(100%-2rem)] max-w-4xl overflow-hidden backdrop:bg-black/30">
        <div class="bg-white rounded-lg w-full max-h-[90vh] flex flex-col overflow-hidden">

            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b shrink-0">
                <h3 class="text-xl font-semibold text-gray-900">Detail Pesanan #{{ $order->order_number }}</h3>
                <button type="button" class="inline-flex items-center justify-center h-8 w-8 rounded-md text-zinc-800 hover:bg-zinc-800/5"
                        onclick="this.closest('dialog').close()" aria-label="Tutup">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z"/></svg>
                </button>
            </div>

            <!-- Body (satu-satunya area scroll) -->
            <div class="p-6 flex-1 min-h-0 overflow-y-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Informasi Pesanan -->
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-3">Informasi Pesanan</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Order ID:</span>
                                <span class="font-medium">{{ $order->order_number }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tanggal:</span>
                                <span class="font-medium">{{ $wib($order->created_at) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal:</span>
                                <span class="font-medium">{{ $rp($order->subtotal ?? ($order->total + $discount)) }}</span>
                            </div>
                            @if ($discount > 0)
                                <div class="flex justify-between text-green-600">
                                    <span>Diskon Kupon:</span>
                                    <span class="font-medium">-{{ $rp($discount) }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between border-t pt-2 mt-2">
                                <span class="text-gray-900 font-semibold">Total:</span>
                                <span class="font-bold text-lg text-primary-600">{{ $rp($order->total) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Status:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $statusLabels[$order->status] ?? ucfirst($order->status) }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Metode Bayar:</span>
                                <span class="font-medium">
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs {{ $isFull ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                        <i class="fas {{ $isFull ? 'fa-money-bill' : 'fa-chart-pie' }} mr-1"></i>
                                        {{ $isFull ? 'Bayar Penuh' : 'Down Payment' }}
                                    </span>
                                </span>
                            </div>
                            @unless ($isFull)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Status DP:</span>
                                    <span class="font-medium">
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs {{ $dpPaid ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            <i class="fas {{ $dpPaid ? 'fa-check' : 'fa-clock' }} mr-1"></i>
                                            {{ $dpLabel }}
                                        </span>
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Status Sisa:</span>
                                    <span class="font-medium">
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs {{ $remBadge[0] }}">
                                            <i class="fas {{ $remBadge[1] }} mr-1"></i>
                                            {{ $remLabel }}
                                        </span>
                                    </span>
                                </div>
                            @endunless
                            <div class="flex justify-between">
                                <span class="text-gray-600">Pembayaran:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $paymentClasses[$payKey] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $paymentLabels[$payKey] ?? ucfirst($payKey) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Pelanggan -->
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-3">Informasi Pelanggan</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Nama:</span>
                                <span class="font-medium">{{ trim($order->first_name . ' ' . $order->last_name) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Email:</span>
                                <span class="font-medium">{{ $order->email ?: '-' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">WhatsApp:</span>
                                <span class="font-medium">{{ $order->whatsapp_number ?: '-' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Seller:</span>
                                <span class="font-medium">{{ $order->seller_name ?? 'Pembelian Website' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">ID Seller:</span>
                                <span class="font-medium">{{ $order->seller_id ?: '-' }}</span>
                            </div>
                            @if (! empty($order->admin_handle))
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Admin Handle:</span>
                                    <span class="font-medium">{{ $order->admin_handle }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Informasi Kupon -->
                @if ($coupon)
                    <div class="mt-6">
                        <h4 class="font-semibold text-gray-900 mb-3">Informasi Kupon</h4>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-center space-x-2 mb-2">
                                <i class="fas fa-ticket-alt text-green-600"></i>
                                <h5 class="font-medium text-green-900">{{ $coupon->name }}</h5>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ $coupon->code }}
                                </span>
                            </div>

                            @if (! empty($coupon->description))
                                <p class="text-sm text-green-700 mb-3">{{ $coupon->description }}</p>
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-green-700 font-medium">Tipe Diskon:</span>
                                    <div class="text-green-900">
                                        @if ($coupon->type === 'percentage')
                                            {{ number_format($coupon->value, 2, '.', ',') }}%
                                            @if (! empty($coupon->max_discount))
                                                (Max: {{ $rp($coupon->max_discount) }})
                                            @endif
                                        @else
                                            {{ $rp($coupon->value) }}
                                        @endif
                                    </div>
                                </div>

                                <div>
                                    <span class="text-green-700 font-medium">Diskon Diberikan:</span>
                                    <div class="text-green-900 font-semibold">{{ $rp($discount) }}</div>
                                </div>

                                @if (! empty($coupon->min_purchase))
                                    <div>
                                        <span class="text-green-700 font-medium">Min. Pembelian:</span>
                                        <div class="text-green-900">{{ $rp($coupon->min_purchase) }}</div>
                                    </div>
                                @endif

                                @if (! empty($coupon->expires_at))
                                    <div>
                                        <span class="text-green-700 font-medium">Berlaku Sampai:</span>
                                        <div class="text-green-900">{{ $wib($coupon->expires_at) }}</div>
                                    </div>
                                @endif
                            </div>

                            <!-- Statistik penggunaan kupon -->
                            <div class="mt-3 pt-3 border-t border-green-200">
                                <div class="flex items-center justify-between text-xs text-green-600">
                                    <span>
                                        <i class="fas fa-chart-bar mr-1"></i>
                                        Penggunaan: {{ $coupon->used_count ?? 0 }}
                                        @if (! empty($coupon->usage_limit))
                                            / {{ $coupon->usage_limit }}
                                        @endif
                                    </span>
                                    <span>
                                        <i class="fas fa-clock mr-1"></i>
                                        Digunakan: {{ $wib($order->coupon_used_at ?? $order->created_at) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Rincian Pembayaran DP -->
                @unless ($isFull)
                    <div class="mt-6">
                        <h4 class="font-semibold text-gray-900 mb-3">Rincian Pembayaran DP</h4>
                        <div class="bg-blue-50 rounded-lg p-4 mb-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <div class="text-sm font-medium text-gray-700">Down Payment ({{ number_format($dpPercent, 1) }}%)</div>
                                    <div class="text-lg font-semibold text-blue-900">{{ $rp($order->amount_due) }}</div>
                                    @if ($dpPaid)
                                        <div class="text-xs text-green-600 mt-1">
                                            <i class="fas fa-check mr-1"></i>
                                            Dibayar {{ $wib($order->dp_paid_at) }}
                                        </div>
                                    @else
                                        <div class="text-xs text-yellow-600 mt-1">
                                            <i class="fas fa-clock mr-1"></i>
                                            Menunggu pembayaran
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-700">Sisa Pembayaran ({{ number_format($remPercent, 1) }}%)</div>
                                    <div class="text-lg font-semibold text-orange-900">{{ $rp($remaining) }}</div>
                                    @if (! $dpPaid)
                                        <div class="text-xs text-gray-500 mt-1">
                                            <i class="fas fa-minus mr-1"></i>
                                            Menunggu DP terlebih dahulu
                                        </div>
                                    @elseif ($remPaid)
                                        <div class="text-xs text-green-600 mt-1">
                                            <i class="fas fa-check mr-1"></i>
                                            Dibayar {{ $wib($order->remaining_paid_at) }}
                                        </div>
                                    @else
                                        <div class="text-xs text-orange-600 mt-1">
                                            <i class="fas fa-hourglass-half mr-1"></i>
                                            Menunggu pelunasan
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endunless

                <!-- Bukti Pembayaran -->
                @foreach ($proofSections as [$proofTitle, $proofStatus, $proofList, $showEmpty, $proofBtn])
                    @if ($proofList->isNotEmpty() || $showEmpty)
                        <div class="mt-6">
                            <h4 class="font-semibold text-gray-900 mb-3">{{ $proofTitle }}</h4>
                            @forelse ($proofList as $p)
                                @php $proofUrl = Storage::disk('public')->url($p->proof_path); @endphp
                                <div class="bg-gray-50 rounded-lg p-4 {{ ! $loop->last ? 'mb-3' : '' }}">
                                    <div class="flex items-center justify-between mb-3">
                                        <div>
                                            <span class="text-sm font-medium text-gray-700">Status:</span>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 ml-2">
                                                <i class="fas fa-check mr-1"></i>
                                                {{ $proofStatus }}
                                            </span>
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $wib($p->created_at) }}</div>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <a href="{{ $proofUrl }}" target="_blank" rel="noopener" class="{{ $btnPrimary }}">
                                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M8 9.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z"/><path fill-rule="evenodd" d="M1.38 8.28a.87.87 0 0 1 0-.566 7.003 7.003 0 0 1 13.238.006.87.87 0 0 1 0 .566A7.003 7.003 0 0 1 1.379 8.28ZM11 8a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" clip-rule="evenodd"/></svg>
                                            <span>{{ $proofBtn }}</span>
                                        </a>
                                        <a href="{{ $proofUrl }}" download class="{{ $btnGhost }}">
                                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M8.75 2.75a.75.75 0 0 0-1.5 0v5.69L5.03 6.22a.75.75 0 0 0-1.06 1.06l3.5 3.5a.75.75 0 0 0 1.06 0l3.5-3.5a.75.75 0 0 0-1.06-1.06L8.75 8.44V2.75Z"/><path d="M3.5 9.75a.75.75 0 0 0-1.5 0v1.5A2.75 2.75 0 0 0 4.75 14h6.5A2.75 2.75 0 0 0 14 11.25v-1.5a.75.75 0 0 0-1.5 0v1.5c0 .69-.56 1.25-1.25 1.25h-6.5c-.69 0-1.25-.56-1.25-1.25v-1.5Z"/></svg>
                                            <span>Download</span>
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <i class="fas fa-times mr-1"></i> Belum ada bukti pembayaran
                                    </span>
                                </div>
                            @endforelse
                        </div>
                    @endif
                @endforeach

                <!-- Alamat Pengiriman -->
                <div class="mt-6">
                    <h4 class="font-semibold text-gray-900 mb-3">Alamat Pengiriman</h4>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-600">
                            {{ $order->address }}<br>
                            {{ $order->city }}, {{ $order->province }} {{ $order->postal_code }}<br>
                            {{ $order->country ?? 'Indonesia' }}
                        </p>
                    </div>
                </div>

                <!-- Item Pesanan -->
                <div class="mt-6">
                    <h4 class="font-semibold text-gray-900 mb-3">Item Pesanan</h4>
                    <div class="space-y-3">
                        @foreach ($order->items as $item)
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
                            <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                                <div class="w-16 h-16 bg-gray-200 rounded overflow-hidden flex-shrink-0">
                                    @if ($image)
                                        <img src="{{ Storage::disk('public')->url($image) }}" alt="{{ $name }}" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <h5 class="font-medium text-gray-900">{{ $name }}</h5>
                                    @if ($attrs)
                                        <div class="text-sm text-gray-500">{{ implode(' | ', $attrs) }}</div>
                                    @endif
                                    <div class="text-sm text-gray-500">{{ $rp($price) }} × {{ $qty }}</div>
                                </div>
                                <div class="text-sm font-medium text-gray-900">{{ $rp($price * $qty) }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="flex justify-end p-6 border-t bg-gray-50 shrink-0">
                <button type="button" onclick="this.closest('dialog').close()"
                        class="inline-flex items-center justify-center h-10 px-4 text-sm font-medium rounded-lg text-zinc-800 hover:bg-zinc-800/5 transition-colors">
                    <span>Tutup</span>
                </button>
            </div>
        </div>
    </dialog>
@endforeach

{{-- ============ Modal Buat Faktur (satu dialog dipakai semua baris) ============ --}}
<dialog id="invoice-dialog" data-default-number="{{ $nextInvoiceNumber }}" data-default-date="{{ $nowLocal }}"
        class="m-auto p-0 bg-transparent w-[calc(100%-2rem)] max-w-md overflow-hidden backdrop:bg-black/30">
    <form method="POST" action="#" id="invoice-form" data-keep-scroll class="bg-white rounded-lg w-full">
        @csrf
        <input type="hidden" name="_invoice_order" id="invoice-order" value="{{ old('_invoice_order') }}">

        <div class="flex items-center justify-between p-6 border-b">
            <h3 class="text-xl font-semibold text-gray-900">Buat Faktur</h3>
            <button type="button" class="inline-flex items-center justify-center h-8 w-8 rounded-md text-zinc-800 hover:bg-zinc-800/5"
                    onclick="this.closest('dialog').close()" aria-label="Tutup">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z"/></svg>
            </button>
        </div>

        <div class="p-6 space-y-4">
            <div>
                <label for="invoice_number" class="{{ $labelClass }}">Nomor Faktur</label>
                <input type="text" id="invoice_number" name="invoice_number" required
                       value="{{ old('invoice_number', $nextInvoiceNumber) }}"
                       placeholder="FKT-20240101-0001" class="{{ $inputClass }}">
                @error('invoice_number') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="invoice_date" class="{{ $labelClass }}">Tanggal Faktur</label>
                <input type="datetime-local" id="invoice_date" name="invoice_date" required
                       value="{{ old('invoice_date', $nowLocal) }}" class="{{ $inputClass }}">
                @error('invoice_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="invoice_notes" class="{{ $labelClass }}">Catatan Faktur (Opsional)</label>
                <textarea id="invoice_notes" name="invoice_notes" rows="3"
                          placeholder="Catatan tambahan untuk faktur..." class="{{ $inputClass }}">{{ old('invoice_notes') }}</textarea>
                @error('invoice_notes') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex justify-end gap-3 p-6 border-t bg-gray-50">
            <button type="button" onclick="this.closest('dialog').close()"
                    class="inline-flex items-center justify-center h-10 px-4 text-sm font-medium rounded-lg text-zinc-800 hover:bg-zinc-800/5 transition-colors">
                <span>Batal</span>
            </button>
            <button type="submit" name="format" value="pdf" data-submit-btn
                    class="inline-flex items-center justify-center gap-2 whitespace-nowrap h-10 px-4 text-sm font-medium rounded-lg bg-[var(--color-accent)] hover:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_10%)] text-[var(--color-accent-foreground)] border border-black/10 transition-colors disabled:opacity-60 disabled:cursor-wait">
                <i class="fas fa-file-pdf"></i> <span>Buat Faktur PDF</span>
            </button>
            <button type="submit" name="format" value="excel" data-submit-btn
                    class="inline-flex items-center justify-center gap-2 whitespace-nowrap h-10 px-4 text-sm font-medium rounded-lg bg-[var(--color-accent)] hover:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_10%)] text-[var(--color-accent-foreground)] border border-black/10 transition-colors disabled:opacity-60 disabled:cursor-wait">
                <i class="fas fa-file-excel"></i> <span>Buat Faktur Excel</span>
            </button>
        </div>
    </form>
</dialog>

<style>
    /* Kunci scroll halaman di belakang modal */
    html:has(dialog[data-order-dialog][open]),
    body:has(dialog[data-order-dialog][open]),
    html:has(#invoice-dialog[open]),
    body:has(#invoice-dialog[open]) { overflow: hidden; }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Pertahankan posisi scroll setelah buat / hapus faktur (halaman dimuat ulang)
    const scrollParents = () => {
        const list = [];
        for (let el = document.getElementById('filterForm'); el; el = el.parentElement) list.push(el);
        return list;
    };
    document.addEventListener('submit', (e) => {
        if (e.defaultPrevented || !e.target.matches('[data-keep-scroll]')) return;
        sessionStorage.setItem('keepScroll', JSON.stringify({
            win: window.scrollY,
            els: scrollParents().map((el) => el.scrollTop),
        }));
    });
    const restoreScroll = () => {
        const raw = sessionStorage.getItem('keepScroll');
        if (!raw) return;
        sessionStorage.removeItem('keepScroll');
        const { win, els } = JSON.parse(raw);
        scrollParents().forEach((el, i) => { if (els[i]) el.scrollTop = els[i]; });
        window.scrollTo(0, win);
    };
    restoreScroll();
    window.addEventListener('load', restoreScroll);

    // Notifikasi sukses hilang otomatis
    const toast = document.getElementById('flash-toast');
    if (toast) setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 500); }, 3000);

    // Modal Buat Faktur
    const invDlg  = document.getElementById('invoice-dialog');
    const invForm = document.getElementById('invoice-form');

    document.querySelectorAll('[data-invoice-open]').forEach((btn) =>
        btn.addEventListener('click', () => {
            invForm.action = btn.dataset.action;
            document.getElementById('invoice-order').value = btn.dataset.order;
            invForm.querySelector('#invoice_number').value = invDlg.dataset.defaultNumber;
            invForm.querySelector('#invoice_date').value   = invDlg.dataset.defaultDate;
            invForm.querySelector('#invoice_notes').value  = '';
            invDlg.showModal();
        })
    );
    invDlg.addEventListener('click', (e) => { if (e.target === invDlg) invDlg.close(); });

    // Loading: tombol dinonaktifkan setelah submit; format dikirim lewat input hidden
    invForm.addEventListener('submit', (e) => {
        const hidden = document.createElement('input');
        hidden.type = 'hidden'; hidden.name = 'format'; hidden.value = e.submitter ? e.submitter.value : 'pdf';
        invForm.appendChild(hidden);
        invForm.querySelectorAll('[data-submit-btn]').forEach((b) => {
            b.disabled = true;
            if (b === e.submitter) b.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Memproses...</span>';
        });
    });

    @if ($errors->hasAny(['invoice_number', 'invoice_date', 'invoice_notes']) && old('_invoice_order'))
        // Validasi gagal: buka lagi form dengan pesan error
        invForm.action = @json(route('admin.orders.invoice.store', ['order' => old('_invoice_order')]));
        invDlg.showModal();
    @endif

    // Filter dropdown langsung diterapkan; kolom cari lewat Enter
    document.querySelectorAll('#filterForm [data-autosubmit]').forEach((el) =>
        el.addEventListener('change', () => el.form.submit())
    );

    // Modal detail: klik area gelap di luar kotak menutup modal
    document.querySelectorAll('dialog[data-order-dialog]').forEach((dlg) =>
        dlg.addEventListener('click', (e) => { if (e.target === dlg) dlg.close(); })
    );

    // Dropdown aksi: menu memakai posisi fixed agar tidak terpotong oleh tabel
    const dds = document.querySelectorAll('details[data-dd]');
    const closeAll = (except = null) => dds.forEach((d) => { if (d !== except) d.open = false; });

    dds.forEach((d) => d.addEventListener('toggle', () => {
        if (!d.open) return;
        closeAll(d);
        const menu = d.querySelector('[data-menu]');
        const r = d.getBoundingClientRect();
        menu.style.position = 'fixed';
        menu.style.top  = (r.bottom + 4) + 'px';
        menu.style.left = Math.max(8, Math.min(r.left, window.innerWidth - menu.offsetWidth - 8)) + 'px';
    }));

    document.addEventListener('click', (e) => {
        dds.forEach((d) => { if (d.open && !d.contains(e.target)) d.open = false; });
    });
    window.addEventListener('scroll', () => closeAll(), true);
    window.addEventListener('resize', () => closeAll());
});
</script>
@endsection