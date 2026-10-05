<?php

namespace App\Libraries;

use App\Models\ReportModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class ReportExporter
{
    /** Periode yang boleh dipakai membentuk query dan nama file. */
    public const PERIOD_KEYWORDS = ['today', '7days', 'this_month', 'last_month'];

    /** Dipakai bila period dari input tidak dikenal. */
    public const DEFAULT_PERIOD = 'this_month';

    protected ReportModel $report;

    public function __construct(?ReportModel $report = null)
    {
        $this->report = $report ?? new ReportModel();
    }

    /**
     * Apakah `period` aman dipakai membentuk query dan nama file?
     *
     * Period datang dari dua sumber yang bisa dikendalikan penyerang:
     * query string pada ReportController dan callback data pada
     * ReportCommand Telegram. Karena itu allowlist ini harus ditegakkan
     * di sini, bukan di pemanggil.
     */
    public function isValidPeriod(string $period): bool
    {
        if (in_array($period, self::PERIOD_KEYWORDS, true)) {
            return true;
        }

        // Format YYYY-MM, dengan rentang bulan/tahun yang masuk akal.
        if (preg_match('/^(\d{4})-(\d{2})$/', $period, $m) !== 1) {
            return false;
        }

        $year  = (int) $m[1];
        $month = (int) $m[2];

        return $year >= 2000 && $year <= 2999 && $month >= 1 && $month <= 12;
    }

    /** Kembalikan period kalau aman, selain itu jatuhkan ke default. */
    public function normalizePeriod(string $period): string
    {
        return $this->isValidPeriod($period) ? $period : self::DEFAULT_PERIOD;
    }

    /**
     * Bangun path file temp untuk hasil export.
     *
     * Nama file dibuat dari nilai acak — `period` sengaja TIDAK ikut
     * ke dalam nama file, karena string yang tidak tervalidasi di dalam
     * path adalah awal dari path traversal.
     */
    public function tempFilePathFor(string $period, string $extension): string
    {
        $extension = preg_replace('/[^a-z0-9]/i', '', $extension) ?: 'dat';
        $directory = rtrim(sys_get_temp_dir(), '/\\') . DIRECTORY_SEPARATOR;

        return $directory . 'laporan_' . bin2hex(random_bytes(8)) . '.' . $extension;
    }

    /**
     * Ambil data laporan berdasarkan periode.
     * Periode: 'today', '7days', 'this_month', 'last_month', atau 'YYYY-MM'
     */
    public function getReportData(string $period): array
    {
        $period    = $this->normalizePeriod($period);
        $startDate = '';
        $endDate   = '';
        $title     = '';

        if ($period === 'today') {
            $startDate = date('Y-m-d 00:00:00');
            $endDate   = date('Y-m-d 23:59:59');
            $title     = 'Laporan Penjualan Hari Ini (' . date('d M Y') . ')';
        } elseif ($period === '7days') {
            $startDate = date('Y-m-d 00:00:00', strtotime('-6 days'));
            $endDate   = date('Y-m-d 23:59:59');
            $title     = 'Laporan Penjualan 7 Hari Terakhir';
        } elseif ($period === 'this_month') {
            $startDate = date('Y-m-01 00:00:00');
            $endDate   = date('Y-m-t 23:59:59');
            $title     = 'Laporan Penjualan Bulan Ini (' . date('F Y') . ')';
        } elseif ($period === 'last_month') {
            $startDate = date('Y-m-01 00:00:00', strtotime('first day of last month'));
            $endDate   = date('Y-m-t 23:59:59', strtotime('last day of last month'));
            $title     = 'Laporan Penjualan Bulan Lalu (' . date('F Y', strtotime('last month')) . ')';
        } elseif (preg_match('/^\d{4}-\d{2}$/', $period)) {
            [$year, $month] = explode('-', $period);
            $startDate = sprintf('%04d-%02d-01 00:00:00', $year, $month);
            $endDate   = date('Y-m-t 23:59:59', strtotime($startDate));
            $title     = "Laporan Penjualan Bulan {$month}/{$year}";
        } else {
            $startDate = date('Y-m-01 00:00:00');
            $endDate   = date('Y-m-t 23:59:59');
            $title     = 'Laporan Penjualan';
        }

        $db = \Config\Database::connect();

        // Mengambil pesanan berstatus diproses dan selesai (omzet)
        $orders = $db->table('orders o')
            ->select('o.invoice_number, o.product_name_snapshot, o.nominal_snapshot, o.price_snapshot, o.discount_amount, o.total_amount, o.status, o.created_at')
            ->whereIn('o.status', ['diproses', 'selesai'])
            ->where('o.created_at >=', $startDate)
            ->where('o.created_at <=', $endDate)
            ->orderBy('o.created_at', 'ASC')
            ->get()
            ->getResultArray();

        $totalRevenue  = 0;
        $totalOrders   = count($orders);
        $totalDiscount = 0;
        $productsMap   = [];

        foreach ($orders as $o) {
            $totalRevenue  += (float) $o['total_amount'];
            $totalDiscount += (float) $o['discount_amount'];

            $pName = $o['product_name_snapshot'] . ' (' . $o['nominal_snapshot'] . ')';
            if (! isset($productsMap[$pName])) {
                $productsMap[$pName] = ['name' => $pName, 'qty' => 0, 'total' => 0];
            }
            $productsMap[$pName]['qty']++;
            $productsMap[$pName]['total'] += (float) $o['total_amount'];
        }

        usort($productsMap, fn($a, $b) => $b['qty'] <=> $a['qty']);

        return [
            'title'         => $title,
            'period'        => $period,
            'startDate'     => $startDate,
            'endDate'       => $endDate,
            'totalRevenue'  => $totalRevenue,
            'totalOrders'   => $totalOrders,
            'totalDiscount' => $totalDiscount,
            'topProducts'   => $productsMap,
            'orders'        => $orders,
        ];
    }

    /** Export ke file CSV/XLSX (return path file temp). */
    public function exportExcelFile(string $period): string
    {
        $data     = $this->getReportData($period);
        $tempPath = $this->tempFilePathFor($period, 'xlsx');

        // Menghasilkan XML Spreadsheet 2003 yang didukung Microsoft Excel sebagai file .xlsx / .xml
        $xml = '<?xml version="1.0"?>' . "\n";
        $xml .= '<?mso-application progid="Excel.Sheet"?>' . "\n";
        $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"' . "\n";
        $xml .= ' xmlns:o="urn:schemas-microsoft-com:office:office"' . "\n";
        $xml .= ' xmlns:x="urn:schemas-microsoft-com:office:excel"' . "\n";
        $xml .= ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">' . "\n";
        $xml .= ' <Worksheet ss:Name="Laporan Penjualan">' . "\n";
        $xml .= '  <Table>' . "\n";

        // Header Ringkasan
        $xml .= '   <Row><Cell><Data ss:Type="String">' . htmlspecialchars($data['title']) . '</Data></Cell></Row>' . "\n";
        $xml .= '   <Row><Cell><Data ss:Type="String">Total Pesanan: ' . $data['totalOrders'] . '</Data></Cell></Row>' . "\n";
        $xml .= '   <Row><Cell><Data ss:Type="String">Total Omzet: Rp ' . number_format($data['totalRevenue'], 0, ',', '.') . '</Data></Cell></Row>' . "\n";
        $xml .= '   <Row><Cell><Data ss:Type="String">Total Diskon: Rp ' . number_format($data['totalDiscount'], 0, ',', '.') . '</Data></Cell></Row>' . "\n";
        $xml .= '   <Row></Row>' . "\n";

        // Table Header Rincian
        $xml .= '   <Row>' . "\n";
        $xml .= '    <Cell><Data ss:Type="String">No Invoice</Data></Cell>' . "\n";
        $xml .= '    <Cell><Data ss:Type="String">Produk</Data></Cell>' . "\n";
        $xml .= '    <Cell><Data ss:Type="String">Nominal</Data></Cell>' . "\n";
        $xml .= '    <Cell><Data ss:Type="String">Diskon</Data></Cell>' . "\n";
        $xml .= '    <Cell><Data ss:Type="String">Total Bayar</Data></Cell>' . "\n";
        $xml .= '    <Cell><Data ss:Type="String">Status</Data></Cell>' . "\n";
        $xml .= '    <Cell><Data ss:Type="String">Tanggal</Data></Cell>' . "\n";
        $xml .= '   </Row>' . "\n";

        foreach ($data['orders'] as $o) {
            $xml .= '   <Row>' . "\n";
            $xml .= '    <Cell><Data ss:Type="String">' . htmlspecialchars($o['invoice_number']) . '</Data></Cell>' . "\n";
            $xml .= '    <Cell><Data ss:Type="String">' . htmlspecialchars($o['product_name_snapshot']) . '</Data></Cell>' . "\n";
            $xml .= '    <Cell><Data ss:Type="String">' . htmlspecialchars($o['nominal_snapshot']) . '</Data></Cell>' . "\n";
            $xml .= '    <Cell><Data ss:Type="Number">' . $o['discount_amount'] . '</Data></Cell>' . "\n";
            $xml .= '    <Cell><Data ss:Type="Number">' . $o['total_amount'] . '</Data></Cell>' . "\n";
            $xml .= '    <Cell><Data ss:Type="String">' . htmlspecialchars($o['status']) . '</Data></Cell>' . "\n";
            $xml .= '    <Cell><Data ss:Type="String">' . htmlspecialchars($o['created_at']) . '</Data></Cell>' . "\n";
            $xml .= '   </Row>' . "\n";
        }

        $xml .= '  </Table>' . "\n";
        $xml .= ' </Worksheet>' . "\n";
        $xml .= '</Workbook>';

        file_put_contents($tempPath, $xml);

        return $tempPath;
    }

    /** Export ke PDF (return path file temp). */
    public function exportPdfFile(string $period): string
    {
        $data     = $this->getReportData($period);
        $tempPath = $this->tempFilePathFor($period, 'pdf');

        $html = '<html><head><style>
            body { font-family: sans-serif; font-size: 12px; }
            h2 { margin-bottom: 5px; }
            table { width: 100%; border-collapse: collapse; margin-top: 15px; }
            th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
            th { background-color: #f2f2f2; }
            .summary { margin-bottom: 15px; }
            .summary td { border: none; padding: 3px 0; }
        </style></head><body>';
        $html .= '<h2>' . htmlspecialchars($data['title']) . '</h2>';
        $html .= '<table class="summary">';
        $html .= '<tr><td><strong>Total Pesanan:</strong> ' . $data['totalOrders'] . '</td></tr>';
        $html .= '<tr><td><strong>Total Omzet:</strong> Rp ' . number_format($data['totalRevenue'], 0, ',', '.') . '</td></tr>';
        $html .= '<tr><td><strong>Total Diskon:</strong> Rp ' . number_format($data['totalDiscount'], 0, ',', '.') . '</td></tr>';
        $html .= '</table>';

        $html .= '<table>';
        $html .= '<thead><tr><th>Invoice</th><th>Produk</th><th>Nominal</th><th>Total Bayar</th><th>Tanggal</th></tr></thead>';
        $html .= '<tbody>';
        foreach ($data['orders'] as $o) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($o['invoice_number']) . '</td>';
            $html .= '<td>' . htmlspecialchars($o['product_name_snapshot']) . '</td>';
            $html .= '<td>' . htmlspecialchars($o['nominal_snapshot']) . '</td>';
            $html .= '<td>Rp ' . number_format($o['total_amount'], 0, ',', '.') . '</td>';
            $html .= '<td>' . htmlspecialchars($o['created_at']) . '</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody></table></body></html>';

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        file_put_contents($tempPath, $dompdf->output());

        return $tempPath;
    }
}
