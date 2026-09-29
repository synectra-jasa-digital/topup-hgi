<?php

namespace App\Libraries\Telegram\Commands;

use App\Libraries\ReportExporter;

class ReportCommand extends BaseCommand
{
    public function handle(array $update, ?array $session): void
    {
        if ($this->requireLogin($update, $session)) {
            return;
        }

        $chatId = $this->getChatId($update);
        $text   = strtolower(trim($this->getText($update)));

        // Cek apakah ada callback_query untuk pemilihan periode laporan
        if (isset($update['callback_query'])) {
            $data = $update['callback_query']['data'] ?? '';
            if (str_starts_with($data, 'laporan:')) {
                // format: laporan:<format>:<period>
                [$prefix, $format, $period] = explode(':', $data);
                $this->generateAndSendReport($chatId, $session, $format, $period, $update['callback_query']['id']);
            }
            return;
        }

        $format = str_contains($text, 'pdf') ? 'pdf' : 'excel';

        // Tampilkan tombol pilihan periode
        $markup = [
            'inline_keyboard' => [
                [
                    ['text' => '📅 Hari Ini', 'callback_data' => "laporan:{$format}:today"],
                    ['text' => '📊 7 Hari Terakhir', 'callback_data' => "laporan:{$format}:7days"],
                ],
                [
                    ['text' => '📈 Bulan Ini', 'callback_data' => "laporan:{$format}:this_month"],
                    ['text' => '📉 Bulan Lalu', 'callback_data' => "laporan:{$format}:last_month"],
                ],
            ],
        ];

        $formatLabel = strtoupper($format);
        $this->bot->sendMessage(
            $chatId,
            "📊 <b>Unduh Laporan Penjualan ({$formatLabel})</b>\n\nPilih rentang waktu laporan yang ingin diunduh:",
            $markup
        );
    }

    private function generateAndSendReport(
        int $chatId,
        array $session,
        string $format,
        string $period,
        string $callbackQueryId
    ): void {
        $this->bot->answerCallbackQuery($callbackQueryId, 'Menyiapkan laporan...');

        $exporter = new ReportExporter();
        $adminId  = (int) $session['admin_id'];

        try {
            if ($format === 'pdf') {
                $filePath = $exporter->exportPdfFile($period);
                $filename = "laporan_{$period}.pdf";
            } else {
                $filePath = $exporter->exportExcelFile($period);
                $filename = "laporan_{$period}.xlsx";
            }

            $this->bot->sendDocument(
                $chatId,
                $filePath,
                "📊 Laporan Penjualan ({$period})",
                $filename
            );

            // Log activity sesuai PRD
            $this->logActivity($adminId, 'telegram_unduh_laporan', "Mengunduh laporan {$format} periode {$period}");

            @unlink($filePath);
        } catch (\Throwable $e) {
            log_message('error', "Gagal membuat laporan telegram: " . $e->getMessage());
            $this->bot->sendMessage($chatId, "❌ Gagal membuat laporan: " . $e->getMessage());
        }
    }
}
