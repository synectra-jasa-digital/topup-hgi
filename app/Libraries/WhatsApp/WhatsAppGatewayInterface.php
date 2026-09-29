<?php

namespace App\Libraries\WhatsApp;

interface WhatsAppGatewayInterface
{
    /**
     * Send a WhatsApp text message.
     *
     * @return array{success: bool, message_id: ?string, code: ?string, error: ?string}
     */
    public function send(string $phone, string $message): array;

    /**
     * Check current gateway connection status.
     *
     * @return array{connected: bool, status: string, phone: ?string, last_seen: ?string}
     */
    public function status(): array;
}
