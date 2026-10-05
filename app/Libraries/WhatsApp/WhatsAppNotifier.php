<?php

namespace App\Libraries\WhatsApp;

use App\Models\StoreSettingModel;
use App\Models\WaOutboxModel;

class WhatsAppNotifier
{
    protected StoreSettingModel $settings;
    protected WaOutboxModel $outbox;

    public function __construct(?StoreSettingModel $settings = null, ?WaOutboxModel $outbox = null)
    {
        $this->settings = $settings ?? new StoreSettingModel();
        $this->outbox   = $outbox ?? new WaOutboxModel();
    }

    public function getDriver(): ?WhatsAppGatewayInterface
    {
        $driver = $this->settings->getVal('wa_driver', 'off');

        if ($driver === 'baileys') {
            return new BaileysGateway();
        }
        if ($driver === 'wablas') {
            return new \App\Libraries\WablasGateway();
        }

        return null;
    }

    public function isTypeEnabled(string $type): bool
    {
        $driver = $this->settings->getVal('wa_driver', 'off');
        if ($driver === 'off') {
            return false;
        }

        // Default to enabled (1) if setting not explicitly disabled (0)
        return $this->settings->getVal("wa_enable_{$type}", '1') === '1';
    }

    public function orderCreated(array $order): void
    {
        try {
            $type = 'order_created';
            if (! $this->isTypeEnabled($type)) {
                return;
            }

            $orderId = (int) ($order['id'] ?? 0);
            $phone   = (string) ($order['whatsapp_number'] ?? '');
            if (empty($phone)) {
                return;
            }

            $message = MessageTemplates::orderCreated($order);
            $outboxId = $this->outbox->enqueue([
                'recipient'  => $phone,
                'type'       => $type,
                'message'    => $message,
                'ref_type'   => 'orders',
                'ref_id'     => $orderId,
                'dedupe_key' => "order_created:{$orderId}",
            ]);

            if ($outboxId) {
                $this->sendOutboxImmediately($outboxId);
            }
        } catch (\Throwable $e) {
            log_message('error', 'WhatsAppNotifier orderCreated failed: ' . $e->getMessage());
        }
    }

    public function paymentReceived(array $order): void
    {
        try {
            $type = 'payment_received';
            if (! $this->isTypeEnabled($type)) {
                return;
            }

            $orderId = (int) ($order['id'] ?? 0);
            $phone   = (string) ($order['whatsapp_number'] ?? '');
            if (empty($phone)) {
                return;
            }

            $message = MessageTemplates::paymentReceived($order);
            $outboxId = $this->outbox->enqueue([
                'recipient'  => $phone,
                'type'       => $type,
                'message'    => $message,
                'ref_type'   => 'orders',
                'ref_id'     => $orderId,
                'dedupe_key' => "payment_received:{$orderId}",
            ]);

            if ($outboxId) {
                $this->sendOutboxImmediately($outboxId);
            }
        } catch (\Throwable $e) {
            log_message('error', 'WhatsAppNotifier paymentReceived failed: ' . $e->getMessage());
        }
    }

    public function paymentVerified(array $order): void
    {
        try {
            $type = 'payment_verified';
            if (! $this->isTypeEnabled($type)) {
                return;
            }

            $orderId = (int) ($order['id'] ?? 0);
            $phone   = (string) ($order['whatsapp_number'] ?? '');
            if (empty($phone)) {
                return;
            }

            $message = MessageTemplates::paymentVerified($order);
            $outboxId = $this->outbox->enqueue([
                'recipient'  => $phone,
                'type'       => $type,
                'message'    => $message,
                'ref_type'   => 'orders',
                'ref_id'     => $orderId,
                'dedupe_key' => "payment_verified:{$orderId}",
            ]);

            if ($outboxId) {
                $this->sendOutboxImmediately($outboxId);
            }
        } catch (\Throwable $e) {
            log_message('error', 'WhatsAppNotifier paymentVerified failed: ' . $e->getMessage());
        }
    }

