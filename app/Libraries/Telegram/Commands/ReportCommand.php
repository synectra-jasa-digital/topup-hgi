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

        // Callback dari tombol inline
        if (isset($update['callback_query'])) {
            $data  = $update['callback_query']['data'] ?? '';
            $cbqId = $update['callback_query']['id'];

            // Langkah 2: pilih periode → generate
            if (str_starts_with($data, 'laporan:')) {
                // format: laporan:<format>:<period>
                $parts  = explode(':', $data, 3);
                $format = $parts[1] ?? 'excel';
                $period = $parts[2] ?? 'this_month';
                $this->generateAndSendReport($chatId, $session, $format, $period, $cbqId);
                return;
            }

            // Langkah 1: pilih format → tampilkan pilihan periode
            if (str_starts_with($data, 'laporan_fmt:')) {
                $format = substr($data, strlen('laporan_fmt:'));
                $this->bot->answerCallbackQuery($cbqId);
                $this->sendPeriodPicker($chatId, $format);
                return;
            }

            return;
        }

        // Command /laporan — langsung tampilkan pilihan format terlebih dahulu
        $this->sendFormatPicker($chatId);
    }

    private function sendFormatPicker(int $chatId): void
    {
        $markup = [
            'inline_keyboard' => [
                [
                    ['text' => '📊 Excel (.xlsx)', 'callback_data' => 'laporan_fmt:excel'],
                    ['text' => '📄 PDF (.pdf)',    'callback_data' => 'laporan_fmt:pdf'],
                ],
            ],
        ];

        $this->bot->sendMessage(
            $chatId,
            "<b>Unduh Laporan Penjualan</b>\n\nPilih format file yang ingin diunduh:",
            $markup
        );
    }

    private function sendPeriodPicker(int $chatId, string $format): void
    {
        $label  = strtoupper($format);
        $markup = [
            'inline_keyboard' => [
                [
                    ['text' => 'Hari Ini',        'callback_data' => "laporan:{$format}:today"],
                    ['text' => '7 Hari Terakhir', 'callback_data' => "laporan:{$format}:7days"],
                ],
                [
                    ['text' => 'Bulan Ini',  'callback_data' => "laporan:{$format}:this_month"],
                    ['text' => 'Bulan Lalu', 'callback_data' => "laporan:{$format}:last_month"],
                ],
            ],
        ];

        $this->bot->sendMessage(
            $chatId,
            "Format: <b>{$label}</b>\n\nPilih rentang waktu laporan:",
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
                "Laporan Penjualan — {$period} ({$format})",
                $filename
            );

            $this->logActivity($adminId, 'telegram_unduh_laporan', "Mengunduh laporan {$format} periode {$period}");

            @unlink($filePath);
        } catch (\Throwable $e) {
            log_message('error', "Gagal membuat laporan telegram: " . $e->getMessage());
            $this->bot->sendMessage($chatId, "Gagal membuat laporan: " . $e->getMessage());
        }
    }
}
