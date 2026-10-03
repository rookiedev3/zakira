<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerOrder;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class InvoiceController extends Controller
{
    private const BRAND = 'Zakira';

    /** Dipanggil dari form modal "Buat Faktur" (tombol PDF / Excel). */
    public function store(Request $request, CustomerOrder $order)
    {
        $data = $request->validate([
            'invoice_number' => ['required', 'string', 'max:50', 'unique:invoices,invoice_number'],
            'invoice_date'   => ['required', 'date'],
            'invoice_notes'  => ['nullable', 'string', 'max:1000'],
            'format'         => ['required', 'in:pdf,excel'],
        ]);

        if ($order->invoice) {
            return back()->with('success', 'Pesanan ini sudah memiliki faktur. Hapus dulu faktur lama.');
        }

        // Input dari admin = WIB; simpan dalam timezone aplikasi.
        $date = Carbon::parse($data['invoice_date'], 'Asia/Jakarta')->setTimezone(config('app.timezone'));

        $invoice = Invoice::create([
            'customer_order_id' => $order->id,
            'invoice_number'    => $data['invoice_number'],
            'format'            => $data['format'],
            'invoice_date'      => $date,
            'notes'             => $data['invoice_notes'] ?? null,
        ]);

        if ($data['format'] === 'excel') {
            $invoice->update(['file_path' => $this->buildExcel($invoice->load('order.items'))]);
        }

        return back()->with('success', 'Faktur ' . strtoupper($data['format']) . ' berhasil dibuat.');
    }

    public function download(Invoice $invoice)
    {
        if ($invoice->format === 'excel' && $invoice->file_path) {
            return Storage::disk('public')->download($invoice->file_path);
        }

        $invoice->load('order.items');

        // download langsung (tidak dibuka di tab baru)
        return Pdf::loadView('admin.invoices.pdf', ['invoice' => $invoice])
            ->setPaper('a4')
            ->download('faktur-' . $invoice->invoice_number . '.pdf');
    }

    public function destroy(Invoice $invoice)
    {
        if ($invoice->file_path) {
            Storage::disk('public')->delete($invoice->file_path);
        }
        $invoice->delete();

        return back()->with('success', 'Faktur berhasil dihapus.');
    }

    /** Buat file Excel dengan susunan yang sama seperti contoh, simpan di disk public. */
    private function buildExcel(Invoice $invoice): string
    {
        $o  = $invoice->order;
        $rp = fn ($n) => 'Rp ' . number_format((int) $n, 0, ',', '.');

        $discount = (int) ($o->discount_amount ?? 0);
        $subtotal = (int) ($o->subtotal ?? $o->items->sum(fn ($i) => (int) $i->price * (int) $i->quantity));
        $totalQty = (int) $o->items->sum(fn ($i) => (int) $i->quantity);

        $book = new Spreadsheet();
        $ws   = $book->getActiveSheet()->setTitle('Faktur');

        foreach (['A' => 8, 'B' => 40, 'C' => 10, 'D' => 20, 'E' => 20] as $col => $w) {
            $ws->getColumnDimension($col)->setWidth($w);
        }

        // Judul
        foreach ([1 => [self::BRAND, 16], 2 => ['FAKTUR', 14], 3 => [$invoice->invoice_number, 11]] as $row => [$text, $size]) {
            $ws->mergeCells("A{$row}:E{$row}");
            $ws->setCellValue("A{$row}", $text);
            $ws->getStyle("A{$row}")->getFont()->setBold(true)->setSize($size);
            $ws->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // Info pesanan
        $info = [
            'Order ID'        => $o->order_number,
            'Nama Seller'     => $o->seller_name ?? 'Pembelian Website',
            'ID Seller'       => $o->seller_id,
            'Nama'            => trim($o->first_name . ' ' . $o->last_name),
            'Tanggal Pesanan' => $o->created_at->timezone('Asia/Jakarta')->format('d M Y'),
            'Alamat'          => $o->address . ', ' . $o->city . ', ' . $o->province . ' ' . $o->postal_code,
        ];
        $r = 5;
        foreach ($info as $label => $value) {
            $ws->setCellValue("A{$r}", $label);
            $ws->setCellValue("B{$r}", ': ' . $value);
            $r++;
        }

        // Header tabel (baris 12)
        $r = 12;
        foreach (['No', 'Produk', 'Qty', 'Harga Satuan', 'Total'] as $i => $head) {
            $ws->setCellValue(chr(65 + $i) . $r, $head);
        }
        $this->headerStyle($ws, "A{$r}:E{$r}");

        // Item
        $r++;
        foreach ($o->items as $n => $item) {
            $name  = $item->product_name ?? $item->product->name ?? '-';
            $attrs = array_filter([
                ! empty($item->model) ? 'Model: '  . $item->model : null,
                ! empty($item->color) ? 'Warna: '  . $item->color : null,
                ! empty($item->size)  ? 'Ukuran: ' . $item->size  : null,
            ]);
            $ws->setCellValue("A{$r}", $n + 1);
            $ws->setCellValue("B{$r}", $name . ($attrs ? "\n" . implode(' | ', $attrs) : ''));
            $ws->getStyle("B{$r}")->getAlignment()->setWrapText(true);
            $ws->setCellValue("C{$r}", (int) $item->quantity);
            $ws->setCellValue("D{$r}", $rp($item->price));
            $ws->setCellValue("E{$r}", $rp((int) $item->price * (int) $item->quantity));
            $ws->getStyle("A{$r}:E{$r}")->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
            $r++;
        }

        // Total kuantitas
        $ws->setCellValue("B{$r}", 'Total Kuantitas:');
        $ws->setCellValue("C{$r}", $totalQty);
        $this->headerStyle($ws, "B{$r}:C{$r}");
        $r += 2;

        // Subtotal / diskon / total
        $ws->setCellValue("D{$r}", 'Subtotal:');
        $ws->setCellValue("E{$r}", $rp($subtotal));
        if ($discount > 0) {
            $r++;
            $ws->setCellValue("D{$r}", 'Diskon Kupon:');
            $ws->setCellValue("E{$r}", '-' . $rp($discount));
        }
        $r++;
        $ws->setCellValue("B{$r}", 'Terima kasih atas kepercayaan Anda berbelanja di ' . self::BRAND);
        $ws->setCellValue("D{$r}", 'Total:');
        $ws->setCellValue("E{$r}", $rp($o->total));
        $ws->getStyle("D{$r}:E{$r}")->getFont()->setBold(true)->setSize(12);

        if (filled($invoice->notes)) {
            $r += 2;
            $ws->setCellValue("A{$r}", 'Catatan');
            $ws->setCellValue("B{$r}", ': ' . $invoice->notes);
            $ws->getStyle("B{$r}")->getAlignment()->setWrapText(true);
        }

        $path = 'invoices/excel/faktur-' . $o->order_number . '-' . now('Asia/Jakarta')->format('YmdHis') . '.xlsx';
        $tmp  = tempnam(sys_get_temp_dir(), 'fkt');
        (new Xlsx($book))->save($tmp);
        Storage::disk('public')->put($path, file_get_contents($tmp));
        @unlink($tmp);

        return $path;
    }

    private function headerStyle($ws, string $range): void
    {
        $s = $ws->getStyle($range);
        $s->getFont()->setBold(true);
        $s->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF8F9FA');
    }
}