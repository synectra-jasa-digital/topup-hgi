<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ReportModel;

class ReportController extends BaseController
{
    protected ReportModel $report;

    public function __construct()
    {
        $this->report = new ReportModel();
    }

    // Dashboard utama admin – menampilkan ringkasan harian & grafik penjualan
    public function index()
    {
        $today   = $this->report->dailySummary();
        $yesterday = $this->report->dailySummary(1);
        $last7   = [];
        for ($i = 0; $i < 7; $i++) {
            $last7[] = $this->report->dailySummary($i);
        }

        // Data untuk chart.js (labels & dataset)
        $chartLabels = array_reverse(array_map(fn($d) => $d['date'], $last7));
        $chartData   = array_reverse(array_map(fn($d) => (float) $d['revenue'], $last7));

        return view('admin/report/index', [
            'today'       => $today,
            'yesterday'   => $yesterday,
            'chartLabels' => $chartLabels,
            'chartData'   => $chartData,
        ]);
    }

    // Export CSV – untuk rentang bulan tertentu
    public function exportCsv(int $year, int $month)
    {
        $data = $this->report->monthlySummary($year, $month);
        $filename = "laporan-{$year}-{$month}.csv";
        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, ['Tanggal', 'Revenue']);
        foreach ($data as $row) {
            fputcsv($handle, [$row['order_date'], $row['revenue']]);
        }
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return $this->response
            ->setHeader('Content-Type', 'text/csv')
            ->setHeader('Content-Disposition', "attachment; filename=\"$filename\"")
            ->setBody($csv);
    }

    public function exportExcel()
    {
        $exporter = new \App\Libraries\ReportExporter($this->report);
        $period   = $exporter->normalizePeriod((string) $this->request->getGet('period'));
        $file     = $exporter->exportExcelFile($period);

        return $this->response
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', "attachment; filename=\"laporan-{$period}.xlsx\"")
            ->setBody($this->downloadAndCleanup($file));
    }

    public function exportPdf()
    {
        $exporter = new \App\Libraries\ReportExporter($this->report);
        $period   = $exporter->normalizePeriod((string) $this->request->getGet('period'));
        $file     = $exporter->exportPdfFile($period);

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', "attachment; filename=\"laporan-{$period}.pdf\"")
            ->setBody($this->downloadAndCleanup($file));
    }

    /**
     * Baca file hasil export lalu hapus dari disk.
     *
     * Export menulis file temp di system temp dir. Kalau tidak dihapus,
     * setiap unduhan laporan menyisakan file yang menumpuk di hosting.
     */
    private function downloadAndCleanup(string $file): string
    {
        try {
            return (string) file_get_contents($file);
        } finally {
            if (is_file($file)) {
                @unlink($file);
            }
        }
    }
}
