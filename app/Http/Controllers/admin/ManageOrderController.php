<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\CustomerOrder;
use App\Models\PaymentConfirmation;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ManageOrderController extends Controller
{
    private const STATUSES = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
    private const PAYMENT  = ['pending', 'paid', 'failed', 'refunded'];

    /** Daftar pesanan + filter (semua dari database) */
    public function index(Request $request)
    {
        $q = CustomerOrder::query()
            ->withCount('items')
            ->with([
                'items',
                'coupon', // relasi lewat kolom coupon_code
                'paymentConfirmations' => fn ($c) => $c->latest('id'),
            ])
            ->latest('id');

        if ($s = trim((string) $request->input('search'))) {
            $q->where(function ($w) use ($s) {
                $w->where('order_number', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('whatsapp_number', 'like', "%{$s}%")
                  ->orWhere('first_name', 'like', "%{$s}%")
                  ->orWhere('last_name', 'like', "%{$s}%")
                  ->orWhereRaw("CONCAT(first_name, ' ', last_name) like ?", ["%{$s}%"]);
            });
        }

        if (in_array($request->input('status_filter'), self::STATUSES, true)) {
            $q->where('status', $request->input('status_filter'));
        }

        if (in_array($request->input('payment_status_filter'), self::PAYMENT, true)) {
            $q->where('payment_status', $request->input('payment_status_filter'));
        }

        match ($request->input('dp_status_filter')) {
            'full_payment'      => $q->where('payment_method', 'full'),
            'dp_pending'        => $q->where('payment_method', 'dp')->whereNull('dp_paid_at'),
            'dp_paid'           => $q->where('payment_method', 'dp')->whereNotNull('dp_paid_at'),
            'remaining_pending' => $q->where('payment_method', 'dp')->whereNotNull('dp_paid_at')->whereNull('remaining_paid_at'),
            'fully_paid'        => $q->where('payment_method', 'dp')->whereNotNull('remaining_paid_at'),
            default             => null,
        };

        if ($brand = $request->input('brand_filter')) {
            $q->whereHas('items', fn ($i) => $i->whereIn(
                'product_id',
                DB::table('products')->where('brand_id', $brand)->select('id')
            ));
        }

        if ($product = $request->input('product_filter')) {
            $q->whereHas('items', fn ($i) => $i->where('product_id', $product));
        }

        return view('orders.index', [
            'orders'   => $q->paginate(15)->withQueryString(),
            'brands'   => DB::table('brands')->orderBy('name')->pluck('name', 'id'),
            'products' => DB::table('products')->orderBy('name')->pluck('name', 'id'),
            'coupons'  => Coupon::orderBy('code')->get(),
            // Dipakai dialog "Tambah Produk": varian & matriks harga tiap produk
            'productMeta' => $this->productMeta(),
        ]);
    }

    /** Nama varian (kolom nama bisa berbeda di tiap tabel varian) */
    private function variantName($variant): ?string
    {
        if (! $variant) {
            return null;
        }

        // product_colors/product_models memakai `name`, product_sizes memakai `size`
        return $variant->name ?? $variant->size ?? $variant->label ?? $variant->value ?? null;
    }

    /**
     * [product_id => [name, min, max, models, colors, sizes, prices]]
     * prices: kunci "modelId|colorId|sizeId" => harga (id kosong bila produk tak punya varian itu)
     */
    private function productMeta(): array
    {
        return Product::with(['prices', 'models', 'colors', 'sizes'])
            ->get()
            ->mapWithKeys(function (Product $p) {
                $toList = fn ($col) => $col->map(fn ($v) => [
                    'id'   => $v->id,
                    'name' => $this->variantName($v),
                ])->values()->all();

                // Ukuran yang ditandai tidak tersedia (is_available = false) tidak ditawarkan
                $sizes = $p->sizes->where('is_available', true)->values();

                $prices = [];
                foreach ($p->models->isEmpty() ? [null] : $p->models as $m) {
                    foreach ($p->colors->isEmpty() ? [null] : $p->colors as $c) {
                        foreach ($sizes->isEmpty() ? [null] : $sizes as $s) {
                            $key          = ($m?->id) . '|' . ($c?->id) . '|' . ($s?->id);
                            $prices[$key] = (int) $p->priceFor($m?->id, $c?->id, $s?->id);
                        }
                    }
                }

                return [$p->id => [
                    'name'   => $p->name,
                    'min'    => $prices ? min($prices) : 0,
                    'max'    => $prices ? max($prices) : 0,
                    'models' => $toList($p->models),
                    'colors' => $toList($p->colors),
                    'sizes'  => $toList($sizes),
                    'prices' => $prices,
                ]];
            })
            ->all();
    }

    /** Cocokkan teks varian dengan data produk, lalu cari harga yang tepat */
    private function resolvePrice(Product $product, ?string $model, ?string $color, ?string $size): int
    {
        $modelId = $product->models->first(fn ($m) => $this->variantName($m) === $model)?->id;
        $colorId = $product->colors->first(fn ($c) => $this->variantName($c) === $color)?->id;
        $sizeId  = $product->sizes->first(fn ($s) => $this->variantName($s) === $size)?->id;

        return (int) $product->priceFor($modelId, $colorId, $sizeId);
    }

    /** Simpan perubahan dari modal Edit Pesanan */
    public function update(Request $request, CustomerOrder $order)
    {
        $data = $request->validate([
            'status'                 => 'required|in:' . implode(',', self::STATUSES),
            'payment_status'         => 'required|in:' . implode(',', self::PAYMENT),
            'coupon_code'            => 'nullable|string|max:50|exists:coupons,code',
            'discount'               => 'nullable|numeric|min:0',

            // Informasi pelanggan, alamat, catatan
            'full_name'              => 'required|string|max:200',
            'seller_id'              => 'nullable|string|max:50',
            'email'                  => 'nullable|email|max:255',
            'whatsapp_number'        => 'required|string|max:20',
            'address'                => 'required|string|max:1000',
            'city'                   => 'required|string|max:100',
            'province'               => 'required|string|max:100',
            'postal_code'            => 'required|string|max:10',
            'notes'                  => 'nullable|string|max:2000',

            'items'                  => 'nullable|array',
            'items.*.id'             => 'required|integer',
            'items.*.quantity'       => 'required|integer|min:1|max:9999',
            'items.*.delete'         => 'nullable|boolean',
            'items.*.model'          => 'nullable|string|max:255',
            'items.*.color'          => 'nullable|string|max:255',
            'items.*.size'           => 'nullable|string|max:255',

            'new_items'              => 'nullable|array',
            'new_items.*.product_id' => 'required|exists:products,id',
            'new_items.*.quantity'   => 'required|integer|min:1|max:9999',
            'new_items.*.model'      => 'nullable|string|max:255',
            'new_items.*.color'      => 'nullable|string|max:255',
            'new_items.*.size'       => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($order, $data) {
            $order = CustomerOrder::whereKey($order->getKey())->lockForUpdate()->first();

            $oldTotal   = (int) $order->total;
            $oldCoupon  = $order->coupon_code;
            $oldPayment = $order->payment_status;

            // ---- 1. Item yang sudah ada: ubah qty/varian atau hapus ----
            $existing = $order->items()->get()->keyBy('id');

            foreach ($data['items'] ?? [] as $row) {
                $item = $existing->get((int) $row['id']);
                if (! $item) {
                    continue; // id bukan milik pesanan ini -> abaikan
                }

                if (! empty($row['delete'])) {
                    $item->delete();
                    continue;
                }

                $qty   = (int) $row['quantity'];
                $model = ($row['model'] ?? null) ?: null;
                $color = ($row['color'] ?? null) ?: null;
                $size  = ($row['size'] ?? null) ?: null;
                $price = (int) $item->price;

                // Varian berubah -> harga ikut dihitung ulang (bila harga varian baru diketahui)
                $variantChanged = ($item->model ?: null) !== $model
                    || ($item->color ?: null) !== $color
                    || ($item->size ?: null) !== $size;

                if ($variantChanged && $item->product_id && ($product = Product::with(['prices', 'models', 'colors', 'sizes'])->find($item->product_id))) {
                    $newPrice = $this->resolvePrice($product, $model, $color, $size);
                    if ($newPrice > 0) {
                        $price = $newPrice;
                    }
                }

                $item->forceFill([
                    'quantity' => $qty,
                    'price'    => $price,
                    'subtotal' => $price * $qty,
                    'model'    => $model,
                    'color'    => $color,
                    'size'     => $size,
                ])->save();
            }

            // ---- 2. Produk baru (harga dari product_prices sesuai model, warna & ukuran) ----
            foreach ($data['new_items'] ?? [] as $row) {
                $product = Product::with(['prices', 'models', 'colors', 'sizes'])->find($row['product_id']);
                if (! $product) {
                    continue;
                }

                $qty   = (int) $row['quantity'];
                $model = $row['model'] ?? null;
                $color = $row['color'] ?? null;
                $size  = $row['size'] ?? null;

                $price = $this->resolvePrice($product, $model, $color, $size);

                $order->items()->forceCreate([
                    'product_id'   => $product->id,
                    'product_name' => $product->name,
                    'model'        => $model ?: null,
                    'color'        => $color ?: null,
                    'size'         => $size ?: null,
                    'price'        => $price,
                    'quantity'     => $qty,
                    'subtotal'     => $price * $qty,
                ]);
            }

            // Pesanan wajib punya minimal 1 item
            $items = $order->items()->get();
            if ($items->isEmpty()) {
                throw ValidationException::withMessages([
                    'items' => 'Pesanan harus memiliki minimal 1 produk.',
                ]);
            }

            // ---- 3. Hitung ulang subtotal, diskon, total ----
            $subtotal   = (int) $items->sum(fn ($i) => (int) $i->price * (int) $i->quantity);
            $couponCode = $data['coupon_code'] ?? null;
            $coupon     = $couponCode ? Coupon::where('code', $couponCode)->first() : null;

            // Kupon baru yang batas pemakaiannya sudah habis ditolak
            if ($coupon && $oldCoupon !== $couponCode
                && $coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
                throw ValidationException::withMessages([
                    'coupon_code' => "Kupon {$coupon->code} sudah mencapai batas pemakaian.",
                ]);
            }

            // Diskon: nilai dari input admin; bila kosong/0 dihitung dari aturan kupon.
            // Tanpa kupon -> diskon selalu 0.
            $discountInput = (float) ($data['discount'] ?? 0);
            if ($coupon && $discountInput <= 0) {
                $discountInput = $coupon->type === 'percentage'
                    ? $subtotal * (float) $coupon->value / 100
                    : (float) $coupon->value;

                if ($coupon->type === 'percentage' && (float) $coupon->max_discount_amount > 0) {
                    $discountInput = min($discountInput, (float) $coupon->max_discount_amount);
                }
            }
            $discount = $coupon ? (int) min($subtotal, round($discountInput)) : 0;
            $total    = max(0, $subtotal - $discount);

            // ---- 4. Nominal DP ----
            // DP sudah dibayar -> nominal DP tidak diubah (hanya dibatasi maksimal total).
            // DP belum dibayar  -> DP mengikuti dp_percent terhadap total baru.
            $amountDue = (int) $order->amount_due;
            if ($order->payment_method === 'dp') {
                if ($order->dp_paid_at) {
                    $amountDue = min($amountDue, $total);
                } else {
                    $percent   = $order->dp_percent
                        ?: ($oldTotal > 0 ? round($amountDue / $oldTotal * 100) : 50);
                    $amountDue = (int) round($total * $percent / 100);
                }
            } else {
                $amountDue = $total;
            }

            // ---- 5. Simpan pesanan ----
            $nameParts = preg_split('/\s+/', trim($data['full_name']), 2);
            $firstName = $nameParts[0];
            $lastName  = $nameParts[1] ?? '';

            $order->forceFill([
                'status'         => $data['status'],
                'payment_status' => $data['payment_status'],
                'coupon_code'    => $couponCode,
                'discount'       => $discount,
                'subtotal'       => $subtotal,
                'total'          => $total,
                'amount_due'     => $amountDue,

                // Nama lengkap dipecah: kata pertama = first_name, sisanya = last_name
                'first_name'      => $firstName,
                'last_name'       => $lastName,
                'seller_id'       => $data['seller_id'] ?? null,
                'email'           => $data['email'] ?? null,
                'whatsapp_number' => $data['whatsapp_number'],
                'address'         => $data['address'],
                'city'            => $data['city'],
                'province'        => $data['province'],
                'postal_code'     => $data['postal_code'],
                'notes'           => $data['notes'] ?? null,
            ])->save();

            // ---- 6. Sesuaikan counter pemakaian kupon bila kupon berganti ----
            if ($oldCoupon !== $couponCode) {
                if ($oldCoupon) {
                    Coupon::where('code', $oldCoupon)->where('used_count', '>', 0)->decrement('used_count');
                }
                if ($couponCode) {
                    Coupon::where('code', $couponCode)->increment('used_count');
                }
            }

            // ---- 7. Sinkronkan bukti transfer (pesanan Bayar Penuh) bila status bayar berubah ----
            if ($order->payment_method === 'full' && $oldPayment !== $data['payment_status']) {
                if ($data['payment_status'] === 'paid')   $this->reviewLatestProof($order, 'verified');
                if ($data['payment_status'] === 'failed') $this->reviewLatestProof($order, 'rejected');
            }
        });

        return back()->with('success', "Pesanan {$order->order_number} berhasil diperbarui.");
    }

    /** Ubah status pesanan */
    public function updateStatus(Request $request, CustomerOrder $order)
    {
        $data = $request->validate(['status' => 'required|in:' . implode(',', self::STATUSES)]);

        $order->forceFill(['status' => $data['status']])->save();

        return back()->with('success', "Status pesanan {$order->order_number} diperbarui.");
    }

    /** Ubah status pembayaran (pesanan Bayar Penuh) */
    public function updatePayment(Request $request, CustomerOrder $order)
    {
        abort_unless($order->payment_method === 'full', 404);

        $data = $request->validate(['payment_status' => 'required|in:' . implode(',', self::PAYMENT)]);

        $order->forceFill(['payment_status' => $data['payment_status']])->save();

        if ($data['payment_status'] === 'paid')   $this->reviewLatestProof($order, 'verified');
        if ($data['payment_status'] === 'failed') $this->reviewLatestProof($order, 'rejected');

        return back()->with('success', "Status pembayaran {$order->order_number} diperbarui.");
    }

    /** Tandai DP lunas (pesanan Down Payment) */
    public function markDpPaid(CustomerOrder $order)
    {
        abort_unless($order->payment_method === 'dp' && ! $order->dp_paid_at, 404);

        $order->forceFill(['dp_paid_at' => now()])->save();
        $this->reviewLatestProof($order, 'verified');

        return back()->with('success', "DP pesanan {$order->order_number} ditandai lunas.");
    }

    /** Tandai sisa pembayaran lunas (pesanan Down Payment) */
    public function markRemainingPaid(CustomerOrder $order)
    {
        abort_unless($order->payment_method === 'dp' && $order->dp_paid_at && ! $order->remaining_paid_at, 404);

        $order->forceFill(['remaining_paid_at' => now(), 'payment_status' => 'paid'])->save();
        $this->reviewLatestProof($order, 'verified');

        return back()->with('success', "Sisa pembayaran {$order->order_number} ditandai lunas.");
    }

    /** Hapus pesanan beserta file bukti transfernya */
    public function destroy(CustomerOrder $order)
    {
        foreach ($order->paymentConfirmations as $c) {
            Storage::disk('public')->delete($c->proof_path);
        }

        $number = $order->order_number;
        $order->delete(); // item & konfirmasi ikut terhapus (cascade)

        return back()->with('success', "Pesanan {$number} dihapus.");
    }

    /** Tampilkan bukti di browser */
    public function showProof(PaymentConfirmation $confirmation)
    {
        abort_unless(Storage::disk('public')->exists($confirmation->proof_path), 404);

        return Storage::disk('public')->response($confirmation->proof_path);
    }

    /** Unduh bukti sebagai file */
    public function downloadProof(PaymentConfirmation $confirmation)
    {
        abort_unless(Storage::disk('public')->exists($confirmation->proof_path), 404);

        $name = 'bukti-' . $confirmation->id . '.' . pathinfo($confirmation->proof_path, PATHINFO_EXTENSION);

        return Storage::disk('public')->download($confirmation->proof_path, $name);
    }

    private function reviewLatestProof(CustomerOrder $order, string $status): void
    {
        $proof = $order->paymentConfirmations()->where('status', 'pending')->latest('id')->first();
        $proof?->update(['status' => $status]);
    }
}