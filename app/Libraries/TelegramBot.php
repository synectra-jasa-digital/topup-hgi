<?php

namespace App\Libraries;

use CodeIgniter\HTTP\CURLRequest;

class TelegramBot
{
    private string $token;
    private string $apiUrl;

    public function __construct(?string $token = null)
    {
        if ($token !== null && $token !== '') {
            $this->token = $token;
        } else {
            $integration = new IntegrationSettings();
            $this->token = $integration->get('telegram_bot_token', 'TELEGRAM_BOT_TOKEN');
        }

        $this->apiUrl = 'https://api.telegram.org/bot' . $this->token . '/';
    }

    public function isConfigured(): bool
    {
        return $this->token !== '';
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function sendMessage(
        int|string $chatId,
        string $text,
        array $replyMarkup = [],
        string $parseMode = 'HTML'
    ): ?array {
        $params = [
            'chat_id'    => $chatId,
            'text'       => $text,
            'parse_mode' => $parseMode,
        ];

        if (! empty($replyMarkup)) {
            $params['reply_markup'] = $replyMarkup;
        }

        return $this->request('sendMessage', $params);
    }

    public function sendPhoto(
        int|string $chatId,
        string $photoPath,
        string $caption = '',
        array $replyMarkup = [],
        string $parseMode = 'HTML'
    ): ?array {
        if (! file_exists($photoPath)) {
            // Jika file foto lokal tidak ada, kirim pesan teks sebagai fallback
            return $this->sendMessage($chatId, $caption . "\n\n<i>[Bukti bayar tidak ditemukan di server]</i>", $replyMarkup, $parseMode);
        }

        $postFields = [
            'chat_id'    => $chatId,
            'caption'    => $caption,
            'parse_mode' => $parseMode,
            'photo'      => new \CURLFile(realpath($photoPath)),
        ];

        if (! empty($replyMarkup)) {
            $postFields['reply_markup'] = json_encode($replyMarkup);
        }

        return $this->requestMultipart('sendPhoto', $postFields);
    }

    public function sendDocument(
        int|string $chatId,
        string $filePath,
        string $caption = '',
        string $filename = ''
    ): ?array {
        if (! file_exists($filePath)) {
            return $this->sendMessage($chatId, "File dokumen tidak ditemukan.");
        }

        $postFields = [
            'chat_id'  => $chatId,
            'caption'  => $caption,
            'document' => new \CURLFile(realpath($filePath), '', $filename !== '' ? $filename : basename($filePath)),
        ];

        return $this->requestMultipart('sendDocument', $postFields);
    }

    public function editMessageText(
        int|string $chatId,
        int $messageId,
        string $text,
        array $replyMarkup = [],
        string $parseMode = 'HTML'
    ): ?array {
        $params = [
            'chat_id'    => $chatId,
            'message_id' => $messageId,
            'text'       => $text,
            'parse_mode' => $parseMode,
        ];

        if (! empty($replyMarkup)) {
            $params['reply_markup'] = $replyMarkup;
        }

        return $this->request('editMessageText', $params);
    }

    public function editMessageCaption(
        int|string $chatId,
        int $messageId,
        string $caption,
        array $replyMarkup = [],
        string $parseMode = 'HTML'
    ): ?array {
        $params = [
            'chat_id'    => $chatId,
            'message_id' => $messageId,
            'caption'    => $caption,
            'parse_mode' => $parseMode,
        ];

        if (! empty($replyMarkup)) {
            $params['reply_markup'] = $replyMarkup;
        }

        return $this->request('editMessageCaption', $params);
    }

    public function answerCallbackQuery(
        string $callbackQueryId,
        string $text = '',
        bool $showAlert = false
    ): bool {
        $res = $this->request('answerCallbackQuery', [
            'callback_query_id' => $callbackQueryId,
            'text'              => $text,
            'show_alert'        => $showAlert,
        ]);

        return ($res['ok'] ?? false) === true;
    }

    public function deleteMessage(int|string $chatId, int $messageId): bool
    {
        $res = $this->request('deleteMessage', [
            'chat_id'    => $chatId,
            'message_id' => $messageId,
        ]);

        return ($res['ok'] ?? false) === true;
    }

    private function request(string $method, array $params): ?array
    {
        if (! $this->isConfigured()) {
            log_message('error', "TelegramBot not configured (no token).");
            return null;
        }

        $ch = curl_init($this->apiUrl . $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            log_message('error', "TelegramBot::{$method} error: {$err}");
            return null;
        }

        $decoded = json_decode((string) $response, true);
        if (! ($decoded['ok'] ?? false)) {
            log_message('warning', "TelegramBot::{$method} failed: " . json_encode($decoded));
        }

        return $decoded;
    }

    private function requestMultipart(string $method, array $postFields): ?array
    {
        if (! $this->isConfigured()) {
            return null;
        }

        $ch = curl_init($this->apiUrl . $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            log_message('error', "TelegramBot::{$method} multipart error: {$err}");
            return null;
        }

        return json_decode((string) $response, true);
    }
}
