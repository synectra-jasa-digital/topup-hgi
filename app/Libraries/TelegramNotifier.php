<?php

namespace App\Libraries;

use App\Models\OrderModel;
use App\Models\TelegramOutboxModel;
use App\Models\TelegramSessionModel;

class TelegramNotifier
{
    /**
     * Enqueue notifikasi bukti pembayaran baru ke antrean outbox
     * dan langsung memicu pengiriman tanpa memblokir/menggagalkan request utama.
     */
    public static function notifyNewPaymentProof(int $orderId): void
    {
        try {
            $orderModel = new OrderModel();
            $order      = $orderModel->find($orderId);
            if (! $order) {
                return;
            }

            $sessions = (new TelegramSessionModel())->getNotifiableSessions();
            if (empty($sessions)) {
                return;
            }

            $rp = fn(float $n) => 'Rp ' . number_format($n, 0, ',', '.');

            $caption = "🔔 <b>Bukti Pembayaran Baru!</b>\n\n"
                . "📄 <b>Invoice:</b> <code>{$order['invoice_number']}</code>\n"
                . "📦 <b>Produk:</b> {$order['product_name_snapshot']} ({$order['nominal_snapshot']})\n"
                . "💵 <b>Nominal:</b> " . $rp((float) $order['price_snapshot']) . "\n"
                . "🏷️ <b>Diskon:</b> " . $rp((float) $order['discount_amount']) . "\n"
                . "💰 <b>Total Bayar:</b> <b>" . $rp((float) $order['total_amount']) . "</b>\n"
                . "💳 <b>Kanal:</b> {$order['payment_channel_name']}\n"
                . "🕒 <b>Waktu Upload:</b> <code>{$order['payment_proof_uploaded_at']}</code>\n"
                . "👤 <b>ID Game:</b> <code>{$order['game_id']}</code>\n"
                . "📱 <b>WhatsApp:</b> <code>{$order['whatsapp_number']}</code>";

            $markup = [
                'inline_keyboard' => [
                    [
                        ['text' => '✅ Verifikasi', 'callback_data' => "order_verify:{$order['id']}"],
                        ['text' => '❌ Tolak', 'callback_data' => "order_reject:{$order['id']}"],
                    ],
                ],
            ];

            $filename  = basename((string) ($order['payment_proof_path'] ?? ''));
            $photoPath = WRITEPATH . 'uploads/payment-proofs/' . $filename;
            if (! is_file($photoPath)) {
                $altPath = WRITEPATH . 'uploads/' . ($order['payment_proof_path'] ?? '');
                if (is_file($altPath)) {
                    $photoPath = $altPath;
                }
            }

            $outbox = new TelegramOutboxModel();
            $bot    = new TelegramBot();

            foreach ($sessions as $session) {
                $chatId = (int) $session['chat_id'];
                $payload = [
                    'photo_path'   => $photoPath,
                    'caption'      => $caption,
                    'reply_markup' => $markup,
                ];

                $outboxId = $outbox->enqueue($chatId, 'order_payment_proof', $payload, (int) $order['id']);

                // Picu langsung pengiriman saat ini juga
                if ($bot->isConfigured()) {
                    try {
                        $res = $bot->sendPhoto($chatId, $photoPath, $caption, $markup);
                        if ($res && ($res['ok'] ?? false)) {
                            $sentMsgId = (int) ($res['result']['message_id'] ?? 0);
                            $outbox->markSent($outboxId, $sentMsgId);
                        } else {
                            $outbox->markFailed($outboxId, json_encode($res));
                        }
                    } catch (\Throwable $err) {
                        $outbox->markFailed($outboxId, $err->getMessage());
                    }
                }
            }
        } catch (\Throwable $e) {
            // Upload bukti customer tidak boleh gagal karena Telegram gagal
            log_message('error', 'Gagal memproses antrean Telegram: ' . $e->getMessage());
        }
    }
}
