<?php

namespace App\Libraries\Telegram\Commands;

use App\Models\OrderModel;

class OrderCommand extends BaseCommand
{
    public function handle(array $update, ?array $session): void
    {
        if ($this->requireLogin($update, $session)) {
            return;
        }

        $chatId = $this->getChatId($update);
        $text   = $this->getText($update);

        $parts = explode(' ', trim($text));
        if (count($parts) < 2 || trim($parts[1]) === '') {
            $this->bot->sendMessage($chatId, "Gunakan format: /pesanan <code>&lt;invoice&gt;</code>");
            return;
        }

        $invoice    = trim($parts[1]);
        $orderModel = new OrderModel();
        $order      = $orderModel->findByInvoice($invoice);

        if ($order === null) {
            $this->bot->sendMessage($chatId, "❌ Pesanan dengan invoice <code>{$invoice}</code> tidak ditemukan.");
            return;
        }

        $text = "📄 <b>Detail Pesanan</b>\n\n"
            . "Invoice: <code>{$order['invoice_number']}</code>\n"
            . "Status: <b>" . strtoupper($order['status']) . "</b>\n"
            . "Produk: {$order['product_name_snapshot']} ({$order['nominal_snapshot']})\n"
            . "Harga: " . $this->rp((float) $order['price_snapshot']) . "\n"
            . "Diskon: " . $this->rp((float) $order['discount_amount']) . "\n"
            . "Total Bayar: <b>" . $this->rp((float) $order['total_amount']) . "</b>\n"
            . "Metode Bayar: {$order['payment_channel_name']}\n"
            . "ID Game: <code>{$order['game_id']}</code>\n"
            . "No WA: <code>{$order['whatsapp_number']}</code>\n"
            . "Waktu Dibuat: <code>{$order['created_at']}</code>\n";

        if (! empty($order['payment_rejection_reason'])) {
            $text .= "Alasan Tolak: <i>" . htmlspecialchars($order['payment_rejection_reason']) . "</i>\n";
        }

        $markup = [];
        if ($order['status'] === 'menunggu_verifikasi') {
            $markup = [
                'inline_keyboard' => [
                    [
                        ['text' => '✅ Verifikasi', 'callback_data' => "order_verify:{$order['id']}"],
                        ['text' => '❌ Tolak', 'callback_data' => "order_reject:{$order['id']}"],
                    ],
                ],
            ];
        }

        $filename  = basename((string) ($order['payment_proof_path'] ?? ''));
        $proofPath = WRITEPATH . 'uploads/payment-proofs/' . $filename;
        if (! is_file($proofPath)) {
            $proofPath = WRITEPATH . 'uploads/' . ($order['payment_proof_path'] ?? '');
        }

        if (! empty($order['payment_proof_path']) && file_exists($proofPath)) {
            $this->bot->sendPhoto($chatId, $proofPath, $text, $markup);
        } else {
            $this->bot->sendMessage($chatId, $text, $markup);
        }
    }
}
