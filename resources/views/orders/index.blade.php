@extends('layouts.sidebar')
 
@section('title', 'Manage Orders')
 
@section('content')
@php
    $dummyOrders = [
        [
            'id' => 6,
            'order_id' => 'ORD260924113552OSF',
            'items_count' => 1,
            'customer_name' => 'Pembelian Website',
            'customer_contact' => '085926276644',
            'total' => 100000,
            'payment_method' => 'Down Payment',
            'status' => 'Menunggu',
            'dp_status' => 'DP Lunas',
            'remaining_status' => 'Lunas Semua',
            'dp_amount' => 30000,
            'remaining_amount' => 70000,
            'payment_status' => 'Lunas',
            'proof_status' => 'Ada',
            'invoice_number' => 'FKT-20260924-0002',
            'invoice_url' => 'https://zakira.demowebjalan.com/download/invoice/6',
            'created_at' => '24 Sep 2026 11:35',
        ],
        [
            'id' => 5,
            'order_id' => 'ORD260922170018I42',
            'items_count' => 2,
            'customer_name' => 'AKN',
            'customer_contact' => 'customer@gmail.com',
            'total' => 500000,
            'payment_method' => 'Down Payment',
            'status' => 'Menunggu',
            'dp_status' => 'DP Lunas',
            'remaining_status' => 'Lunas Semua',
            'dp_amount' => 150000,
            'remaining_amount' => 350000,
            'payment_status' => 'Lunas',
            'proof_status' => 'Ada',
            'invoice_number' => 'FKT-20260924-0001',
            'invoice_url' => 'https://zakira.demowebjalan.com/download/invoice/5',
            'created_at' => '22 Sep 2026 17:00',
        ],
        [
            'id' => 4,
            'order_id' => 'ORD260922135407WHQ',
            'items_count' => 1,
            'customer_name' => 'Pembelian Website',
            'customer_contact' => 'lala@gmail.com',
            'total' => 100000,
            'payment_method' => 'Down Payment',
            'status' => 'Menunggu',
            'dp_status' => 'DP Lunas',
            'remaining_status' => 'Sisa Pending',
            'dp_amount' => 30000,
            'remaining_amount' => 70000,
            'payment_status' => 'Menunggu',
            'proof_status' => 'Ada',
            'invoice_number' => 'FKT-20260922-0002',
            'invoice_url' => 'https://zakira.demowebjalan.com/images/invoices/excel/faktur-ORD260922135407WHQ-20260922154035.xlsx',
            'created_at' => '22 Sep 2026 13:54',
        ],
        [
            'id' => 3,
            'order_id' => 'ORD260918162345LPM',
            'items_count' => 1,
            'customer_name' => 'AKN',
            'customer_contact' => 'admin@gmail.com',
            'total' => 100000,
            'payment_method' => 'Bayar Penuh',
            'status' => 'Menunggu',
            'dp_status' => 'Bayar Penuh',
            'remaining_status' => 'Menunggu',
            'dp_amount' => 100000,
            'remaining_amount' => 0,
            'payment_status' => 'Menunggu',
            'proof_status' => 'Belum',
            'invoice_number' => 'FKT-20260924-0001',
            'invoice_url' => 'https://zakira.demowebjalan.com/download/invoice/3',
            'created_at' => '18 Sep 2026 16:23',
        ],
        [
            'id' => 2,
            'order_id' => 'ORD26091814172373H',
            'items_count' => 1,
            'customer_name' => 'Pembelian Website',
            'customer_contact' => '08123929321',
            'total' => 300000,
            'payment_method' => 'Down Payment',
            'status' => 'Menunggu',
            'dp_status' => 'DP Pending',
            'remaining_status' => 'Menunggu DP',
            'dp_amount' => 90000,
            'remaining_amount' => 210000,
            'payment_status' => 'Menunggu',
            'proof_status' => 'Belum',
            'invoice_number' => null,
            'invoice_url' => null,
            'created_at' => '18 Sep 2026 14:17',
        ],
    ];
 
    $inputClass = 'w-full border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500';
    $labelClass = 'block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2';
    $thClass = 'px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider';
    $menuItemClass = 'flex items-center px-2 py-1.5 w-full rounded-md text-start text-sm font-medium text-zinc-800 hover:bg-zinc-100 dark:text-white dark:hover:bg-zinc-600';
    $ghostBtnClass = 'relative items-center font-medium justify-center h-8 text-sm rounded-md px-3 inline-flex bg-transparent hover:bg-zinc-800/5';
