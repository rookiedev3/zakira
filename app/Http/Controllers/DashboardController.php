<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\CustomerOrder;
use App\Models\CustomerOrderItem;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | NILAI STATUS (cek dengan data asli Anda)
    |--------------------------------------------------------------------------
    | Nama kolom sudah sesuai tabel customer_orders. Yang masih tebakan hanya
    | ISI kolom `status`. Cek nilai aslinya lewat tinker:
    |   DB::table('customer_orders')->distinct()->pluck('status');
    */
    // Sesuai dropdown: Menunggu, Diproses, Dikirim, Terkirim, Dibatalkan
    private const ST_PENDING    = 'pending';    // Menunggu
    private const ST_PROCESSING = 'processing'; // Diproses
    private const ST_SHIPPED    = 'shipped';    // Dikirim
    private const ST_DELIVERED  = 'delivered';  // Terkirim (dianggap "selesai")
    private const ST_CANCELLED  = 'cancelled';  // Dibatalkan

    // Nilai kolom payment_method untuk pesanan Down Payment
    private const PAY_DP = 'dp';

    public function index()
    {
        /*
         * Pesanan DP  : payment_method = 'dp'  ATAU dp_percent antara 1 dan 99.
         * Nominal DP  : kolom amount_due (jumlah yang harus dibayar saat checkout).
         * DP terbayar : dp_paid_at terisi.
         * Lunas penuh : remaining_paid_at terisi.
         */
        $dpOrders = fn () => CustomerOrder::where(function ($q) {
            $q->where('payment_method', self::PAY_DP)
              ->orWhere(fn ($w) => $w->where('dp_percent', '>', 0)->where('dp_percent', '<', 100));
        });

        /* ---------- Kartu utama ---------- */
        $stats = [
            'totalOrders'    => CustomerOrder::count(),
            // pesanan batal tidak dihitung sebagai pendapatan
            'totalRevenue'   => (int) CustomerOrder::where('status', '!=', self::ST_CANCELLED)->sum('total'),
            'totalCustomers' => User::where('role', '!=', 'admin')->count(),
            'totalProducts'  => Product::where('is_active', true)->count(),
        ];

        /* ---------- Status pesanan ---------- */
        $orderStatus = [
            'pending'    => CustomerOrder::where('status', self::ST_PENDING)->count(),
            'processing' => CustomerOrder::where('status', self::ST_PROCESSING)->count(),
            'completed'  => CustomerOrder::where('status', self::ST_DELIVERED)->count(),
        ];

        /* ---------- Statistik Down Payment ---------- */
        $dpStats = [
            'total_dp_orders'   => $dpOrders()->count(),
            'paid_dp'           => $dpOrders()->whereNotNull('dp_paid_at')->count(),
            'pending_dp'        => $dpOrders()->whereNull('dp_paid_at')->count(),
            'fully_paid'        => $dpOrders()->whereNotNull('remaining_paid_at')->count(),
            // DP yang sudah masuk
            'dp_revenue'        => (int) $dpOrders()
                ->whereNotNull('dp_paid_at')
                ->selectRaw('COALESCE(SUM(amount_due), 0) as v')
                ->value('v'),
            // Pelunasan yang sudah masuk (total - DP)
            'remaining_revenue' => (int) $dpOrders()
                ->whereNotNull('remaining_paid_at')
                ->selectRaw('COALESCE(SUM(total - amount_due), 0) as v')
                ->value('v'),
        ];

        /* ---------- Pesanan terbaru ---------- */
        // Class Tailwind ditulis lengkap agar tidak dibuang saat build
        $statusBadges = [
            self::ST_PENDING    => ['Menunggu',  'bg-yellow-100 text-yellow-800'],
            self::ST_PROCESSING => ['Diproses',  'bg-blue-100 text-blue-800'],
            self::ST_SHIPPED    => ['Dikirim',   'bg-indigo-100 text-indigo-800'],
            self::ST_DELIVERED  => ['Terkirim',  'bg-green-100 text-green-800'],
            self::ST_CANCELLED  => ['Dibatalkan', 'bg-red-100 text-red-800'],
        ];

        $recentOrders = CustomerOrder::latest()->take(5)->get()->map(function ($o) use ($statusBadges) {
            $key = strtolower((string) $o->status);
            [$label, $badge] = $statusBadges[$key] ?? [ucfirst($key ?: '-'), 'bg-gray-100 text-gray-800'];
            $isDp = $o->payment_method === self::PAY_DP || ($o->dp_percent > 0 && $o->dp_percent < 100);

            return [
                'number'        => '#' . $o->order_number,
                'customer'      => trim($o->first_name . ' ' . $o->last_name) ?: 'Guest',
                'date'          => $o->created_at->timezone('Asia/Jakarta')->format('d M Y H:i'),
                'amount'        => (int) $o->total,
                'status'        => $label,
                'status_badge'  => $badge,
                'payment_type'  => $isDp ? 'DP System' : 'Full Payment',
                'payment_badge' => $isDp ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800',
            ];
        })->all();

        /* ---------- Produk terpopuler (30 hari terakhir) ---------- */
        $topProducts = CustomerOrderItem::query()
            ->join('customer_orders', 'customer_orders.id', '=', 'customer_order_items.customer_order_id')
            ->where('customer_orders.created_at', '>=', now()->subDays(30))
            ->where('customer_orders.status', '!=', self::ST_CANCELLED)
            ->selectRaw(
                'MAX(customer_order_items.product_name) as name, '
                . 'SUM(customer_order_items.quantity) as sold, '
                . 'SUM(customer_order_items.subtotal) as revenue'
            )
            ->groupBy('customer_order_items.product_id')
            ->orderByDesc('sold')
            ->take(5)
            ->get()
            ->map(fn ($r) => [
                'name'    => $r->name,
                'sold'    => (int) $r->sold,
                'revenue' => (int) $r->revenue,
            ])->all();

        // Belum ada penjualan 30 hari terakhir: tampilkan produk aktif dengan 0 terjual
        if (empty($topProducts)) {
            $topProducts = Product::where('is_active', true)->latest()->take(5)->get()
                ->map(fn ($p) => ['name' => $p->name, 'sold' => 0, 'revenue' => 0])
                ->all();
        }

        /* ---------- Pendapatan 6 bulan terakhir (terbaru di atas) ---------- */
        $start = now()->startOfMonth()->subMonths(5);

        $perMonth = CustomerOrder::where('status', '!=', self::ST_CANCELLED)
            ->where('created_at', '>=', $start)
            ->get(['created_at', 'total'])
            ->groupBy(fn ($o) => $o->created_at->format('Y-m'))
            ->map(fn ($g) => (int) $g->sum('total'));

        $monthlyRevenue = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = Carbon::parse($start)->addMonths($i);
            $monthlyRevenue[$m->locale('id')->translatedFormat('M Y')] = $perMonth[$m->format('Y-m')] ?? 0;
        }

        /* ---------- Kupon aktif (pakai accessor Coupon::status) ---------- */
        $activeCoupons = Coupon::where('active', true)->get()
            ->filter(fn ($c) => $c->status === 'active')
            ->count();

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