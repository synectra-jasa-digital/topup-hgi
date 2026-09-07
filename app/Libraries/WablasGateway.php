<?php

namespace App\Libraries;

use CodeIgniter\Config\Services;

class WablasGateway
{
    protected string $domain;
    protected string $token;
    protected string $adminPhone;

    public function __construct()
    {
        $this->domain = getenv('wablas.domain') ?: $_ENV['wablas.domain'] ?? '';
        $this->token = getenv('wablas.token') ?: $_ENV['wablas.token'] ?? '';
        $this->adminPhone = getenv('wablas.adminPhone') ?: $_ENV['wablas.adminPhone'] ?? '';
    }

    public function sendToAdminNewOrder(array $order): bool
    {
        if (empty($this->adminPhone) || empty($this->token)) {
            return false;
        }

        $message = "Halo Admin!\n\n";
        $message .= "Ada pesanan baru yang sudah DIBAYAR.\n";
        $message .= "No. Invoice: " . $order['invoice_number'] . "\n";
        $message .= "Produk: " . $order['product_name_snapshot'] . " (" . $order['nominal_snapshot'] . ")\n";
        $message .= "Total Bayar: Rp" . number_format($order['total_amount'], 0, ',', '.') . "\n";
        $message .= "ID Game Tujuan: " . $order['game_id'] . "\n";
        $message .= "WA Customer: " . $order['whatsapp_number'] . "\n\n";
        $message .= "Mohon segera proses dan update status pesanan.";

        return $this->sendMessage($this->adminPhone, $message);
    }

    public function sendToCustomerOrderCompleted(array $order): bool
    {
        if (empty($order['whatsapp_number']) || empty($this->token)) {
            return false;
        }

        $message = "Halo!\n\n";
        $message .= "Pesanan Anda di Ayong Store dengan No. Invoice " . $order['invoice_number'] . " telah SELESAI diproses.\n";
        $message .= "Produk " . $order['product_name_snapshot'] . " (" . $order['nominal_snapshot'] . ") sudah dikirimkan ke ID Game: " . $order['game_id'] . ".\n\n";
        $message .= "Terima kasih telah berbelanja di Ayong Store!";

        return $this->sendMessage($order['whatsapp_number'], $message);
    }

    protected function sendMessage(string $phone, string $message): bool
    {
        try {
            $client = Services::curlrequest();
            $url = rtrim($this->domain, '/') . '/api/send-message';
            
            if (strpos($phone, '0') === 0) {
                $phone = '62' . substr($phone, 1);
            }

            $response = $client->post($url, [
                'headers' => [
                    'Authorization' => $this->token,
                    'Accept' => 'application/json',
                ],
                'form_params' => [
                    'phone'   => $phone,
                    'message' => $message,
                ],
                'http_errors' => false,
                'timeout' => 5
            ]);

            $statusCode = $response->getStatusCode();
            if ($statusCode >= 200 && $statusCode < 300) {
                return true;
            }

            log_message('error', 'Wablas Error: ' . $response->getBody());
            return false;
        } catch (\Exception $e) {
            log_message('error', 'Wablas Exception: ' . $e->getMessage());
            return false;
        }
    }
}
