<?php

namespace App\Libraries\WhatsApp;

use App\Libraries\IntegrationSettings;
use CodeIgniter\Config\Services;

class BaileysGateway implements WhatsAppGatewayInterface
{
    protected string $url;
    protected string $apiKey;

    public function __construct(?string $url = null, ?string $apiKey = null)
    {
        $settings     = new IntegrationSettings();
        $this->url    = rtrim($url ?? (string) $settings->get('wa_gateway_url', 'WA_GATEWAY_URL'), '/');
        $this->apiKey = $apiKey ?? (string) $settings->get('wa_gateway_key', 'WA_GATEWAY_KEY');
    }

    public function send(string $phone, string $message): array
    {
        if (empty($this->url) || empty($this->apiKey)) {
            return [
                'success'    => false,
                'message_id' => null,
                'code'       => 'not_configured',
                'error'      => 'Gateway URL or API key not configured',
            ];
        }

        if (! function_exists('normalize_phone')) {
            helper('phone');
        }
        $formattedPhone = normalize_phone($phone);

        try {
            $client   = Services::curlrequest();
            $response = $client->post($this->url . '/send', [
                'headers' => [
                    'x-api-key'    => $this->apiKey,
                    'Accept'       => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'phone'   => $formattedPhone,
                    'message' => $message,
                ],
                'http_errors' => false,
                'timeout'     => 5,
            ]);

            $statusCode = $response->getStatusCode();
            $body       = json_decode($response->getBody(), true) ?? [];

            if ($statusCode >= 200 && $statusCode < 300 && ! empty($body['success'])) {
                return [
                    'success'    => true,
                    'message_id' => $body['messageId'] ?? null,
                    'code'       => null,
                    'error'      => null,
                ];
            }

            return [
                'success'    => false,
                'message_id' => null,
                'code'       => $body['code'] ?? 'send_failed',
                'error'      => $body['error'] ?? 'HTTP ' . $statusCode,
            ];
        } catch (\Throwable $e) {
            log_message('error', 'BaileysGateway Send Exception: ' . $e->getMessage());

            return [
                'success'    => false,
                'message_id' => null,
                'code'       => 'send_failed',
                'error'      => $e->getMessage(),
            ];
        }
    }

    public function status(): array
    {
        if (empty($this->url) || empty($this->apiKey)) {
            return [
                'connected' => false,
                'status'    => 'not_configured',
                'phone'     => null,
                'last_seen' => null,
            ];
        }

        try {
            $client   = Services::curlrequest();
            $response = $client->get($this->url . '/health', [
                'headers' => [
                    'x-api-key' => $this->apiKey,
                    'Accept'    => 'application/json',
                ],
                'http_errors' => false,
                'timeout'     => 5,
            ]);

            $body = json_decode($response->getBody(), true) ?? [];

            return [
                'connected' => ! empty($body['connected']),
                'status'    => $body['status'] ?? 'disconnected',
                'phone'     => $body['phone'] ?? null,
                'last_seen' => $body['last_seen'] ?? null,
            ];
        } catch (\Throwable $e) {
            return [
                'connected' => false,
                'status'    => 'error',
                'phone'     => null,
                'last_seen' => null,
            ];
        }
    }

    public function getQr(): ?string
    {
        if (empty($this->url) || empty($this->apiKey)) {
            return null;
        }

        try {
            $client   = Services::curlrequest();
            $response = $client->get($this->url . '/qr', [
                'headers' => [
                    'x-api-key' => $this->apiKey,
                    'Accept'    => 'application/json',
                ],
                'http_errors' => false,
                'timeout'     => 5,
            ]);

            $body = json_decode($response->getBody(), true) ?? [];

            return $body['qr'] ?? null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function logout(): bool
    {
        if (empty($this->url) || empty($this->apiKey)) {
            return false;
        }

        try {
            $client   = Services::curlrequest();
            $response = $client->post($this->url . '/logout', [
                'headers' => [
                    'x-api-key' => $this->apiKey,
                    'Accept'    => 'application/json',
                ],
                'http_errors' => false,
                'timeout'     => 5,
            ]);

            return $response->getStatusCode() === 200;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
