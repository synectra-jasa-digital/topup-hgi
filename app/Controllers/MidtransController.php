<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\OrderPaymentModel;
use CodeIgniter\API\ResponseTrait;

class MidtransController extends BaseController
{
    use ResponseTrait;

    public function webhook()
    {
        $serverKey = getenv('midtrans.serverKey') ?: $_ENV['midtrans.serverKey'] ?? '';
        $json = $this->request->getBody();
        
        $notif = json_decode($json, true);
        if (! $notif) {
            return $this->fail('Invalid JSON format', 400);
        }

        $orderId = $notif['order_id'] ?? null;
        $statusCode = $notif['status_code'] ?? null;
        $grossAmount = $notif['gross_amount'] ?? null;
        $signatureKey = $notif['signature_key'] ?? null;

        $calculatedSignatureKey = hash("sha512", $orderId . $statusCode . $grossAmount . $serverKey);
        if ($calculatedSignatureKey !== $signatureKey) {
            log_message('error', 'Midtrans Webhook Invalid Signature for Order: ' . $orderId);
            return $this->fail('Invalid signature', 403);
        }

        $orderModel = new OrderModel();
        $order = $orderModel->findByInvoice($orderId);

        if (! $order) {
            return $this->failNotFound('Order not found');
        }

        $transactionStatus = $notif['transaction_status'];
        $fraudStatus = $notif['fraud_status'] ?? '';
        
        $newStatus = $order['status'];
        
        if ($transactionStatus == 'capture') {
            if ($fraudStatus == 'challenge') {
                $newStatus = 'menunggu_pembayaran';
            } else if ($fraudStatus == 'accept') {
                $newStatus = 'diproses';
            }
        } else if ($transactionStatus == 'settlement') {
            $newStatus = 'diproses';
        } else if ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
            $newStatus = 'dibatalkan';
        } else if ($transactionStatus == 'pending') {
            $newStatus = 'menunggu_pembayaran';
        }

        if ($order['status'] !== $newStatus) {
            $orderModel->update($order['id'], ['status' => $newStatus]);

            if (in_array($newStatus, ['diproses']) && !in_array($order['status'], ['diproses'])) {
                $wablas = new \App\Libraries\WablasGateway();
                $freshOrder = $orderModel->find($order['id']);
                $wablas->sendToAdminNewOrder($freshOrder);
            }
        }

        $orderPaymentModel = new OrderPaymentModel();
        
        $paymentData = [
            'order_id'               => $order['id'],
            'midtrans_order_id'      => $notif['order_id'] ?? null,
            'midtrans_transaction_id'=> $notif['transaction_id'] ?? null,
            'payment_method'         => $notif['payment_type'] ?? 'unknown',
            'raw_notification'       => json_encode($notif),
        ];

        if (in_array($transactionStatus, ['capture', 'settlement'])) {
            $paymentData['paid_at'] = date('Y-m-d H:i:s');
        }
        
        $existingPayment = $orderPaymentModel->where('order_id', $order['id'])->first();
        if ($existingPayment) {
            $orderPaymentModel->update($existingPayment['id'], $paymentData);
        } else {
            $orderPaymentModel->insert($paymentData);
        }

        return $this->respond(['status' => 'success']);
    }
}
