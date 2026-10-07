<?php

namespace App\Exports;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Export pesanan ke Excel dengan setting dari modal "Export Pesanan ke Excel".
 *
 * Opsi ($options):
 *  - columns     : array key kolom (lihat COLUMNS); kosong = DEFAULT_COLUMNS
 *  - mode        : ordered_only | all_variants
 *  - format      : consolidated (1 baris / pesanan) | detailed (1 baris / item)
 *  - sorting     : model_first | size_first (urutan kombinasi varian)
 *  - product_ids : null | array id produk; bila diisi, hanya item produk tsb yang diekspor
 */
class OrdersExport
{
    /** key => label (label dipakai juga oleh modal di view) */
    public const COLUMNS = [
        'order_number'           => 'Order Number',
        'created_at'             => 'Tanggal Pesanan',
        'seller_name'            => 'Nama Seller',
        'seller_id'              => 'ID Seller',
        'customer_name'          => 'Nama Pelanggan',
        'email'                  => 'Email',
        'whatsapp_number'        => 'WhatsApp',
        'shipping_address'       => 'Alamat Pengiriman',
        'items'                  => 'Item Pesanan (Gabungan)',
        'product_names'          => 'Nama Produk (Terpisah)',
        'product_quantities'     => 'Kuantitas (Terpisah)',
        'subtotal'               => 'Subtotal',
        'coupon_discount'        => 'Diskon Kupon',
        'total'                  => 'Total',
        'order_status'           => 'Status Pesanan',
        'payment_status'         => 'Status Pembayaran',
        'payment_method'         => 'Metode Pembayaran',
        'payment_proof'          => 'Bukti Pembayaran',
        'dp_amount'              => 'Jumlah DP',
        'dp_status'              => 'Status DP',
        'remaining_amount'       => 'Sisa Pembayaran',
        'dp_paid_at'             => 'Tanggal DP Lunas',
        'remaining_paid_at'      => 'Tanggal Sisa Lunas',
        'payment_method_type'    => 'Tipe Pembayaran',
        'admin_handle'           => 'Admin Handle',
        'notes'                  => 'Catatan',
        'product_categories'     => 'Kategori Produk',
        'product_variants_model' => 'Varian Model',
        'product_variants_size'  => 'Varian Ukuran',
        'product_variants_color' => 'Varian Warna',
        'variant_combinations'   => 'Kombinasi Varian',
    ];

    /** Kolom yang tercentang secara default */
    public const DEFAULT_COLUMNS = [
        'order_number', 'created_at', 'seller_name', 'customer_name', 'email', 'whatsapp_number',
        'product_names', 'product_quantities', 'total', 'order_status', 'payment_status', 'payment_proof',
    ];

    /** Kolom berformat uang */
    private const MONEY = ['subtotal', 'coupon_discount', 'total', 'dp_amount', 'remaining_amount'];

    /** Kolom teks panjang / banyak baris: lebar tetap + wrap */
    private const WIDE = [
        'shipping_address'       => 40,
        'items'                  => 48,
        'product_names'          => 34,
        'product_quantities'     => 14,
        'product_categories'     => 24,
        'product_variants_model' => 20,
        'product_variants_size'  => 14,
        'product_variants_color' => 18,
        'notes'                  => 36,
    ];

    private const STATUS = [
        'pending' => 'Menunggu', 'processing' => 'Diproses', 'shipped' => 'Dikirim',
        'delivered' => 'Terkirim', 'cancelled' => 'Dibatalkan',
    ];

    private const PAYMENT = [
        'pending' => 'Menunggu', 'paid' => 'Lunas', 'failed' => 'Gagal', 'refunded' => 'Dikembalikan',
    ];

    private array $columns;
    private bool $withCombos;
    private bool $detailed;
    private bool $allVariants;
    private bool $sizeFirst;
    private ?array $productIds;

    private Collection $products;
    private array $categories = [];
    private $handles;

