<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\OrderPaymentModel;
use App\Models\VoucherModel;
use App\Libraries\Money;
use CodeIgniter\API\ResponseTrait;

class MidtransController extends BaseController
{
    use ResponseTrait;

    public function webhook()
    {
        $serverKey = getenv('midtrans.serverKey') ?: $_ENV['midtrans.serverKey'] ?? '';
        $json = $this->request->getBody();
        $notif = json_decode($json, true);
        if (! is_array($notif)) {
            return $this->fail('Invalid JSON format', 400);
        }

        $orderId      = (string) ($notif['order_id'] ?? '');
        $statusCode   = (string) ($notif['status_code'] ?? '');
        $grossAmount  = (string) ($notif['gross_amount'] ?? '');
        $signatureKey = (string) ($notif['signature_key'] ?? '');

        $calculatedSignatureKey = hash("sha512", $orderId . $statusCode . $grossAmount . $serverKey);
        if ($orderId === '' || $statusCode === '' || $grossAmount === '' || $signatureKey === ''
            || ! hash_equals($calculatedSignatureKey, $signatureKey)) {
            log_message('error', 'Midtrans Webhook Invalid Signature for Order: ' . $orderId);
            return $this->fail('Invalid signature', 403);
        }

        $orderModel = new OrderModel();
        $orderModel->db->transStart();
        $order = $orderModel->findByInvoice($orderId);

        if (! $order) {
            $orderModel->db->transRollback();
            return $this->failNotFound('Order not found');
        }

        $transactionStatus = (string) ($notif['transaction_status'] ?? '');
        $fraudStatus = $notif['fraud_status'] ?? '';

        if ($transactionStatus === '') {
            $orderModel->db->transRollback();
            return $this->fail('Missing transaction status', 400);
        }

        if (! self::amountsMatch($grossAmount, (string) $order['total_amount'])) {
            log_message('error', 'Midtrans Webhook Amount Mismatch for Order: ' . $orderId);
            $orderModel->db->transRollback();
            return $this->fail('Invalid amount', 422);
        }

        $newStatus = $order['status'];

        if ($transactionStatus === 'capture') {
            if ($fraudStatus == 'challenge') {
                $newStatus = 'menunggu_pembayaran';
            } else if ($fraudStatus == 'accept') {
                $newStatus = 'diproses';
            }
        } else if ($transactionStatus === 'settlement') {
            $newStatus = 'diproses';
        } else if (in_array($transactionStatus, ['cancel', 'deny', 'expire'], true)) {
            $newStatus = 'dibatalkan';
        } else if ($transactionStatus === 'pending') {
            $newStatus = 'menunggu_pembayaran';
        }

        if (! self::canTransition($order['status'], $newStatus)) {
            $newStatus = $order['status'];
        }

        $orderPaymentModel = new OrderPaymentModel();
        $transactionId = (string) ($notif['transaction_id'] ?? '');
        $existingPayment = $orderPaymentModel->where('order_id', $order['id'])->first();
        $notificationKey = self::notificationKey($notif);
        if ($existingPayment && $existingPayment['notification_key'] === $notificationKey) {
            $orderModel->db->transComplete();
            return $this->respond(['status' => 'success']);
        }

        $paymentData = [
            'order_id'               => $order['id'],
            'midtrans_order_id'      => $orderId,
            'midtrans_transaction_id'=> $transactionId !== '' ? $transactionId : null,
            'notification_key'       => $notificationKey,
            'payment_method'         => $notif['payment_type'] ?? 'unknown',
            'raw_notification'       => json_encode($notif),
        ];

        if (in_array($transactionStatus, ['capture', 'settlement'])) {
            $paymentData['paid_at'] = date('Y-m-d H:i:s');
        }
        
        $voucherModel = new VoucherModel();
        if ($newStatus === 'diproses' && ! (int) ($order['voucher_committed'] ?? 0) && ! empty($order['voucher_id'])) {
            if (! $voucherModel->commitReservation((int) $order['voucher_id'])) {
                $orderModel->db->transRollback();
                return $this->failServerError('Voucher reservation commit failed');
            }
            $orderModel->update($order['id'], ['voucher_reserved' => 0, 'voucher_committed' => 1, 'voucher_reserved_until' => null]);
        } elseif ($newStatus === 'dibatalkan' && (int) ($order['voucher_reserved'] ?? 0) && ! empty($order['voucher_id'])) {
            if (! $voucherModel->releaseReservation((int) $order['voucher_id'])) {
                $orderModel->db->transRollback();
                return $this->failServerError('Voucher reservation release failed');
            }
            $orderModel->update($order['id'], ['voucher_reserved' => 0, 'voucher_reserved_until' => null]);
        }
        if ($order['status'] !== $newStatus) {
            $orderModel->update($order['id'], ['status' => $newStatus]);
        }
        if ($existingPayment) {
            $orderPaymentModel->update($existingPayment['id'], $paymentData);
        } else {
            $orderPaymentModel->insert($paymentData);
        }
        $orderModel->db->transComplete();

        if (! $orderModel->db->transStatus()) {
            return $this->failServerError('Payment update failed');
        }

        if ($order['status'] !== 'diproses' && $newStatus === 'diproses') {
            $claimed = $orderModel->builder()->where('id', $order['id'])->where('wablas_notification_claimed', 0)->update(['wablas_notification_claimed' => 1]);
            if ($claimed && $orderModel->db->affectedRows() === 1) {
                $wablas = new \App\Libraries\WablasGateway();
                $freshOrder = $orderModel->find($order['id']);
                $wablas->sendToAdminNewOrder($freshOrder);
            }
        }

        return $this->respond(['status' => 'success']);
    }

    private static function amountsMatch(string $received, string $expected): bool
    {
        return Money::rupiah($received) === Money::rupiah($expected);
    }

    private static function notificationKey(array $notification): string
    {
        $identity = [];
        foreach ([
            'order_id', 'transaction_id', 'transaction_status', 'status_code',
            'gross_amount', 'fraud_status', 'payment_type', 'settlement_time',
        ] as $field) {
            $identity[$field] = (string) ($notification[$field] ?? '');
        }

        return hash('sha256', json_encode($identity, JSON_UNESCAPED_SLASHES));
    }

    private static function canTransition(string $current, string $next): bool
    {
        if ($current === $next) {
            return true;
        }
        if (in_array($current, ['selesai', 'dibatalkan'], true)) {
            return false;
        }
        return match ($next) {
            'diproses'   => $current === 'menunggu_pembayaran',
            'dibatalkan' => $current === 'menunggu_pembayaran',
            'menunggu_pembayaran' => $current === 'menunggu_pembayaran',
            default      => false,
        };
    }
}
