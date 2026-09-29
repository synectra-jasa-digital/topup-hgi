<?php

namespace App\Libraries;

use App\Libraries\WhatsApp\WhatsAppGatewayInterface;
use CodeIgniter\Config\Services;

class WablasGateway implements WhatsAppGatewayInterface
{
    protected string $domain;
    protected string $token;
    protected string $adminPhone;

    public function __construct()
    {
        $this->domain     = getenv('wablas.domain') ?: $_ENV['wablas.domain'] ?? '';
        $this->token      = (new IntegrationSettings())->get('wablas_key', 'wablas.token');
        $this->adminPhone = getenv('wablas.adminPhone') ?: $_ENV['wablas.adminPhone'] ?? '';
    }

    public function send(string $phone, string $message): array
    {
        if (empty($this->token)) {
            return [
                'success'    => false,
                'message_id' => null,
                'code'       => 'not_configured',
                'error'      => 'Wablas token not configured',
            ];
        }

        $success = $this->sendMessage($phone, $message);

        return [
            'success'    => $success,
            'message_id' => null,
            'code'       => $success ? null : 'send_failed',
            'error'      => $success ? null : 'Wablas request failed',
        ];
    }

    public function status(): array
    {
        $hasToken = ! empty($this->token);

        return [
            'connected' => $hasToken,
            'status'    => $hasToken ? 'connected' : 'disconnected',
            'phone'     => $this->adminPhone ?: null,
            'last_seen' => null,
        ];
    }

    public function sendToAdminNewOrder(array $order): bool
    {
        if (empty($this->adminPhone) || empty($this->token)) {
            return false;
        }

        $message = "Halo Admin!\n\n"
            . "Ada pesanan baru yang sudah DIBAYAR.\n"
            . "No. Invoice: " . $order['invoice_number'] . "\n"
            . "Produk: " . $order['product_name_snapshot'] . " (" . $order['nominal_snapshot'] . ")\n"
            . "Total Bayar: Rp" . number_format($order['total_amount'], 0, ',', '.') . "\n"
            . "ID Game Tujuan: " . $order['game_id'] . "\n"
            . "WA Customer: " . $order['whatsapp_number'] . "\n\n"
            . "Mohon segera proses dan update status pesanan.";

        return $this->sendMessage($this->adminPhone, $message);
    }

    public function sendToCustomerOrderCompleted(array $order): bool
    {
        if (empty($order['whatsapp_number']) || empty($this->token)) {
            return false;
        }

        $message = "Halo!\n\n"
            . "Pesanan Anda di Ayong Store dengan No. Invoice " . $order['invoice_number'] . " telah SELESAI diproses.\n"
            . "Produk " . $order['product_name_snapshot'] . " (" . $order['nominal_snapshot'] . ") sudah dikirimkan ke ID Game: " . $order['game_id'] . ".\n\n"
            . "Terima kasih telah berbelanja di Ayong Store!";

        return $this->sendMessage($order['whatsapp_number'], $message);
    }

    protected function sendMessage(string $phone, string $message): bool
    {
        try {
            $client = Services::curlrequest();
            $url    = rtrim($this->domain, '/') . '/api/send-message';

            if (! function_exists('normalize_phone')) {
                helper('phone');
            }
            $formattedPhone = normalize_phone($phone);

            $response = $client->post($url, [
                'headers' => [
                    'Authorization' => $this->token,
                    'Accept'        => 'application/json',
                ],
                'form_params' => [
                    'phone'   => $formattedPhone,
                    'message' => $message,
                ],
                'http_errors' => false,
                'timeout'     => 5,
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

    public function sendToCustomerBongkarStatus(array $request): bool
    {
        if (empty($request['customer_whatsapp']) || empty($this->token)) {
            return false;
        }

        $label = bongkar_status_label($request['status'] ?? 'pending');

        $message = "Halo!\n\n"
            . "Pengajuan bongkar Anda dengan No. " . $request['request_number'] . " telah diperbarui menjadi status: " . $label . ".\n\n"
            . "Item: " . $request['catalog_name_snapshot'] . " (" . $request['unit_label_snapshot'] . ")\n"
            . "Jumlah: " . $request['quantity'] . "\n"
            . "Perkiraan Dana: Rp" . number_format($request['estimated_amount'], 0, ',', '.') . "\n";

        return $this->sendMessage($request['customer_whatsapp'], $message);
    }
}
