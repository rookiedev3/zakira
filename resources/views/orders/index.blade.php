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

    // Tombol aksi
    $btnBase   = 'inline-flex items-center justify-center gap-1 whitespace-nowrap h-8 px-3 text-sm font-medium rounded-md border cursor-pointer list-none transition-colors [&::-webkit-details-marker]:hidden';
    $btnView   = $btnBase . ' bg-gray-100 border-gray-300 text-gray-700 hover:bg-gray-200';
    $btnEdit   = $btnBase . ' bg-amber-50 border-amber-200 text-amber-700 hover:bg-amber-100';
    $btnStatus = $btnBase . ' bg-blue-50 border-blue-200 text-blue-700 hover:bg-blue-100';
    $btnPay    = $btnBase . ' bg-green-50 border-green-200 text-green-700 hover:bg-green-100';
    $btnDelete = $btnBase . ' bg-red-50 border-red-200 text-red-700 hover:bg-red-100';

    // Baris produk di modal Edit
    $btnVariant = 'inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 cursor-pointer';
    $btnTrash   = 'inline-flex items-center justify-center h-8 w-8 rounded-md bg-[#ff000f] hover:bg-[#e6000d] text-white shrink-0 cursor-pointer';
    $qtyInput   = 'w-16 border border-gray-300 rounded px-2 py-1 text-center focus:outline-none focus:ring-1 focus:ring-primary-500';
    $iconPencil = '<svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>';
    $iconTrash  = '<svg class="shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 0 0 6 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 1 0 .23 1.482l.149-.022.841 10.518A2.75 2.75 0 0 0 7.596 19h4.807a2.75 2.75 0 0 0 2.742-2.53l.841-10.52.149.023a.75.75 0 0 0 .23-1.482A41.03 41.03 0 0 0 14 4.193V3.75A2.75 2.75 0 0 0 11.25 1h-2.5ZM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4ZM8.58 7.72a.75.75 0 0 0-1.5.06l.3 7.5a.75.75 0 1 0 1.5-.06l-.3-7.5Zm4.34.06a.75.75 0 1 0-1.5-.06l-.3 7.5a.75.75 0 1 0 1.5.06l.3-7.5Z" clip-rule="evenodd"/></svg>';

    $chevron       = '<svg class="shrink-0 ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>';

    $sel = fn (string $key, string $value) => (string) request($key) === $value ? 'selected' : '';
    $rp  = fn ($n) => 'Rp ' . number_format((int) $n, 0, ',', '.');

    // Daftar kupon untuk dropdown modal Edit (dikirim dari controller)
    $coupons = $coupons ?? collect();

    // Varian & harga tiap produk untuk dialog "Tambah Produk" (dikirim dari controller)
    $productMeta = $productMeta ?? [];

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
                    placeholder="Order ID, nama, email, WhatsApp..." class="{{ $inputClass }}">
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
                            // Bukti pelunasan (sisa): bertipe 'remaining'; bila tabel tanpa kolom type, bukti terbaru setelah DP lunas dianggap bukti sisa
                            $allProofs = $order->paymentConfirmations;
                            $remProof  = $allProofs->first(fn ($p) => ($p->type ?? null) === 'remaining')
                                ?? ($dpPaid && ! $isFull && $allProofs->count() > 1 && $allProofs->whereNotNull('type')->isEmpty() ? $allProofs->first() : null);
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

                            <!-- Bukti Bayar (klik = buka di tab baru) -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($proof)
                                    <a href="{{ asset('storage/' . ltrim($proof->proof_path, '/')) }}" target="_blank" rel="noopener" title="Lihat bukti transfer"
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

                                    {{-- Edit: buka modal Edit Pesanan --}}
                                    <button type="button" class="{{ $btnEdit }}"
                                            onclick="document.getElementById('order-edit-{{ $order->id }}').showModal()">
                                        <i class="fas fa-pen text-xs"></i> <span>Edit</span>
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
                                                        <button type="submit" class="{{ $menuItemClass }}">
                                                            <i class="fas fa-check text-green-600 mr-2"></i> Tandai DP Lunas
                                                        </button>
                                                    </form>
                                                @elseif (! $remPaid)
                                                    <form method="POST" action="{{ route('admin.orders.remaining-paid', $param) }}"
                                                          onsubmit="return confirm('Tandai sisa pembayaran sebagai lunas?')">
                                                        @csrf @method('PATCH')
                                                        <button type="submit" class="{{ $menuItemClass }}">
                                                            <i class="fas fa-check-double text-green-600 mr-2"></i> Tandai Sisa Lunas
                                                        </button>
                                                    </form>
                                                @endif

                                                @if ($remProof)
                                                    <a href="{{ asset('storage/' . ltrim($remProof->proof_path, '/')) }}" target="_blank" rel="noopener"
                                                       class="{{ $menuItemClass }}">
                                                        <i class="fas fa-file-invoice text-purple-600 mr-2"></i> Lihat Bukti Sisa
                                                    </a>
                                                @elseif ($dpPaid && $remPaid)
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
        $discount   = (int) ($order->discount ?? 0);
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
                // Tabel payment_confirmations tidak punya kolom `type`: bukti tanpa type dianggap bukti DP
                ['Bukti Pembayaran DP', 'Bukti DP Terupload', $proofs->filter(fn ($p) => ($p->type ?? 'dp') === 'dp'), true, 'Lihat Bukti DP'],
                ['Bukti Pelunasan', 'Bukti Pelunasan Terupload', $proofs->filter(fn ($p) => ($p->type ?? null) === 'remaining'), false, 'Lihat Bukti Pelunasan'],
            ];

        $btnPrimary = 'inline-flex items-center justify-center gap-2 whitespace-nowrap h-8 px-3 text-sm font-medium rounded-md bg-[#935b33] hover:bg-[#845230] text-white border border-black/10 shadow-[inset_0px_1px_--theme(--color-white/.2)] transition-colors cursor-pointer';
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
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $coupon->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">
                                    {{ $coupon->status_label }}
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
                                            @if (! empty($coupon->max_discount_amount))
                                                (Max: {{ $rp($coupon->max_discount_amount) }})
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

                                @if (! empty($coupon->minimum_amount))
                                    <div>
                                        <span class="text-green-700 font-medium">Min. Pembelian:</span>
                                        <div class="text-green-900">{{ $rp($coupon->minimum_amount) }}</div>
                                    </div>
                                @endif

                                @if (! empty($coupon->minimum_quantity))
                                    <div>
                                        <span class="text-green-700 font-medium">Min. Kuantitas:</span>
                                        <div class="text-green-900">{{ $coupon->minimum_quantity }} item</div>
                                    </div>
                                @endif

                                <div>
                                    <span class="text-green-700 font-medium">Berlaku Untuk:</span>
                                    <div class="text-green-900">{{ $coupon->audience_label }}</div>
                                </div>

                                @if (! empty($coupon->starts_at))
                                    <div>
                                        <span class="text-green-700 font-medium">Mulai Berlaku:</span>
                                        <div class="text-green-900">{{ $wib($coupon->starts_at) }}</div>
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
                                @php
                                    $proofUrl = asset('storage/' . ltrim($p->proof_path, '/'));
                                @endphp
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
                                        {{-- Lihat Bukti: buka di tab baru --}}
                                        <a href="{{ $proofUrl }}" target="_blank" rel="noopener" class="{{ $btnPrimary }}">
                                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M8 9.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z"/><path fill-rule="evenodd" d="M1.38 8.28a.87.87 0 0 1 0-.566 7.003 7.003 0 0 1 13.238.006.87.87 0 0 1 0 .566A7.003 7.003 0 0 1 1.379 8.28ZM11 8a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" clip-rule="evenodd"/></svg>
                                            <span>{{ $proofBtn }}</span>
                                        </a>
                                        <a href="{{ $proofUrl }}" download="bukti-{{ $order->order_number }}.{{ pathinfo($p->proof_path, PATHINFO_EXTENSION) }}" class="{{ $btnGhost }}">
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
                                $name  = $item->product_name ?? '-';
                                $image = $item->product_image ?? $item->product?->image ?? null;
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

