<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\CustomerOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\URL;

class CheckoutController extends Controller
{
    private const SHIPPING = [
        'JNE',
        'J&T Express',
        'SiCepat',
        'AnterAja',
        'POS Indonesia',
        'Pickup / Ambil di Toko',
    ];

    private const PROVINCES = [
        'Aceh',
        'Sumatera Utara',
        'Sumatera Barat',
        'Riau',
        'Kepulauan Riau',
        'Jambi',
        'Sumatera Selatan',
        'Bangka Belitung',
        'Bengkulu',
        'Lampung',
        'DKI Jakarta',
        'Jawa Barat',
        'Jawa Tengah',
        'DI Yogyakarta',
        'Jawa Timur',
        'Banten',
        'Bali',
        'Nusa Tenggara Barat',
        'Nusa Tenggara Timur',
        'Kalimantan Barat',
        'Kalimantan Tengah',
        'Kalimantan Selatan',
        'Kalimantan Timur',
        'Kalimantan Utara',
        'Sulawesi Utara',
        'Sulawesi Tengah',
        'Sulawesi Selatan',
        'Sulawesi Tenggara',
        'Gorontalo',
        'Sulawesi Barat',
        'Maluku',
        'Maluku Utara',
        'Papua',
        'Papua Barat',
        'Papua Tengah',
        'Papua Pegunungan',
        'Papua Selatan',
        'Papua Barat Daya',
    ];

    /** Bawaan; bisa ditimpa lewat config/cart.php => 'bank_accounts'. */
    private const BANKS = [
        ['bank' => 'BSI', 'number' => '824682748372', 'name' => 'ADN'],
        ['bank' => 'BSI', 'number' => '20920029020',  'name' => 'Zahwa'],
    ];

    public function __construct(private CartController $cart) {}

    /** Halaman Checkout */
    public function show()
    {
        $cart = $this->cart->payload();

        if (empty($cart['items'])) {
            return redirect(url('/cart'))->with('error', 'Keranjang masih kosong.');
        }

        return view('checkout.index', [
            'cart'      => $cart,
            'shipping'  => self::SHIPPING,
            'provinces' => self::PROVINCES,
            'banks'     => config('cart.bank_accounts', self::BANKS),
        ]);
    }

    /** Tombol "Buat Pesanan" */
    public function store(Request $request)
    {
        $data = $request->validate([
            'seller_id'       => 'nullable|string|max:50',
            'whatsapp_number' => ['required', 'regex:/^(\+62|62|0)8[0-9]{8,12}$/'],
            'shipping_method' => ['required', Rule::in(self::SHIPPING)],
            'first_name'      => 'required|string|max:100',
            'last_name'       => 'required|string|max:100',
            'address'         => 'required|string|max:500',
            'city'            => 'required|string|max:100',
            'province'        => ['required', Rule::in(self::PROVINCES)],
            'postal_code'     => ['required', 'digits:5'],
            'notes'           => 'nullable|string|max:1000',
        ], [
            'whatsapp_number.regex' => 'Format nomor WhatsApp tidak valid. Contoh: 081234567890.',
            'postal_code.digits'    => 'Kode pos harus 5 digit angka.',
        ], [
            'whatsapp_number' => 'No WhatsApp',
            'shipping_method' => 'Ekspedisi',
            'first_name'      => 'Nama depan',
            'last_name'       => 'Nama belakang',
            'address'         => 'Alamat',
            'city'            => 'Kota',
            'province'        => 'Provinsi',
            'postal_code'     => 'Kode pos',
        ]);

        // Hitung ulang dari server (harga, kupon, total) — jangan percaya data dari browser
        $cart = $this->cart->payload();

        if (empty($cart['items'])) {
            return redirect(url('/cart'))->with('error', 'Keranjang masih kosong.');
        }

        $order = DB::transaction(function () use ($data, $cart) {
            $couponCode = null;

            if ($cart['coupon']) {
                $coupon = Coupon::whereRaw('LOWER(code) = ?', [mb_strtolower($cart['coupon']['code'])])
                    ->lockForUpdate()
                    ->first();

                if (! $coupon || ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit)) {
                    throw ValidationException::withMessages([
                        'coupon' => 'Kuota kupon sudah habis. Silakan periksa kembali kupon Anda.',
                    ]);
                }

                $coupon->increment('used_count');
                $couponCode = $coupon->code;
            }

            $isDp = $cart['payment_method'] === 'dp';

            $order = CustomerOrder::create([
                'order_number'    => $this->newOrderNumber(),
                'seller_id'       => $data['seller_id'] ?? null,
                'whatsapp_number' => $data['whatsapp_number'],
                'shipping_method' => $data['shipping_method'],
                'first_name'      => $data['first_name'],
                'last_name'       => $data['last_name'],
                'address'         => $data['address'],
                'city'            => $data['city'],
                'province'        => $data['province'],
                'postal_code'     => $data['postal_code'],
                'notes'           => $data['notes'] ?? null,
                'subtotal'        => $cart['subtotal'],
                'discount'        => $cart['discount'],
                'coupon_code'     => $couponCode,
                'total'           => $cart['total'],
                'payment_method'  => $cart['payment_method'],
                'dp_percent'      => $isDp ? $cart['dp_percent'] : null,
                'amount_due'      => $isDp ? $cart['dp_amount'] : $cart['total'],
                'status'          => 'pending',
            ]);

            foreach ($cart['items'] as $it) {
                $order->items()->create([
                    'product_id'   => $it['product_id'],
                    'product_name' => $it['name'],
                    'model'        => $it['model'],
                    'color'        => $it['color'],
                    'size'         => $it['size'],
                    'price'        => $it['unit_price'],
                    'quantity'     => $it['quantity'],
                    'subtotal'     => $it['line_total'],
                ]);
            }

            return $order;
        });

        session()->forget(['cart', 'coupon', 'payment_method']);
        session(['last_order' => $order->order_number]);

        return redirect()->route('checkout.success', $order->order_number);
    }

    /** Halaman pesanan berhasil (hanya untuk pembuat pesanan di sesi yang sama) */
    public function success(CustomerOrder $order)
    {
        abort_unless(session('last_order') === $order->order_number, 404);

        return view('checkout.success', [
            'order' => $order->load('items'),
            'banks' => config('cart.bank_accounts', self::BANKS),
            'paymentConfirmUrl' => URL::signedRoute('payment.confirmation', ['order' => $order->order_number]),
        ]);
    }

    private function newOrderNumber(): string
    {
        do {
            $number = 'ZKR-' . now()->format('ymd') . '-' . Str::upper(Str::random(5));
        } while (CustomerOrder::where('order_number', $number)->exists());

        return $number;
    }
}
