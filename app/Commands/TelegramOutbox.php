<?php

namespace App\Commands;

use App\Libraries\TelegramBot;
use App\Models\TelegramOutboxModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TelegramOutbox extends BaseCommand
{
    protected $group       = 'Telegram';
    protected $name        = 'telegram:outbox';
    protected $description = 'Kirim dan retry antrean pesan Telegram outbox.';

    public function run(array $params)
    {
        $outboxModel = new TelegramOutboxModel();
        $bot         = new TelegramBot();

        if (! $bot->isConfigured()) {
            CLI::error('Telegram bot belum dikonfigurasi.');
            return;
        }

        $pending = $outboxModel->getPending(50);
        if (empty($pending)) {
            CLI::write('Tidak ada pesan pending di outbox.', 'yellow');
            return;
        }

        CLI::write('Memproses ' . count($pending) . ' pesan outbox...', 'green');

        foreach ($pending as $item) {
            $chatId  = (int) $item['chat_id'];
            $payload = json_decode($item['payload'], true) ?: [];
            $res     = null;

            try {
                if ($item['type'] === 'order_payment_proof') {
                    $photoPath = $payload['photo_path'] ?? '';
                    $caption   = $payload['caption'] ?? '';
                    $markup    = $payload['reply_markup'] ?? [];

                    $res = $bot->sendPhoto($chatId, $photoPath, $caption, $markup);
                } elseif ($item['type'] === 'document') {
                    $filePath = $payload['file_path'] ?? '';
                    $caption  = $payload['caption'] ?? '';
                    $filename = $payload['filename'] ?? '';

                    $res = $bot->sendDocument($chatId, $filePath, $caption, $filename);
                } else {
                    $text   = $payload['text'] ?? '';
                    $markup = $payload['reply_markup'] ?? [];

                    $res = $bot->sendMessage($chatId, $text, $markup);
                }

                if ($res && ($res['ok'] ?? false)) {
                    $sentMsgId = (int) ($res['result']['message_id'] ?? 0);
                    $outboxModel->markSent((int) $item['id'], $sentMsgId);
                    CLI::write("Pesan ID {$item['id']} berhasil dikirim.", 'green');
                } else {
                    $errMsg = json_encode($res) ?: 'Unknown Telegram API error';
                    $outboxModel->markFailed((int) $item['id'], $errMsg);
                    CLI::error("Gagal kirim pesan ID {$item['id']}: {$errMsg}");
                }
            } catch (\Throwable $e) {
                $outboxModel->markFailed((int) $item['id'], $e->getMessage());
                CLI::error("Exception pada pesan ID {$item['id']}: " . $e->getMessage());
            }
        }

        CLI::write('Selesai.', 'green');
    }
}
