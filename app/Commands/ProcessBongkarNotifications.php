<?php

namespace App\Commands;

use App\Libraries\WablasGateway;
use App\Models\BongkarRequestModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ProcessBongkarNotifications extends BaseCommand
{
    protected $group = 'Bongkar';
    protected $name = 'bongkar:notifications';
    protected $description = 'Mengirim ulang notifikasi status bongkar yang gagal.';

    public function run(array $params)
    {
        $requests = new BongkarRequestModel();
        $gateway = new WablasGateway();
        $processed = 0;

        foreach ($requests->findNotificationQueue() as $request) {
            if (! $requests->claimNotification((int) $request['id'])) {
                continue;
            }

            $sent = false;
            try {
                $sent = $gateway->sendToCustomerBongkarStatus($request);
            } catch (\Throwable $e) {
                log_message('error', 'Bongkar notification retry failed: ' . $e->getMessage());
            }
            $requests->markNotificationResult((int) $request['id'], $sent);
            $processed++;
        }

        CLI::write("Processed {$processed} bongkar notification(s).");
    }
}
