{{-- resources/views/admin/invoices/pdf.blade.php --}}
@php
    $o   = $invoice->order;
    $rp  = fn ($n) => 'Rp ' . number_format((int) $n, 0, ',', '.');
    $discount = (int) ($o->discount_amount ?? 0);
    $sumItems = $o->items->sum(fn ($i) => (int) $i->price * (int) $i->quantity);
    $subtotal = (int) ($o->subtotal ?? $sumItems);
    $totalQty = $o->items->sum(fn ($i) => (int) $i->quantity);
    $address  = $o->address . ', ' . $o->city . ', ' . $o->province . ' ' . $o->postal_code;
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>{{ $invoice->invoice_number }}</title>
<style>
    @page { margin: 36px 40px; }
    body { font-family: Helvetica, Arial, sans-serif; font-size: 11px; color: #333; }
    .center { text-align: center; }
    .brand { font-size: 20px; font-weight: bold; color: #b5876a; margin: 0; }
    .title { font-size: 15px; font-weight: bold; margin: 10px 0 6px; }
    .number { font-size: 10px; }
    .rule { border-top: 1px solid #b5876a; margin: 14px 0 22px; }
    table { width: 100%; border-collapse: collapse; }
    .info td { padding: 5px 0; vertical-align: top; }
    .info td.l { width: 20%; font-weight: bold; }
    .items th { background: #f8f9fa; color: #b5876a; font-size: 10px; text-align: left; padding: 10px 8px; border-bottom: 1px solid #ddd; }
    .items td { padding: 12px 8px; border-bottom: 1px solid #ddd; vertical-align: top; }
    .items .r { text-align: right; } .items .c { text-align: center; }
    .attr { font-size: 9px; color: #666; margin-top: 3px; }
    .qty-row td { background: #f8f9fa; font-weight: bold; padding: 10px 8px; border-bottom: 1px solid #eee; }
    .sum { width: 42%; margin-left: 58%; margin-top: 22px; }
    .sum td { padding: 8px 0; border-bottom: 1px solid #eee; }
    .sum .total td { font-weight: bold; color: #b5876a; font-size: 12px; border-top: 1px solid #b5876a; border-bottom: 1px solid #b5876a; }
    .notes { margin-top: 22px; font-size: 10px; }
    .foot { margin-top: 40px; border-top: 1px solid #eee; padding-top: 14px; text-align: center; font-size: 9px; color: #666; }
</style>
</head>
<body>
    <div class="center">
        <p class="brand">Zakira</p>
        <div class="title">FAKTUR</div>
        <div class="number">{{ $invoice->invoice_number }}</div>
    </div>
    <div class="rule"></div>

    <table class="info">
        <tr><td class="l">Order ID</td><td>: {{ $o->order_number }}</td></tr>
        <tr><td class="l">Nama</td><td>: {{ trim($o->first_name . ' ' . $o->last_name) }}</td></tr>
        <tr><td class="l">Seller</td><td>: {{ $o->seller_name ?? 'Pembelian Website' }}</td></tr>
        <tr><td class="l">ID Seller</td><td>: {{ $o->seller_id }}</td></tr>
        <tr><td class="l">Tanggal Pesanan</td><td>: {{ $o->created_at->timezone('Asia/Jakarta')->format('d M Y') }}</td></tr>
        <tr><td class="l">Alamat</td><td>: {{ $address }}</td></tr>
    </table>

    <table class="items" style="margin-top:22px;">
        <thead>
            <tr>
                <th style="width:6%">No</th>
                <th>Produk</th>
                <th class="c" style="width:8%">Qty</th>
                <th class="r" style="width:20%">Harga Satuan</th>
                <th class="r" style="width:20%">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($o->items as $i => $item)
                @php
                    $name  = $item->product_name ?? $item->product->name ?? '-';
                    $attrs = array_filter([
                        ! empty($item->model) ? 'Model: '  . $item->model : null,
                        ! empty($item->color) ? 'Warna: '  . $item->color : null,
                        ! empty($item->size)  ? 'Ukuran: ' . $item->size  : null,
                    ]);
                @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>
                        <strong>{{ $name }}</strong>
                        @if ($attrs)<div class="attr">{{ implode(' | ', $attrs) }}</div>@endif
                    </td>
                    <td class="c">{{ (int) $item->quantity }}</td>
                    <td class="r">{{ $rp($item->price) }}</td>
                    <td class="r">{{ $rp((int) $item->price * (int) $item->quantity) }}</td>
                </tr>
            @endforeach
            <tr class="qty-row">
                <td colspan="2" style="text-align:right">Total Kuantitas:</td>
                <td class="c">{{ $totalQty }}</td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>

    <table class="sum">
        <tr><td>Subtotal: {{ $rp($subtotal) }}</td></tr>
        @if ($discount > 0)
            <tr><td>Diskon Kupon: -{{ $rp($discount) }}</td></tr>
        @endif
        <tr class="total"><td>Total: {{ $rp($o->total) }}</td></tr>
    </table>

    @if (filled($invoice->notes))
        <div class="notes"><strong>Catatan:</strong> {{ $invoice->notes }}</div>
    @endif

    <div class="foot">Terima kasih atas kepercayaan Anda berbelanja di Zakira</div>
</body>
</html>