    private function __construct(array $o)
    {
        $wanted = array_filter((array) ($o['columns'] ?? []), 'is_string');
        $picked = array_values(array_intersect(array_keys(self::COLUMNS), $wanted)) ?: self::DEFAULT_COLUMNS;

        $this->withCombos = in_array('variant_combinations', $picked, true);
        $this->columns    = array_values(array_diff($picked, ['variant_combinations']));
        if (! $this->columns) {
            $this->columns = ['order_number']; // minimal 1 kolom identitas
        }

        $this->detailed    = ($o['format'] ?? 'consolidated') === 'detailed';
        $this->allVariants = ($o['mode'] ?? 'ordered_only') === 'all_variants';
        $this->sizeFirst   = ($o['sorting'] ?? 'model_first') === 'size_first';
        $this->productIds  = isset($o['product_ids']) ? array_map('intval', $o['product_ids']) : null;
    }

    public static function download(Builder $query, array $options, string $filename): StreamedResponse
    {
        $spreadsheet = (new self($options))->build($query);

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /* =====================================================================
     |  Membangun workbook
     ===================================================================== */

    private function build(Builder $query): Spreadsheet
    {
        $orders = $query->get();

        $productIds = $orders->flatMap(fn ($o) => $o->items->pluck('product_id'))
            ->filter()->unique()->values()->all();

        $this->products   = Product::with(['models', 'colors', 'sizes'])->whereIn('id', $productIds)->get()->keyBy('id');
        $this->categories = $this->loadCategories($productIds);
        $this->handles    = DB::table('admin_handles')->pluck('name', 'id');

        // [pesanan, daftar baris-produk (lines)]
        $all = [];
        foreach ($orders as $o) {
            $all[] = [$o, $this->lines($o)];
        }

        // Baris sheet: ringkas = 1 / pesanan, detail = 1 / item
        $rows = [];
        foreach ($all as [$o, $lines]) {
            if ($this->detailed) {
                foreach ($lines ?: [null] as $line) {
                    $rows[] = [$o, $lines, $line];
                }
            } else {
                $rows[] = [$o, $lines, null];
            }
        }

        // Kolom dinamis "Kombinasi Varian"
        $combos = [];
        if ($this->withCombos) {
            foreach ($all as [, $lines]) {
                foreach ($lines as $l) {
                    $k = $this->key($l);
                    if (! isset($combos[$k])) {
                        $combos[$k] = ['product' => $l['name'], 'label' => $this->label($l), 'sort' => $this->sortKey($l)];
                    }
                }
            }
            uasort($combos, fn ($a, $b) => strcasecmp($a['product'], $b['product'])
                ?: ($a['sort'] <=> $b['sort'])
                ?: strcasecmp($a['label'], $b['label']));
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Pesanan');

        // Header
        $headings = array_map(fn ($k) => self::COLUMNS[$k], $this->columns);
        foreach ($combos as $c) {
            $headings[] = $c['product'] . ' | ' . $c['label'];
        }
        foreach ($headings as $i => $h) {
            $this->put($sheet, $i + 1, 1, $h);
        }

        // Data
        $r = 2;
        $totals = array_fill_keys(array_keys($combos), 0);

        foreach ($rows as [$o, $lines, $line]) {
            $c = 1;
            foreach ($this->columns as $key) {
                $this->put($sheet, $c++, $r, $this->cell($key, $o, $lines, $line));
            }

            if ($combos) {
                $qty = [];
                foreach ($this->detailed ? ($line ? [$line] : []) : $lines as $l) {
                    $k = $this->key($l);
                    $qty[$k] = ($qty[$k] ?? 0) + $l['qty'];
                }

                foreach ($combos as $k => $_) {
                    if (isset($qty[$k])) {
                        $v = $qty[$k];
                        $totals[$k] += $v;
                    } else {
                        // mode semua varian (ringkas): kombinasi yang tidak dipesan tampil 0
                        $v = (! $this->detailed && $this->allVariants) ? 0 : null;
                    }
                    $this->put($sheet, $c++, $r, $v);
                }
            }

            $r++;
        }

        $dataEnd = $r - 1;

        // Baris TOTAL untuk kolom kombinasi varian
        if ($combos) {
            $this->put($sheet, 1, $r, 'TOTAL');
            $c = count($this->columns) + 1;
            foreach ($combos as $k => $_) {
                $this->put($sheet, $c++, $r, $totals[$k]);
            }
        }
        $end = $combos ? $r : $dataEnd;

        $this->style($sheet, count($headings), $dataEnd, $end, (bool) $combos);

        return $spreadsheet;
    }

    private function style(Worksheet $sheet, int $ncols, int $dataEnd, int $end, bool $hasTotal): void
    {
        $last = Coordinate::stringFromColumnIndex($ncols);

        $sheet->getStyle("A1:{$last}1")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '935B33']],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        ]);
        $sheet->freezePane('A2');

        if ($end >= 2) {
            $sheet->getStyle("A2:{$last}{$end}")->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
        }
        if ($dataEnd >= 2) {
            $sheet->setAutoFilter("A1:{$last}{$dataEnd}");
        }
        if ($hasTotal) {
            $sheet->getStyle("A{$end}:{$last}{$end}")->applyFromArray([
                'font'    => ['bold' => true],
                'borders' => ['top' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
        }

        foreach ($this->columns as $i => $key) {
            $letter = Coordinate::stringFromColumnIndex($i + 1);
            $range  = "{$letter}2:{$letter}{$end}";

            if (in_array($key, self::MONEY, true)) {
                $sheet->getStyle($range)->getNumberFormat()->setFormatCode('#,##0');
            }

            if (isset(self::WIDE[$key])) {
                $sheet->getColumnDimension($letter)->setAutoSize(false)->setWidth(self::WIDE[$key]);
                $sheet->getStyle($range)->getAlignment()->setWrapText(true);
            } else {
                $sheet->getColumnDimension($letter)->setAutoSize(true);
            }
        }

        // Kolom kombinasi varian: lebar tetap
        for ($i = count($this->columns) + 1; $i <= $ncols; $i++) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setAutoSize(false)->setWidth(18);
        }
    }

    /** Tulis sel: teks selalu sebagai string (tidak dibaca sebagai rumus), angka sebagai angka */
    private function put(Worksheet $sheet, int $col, int $row, $value): void
    {
        if ($value === null || $value === '') {
            return;
        }

        $cell = $sheet->getCell(Coordinate::stringFromColumnIndex($col) . $row);

        if (is_int($value) || is_float($value)) {
            $cell->setValue($value);
        } elseif (is_array($value)) { // ['text' => ..., 'url' => ...]
            $cell->setValueExplicit($value['text'], DataType::TYPE_STRING);
            $cell->getHyperlink()->setUrl($value['url']);
            $font = $cell->getStyle()->getFont();
            $font->setUnderline(true);
            $font->getColor()->setRGB('0563C1');
        } else {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
        }
    }

    /* =====================================================================
     |  Nilai tiap kolom
     ===================================================================== */

    private function cell(string $key, $o, array $lines, ?array $line)
    {
        $isFull = $o->payment_method === 'full';
        $set    = $this->detailed ? ($line ? [$line] : []) : $lines;
        $each   = fn (callable $f) => implode("\n", array_map($f, $set));
        $wib    = fn ($d) => $d ? Carbon::parse($d)->timezone('Asia/Jakarta')->format('d-m-Y H:i') : '';

        return match ($key) {
            'order_number'     => $o->order_number,
            'created_at'       => $wib($o->created_at),
            'seller_name'      => $o->seller_name ?? 'Pembelian Website',
            'seller_id'        => (string) $o->seller_id,
            'customer_name'    => trim($o->first_name . ' ' . $o->last_name),
            'email'            => (string) $o->email,
            'whatsapp_number'  => (string) $o->whatsapp_number,
            'shipping_address' => $this->address($o),

            'items'              => $each(fn ($l) => $this->itemText($l)),
            'product_names'      => $each(fn ($l) => $l['name']),
            'product_quantities' => $this->detailed ? ($line['qty'] ?? '') : $each(fn ($l) => (string) $l['qty']),

            'subtotal'        => (int) $o->subtotal,
            'coupon_discount' => (int) $o->discount,
            'total'           => (int) $o->total,

            'order_status'    => self::STATUS[$o->status] ?? ucfirst((string) $o->status),
            'payment_status'  => self::PAYMENT[$o->payment_status ?? 'pending'] ?? ucfirst((string) $o->payment_status),
            'payment_method'  => $isFull ? 'Bayar Penuh' : 'Down Payment',
            'payment_proof'   => $this->proof($o),

            'dp_amount'         => $isFull ? 0 : (int) $o->amount_due,
            'dp_status'         => $isFull ? 'Bayar Penuh' : ($o->remaining_paid_at ? 'Lunas Semua' : ($o->dp_paid_at ? 'DP Lunas' : 'DP Pending')),
            'remaining_amount'  => $isFull ? 0 : max(0, (int) $o->total - (int) $o->amount_due),
            'dp_paid_at'        => $wib($o->dp_paid_at),
            'remaining_paid_at' => $wib($o->remaining_paid_at),
            'payment_method_type' => $isFull
                ? 'Pembayaran Penuh'
                : 'DP ' . ($o->dp_percent ?: ((int) $o->total > 0 ? round($o->amount_due / $o->total * 100) : 0)) . '%',

            'admin_handle' => (string) ($this->handles[$o->admin_handle_id] ?? ''),
            'notes'        => (string) $o->notes,

            'product_categories'     => $each(fn ($l) => $l['pid'] ? ($this->categories[$l['pid']] ?? '-') : '-'),
            'product_variants_model' => $each(fn ($l) => $l['model'] !== '' ? $l['model'] : '-'),
            'product_variants_size'  => $each(fn ($l) => $l['size'] !== '' ? $l['size'] : '-'),
            'product_variants_color' => $each(fn ($l) => $l['color'] !== '' ? $l['color'] : '-'),

            default => '',
        };
    }

    private function proof($o): string|array
    {
        $p = $o->paymentConfirmations->first();

        return $p
            ? ['text' => 'Ada', 'url' => asset('storage/' . ltrim($p->proof_path, '/'))]
            : 'Belum';
    }

    private function address($o): string
    {
        $diff = (bool) $o->ship_different; // alamat pengiriman berbeda dari alamat pemesan

        $addr = $diff ? $o->ship_address : $o->address;
        $city = $diff ? $o->ship_city : $o->city;
        $prov = $diff ? $o->ship_province : $o->province;
        $pos  = $diff ? $o->ship_postal_code : $o->postal_code;

        $head  = $diff ? trim(($o->ship_recipient ?: '') . ($o->ship_phone ? ' (' . $o->ship_phone . ')' : '')) : '';
        $line2 = trim(implode(', ', array_filter([$city, $prov])) . ' ' . $pos);

        return implode("\n", array_filter([$head, $addr, $line2]));
    }

    private function itemText(array $l): string
    {
        $attrs = array_filter([
            $l['model'] !== '' ? 'Model: ' . $l['model'] : null,
            $l['color'] !== '' ? 'Warna: ' . $l['color'] : null,
            $l['size'] !== '' ? 'Ukuran: ' . $l['size'] : null,
        ]);

        return $l['name'] . ($attrs ? ' (' . implode(' | ', $attrs) . ')' : '') . ' x' . $l['qty'];
    }

    /* =====================================================================
     |  Baris produk (lines) per pesanan
     ===================================================================== */

    /** @return array<int, array{pid:?int,name:string,model:string,color:string,size:string,qty:int}> */
    private function lines($order): array
    {
        $items = $order->items;

        if ($this->productIds !== null) {
            $items = $items->filter(fn ($i) => in_array((int) $i->product_id, $this->productIds, true));
        }

        $lines = $items->map(fn ($i) => [
            'pid'   => $i->product_id ? (int) $i->product_id : null,
            'name'  => (string) $i->product_name,
            'model' => (string) ($i->model ?? ''),
            'color' => (string) ($i->color ?? ''),
            'size'  => (string) ($i->size ?? ''),
            'qty'   => (int) $i->quantity,
        ])->values()->all();

        if ($this->allVariants) {
            $lines = $this->expandVariants($lines);
        }

        return $this->sortLines($lines);
    }

    /** Mode "Semua Varian": tampilkan semua kombinasi tiap produk, yang tidak dipesan berjumlah 0 */
    private function expandVariants(array $lines): array
    {
        $out = [];

        foreach (collect($lines)->groupBy(fn ($l) => $l['pid'] ?? 'x') as $group) {
            $first = $group->first();
            $p     = $first['pid'] ? $this->products->get($first['pid']) : null;

            if (! $p) { // produk sudah dihapus: tampilkan apa adanya
                foreach ($group as $l) {
                    $out[] = $l;
                }
                continue;
            }

            $byKey = [];
            foreach ($group as $l) {
                $k = $this->key($l);
                if (isset($byKey[$k])) {
                    $byKey[$k]['qty'] += $l['qty'];
                } else {
                    $byKey[$k] = $l;
                }
            }

            $names  = fn ($col) => $col->isEmpty() ? [''] : $col->map(fn ($v) => $this->vname($v))->values()->all();
            $models = $names($p->models);
            $colors = $names($p->colors);
            $sizes  = $names($p->sizes->where('is_available', true));

            $seen = [];
            foreach ($models as $m) {
                foreach ($colors as $c) {
                    foreach ($sizes as $s) {
                        $l = ['pid' => $p->id, 'name' => $p->name, 'model' => $m, 'color' => $c, 'size' => $s, 'qty' => 0];
                        $k = $this->key($l);

                        $l['qty'] = $byKey[$k]['qty'] ?? 0;
                        $seen[$k] = true;
                        $out[]    = $l;
                    }
                }
            }

            // Dipesan tetapi tidak cocok dengan kombinasi mana pun (varian sudah berubah/dihapus)
            foreach ($byKey as $k => $l) {
                if (! isset($seen[$k])) {
                    $out[] = $l;
                }
            }
        }

        return $out;
    }

    /** Kelompokkan per produk (urutan kemunculan), urutkan varian sesuai metode pengurutan */
    private function sortLines(array $lines): array
    {
        $out = [];

        foreach (collect($lines)->groupBy(fn ($l) => $l['pid'] ?? 'x') as $group) {
            $g = $group->all();
            usort($g, fn ($a, $b) => $this->sortKey($a) <=> $this->sortKey($b));

            foreach ($g as $l) {
                $out[] = $l;
            }
        }

        return $out;
    }

    /* =====================================================================
     |  Helper varian
     ===================================================================== */

    private function key(array $l): string
    {
        return ($l['pid'] ?? '') . '|' . $l['model'] . '|' . $l['color'] . '|' . $l['size'];
    }

    private function label(array $l): string
    {
        return implode(' - ', array_filter([$l['model'], $l['color'], $l['size']], fn ($v) => $v !== '')) ?: 'Tanpa varian';
    }

    /** Urutan sesuai urutan varian di data produk (mis. M, L, XL), bukan abjad */
    private function sortKey(array $l): array
    {
        $p = $l['pid'] ? $this->products->get($l['pid']) : null;

        $mi = $this->pos($p?->models, $l['model']);
        $ci = $this->pos($p?->colors, $l['color']);
        $si = $this->pos($p?->sizes, $l['size']);

        return $this->sizeFirst ? [$si, $mi, $ci] : [$mi, $ci, $si];
    }

    private function pos($list, string $name): int
    {
        if ($name === '' || ! $list) {
            return 0;
        }

        foreach ($list->values() as $i => $v) {
            if ($this->vname($v) === $name) {
                return $i + 1;
            }
        }

        return 9999; // varian tidak dikenal -> paling akhir
    }

    /** product_colors/product_models memakai `name`, product_sizes memakai `size` */
    private function vname($v): string
    {
        return (string) ($v->name ?? $v->size ?? '');
    }

    private function loadCategories(array $ids): array
    {
        if (! $ids) {
            return [];
        }

        $map = [];

        $pivot = DB::table('product_categories')
            ->join('categories', 'categories.id', '=', 'product_categories.category_id')
            ->whereIn('product_categories.product_id', $ids)
            ->get(['product_categories.product_id', 'categories.name']);
        foreach ($pivot as $r) {
            $map[$r->product_id][] = $r->name;
        }

        // Kolom lama products.category_id (nullable)
        $legacy = DB::table('products')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->whereIn('products.id', $ids)
            ->get(['products.id', 'categories.name']);
        foreach ($legacy as $r) {
            $map[$r->id][] = $r->name;
        }

        return array_map(fn ($names) => implode(', ', array_unique($names)), $map);
    }
}