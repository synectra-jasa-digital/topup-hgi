<?php

namespace App\Libraries\Telegram\Commands;

use App\Models\OrderModel;

class PendingCommand extends BaseCommand
{
    public function handle(array $update, ?array $session): void
    {
        if ($this->requireLogin($update, $session)) {
            return;
        }

        $chatId     = $this->getChatId($update);
        $orderModel = new OrderModel();

        // Maksimal 10, paling lama di atas (ASC)
        $orders = $orderModel->where('status', 'menunggu_verifikasi')
                             ->orderBy('created_at', 'ASC')
                             ->findAll(10);

        if (empty($orders)) {
            $this->bot->sendMessage($chatId, "ℹ️ Tidak ada pesanan menunggu verifikasi.");
            return;
        }

        $this->bot->sendMessage($chatId, "⏳ <b>Daftar Pesanan Menunggu Verifikasi</b> (" . count($orders) . " pesanan)");

        foreach ($orders as $o) {
            $text = "📄 <b>Invoice:</b> <code>{$o['invoice_number']}</code>\n"
                . "📦 <b>Produk:</b> {$o['product_name_snapshot']} ({$o['nominal_snapshot']})\n"
                . "💰 <b>Total:</b> " . $this->rp((float) $o['total_amount']) . "\n"
                . "💳 <b>Metode:</b> {$o['payment_channel_name']}\n"
                . "👤 <b>ID Game:</b> <code>{$o['game_id']}</code>\n"
                . "📱 <b>WA:</b> <code>{$o['whatsapp_number']}</code>\n"
                . "🕒 <b>Waktu:</b> <code>{$o['payment_proof_uploaded_at']}</code>";

            $markup = [
                'inline_keyboard' => [
                    [
                        ['text' => '✅ Verifikasi', 'callback_data' => "order_verify:{$o['id']}"],
                        ['text' => '❌ Tolak', 'callback_data' => "order_reject:{$o['id']}"],
                    ],
                ],
            ];

            $filename  = basename((string) ($o['payment_proof_path'] ?? ''));
            $proofPath = WRITEPATH . 'uploads/payment-proofs/' . $filename;
            if (! is_file($proofPath)) {
                $proofPath = WRITEPATH . 'uploads/' . ($o['payment_proof_path'] ?? '');
            }

            if (! empty($o['payment_proof_path']) && file_exists($proofPath)) {
                $this->bot->sendPhoto($chatId, $proofPath, $text, $markup);
            } else {
                $this->bot->sendMessage($chatId, $text, $markup);
            }
        }
    }
}
