<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use ZipArchive;

class LaporanController extends Controller
{
    /**
     * Tanggal yang dipakai laporan:
     * - Sudah paid  -> tanggal dibayar (pelunasan -> DP -> updated_at)
     * - Belum paid  -> tanggal order dibuat
     * Kalau pencatatan tanggal bayar Anda beda, cukup ubah di sini.
     */
    private const TGL = "CASE WHEN co.payment_status = 'paid'
                              THEN COALESCE(co.remaining_paid_at, co.dp_paid_at, co.updated_at)
                              ELSE co.created_at END";

    public function index(Request $request)
    {
        $now = now();
        $f   = $this->resolveFilters($request);

        [$periode, $tahun, $bulan] = [$f['periode'], $f['tahun'], $f['bulan']];
        [$brandId, $sellerId]      = [$f['brandId'], $f['sellerId']];
        [$from, $to]               = [$f['from'], $f['to']];

        // ---------- 3 kartu statistik ----------
        // Kartu 1 = semua order hari ini (termasuk pending)
        $closingHariIni = $this->orderQuery($brandId, $sellerId, $now->copy()->startOfDay(), $now->copy()->endOfDay(), false)
            ->count();

        // Kartu 2 = periode yang dipilih di dropdown (default: bulan sekarang), hanya yang paid
        $totalBulanIni = $this->orderQuery($brandId, $sellerId, $from, $to, true)
            ->sum('co.total');

        // Kartu 3 = satu periode sebelum yang dipilih (bulan lalu, atau tahun lalu kalau Per Tahun)
        if ($periode === 'tahun') {
            $fromLalu = $from->copy()->subYear()->startOfYear();
            $toLalu   = $fromLalu->copy()->endOfYear();
            $labelBulanIni  = $from->format('Y');
            $labelBulanLalu = $fromLalu->format('Y');
        } else {
            $fromLalu = $from->copy()->subMonthNoOverflow()->startOfMonth();
            $toLalu   = $fromLalu->copy()->endOfMonth();
            $labelBulanIni  = $from->format('F Y');
            $labelBulanLalu = $fromLalu->format('F Y');
        }

        $totalBulanLalu = $this->orderQuery($brandId, $sellerId, $fromLalu, $toLalu, true)
            ->sum('co.total');

        // ---------- Tabel Detail Closing (SEMUA order, termasuk pending) ----------
        $closings = $this->selectRows($this->orderQuery($brandId, $sellerId, $from, $to, false))
            ->paginate(15)
            ->withQueryString();

        // ---------- Data untuk dropdown ----------
        $brands = DB::table('brands')
            ->where('is_active', 1)
            ->orderBy('name')
            ->get(['id', 'name']);

        $daftarTahun = range($now->year, $now->year - 3);
        if (!in_array($tahun, $daftarTahun)) {
            $daftarTahun[] = $tahun;
            rsort($daftarTahun);
        }

        $daftarBulan = [];
        for ($m = 1; $m <= 12; $m++) {
            $daftarBulan[$m] = Carbon::create(2000, $m, 1)->format('F');
        }

        return view('admin.laporan', [
            'closings'       => $closings,
            'brands'         => $brands,
            'daftarTahun'    => $daftarTahun,
            'daftarBulan'    => $daftarBulan,
            'periode'        => $periode,
            'tahun'          => $tahun,
            'bulan'          => $bulan,
            'brandId'        => $brandId,
            'sellerId'       => $sellerId,
            'closingHariIni' => $closingHariIni,
            'totalBulanIni'  => $totalBulanIni,
            'totalBulanLalu' => $totalBulanLalu,
            'labelBulanIni'  => $labelBulanIni,
            'labelBulanLalu' => $labelBulanLalu,
        ]);
    }