@endphp
 
<div class="space-y-6">
 
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Kelola Pesanan</h1>
        <div class="flex items-center space-x-4">
            <button type="button" wire:click="openExportModal"
                class="inline-flex items-center justify-center h-10 px-4 text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 shadow-sm transition">
                <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor">
                    <path fill-rule="evenodd" d="M4 2a1.5 1.5 0 0 0-1.5 1.5v9A1.5 1.5 0 0 0 4 14h8a1.5 1.5 0 0 0 1.5-1.5V6.621a1.5 1.5 0 0 0-.44-1.06L9.94 2.439A1.5 1.5 0 0 0 8.878 2H4Zm4 3.5a.75.75 0 0 1 .75.75v2.69l.72-.72a.75.75 0 1 1 1.06 1.06l-2 2a.75.75 0 0 1-1.06 0l-2-2a.75.75 0 0 1 1.06-1.06l.72.72V6.25A.75.75 0 0 1 8 5.5Z" clip-rule="evenodd"/>
                </svg>
                Export Excel
            </button>
            <div class="text-sm text-gray-500 dark:text-gray-400">
                Total: {{ count($dummyOrders) }} pesanan
            </div>
        </div>
    </div>
 
    <!-- Filters -->
    <div class="bg-white dark:bg-zinc-900 shadow rounded-lg p-6 border border-zinc-200 dark:border-zinc-700">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="search" class="{{ $labelClass }}">Cari Pesanan</label>
                <input type="text" id="search" wire:model.live="search"
                    placeholder="Order ID, nama, email, WhatsApp..." class="{{ $inputClass }}">
            </div>
 
            <div>
                <label for="status_filter" class="{{ $labelClass }}">Status Pesanan</label>
                <select id="status_filter" wire:model.live="status_filter" class="{{ $inputClass }}">
                    <option value="">Semua Status</option>
                    <option value="pending">Menunggu</option>
                    <option value="processing">Diproses</option>
                    <option value="shipped">Dikirim</option>
                    <option value="delivered">Terkirim</option>
                    <option value="cancelled">Dibatalkan</option>
                </select>
            </div>
 
            <div>
                <label for="payment_status_filter" class="{{ $labelClass }}">Status Pembayaran</label>
                <select id="payment_status_filter" wire:model.live="payment_status_filter" class="{{ $inputClass }}">
                    <option value="">Semua Status</option>
                    <option value="pending">Menunggu</option>
                    <option value="paid">Lunas</option>
                    <option value="failed">Gagal</option>
                    <option value="refunded">Dikembalikan</option>
                </select>
            </div>
 
            <div>
                <label for="dp_status_filter" class="{{ $labelClass }}">Status DP</label>
                <select id="dp_status_filter" wire:model.live="dp_status_filter" class="{{ $inputClass }}">
                    <option value="">Semua Status</option>
                    <option value="dp_pending">DP Pending</option>
                    <option value="dp_paid">DP Lunas</option>
                    <option value="remaining_pending">Sisa Pending</option>
                    <option value="fully_paid">Lunas Semua</option>
                    <option value="full_payment">Bayar Penuh</option>
                </select>
            </div>
 
            <div>
                <label for="brand_filter" class="{{ $labelClass }}">Brand Produk</label>
                <select id="brand_filter" wire:model.live="brand_filter" class="{{ $inputClass }}">
                    <option value="">Semua Brand</option>
                    <option value="1">ZAKIRA</option>
                </select>
            </div>
 
            <div>
                <label for="product_filter" class="{{ $labelClass }}">Produk</label>
                <select id="product_filter" wire:model.live="product_filter" class="{{ $inputClass }}">
                    <option value="">Semua Produk</option>
                    <option value="2">Almet PNC1</option>
                    <option value="1">Baju Koko</option>
                    <option value="3">test</option>
                </select>
            </div>
 
            <div class="flex items-end md:col-span-2">
                <button type="button" wire:click="resetFilters"
                    class="w-full h-10 px-4 text-sm font-medium bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-white rounded-lg transition">
                    Reset Filter
                </button>
            </div>
        </div>
    </div>
 
    <!-- Orders Table -->
    <div class="bg-white dark:bg-zinc-900 shadow rounded-lg overflow-hidden border border-zinc-200 dark:border-zinc-700">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                <thead class="bg-gray-50 dark:bg-zinc-800">
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
 
                <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-700">
                    @foreach ($dummyOrders as $order)
                        @php
                            $isExcel = $order['invoice_url'] && str_contains($order['invoice_url'], 'excel');
                            $isDpPaid = str_contains($order['dp_status'], 'Lunas');
                            $isDpPending = str_contains($order['dp_status'], 'Pending');
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800/50" wire:key="order-{{ $order['id'] }}">
                            <!-- Order ID -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $order['order_id'] }}</div>
                                <div class="text-sm text-gray-500">{{ $order['items_count'] }} item(s)</div>
                            </td>
 
                            <!-- Pelanggan -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $order['customer_name'] }}</div>
                                <div class="text-sm text-gray-500">{{ $order['customer_contact'] }}</div>
                            </td>
 
                            <!-- Total & Metode -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    Rp {{ number_format($order['total'], 0, ',', '.') }}
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium {{ $order['payment_method'] === 'Bayar Penuh' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                        <i class="fas {{ $order['payment_method'] === 'Bayar Penuh' ? 'fa-money-bill' : 'fa-chart-pie' }} mr-1"></i>
                                        {{ $order['payment_method'] }}
                                    </span>
                                </div>
                            </td>
 
                            <!-- Status Pesanan -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    {{ $order['status'] }}
                                </span>
                            </td>
 
                            <!-- Status DP -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="space-y-1">
                                    <div class="flex items-center text-xs">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full {{ $isDpPending && ! $isDpPaid ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                            <i class="fas {{ $isDpPaid ? 'fa-check' : 'fa-clock' }} mr-1"></i>
                                            {{ $order['dp_status'] }}
                                        </span>
                                    </div>
                                    <div class="flex items-center text-xs">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full {{ $order['remaining_status'] === 'Lunas Semua' ? 'bg-green-100 text-green-800' : ($order['remaining_status'] === 'Sisa Pending' ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-600') }}">
                                            <i class="fas {{ $order['remaining_status'] === 'Lunas Semua' ? 'fa-check-double' : ($order['remaining_status'] === 'Sisa Pending' ? 'fa-hourglass-half' : 'fa-minus') }} mr-1"></i>
                                            {{ $order['remaining_status'] }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        DP: {{ number_format($order['dp_amount'], 0, ',', '.') }} |
                                        Sisa: {{ number_format($order['remaining_amount'], 0, ',', '.') }}
                                    </div>
                                </div>
                            </td>
 
                            <!-- Pembayaran -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $order['payment_status'] === 'Lunas' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $order['payment_status'] }}
                                </span>
                            </td>
 
                            <!-- Bukti Bayar -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $order['proof_status'] === 'Ada' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    <i class="fas {{ $order['proof_status'] === 'Ada' ? 'fa-check' : 'fa-times' }} mr-1"></i>
                                    {{ $order['proof_status'] }}
                                </span>
                            </td>
 
                            <!-- Faktur -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    <div class="space-y-1">
                                        @if ($order['invoice_url'])
                                            <a href="{{ $order['invoice_url'] }}" target="_blank"
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $isExcel ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-blue-100 text-blue-800 hover:bg-blue-200' }}">
                                                <i class="fas {{ $isExcel ? 'fa-file-excel' : 'fa-file-pdf' }} mr-1"></i>
                                                {{ $order['invoice_number'] }} ({{ $isExcel ? 'Excel' : 'PDF' }})
                                            </a>
                                        @else
                                            <button type="button" class="{{ $ghostBtnClass }} text-zinc-800 dark:text-white"
                                                wire:click="openInvoiceModal({{ $order['id'] }})">
                                                <svg class="shrink-0 size-4 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor">
                                                    <path d="M8.75 3.75a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z"/>
                                                </svg>
                                                <span>Buat Faktur</span>
                                            </button>
                                        @endif
                                        <div class="text-xs text-gray-500">{{ $order['created_at'] }}</div>
                                    </div>
 
                                    @if ($order['invoice_url'])
                                        <button type="button"
                                            class="relative items-center font-medium justify-center h-8 w-8 rounded-md inline-flex bg-red-500 hover:bg-red-600 text-white shadow-sm"
                                            wire:click="deleteInvoice({{ $order['id'] }})"
                                            wire:confirm="Apakah Anda yakin ingin menghapus faktur ini?"
                                            title="Hapus Faktur">
                                            <svg class="shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 0 0 6 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 1 0 .23 1.482l.149-.022.841 10.518A2.75 2.75 0 0 0 7.596 19h4.807a2.75 2.75 0 0 0 2.742-2.53l.841-10.52.149.023a.75.75 0 0 0 .23-1.482A41.03 41.03 0 0 0 14 4.193V3.75A2.75 2.75 0 0 0 11.25 1h-2.5ZM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4ZM8.58 7.72a.75.75 0 0 0-1.5.06l.3 7.5a.75.75 0 1 0 1.5-.06l-.3-7.5Zm4.34.06a.75.75 0 1 0-1.5-.06l-.3 7.5a.75.75 0 1 0 1.5.06l.3-7.5Z" clip-rule="evenodd"/>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
 
                            <!-- Tanggal -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $order['created_at'] }}
                            </td>
 
                            <!-- Aksi -->
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <button type="button" class="{{ $ghostBtnClass }} text-zinc-800 dark:text-white"
                                        wire:click="viewOrder({{ $order['id'] }})">
                                        <span>Lihat</span>
                                    </button>
 
                                    <button type="button" class="{{ $ghostBtnClass }} text-blue-600 hover:text-blue-800"
                                        wire:click="editOrder({{ $order['id'] }})">
                                        <span>Edit</span>
                                    </button>
 
                                    <!-- Dropdown Status Pesanan -->
                                    <ui-dropdown position="bottom start" data-flux-dropdown="">
                                        <button type="button" class="{{ $ghostBtnClass }} text-zinc-800 dark:text-white">
                                            Status
                                            <svg class="shrink-0 ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                                            </svg>
                                        </button>
                                        <ui-menu class="min-w-48 p-1 rounded-lg shadow-xs border border-zinc-200 dark:border-zinc-600 bg-white dark:bg-zinc-700" popover="manual" data-flux-menu="">
                                            <button type="button" class="{{ $menuItemClass }}" wire:click="updateOrderStatus({{ $order['id'] }}, 'pending')">Menunggu</button>
                                            <button type="button" class="{{ $menuItemClass }}" wire:click="updateOrderStatus({{ $order['id'] }}, 'processing')">Diproses</button>
                                            <button type="button" class="{{ $menuItemClass }}" wire:click="updateOrderStatus({{ $order['id'] }}, 'shipped')">Dikirim</button>
                                            <button type="button" class="{{ $menuItemClass }}" wire:click="updateOrderStatus({{ $order['id'] }}, 'delivered')">Terkirim</button>
                                            <button type="button" class="{{ $menuItemClass }}" wire:click="updateOrderStatus({{ $order['id'] }}, 'cancelled')">Dibatalkan</button>
                                        </ui-menu>
                                    </ui-dropdown>
 
                                    <button type="button" class="{{ $ghostBtnClass }} text-red-600 hover:text-red-800"
                                        wire:click="deleteOrder({{ $order['id'] }})"
                                        wire:confirm="Apakah Anda yakin ingin menghapus pesanan ini?">
                                        <span>Hapus</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
 
</div>
 
@endsection