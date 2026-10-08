<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Coupon;
use App\Models\CustomerOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /** Jenis pembayaran yang didukung: 'dp' dan 'remaining' (sisa). */
    private const PAY_TYPES = ['dp', 'remaining'];

    /** Daftar ekspedisi (sama dengan halaman checkout). */
    public const SHIPPING = [
        'JNE',
        'J&T Express',
        'SiCepat',
        'AnterAja',
        'POS Indonesia',
        'Pickup / Ambil di Toko',
    ];

    /** Daftar provinsi (sama dengan halaman checkout). */
    public const PROVINCES = [
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

    // ==================================================================
    // PROFIL & RIWAYAT PESANAN
    // ==================================================================

    public function show()
    {
        $user = Auth::user()->load('detail');

        // Riwayat pesanan milik user ini, dicocokkan lewat email atau no telp (10 per halaman)
        $orders = CustomerOrder::ownedBy($user)
            ->withCount('items')
            ->with(['items', 'invoice'])
            ->latest()
            ->paginate(10, ['*'], 'pesanan')
            ->withQueryString();

        // Statistik pesanan
        $base = CustomerOrder::ownedBy($user);
        $stats = [
            'total'  => (clone $base)->count(),
            'active' => (clone $base)->whereNotIn('status', ['delivered', 'cancelled'])->count(),
            'done'   => (clone $base)->where('status', 'delivered')->count(),
            'spent'  => (clone $base)->where('status', '!=', 'cancelled')->sum('total'),
        ];

        // Daftar ekspedisi & provinsi untuk dropdown di modal Edit Profil
        return view('member.profile', [
            'user'      => $user,
            'orders'    => $orders,
            'stats'     => $stats,
            'shipping'  => self::SHIPPING,
            'provinces' => self::PROVINCES,
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name'                => ['required', 'string', 'max:255'],
            'email'               => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone'               => ['nullable', 'string', 'max:20'],
            'address'             => ['nullable', 'string', 'max:500'],
            'city'                => ['nullable', 'string', 'max:100'],
            'province'            => ['nullable', Rule::in(self::PROVINCES)],
            'postal_code'         => ['nullable', 'string', 'max:10'],
            'seller_id'           => ['nullable', 'string', 'max:100'],
            'shipping_expedition' => ['nullable', Rule::in(self::SHIPPING)],
        ]);

        $user->update([
            'name'  => $data['name'],
            'email' => $data['email'],
        ]);

        $user->detail()->updateOrCreate(
            ['user_id' => $user->id],
            collect($data)->except(['name', 'email'])->toArray()
        );

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password'      => ['required', 'current_password'],
            'password'              => ['required', 'confirmed', Password::min(8)],
            'password_confirmation' => ['required'],
        ], [
            'password_confirmation.required' => 'Konfirmasi password baru wajib diisi.',
            'password.confirmed'             => 'Konfirmasi password tidak cocok.',
        ]);

        Auth::user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password berhasil diubah.');
    }

    // ==================================================================
    // FAKTUR
    // ==================================================================

    /**
     * Unduh faktur milik user. Faktur dibuat admin (model Invoice);
     * di sini hanya dicek kepemilikannya, lalu file dikirim lewat logika
     * download admin supaya PDF/Excel-nya persis sama.
     */
    public function invoice(string $orderNumber)
    {
        $order = $this->ownOrder($orderNumber, ['invoice']);

        abort_unless($order->invoice, 404, 'Faktur belum dibuat.');

        return app(\App\Http\Controllers\Admin\InvoiceController::class)->download($order->invoice);
    }

    // ==================================================================
    // PEMBAYARAN DP / SISA
    // ==================================================================

    /** Halaman pembayaran DP / Sisa (satu view: member.orders.pay) */
    public function payment(string $orderNumber, string $type)
    {
        abort_unless(in_array($type, self::PAY_TYPES, true), 404);

        $order = $this->ownOrder($orderNumber);

        if (! $this->canPay($order, $type)) {
            return redirect()->route('member.profile')->with('error', $this->payDeniedMessage($type));
        }

        return view('member.orders.pay', [
            'type'         => $type,
            'order'        => $order,
            'bankAccounts' => $this->bankAccounts(),
        ]);
    }

    /**
     * Upload bukti transfer DP / Sisa.
     * Disimpan sebagai PaymentConfirmation berstatus 'pending'; admin yang
     * menandai lunas lewat ManageOrderController (markDpPaid / markRemainingPaid).
     *
     * Begitu bukti tersimpan, pesanan tidak bisa diedit lagi (CustomerOrder::editBlockReason()).
     */
    public function storePayment(Request $request, string $orderNumber, string $type)
    {
        abort_unless(in_array($type, self::PAY_TYPES, true), 404);

        $order = $this->ownOrder($orderNumber);

        if (! $this->canPay($order, $type)) {
            return redirect()->route('member.profile')->with('error', $this->payDeniedMessage($type));
        }

        $request->validate([
            'payment_proof' => ['required', 'image', 'mimes:jpg,jpeg,png,gif', 'max:2048'],
        ]);

        $path = $request->file('payment_proof')->store('payment-proofs', 'public');

        // Kunci baris pesanan, lalu baca ulang datanya. Simpan edit pesanan juga mengunci baris yang sama,
        // jadi nominal di bawah selalu memakai total terbaru dan edit tidak bisa menyelip setelah bukti masuk.
        DB::transaction(function () use ($order, $type, $path) {
            $fresh = CustomerOrder::whereKey($order->id)->lockForUpdate()->firstOrFail();

            // Nominal yang seharusnya dibayar: DP = amount_due, Sisa = total - amount_due.
            $amount = $type === 'dp'
                ? (int) $fresh->amount_due
                : max(0, (int) $fresh->total - (int) $fresh->amount_due);

            // Kolom `amount` wajib diisi (NOT NULL tanpa default di tabel payment_confirmations).
            // Kolom `type` ('dp' / 'remaining') dipakai admin untuk memisahkan bukti DP dan pelunasan.
            $fresh->paymentConfirmations()->forceCreate([
                'type'          => $type,
                'amount'        => $amount,
                'account_name'  => trim($fresh->first_name . ' ' . $fresh->last_name),
                'transfer_date' => now('Asia/Jakarta')->toDateString(),
                'proof_path'    => $path,
                'status'        => 'pending',
            ]);
        });

        return redirect()->route('member.profile')->with(
            'success',
            $type === 'dp'
                ? 'Bukti pembayaran DP berhasil diunggah.'
                : 'Bukti pelunasan berhasil diunggah.'
        );
    }

    // ==================================================================
    // EDIT PESANAN
    // ==================================================================

    /** Halaman edit pesanan (view: member.orders.edit) */
    public function editOrder(string $orderNumber)
    {
        $order = $this->ownOrder($orderNumber, ['items']);

        if ($reason = $order->editBlockReason()) {
            return redirect()->route('member.profile')->with('error', $reason);
        }

        // Tombol "+ Variant" menambah variant dari produk pertama di pesanan ini
        $productId = $this->primaryProductId($order);
        $product   = $productId ? DB::table('products')->where('id', $productId)->first() : null;
        abort_if(! $product, 404, 'Produk pesanan tidak ditemukan.');

        $variants = $this->buildVariants($productId);

        // Cocokkan item pesanan (disimpan sebagai teks model/warna/ukuran) ke variant yang ada
        $variantIndex = $variants->keyBy(fn ($v) => $this->nameKey($v['model'], $v['color'], $v['size']));
        $placeholder  = $this->placeholder();
        $productImage = $this->imageUrl(data_get($product, 'image'));

        $items = $order->items->map(function ($item) use ($productId, $variantIndex, $productImage, $placeholder) {
            $match = $item->product_id == $productId
                ? $variantIndex->get($this->nameKey($item->model, $item->color, $item->size))
                : null;

            return [
                // Item yang tidak cocok dengan variant mana pun dipertahankan apa adanya ("old-{id}")
                'variant_id' => $match['id'] ?? 'old-' . $item->id,
                'name'       => $item->product_name,
                'image'      => ($match['image'] ?? null) ?: ($item->product_id == $productId ? $productImage : null) ?: $placeholder,
                'model'      => $item->model ?: '-',
                'color'      => $item->color ?: '-',
                'size'       => $item->size ?: '-',
                'price'      => (int) $item->price,
                'qty'        => (int) $item->quantity,
            ];
        })->values();

        $product = [
            'name'  => $product->name,
            'image' => $productImage ?: $placeholder,
        ];

        return view('member.orders.edit', compact('order', 'variants', 'product', 'items'));
    }

    /** Simpan perubahan pesanan */
    public function updateOrder(Request $request, string $orderNumber)
    {
        $data = $request->validate([
            'customer_name'      => ['required', 'string', 'max:100'],
            'whatsapp'           => ['required', 'string', 'max:20'],
            'email'              => ['nullable', 'email', 'max:255'],
            'province'           => ['required', 'string', 'max:100'],
            'city'               => ['required', 'string', 'max:100'],
            'postal_code'        => ['required', 'string', 'max:10'],
            'address'            => ['required', 'string', 'max:1000'],
            'notes'              => ['nullable', 'string', 'max:1000'],
            'coupon_code'        => ['nullable', 'string', 'max:50'],
            'items'              => ['required', 'array', 'min:1'],
            'items.*.variant_id' => ['required', 'string', 'distinct', 'regex:/^(\d+-\d+-\d+|old-\d+)$/'],
            'items.*.quantity'   => ['required', 'integer', 'min:1', 'max:999'],
        ]);

        $user = Auth::user()->load('detail');

        DB::transaction(function () use ($user, $orderNumber, $data) {
            // Kunci baris pesanan supaya tidak bentrok dengan upload bukti di saat yang sama
            $order = CustomerOrder::ownedBy($user)
                ->where('order_number', $orderNumber)
                ->lockForUpdate()
                ->firstOrFail();

            if ($reason = $order->editBlockReason()) {
                throw ValidationException::withMessages(['order' => $reason]);
            }

            $existing  = $order->items()->get();
            $byId      = $existing->keyBy('id');
            $productId = $this->primaryProductId($order);

            $rows     = [];
            $subtotal = 0;
            $qtyTotal = 0;

            foreach ($data['items'] as $i => $line) {
                $key = $line['variant_id'];
                $qty = (int) $line['quantity'];

                if (str_starts_with($key, 'old-')) {
                    $old = $byId->get((int) substr($key, 4));
                    if (! $old) {
                        throw ValidationException::withMessages(["items.$i.variant_id" => 'Item pesanan tidak ditemukan.']);
                    }
                    $row = [
                        'product_id'   => $old->product_id,
                        'product_name' => $old->product_name,
                        'model'        => $old->model,
                        'color'        => $old->color,
                        'size'         => $old->size,
                        'price'        => (int) $old->price,
                    ];
                } else {
                    $row = $this->resolveVariantRow($key, (int) $productId, $existing, "items.$i.variant_id");
                }

                $row['quantity'] = $qty;
                $row['subtotal'] = $row['price'] * $qty;
                $rows[]          = $row;

                $subtotal += $row['subtotal'];
                $qtyTotal += $qty;
            }

            // Total SELALU dihitung ulang di server, jangan percaya angka dari browser
            [$coupon, $couponError] = $this->resolveCoupon($data['coupon_code'] ?? null, $subtotal, $qtyTotal, $order->coupon_code);
            if ($couponError) {
                throw ValidationException::withMessages(['coupon_code' => $couponError]);
            }

            $discount = $coupon ? $this->discountFor($coupon, $subtotal) : 0;
            $total    = max(0, $subtotal - $discount);

            $amountDue = $order->payment_method === 'full'
                ? $total
                : (int) round($total * ((int) ($order->dp_percent ?? 30)) / 100);

            $name = Str::of($data['customer_name'])->squish();

            // Ganti item lama dengan item baru
            DB::table('customer_order_items')->where('customer_order_id', $order->id)->delete();
            DB::table('customer_order_items')->insert(array_map(
                fn ($r) => $r + [
                    'customer_order_id' => $order->id,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ],
                $rows
            ));

            $order->forceFill([
                'first_name'      => (string) $name->before(' '),
                'last_name'       => $name->contains(' ') ? (string) $name->after(' ') : '',
                'whatsapp_number' => $data['whatsapp'],
                'email'           => $data['email'] ?? null,
                'province'        => $data['province'],
                'city'            => $data['city'],
                'postal_code'     => $data['postal_code'],
                'address'         => $data['address'],
                'notes'           => $data['notes'] ?? null,
                'coupon_code'     => $coupon?->code,
                'subtotal'        => $subtotal,
                'discount'        => $discount,
                'total'           => $total,
                'amount_due'      => $amountDue,
                'edit_count'      => (int) $order->edit_count + 1,
                'last_edited_at'  => now(),
            ])->save();
        });

        return redirect()->route('member.profile')->with('success', 'Pesanan berhasil diperbarui.');
    }

    /** Cek kupon dari tombol "Terapkan" di halaman edit */
    public function checkCoupon(Request $request, string $orderNumber)
    {
        $order = $this->ownOrder($orderNumber);

        [$coupon, $error] = $this->resolveCoupon(
            $request->input('code'),
            (int) $request->input('subtotal'),
            (int) $request->input('quantity'),
            $order->coupon_code
        );

        if (! $coupon) {
            return response()->json(['valid' => false, 'message' => $error ?: 'Kode kupon tidak valid.']);
        }

        return response()->json([
            'valid'   => true,
            'code'    => Str::upper($coupon->code),
            'type'    => $coupon->type === 'percentage' ? 'percent' : 'nominal',
            'value'   => (float) $coupon->value,
            'max'     => (float) $coupon->max_discount_amount > 0 ? (int) $coupon->max_discount_amount : null,
            'message' => 'Kupon ' . Str::upper($coupon->code) . ' berhasil diterapkan.',
        ]);
    }

    // ==================================================================
    // HELPER: PESANAN & PEMBAYARAN
    // ==================================================================

    /** Pesanan milik user yang login (dicocokkan lewat email / no telp). */
    private function ownOrder(string $orderNumber, array $with = []): CustomerOrder
    {
        return CustomerOrder::with($with)
            ->where('order_number', $orderNumber)
            ->ownedBy(Auth::user()->load('detail'))
            ->firstOrFail();
    }

    /** Syarat boleh membayar. Dicek di halaman maupun saat upload. */
    private function canPay(CustomerOrder $order, string $type): bool
    {
        if ($order->payment_method === 'full' || $order->status === 'cancelled') {
            return false;
        }

        return $type === 'dp'
            ? ! $order->dp_paid_at
            : (bool) ($order->dp_paid_at && ! $order->remaining_paid_at);
    }

    private function payDeniedMessage(string $type): string
    {
        return $type === 'dp'
            ? 'Pesanan ini tidak memerlukan pembayaran DP.'
            : 'Pesanan ini tidak memerlukan pembayaran sisa.';
    }

    /**
     * Rekening aktif dari database, dinormalisasi ke format yang dipakai view
     * (bank_name, account_number, account_name, is_active).
     */
    private function bankAccounts()
    {
        return BankAccount::query()
            ->where('status', true) // sama seperti CheckoutController
            ->orderBy('id')
            ->get()
            ->map(fn ($b) => (object) [
                'bank_name'      => $b->bank_name,
                'account_number' => $b->account_number,
                'account_name'   => $b->account_holder_name,
                'is_active'      => true,
            ]);
    }

    // ==================================================================
    // HELPER: VARIANT (edit pesanan)
    // ==================================================================

    private function primaryProductId(CustomerOrder $order): ?int
    {
        $id = $order->items()->whereNotNull('product_id')->orderBy('id')->value('product_id');

        return $id ? (int) $id : null;
    }

    private function nameKey($model, $color, $size): string
    {
        return mb_strtolower(trim("$model|$color|$size"));
    }

    /**
     * Semua kombinasi model x warna x ukuran sebuah produk.
     * id variant = "{modelId}-{colorId}-{sizeId}".
     */
    private function buildVariants(int $productId): Collection
    {
        $models = DB::table('product_models')->where('product_id', $productId)->orderBy('id')->get();
        $colors = DB::table('product_colors')->where('product_id', $productId)->orderBy('id')->get();
        $sizes  = DB::table('product_sizes')->where('product_id', $productId)->orderBy('id')->get();
        $rules  = DB::table('product_price_rules')->where('product_id', $productId)->get();
        $prices = DB::table('product_prices')->where('product_id', $productId)->get()
            ->keyBy(fn ($p) => $p->product_model_id . '-' . $p->product_size_id);

        $out = collect();

        foreach ($models as $model) {
            foreach ($colors as $color) {
                foreach ($sizes as $size) {
                    $base = $prices->get($model->id . '-' . $size->id);
                    if (! $base) {
                        continue; // kombinasi tanpa harga tidak dijual
                    }

                    $out->push([
                        'id'         => "{$model->id}-{$color->id}-{$size->id}",
                        'model'      => $model->name,
                        'model_desc' => $model->description,
                        'color'      => $color->name,
                        'color_hex'  => $color->hex_code,
                        'size'       => $size->size,
                        // Tidak ada kolom stok; ketersediaan dari product_sizes.is_available (1 = tersedia)
                        'stock'      => $size->is_available ? 1 : 0,
                        'price'      => $this->priceFor((int) $base->price, $model, $color, $size, $rules),
                        'image'      => $this->imageUrl($color->image ?: $model->image),
                    ]);
                }
            }
        }

        return $out;
    }

    /**
     * Harga = harga dasar (model x ukuran) + aturan di product_price_rules yang cocok.
     * CATATAN: arti kolom `type` di price_rules diasumsikan; samakan dengan logic checkout.
     */
    private function priceFor(int $base, object $model, object $color, object $size, $rules): int
    {
        $price = $base;

        foreach ($rules as $r) {
            if ($r->product_model_id && $r->product_model_id != $model->id) continue;
            if ($r->product_color_id && $r->product_color_id != $color->id) continue;
            if ($r->product_size_id && $r->product_size_id != $size->id) continue;

            $type  = Str::lower($r->type);
            $delta = in_array($type, ['percent', 'percentage'], true)
                ? (int) round($base * $r->amount / 100)
                : (int) $r->amount;

            $price += in_array($type, ['minus', 'sub', 'discount'], true) ? -$delta : $delta;
        }

        return max(0, $price);
    }

    /** Ubah id variant "m-c-s" jadi baris item. Divalidasi ulang ke database. */
    private function resolveVariantRow(string $key, int $productId, Collection $existing, string $field): array
    {
        [$modelId, $colorId, $sizeId] = array_map('intval', explode('-', $key));

        $model = DB::table('product_models')->where('id', $modelId)->where('product_id', $productId)->first();
        $color = DB::table('product_colors')->where('id', $colorId)->where('product_id', $productId)->first();
        $size  = DB::table('product_sizes')->where('id', $sizeId)->where('product_id', $productId)->first();

        if (! $model || ! $color || ! $size) {
            throw ValidationException::withMessages([$field => 'Variant tidak valid.']);
        }

        $name = DB::table('products')->where('id', $productId)->value('name');

        // Item yang memang sudah ada di pesanan tetap memakai harganya yang lama
        $same = $existing->first(fn ($it) => $it->product_id == $productId
            && $this->nameKey($it->model, $it->color, $it->size) === $this->nameKey($model->name, $color->name, $size->size));

        if ($same) {
            $price = (int) $same->price;
        } else {
            if (! $size->is_available) {
                throw ValidationException::withMessages([$field => "Ukuran {$size->size} sedang tidak tersedia."]);
            }

            $base = DB::table('product_prices')
                ->where('product_model_id', $model->id)
                ->where('product_size_id', $size->id)
                ->value('price');

            if ($base === null) {
                throw ValidationException::withMessages([$field => 'Harga variant belum diatur.']);
            }

            $rules = DB::table('product_price_rules')->where('product_id', $productId)->get();
            $price = $this->priceFor((int) $base, $model, $color, $size, $rules);
        }

        return [
            'product_id'   => $productId,
            'product_name' => $name,
            'model'        => $model->name,
            'color'        => $color->name,
            'size'         => $size->size,
            'price'        => $price,
        ];
    }

    // ==================================================================
    // HELPER: KUPON (edit pesanan)
    // ==================================================================

    /**
     * @return array{0: ?Coupon, 1: ?string} [kupon, pesan error]
     *
     * Versi sederhana. Belum mencakup: batasan brand/kategori/produk (coupon_brand/category/product),
     * usage_limit_per_customer, new_customers_only. Samakan dengan validasi kupon di checkout.
     */
    private function resolveCoupon(?string $code, int $subtotal, int $qty, ?string $currentCode): array
    {
        $code = Str::upper(trim((string) $code));
        if ($code === '') {
            return [null, null];
        }

        $coupon = Coupon::whereRaw('UPPER(code) = ?', [$code])->first();
        if (! $coupon || ! $coupon->active) {
            return [null, 'Kode kupon tidak valid.'];
        }

        // Kupon yang sudah dipakai di pesanan ini tidak dicek tanggal/kuota lagi
        $isCurrent = $currentCode && Str::upper($currentCode) === $code;

        if (! $isCurrent) {
            if ($coupon->starts_at && now()->lt(Carbon::parse($coupon->starts_at))) {
                return [null, 'Kupon belum aktif.'];
            }
            if ($coupon->expires_at && now()->gt(Carbon::parse($coupon->expires_at))) {
                return [null, 'Kupon sudah kedaluwarsa.'];
            }
            if ($coupon->usage_limit !== null && $coupon->used_count >= $coupon->usage_limit) {
                return [null, 'Kuota kupon sudah habis.'];
            }
        }

        // Pesanan ini milik member yang login
        if ($coupon->customer_scope === 'non_member') {
            return [null, 'Kupon ini hanya untuk non member.'];
        }

        if ((float) $coupon->minimum_amount > 0 && $subtotal < (float) $coupon->minimum_amount) {
            return [null, 'Minimal belanja Rp ' . number_format((float) $coupon->minimum_amount, 0, ',', '.') . ' untuk kupon ini.'];
        }
        if ((int) $coupon->minimum_quantity > 0 && $qty < (int) $coupon->minimum_quantity) {
            return [null, 'Minimal ' . (int) $coupon->minimum_quantity . ' item untuk kupon ini.'];
        }

        return [$coupon, null];
    }

    private function discountFor(Coupon $coupon, int $subtotal): int
    {
        if ($coupon->type === 'percentage') {
            $discount = (int) round($subtotal * (float) $coupon->value / 100);
            if ((float) $coupon->max_discount_amount > 0) {
                $discount = min($discount, (int) $coupon->max_discount_amount);
            }

            return min($discount, $subtotal);
        }

        return min((int) $coupon->value, $subtotal);
    }

    // ==================================================================
    // HELPER: GAMBAR
    // ==================================================================

    private function imageUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return Str::startsWith($path, ['http://', 'https://'])
            ? $path
            : Storage::disk('public')->url($path);
    }

    private function placeholder(): string
    {
        return 'data:image/svg+xml;utf8,' . rawurlencode(
            '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200"><rect width="200" height="200" fill="#e5e7eb"/><text x="100" y="108" font-size="16" text-anchor="middle" fill="#9ca3af">Produk</text></svg>'
        );
    }
}