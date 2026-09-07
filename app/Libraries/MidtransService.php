<?php

namespace App\Libraries;

use Midtrans\Config;
use Midtrans\Snap;
use Exception;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = getenv('midtrans.serverKey') ?: $_ENV['midtrans.serverKey'] ?? '';
        Config::$clientKey = getenv('midtrans.clientKey') ?: $_ENV['midtrans.clientKey'] ?? '';
        Config::$isProduction = filter_var(getenv('midtrans.isProduction') ?: $_ENV['midtrans.isProduction'] ?? false, FILTER_VALIDATE_BOOLEAN);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function getSnapToken(array $orderData): ?string
    {
        $params = [
            'transaction_details' => [
                'order_id'     => $orderData['invoice_number'],
                'gross_amount' => (int) $orderData['total_amount'],
            ],
            'customer_details'    => [
                'first_name' => $orderData['game_id'],
                'phone'      => $orderData['whatsapp_number'],
            ],
            'item_details'        => [
                [
                    'id'       => $orderData['product_id'],
                    'price'    => (int) $orderData['total_amount'],
                    'quantity' => 1,
                    'name'     => substr($orderData['product_name_snapshot'] . ' - ' . $orderData['nominal_snapshot'], 0, 50),
                ]
            ],
        ];

        try {
            return Snap::getSnapToken($params);
        } catch (Exception $e) {
            log_message('error', 'Midtrans Error (getSnapToken): ' . $e->getMessage());
            return null;
        }
    }
}
