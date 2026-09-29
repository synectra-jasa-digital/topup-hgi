<?php

namespace App\Commands;

use App\Libraries\WhatsApp\WhatsAppNotifier;
use App\Models\WaOutboxModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class WaOutbox extends BaseCommand
{
    protected $group       = 'WhatsApp';
    protected $name        = 'wa:outbox';
    protected $description = 'Proses antrean pesan WhatsApp yang pending atau gagal (Cron per menit).';

    public function run(array $params)
    {
        $notifier = new WhatsAppNotifier();
        $gateway  = $notifier->getDriver();

        if (! $gateway) {
            $this->writeLine('Driver WhatsApp non-aktif or belum dikonfigurasi.', 'yellow');
            return;
        }

        // Wake up Node.js app & check health
        $health = $gateway->status();
        if (! $health['connected']) {
            $this->writeLine('WhatsApp Gateway disconnected/unhealthy. Status: ' . ($health['status'] ?? 'unknown'), 'red');
        }

        $outboxModel = new WaOutboxModel();

        // Perform cleanup of old messages (>30 days)
        $cleaned = $outboxModel->cleanupOldMessages(30);
        if ($cleaned > 0) {
            $this->writeLine("Cleaned up {$cleaned} old message contents (>30 days).", 'green');
        }

        $rows = $outboxModel->claim(10);
        if (empty($rows)) {
            $this->writeLine('Tidak ada antrean pesan WhatsApp.', 'green');
            return;
        }

        $this->writeLine('Memproses ' . count($rows) . ' pesan WhatsApp...', 'yellow');

        foreach ($rows as $index => $row) {
            if ($index > 0) {
                // Random delay 3 to 10 seconds to avoid spam flags
                $delay = rand(3, 10);
                sleep($delay);
            }

            $res = $gateway->send($row['recipient'], $row['message']);

            if ($res['success']) {
                $outboxModel->markSent((int) $row['id'], $res['message_id']);
                $this->writeLine("Pesan #{$row['id']} ke {$row['recipient']} TERKIRIM.", 'green');
            } elseif (($res['code'] ?? '') === 'invalid_number') {
                $outboxModel->markInvalidNumber((int) $row['id'], $res['error'] ?? 'Nomor tidak terdaftar');
                $this->writeLine("Pesan #{$row['id']} ke {$row['recipient']} INVALID NUMBER.", 'yellow');
            } else {
                $outboxModel->markFailed((int) $row['id'], $res['error'] ?? 'Gagal kirim');
                $this->writeLine("Pesan #{$row['id']} ke {$row['recipient']} GAGAL: " . ($res['error'] ?? 'Gagal'), 'red');
            }
        }
    }

    private function writeLine(string $text, string $color = 'white'): void
    {
        if (is_cli()) {
            CLI::write($text, $color);
        }
    }
}
