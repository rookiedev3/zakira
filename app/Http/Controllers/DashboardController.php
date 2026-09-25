<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman dashboard.
     *
     * Semua data di bawah masih HARDCODE (belum query ke database).
     * Nanti tinggal ganti isi array ini dengan query Eloquent yang sesuai,
     * misalnya:
     *   'totalOrders' => Order::count(),
     *   'totalRevenue' => Order::sum('total'),
     */
    public function index()
    {
        $stats = [
            'totalOrders'    => 5,
            'totalRevenue'   => 1490000,
            'totalCustomers' => 2,
            'totalProducts'  => 1,
        ];

        $orderStatus = [
            'pending'    => 4,
            'processing' => 1,
            'completed'  => 0,
        ];

        $dpStats = [
            'total_dp_orders'   => 3,
            'paid_dp'           => 2,
            'pending_dp'        => 3,
            'fully_paid'        => 1,
            'dp_revenue'        => 180000,
            'remaining_revenue' => 350000,
        ];

        // status_badge / payment_badge sudah berupa class Tailwind siap pakai
        // (bukan nama warna saja), supaya Tailwind tidak "membuang" class saat build.
        $recentOrders = [
            [
                'number'        => '#ORD260922170018I42',
                'customer'      => 'User Customer',
                'date'          => '22 Sep 2026 17:00',
                'amount'        => 500000,
                'status'        => 'Pending',
                'status_badge'  => 'bg-yellow-100 text-yellow-800',
                'payment_type'  => 'DP System',
                'payment_badge' => 'bg-blue-100 text-blue-800',
            ],
            [
                'number'        => '#ORD260922135407WHQ',
                'customer'      => 'Guest',
                'date'          => '22 Sep 2026 13:54',
                'amount'        => 100000,
                'status'        => 'Pending',
                'status_badge'  => 'bg-yellow-100 text-yellow-800',
                'payment_type'  => 'DP System',
                'payment_badge' => 'bg-blue-100 text-blue-800',
            ],
            [
                'number'        => '#ORD260918162345LPM',
                'customer'      => 'User Admin',
                'date'          => '18 Sep 2026 16:23',
                'amount'        => 100000,
                'status'        => 'Pending',
                'status_badge'  => 'bg-yellow-100 text-yellow-800',
                'payment_type'  => 'Full Payment',
                'payment_badge' => 'bg-green-100 text-green-800',
            ],
            [
                'number'        => '#ORD26091814172373H',
                'customer'      => 'Guest',
                'date'          => '18 Sep 2026 14:17',
                'amount'        => 300000,
                'status'        => 'Pending',
                'status_badge'  => 'bg-yellow-100 text-yellow-800',
                'payment_type'  => 'DP System',
                'payment_badge' => 'bg-blue-100 text-blue-800',
            ],
            [
                'number'        => '#ORD260917142416TFC',
                'customer'      => 'User Admin',
                'date'          => '17 Sep 2026 14:24',
                'amount'        => 990000,
                'status'        => 'Processing',
                'status_badge'  => 'bg-blue-100 text-blue-800',
                'payment_type'  => 'Full Payment',
                'payment_badge' => 'bg-green-100 text-green-800',
            ],
        ];

        $topProducts = [
            [
                'name'    => 'Baju Koko',
                'sold'    => 0,
                'revenue' => 0,
            ],
        ];

        $monthlyRevenue = [
            'Sep 2026' => 1490000,
        ];

        $activeCoupons = 0;

        return view('admin.dashboard', compact(
            'stats',
            'orderStatus',
            'dpStats',
            'recentOrders',
            'topProducts',
            'monthlyRevenue',
            'activeCoupons'
        ));
    }
}