    /**
     * Export Excel: semua baris sesuai filter yang sedang aktif (tanpa paginasi).
     * Nama file: mitra-sales-2026-10-02-08-52-21.xlsx
     */
    public function export(Request $request)
    {
        $f = $this->resolveFilters($request);

        $rows = $this->selectRows($this->orderQuery($f['brandId'], $f['sellerId'], $f['from'], $f['to'], false))
            ->get();

        $header = [
            'Tanggal', 'Brand', 'ID Seller', 'Produk', 'Jumlah Produk',
            'Total (Rp)', 'Status Pesanan', 'Status Pembayaran',
        ];

        $data = [];
        foreach ($rows as $r) {
            $data[] = [
                Carbon::parse($r->tanggal)->format('d M Y'),
                $r->brand_name,
                $r->seller_id ?: '-',
                $r->produk ?: '-',
                (int) $r->total_qty,
                (int) $r->total,
                (string) $r->status,
                (string) $r->payment_status,
            ];
        }

        $path     = $this->buildXlsx($header, $data);
        $filename = 'mitra-sales-' . now()->format('Y-m-d-H-i-s') . '.xlsx';

        return response()->download($path, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Baca & rapikan filter dari request (dipakai halaman dan export).
     */
    private function resolveFilters(Request $request): array
    {
        $now = now();

        $periode  = $request->input('periode') === 'tahun' ? 'tahun' : 'bulan';
        $tahun    = (int) $request->input('tahun', $now->year);
        $bulan    = (int) $request->input('bulan', $now->month);
        $brandId  = $request->input('brand_id');
        $sellerId = trim((string) $request->input('seller_id'));

        if ($tahun < 2000 || $tahun > 2100) {
            $tahun = $now->year;
        }
        if ($bulan < 1 || $bulan > 12) {
            $bulan = $now->month;
        }

        if ($periode === 'tahun') {
            $from = Carbon::create($tahun, 1, 1, 0, 0, 0)->startOfYear();
            $to   = $from->copy()->endOfYear();
        } else {
            $from = Carbon::create($tahun, $bulan, 1, 0, 0, 0)->startOfMonth();
            $to   = $from->copy()->endOfMonth();
        }

        return compact('periode', 'tahun', 'bulan', 'brandId', 'sellerId', 'from', 'to');
    }

    /**
     * Kolom & urutan yang dipakai tabel dan export.
     */
    private function selectRows($query)
    {
        return $query
            ->select(
                'co.id',
                'co.order_number',
                'co.seller_id',
                'co.total',
                'co.status',
                'co.payment_status',
                DB::raw(self::TGL . ' as tanggal'),
                DB::raw("COALESCE(b.name, 'ZAKIRA') as brand_name"),
                'i.produk',
                'i.total_qty'
            )
            ->orderByDesc('tanggal')
            ->orderByDesc('co.id');
    }

    /**
     * Query dasar order dalam rentang tanggal tertentu, lengkap dengan
     * filter brand & ID seller.
     *
     * @param bool $onlyPaid true  = hanya order yang sudah dibayar (untuk kartu total)
     *                       false = semua order termasuk pending (untuk tabel, export, kartu hari ini)
     */
    private function orderQuery($brandId, $sellerId, Carbon $from, Carbon $to, bool $onlyPaid)
    {
        // Ringkasan item per order: jumlah produk & daftar nama produk
        $items = DB::table('customer_order_items')
            ->select(
                'customer_order_id',
                DB::raw('SUM(quantity) as total_qty'),
                DB::raw("GROUP_CONCAT(DISTINCT product_name ORDER BY product_name SEPARATOR ', ') as produk")
            )
            ->groupBy('customer_order_id');

        $query = DB::table('customer_orders as co')
            ->leftJoin('sellers as s', 's.seller_id', '=', 'co.seller_id')
            ->leftJoin('brands as b', 'b.id', '=', 's.brand_id')
            ->leftJoinSub($items, 'i', 'i.customer_order_id', '=', 'co.id')
            ->whereRaw(self::TGL . ' BETWEEN ? AND ?', [$from, $to]);

        if ($onlyPaid) {
            $query->where('co.payment_status', 'paid');
        }

        // Filter brand. Order tanpa seller dianggap brand ZAKIRA.
        if ($brandId) {
            $brandName = DB::table('brands')->where('id', $brandId)->value('name');

            $query->where(function ($q) use ($brandId, $brandName) {
                $q->where('s.brand_id', $brandId);

                if (strtoupper((string) $brandName) === 'ZAKIRA') {
                    $q->orWhereNull('s.id');
                }
            });
        }

        // Filter ID seller (pencarian sebagian)
        if ($sellerId !== null && $sellerId !== '') {
            $query->where('co.seller_id', 'like', '%' . $sellerId . '%');
        }

        return $query;
    }

    // =====================================================================
    //  Pembuat file .xlsx sederhana (tanpa package, memakai ZipArchive)
    // =====================================================================

    /**
     * @param array $header  Baris judul kolom
     * @param array $data    Baris data (angka ditulis sebagai angka, selain itu teks)
     * @return string        Path file xlsx sementara
     */
    private function buildXlsx(array $header, array $data): string
    {
        $ns = 'http://schemas.openxmlformats.org/spreadsheetml/2006/main';

        // ----- sheet1.xml -----
        $widths = [14, 14, 16, 36, 16, 16, 18, 20];
        $cols   = '';
        foreach ($widths as $i => $w) {
            $n     = $i + 1;
            $cols .= '<col min="' . $n . '" max="' . $n . '" width="' . $w . '" customWidth="1"/>';
        }

        $rowsXml = '';
        $rowNum  = 1;

        // header (style 1 = tebal)
        $cells = '';
        foreach ($header as $i => $text) {
            $cells .= $this->cell($i, $rowNum, $text, 1);
        }
        $rowsXml .= '<row r="' . $rowNum . '">' . $cells . '</row>';

        // data
        foreach ($data as $row) {
            $rowNum++;
            $cells = '';
            foreach ($row as $i => $value) {
                $cells .= $this->cell($i, $rowNum, $value, 0);
            }
            $rowsXml .= '<row r="' . $rowNum . '">' . $cells . '</row>';
        }

        $sheet = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="' . $ns . '">'
            . '<cols>' . $cols . '</cols>'
            . '<sheetData>' . $rowsXml . '</sheetData>'
            . '</worksheet>';

        // ----- file pendukung -----
        $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . '</Types>';

        $rootRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>';

        $workbook = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="' . $ns . '" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets><sheet name="Laporan" sheetId="1" r:id="rId1"/></sheets>'
            . '</workbook>';

        $workbookRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            . '</Relationships>';

        $styles = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<styleSheet xmlns="' . $ns . '">'
            . '<fonts count="2">'
            . '<font><sz val="11"/><name val="Calibri"/></font>'
            . '<font><b/><sz val="11"/><name val="Calibri"/></font>'
            . '</fonts>'
            . '<fills count="2"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill></fills>'
            . '<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="2">'
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            . '<xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1"/>'
            . '</cellXfs>'
            . '<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
            . '</styleSheet>';

        // ----- zip jadi .xlsx -----
        $tmp = tempnam(sys_get_temp_dir(), 'xlsx');
        $zip = new ZipArchive();
        $zip->open($tmp, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $zip->addFromString('[Content_Types].xml', $contentTypes);
        $zip->addFromString('_rels/.rels', $rootRels);
        $zip->addFromString('xl/workbook.xml', $workbook);
        $zip->addFromString('xl/_rels/workbook.xml.rels', $workbookRels);
        $zip->addFromString('xl/styles.xml', $styles);
        $zip->addFromString('xl/worksheets/sheet1.xml', $sheet);
        $zip->close();

        return $tmp;
    }

    /**
     * Satu sel: angka -> numerik, selain itu -> teks.
     */
    private function cell(int $colIndex, int $rowNum, $value, int $style): string
    {
        $ref = chr(ord('A') + $colIndex) . $rowNum; // kolom A-H

        if (is_int($value) || is_float($value)) {
            return '<c r="' . $ref . '" s="' . $style . '"><v>' . $value . '</v></c>';
        }

        $text = htmlspecialchars((string) $value, ENT_XML1 | ENT_QUOTES, 'UTF-8');

        return '<c r="' . $ref . '" s="' . $style . '" t="inlineStr"><is><t xml:space="preserve">' . $text . '</t></is></c>';
    }
}