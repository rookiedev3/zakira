@extends('layouts.sidebar')

@section('title', 'Manage Orders')

@section('content')
@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\URL;

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
@endphp

<div class="space-y-6">

    @if (session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
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
                                <div class="text-sm font-medium text-gray-900">{{ trim($order->first_name . ' ' . $order->last_name) }}</div>
                                <div class="text-sm text-gray-500">{{ $order->whatsapp_number }}</div>
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

                            <!-- Tanggal -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $order->created_at->timezone('Asia/Jakarta')->format('d M Y H:i') }}
                            </td>

                            <!-- Aksi -->
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ URL::signedRoute('checkout.success', $param) }}" target="_blank" rel="noopener"
                                       class="{{ $btnView }}">
                                        <span>Lihat</span>
                                    </a>

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
                            <td colspan="9" class="px-6 py-10 text-center text-sm text-gray-500">
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

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Filter dropdown langsung diterapkan; kolom cari lewat Enter
    document.querySelectorAll('#filterForm [data-autosubmit]').forEach((el) =>
        el.addEventListener('change', () => el.form.submit())
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