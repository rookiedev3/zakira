{{--
    resources/views/dashboard.blade.php
    Tailwind CSS murni, tanpa Flux/Livewire. Semua data dikirim dari
    DashboardController dengan nilai hardcode.
--}}
@extends('layouts.sidebar')

@section('title', 'Dashboard')

@section('content')

    <h1 class="text-2xl font-medium text-zinc-800 mb-2">Dashboard</h1>
    <p class="text-sm text-zinc-500 mb-8">Selamat datang di panel admin Zakira</p>

    {{-- Statistic Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm border p-6 flex items-center justify-between">
            <div>
                <p class="text-sm text-zinc-500">Total Pesanan</p>
                <p class="text-2xl font-medium text-gray-900">{{ $stats['totalOrders'] }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z"/></svg>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-6 flex items-center justify-between">
            <div>
                <p class="text-sm text-zinc-500">Total Pendapatan</p>
                <p class="text-2xl font-medium text-green-600">Rp {{ number_format($stats['totalRevenue'], 0, ',', '.') }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z"/></svg>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-6 flex items-center justify-between">
            <div>
                <p class="text-sm text-zinc-500">Total Pelanggan</p>
                <p class="text-2xl font-medium text-purple-600">{{ $stats['totalCustomers'] }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Z"/></svg>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-6 flex items-center justify-between">
            <div>
                <p class="text-sm text-zinc-500">Produk Aktif</p>
                <p class="text-2xl font-medium text-orange-600">{{ $stats['totalProducts'] }}</p>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"/></svg>
            </div>
        </div>
    </div>

    {{-- Order Status Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm border p-6 flex items-center justify-between">
            <div>
                <p class="text-sm text-zinc-500">Pesanan Pending</p>
                <p class="text-base font-medium text-yellow-600">{{ $orderStatus['pending'] }}</p>
            </div>
            <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-6 flex items-center justify-between">
            <div>
                <p class="text-sm text-zinc-500">Pesanan Diproses</p>
                <p class="text-base font-medium text-blue-600">{{ $orderStatus['processing'] }}</p>
            </div>
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-6 flex items-center justify-between">
            <div>
                <p class="text-sm text-zinc-500">Pesanan Selesai</p>
                <p class="text-base font-medium text-green-600">{{ $orderStatus['completed'] }}</p>
            </div>
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
            </div>
        </div>
    </div>

    {{-- DP Statistics --}}
    <div class="bg-white rounded-lg shadow-sm border p-6 mb-8">
        <p class="text-base font-medium text-zinc-800 mb-4">Statistik Down Payment</p>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
            <div class="text-center">
                <p class="text-base font-medium text-blue-600">{{ $dpStats['total_dp_orders'] }}</p>
                <p class="text-sm text-zinc-500">Total Pesanan DP</p>
            </div>
            <div class="text-center">
                <p class="text-base font-medium text-green-600">{{ $dpStats['paid_dp'] }}</p>
                <p class="text-sm text-zinc-500">DP Terbayar</p>
            </div>
            <div class="text-center">
                <p class="text-base font-medium text-yellow-600">{{ $dpStats['pending_dp'] }}</p>
                <p class="text-sm text-zinc-500">DP Pending</p>
            </div>
            <div class="text-center">
                <p class="text-base font-medium text-purple-600">{{ $dpStats['fully_paid'] }}</p>
                <p class="text-sm text-zinc-500">Lunas Penuh</p>
            </div>
            <div class="text-center">
                <p class="text-base font-medium text-gray-900">Rp {{ number_format($dpStats['remaining_revenue'], 0, ',', '.') }}</p>
                <p class="text-sm text-zinc-500">Pendapatan Sisa</p>
            </div>
        </div>
    </div>

    {{-- Recent Orders & Top Products --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        <div class="bg-white rounded-lg shadow-sm border">
            <div class="px-6 py-4 border-b flex items-center justify-between">
                <p class="text-base font-medium text-zinc-800">Pesanan Terbaru</p>
                <a href="{{ route('orders.index') }}" class="h-8 px-3 inline-flex items-center rounded-md text-sm font-medium text-zinc-800 hover:bg-zinc-800/5">Lihat Semua</a>
            </div>
            <div class="p-6 space-y-4">
                @foreach ($recentOrders as $order)
                    <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-b-0">
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-zinc-500">{{ $order['number'] }}</p>
                                <span class="inline-flex items-center text-xs font-medium py-1 px-2 rounded-md {{ $order['status_badge'] }}">{{ $order['status'] }}</span>
                            </div>
                            <p class="text-sm text-gray-600">{{ $order['customer'] }}</p>
                            <p class="text-sm text-gray-500">{{ $order['date'] }}</p>
                        </div>
                        <div class="text-right ml-4">
                            <p class="font-semibold text-gray-900">Rp {{ number_format($order['amount'], 0, ',', '.') }}</p>
                            <span class="inline-flex items-center text-xs font-medium py-1 px-2 rounded-md {{ $order['payment_badge'] }}">{{ $order['payment_type'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border">
            <div class="px-6 py-4 border-b flex items-center justify-between">
                <p class="text-base font-medium text-zinc-800">Produk Terpopuler</p>
                <a href="{{ route('products.index') }}" class="h-8 px-3 inline-flex items-center rounded-md text-sm font-medium text-zinc-800 hover:bg-zinc-800/5">Lihat Semua</a>
            </div>
            <div class="p-6 space-y-4">
                @foreach ($topProducts as $product)
                    <div class="flex items-center py-3 border-b border-gray-100 last:border-b-0">
                        <div class="flex-shrink-0 w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Z"/></svg>
                        </div>
                        <div class="flex-1 ml-4">
                            <p class="text-sm font-medium text-zinc-500">{{ $product['name'] }}</p>
                            <p class="text-sm text-gray-600">{{ $product['sold'] }} terjual (30 hari)</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-gray-900">Rp {{ number_format($product['revenue'], 0, ',', '.') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Monthly Revenue --}}
    <div class="mt-8 bg-white rounded-lg shadow-sm border">
        <div class="px-6 py-4 border-b">
            <p class="text-base font-medium text-zinc-800">Pendapatan 6 Bulan Terakhir</p>
        </div>
        <div class="p-6 space-y-4">
            @foreach ($monthlyRevenue as $month => $revenue)
                <div class="flex items-center justify-between py-2">
                    <p class="text-sm font-medium text-zinc-500">{{ $month }}</p>
                    <p class="text-base font-medium text-zinc-800">Rp {{ number_format($revenue, 0, ',', '.') }}</p>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="mt-8 bg-white rounded-lg shadow-sm border">
        <div class="px-6 py-4 border-b">
            <p class="text-base font-medium text-zinc-800">Aksi Cepat</p>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('products.create') }}" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                </div>
                <div>
                    <p class="font-medium text-blue-900">Tambah Produk</p>
                    <p class="text-sm text-blue-600">Produk baru</p>
                </div>
            </a>

            <a href="{{ route('coupons.create') }}" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z"/></svg>
                </div>
                <div>
                    <p class="font-medium text-green-900">Tambah Kupon</p>
                    <p class="text-sm text-green-600">Diskon baru</p>
                </div>
            </a>

            <a href="{{ route('orders.index') }}" class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z"/></svg>
                </div>
                <div>
                    <p class="font-medium text-purple-900">Kelola Pesanan</p>
                    <p class="text-sm text-purple-600">{{ $orderStatus['pending'] }} pending</p>
                </div>
            </a>

            <a href="{{ route('users.index') }}" class="flex items-center p-4 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors">
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-orange-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/></svg>
                </div>
                <div>
                    <p class="font-medium text-orange-900">Kelola User</p>
                    <p class="text-sm text-orange-600">{{ $stats['totalCustomers'] }} pelanggan</p>
                </div>
            </a>
        </div>
    </div>

    {{-- Active Coupons --}}
    <div class="mt-8 bg-white rounded-lg shadow-sm border">
        <div class="px-6 py-4 border-b flex items-center justify-between">
            <p class="text-base font-medium text-zinc-800">Kupon Aktif</p>
            <span class="inline-flex items-center text-xs font-medium py-1 px-2 rounded-md bg-green-100 text-green-800">{{ $activeCoupons }} aktif</span>
        </div>
        <div class="p-6 flex items-center justify-center py-8">
            <div class="text-center">
                <svg class="w-12 h-12 text-green-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25"/></svg>
                <p class="text-base font-medium text-zinc-800 mb-2">{{ $activeCoupons }} Kupon Aktif</p>
                <p class="text-sm text-zinc-500 mb-4">Kelola kupon untuk meningkatkan penjualan</p>
                <a href="{{ route('coupons.index') }}" class="h-10 px-4 inline-flex items-center rounded-lg text-sm font-medium bg-blue-600 text-white hover:bg-blue-700">Kelola Kupon</a>
            </div>
        </div>
    </div>

@endsection