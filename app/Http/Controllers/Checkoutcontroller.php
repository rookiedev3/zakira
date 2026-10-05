<?php

namespace App\Http\Controllers;

use App\Models\AdminHandle;
use App\Models\BankAccount;
use App\Models\Coupon;
use App\Models\CustomerOrder;
use App\Models\Seller;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

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

    public function __construct(private CartController $cart) {}

    /** Halaman Checkout */
    public function show()
    {
        $cart = $this->cart->payload();

        if (empty($cart['items'])) {
            return redirect(url('/cart'))->with('error', 'Keranjang masih kosong.');
        }

        return view('checkout.index', [
            'cart'         => $cart,
            'shipping'     => self::SHIPPING,
            'provinces'    => self::PROVINCES,
            'banks'        => $this->banks(),
            'prefill'      => $this->prefill(),
            'adminHandles' => AdminHandle::orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Data awal formulir dari akun yang sedang login (user + user_details).
     * Tamu (belum login) mendapat isian kosong. Nilai provinsi & ekspedisi
     * hanya dipakai bila cocok dengan daftar pilihan di halaman ini.
     */
    private function prefill(): array
    {
        $empty = [
            'seller_id'       => '',
            'admin_handle_id' => '',
            'whatsapp_number' => '',
            'shipping_method' => '',
            'first_name'      => '',
            'last_name'       => '',
            'address'         => '',
            'city'            => '',
            'province'        => '',
            'postal_code'     => '',
        ];

        $user = Auth::user();
        if (! $user) {
            return $empty;
        }

        $detail = UserDetail::where('user_id', $user->id)->first();

        // Nama lengkap dipecah: kata pertama = nama depan, sisanya = nama belakang
        $parts = preg_split('/\s+/', trim((string) ($user->name ?? '')), 2);

        return [
            'seller_id'       => (string) ($detail->seller_id ?? ''),
            'admin_handle_id' => '', // dipilih manual oleh pembeli di halaman checkout
            'whatsapp_number' => (string) ($detail->phone ?? ''),
            'shipping_method' => $this->matchOption($detail->shipping_expedition ?? null, self::SHIPPING),
            'first_name'      => $parts[0] ?? '',
            'last_name'       => $parts[1] ?? '',
            'address'         => (string) ($detail->address ?? ''),
            'city'            => (string) ($detail->city ?? ''),
            'province'        => $this->matchOption($detail->province ?? null, self::PROVINCES),
            'postal_code'     => (string) ($detail->postal_code ?? ''),
        ];
    }

    /** Cocokkan nilai (tanpa peduli huruf besar/kecil) ke daftar pilihan; kosong bila tidak cocok. */
    private function matchOption(?string $value, array $options): string
    {
        $value = mb_strtolower(trim((string) $value));
        if ($value === '') {
            return '';
        }

        foreach ($options as $option) {
            if (mb_strtolower($option) === $value) {
                return $option;
            }
        }

        return '';
    }

    /** Cek ID seller (dipakai tombol "Cek ID" di halaman checkout) */
    public function lookupSeller(Request $request)
    {
        $id = trim((string) $request->query('seller_id'));

        $seller = $id !== ''
            ? Seller::whereRaw('LOWER(seller_id) = ?', [mb_strtolower($id)])->first()
            : null;

        return $seller
            ? response()->json(['found' => true, 'name' => $seller->name])
            : response()->json(['found' => false], 404);
    }

    /** Tombol "Buat Pesanan" */
    public function store(Request $request)
    {
        $data = $request->validate([
            'seller_id'       => ['required', 'string', 'max:50', Rule::exists('sellers', 'seller_id')],
            'admin_handle_id' => ['required', 'integer', Rule::exists('admin_handles', 'id')],
            'whatsapp_number' => ['required', 'regex:/^(\+62|62|0)8[0-9]{8,12}$/'],
            'shipping_method' => ['required', Rule::in(self::SHIPPING)],
            'first_name'      => 'required|string|max:100',
            'last_name'       => 'required|string|max:100',
            'address'         => 'required|string|max:500',
            'city'            => 'required|string|max:100',
            'province'        => ['required', Rule::in(self::PROVINCES)],
            'postal_code'     => ['required', 'digits:5'],
            'notes'           => 'nullable|string|max:1000',

            // Alamat pengiriman berbeda (wajib hanya bila checkbox dicentang)
            'ship_different'   => ['nullable', 'boolean'],
            'ship_recipient'   => ['required_if:ship_different,1', 'nullable', 'string', 'max:100'],
            'ship_phone'       => ['required_if:ship_different,1', 'nullable', 'regex:/^(\+62|62|0)8[0-9]{8,12}$/'],
            'ship_address'     => ['required_if:ship_different,1', 'nullable', 'string', 'max:500'],
            'ship_city'        => ['required_if:ship_different,1', 'nullable', 'string', 'max:100'],
            'ship_province'    => ['required_if:ship_different,1', 'nullable', Rule::in(self::PROVINCES)],
            'ship_postal_code' => ['required_if:ship_different,1', 'nullable', 'digits:5'],
        ], [
            'seller_id.required'        => 'ID Seller wajib diisi.',
            'seller_id.exists'          => 'ID Seller tidak ditemukan.',
            'admin_handle_id.required'  => 'Admin Handle wajib dipilih.',
            'admin_handle_id.exists'    => 'Admin Handle tidak valid.',
            'whatsapp_number.regex'     => 'Format nomor WhatsApp tidak valid. Contoh: 081234567890.',
            'postal_code.digits'        => 'Kode pos harus 5 digit angka.',
            'ship_phone.regex'          => 'Format nomor WhatsApp penerima tidak valid. Contoh: 081234567890.',
            'ship_postal_code.digits'   => 'Kode pos penerima harus 5 digit angka.',
            'required_if'               => ':attribute wajib diisi.',
        ], [
            'seller_id'        => 'ID Seller',
            'admin_handle_id'  => 'Admin Handle',
            'whatsapp_number'  => 'No WhatsApp',
            'shipping_method'  => 'Ekspedisi',
            'first_name'       => 'Nama depan',
            'last_name'        => 'Nama belakang',
            'address'          => 'Alamat',
            'city'             => 'Kota',
            'province'         => 'Provinsi',
            'postal_code'      => 'Kode pos',
            'ship_recipient'   => 'Nama penerima',
            'ship_phone'       => 'No WhatsApp penerima',
            'ship_address'     => 'Alamat penerima',
            'ship_city'        => 'Kota penerima',
            'ship_province'    => 'Provinsi penerima',
            'ship_postal_code' => 'Kode pos penerima',
        ]);

        $shipDifferent = $request->boolean('ship_different');

        // Email diambil dari akun yang login; null jika pembeli adalah tamu
        $email = Auth::user()?->email;

        // Ambil seller dari database (case-insensitive, sama seperti lookupSeller)
        $seller = Seller::whereRaw('LOWER(seller_id) = ?', [mb_strtolower($data['seller_id'])])->firstOrFail();

        // Ambil admin handle dari database berdasarkan ID yang dipilih
        $admin = AdminHandle::findOrFail($data['admin_handle_id']);

        // Hitung ulang dari server (harga, kupon, total) — jangan percaya data dari browser
        $cart = $this->cart->payload();

        if (empty($cart['items'])) {
            return redirect(url('/cart'))->with('error', 'Keranjang masih kosong.');
        }

        $order = DB::transaction(function () use ($data, $cart, $seller, $admin, $shipDifferent, $email) {
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
                'order_number'     => $this->newOrderNumber(),
                'seller_id'        => $seller->seller_id,
                // 'seller_name'   => $seller->name, // aktifkan jika tabel customer_orders punya kolom seller_name
                'admin_handle_id'  => $admin->id,
                'email'            => $email,
                'whatsapp_number'  => $data['whatsapp_number'],
                'shipping_method'  => $data['shipping_method'],
                'first_name'       => $data['first_name'],
                'last_name'        => $data['last_name'],
                'address'          => $data['address'],
                'city'             => $data['city'],
                'province'         => $data['province'],
                'postal_code'      => $data['postal_code'],
                'notes'            => $data['notes'] ?? null,
                'ship_different'   => $shipDifferent,
                'ship_recipient'   => $shipDifferent ? $data['ship_recipient'] : null,
                'ship_phone'       => $shipDifferent ? $data['ship_phone'] : null,
                'ship_address'     => $shipDifferent ? $data['ship_address'] : null,
                'ship_city'        => $shipDifferent ? $data['ship_city'] : null,
                'ship_province'    => $shipDifferent ? $data['ship_province'] : null,
                'ship_postal_code' => $shipDifferent ? $data['ship_postal_code'] : null,
                'subtotal'         => $cart['subtotal'],
                'discount'         => $cart['discount'],
                'coupon_code'      => $couponCode,
                'total'            => $cart['total'],
                'payment_method'   => $cart['payment_method'],
                'dp_percent'       => $isDp ? $cart['dp_percent'] : null,
                'amount_due'       => $isDp ? $cart['dp_amount'] : $cart['total'],
                'status'           => 'pending',
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
            'order'             => $order->load('items'),
            'banks'             => $this->banks(),
            'paymentConfirmUrl' => URL::signedRoute('payment.confirmation', ['order' => $order->order_number]),
        ]);
    }

    /**
     * Rekening bank dari database, dinormalisasi ke format yang dipakai view
     * (bank, number, name) sehingga Blade tidak perlu diubah.
     */
    private function banks(): array
    {
        return BankAccount::query()
            ->where('status', true) // sesuaikan: boolean => true, string => 'active'
            ->orderBy('id')
            ->get()
            ->map(fn ($b) => [
                'bank'   => $b->bank_name,
                'number' => $b->account_number,
                'name'   => $b->account_holder_name,
            ])
            ->all();
    }

    private function newOrderNumber(): string
    {
        do {
            $number = 'ZKR-' . now()->format('ymd') . '-' . Str::upper(Str::random(5));
        } while (CustomerOrder::where('order_number', $number)->exists());

        return $number;
    }
}x