{{-- ============ Modal Edit Pesanan (satu <dialog> per pesanan) ============ --}}
@foreach ($orders as $order)
    @php
        $isOld        = (string) old('_edit_order') === (string) $order->id;
        $editStatus   = $isOld ? old('status') : $order->status;
        $editPayment  = $isOld ? old('payment_status') : ($order->payment_status ?? 'pending');
        $editCoupon   = $isOld ? old('coupon_code') : ($order->coupon_code ?? '');
        $editDiscount = $isOld ? old('discount') : (int) ($order->discount ?? 0);
        $ov           = fn (string $key, $default) => $isOld ? old($key, $default) : $default;
        $custErr      = $isOld && $errors->hasAny(['full_name', 'seller_id', 'email', 'whatsapp_number']);
        $addrErr      = $isOld && $errors->hasAny(['address', 'city', 'province', 'postal_code']);

        $btnPrimary = 'inline-flex items-center justify-center gap-2 whitespace-nowrap h-8 px-3 text-sm font-medium rounded-md bg-[#935b33] hover:bg-[#845230] text-white border border-black/10 shadow-[inset_0px_1px_--theme(--color-white/.2)] transition-colors cursor-pointer';
        $btnDanger  = 'inline-flex items-center justify-center h-8 w-8 rounded-md bg-red-500 hover:bg-red-600 text-white shrink-0 cursor-pointer';
    @endphp

    <dialog id="order-edit-{{ $order->id }}" data-order-dialog data-edit-dialog
            class="m-auto p-0 bg-transparent w-[calc(100%-2rem)] max-w-4xl overflow-hidden backdrop:bg-black/30">
        <form method="POST" action="{{ route('admin.orders.update', ['order' => $order->order_number]) }}" data-keep-scroll
              class="bg-white rounded-lg w-full max-h-[90vh] flex flex-col overflow-hidden">
            @csrf @method('PUT')
            <input type="hidden" name="_edit_order" value="{{ $order->id }}">

            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b shrink-0">
                <h3 class="text-xl font-semibold text-gray-900">Edit Pesanan #{{ $order->order_number }}</h3>
                <button type="button" class="inline-flex items-center justify-center h-8 w-8 rounded-md text-zinc-800 hover:bg-zinc-800/5"
                        onclick="this.closest('dialog').close()" aria-label="Tutup">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z"/></svg>
                </button>
            </div>

            <!-- Body -->
            <div class="p-6 flex-1 min-h-0 overflow-y-auto space-y-6">

                @if ($isOld && $errors->any())
                    <div class="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $err) <li>{{ $err }}</li> @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Detail Pesanan -->
                <div>
                    <h4 class="font-semibold text-gray-900 mb-3">Detail Pesanan</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm text-gray-700 mb-1 block">Status Pesanan</label>
                            <select name="status" class="{{ $inputClass }}">
                                @foreach ($statusLabels as $value => $label)
                                    <option value="{{ $value }}" @selected($editStatus === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-sm text-gray-700 mb-1 block">Status Pembayaran</label>
                            <select name="payment_status" class="{{ $inputClass }}">
                                @foreach ($paymentLabels as $value => $label)
                                    <option value="{{ $value }}" @selected($editPayment === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Produk Pesanan -->
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-semibold text-gray-900">Produk Pesanan</h4>
                        <button type="button" data-add-item class="{{ $btnPrimary }}">
                            <i class="fas fa-plus text-xs"></i> <span>Tambah Produk</span>
                        </button>
                    </div>

                    <div class="space-y-3">
                        @foreach ($order->items as $item)
                            @php
                                $iName  = $item->product_name ?? '-';
                                $iAttrs = array_filter([
                                    ! empty($item->model) ? 'Model: '  . $item->model : null,
                                    ! empty($item->color) ? 'Warna: '  . $item->color : null,
                                    ! empty($item->size)  ? 'Ukuran: ' . $item->size  : null,
                                ]);
                            @endphp
                            <div class="border border-gray-200 rounded-lg p-4" data-item-row data-product-id="{{ $item->product_id }}" data-price="{{ (int) $item->price }}">
                                <input type="hidden" name="items[{{ $item->id }}][id]" value="{{ $item->id }}">
                                <input type="hidden" name="items[{{ $item->id }}][delete]" value="0" data-delete-input>

                                <div class="flex items-center justify-between gap-4">
                                    <div class="flex-1 min-w-0" data-item-info>
                                        <h5 class="font-medium text-gray-900" data-item-name>{{ $iName }}</h5>
                                        <div class="text-sm text-gray-500 mt-1" data-item-attrs>{{ implode(' | ', $iAttrs) }}</div>
                                    </div>

                                    <div class="flex items-center space-x-3 shrink-0">
                                        <div class="flex items-center space-x-2">
                                            <label class="text-sm text-gray-600">Qty:</label>
                                            <input type="number" min="1" data-qty name="items[{{ $item->id }}][quantity]"
                                                   value="{{ (int) $item->quantity }}" class="{{ $qtyInput }}">
                                        </div>
                                        <div class="text-sm font-medium text-gray-900" data-item-price>{{ $rp($item->price) }}</div>
                                        <button type="button" data-toggle-variant class="{{ $btnVariant }}">
                                            {!! $iconPencil !!} Edit Varian
                                        </button>
                                        <button type="button" data-delete-item class="{{ $btnTrash }}" title="Hapus produk">
                                            {!! $iconTrash !!}
                                        </button>
                                    </div>
                                </div>

                                {{-- Varian diubah lewat dialog "Edit Varian Produk"; nilainya disimpan di input hidden --}}
                                <input type="hidden" name="items[{{ $item->id }}][model]" value="{{ $item->model }}" data-v-model>
                                <input type="hidden" name="items[{{ $item->id }}][color]" value="{{ $item->color }}" data-v-color>
                                <input type="hidden" name="items[{{ $item->id }}][size]"  value="{{ $item->size }}"  data-v-size>
                            </div>
                        @endforeach

                        {{-- Baris produk baru disisipkan lewat JS dari dialog "Tambah Produk ke Pesanan" --}}
                        <div class="space-y-3" data-new-items></div>
                    </div>
                </div>

                <!-- Kupon Diskon -->
                <div>
                    <h4 class="font-semibold text-gray-900 mb-3">Kupon Diskon</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm text-gray-700 mb-1 block">Pilih Kupon</label>
                            <select name="coupon_code" data-coupon-select class="{{ $inputClass }}">
                                <option value="">Tidak ada kupon</option>
                                @foreach ($coupons as $c)
                                    <option value="{{ $c->code }}"
                                            data-type="{{ $c->type }}" data-value="{{ $c->value }}" data-max="{{ $c->max_discount_amount }}"
                                            data-min="{{ $c->minimum_amount }}"
                                            @selected((string) $editCoupon === (string) $c->code)>
                                        {{ $c->code }} — {{ $c->name }} ({{ $c->discount_label }})@if ($c->status !== 'active') [{{ $c->status_label }}]@endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-sm text-gray-700 mb-1 block">Diskon Kupon</label>
                            <input type="number" min="0" step="0.01" name="discount" data-discount-input
                                   value="{{ number_format((float) $editDiscount, 2, '.', '') }}" class="{{ $inputClass }}">
                        </div>
                        <p class="md:col-span-2 -mt-2 text-xs text-amber-600" data-coupon-min-warning></p>
                    </div>
                </div>

                <!-- Ringkasan Harga -->
                <div>
                    <h4 class="font-semibold text-gray-900 mb-3">Ringkasan Harga</h4>
                    <div class="bg-gray-50 rounded-lg p-4 space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="font-medium" data-sum-subtotal>-</span>
                        </div>
                        <div class="flex justify-between text-green-600">
                            <span>Diskon:</span>
                            <span class="font-medium" data-sum-discount>-</span>
                        </div>
                        <div class="flex justify-between border-t pt-2">
                            <span class="font-semibold text-gray-900">Total:</span>
                            <span class="font-bold text-lg" data-sum-total>-</span>
                        </div>
                        <p class="text-xs text-gray-500">Harga produk baru ditampilkan sebagai perkiraan; server menghitung ulang sesuai model, warna, dan ukuran saat disimpan.</p>
                    </div>
                </div>

                <!-- Informasi Pelanggan (accordion) -->
                <details class="border border-gray-200 rounded-lg" @if ($custErr) open @endif>
                    <summary class="flex items-center justify-between p-4 cursor-pointer list-none bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors [&::-webkit-details-marker]:hidden">
                        <h4 class="font-semibold text-gray-900">Informasi Pelanggan</h4>
                        <svg class="w-5 h-5 text-gray-500 transition-transform duration-200 [details[open]_&]:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </summary>
                    <div class="p-4 border-t border-gray-200 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="{{ $labelClass }}">Nama Lengkap</label>
                            <input type="text" name="full_name" value="{{ $ov('full_name', trim($order->first_name . ' ' . $order->last_name)) }}" class="{{ $inputClass }}">
                            @if ($isOld) @error('full_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror @endif
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">ID Penjual</label>
                            <input type="text" name="seller_id" value="{{ $ov('seller_id', $order->seller_id) }}" class="{{ $inputClass }}">
                            @if ($isOld) @error('seller_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror @endif
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">Email</label>
                            <input type="email" name="email" value="{{ $ov('email', $order->email) }}" class="{{ $inputClass }}">
                            @if ($isOld) @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror @endif
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">WhatsApp</label>
                            <input type="text" name="whatsapp_number" value="{{ $ov('whatsapp_number', $order->whatsapp_number) }}" class="{{ $inputClass }}">
                            @if ($isOld) @error('whatsapp_number') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror @endif
                        </div>
                    </div>
                </details>

                <!-- Alamat Pengiriman (accordion) -->
                <details class="border border-gray-200 rounded-lg" @if ($addrErr) open @endif>
                    <summary class="flex items-center justify-between p-4 cursor-pointer list-none bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors [&::-webkit-details-marker]:hidden">
                        <h4 class="font-semibold text-gray-900">Alamat Pengiriman</h4>
                        <svg class="w-5 h-5 text-gray-500 transition-transform duration-200 [details[open]_&]:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </summary>
                    <div class="p-4 border-t border-gray-200 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="{{ $labelClass }}">Alamat Lengkap</label>
                            <textarea name="address" rows="3" class="{{ $inputClass }}">{{ $ov('address', $order->address) }}</textarea>
                            @if ($isOld) @error('address') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror @endif
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">Kota</label>
                            <input type="text" name="city" value="{{ $ov('city', $order->city) }}" class="{{ $inputClass }}">
                            @if ($isOld) @error('city') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror @endif
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">Provinsi</label>
                            <input type="text" name="province" value="{{ $ov('province', $order->province) }}" class="{{ $inputClass }}">
                            @if ($isOld) @error('province') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror @endif
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">Kode Pos</label>
                            <input type="text" name="postal_code" value="{{ $ov('postal_code', $order->postal_code) }}" class="{{ $inputClass }}">
                            @if ($isOld) @error('postal_code') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror @endif
                        </div>
                    </div>
                </details>

                <!-- Catatan -->
                <div>
                    <h4 class="font-semibold text-gray-900 mb-3">Catatan</h4>
                    <textarea name="notes" rows="3" placeholder="Catatan tambahan untuk pesanan..." class="{{ $inputClass }}">{{ $ov('notes', $order->notes) }}</textarea>
                    @if ($isOld) @error('notes') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror @endif
                </div>
            </div>

            <!-- Footer -->
            <div class="flex justify-end gap-3 p-6 border-t bg-gray-50 shrink-0">
                <button type="button" onclick="this.closest('dialog').close()"
                        class="inline-flex items-center justify-center h-10 px-4 text-sm font-medium rounded-lg text-zinc-800 hover:bg-zinc-800/5 transition-colors">
                    <span>Batal</span>
                </button>
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap h-10 px-4 text-sm font-medium rounded-lg bg-[#935b33] hover:bg-[#845230] text-white border border-black/10 transition-colors">
                    <i class="fas fa-check text-xs"></i> <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </dialog>
@endforeach

{{-- ============ Modal Tambah Produk ke Pesanan (satu dialog dipakai semua modal Edit; di luar <form>) ============ --}}
<dialog id="add-item-dialog"
        class="m-auto p-0 bg-transparent w-[calc(100%-2rem)] max-w-xl overflow-hidden backdrop:bg-black/30">
    <div class="bg-white rounded-lg w-full">
        <div class="flex items-center justify-between p-6 border-b">
            <h3 class="text-xl font-semibold text-gray-900">Tambah Produk ke Pesanan</h3>
            <button type="button" class="inline-flex items-center justify-center h-8 w-8 rounded-md text-zinc-800 hover:bg-zinc-800/5"
                    onclick="this.closest('dialog').close()" aria-label="Tutup">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z"/></svg>
            </button>
        </div>

        <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
            <div>
                <label for="add-item-product" class="{{ $labelClass }}">Pilih Produk</label>
                <select id="add-item-product" class="{{ $inputClass }}">
                    <option value="">-- Pilih Produk --</option>
                    @foreach ($products as $pid => $pname)
                        @php $pm = $productMeta[$pid] ?? null; @endphp
                        <option value="{{ $pid }}">
                            {{ $pname }}@if ($pm && $pm['max'] > 0) - {{ $rp($pm['min']) }}@if ($pm['max'] !== $pm['min']) - {{ $rp($pm['max']) }}@endif @endif
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Detail produk: muncul hanya setelah produk dipilih --}}
            <div id="add-item-details" class="hidden space-y-4">
                <div>
                    <label for="add-item-qty" class="{{ $labelClass }}">Kuantitas</label>
                    <input type="number" id="add-item-qty" min="1" max="9999" value="1" class="{{ $inputClass }}">
                </div>

                <div data-variant-wrap="color" class="hidden">
                    <label for="add-item-color" class="{{ $labelClass }}">Warna</label>
                    <select id="add-item-color" class="{{ $inputClass }}"></select>
                </div>
                <div data-variant-wrap="model" class="hidden">
                    <label for="add-item-model" class="{{ $labelClass }}">Model</label>
                    <select id="add-item-model" class="{{ $inputClass }}"></select>
                </div>
                <div data-variant-wrap="size" class="hidden">
                    <label for="add-item-size" class="{{ $labelClass }}">Ukuran</label>
                    <select id="add-item-size" class="{{ $inputClass }}"></select>
                </div>

                <div class="flex justify-between items-center rounded-lg bg-gray-50 px-4 py-3 text-sm">
                    <span class="text-gray-600">Harga satuan</span>
                    <span id="add-item-price" class="font-semibold text-gray-900">-</span>
                </div>
            </div>

            <p id="add-item-error" class="hidden text-xs text-red-600">Pilih produk terlebih dahulu.</p>
        </div>

        <div class="flex justify-end gap-3 p-6 border-t bg-gray-50">
            <button type="button" onclick="this.closest('dialog').close()"
                    class="inline-flex items-center justify-center h-10 px-4 text-sm font-medium rounded-lg text-zinc-800 hover:bg-zinc-800/5 transition-colors">
                <span>Batal</span>
            </button>
            <button type="button" id="add-item-confirm" disabled
                    class="inline-flex items-center justify-center gap-2 whitespace-nowrap h-10 px-4 text-sm font-medium rounded-lg bg-[#935b33] hover:bg-[#845230] text-white border border-black/10 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="fas fa-plus text-xs"></i> <span>Tambah ke Pesanan</span>
            </button>
        </div>
    </div>
</dialog>

{{-- ============ Modal Edit Varian Produk (satu dialog dipakai semua baris produk; di luar <form>) ============ --}}
<dialog id="edit-variant-dialog"
        class="m-auto p-0 bg-transparent w-[calc(100%-2rem)] max-w-md overflow-hidden backdrop:bg-black/30">
    <div class="bg-white rounded-lg w-full">
        <div class="flex items-center justify-between p-6 border-b">
            <h3 class="text-xl font-semibold text-gray-900">Edit Varian Produk</h3>
            <button type="button" class="inline-flex items-center justify-center h-8 w-8 rounded-md text-zinc-800 hover:bg-zinc-800/5"
                    onclick="this.closest('dialog').close()" aria-label="Tutup">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z"/></svg>
            </button>
        </div>

        <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
            <div id="ev-name" class="rounded-lg bg-gray-50 px-4 py-3 font-medium text-gray-900"></div>

            <div data-ev-wrap="model" class="hidden">
                <label for="ev-model" class="{{ $labelClass }}">Model</label>
                <select id="ev-model" class="{{ $inputClass }}"></select>
            </div>
            <div data-ev-wrap="color" class="hidden">
                <label for="ev-color" class="{{ $labelClass }}">Warna</label>
                <select id="ev-color" class="{{ $inputClass }}"></select>
            </div>
            <div data-ev-wrap="size" class="hidden">
                <label for="ev-size" class="{{ $labelClass }}">Ukuran</label>
                <select id="ev-size" class="{{ $inputClass }}"></select>
            </div>

            <p id="ev-error" class="hidden text-xs text-red-600"></p>
        </div>

        <div class="flex justify-end gap-3 p-6 border-t bg-gray-50">
            <button type="button" onclick="this.closest('dialog').close()"
                    class="inline-flex items-center justify-center h-10 px-4 text-sm font-medium rounded-lg text-zinc-800 hover:bg-zinc-800/5 transition-colors">
                <span>Batal</span>
            </button>
            <button type="button" id="ev-save"
                    class="inline-flex items-center justify-center gap-2 whitespace-nowrap h-10 px-4 text-sm font-medium rounded-lg bg-[#935b33] hover:bg-[#845230] text-white border border-black/10 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="fas fa-check text-xs"></i> <span>Simpan Varian</span>
            </button>
        </div>
    </div>
</dialog>

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
                    class="inline-flex items-center justify-center gap-2 whitespace-nowrap h-10 px-4 text-sm font-medium rounded-lg bg-[#935b33] hover:bg-[#845230] text-white border border-black/10 transition-colors disabled:opacity-60 disabled:cursor-wait">
                <i class="fas fa-file-pdf"></i> <span>Buat Faktur PDF</span>
            </button>
            <button type="submit" name="format" value="excel" data-submit-btn
                    class="inline-flex items-center justify-center gap-2 whitespace-nowrap h-10 px-4 text-sm font-medium rounded-lg bg-[#935b33] hover:bg-[#845230] text-white border border-black/10 transition-colors disabled:opacity-60 disabled:cursor-wait">
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
    body:has(#invoice-dialog[open]),
    html:has(#add-item-dialog[open]),
    body:has(#add-item-dialog[open]),
    html:has(#edit-variant-dialog[open]),
    body:has(#edit-variant-dialog[open]) { overflow: hidden; }
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

    // Modal detail & edit: klik area gelap di luar kotak menutup modal
    document.querySelectorAll('dialog[data-order-dialog]').forEach((dlg) =>
        dlg.addEventListener('click', (e) => { if (e.target === dlg) dlg.close(); })
    );

    // ------------------------------------------------------------------
    // Dialog "Tambah Produk ke Pesanan" (dipakai bersama oleh semua modal Edit)
    // ------------------------------------------------------------------
    const addDlg = document.getElementById('add-item-dialog');
    const addSel = document.getElementById('add-item-product');
    const addQty = document.getElementById('add-item-qty');
    const addErr = document.getElementById('add-item-error');
    let addTarget = null; // modal Edit yang sedang membuka dialog ini

    addDlg.addEventListener('click', (e) => { if (e.target === addDlg) addDlg.close(); });

    const PRODUCT_META = @json($productMeta ?? []);
    const BTN_VARIANT = @json($btnVariant);
    const BTN_TRASH   = @json($btnTrash);
    const QTY_INPUT   = @json($qtyInput);
    const ICON_PENCIL = @json($iconPencil);
    const ICON_TRASH  = @json($iconTrash);
    const TEXT_INPUT  = @json($inputClass);
    const rpFmt = (n) => 'Rp ' + Math.round(n).toLocaleString('id-ID');
    const attrText = (m, c, z) => [m && 'Model: ' + m, c && 'Warna: ' + c, z && 'Ukuran: ' + z].filter(Boolean).join(' | ');

    const addDetails = document.getElementById('add-item-details');
    const addConfirm = document.getElementById('add-item-confirm');
    const addPrice   = document.getElementById('add-item-price');
    const VARIANTS = {
        color: { sel: document.getElementById('add-item-color'), label: 'Warna',  list: 'colors' },
        model: { sel: document.getElementById('add-item-model'), label: 'Model',  list: 'models' },
        size:  { sel: document.getElementById('add-item-size'),  label: 'Ukuran', list: 'sizes'  },
    };

    const currentPrice = () => {
        const meta = PRODUCT_META[addSel.value];
        if (!meta) return 0;
        const key = ['model', 'color', 'size'].map((k) => VARIANTS[k].sel.value).join('|');
        return meta.prices[key] ?? meta.min ?? 0;
    };
    const selectedName = (k) => {
        const o = VARIANTS[k].sel.selectedOptions[0];
        return o && o.value ? o.textContent : '';
    };
    const refreshPrice = () => { addPrice.textContent = rpFmt(currentPrice()); };

    // Reset form tiap kali dialog dibuka: hanya "Pilih Produk" yang terlihat
    const resetAddForm = () => {
        addSel.value = '';
        addQty.value = 1;
        addErr.classList.add('hidden');
        addDetails.classList.add('hidden');
        addConfirm.disabled = true;
    };

    // Form detail muncul ketika produk dipilih
    addSel.addEventListener('change', () => {
        addErr.classList.add('hidden');
        const meta = PRODUCT_META[addSel.value];
        if (!meta) { addDetails.classList.add('hidden'); addConfirm.disabled = true; return; }

        addQty.value = 1;
        Object.entries(VARIANTS).forEach(([key, v]) => {
            const wrap  = addDlg.querySelector(`[data-variant-wrap="${key}"]`);
            const items = meta[v.list] || [];
            wrap.classList.toggle('hidden', items.length === 0);
            v.sel.innerHTML = `<option value="">-- Pilih ${v.label} --</option>` +
                items.map((i) => `<option value="${i.id}"></option>`).join('');
            // isi teks lewat textContent agar aman dari karakter khusus
            [...v.sel.options].slice(1).forEach((o, idx) => { o.textContent = items[idx].name; });
        });

        addDetails.classList.remove('hidden');
        addConfirm.disabled = false;
        refreshPrice();
    });

    Object.values(VARIANTS).forEach((v) => v.sel.addEventListener('change', refreshPrice));

    addConfirm.addEventListener('click', () => {
        const meta = PRODUCT_META[addSel.value];
        if (!meta) { addErr.textContent = 'Pilih produk terlebih dahulu.'; addErr.classList.remove('hidden'); return; }

        // Varian wajib dipilih bila produk memilikinya
        for (const v of Object.values(VARIANTS)) {
            if ((meta[v.list] || []).length && !v.sel.value) {
                addErr.textContent = `Pilih ${v.label.toLowerCase()} terlebih dahulu.`;
                addErr.classList.remove('hidden');
                return;
            }
        }

        const model = selectedName('model'), color = selectedName('color'), size = selectedName('size');
        const price = currentPrice();
        const n     = (parseInt(addTarget.dataset.newIdx, 10) || 0) + 1;
        addTarget.dataset.newIdx = n;
        const qty   = Math.max(1, parseInt(addQty.value, 10) || 1);

        // Baris dibuat sama dengan item lama: nama, varian, Qty, harga, Edit Varian, hapus
        const row = document.createElement('div');
        row.dataset.itemRow   = '';
        row.dataset.newRow    = '';
        row.dataset.productId = addSel.value;
        row.dataset.price     = price;
        row.className = 'border border-gray-200 rounded-lg p-4';
        row.innerHTML = `
            <input type="hidden" name="new_items[${n}][product_id]" value="${addSel.value}">
            <input type="hidden" name="new_items[${n}][model]" data-v-model>
            <input type="hidden" name="new_items[${n}][color]" data-v-color>
            <input type="hidden" name="new_items[${n}][size]"  data-v-size>
            <div class="flex items-center justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <h5 class="font-medium text-gray-900" data-item-name></h5>
                    <div class="text-sm text-gray-500 mt-1" data-item-attrs></div>
                </div>
                <div class="flex items-center space-x-3 shrink-0">
                    <div class="flex items-center space-x-2">
                        <label class="text-sm text-gray-600">Qty:</label>
                        <input type="number" min="1" max="9999" data-qty name="new_items[${n}][quantity]" class="${QTY_INPUT}">
                    </div>
                    <div class="text-sm font-medium text-gray-900" data-item-price></div>
                    <button type="button" data-toggle-variant class="${BTN_VARIANT}">${ICON_PENCIL} Edit Varian</button>
                    <button type="button" data-remove-new class="${BTN_TRASH}" title="Hapus produk">${ICON_TRASH}</button>
                </div>
            </div>`;

        row.querySelector('[data-item-name]').textContent  = meta.name;
        row.querySelector('[data-item-price]').textContent = rpFmt(price);
        row.querySelector('[data-qty]').value = qty;
        row.querySelector('[data-v-model]').value = model;
        row.querySelector('[data-v-color]').value = color;
        row.querySelector('[data-v-size]').value  = size;
        row.querySelector('[data-item-attrs]').textContent = attrText(model, color, size);

        addTarget.querySelector('[data-new-items]').appendChild(row);
        addTarget.dispatchEvent(new Event('input')); // hitung ulang Ringkasan Harga
        addDlg.close();
    });

    // ------------------------------------------------------------------
    // Dialog "Edit Varian Produk" (dipakai bersama oleh semua baris produk)
    // ------------------------------------------------------------------
    const evDlg  = document.getElementById('edit-variant-dialog');
    const evName = document.getElementById('ev-name');
    const evErr  = document.getElementById('ev-error');
    const evSave = document.getElementById('ev-save');
    const EV = {
        model: { sel: document.getElementById('ev-model'), wrap: evDlg.querySelector('[data-ev-wrap="model"]'), label: 'Model',  list: 'models' },
        color: { sel: document.getElementById('ev-color'), wrap: evDlg.querySelector('[data-ev-wrap="color"]'), label: 'Warna',  list: 'colors' },
        size:  { sel: document.getElementById('ev-size'),  wrap: evDlg.querySelector('[data-ev-wrap="size"]'),  label: 'Ukuran', list: 'sizes'  },
    };
    let evRow = null, evMeta = null;

    evDlg.addEventListener('click', (e) => { if (e.target === evDlg) evDlg.close(); });

    // Harga untuk ukuran tertentu berdasarkan model & warna yang sedang dipilih
    const evPrice = (sizeId) => {
        if (!evMeta) return null;
        const p = evMeta.prices[[EV.model.sel.value, EV.color.sel.value, sizeId].join('|')];
        return p > 0 ? p : null;
    };
    // Opsi ukuran menampilkan harga: "M (Rp 100.000)"
    const refreshSizeLabels = () => {
        [...EV.size.sel.options].forEach((o) => {
            if (!o.value) return;
            const p = evPrice(o.value);
            o.textContent = o.dataset.name + (p ? ' (' + rpFmt(p) + ')' : '');
        });
    };
    EV.model.sel.addEventListener('change', refreshSizeLabels);
    EV.color.sel.addEventListener('change', refreshSizeLabels);

    const openVariantDialog = (row) => {
        evRow  = row;
        evMeta = PRODUCT_META[row.dataset.productId] || null;
        evErr.classList.add('hidden');
        evName.textContent = row.querySelector('[data-item-name]').textContent.trim();

        Object.entries(EV).forEach(([key, v]) => {
            const items   = evMeta ? (evMeta[v.list] || []) : [];
            const current = row.querySelector(`[data-v-${key}]`).value;
            v.wrap.classList.toggle('hidden', items.length === 0);
            v.sel.innerHTML = '';

            const ph = document.createElement('option');
            ph.value = ''; ph.textContent = `-- Pilih ${v.label} --`;
            v.sel.appendChild(ph);

            items.forEach((i) => {
                const o = document.createElement('option');
                o.value = i.id; o.dataset.name = i.name; o.textContent = i.name;
                if (i.name === current) o.selected = true;
                v.sel.appendChild(o);
            });
        });
        refreshSizeLabels();

        evSave.disabled = !evMeta;
        if (!evMeta) {
            evErr.textContent = 'Data varian untuk produk ini tidak ditemukan.';
            evErr.classList.remove('hidden');
        }
        evDlg.showModal();
    };

    evSave.addEventListener('click', () => {
        if (!evMeta || !evRow) return;

        // Varian wajib dipilih bila produk memilikinya
        for (const v of Object.values(EV)) {
            if ((evMeta[v.list] || []).length && !v.sel.value) {
                evErr.textContent = `Pilih ${v.label.toLowerCase()} terlebih dahulu.`;
                evErr.classList.remove('hidden');
                return;
            }
        }

        const nameOf = (k) => { const o = EV[k].sel.selectedOptions[0]; return o && o.value ? o.dataset.name : ''; };
        const model = nameOf('model'), color = nameOf('color'), size = nameOf('size');
        const old   = ['model', 'color', 'size'].map((k) => evRow.querySelector(`[data-v-${k}]`).value);
        const changed = old[0] !== model || old[1] !== color || old[2] !== size;

        evRow.querySelector('[data-v-model]').value = model;
        evRow.querySelector('[data-v-color]').value = color;
        evRow.querySelector('[data-v-size]').value  = size;
        evRow.querySelector('[data-item-attrs]').textContent = attrText(model, color, size);

        // Harga mengikuti varian baru (hanya bila varian berubah dan harganya diketahui)
        if (changed) {
            const p = evMeta.prices[[EV.model.sel.value, EV.color.sel.value, EV.size.sel.value].join('|')];
            if (p > 0) {
                evRow.dataset.price = p;
                evRow.querySelector('[data-item-price]').textContent = rpFmt(p);
            }
        }

        evRow.closest('dialog').dispatchEvent(new Event('input')); // hitung ulang Ringkasan Harga
        evDlg.close();
    });

    // ------------------------------------------------------------------
    // Modal Edit Pesanan
    // ------------------------------------------------------------------
    document.querySelectorAll('dialog[data-edit-dialog]').forEach((dlg) => {
        const couponSel = dlg.querySelector('[data-coupon-select]');
        const discInput = dlg.querySelector('[data-discount-input]');
        const minWarn   = dlg.querySelector('[data-coupon-min-warning]');

        const subtotal = () => {
            let sum = 0;
            dlg.querySelectorAll('[data-item-row]').forEach((row) => {
                if (row.dataset.deleted === '1') return;
                const qty = Math.max(1, parseInt(row.querySelector('[data-qty]').value, 10) || 1);
                sum += qty * Number(row.dataset.price);
            });
            return sum;
        };

        const recalc = () => {
            const sub  = subtotal();
            const disc = Math.min(sub, Math.max(0, parseFloat(discInput.value) || 0));
            dlg.querySelector('[data-sum-subtotal]').textContent = rpFmt(sub);
            dlg.querySelector('[data-sum-discount]').textContent = '-' + rpFmt(disc);
            dlg.querySelector('[data-sum-total]').textContent    = rpFmt(sub - disc);
        };

        couponSel.addEventListener('change', () => {
            const opt = couponSel.selectedOptions[0];
            if (!opt.value) { discInput.value = '0.00'; return recalc(); }
            const val = parseFloat(opt.dataset.value) || 0;
            const max = parseFloat(opt.dataset.max) || 0;
            const sub = subtotal();
            let d = opt.dataset.type === 'percentage' ? sub * val / 100 : val;
            if (opt.dataset.type === 'percentage' && max > 0) d = Math.min(d, max);
            discInput.value = Math.min(d, sub).toFixed(2);
            recalc();

            const min = parseFloat(opt.dataset.min) || 0;
            minWarn.textContent = (min > 0 && sub < min)
                ? 'Subtotal belum memenuhi minimal pembelian kupon (' + rpFmt(min) + ').'
                : '';
        });

        dlg.addEventListener('input', (e) => {
            recalc();
        });

        dlg.addEventListener('click', (e) => {
            const btn = e.target.closest('button');
            if (!btn) return;

            if (btn.matches('[data-toggle-variant]')) {
                openVariantDialog(btn.closest('[data-item-row]'));
            } else if (btn.matches('[data-delete-item]')) {
                const row = btn.closest('[data-item-row]');
                const del = row.dataset.deleted !== '1';
                row.dataset.deleted = del ? '1' : '0';
                row.querySelector('[data-delete-input]').value = del ? '1' : '0';
                row.querySelector('[data-item-info]').classList.toggle('line-through', del);
                row.classList.toggle('opacity-50', del);
                recalc();
            } else if (btn.matches('[data-add-item]')) {
                addTarget = dlg;
                resetAddForm();
                addDlg.showModal();
            } else if (btn.matches('[data-remove-new]')) {
                btn.closest('[data-new-row]').remove();
                recalc();
            }
        });

        recalc();
    });

    @if ($errors->any() && old('_edit_order'))
        // Validasi edit gagal: buka lagi modal edit pesanan terkait
        document.querySelector('#order-edit-{{ (int) old('_edit_order') }}')?.showModal();
    @endif

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