    public function paymentRejected(array $order, string $reason = ''): void
    {
        try {
            $type = 'payment_rejected';
            if (! $this->isTypeEnabled($type)) {
                return;
            }

            $orderId = (int) ($order['id'] ?? 0);
            $phone   = (string) ($order['whatsapp_number'] ?? '');
            if (empty($phone)) {
                return;
            }

            // Dedupe key can append timestamp or attempt count if allowed to be rejected multiple times
            $attempt = (int) ($order['payment_rejection_count'] ?? 1);
            $message = MessageTemplates::paymentRejected($order, $reason);

            $outboxId = $this->outbox->enqueue([
                'recipient'  => $phone,
                'type'       => $type,
                'message'    => $message,
                'ref_type'   => 'orders',
                'ref_id'     => $orderId,
                'dedupe_key' => "payment_rejected:{$orderId}:{$attempt}",
            ]);

            if ($outboxId) {
                $this->sendOutboxImmediately($outboxId);
            }
        } catch (\Throwable $e) {
            log_message('error', 'WhatsAppNotifier paymentRejected failed: ' . $e->getMessage());
        }
    }

    public function orderCompleted(array $order): void
    {
        try {
            $type = 'order_completed';
            if (! $this->isTypeEnabled($type)) {
                return;
            }

            $orderId = (int) ($order['id'] ?? 0);
            $phone   = (string) ($order['whatsapp_number'] ?? '');
            if (empty($phone)) {
                return;
            }

            $message = MessageTemplates::orderCompleted($order);
            $outboxId = $this->outbox->enqueue([
                'recipient'  => $phone,
                'type'       => $type,
                'message'    => $message,
                'ref_type'   => 'orders',
                'ref_id'     => $orderId,
                'dedupe_key' => "order_completed:{$orderId}",
            ]);

            if ($outboxId) {
                $this->sendOutboxImmediately($outboxId);
            }
        } catch (\Throwable $e) {
            log_message('error', 'WhatsAppNotifier orderCompleted failed: ' . $e->getMessage());
        }
    }

    /**
     * Serahkan notifikasi perubahan status bongkar ke outbox (satu-satunya jalur kirim).
     *
     * @return bool true  = ditangani: diantre, duplikat dedupe (sudah terencana),
     *                     atau sengaja dilewat (driver off / tipe dimatikan /
     *                     tanpa nomor) — pemanggil boleh menandai selesai.
     *              false = gagal — pemanggil boleh mencoba lagi.
     */
    public function bongkarStatusChanged(array $request): bool
    {
        try {
            $type = 'bongkar_status';
            if (! $this->isTypeEnabled($type)) {
                return true;
            }

            $reqId  = (int) ($request['id'] ?? 0);
            $phone  = (string) ($request['customer_whatsapp'] ?? '');
            $status = (string) ($request['status'] ?? 'pending');
            if (empty($phone)) {
                return true;
            }

            $message = MessageTemplates::bongkarStatusChanged($request);
            $outboxId = $this->outbox->enqueue([
                'recipient'  => $phone,
                'type'       => $type,
                'message'    => $message,
                'ref_type'   => 'bongkar_requests',
                'ref_id'     => $reqId,
                'dedupe_key' => "bongkar_status:{$reqId}:{$status}",
            ]);

            // $outboxId null = duplikat dedupe — pengiriman sudah terencana, tetap ditangani.
            if ($outboxId) {
                $this->sendOutboxImmediately($outboxId);
            }

            return true;
        } catch (\Throwable $e) {
            log_message('error', 'WhatsAppNotifier bongkarStatusChanged failed: ' . $e->getMessage());

            return false;
        }
    }

    public function sendOutboxImmediately(int $id): bool
    {
        $gateway = $this->getDriver();
        if (! $gateway) {
            return false;
        }

        $row = $this->outbox->find($id);
        if (! $row || $row['status'] === 'sent') {
            return false;
        }

        $res = $gateway->send($row['recipient'], $row['message']);

        if ($res['success']) {
            $this->outbox->markSent($id, $res['message_id']);
            return true;
        }

        if (($res['code'] ?? '') === 'invalid_number') {
            $this->outbox->markInvalidNumber($id, $res['error'] ?? 'Nomor tidak terdaftar di WhatsApp');
            return false;
        }

        $this->outbox->markFailed($id, $res['error'] ?? 'Pengiriman gagal');
        return false;
    }
}
